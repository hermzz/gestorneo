<?php

class Practice_model extends CI_Model
{
	// get tournament by ID
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM practices WHERE id='.$id);

		return $query->num_rows > 0 ? $query->row() : FALSE;
	}

	function getAll($type='all', $check_user_status_id=false)
	{
		$this->db->select('t.*');
		$this->db->select('DATEDIFF(t.signup_deadline, NOW()) as days_to_signup');
		$this->db->from('practices AS t');
		$this->db->order_by('t.start_date', 'DESC');

		switch($type)
		{
			case 'past':
				$this->db->where('t.start_date <', 'NOW()', false);
				break;

			case 'future':
				$this->db->where('t.start_date >', 'NOW()', false);
				break;

			// Redundant
			// case 'all':
			// default;
		}

		$query = $this->db->get();

		return $query->num_rows > 0 ? $query->result() : array();
	}

}
