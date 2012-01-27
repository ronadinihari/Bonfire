<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class media extends Front_Controller {

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// 		$this->auth->restrict('Media.Content.View');
		$this->load->model('media_model', null, true);
		$this->lang->load('media');

		$this->load->helper('html');
		$this->load->helper('date');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('users/auth');
		$this->load->library('upload');
		$this->load->helper('application');
		$this->load->model('activities/Activity_model', 'activity_model', true);
		$this->load->library('image_lib');
		$this->load->model('users/User_model', 'user_model');

		Assets::add_css('flick/jquery-ui-1.8.13.custom.css');
		Assets::add_js('jquery-ui-1.8.8.min.js');
		Assets::add_css('jquery-ui-timepicker.css');
		Assets::add_js('jquery-ui-timepicker-addon.js');
	}

	//--------------------------------------------------------------------

	/*
	 Method: index()

	Displays a list of form data.
	*/
	public function index($pageindex = 0)
	{
		$this->restrict('Media.View');

		$limit = 12;
		$offset = $pageindex * $limit;

		$data = array();
		$this->media_model->order_by('media_tanggalupload', 'desc');
		$this->media_model->limit($limit, $offset);
		$data['records'] = $this->media_model->find_all();
		$data['usernames'] = $this->getusernames($data['records']);
		$data['pagecount'] = ceil($this->media_model->count_all() / $limit);
		$data['pageindex'] = $pageindex;

		Assets::add_js($this->load->view('content/js', null, true), 'inline');

		Template::set('data', $data);
		Template::set('toolbar_title', "Manage media");
		Template::render();
	}

	//--------------------------------------------------------------------

	/*
	 Method: create()

	Creates a media object.
	*/
	public function create()
	{
		$this->restrict('Media.Create');

		if ($this->input->post('submitcreate'))
		{
			if ($insert_id = $this->save_media())
			{
				// Log the activity
				$this->activity_model->log_activity($this->auth->user_id(), lang('media_act_create_record').': ' . $insert_id . ' : ' . $this->input->ip_address(), 'media');

				Template::set_message(lang("media_create_success"), 'success');
				Template::redirect('/media');
			}
			else
			{
				Template::set_message(lang('media_create_failure') . $this->media_model->error, 'error');
			}
		}

		Template::set('toolbar_title', lang('media_create_new_button'));
		Template::set('toolbar_title', lang('media_create') . ' media');
		Template::render();
	}

	//--------------------------------------------------------------------

	/*
		Method: edit()

	Allows editing of media data.
	*/
	public function edit()
	{
		$this->restrict('Media.Edit');

		$id = (int)$this->uri->segment(3);

		if (empty($id))
		{
			Template::set_message(lang('media_invalid_id'), 'error');
			redirect('/media');
		}

		// Check if user is the owner or not
		if (!class_exists('Role_model'))
		{
			$this->load->model('roles/Role_model','role_model');
		}

		$role = array($this->role_model->find_all_by('role_id', $this->auth->role_id()));
		$role = array($role[0][0]);
		$role_name = $role[0]->role_name;

		if ( $this->get_media_bf_users_id($id) != $this->auth->user_id() && !($role_name == 'Administrator' || $role_name == 'Editor') )
		{
			Template::set_message(lang('media_owner_only'), 'error');
			Template::redirect('/media');
		}

		if ($this->input->post('submitedit'))
		{
			if ($this->save_media('update', $id))
			{
				// Log the activity
				$this->activity_model->log_activity($this->auth->user_id(), lang('media_act_edit_record').': ' . $id . ' : ' . $this->input->ip_address(), 'media');

				Template::set_message(lang('media_edit_success'), 'success');
			}
			else
			{
				Template::set_message(lang('media_edit_failure') . $this->media_model->error, 'error');
			}
		}

		Template::set('media', $this->media_model->find($id));

		Template::set('toolbar_title', lang('media_edit_heading'));
		Template::set('toolbar_title', lang('media_edit') . ' media');
		Template::render();
	}

	public function mobile()
	{
		$this->restrict('Media.Create');

		if ($this->input->post('media_username') &&
		$this->input->post('media_password') &&
		$this->input->post('media_title') &&
		$this->input->post('media_description') &&
		! empty($_FILES['media_file']['name']) )
		{
			if ($this->auth->login($this->input->post('media_username'), $this->input->post('media_password'), false) === true)
			{
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_logged').': ' . $this->input->ip_address(), 'users');
			}
			else
			{
				header("HTTP/1.1 400 Error 1");
				exit;
			}

			$records = $this->user_model->find_all_by('username', $this->input->post('media_username'));

			if (isset($records))
			{
				$record = (array) $records[0];
				$media_bf_users_id = $record['id'];
			}
			else
			{
				header("HTTP/1.1 400 Error 2");
				exit;
			}

			$uploaddata = $this->uploadmobilefile();

			if ( ! empty($uploaddata) )
			{
				$filename = $uploaddata['path'];
				list($width, $height, $type, $attr)= getimagesize($filename);
				$thumbfile = $this->makethumbnail($filename, $width, $height);
					
				if ( empty($thumbfile) )
				{
					header("HTTP/1.1 400 Error 3");
					// 					echo $this->media_model->error;
					exit;
				}
					
				$media_mime = $uploaddata['mime'];
				$media_media = $this->readimage($filename);
				$media_thumbnail = $this->readimage($thumbfile);
					
				if (is_file($filename)) {
					unlink($filename);
				}
					
				if (is_file($thumbfile)) {
					unlink($thumbfile);
				}
			}
			else
			{
				header("HTTP/1.1 400 Error 4");
				// 				echo $this->media_model->error;
				// 				print_r(array($_FILES));
				exit;
			}


			if (isset($media_bf_users_id) && isset($media_mime) && isset($media_media) && isset($media_thumbnail))
			{
				$data = array();
				$data['media_bf_users_id']   = $media_bf_users_id;
				$data['media_tanggalupload'] = date('Y-m-d H:i:s');
				$data['media_judul']         = $this->input->post('media_title');
				$data['media_deskripsi']     = $this->input->post('media_description');
				$data['media_mime']          = $media_mime;
				$data['media_media']         = $media_media;
				$data['media_thumbnail']     = $media_thumbnail;
			}
			else
			{
				header("HTTP/1.1 400 Error 5");
				die();
			}

			if (isset($data))
			{
				$id = $this->media_model->insert($data);
			}
			else
			{
				header("HTTP/1.1 400 Error 6");
				die();
			}

			if (isset($id) && is_numeric($id))
			{
				header("HTTP/1.1 200 Ok");
				die();
			}
			else
			{
				header("HTTP/1.1 400 Error 7");
				die();
			}
		}
		else
		{
			// 			ob_start();
			header("HTTP/1.1 400 Error 8");
			// 			header("Content-Type: text/plain");
			// 			echo $this->input->post('media_username') . '|';
			// 			echo $this->input->post('media_password') . '|';
			// 			echo $this->input->post('media_title') . '|';
			// 			echo $this->input->post('media_description') . '|';
			// 			echo (isset($_FILES['media_file']['name'])? $_FILES['media_file']['name']: 'not set') . '|';
			// 			ob_end_flush();
			die();
		}
	}

	//--------------------------------------------------------------------

	/*
	 Method: delete()

	Allows deleting of media data.
	*/
	public function delete()
	{
		$this->restrict('Media.Delete');

		$id = $this->uri->segment(3);

		if (!empty($id))
		{
			// Check if user is the owner or not
			if (!class_exists('Role_model'))
			{
				$this->load->model('roles/Role_model','role_model');
			}

			$role = array($this->role_model->find_all_by('role_id', $this->auth->role_id()));
			$role = array($role[0][0]);
			$role_name = $role[0]->role_name;

			if ( $this->get_media_bf_users_id($id) != $this->auth->user_id() && !($role_name == 'Administrator' || $role_name == 'Editor') )
			{
				Template::set_message(lang('media_owner_only'), 'error');
				Template::redirect('/media');
			}

			if ($this->media_model->delete($id))
			{
				// Log the activity
				$this->activity_model->log_activity($this->auth->user_id(), lang('media_act_delete_record').': ' . $id . ' : ' . $this->input->ip_address(), 'media');

				Template::set_message(lang('media_delete_success'), 'success');
			} else
			{
				Template::set_message(lang('media_delete_failure') . $this->media_model->error, 'error');
			}
		}

		redirect('/media');
	}

	//--------------------------------------------------------------------

	/*
	 Method: image()
	*/
	public function image($id)
	{
		$this->restrict('Media.View');

		header('Content-Type: ' . $this->getmime($id));
		echo $this->media_model->get_field($id, 'media_media');
	}

	/*
	 Method: getimage()
	*/
	public function thumbnail($id)
	{
		$this->restrict('Media.View');

		header('Content-Type: ' . $this->getmime($id));
		echo $this->media_model->get_field($id, 'media_thumbnail');
	}

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/*
	 Method: uploadfile()
	*/
	private function uploadfile()
	{
		$config['upload_path'] = dirname(module_file_path('media', 'uploads', '.uploads'));
		$config['allowed_types'] = 'gif|jpg|png';

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('media_file') )
		{
			$this->media_model->error = $this->upload->display_errors();

			return null;
		}

		$data = $this->upload->data();

		return $data;
	}

	/*
	 Method: uploadfile()
	*/
	private function uploadmobilefile()
	{
		$ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
		$mime = 'image/' . $ext;
		$upload_path = dirname(module_file_path('media', 'uploads', '.uploads'));
		$name = basename($_FILES['media_file']['tmp_name']) . '.' . $ext;
		$target_path = $upload_path . '/' . $name;

		move_uploaded_file($_FILES['media_file']['tmp_name'], $target_path);

		$data = array();
		$data['path'] = $target_path;
		$data['mime'] = $mime;

		return $data;
	}

	/*
	 Method: readimage($filename)
	*/
	private function readimage($filename)
	{
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		return $contents;
	}

	/*
	 Method: thumbnail()
	*/
	private function makethumbnail($source_image, $width, $height) {
		$r = $height / $width;
		$newwidth = 200;
		$newheight = $newwidth * $r;

		$config['image_library'] = 'gd2';
		$config['source_image'] = $source_image;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $newwidth;
		$config['height'] = $newheight;

		$this->image_lib->initialize($config);

		if ( ! $this->image_lib->resize() )
		{
			$this->media_model->error = $this->image_lib->display_errors();

			return null;
		}

		$ext = pathinfo($source_image, PATHINFO_EXTENSION);
		$result = dirname($source_image) . '/' . basename($source_image, '.' . $ext) . '_thumb.' . $ext;

		return $result;
	}

	/*
	 Method: getmime($id)
	*/
	private function getmime($id)
	{
		return $this->media_model->get_field($id, 'media_mime');
	}

	private function get_media_bf_users_id($id)
	{
		return $this->media_model->get_field($id, 'media_bf_users_id');
	}

	/*
	 Method: isloggedin()
	*/
	private function isloggedin()
	{
		// acessing our userdata cookie
		$cookie = unserialize($this->input->cookie($this->config->item('sess_cookie_name')));
		$logged_in = isset ($cookie['logged_in']);

		return $logged_in;
	}

	/*
	 Method: restrict()
	*/
	private function restrict()
	{
		if ( ! $this->isloggedin() ) {
			Template::set_message(lang('media_not_logged_in'), 'error');
			Template::redirect('/');
		}
	}

	/*
	 Method: getusername()
	*/
	private function getusername($id)
	{
		return $this->user_model->get_field($id, 'username');
	}

	/*
	 Method: getusernames()
	*/
	private function getusernames($records)
	{
		$usernames = array();
		$i = 0;

		if (isset($records) && is_array($records) && count($records)) :
		foreach ($records as $record) : $record = (array) $record;
		$usernames[$i++] = $this->getusername($record['media_bf_users_id']);
		endforeach;
		endif;

		return $usernames;
	}

	/*
	 Method: save_media()

	Does the actual validation and saving of form data.

	Parameters:
	$type	- Either "insert" or "update"
	$id		- The ID of the record to update. Not needed for inserts.

	Returns:
	An INT id for successful inserts. If updating, returns TRUE on success.
	Otherwise, returns FALSE.
	*/
	private function save_media($type='insert', $id=0)
	{

		// 		$this->form_validation->set_rules('media_bf_users_id','bf_users_id','required|max_length[20]');
		// 		$this->form_validation->set_rules('media_tanggalupload','Date','required');
		$this->form_validation->set_rules('media_judul','Title','required|max_length[50]');
		$this->form_validation->set_rules('media_deskripsi','Description','max_length[500]');
		// 		$this->form_validation->set_rules('media_mime','MIME','required|max_length[20]');
		// 		$this->form_validation->set_rules('media_media','Media','required');

		if ($this->form_validation->run() === FALSE)
		{
			return FALSE;
		}

		// make sure we only pass in the fields we want

		if ($type == 'insert')
		{
			$data = array();

			// User ID
			$data['media_bf_users_id'] = $this->auth->user_id();

			// Date Time
			$data['media_tanggalupload'] = date('Y-m-d H:i:s');
		}
		elseif ($type == 'update')
		{
			$data = $this->media_model->find($this->input->post('id'));
		}

		// 		$data['media_bf_users_id']        = $this->input->post('media_bf_users_id');
		// 		$data['media_tanggalupload']        = $this->input->post('media_tanggalupload');
		$data['media_judul']        = $this->input->post('media_judul');
		$data['media_deskripsi']        = $this->input->post('media_deskripsi');
		// 		$data['media_mime']        = $this->input->post('media_mime');
		// 		$data['media_media']        = $this->input->post('media_media');

		// Media
		if ( ! empty($_FILES['media_file']['name']) )
		{
			$uploaddata = $this->uploadfile();
		}
		elseif ( $type == 'insert' )
		{
			$this->media_model->error = br() . lang('media_no_file');

			return FALSE;
		}

		if ( ! empty($uploaddata) )
		{
			$filename = $uploaddata['full_path'];
			$thumbfile = $this->makethumbnail($filename, $uploaddata['image_width'], $uploaddata['image_height']);

			if ( empty($thumbfile) )
			{
				return FALSE;
			}

			$data['media_mime'] = 'image/' . $uploaddata['image_type'];
			$data['media_media'] = $this->readimage($filename);
			$data['media_thumbnail'] = $this->readimage($thumbfile);

			if (is_file($filename)) {
				unlink($filename);
			}

			if (is_file($thumbfile)) {
				unlink($thumbfile);
			}
		}
		elseif ( $type == 'insert' )
		{
			return FALSE;
		}

		if ($type == 'insert')
		{
			$id = $this->media_model->insert($data);

			if (is_numeric($id))
			{
				$return = $id;
			} else
			{
				$return = FALSE;
			}
		}
		else if ($type == 'update')
		{
			$return = $this->media_model->update($id, $data);
		}

		return $return;
	}

	//--------------------------------------------------------------------



}