<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class content extends Admin_Controller {

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict('Media.Content.View');
		$this->load->model('media_model', null, true);
		$this->lang->load('media');


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
	public function index()
	{
		$data = array();
		$data['records'] = $this->media_model->find_all();

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
		$this->auth->restrict('Media.Content.Create');

		if ($this->input->post('submitcreate'))
		{
			if ($insert_id = $this->save_media())
			{
				// Log the activity
				$this->activity_model->log_activity($this->auth->user_id(), lang('media_act_create_record').': ' . $insert_id . ' : ' . $this->input->ip_address(), 'media');
					
				Template::set_message(lang("media_create_success"), 'success');
				Template::redirect(SITE_AREA .'/content/media');
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
		$this->auth->restrict('Media.Content.Edit');

		$id = (int)$this->uri->segment(5);

		if (empty($id))
		{
			Template::set_message(lang('media_invalid_id'), 'error');
			redirect(SITE_AREA .'/content/media');
		}

		if ($this->input->post('submit'))
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
		$this->auth->restrict('Media.Content.Delete');

		$id = $this->uri->segment(5);

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

		redirect(SITE_AREA .'/content/media');
	}

	//--------------------------------------------------------------------

	/*
	 Method: getimage()
	*/
	public function image($id)
	{
		return $this->media_model->get_field($id, 'media_media');
	}

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/*
	 Method: uploadfile()
	*/
	private function uploadfile()
	{
		$this->load->helper('application');

		$config['upload_path'] = dirname(module_file_path('media', 'uploads', '.uploads'));
		$config['allowed_types'] = 'gif|jpg|png';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload() )
		{
			$this->media_model->error = $this->upload->display_errors();

			return null;
		}

		$data = $this->upload->data();

		return $data;
	}

	private function readfile($filename)
	{
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		return $contents;
	}

	/*
	 Method: thumbnail()
	*/
	private function thumbnail() {
		// TODO Thumbnail from database
		$config['image_library'] = 'gd2';
		$config['source_image'] = '/path/to/image/mypic.jpg';
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 75;
		$config['height'] = 50;

		$this->load->library('image_lib', $config);

		$this->image_lib->resize();
	}

	/*
	 Method: getmime($id)
	*/
	private function getmime($id)
	{
		$mime = $this->media_model->get_field($id, 'media_mime');

		return $mime;
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

		$data = array();
		// 		$data['media_bf_users_id']        = $this->input->post('media_bf_users_id');
		// 		$data['media_tanggalupload']        = $this->input->post('media_tanggalupload');
		$data['media_judul']        = $this->input->post('media_judul');
		$data['media_deskripsi']        = $this->input->post('media_deskripsi');
		// 		$data['media_mime']        = $this->input->post('media_mime');
		// 		$data['media_media']        = $this->input->post('media_media');

		// User ID
		$data['media_bf_users_id'] = $this->auth->user_id();

		// Date Time
		$this->load->helper('date');
		$data['media_tanggalupload'] = date('Y-m-d H:i:s');

		// Media
		$uploaddata = $this->uploadfile();

		if ( ! empty($uploaddata) )
		{
			$filename = $uploaddata['full_path'];
			$data['media_mime'] = 'image/' . $uploaddata['image_type'];
			//$data['media_media'] = readfile($filename);
			$data['media_media'] = $filename;

			if (is_file($filename))
			{
				unlink($filename);
			}
		}
		else
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