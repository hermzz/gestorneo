<?php

class Tripleg_model extends Model
{
	function get($id) {}
	
	function create($pid, $tid, $type, $company, $number, $origin, $departure, $destination, $arrival)
	{
		$this->db->query('INSERT INTO trip_leg 
			(trip_type, company_name, trip_number, origin, departure_time, destination, arrival_time) 
			VALUES (?, ?, ?, ?, ?, ?, ?)',
			array($type, $company, $number, $origin, $departure, $destination, $arrival));
			
		$tlid = $this->db->insert_id();
		
		$this->addPlayer($tlid, $tid, $pid);
	}
	
	function addPlayer($tlid, $tid, $pid)
	{
		$this->db->query('INSERT IGNORE INTO player_trip_leg (pid, tlid, tid) VALUES (?, ?, ?)',
			array($pid, $tlid, $tid));
	}
	
	function removePlayer($tlid, $tid, $pid)
	{
		$this->db->query('DELETE FROM player_trip_leg WHERE pid=? AND tlid=? AND tid=?',
			array($pid, $tlid, $tid));
	}
	
	function getTripsForTournament($tid)
	{
		$trips = $this->db->query(
			'SELECT tl.* FROM trip_leg AS tl, player_trip_leg AS ptl WHERE ptl.tid=? AND ptl.tlid = tl.leg_id GROUP BY tl.leg_id', 
			array($tid)
		);
		
		return $trips->num_rows > 0 ? $trips->result() : FALSE;
	}
	
	function getTripPassengers($tlid)
	{
		$passengers = $this->db->query(
			'SELECT u.* FROM player_trip_leg AS ptl, users AS u WHERE ptl.tlid = ? AND ptl.pid = u.id',
			array($tlid)
		);
		
		return $passengers->num_rows > 0 ? $passengers->result() : FALSE;
	}
	
	function isPlayerOnIt($tlid, $pid)
	{
		$rows = $this->db->query(
			'SELECT * FROM player_trip_leg WHERE tlid = ? AND pid = ?',
			array($tlid, $pid)
		);
		
		return $rows->num_rows > 0 ? TRUE : FALSE;
	}
}

?>
