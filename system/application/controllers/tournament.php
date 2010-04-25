<?php

class Tournament extends GS_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->load->scaffolding('tournaments');
        
		if(!$this->tank_auth->is_logged_in())
		    redirect('/auth/login/');
	}
	
	function index()
	{
		$this->data['future_tournaments'] = $this->tournament_model->getAll('future');
		$this->data['past_tournaments'] = $this->tournament_model->getAll('past');
		
		$this->data['title'] = _('Tournaments');
		$this->data['content_view'] = 'tournaments/index';
		
		$this->load->view('skeleton', $this->data);
	}
	
	function view($id)
	{
		$this->load->helper('markdown');
	
		$this->data['tournament'] = $this->tournament_model->get($id);
		$this->data['players_confirmed'] = $this->tournament_model->getPlayers($id, true);
		$this->data['players_waiting'] = $this->tournament_model->getPlayers($id, false);

		$this->data['title'] = $this->data['tournament'] ?  $this->data['tournament']->name : _("Tournament not found");
		
		$this->data['tank_auth'] = $this->tank_auth;
		$this->data['content_view'] = 'tournaments/view';
		$this->load->view('skeleton', $this->data);
	}
	
	function approve_player($tournament_id, $player_id)
	{
		$this->tournament_model->approve_player($tournament_id, $player_id);
		
		header('Location: /tournament/view/'.$tournament_id);
	}
	
	function drop_player($tournament_id, $player_id)
	{
		$this->tournament_model->drop_player($tournament_id, $player_id);
		
		header('Location: /tournament/view/'.$tournament_id);
	}

	function create()
	{
		$this->data['title'] = _('New tournament');
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', _('Name'), 'required');
		$this->form_validation->set_rules('start_date', _('Start date'), 'callback_date_check');
		$this->form_validation->set_rules('end_date', _('End date'), 'callback_date_check|callback_is_after[start_date]');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'tournaments/create';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->tournament_model->create(
				$this->input->post('name'),
				$this->input->post('notes'),
				preg_replace('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/', '\3-\2-\1', $this->input->post('start_date')),
				preg_replace('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/', '\3-\2-\1', $this->input->post('end_date'))
			);
			
			header('Location: /');
		}
	}
	
	function date_check($date)
	{
		if(preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4,4}/', $date))
		{
			return true;
		} else {
			$this->form_validation->set_message('date_check', _('The %s is not a valid date'));
			return false;
		}
	}
	
	function is_after($a, $b)
	{		
		if(strtotime(preg_replace('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/', '\3-\2-\1', $a)) < strtotime(preg_replace('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/', '\3-\2-\1', $this->input->post($b))))
		{
			$this->form_validation->set_message('is_after', _('Start date must be before end date'));
			return false;
		} else {
			return true;
		}
	}
	
	function sign_up()
	{
		if($this->tank_auth->get_user_id() == $this->input->post('player_id'))
		{
			$this->tournament_model->add_player(
				$this->input->post('tournament_id'),
				$this->input->post('player_id')
			);
			
			$player = $this->player_model->get($this->input->post('player_id'));
			$tournament = $this->tournament_model->get($this->input->post('tournament_id'));
			
			$this->load->library('email');
			
			$this->email->from($this->config->config['tank_auth']['webmaster_email'], 'Gestorneo Gremlin');
			
			foreach($this->player_model->getAdmins()->result() as $admin)
				$this->email->to($admin->email);
			
			$this->email->subject($player->username.' signed up to '.$tournament->name);
			$this->email->message(
				$player->username.' has signed up to play at '.$tournament->name.'
				'.$this->config->config['base_url'].'tournament/view/'.$tournament->id
			);
			
			$this->email->send();
		}
		
		header('Location: /tournament/view/'.$this->input->post('tournament_id'));
	}
	
	function cancel_sign_up()
	{
		if($this->tank_auth->get_user_id() == $this->input->post('player_id'))
		{
			$this->tournament_model->remove_player(
				$this->input->post('tournament_id'),
				$this->input->post('player_id')
			);
			
			$player = $this->player_model->get($this->input->post('player_id'));
			$tournament = $this->tournament_model->get($this->input->post('tournament_id'));
			
			$this->load->library('email');
			
			$this->email->from($this->config->config['tank_auth']['webmaster_email'], 'Gestorneo Gremlin');
			
			foreach($this->player_model->getAdmins()->result() as $admin)
				$this->email->to($admin->email);
			
			$this->email->subject($player->username.' cancelled attendance to '.$tournament->name);
			$this->email->message(
				$player->username.' has cancelled attendance to play at '.$tournament->name.'
				'.$this->config->config['base_url'].'tournament/view/'.$tournament->id
			);
			
			$this->email->send();
		}
		
		header('Location: /tournament/view/'.$this->input->post('tournament_id'));
	}
	
	function email($tournament_id)
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('subject', _('Subject'), 'required');
		$this->form_validation->set_rules('message', _('Message'), 'required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['title'] = _("Email team");
			
			$this->data['tank_auth'] = $this->tank_auth;
			$this->data['content_view'] = 'tournaments/email';
			$this->load->view('skeleton', $this->data);
		} else {
			$players = array_merge(
				$this->tournament_model->getPlayers($tournament_id, true),
				$this->tournament_model->getPlayers($tournament_id, false)
			);
			
			$this->load->library('email');
			
			$this->email->from($this->config->config['tank_auth']['webmaster_email'], 'Gestorneo Gremlin');
			
			foreach($players as $player)
				$this->email->to($player->email);
			
			$this->email->subject($this->input->post('subject'));
			$this->email->message($this->input->post('message'));
			
			$this->email->send();
			
			header('Location: /tournament/view/'.$tournament_id);
		}
	}
}

?>
