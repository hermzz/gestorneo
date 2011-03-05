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
		$players = $this->db->query('SELECT * FROM users ORDER BY username ASC');
		
		return $players->num_rows > 0 ? $players->result() : FALSE;
	}
	
	function edit($id, $username, $email, $password, $sex)
	{
		$this->db->query('UPDATE users SET username=?, email=?, sex=? WHERE id=?',
			array($username, $email, $sex, $id));
			
		if($password)
		{
			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
			$hashed_password = $hasher->HashPassword($password);
			
			$this->db->query('UPDATE users SET password=? WHERE id=?', array($hashed_password));
		}
	}
	
	function getTournaments($id, $only_confirmed=false)
	{
		$sql = 'SELECT
					t.*
				FROM 
					tournaments AS t,
					tournament_players AS tp
				WHERE
					t.id = tp.tid AND
					tp.pid = ? '
				. ($only_confirmed ? ' AND tp.confirmed = 1' : '') . 
				' ORDER BY
					t.start_date DESC';
			
		$query = $this->db->query($sql,
			array($id));
		
		return $query->num_rows > 0 ? $query->result() : FALSE;
	}
	
	function getAdmins()
	{
		$query = $this->db->query('SELECT * FROM users WHERE level=?', array('admin'));
		return $query->num_rows > 0 ? $query->result() : FALSE;
	}
}

?>
