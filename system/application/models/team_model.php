<?php

class Team_model extends Model
{
	function get($id)
	{
		$query = $this->db->query(
			'SELECT * FROM teams WHERE id='.$id);
		
		return $query->num_rows > 0 ? $query->row() : FALSE;
	}
	
	function getAll()
	{
		
		$sql = 'SELECT * FROM teams ORDER BY name ASC';
		$teams = $this->db->query($sql);
		
		return $teams->num_rows > 0 ? $teams->result() : FALSE;
	}
	
	function create($name, $description)
	{
		$this->db->query('INSERT INTO teams (name, description) VALUES (?, ?)',
			array($name, $description));
	}
	
	function getTournamentPlayers($tournament_id, $team_id)
	{
		$players = $this->db->query(
			'SELECT
				u.*
			FROM
				tournament_players AS tp,
				users AS u
			WHERE
				tp.tid = '.$tournament_id.' AND
				tp.pid = u.id AND
				tp.team_id = '.$team_id
		);
		
		return $players->num_rows > 0 ? $players->result() : FALSE;
	}
} 

?>
