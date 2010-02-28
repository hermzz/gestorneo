<?php

class Player extends Controller {

	function __construct()
	{
		parent::__construct();	

		$this->load->scaffolding('players');
		$this->load->library('tank_auth');
		
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
			$data['title'] = $data['player']->name;
			$data['tournaments'] = $this->player_model->getTournaments($id);
		} else {
			$data['title'] = "Player not found";
		}
		
		$data['content_view'] = 'players/view';
		$this->load->view('skeleton', $data);
	}
	
	function create()
	{
		$data['title'] = 'New Player';
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Name', 'required');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['content_view'] = 'players/create';
			$this->load->view('skeleton', $data);
		} else {
			$this->player_model->create(
				$this->input->post('name'),
				preg_replace('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4,4})/', '\3-\2-\1', $this->input->post('joined'))
			);
			
			header('Location: /player/');
		}
	}
}
	
?>
