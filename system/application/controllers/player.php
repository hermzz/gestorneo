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
		$this->data['players'] = $this->player_model->getAll();
		
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
}
	
?>
