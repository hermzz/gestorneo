<?php

class GS_Controller extends Controller
{
	function __construct()
	{
		parent::__construct();
		
		// Data array passed on to template
		$this->data = array();
		
		$this->data['breadcrumbs'][] = array(
			'url' => '/',
			'text' => _('Home')
		);
		
		$this->_set_language();
	}
	
	function _set_language()
	{
		$language_configs = array(
			'en' => array('en_GB.utf8', 'en_GB', 'en'),
			'es' => array('es_ES.utf8', 'es_ES', 'es')
		);
		
		$this->load->helper('cookie');
		
		$language = get_cookie('language');
		
		if(!$language || !isset($language_configs[$language]))
		{
			$language = 'en';
			set_cookie(
				array(
					'name' => 'language', 
					'value' => $language, 
					'expire' => 60*60*24*30,
					'domain' => $_SERVER['SERVER_NAME'],
					'path' => '/'
				)
			);
		}
		
		setlocale(
			LC_ALL, 
			$language_configs[$language][0],
			$language_configs[$language][1],
			$language_configs[$language][2]
		);

        $gettext_domain = 'messages';
        bindtextdomain($gettext_domain, APPPATH . "language/locale"); 
        bind_textdomain_codeset($gettext_domain, "UTF-8");
        textdomain($gettext_domain);
        
		$this->data['languages'] = array(
			'es' => _('Spanish'),
			'en' => _('English')
		);
		
		$this->data['selected_language'] = $language;
	}
}

?>
