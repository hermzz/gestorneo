<?php

class Player extends GS_Controller {

	function __construct()
	{
		parent::__construct();
		
		if(!$this->tank_auth->is_logged_in())
		    redirect('/auth/login/');
	}
	
	function index()
	{
		$this->data['title'] = _("Players");
		$this->data['active_players'] = $this->player_model->getAll();
		$this->data['old_players'] = $this->player_model->getAll(false);
		
		$this->data['content_view'] = 'players/index';
		$this->load->view('skeleton', $this->data);
	}
	
	function view($id)
	{
		$this->data['player'] = $this->player_model->get($id);
		if($this->data['player'])
		{
			$this->data['title'] = $this->data['player']->username;
			$this->data['tournaments'] = $this->player_model->getTournaments($id, true);
		} else {
			$this->data['title'] = _("Player not found");
		}
		
		$this->data['content_view'] = 'players/view';
		$this->load->view('skeleton', $this->data);
	}
	
	function edit($id)
	{
		if(!$this->tank_auth->is_admin(array('player' => $id)))
			header('Location: /');
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->data['player'] = $this->player_model->get($id);
		
		$this->data['title'] = sprintf(_('Edit player %s'), $this->data['player']->username);
		
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|max_length['.$this->config->item('username_max_length', 'tank_auth').']');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|xss_clean|matches[password]');
		$this->form_validation->set_rules('sex', 'Sex', 'required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'players/edit';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->player_model->edit(
				$id,
				$this->input->post('username'),
				$this->input->post('email'),
				$this->input->post('password'),
				$this->input->post('sex')
			);
			
			header('Location: /player/view/'.$id);
		}
	}
	
	function disable($player_id)
	{
		if(!$this->tank_auth->is_admin())
			header('Location: /');
		
		$this->player_model->disable($player_id);
			
		header('Location: /player/view/'.$player_id);
	}
	
	function enable($player_id)
	{
		if(!$this->tank_auth->is_admin())
			header('Location: /');
		
		$this->player_model->enable($player_id);
			
		header('Location: /player/view/'.$player_id);
	}
}
	
?>
