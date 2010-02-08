<?php

class Player_model extends Model
{
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM players WHERE id='.$id);
		
		return $query->num_rows > 0 ? $query->row() : FALSE;
	}
	
	function getAll()
	{
		$players = $this->db->query('SELECT * FROM players  ORDER BY joined ASC');
		
		return $players->num_rows > 0 ? $players : FALSE;
	}
	
	function getTournaments($id)
	{
		$query = $this->db->query('SELECT
			t.*
		FROM 
			tournaments AS t,
			player2tournament AS p2t
		WHERE
			t.id = p2t.tid AND
			p2t.pid = '.$id);
		
		return $query->num_rows > 0 ? $query : FALSE;
	}
	
	function create($name, $joined=false)
	{
		if(!$joined)
			$joined = date('Y-m-d');
			
		$this->db->query('INSERT INTO players (name, joined) VALUES (?, ?)',
			array($name, $joined));
	}
}

?>
