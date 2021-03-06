<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends MY_Controller
{
	public $languages = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

		if(!$this->login->is_admin())
		{
			redirect('/admin/admin/access_denied', 'refresh');
		}

		// access granted, loading modules
		$this->load->model('News_model');
		$this->load->model("Images_model");
		$this->load->helper('form');

		$this->languages = array	(
										array(	'language_abbr' => 'se',
												'language_name' => 'Svenska',
												'id' => 1),
										array(	'language_abbr' => 'en',
												'language_name' => 'English',
												'id' => 2)
									);
    }

	public function index()
	{
		$this->overview();
	}

	function overview($message = '')
	{
		// Data for overview view
		$this->load->model('News_model');
		$main_data['news_array'] = $this->News_model->admin_get_all_news_overview();
		$main_data['notifications'] = $this->News_model->admin_get_notifications();
		$main_data['lang'] = $this->lang_data;

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/news_overview',  $main_data, true);
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

	function create()
	{
		// Data for forum view
		$main_data['lang'] = $this->lang_data;
		$main_data['is_editor'] = true;
		$main_data['languages'] = $this->languages;
		$main_data['images_array'] = $this->Images_model->get_all_images();

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/news_edit',  $main_data, true);
		$template_data['sidebar_content'] = $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

	function delete($id)
	{
		if($this->News_model->is_draft($id) && $this->News_model->delete($id))
			redirect('admin/news/overview/success', 'location');
		else
			redirect('admin/news/edit/'.$id.'/error', 'location');
	}

	function edit($id, $message = '')
	{
		// Data for overview view
		$main_data['news'] = $this->News_model->admin_get_news($id);
		$main_data['lang'] = $this->lang_data;
		$main_data['is_editor'] = true;
		$main_data['id'] = $id;
		$main_data['message'] = $message;
		$main_data['images_array'] = $this->Images_model->get_all_images();

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/news_edit',  $main_data, true);
		$template_data['sidebar_content'] = $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);

	}

	function edit_news($id)
	{
		$config = $this->Images_model->get_config();
		$this->load->library('upload', $config);

		// get the time
		$theTime = date("Y-m-d H:i",time());
		if(strtotime($this->input->post('post_date')) !== false)
		{
			$theTime = $this->input->post('post_date');
		}

		// get draft and approved setting
		$draft = 0; $approved = 0;
		if($this->input->post('draft') == 1)
		{
			$draft = 1;
		}
		if($this->input->post('approved') == 1)
		{
			$approved = 1;
		}


		$news_id = 0;
		$this->db->trans_start();
		if ($id == 0) {
			$translations = array();
			$success = false;

			// check if translations is added
			foreach($this->languages as $lang)
			{
				if($this->input->post('title_'.$lang['language_abbr']) != '' && $this->input->post('text_'.$lang['language_abbr']) != '')
				{
					array_push($translations,
						array(
							"lang" => $lang['language_abbr'],
							"title" => $this->input->post('title_'.$lang['language_abbr']),
							"text" => $this->input->post('text_'.$lang['language_abbr'])
							)
						);
					$success = true;
				}
			}

			if($success)
			{
				$news_id = $this->News_model->add_news($this->login->get_id(), $translations, $theTime, $draft, $approved);
			}
		}
		else
		{
			// check if translations is added
			foreach($this->languages as $lang)
			{
				$theTitle = addslashes($this->input->post('title_'.$lang['language_abbr']));
				$theText = addslashes($this->input->post('text_'.$lang['language_abbr']));
				$this->News_model->update_translation($id, $lang['language_abbr'], $theTitle, $theText);
			}

			$data = array(
				'draft' => $draft,
				'approved' => $approved,
				'date' => $theTime,
				);

			$this->db->update('news', $data, array('id' => $id));
		}

		$images_id = 0;
		if($this->input->post('image_id') != '')
		{
			$images_id = $this->input->post('image_id');
		}
		else
		{
			if ($this->upload->do_upload('img_file'))
			{
				if ($news_id != 0)
					$id = $news_id;

				$images_id = $this->Images_model->add_uploaded_image($this->upload->data(), $this->login->get_id(), 'News', 'News');
			}
		}

		$this->Images_model->add_or_replace_news_image($id,$images_id);
		$this->db->trans_complete();
		redirect('admin/news/edit/'.($id ? $id : $news_id).'/success', 'location');
	}
}
