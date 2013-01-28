<?php

class Misc extends GS_Controller
{
	function page($name)
	{
		$titles = array(
			'markdown_help' => _('Markdown help')
		);

		$this->data['title'] = $titles[$name];

		$this->data['content_view'] = 'misc/'.$name;
		$this->load->view('skeleton', $this->data);
	}

	function markdown_preview() {
		$this->load->helper('markdown');

		$md = $this->input->post('markdown');

		$this->output->set_output(markdown($md));
	}
}

?>
