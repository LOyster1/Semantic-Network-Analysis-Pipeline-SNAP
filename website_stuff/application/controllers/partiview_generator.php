<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Partiview_generator extends CI_Controller{
	public $data;
	public $file_dir;
	//public $post;

	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$this->data = $this->session->userdata;
		//$this->file_dir = $this->data['file_dir']. '/preprocessed';
		$this->file_dir = $this->data['file_dir'];
	}

	public function index(){
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
	
/*	public function transfer()//Attempt to transfer .dl files to Semantic Networks
	{
		$post=$this->input->post();
		$files=scandir('/Applications/MAMP/htdocs/website_stuff/');
		$source='/Applications/MAMP/htdocs/website_stuff/';
		$destination=$this->file_dir.'/gephi_output/';
		foreach ($files as $file) 
		{
			$file_parts=pathinfo($file);
			if(($file_parts['extension']=="gexf") || ($file_parts['extension']=="pdf"))//Check File Extensions, transfer file to Semantic Networks if .dl 
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
*/

	public function partiGeneration()//--------------To be fixed, output destination incorrect, goes to Website Stuff---------------//
	{
		$this->index();
		$post=$this->input->post();
	
		$gephi_path='/Applications/MAMP/htdocs/website_stuff/assets/partiViewGen/Graph.jar ';
		$output='';
		$cmd='';
		$gexf_file='';
		$text_file='';

		//~~~~~~~~ Eric Pak was here ~~~~~~~~//
		$dates_file='';
		//~~~~~~~~                   ~~~~~~~~//
		
		$files=scandir($this->file_dir.'/partiview_generator/');
		//$source='/Applications/MAMP/htdocs/website_stuff/';
		//$destination=$this->file_dir.'/gephi_output/';
		foreach ($files as $file) 
		{
			$file_parts=pathinfo($file);
			if($file_parts['extension']=="gexf")//Check File Extensions, transfer file to Semantic Networks if .dl 
			{
				$gexf_file=$this->file_dir.'/partiview_generator/' . $file . ' ';
				echo "<script type='text/javascript'>alert('$gexf_file');</script>";

				//~~~~~~~~ Eric Pak was here ~~~~~~~~//
			//	$dates_file=$this->file_dir.'/partiview_generator/' . $file_parts['filename'] . 'FileDates.txt ';
				//~~~~~~~~                   ~~~~~~~~//

			}
			if($file_parts['extension']=="txt")
			{
				$text_file=$this->file_dir.'/partiview_generator/' . $file ;
				echo "<script type='text/javascript'>alert('$text_file');</script>";
			}
			else;
		}

		//$file_path=$this->file_dir.'/partiview_generator/raw_ner2007-12-10';



		//-------------------Generate .dl files for every file in preprocessed directory----------------------------------//
		$cmd='java'. ' -jar '. $gephi_path. $gexf_file  . $text_file ; //. $dates_file; //~~~~~~~~ Eric was also here ~~~~~~~~//
		//--------debug-----------//
		// $message = "command: ".$cmd;
		// echo "<script type='text/javascript'>alert('$message');</script>";


		$output=shell_exec($cmd);
		if($output=='')
		{
			$output="Network Visualization Generation failed";
		}

		
		$this->session->set_flashdata('flash_message', 'Saved to Partiview');
		$this->index();
		//$this->transfer();
	}

	public function display_file()
	{
		$file = $this->uri->segment(3);
		$file_path = $this->file_dir . "/partiview_generator/" . $file;
		
		echo nl2br(file_get_contents($file_path));
		exit;

	}

	public function submit_files()
	{
		//$this->form_validation->set_rules('checkbox[]', 'Network Generation', 'required');
		//if($this->form_validation->run() == FALSE){
		//	$this->load->view('preprocessed_uploads', $user_info);
		//}
		//else{
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
		//}
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
		if(null != $this->input->post()){
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
