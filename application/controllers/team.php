<?php

class Team extends GS_Controller {

	function __construct()
	{
		parent::__construct();
       
		if(!$this->tank_auth->is_logged_in())
		{
			$this->session->set_userdata(array('login_return' => $this->uri->uri_string()));
			redirect('/auth/login/');
		}
		
		$this->data['breadcrumbs'][] = array(
			'url' => '/team/',
			'text' => _('Teams')
		);
	}
	
	function index()
	{
		$this->data['teams'] = $this->team_model->getAll();
		
		$this->data['title'] = _('Teams');
		$this->data['content_view'] = 'teams/index';
		
		$this->load->view('skeleton', $this->data);
	}

	function view($id)
	{
		$this->data['team'] = $this->team_model->get($id);
		if($this->data['team'])
		{
			$this->data['title'] = $this->data['team']->name;
			$this->data['tournaments'] = $this->team_model->getTournaments($id, true);
		} else {
			$this->data['title'] = _("Team not found");
		}

		$this->data['breadcrumbs'][] = array(
			'url' => '/team/view/'.$id,
			'text' => $this->data['team']->name
		);

		$this->data['content_view'] = 'teams/view';
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
	
	function edit($id)
	{
		if(!$this->tank_auth->is_admin())
			header('Location: /');
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->data['team'] = $this->team_model->get($id);
		$this->data['title'] = sprintf(_('Edit team "%s"'), $this->data['team']->name);
		
		$this->form_validation->set_rules('name', _('Name'), 'required');
		
		$this->data['breadcrumbs'][] = array(
			'url' => '/team/view/'.$id,
			'text' => $this->data['team']->name
		);
		
		if($this->form_validation->run() == FALSE)
		{
			$this->data['content_view'] = 'teams/edit';
			$this->load->view('skeleton', $this->data);
		} else {
			$this->team_model->edit(
				$id,
				$this->input->post('name'),
				$this->input->post('description')
			);
			
			header('Location: /team/edit/'.$this->data['team']->id);
		}
	}
}
