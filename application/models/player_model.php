<?php

class Player_model extends CI_Model
{
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM users WHERE id='.$id);

		return $query->num_rows > 0 ? $query->row() : FALSE;
	}

	function getAll($active=true)
	{
		$players = $this->db->query(
			'SELECT
				*
			FROM
				users
			WHERE
				activated='.($active ? '1' : '0').'
			ORDER BY
				username ASC'
		);

		return $players->num_rows > 0 ? $players->result() : FALSE;
	}

	function create($username, $email, $sex)
	{
		$this->db->query('INSERT INTO users (username, email, sex, created) VALUES (?, ?, ?, NOW())',
			array($username, $email, $sex));

		 return $this->db->insert_id();
	}

	function edit($id, $username, $email, $password, $sex)
	{
		$this->db->query('UPDATE users SET username=?, email=?, sex=? WHERE id=?',
			array($username, $email, $sex, $id));

		if($password)
		{
			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
			$hashed_password = $hasher->HashPassword($password);

			$this->db->query('UPDATE users SET password=? WHERE id=?', array($hashed_password, $id));
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

	function search($name, $filter)
	{
		if($filter['tournament_id'])
		{
			$query = $this->db->query(
				'SELECT
					u.*
				FROM
					users AS u,
					tournament_players AS tp
				WHERE
					u.id = tp.pid AND
					tp.tid = ? AND
					username LIKE "%'.$this->db->escape_like_str($name).'%"
				ORDER BY
					u.username ASC',
				array($filter['tournament_id'])
			);
		} else {
			$query = $this->db->query(
				'SELECT * FROM users WHERE username LIKE "%'.$this->db->escape_like_str($name).'%"'
			);
		}

		return $query->num_rows > 0 ? $query->result() : FALSE;
	}

	function disable($id)
	{
		$this->db->query('UPDATE users SET activated=0 WHERE id=?', array($id));
	}

	function enable($id)
	{
		$this->db->query('UPDATE users SET activated=1 WHERE id=?', array($id));
	}

	function getPlayerPayments($tpid)
	{
		$query = $this->db->query(
			'SELECT
				pp.*,
				u.username
			FROM
				player_payments AS pp,
				users AS u
			WHERE
				pp.plid=u.id AND
				pp.tpid=?',
			array($tpid));

		return $query->num_rows > 0 ? $query->result() : array();
	}

	function getPlayerDebtByTournament($tournament_id, $player_id)
	{
		$query = $this->db->query(
			'SELECT
				SUM(tp.amount) AS total
			FROM
				tournament_payments AS tp,
				player_payments AS pp
			WHERE
				tp.tid = ? AND
				tp.tpid = pp.tpid AND
				pp.paid = 0 AND
				pp.plid = ?',
			array($tournament_id, $player_id)
		);

		if($query->num_rows > 0)
		{
			$row = $query->row();
			return $row->total;
		} else {
			return false;
		}
	}
}

?>
