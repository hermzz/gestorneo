<?php

class Tournament_model extends Model
{
	// get tournament by ID
	function get($id)
	{
		$query = $this->db->query(
			'SELECT 
				*,
				UNIX_TIMESTAMP(start_date) AS u_start_date,
				UNIX_TIMESTAMP(end_date) AS u_end_date,
				UNIX_TIMESTAMP(signup_deadline) AS u_signup_deadline
			FROM 
				tournaments 
			WHERE 
				id='.$id);
		
		return $query->num_rows > 0 ? $query->row() : FALSE;
	}
	
	function getAll($type='all')
	{
		switch($type)
		{
			case 'past':
				$where = ' start_date < NOW()';
				break;
				
			case 'future':
				$where = ' start_date > NOW()';
				break;
				
			case 'all':
			default;
				$where = FALSE;
				break;
		}
		
		$sql = 'SELECT * FROM tournaments ' . 
			( $where ? ' WHERE '.$where : '' ) . 
		' ORDER BY start_date ASC';
		$tournaments = $this->db->query($sql);
		
		return $tournaments->num_rows > 0 ? $tournaments->result() : FALSE;
	}
	
	function countSignedUp($id, $sex=false)
	{
		if($sex)
		{
			$query = $this->db->query('SELECT 
					COUNT(*) AS cnt
				FROM
					users AS u,
					tournament_players AS tp
				WHERE 
					tp.tid = ? AND
					tp.pid = u.id AND
					u.sex = ?',
				array($id, $sex));
		} else {
			$query = $this->db->query('SELECT 
					COUNT(*) AS cnt
				FROM 
					tournament_players
				WHERE 
					tid=?',
				array($id));
		}
		
		$row = $query->row();
		return $row->cnt;
	}
	
	function getPlayers($id, $confirmed=true)
	{
		$query = $this->db->query(
			'SELECT 
				u.*
			FROM 
				users AS u, 
				tournament_players AS tp
			WHERE 
				u.id = tp.pid AND
				tp.confirmed = '.($confirmed ? 1 : 0).' AND
				tp.tid = '.$id);
			
		return $query->num_rows ? $query->result() : array();
	}
	
	function getUnassignedPlayers($id)
	{
		$query = $this->db->query(
			'SELECT 
				u.*
			FROM 
				users AS u, 
				tournament_players AS tp
			WHERE 
				u.id = tp.pid AND
				tp.confirmed = 1 AND
				tp.team_id IS NULL AND
				tp.tid = '.$id);
			
		return $query->num_rows ? $query->result() : array();
	}
	
	function create($name, $notes, $start_date, $end_date, $signup_deadline, $team_ids)
	{
		$this->db->query('INSERT INTO tournaments (name, notes, start_date, end_date, signup_deadline) VALUES (?, ?, ?, ?, ?)',
			array($name, $notes, $start_date, $end_date, $signup_deadline));
			
		$tournament_id = $this->db->insert_id();
			
		foreach($team_ids as $team_id)
			$this->db->query('INSERT INTO tournament_teams (tid, teid) VALUES (?, ?)', 
				array($tournament_id, $team_id));
	}
	
	function edit($id, $name, $notes, $start_date, $end_date, $signup_deadline, $team_ids)
	{
		$this->db->query('UPDATE tournaments SET name=?, notes=?, start_date=?, end_date=?, signup_deadline=? WHERE id=?',
			array($name, $notes, $start_date, $end_date, $signup_deadline, $id));
		
		$this->db->query('DELETE FROM tournament_teams WHERE tid=?', $id);
		
		foreach($team_ids as $team_id)
			$this->db->query('INSERT INTO tournament_teams (tid, teid) VALUES (?, ?)', 
				array($id, $team_id));
		
		// unset team id for player with a team that has been removed, ie: leave them as unassigned
		$this->db->query(
			'UPDATE 
				tournament_players AS tp 
					LEFT JOIN tournament_teams AS tt 
						ON tp.tid=tt.tid AND tp.team_id=tt.teid 
			SET 
				tp.team_id = null 
			WHERE 
				tp.tid = ? AND 
				tt.tid IS NULL', 
			$id
		);
	}
	
	function add_player($tournament_id, $player_id)
	{
		$this->db->query('INSERT INTO tournament_players VALUES (?, ?, 0, NULL)',
			array($player_id, $tournament_id));
	}
	
	function remove_player($tournament_id, $player_id)
	{
		$this->db->query('DELETE FROM tournament_players WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}
	
	function approve_player($tournament_id, $team_id, $player_id)
	{
		if(!$team_id)
			$team_id = null;
		
		$this->db->query('UPDATE tournament_players SET confirmed=1, team_id=? WHERE pid=? AND tid=?',
			array($team_id, $player_id, $tournament_id));
	}
	
	function drop_player($tournament_id, $player_id)
	{
		$this->db->query('UPDATE tournament_players SET confirmed=0, team_id=NULL WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}
	
	function is_signed_up($tournament_id, $player_id) 
	{
		$query = $this->db->query('SELECT * FROM tournament_players WHERE tid=? AND pid=?',
			array($tournament_id, $player_id));
			
		return $query->num_rows ? $query->result() : FALSE;
	}
	
	function can_sign_up($tournament_id, $player_id)
	{
		// is the user already signed up?
		return !$this->is_signed_up($tournament_id, $player_id);
		
		//TODO: more stuff to add here:
		//	- Sex limitation (ie: womens only tournament)
	}
	
	function is_old($tournament)
	{
		return mktime() > $tournament->u_start_date;
	}
	
	function undeadlined($tournament)
	{
		return mktime(0, 0, 0, date('n'), date('j'), date('Y')) <= $tournament->u_signup_deadline;
	}
	
	function getTeams($tournament_id)
	{
		$query = $this->db->query(
			'SELECT 
				t.*
			FROM 
				tournament_teams AS tt,
				teams AS t
			WHERE 
				tt.teid = t.id AND
				tt.tid = '.$tournament_id);
			
		return $query->num_rows ? $query->result() : array();
	}
}

?>
