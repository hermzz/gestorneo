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
	
	function countSignedUp($id)
	{
		$query = $this->db->query(
			'SELECT 
				COUNT(*) AS cnt
			FROM 
				player2tournament
			WHERE 
				tid='.$id);
			
		$row = $query->row();
		
		return $row->cnt;
	}
	
	function getPlayers($id, $confirmed=true)
	{
		$query = $this->db->query(
			'SELECT 
				p.*
			FROM 
				players AS p, 
				player2tournament AS p2t 
			WHERE 
				p.id=p2t.pid AND
				p2t.confirmed = '.($confirmed ? 1 : 0).' AND
				p2t.tid='.$id);
			
		return $query->num_rows ? $query->result() : FALSE;
	}
	
	function create($name, $notes, $date)
	{
		$this->db->query('INSERT INTO tournaments (name, notes, date) VALUES (?, ?, ?)',
			array($name, $notes, $date));
	}
}

?>
