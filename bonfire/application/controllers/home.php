<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Front_Controller {

	//--------------------------------------------------------------------
	
	public function index() 
	{
		parent::__construct();

		// acessing our userdata cookie
		$cookie = unserialize($this->input->cookie($this->config->item('sess_cookie_name')));
		$logged_in = isset ($cookie['logged_in']);
	
		if ($logged_in) {
			Template::redirect(SITE_AREA .'/content/media');
		}
		
		Template::render();
	}
	
	//--------------------------------------------------------------------
	

}