<?php

class Ajax extends GS_Controller 
{
	function team_autocomplete() 
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		$results = $this->team_model->search($params['term']);
		
		if($results)
		{
			$teams = array();
			foreach($results as $result)
				$teams[] = array('id' => $result->id, 'name' => $result->name);
			
			echo $params['callback'].'('.json_encode(array('success' => true, 'results' => $teams)).')';
		} else {
			echo $params['callback'].'('.json_encode(array('success' => false, 'message' => _('No results found'))).')';
		}
	}
	
	function player_autocomplete() 
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		$results = $this->player_model->search($params['term']);
		
		if($results)
		{
			$players = array();
			foreach($results as $result)
				$players[] = array('id' => $result->id, 'name' => $result->username);
			
			echo $params['callback'].'('.json_encode(array('success' => true, 'results' => $players)).')';
		} else {
			echo $params['callback'].'('.json_encode(array('success' => false, 'message' => _('No results found'))).')';
		}
	}
	
	function invite_player_to_tournament()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		
		$this->tournament_model->add_player($params['tid'], $params['pid']);
		
		echo $params['callback'].'('.json_encode(array('success' => true)).')';
	}
}

?>
