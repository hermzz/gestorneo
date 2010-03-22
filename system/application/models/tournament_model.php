<?php

class Tournament_model extends Model
{
	// get tournament by ID
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM tournaments WHERE id='.$id);
		
		return $query->num_rows > 0 ? $query->row() : FALSE;
	}
	
	function getAll($type='all')
	{
		switch($type)
		{
			case 'past':
				$where = ' date < NOW()';
				break;
				
			case 'future':
				$where = ' date > NOW()';
				break;
				
			case 'all':
			default;
				$where = FALSE;
				break;
		}
		
		$sql = 'SELECT * FROM tournaments ' . 
			( $where ? ' WHERE '.$where : '' ) . 
		' ORDER BY date ASC';
		$tournaments = $this->db->query($sql);
		
		return $tournaments->num_rows > 0 ? $tournaments : FALSE;
	}
	
	function countSignedUp($id, $sex=false)
	{
		if($sex)
		{
			$query = $this->db->query('SELECT 
					COUNT(*) AS cnt
				FROM
					users AS u,
					player2tournament AS p2t
				WHERE 
					p2t.tid = ? AND
					p2t.pid = u.id AND
					u.sex = ?',
				array($id, $sex));
		} else {
			$query = $this->db->query('SELECT 
					COUNT(*) AS cnt
				FROM 
					player2tournament
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
				player2tournament AS p2t 
			WHERE 
				u.id = p2t.pid AND
				p2t.confirmed = '.($confirmed ? 1 : 0).' AND
				p2t.tid = '.$id);
			
		return $query->num_rows ? $query->result() : FALSE;
	}
	
	function create($name, $notes, $date)
	{
		$this->db->query('INSERT INTO tournaments (name, notes, date) VALUES (?, ?, ?)',
			array($name, $notes, $date));
	}
	
	function add_player($tournament_id, $player_id)
	{
		$this->db->query('INSERT INTO player2tournament VALUES (?, ?, 0)',
			array($player_id, $tournament_id));
	}
	
	function remove_player($tournament_id, $player_id)
	{
		$this->db->query('DELETE FROM player2tournament WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}
	
	function approve_player($tournament_id, $player_id)
	{
		$this->db->query('UPDATE player2tournament SET confirmed=1 WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}
	
	function drop_player($tournament_id, $player_id)
	{
		$this->db->query('UPDATE player2tournament SET confirmed=0 WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}
	
	function is_signed_up($tournament_id, $player_id) 
	{
		$query = $this->db->query('SELECT * FROM player2tournament WHERE tid=? AND pid=?',
			array($tournament_id, $player_id));
			
		return $query->num_rows ? $query->result() : FALSE;
	}
	
	function can_sign_up($tournament_id, $player_id)
	{
		// is the user already signed up?
		return !$this->is_signed_up($tournament_id, $player_id);
		
		//TODO: more stuff to add here:
		//	- Sex limitation (ie: womens only tournament)
		//	- Tournament regstration deadline has passed
	}
}

?>
