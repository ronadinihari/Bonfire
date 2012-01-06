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
		$this->restrict();

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
		$this->restrict();

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
		$this->restrict();

		$id = (int)$this->uri->segment(3);

		if (empty($id))
		{
			Template::set_message(lang('media_invalid_id'), 'error');
			redirect('/media');
		}

		// Check if user is the owner or not
		if ( $this->get_media_bf_users_id($id) != $this->auth->user_id() )
		{
			print($this->get_media_bf_users_id($id) . ' != ' . $this->auth->user_id());
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

	//--------------------------------------------------------------------

	/*
	 Method: delete()

	Allows deleting of media data.
	*/
	public function delete()
	{
		$this->restrict();

		$id = $this->uri->segment(3);

		if (!empty($id))
		{
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
		$this->restrict();

		header('Content-Type: ' . $this->getmime($id));
		echo $this->media_model->get_field($id, 'media_media');
	}

	/*
	 Method: getimage()
	*/
	public function thumbnail($id)
	{
		$this->restrict();

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

			// 			if (is_file($filename)) {
			// 				unlink($filename);
			// 			}

			// 			if (is_file($thumbfile)) {
			// 				unlink($thumbfile);
			// 			}
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