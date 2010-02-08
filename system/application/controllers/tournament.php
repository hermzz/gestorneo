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

	function create()
	{
		$data['title'] = 'New tournament';
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('date', 'Date', 'callback_date_check');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['content_view'] = 'tournaments/create';
			$this->load->view('skeleton', $data);
		} else {
			$this->tournament_model->create(
				$this->input->post('name'),
				$this->input->post('notes'),
				preg_replace(
					'/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/', 
					'\3-\1-\2', 
					$this->input->post('date')
				)
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
			$this->form_validation->set_message('date_check', 'The %s is not a valid date');
			return false;
		}
	}
}

?>
