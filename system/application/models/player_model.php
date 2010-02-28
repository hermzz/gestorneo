<?php

class Player_model extends Model
{
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM users WHERE id='.$id);
		
		return $query->num_rows > 0 ? $query->row() : FALSE;
	}
	
	function getAll()
	{
		$players = $this->db->query('SELECT * FROM users ORDER BY created ASC');
		
		return $players->num_rows > 0 ? $players : FALSE;
	}
	
	function getTournaments($id)
	{
		$query = $this->db->query('SELECT
		    UNIX_TIMESTAMP(t.date) AS u_date,
			t.*
		FROM 
			tournaments AS t,
			player2tournament AS p2t
		WHERE
			t.id = p2t.tid AND
			p2t.pid = '.$id);
		
		return $query->num_rows > 0 ? $query : FALSE;
	}
}

?>
