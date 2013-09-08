<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class Association extends MY_Controller 
{
	
	public function index()
	{
		$this->page("board");
	}
	
	public function page()
	{
		$string = "association/";
		$numargs = func_num_args();
		$arg_list = func_get_args();
		for ($i = 0; $i < $numargs; $i++) {
			$string .= $arg_list[$i] . "/";
		}
		$string = rtrim($string,"/");
		$string = trim($string,"/");

		$this->load->model('Page_model');

		$main_data['name'] = $string;
		$main_data['lang'] = $this->lang_data;
		switch($string) 
		{
			case "association/board":
				$this->load->model('Group_model');
				$main_data['groups'] = $this->Group_model->get_group(1);
				$template_data['main_content'] = $this->load->view('group_overview',  $main_data, true);
				break;
			case "association/web":
				$this->load->model('Group_model');
				$main_data['groups'] = $this->Group_model->get_group(2);
				$template_data['main_content'] = $this->load->view('group_overview',  $main_data, true);
				break;
			case "association/documents":
				$this->load->model('Documents_model');
				$main_data['documents'] = $this->Documents_model->get_all_documents_for_group(1);
				$template_data['main_content'] = $this->load->view('documents_view',  $main_data, true);
				break;
			default:
				$main_data['content'] = $this->Page_model->get_page_by_name($string);
				$template_data['main_content'] = $this->load->view('about_page',  $main_data, true);
				break;
		}
		
		

		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['sidebar_content'] = $this->sidebar->get_association().$this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
		

		
	}

}
