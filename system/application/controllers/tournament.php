<?php

class Tournament extends GS_Controller {

	var $_date_regex = '/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/';
	var $_datetime_regex = '/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4}) ([0-9]{1,2}):([0-9]{2,2})/';

	function __construct()
	{
		parent::__construct();
        
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
		
		if($this->input->post('signupToTrip'))
		{
			$this->tripleg_model->addPlayer(
				$this->input->post('tlid'), 
				$id,
				$this->tank_auth->get_user_id()
			);
		}
		
		if($this->input->post('signoffFromTrip'))
		{
			$this->tripleg_model->removePlayer(
				$this->input->post('tlid'), 
				$id,
				$this->tank_auth->get_user_id()
			);
		}
		
		$this->data['tournament'] = $this->tournament_model->get($id);
		
		$this->data['teams'] = $this->tournament_model->getTeams($id);
		foreach($this->data['teams'] as $team)
		{
			$team->players = $this->team_model->getTournamentPlayers($id, $team->id);
			
			if($team->players)
			{
				$team->males = count(array_filter($team->players, function($p) { return $p->sex == 'M'; }));
				$team->females = count(array_filter($team->players, function($p) { return $p->sex == 'F'; }));
			} else {
				$team->males = 0;
				$team->females = 0;
			}
		}
		
		$this->data['players_unassigned'] = $this->tournament_model->getUnassignedPlayers($id);
		$this->data['players_waiting'] = $this->tournament_model->getPlayers($id, false);
		
		$this->data['trips'] = $this->tripleg_model->getTripsForTournament($id);
		if($this->data['trips'])
		{
			foreach($this->data['trips'] as $trip)
			{
				$trip->passengers = $this->tripleg_model->getTripPassengers($trip->leg_id);
				$trip->player_on_it  =$this->tripleg_model->isPlayerOnIt($trip->leg_id, $this->tank_auth->get_user_id());
			}
		}

		$this->data['title'] = $this->data['tournament'] ?  $this->data['tournament']->name : _("Tournament not found");
		
		$this->data['tank_auth'] = $this->tank_auth;
		$this->data['content_view'] = 'tournaments/view';
		$this->load->view('skeleton', $this->data);
	}
	
	function approve_player($tournament_id, $player_id)
	{
		if(!$this->tank_auth->is_admin(array('tournament' => $tournament_id)))
			header('Location: /');
		
		if($this->input->post('team_id') != 'invalid')
			$this->tournament_model->approve_player($tournament_id, $this->input->post('team_id'), $player_id);
		
		header('Location: /tournament/view/'.$tournament_id);
	}
	
	function drop_player($tournament_id, $player_id)
	{
		if(!$this->tank_auth->is_admin(array('tournament' => $tournament_id)))
			header('Location: /');
			
		$this->tournament_model->drop_player($tournament_id, $player_id);
		
		header('Location: /tournament/view/'.$tournament_id);
	}

	function create()
	{
		if(!$this->tank_auth->is_admin())
			header('Location: /');
			
		$this->data['title'] = _('New tournament');
		
		$this->data['teams'] = $this->team_model->getAll();
		$this->data['users'] = $this->player_model->getAll();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', _('Name'), 'required');
		$this->form_validation->set_rules('start_date', _('Start date'), 'callback_date_check');
		$this->form_validation->set_rules('end_date', _('End date'), 'callback_date_check|callback_is_after[start_date]');
		$this->form_validation->set_rules('deadline_date', _('Deadline date'), 'callback_date_check|callback_is_before[end_date]');
		$this->form_validation->set_rules('notes', _('Notes'), 'trim');
		$this->form_validation->set_rules('teams[]', _('Teams'), 'required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'tournaments/create';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->tournament_model->create(
				$this->input->post('name'),
				$this->input->post('notes'),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('start_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('end_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('deadline_date')),
				$this->input->post('teams'),
				$this->input->post('admin_users')
			);
			
			header('Location: /');
		}
	}

	function edit($id)
	{
		if(!$this->tank_auth->is_admin(array('tournament' => $id)))
			header('Location: /');
			
		$this->data['title'] = _('Edit tournament');
		
		$this->data['tournament'] = $this->tournament_model->get($id);
		
		$this->data['teams'] = $this->team_model->getAll();
		$selected_teams = $this->tournament_model->getTeams($id);
		
		$team_ids = array();
		foreach($selected_teams as $team)
			$team_ids[] = $team->id;
		
		$this->data['users'] = $this->player_model->getAll();
		$tournament_admins = $this->tournament_model->getAdmins($id);
		
		$admin_ids = array();
		foreach($tournament_admins as $tournament_admin)
			$admin_ids[] = $tournament_admin->id;
			
		$this->data['tournament_admins'] = $admin_ids;
		
		$this->data['selected_teams'] = $team_ids;
			
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', _('Name'), 'required');
		$this->form_validation->set_rules('start_date', _('Start date'), 'callback_date_check');
		$this->form_validation->set_rules('end_date', _('End date'), 'callback_date_check|callback_is_after[start_date]');
		$this->form_validation->set_rules('deadline_date', _('Deadline date'), 'callback_date_check|callback_is_before[end_date]');
		$this->form_validation->set_rules('notes', _('Notes'), 'trim');
		$this->form_validation->set_rules('teams[]', _('Teams'), 'required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'tournaments/edit';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->tournament_model->edit(
				$id,
				$this->input->post('name'),
				$this->input->post('notes'),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('start_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('end_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('deadline_date')),
				$this->input->post('teams'),
				$this->input->post('admin_users')
			);
			
			header('Location: /tournament/view/'.$id);
		}
	}
	
	function date_check($date)
	{
		if(preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4,4}/', $date))
		{
			return true;
		} else {
			$this->form_validation->set_message('date_check', _('%s is not a valid date'));
			return false;
		}
	}
	
	function datetime_check($datetime)
	{
		if(preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4,4} [0-9]{1,2}:[0-9]{2,2}/', $datetime))
		{
			return true;
		} else {
			$this->form_validation->set_message('date_check', _('%s is not a valid date+time'));
			return false;
		}
	}
	
	function is_after($a, $b)
	{		
		if(strtotime(preg_replace($this->_date_regex, '\3-\2-\1', $a)) < strtotime(preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post($b))))
		{
			$this->form_validation->set_message('is_after', _('Start date must be before end date'));
			return false;
		} else {
			return true;
		}
	}
	
	function is_before($a, $b)
	{		
		if(strtotime(preg_replace($this->_date_regex, '\3-\2-\1', $a)) > strtotime(preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post($b))))
		{
			$this->form_validation->set_message('is_before', _('Deadline date must be before end date'));
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
			
			$this->email->to($this->config->config['tank_auth']['webmaster_email']);
			$this->email->cc(implode(',', array_map(function($p){ return $p->email; }, $this->player_model->getAdmins())));
			
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
			
			$this->email->to($this->config->config['tank_auth']['webmaster_email']);
			$this->email->cc(implode(',', array_map(function($p){ return $p->email; }, $this->player_model->getAdmins())));
			
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
		$this->load->helper('markdown');
		
		if(!$this->tank_auth->is_admin())
			header('Location: /');
			
		$tournament = $this->tournament_model->get($tournament_id);
		$this->data['tournament'] = $tournament;
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('subject', _('Subject'), 'required');
		$this->form_validation->set_rules('message', _('Message'), 'required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['title'] = sprintf(_('Email team for %s'), $tournament->name);
			
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
			
			$this->email->to($this->config->config['tank_auth']['webmaster_email']);
			$this->email->cc(implode(',', array_map(function($p){ return $p->email; }, $players)));
			
			$this->email->subject($this->input->post('subject'));
			$this->email->message(markdown($this->input->post('message')));
			
			$this->email->send();
			
			header('Location: /tournament/view/'.$tournament_id);
		}
	}
	
	function email_preview($tournament_id)
	{
		$this->load->helper('markdown');
		
		$tournament = $this->tournament_model->get($tournament_id);
		$this->data['tournament'] = $tournament;
		
		$this->data['title'] = _('Preview email');
		$this->data['content_view'] = 'tournaments/preview_email';
		
		$this->data['subject'] = $this->input->post('subject');
		$this->data['message'] = $this->input->post('message');
		
		$this->load->view('skeleton', $this->data);
	}
	
	function add_trip_leg($id)
	{
		$tournament = $this->tournament_model->get($id);
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('trip_type', _('Trip type'), 'required');
		
		if($this->input->post('submitTripByOther'))
		{
			$this->form_validation->set_rules('company_name', _('Company name'), 'required');
			$this->form_validation->set_rules('trip_number', _('Trip number'), 'required');
			$this->form_validation->set_rules('other_origin', _('Origin'), 'required');
			$this->form_validation->set_rules('departure_time', _('Departure time'), 'callback_datetime_check');
			$this->form_validation->set_rules('other_destination', _('Destination'), 'required');
			$this->form_validation->set_rules('arrival_time', _('Arrival time'), 'callback_datetime_check|callback_is_after[departure_time]');
		} elseif ($this->input->post('submitTripByCar')) {
			$this->form_validation->set_rules('car_origin', _('Origin'), 'required');
			$this->form_validation->set_rules('car_destination', _('Destination'), 'required');
			$this->form_validation->set_rules('car_departure_time', _('Departure time'), 'callback_datetime_check');
		}
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['tournament'] = $tournament;
			$this->data['title'] = _('Add trip leg for '.$tournament->name);
			$this->data['content_view'] = 'tournaments/add_trip_leg';
		
			$this->load->view('skeleton', $this->data);
		} else {
			if($this->input->post('submitTripByOther'))
			{
				$this->tripleg_model->create(
					$this->tank_auth->get_user_id(),
					$id,
					$this->input->post('trip_type'),
					$this->input->post('company_name'),
					$this->input->post('trip_number'),
					$this->input->post('other_origin'),
					preg_replace($this->_datetime_regex, '\3-\2-\1 \4:\5', $this->input->post('departure_time')),
					$this->input->post('other_destination'),
					preg_replace($this->_datetime_regex, '\3-\2-\1 \4:\5', $this->input->post('arrival_time'))
				);
			} elseif ($this->input->post('submitTripByCar')) {
				$this->tripleg_model->create(
					$this->tank_auth->get_user_id(),
					$id,
					$this->input->post('trip_type'),
					false,
					false,
					$this->input->post('car_origin'),
					preg_replace($this->_datetime_regex, '\3-\2-\1 \4:\5', $this->input->post('car_departure_time')),
					$this->input->post('car_destination'),
					false
				);
			} else {
				throw new Exception(_('Oops, if you see this message, something went horribly wrong. Press the back button and try again.'));
			}
			
			header('Location: /tournament/view/'.$id);
		}
	}
}

?>
