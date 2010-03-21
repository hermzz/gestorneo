<?php

class Player extends Controller {

	function __construct()
	{
		parent::__construct();	

		$this->load->scaffolding('players');
		
		if(!$this->tank_auth->is_logged_in())
		    redirect('/auth/login/');
	}
	
	function index()
	{
		$data['title'] = "Players";
		$data['players'] = $this->player_model->getAll();
		
		$data['content_view'] = 'players/index';
		$this->load->view('skeleton', $data);
	}
	
	function view($id)
	{
		$data['player'] = $this->player_model->get($id);
		if($data['player'])
		{
			$data['title'] = $data['player']->username;
			$data['tournaments'] = $this->player_model->getTournaments($id, true);
		} else {
			$data['title'] = "Player not found";
		}
		
		$data['content_view'] = 'players/view';
		$this->load->view('skeleton', $data);
	}
}
	
?>
