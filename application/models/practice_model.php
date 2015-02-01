<?php

class Practice_model extends CI_Model
{
	// get tournament by ID
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM practices WHERE id='.$id);

		return $query->num_rows > 0 ? $query->row() : FALSE;
	}

	/**
	* Sets next_date to the next programmed time
	*/
	function updateRecurring()
	{
		$this->db
			->select('t.*')
			->from('practices AS t')
			->where('t.next_date < NOW()')
			->where('t.recurring', true)
			->order_by('t.next_date', 'DESC');

		$query = $this->db->get();

		foreach ($query as $row)
		{
			$this->db->query(
				'UPDATE practices AS t
				SET t.next_date = DATE_ADD(t.next_date, INTERVAL 1 WEEK)
				WHERE t.next_date < NOW() AND t.recurring IS TRUE'
			);
		}

		return $this;
	}

	function getUpcoming()
	{
		$this->db->select('t.*')
		         ->from('practices AS t')
		         ->where('t.next_date >= NOW()')
		         ->order_by('t.next_date', 'DESC');

		$query = $this->db->get();

		return $query->num_rows > 0 ? $query->result() : array();
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
		}

		$query = $this->db->get();

		return $query->num_rows > 0 ? $query->result() : array();
	}

}
