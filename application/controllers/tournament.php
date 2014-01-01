<?php

class Tournament extends GS_Controller {

	var $_date_regex = '/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/';
	var $_datetime_regex = '/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4}) ([0-9]{1,2}):([0-9]{2,2})/';

	function __construct()
	{
		parent::__construct();

		if(!$this->tank_auth->is_logged_in())
		{
			$this->session->set_userdata(array('login_return' => $this->uri->uri_string()));
			redirect('/auth/login/');
		}

		$this->data['breadcrumbs'][] = array(
			'url' => '/tournament/',
			'text' => _('Tournaments')
		);
	}

	function index()
	{
		$this->data['future_tournaments'] = $this->tournament_model->getAll('future', $this->tank_auth->get_user_id());
		$this->data['past_tournaments'] = $this->tournament_model->getAll('past', $this->tank_auth->get_user_id());

		$this->data['title'] = _('Tournaments');
		$this->data['content_view'] = 'tournaments/index';

		// for the table headers
		$this->load->helper('array');

		$this->load->view('skeleton', $this->data);
	}

	function view($tournament_id)
	{
		$this->load->helper('markdown');

		if($this->input->post('signupToTrip'))
		{
			$this->tripleg_model->addPlayer(
				$this->input->post('tlid'),
				$this->tank_auth->get_user_id()
			);
		}

		if($this->input->post('signoffFromTrip'))
		{
			$this->tripleg_model->removePlayer(
				$this->input->post('tlid'),
				$this->tank_auth->get_user_id()
			);
		}

		$this->data['tournament'] = $this->tournament_model->get($tournament_id);

		// player lists
		$this->data['teams'] = $this->tournament_model->getTeams($tournament_id);
		foreach($this->data['teams'] as $team)
		{
			$team->players = $this->team_model->getTournamentPlayers($tournament_id, $team->id);

			if($team->players)
			{
				$team->males = count(array_filter($team->players, function($p) { return $p->sex == 'M'; }));
				$team->females = count(array_filter($team->players, function($p) { return $p->sex == 'F'; }));
			} else {
				$team->males = 0;
				$team->females = 0;
			}
		}

		$this->data['unassigned']['players'] = $this->tournament_model->getUnassignedPlayers($tournament_id);
		if($this->data['unassigned']['players'])
		{
			$this->data['unassigned']['males'] = count(array_filter($this->data['unassigned']['players'], function($p) { return $p->sex == 'M'; }));
			$this->data['unassigned']['females'] = count(array_filter($this->data['unassigned']['players'], function($p) { return $p->sex == 'F'; }));
		} else {
			$this->data['unassigned']['males'] = 0;
			$this->data['unassigned']['females'] = 0;
		}

		$this->data['waiting']['players'] = $this->tournament_model->getPlayers($tournament_id, false);
		if($this->data['waiting']['players'])
		{
			$this->data['waiting']['males'] = count(array_filter($this->data['waiting']['players'], function($p) { return $p->sex == 'M'; }));
			$this->data['waiting']['females'] = count(array_filter($this->data['waiting']['players'], function($p) { return $p->sex == 'F'; }));
		} else {
			$this->data['waiting']['males'] = 0;
			$this->data['waiting']['females'] = 0;
		}

		// travel details
		$u_end_date = mysql_to_unix($this->data['tournament']->end_date);

		$last_day = mktime(
			0, 0, 0,
			date('m', $u_end_date),
			date('d', $u_end_date),
			date('Y', $u_end_date)
		);

		$trips= $this->tripleg_model->getTripsForTournament($tournament_id);
		if($trips)
		{
			foreach($trips as $trip)
			{
				$trip->passengers = $this->tripleg_model->getTripPassengers($trip->leg_id);
				$trip->player_on_it  =$this->tripleg_model->isPlayerOnIt($trip->leg_id, $this->tank_auth->get_user_id());

				$this->data['trips'][mysql_to_unix($trip->departure_time) < $last_day ? 'way' : 'return'][] = $trip;
			}
		} else {
			$this->data['trips'] = false;
		}

		// payment details
		$this->data['player_owes'] = $this->player_model->getPlayerDebtByTournament($tournament_id, $this->tank_auth->get_user_id());

		$this->data['title'] = $this->data['tournament'] ?  $this->data['tournament']->name : _("Tournament not found");

		$this->data['breadcrumbs'][] = array(
			'url' => '/tournament/view/'.$tournament_id,
			'text' => $this->data['tournament']->name
		);

		$this->data['tank_auth'] = $this->tank_auth;
		$this->data['content_view'] = 'tournaments/view';
		$this->load->view('skeleton', $this->data);
	}

	function assign_players($tournament_id)
	{
		if(!$this->tank_auth->is_admin(array('tournament' => $tournament_id)))
			header(site_url(''));

		$team_id = $this->input->post("team_id");
		$player_ids = $this->input->post("player_ids");
		$player_ids = explode(",", $player_ids);

		switch($team_id)
		{
			case -1:
				foreach($player_ids as $player_id)
				{
					$this->tournament_model->drop_player($tournament_id, $player_id);
				}
				break;
			default:
				if(is_numeric($team_id) && $team_id >= 0)
					foreach($player_ids as $player_id)
					{
						$this->tournament_model->approve_player($tournament_id, $team_id, $player_id);
					}
		}

		redirect('tournament/view/'.$tournament_id);


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
		$this->data['form_action'] = _('Add');

		$this->data['teams'] = $this->team_model->getAll();
		$this->data['users'] = $this->player_model->getAll();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', _('Name'), 'required');
		$this->form_validation->set_rules('start_date', _('Start date'), 'callback_date_check');
		$this->form_validation->set_rules('end_date', _('End date'), 'callback_date_check|callback_is_after[start_date]');
		$this->form_validation->set_rules('signup_deadline', _('Deadline date'), 'callback_date_check|callback_is_before[end_date]');
		$this->form_validation->set_rules('notes', _('Notes'), 'trim');
		$this->form_validation->set_rules('teams[]', _('Teams'), 'required');

		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'tournaments/form';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->tournament_model->create(
				$this->input->post('name'),
				$this->input->post('notes'),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('start_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('end_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('signup_deadline')),
				$this->input->post('teams'),
				$this->input->post('admins')
			);

			header('Location: /');
		}
	}

	function edit($tournament_id)
	{
		if(!$this->tank_auth->is_admin(array('tournament' => $tournament_id)))
			header('Location: /');

		$this->data['title'] = _('Edit tournament');
		$this->data['form_action'] = _('Edit');

		$this->data['tournament'] = $this->tournament_model->get($tournament_id);

		$this->data['teams'] = $this->team_model->getAll();
		$selected_teams = $this->tournament_model->getTeams($tournament_id);

		$team_ids = array();
		foreach($selected_teams as $team)
			$team_ids[] = $team->id;

		$this->data['users'] = $this->player_model->getAll();
		$tournament_admins = $this->tournament_model->getAdmins($tournament_id);

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
		$this->form_validation->set_rules('signup_deadline', _('Deadline date'), 'callback_date_check|callback_is_before[end_date]');
		$this->form_validation->set_rules('notes', _('Notes'), 'trim');
		$this->form_validation->set_rules('teams[]', _('Teams'), 'required');

		$this->data['breadcrumbs'][] = array(
			'url' => '/tournament/view/'.$tournament_id,
			'text' => $this->data['tournament']->name
		);

		if($this->form_validation->run() === FALSE)
		{
			$this->data['content_view'] = 'tournaments/form';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->tournament_model->edit(
				$tournament_id,
				$this->input->post('name'),
				$this->input->post('notes'),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('start_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('end_date')),
				preg_replace($this->_date_regex, '\3-\2-\1', $this->input->post('signup_deadline')),
				$this->input->post('teams'),
				$this->input->post('admins')
			);

			header('Location: /tournament/view/'.$tournament_id);
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

			$this->email->from($this->config->item('via_email'));
			$this->email->reply_to($this->config->config['tank_auth']['webmaster_email'], 'Gestorneo Gremlin');

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

			$this->email->from($this->config->config['email']['via_email']);
			$this->email->reply_to($this->config->config['tank_auth']['webmaster_email'], 'Gestorneo Gremlin');

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

		$this->data['teams'] = $this->tournament_model->getTeams($tournament_id);
		foreach($this->data['teams'] as $team)
			$team->players = $this->team_model->getTournamentPlayers($tournament_id, $team->id);

		$this->data['players_unassigned'] = $this->tournament_model->getUnassignedPlayers($tournament_id);
		$this->data['players_waiting'] = $this->tournament_model->getPlayers($tournament_id, false);

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('subject', _('Subject'), 'required');
		$this->form_validation->set_rules('message', _('Message'), 'required');

		$this->data['breadcrumbs'][] = array(
			'url' => '/tournament/view/'.$tournament_id,
			'text' => $this->data['tournament']->name
		);
		$this->data['breadcrumbs'][] = array(
			'url' => site_url('tournament/email/'.$tournament_id),
			'text' => _('Email team')
		);

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

			$this->email->from($this->config->config['email']['via_email']);
			$this->email->reply_to($this->config->config['tank_auth']['webmaster_email'], 'Gestorneo Gremlin');

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

	function add_trip_leg($tournament_id)
	{
		$tournament = $this->tournament_model->get($tournament_id);

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('trip_type', _('Trip type'), 'required');

		if($this->input->post('submitTripByOther'))
		{
			$this->form_validation->set_rules('trip_name', _('Trip name'), 'required');
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
					$tournament_id,
					$this->input->post('trip_type'),
					$this->input->post('trip_name'),
					$this->input->post('other_origin'),
					preg_replace($this->_datetime_regex, '\3-\2-\1 \4:\5', $this->input->post('departure_time')),
					$this->input->post('other_destination'),
					preg_replace($this->_datetime_regex, '\3-\2-\1 \4:\5', $this->input->post('arrival_time'))
				);
			} elseif ($this->input->post('submitTripByCar')) {
				$this->tripleg_model->create(
					$this->tank_auth->get_user_id(),
					$tournament_id,
					$this->input->post('trip_type'),
					false,
					$this->input->post('car_origin'),
					preg_replace($this->_datetime_regex, '\3-\2-\1 \4:\5', $this->input->post('car_departure_time')),
					$this->input->post('car_destination'),
					false
				);
			} else {
				throw new Exception(_('Oops, if you see this message, something went horribly wrong. Press the back button and try again.'));
			}

			header('Location: /tournament/view/'.$tournament_id);
		}
	}

	function payments($tournament_id)
	{
		$tournament = $this->tournament_model->get($tournament_id);

		$this->data['tournament'] = $tournament;
		$payments = $this->tournament_model->getPayments($tournament->id);

		// group by player
		foreach($payments as $payment)
			$payment->players = $this->player_model->getPlayerPayments($payment->tpid);

		// get total for each player
		$players = $this->tournament_model->getPlayers($tournament->id);
		foreach($players as $player)
		{
			$player->amount_owed = 0;
			foreach($payments as $payment)
				foreach($payment->players as $pplayer)
					if($pplayer->plid == $player->id && !$pplayer->paid)
						$player->amount_owed += $payment->amount;
		}

		$this->data['payments'] = $payments;
		$this->data['players'] = $players;
		$this->data['title'] = _('Tournament payments');
		$this->data['content_view'] = 'tournaments/payments';

		$this->data['breadcrumbs'][] = array(
			'url' => '/tournament/view/'.$tournament_id,
			'text' => $tournament->name
		);

		$this->load->view('skeleton', $this->data);
	}
}

?>
