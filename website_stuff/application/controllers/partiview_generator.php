<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Partiview_generator extends CI_Controller{
	public $data;
	public $file_dir;
	//public $post;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->data = $this->session->userdata;
		$this->file_dir = $this->data['file_dir'];
	}

	public function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$files = array_filter(scandir($this->file_dir . '/partiview_generator'), 
		    function($item)
			{
				return !is_dir($this->file_dir.'/' . $item);
			});
			$error = '';
			$user_info = array('files' => $files, 'error' => $error);
			$this->load->view('partiview_generator', $user_info);
		}
	}

	public function partiGeneration()
	{
		$this->index();
		$post=$this->input->post();
		$partiview_path='/Applications/MAMP/htdocs/website_stuff/assets/partiViewGen/Graph.jar ';
		$output='';
		$cmd='';
		$gexf_file='';
		$text_file='';
		
		$files=scandir($this->file_dir.'/partiview_generator/');
		foreach ($files as $file) //Set gexf and timestamp files for partiview
		{
			$file_parts=pathinfo($file);
			if($file_parts['extension']=="gexf")//Check File Extensions, transfer file to Semantic Networks if .dl 
			{
				$gexf_file=$this->file_dir.'/partiview_generator/' . $file . ' ';
			}
			if($file_parts['extension']=="txt")
			{
				$text_file=$this->file_dir.'/partiview_generator/' . $file ;
			}
			else;
		}
		//-------------------Generate .dl files for every file in preprocessed directory----------------------------------//
		$cmd='java'. ' -jar '. $partiview_path. $gexf_file  . $text_file ;//-- java -jar Graph.jar sample.gexf sampleDates.txt
		
		$output=shell_exec($cmd);
		if($output=='')
		{
			$output="Network Visualization Generation failed";
		}
		$this->session->set_flashdata('flash_message', 'Saved to Partiview');
		redirect('partiview_generator', 'refresh');
	}

	public function display_file()
	{
		$file = $this->uri->segment(3);
		$file_path = $this->file_dir . "/partiview_generator/" . $file;
		
		echo nl2br(file_get_contents($file_path));
		exit;
	}

	public function submit_files()//For executing commands
	{
			if($this->input->post('file_action') == "delete")
			{
				$this->delete_files($this->input->post('checkbox'));
			} 
			else if($this->input->post('file_action') == "download")
			{
				$this->download($this->input->post('checkbox'));
			} 
			else{
				$this->partiGeneration($this->input->post('checkbox'));
			} 
	}

	public function download($files)
	{
		foreach($files as $file => $file_name)
		{
			$file_path=$this->file_dir.'/partiview_generator/'.$file_name;
			if (file_exists($file_path)) 
			{
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file_path));
			    readfile($file_path);
			    exit;
			    $this->index();
			}
			else 
			{
				$this->index();
			}
			
		}
	}

	public function delete_files($files_to_delete){
		$source=$this->file_dir. '/partiview_generator/';
			foreach($files_to_delete as $file){
				$delete[] = $source.$file;
			}
			foreach($delete as $file){
				unlink($file);
			}
			redirect('partiview_generator', 'refresh');
	}
}

/* End of file preprocessed_uploads.php */
/* Location: ./application/controllers/preprocessed_uploads.php */
