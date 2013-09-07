<?php

class Tournament_model extends CI_Model
{
	// get tournament by ID
	function get($id)
	{
		$query = $this->db->query('SELECT * FROM tournaments WHERE id='.$id);

		return $query->num_rows > 0 ? $query->row() : FALSE;
	}

	function getAll($type='all', $check_user_status_id=false)
	{
		$this->db->select('t.*');
		$this->db->select('DATEDIFF(t.signup_deadline, NOW()) as days_to_signup');
		$this->db->from('tournaments AS t');
		$this->db->order_by('t.start_date', 'DESC');

		switch($type)
		{
			case 'past':
				$this->db->where('t.start_date <', 'NOW()', false);
				break;

			case 'future':
				$this->db->where('t.start_date >', 'NOW()', false);
				break;

			case 'all':
			default;
		}

		if($check_user_status_id !== false && is_numeric($check_user_status_id)) {
			$this->db->select('tp.*');
			$this->db->select('tp.pid IS NOT NULL as player_signed_up');
			$this->db->select('tp.confirmed IS NOT NULL AS player_confirmed');
			$this->db->select('t.start_date < NOW() AS passed');
			//stupid joins ... is this right?
			$this->db->join('tournament_players AS tp', 'tp.tid = t.id AND tp.pid=' . $check_user_status_id, 'LEFT');
		}

		$tournaments = $this->db->get();

		return $tournaments->num_rows > 0 ? $tournaments->result() : array();
	}

	function countSignedUp($id, $sex=false)
	{
		if($sex)
		{
			$query = $this->db->query('SELECT
					COUNT(*) AS cnt
				FROM
					users AS u,
					tournament_players AS tp
				WHERE
					tp.tid = ? AND
					tp.pid = u.id AND
					u.sex = ?',
				array($id, $sex));
		} else {
			$query = $this->db->query('SELECT
					COUNT(*) AS cnt
				FROM
					tournament_players
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
				tournament_players AS tp
			WHERE
				u.id = tp.pid AND
				tp.confirmed = '.($confirmed ? 1 : 0).' AND
				tp.tid = '.$id.'
			ORDER BY
				u.username ASC'
		);

		return $query->num_rows ? $query->result() : array();
	}

	function getUnassignedPlayers($id)
	{
		$query = $this->db->query(
			'SELECT
				u.*
			FROM
				users AS u,
				tournament_players AS tp
			WHERE
				u.id = tp.pid AND
				tp.confirmed = 1 AND
				tp.team_id IS NULL AND
				tp.tid = '.$id.'
			ORDER BY
				u.username ASC'
		);

		return $query->num_rows ? $query->result() : array();
	}

	function create($name, $notes, $start_date, $end_date, $signup_deadline, $team_ids, $admin_ids)
	{
		$this->db->query('INSERT INTO tournaments (name, notes, start_date, end_date, signup_deadline) VALUES (?, ?, ?, ?, ?)',
			array($name, $notes, $start_date, $end_date, $signup_deadline));

		$tournament_id = $this->db->insert_id();

		if($team_ids)
			foreach($team_ids as $team_id)
				$this->db->query('INSERT INTO tournament_teams (tid, teid) VALUES (?, ?)',
					array($tournament_id, $team_id));

		if($admin_ids)
			foreach($admin_ids as $admin_id)
				$this->db->query('INSERT INTO tournament_admins (uid, tid) VALUES (?, ?)',
					array($admin_id, $tournament_id));
	}

	function edit($id, $name, $notes, $start_date, $end_date, $signup_deadline, $team_ids, $admin_ids)
	{
		$this->db->query('UPDATE tournaments SET name=?, notes=?, start_date=?, end_date=?, signup_deadline=? WHERE id=?',
			array($name, $notes, $start_date, $end_date, $signup_deadline, $id));

		$this->db->query('DELETE FROM tournament_teams WHERE tid=?', $id);

		if($team_ids)
			foreach($team_ids as $team_id)
				$this->db->query('INSERT INTO tournament_teams (tid, teid) VALUES (?, ?)',
					array($id, $team_id));

		// unset team id for player with a team that has been removed, ie: leave them as unassigned
		$this->db->query(
			'UPDATE
				tournament_players AS tp
					LEFT JOIN tournament_teams AS tt
						ON tp.tid=tt.tid AND tp.team_id=tt.teid
			SET
				tp.team_id = null
			WHERE
				tp.tid = ? AND
				tt.tid IS NULL',
			$id
		);

		// set tournament admins
		$this->db->query('DELETE FROM tournament_admins WHERE tid='.$id);

		if($admin_ids)
			foreach($admin_ids as $admin_id)
				$this->db->query('INSERT INTO tournament_admins (uid, tid) VALUES (?, ?)',
					array($admin_id, $id));
	}

	function add_player($tournament_id, $player_id)
	{
		$this->db->query('INSERT IGNORE INTO tournament_players VALUES (?, ?, 0, NULL)',
			array($player_id, $tournament_id));
	}

	function remove_player($tournament_id, $player_id)
	{
		$this->db->query('DELETE FROM tournament_players WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}

	function approve_player($tournament_id, $team_id, $player_id)
	{
		if(!$team_id)
			$team_id = null;

		$this->db->query('UPDATE tournament_players SET confirmed=1, team_id=? WHERE pid=? AND tid=?',
			array($team_id, $player_id, $tournament_id));
	}

	function drop_player($tournament_id, $player_id)
	{
		$this->db->query('UPDATE tournament_players SET confirmed=0, team_id=NULL WHERE pid=? AND tid=?',
			array($player_id, $tournament_id));
	}

	function is_signed_up($tournament_id, $player_id)
	{
		$query = $this->db->query('SELECT * FROM tournament_players WHERE tid=? AND pid=?',
			array($tournament_id, $player_id));

		return $query->num_rows ? $query->result() : FALSE;
	}

	function can_sign_up($tournament_id, $player_id)
	{
		// is the user already signed up?
		return !$this->is_signed_up($tournament_id, $player_id);

		//TODO: more stuff to add here:
		//	- Sex limitation (ie: womens only tournament)
	}

	function is_old($tournament)
	{
		return mktime() > mysql_to_unix($tournament->start_date);
	}

	function undeadlined($tournament)
	{
		return mktime(0, 0, 0, date('n'), date('j'), date('Y')) <= mysql_to_unix($tournament->signup_deadline);
	}

	function getTeams($tournament_id)
	{
		$query = $this->db->query(
			'SELECT
				t.*
			FROM
				tournament_teams AS tt,
				teams AS t
			WHERE
				tt.teid = t.id AND
				tt.tid = '.$tournament_id);

		return $query->num_rows ? $query->result() : array();
	}

	function getAdmins($tournament_id)
	{
		$query = $this->db->query(
			'SELECT
				u.*
			FROM
				users AS u,
				tournament_admins AS ta
			WHERE
				ta.uid = u.id AND
				ta.tid='.$tournament_id
		);

		return $query->num_rows ? $query->result() : array();
	}

	function addPayment($tournament_id, $concept, $amount, $applies, $pids)
	{
		$this->db->query('INSERT INTO tournament_payments (tid, concept, amount) VALUES (?, ?, ?)',
			array($tournament_id, $concept, $amount)
		);

		$payment_id = $this->db->insert_id();

		if($applies == 'all_team')
			$pids = array_map(function($p) { return $p->id; }, $this->getPlayers($tournament_id));

		foreach($pids as $pid)
		{
			$this->db->query('INSERT INTO player_payments (tpid, plid) VALUES (?, ?)',
				array($payment_id, $pid)
			);
		}
	}

	function editPayments($tpid, $concept, $amount, $applies, $pids)
	{
		$this->db->query('UPDATE tournament_payments SET concept=?, amount=? WHERE tpid=?',
			array($concept, $amount, $tpid)
		);

		$payment = $this->getPayment($tpid);

		if($applies == 'all_team')
			$pids = array_map(function($p) { return $p->id; }, $this->getPlayers($payment->tid));

		$this->db->query('DELETE FROM player_payments WHERE tpid=? AND paid=0', array($tpid));

		foreach($pids as $pid)
		{
			$this->db->query('INSERT IGNORE INTO player_payments (tpid, plid) VALUES (?, ?)',
				array($tpid, $pid)
			);
		}
	}

	function getPayments($tournament_id)
	{
		$query = $this->db->query(
			'SELECT
				*
			FROM
				tournament_payments
			WHERE
				tid = ?
			ORDER BY
				concept ASC',
			$tournament_id
		);

		return $query->num_rows ? $query->result() : array();
	}

	function getPayment($payment_id)
	{
		$query = $this->db->query('SELECT * FROM tournament_payments WHERE tpid=?', array($payment_id));

		return $query->num_rows ? $query->row() : array();
	}

	function editPayment($tpid, $amount)
	{
		$this->db->query('UPDATE tournament_payments SET paid=? WHERE tpid=?', array($amount, $tpid));
	}

	function setPaid($tpid, $plid, $paid)
	{
		$this->db->query('UPDATE player_payments SET paid=? WHERE tpid=? AND plid=?',
			array($paid, $tpid, $plid)
		);
	}

	function deletePayment($tpid)
	{
		$this->db->query('DELETE FROM player_payments WHERE tpid=?', array($tpid));
		$this->db->query('DELETE FROM tournament_payments WHERE tpid=?', array($tpid));
	}
}

?>
