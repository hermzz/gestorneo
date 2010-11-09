<?php

class Team extends GS_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->load->scaffolding('teams');
        
		if(!$this->tank_auth->is_logged_in())
		    redirect('/auth/login/');
	}
	
	function index()
	{
		$this->data['teams'] = $this->team_model->getAll();
		
		$this->data['title'] = _('Teams');
		$this->data['content_view'] = 'teams/index';
		
		$this->load->view('skeleton', $this->data);
	}
	
	function create()
	{
		if(!$this->tank_auth->is_admin())
			header('Location: /');
			
		$this->data['title'] = _('New team');
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', _('Name'), 'required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'teams/create';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->team_model->create(
				$this->input->post('name'),
				$this->input->post('description')
			);
			
			header('Location: /team');
		}
	}
}
