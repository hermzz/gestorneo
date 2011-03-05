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
		
		$this->add_user($tlid, $tid, $pid);
	}
	
	function add_user($tlid, $tid, $pid)
	{
		$this->db->query('INSERT IGNORE INTO player_trip_leg (pid, tlid, tid) VALUES (?, ?, ?)',
			array($pid, $tlid, $tid));
	}
	
	function getTripsForTournament($tid)
	{
		$trips = $this->db->query(
			'SELECT tl.* FROM trip_leg AS tl, player_trip_leg AS ptl WHERE ptl.tid=? AND ptl.tlid = tl.leg_id', 
			array($tid)
		);
		
		return $trips->num_rows > 0 ? $trips->result() : FALSE;
	}
}

?>
