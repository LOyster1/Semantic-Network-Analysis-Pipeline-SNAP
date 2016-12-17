<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Preprocessed_uploads extends CI_Controller{
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

			$files = array_filter(scandir($this->file_dir . '/preprocessed'), 
		    function($item)
			{
				return !is_dir($this->file_dir.'/' . $item);
			});

			$error = '';
			$user_info = array('files' => $files, 'error' => $error);

			$this->load->view('preprocessed_uploads', $user_info);
			
		}
		
	}
	
	// public function transfer()//Attempt to transfer .dl files to Semantic Networks, currently only exports file name to Semantic Networks
	// {
	// 	//$this->index();
	// 	$post=$this->input->post();
	// 	$files=scandir($this->file_dir. '/preprocessed');

	// 	foreach ($files as $file_name) 
	// 	{
	// 		$file_parts=pathinfo($file_name);
	// 		if($file_parts['extension']=="dl")//Check File Extensions, transfer file to Semantic Networks if .dl 
	// 		{
				
	// 			//--TO DO: Delete .dl files from "preprocessed" folder after transfer so that they only exist in "semantic Networks"
	// 				// if(is_uploaded_file($_FILES[$file_name]['name']))
	// 				// {
	// 				// 	echo "<script type='text/javascript'>alert('$file_name'+' is uploaded');</script>";
	// 				// }

	// 				$file_path=$this->file_dir.'/semantic_networks/';
	// 			//	if(!move_uploaded_file ($file_name , $file_path))
	// 				//if(!copy($file_name, $file_path[stream_context_create()]))
	// 				if(!file_put_contents($this->file_dir .'/semantic_networks/' .$file_name,$file_name))//Outputs to current users Preprocessed folder
	// 				{
	// 					$this->session->set_flashdata('flash_message', 'Could not write out file ');
	// 					$this->load->view('preprocessed_uploads');
	// 				}
	// 				if(move_uploaded_file ($file_name , $file_path ))
	// 				{
	// 					echo "<script type='text/javascript'>alert('$file_name'+' moved to Semantic Networks Folder');</script>";
	// 				}
	// 		}
	// 	}

	// }

	public function transfer()//Attempt two
	{
		$post=$this->input->post();
		$files=scandir($this->file_dir. '/preprocessed');
		$source=$this->file_dir. '/preprocessed/';
		$destination=$this->file_dir.'/semantic_networks/';
		foreach ($files as $file) 
		{
			$file_parts=pathinfo($file);
			if($file_parts['extension']=="dl")//Check File Extensions, transfer file to Semantic Networks if .dl 
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


	public function netgen($files)//--------------To be fixed, output destination incorrect---------------//
	{
		$this->index();
		$post=$this->input->post();
		foreach ($files as $file => $file_name) 
		{
			$netgen_path='/Applications/MAMP/htdocs/website_stuff/assets/netgen3/';
			$output='';
			$cmd='';
			$file_path=$this->file_dir.'/preprocessed/'.$file_name;

			//-------------------Generate .dl files for every file in preprocessed directory----------------------------------//
			$cmd='java'. ' -jar '. $netgen_path. 'NetGenL3.jar '. $file_path;
			//--------debug-----------//
			$message = "command: ".$cmd;

			$output=shell_exec($cmd);
			if($output==''){
				$output="Netork Generation failed";
			}

		}
		$this->session->set_flashdata('flash_message', 'Saved to Semantic Networks');
		$this->index();
		$this->transfer();//-----Attempt to transfer processed .dl files
	}

	public function display_file(){
		$file = $this->uri->segment(3);
		$file_path = $this->file_dir . "/preprocessed/" . $file;
		$file_parts=pathinfo($file);
		if($file_parts['extension']=="txt") //Check File Extensions, transfer file to Semantic Networks if .dl 
		{
			
				echo nl2br(file_get_contents($file_path));
				exit;
		}
		else
		{
			exit;
		}
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
				$this->netgen($this->input->post('checkbox'));
			} 
	}

	public function download($files)
	{
		foreach($files as $file => $file_name)
		{
			$file_path=$this->file_dir.'/preprocessed/'.$file_name;
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
			   // $this->index();
			}
			// else 
			// {
				
			//}
			
		}
		$this->index();
	}

	public function delete_files($files_to_delete){
		if(null != $this->input->post()){
			$file_path = $this->file_dir;
			
			foreach($files_to_delete as $file => $file_name){
				unlink($file_path . '/' . $file_name);
			}

			$this->index();
		}
	}
}

/* End of file preprocessed_uploads.php */
/* Location: ./application/controllers/preprocessed_uploads.php */
