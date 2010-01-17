<?php

class Tournament extends Controller {

	function __construct()
	{
		parent::__construct();	

		$this->load->scaffolding('tournaments');
	}
	
	function index()
	{
		$data['future_tournaments'] = $this->tournament_model->getAll('future');
		$data['past_tournaments'] = $this->tournament_model->getAll('past');
		
		$data['title'] = 'Tournaments';
		$data['content_view'] = 'tournaments/index';
		
		$this->load->view('skeleton', $data);
	}
	
	function view($id)
	{
		$data['tournament'] = $this->tournament_model->get($id);
		$data['players'] = $this->tournament_model->getPlayers($id);
		
		$data['title'] = $data['tournament'] ?  $data['tournament']->name : "Tournament not found";
		
		$data['content_view'] = 'tournaments/view';
		$this->load->view('skeleton', $data);
	}
}

?>
