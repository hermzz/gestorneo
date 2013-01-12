<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Email
| -------------------------------------------------------------------------
| This file lets you define parameters for sending emails.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/libraries/email.html
|
*/
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = '\r\n';
$config['useragent'] = 'Gestorneo';
$config['via_email'] = strtolower($config['useragent']).'@'.$_SERVER['SERVER_NAME'];

/* End of file email.php */
/* Location: ./application/config/email.php */
