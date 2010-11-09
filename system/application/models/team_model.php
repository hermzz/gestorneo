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
		
		$sql = 'SELECT * FROM teams ORDER BY name DESC';
		$teams = $this->db->query($sql);
		
		return $teams->num_rows > 0 ? $teams : FALSE;
	}
	
	function create($name, $description)
	{
		$this->db->query('INSERT INTO teams (name, description) VALUES (?, ?)',
			array($name, $description));
	}
} 

?>
