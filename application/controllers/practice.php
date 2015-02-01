<?php

class Practice extends GS_Controller {

	function __construct()
	{
		parent::__construct();

		if (!$this->tank_auth->is_logged_in())
		{
			$this->session->set_userdata(array('login_return' => $this->uri->uri_string()));
			redirect('/auth/login/');
		}

		$this->data['breadcrumbs'][] = array(
			'url' => '/practice/',
			'text' => _('Practice')
		);
	}

	function index()
	{
		// $this->data['future_tournaments'] = $this->tournament_model->getAll('future', $this->tank_auth->get_user_id());
		$this->data['practices'] = $this->practice_model->getUpcoming();

		$this->data['title'] = _('Practices');
		$this->data['content_view'] = 'practice/index';

		// for the table headers
		$this->load->helper('array');

		$this->load->view('skeleton', $this->data);
	}

}
