<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Semantic_networks extends CI_Controller
{
	public $data;
	public $file_dir;
	//public $post;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$this->data = $this->session->userdata;
		//$this->file_dir = $this->data['file_dir']. '/preprocessed';
		$this->file_dir = $this->data['file_dir'];
	}

	public function index()
	{
		if($this->session->userdata('logged_in'))
		{

			$files = array_filter(scandir($this->file_dir . '/semantic_networks'), 
		    function($item)
			{
				return !is_dir($this->file_dir.'/' . $item);
			});

			$error = '';
			$user_info = array('files' => $files, 'error' => $error);

			$this->load->view('semantic_networks', $user_info);
		}	
	}
	
	public function transfer()//Attempt to transfer .dl files to Semantic Networks
	{
		$post=$this->input->post();
		$files=scandir('/Applications/MAMP/htdocs/website_stuff/');
		$source='/Applications/MAMP/htdocs/website_stuff/';
		$destination=$this->file_dir.'/partiview_generator/';
		foreach ($files as $file) 
		{
			$file_parts=pathinfo($file);
			if(($file_parts['extension']=="gexf") || ($file_parts['extension']=="pdf") || ($file_parts['extension']=="txt"))//Check File Extensions, transfer file to Semantic Networks if .dl 
			{
				if (in_array($file, array(".",".."))) continue;
				  // If we copied this successfully, mark it for deletion
				  if (copy($source.$file, $destination.$file)) 
				  {
				    $delete[] = $source.$file;
				  }

			}
		}
		foreach ($delete as $file) //Make so Files only appear in Semantic Networks, deletes them from 
		{
  			unlink($file);
		}
	}


	public function gephiGeneration()//--------------To be fixed, output destination incorrect, goes to Website Stuff---------------//
	{
		$this->index();
		$post=$this->input->post();
	
		$gephi_path='/Applications/MAMP/htdocs/website_stuff/assets/AutoGephiPipe/AutoGephiPipeV3_1.jar ';
		$output='';
		$cmd='';
		$file_path=$this->file_dir.'/semantic_networks/';

		//-------------------Generate .dl files for every file in preprocessed directory----------------------------------//
		$cmd='java'. ' -jar '. $gephi_path. $file_path;
		//--------debug-----------//
		$message = "command: ".$cmd;
		echo "<script type='text/javascript'>alert('$message');</script>";


		$output=shell_exec($cmd);
		if($output=='')
		{
			$output="Netork Generation failed";
		}
		$this->session->set_flashdata('flash_message', 'Saved to Partiview');
		$this->index();
		$this->transfer();
	}

	public function display_file()
	{
		$file = $this->uri->segment(3);
		$file_path = $this->file_dir . "/semantic_networks/" . $file;

		echo nl2br(file_get_contents($file_path));
		exit;
	}

	

	public function submit_files()
	{
			if($this->input->post('file_action') == "delete")
			{
				$this->delete_files($this->input->post('checkbox'));
			}
			else if($this->input->post('file_action') == "download")
			{
				$this->download($this->input->post('checkbox'));
			} 
			else 
			{
				$this->gephiGeneration($this->input->post('checkbox'));
			}
			
	}
	public function download($files)
	{
		foreach($files as $file => $file_name)
		{
			$file_path=$this->file_dir.'/semantic_networks/'.$file_name;
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



	public function delete_files($files_to_delete)
	{
		if(null != $this->input->post())
		{
			$file_path = $this->file_dir;
			
			foreach($files_to_delete as $file => $file_name)
			{
				unlink($file_path . '/' . $file_name);
			}

			$this->index();
		}
	}
}

/* End of file preprocessed_uploads.php */
/* Location: ./application/controllers/preprocessed_uploads.php */
