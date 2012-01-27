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
			$this->load->library('db');
			$this->load->library('users/auth');
				
			if (!class_exists('Role_model'))
			{
				$this->load->model('roles/Role_model','role_model');
			}

			$role = array($this->role_model->find_all_by('role_id', $this->auth->role_id()));
			$role = array($role[0][0]);
			$role_name = $role[0]->role_name;

			if ( $role_name == 'Administrator' || $role_name == 'Editor' )
			{
				Template::redirect(SITE_AREA);
			}

			Template::redirect('/media');
		}

		Template::render();
	}

	//--------------------------------------------------------------------


}