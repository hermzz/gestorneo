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
		$results = $this->player_model->search($params['term'], array('tournament_id' => $params['tournament_id']));
		
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
	
	function add_payments()
	{
		if($this->input->post('tpid'))
		{
			$this->tournament_model->editPayments(
				$this->input->post('tpid'),
				$this->input->post('concept'),
				$this->input->post('amount'),
				$this->input->post('applies'),
				$this->input->post('pids')
			);
		} else {
			$this->tournament_model->addPayment(
				$this->input->post('tid'),
				$this->input->post('concept'),
				$this->input->post('amount'),
				$this->input->post('applies'),
				$this->input->post('pids')
			);
		}
		
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		echo $params['callback'].'('.json_encode(array('success' => true)).')';
	}
	
	function edit_payment()
	{
		$this->tournament_model->editPayment(
			str_replace('player-', '', $this->input->post('element_id')),
			$this->input->post('update_value')
		);
		
		echo $this->input->post('update_value');
	}
	
	function set_paid()
	{
		$this->tournament_model->setPaid(
			$this->input->post('tpid'),
			$this->input->post('plid'),
			$this->input->post('paid')
		);
		
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		echo $params['callback'].'('.json_encode(array('success' => true)).')';
	}
	
	function get_payment_data()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		
		$payment = $this->tournament_model->getPayment(
			$this->input->post('tpid')
		);
		
		$payment->players = $this->player_model->getPlayerPayments($payment->tpid);
		
		echo $params['callback'].'('.json_encode(array('success' => true, 'data' => $payment)).')';
	}
	
	function delete_payment()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		
		$payment = $this->tournament_model->deletePayment(
			$this->input->post('tpid')
		);
		
		echo $params['callback'].'('.json_encode(array('success' => true)).')';
	}
}

?>
