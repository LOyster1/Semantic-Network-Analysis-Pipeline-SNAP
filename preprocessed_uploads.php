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
		if($this->session->userdata('logged_in')){

			$files = array_filter(scandir($this->file_dir . '/preprocessed'), function($item)
			{
				return !is_dir($this->file_dir.'/' . $item);
			});

			$error = '';
			$user_info = array('files' => $files, 'error' => $error);

			$this->load->view('preprocessed_uploads', $user_info);
			$this->transfer($files);
		}
	}

	public function transfer($files)//Attempt to transfer .dl files to Semantic Networks
	{
		$this->index();
		$post=$this->input->post();
		$files=scandir($this->file_dir);
		foreach ($files as $file_name) 
		{
			$file_parts=pathinfo($file_name);
			switch ($file_parts['extension']) 
			{
				case "txt":
					echo "<script type='text/javascript'>alert('$file_name'+' is Text File');</script>";
					break;
				
				case "dl":
					echo "<script type='text/javascript'>alert('$file_name'+' is Graph File');</script>";
					$file_path=$this->file_dir.'/semantic_networks/';
					if(!file_put_contents($this->file_dir .'/semantic_networks/' .$file_name, $output))//Outputs to current users Preprocessed folder
					{
						$this->session->set_flashdata('flash_message', 'Could not write out file ');
						$this->load->view('preprocessed_uploads');
					}
					if(move_uploaded_file ($file_name , $file_path ))
					{
						echo "<script type='text/javascript'>alert('$file_name'+' moved to Semantic Networks Folder');</script>";
					}
					break;
			}
		}

	}

	///------------------------Input is whole directory---------------------------------------///
	// public function netgen($files)//--------------To be fixed, output destination incorrect---------------//
	// {
	// 	$this->index();
	// 	$post=$this->input->post();
	// 	//foreach ($files as $file => $file_name) 
	// 	//{
	// 		$netgen_path='/Applications/MAMP/htdocs/website_stuff/assets/netgen2/';
	// 		$output='';
	// 		$cmd='';
	// 		$file_path=$this->file_dir.'/preprocessed/';
	// 		//$file_path=$this->file_dir.'/'.$file_name;


	// 		//$file_path=$this->file_dir.'/';

	// 		//--------debug-----------//
	// 		//echo "<script type='text/javascript'>alert('$file_path');</script>";

	// 		//-------------------Generate .dl files for every file in preprocessed directory----------------------------------//
	// 		$cmd='java'. ' -jar '. $netgen_path. 'NetGenL3.jar '. $file_path;
	// 		//--------debug-----------//
	// 		//$message = "command: ".$cmd;
	// 		//echo "<script type='text/javascript'>alert('$message');</script>";

	// 		$output=shell_exec($cmd);
	// 		if($output==''){
	// 			$output="Netork Generation failed";
	// 		}

	// 		//--------debug-----------//
	// 		$this->file_dir=$this->file_dir.'/semantic_networks/';
	// 		echo "<script type='text/javascript'>alert('$this->file_dir');</script>";


	// 		//if(!file_put_contents($this->file_dir . '/semantic_networks/' . $file_name, $output))//Outputs to current users Preprocessed folder
	// 		//if(!file_put_contents($this->file_dir . '/semantic_networks/', $output))//Outputs to current users Preprocessed folder
	// 		if(!file_put_contents($this->file_dir, $output))//Outputs to current users Preprocessed folder
	// 			{
	// 				$this->session->set_flashdata('flash_message', 'Could not write out file ');
	// 				$this->load->view('preprocessed_uploads');
	// 			}
	// 	//}
	// 	$this->session->set_flashdata('flash_message', 'Saved to Semantic Networks');
	// 		$this->index();
	// }

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
			//$file_path=$this->file_dir.'/'.$file_name;
			//echo "<script type='text/javascript'>alert('File path: '+ '$file_path' +  ' n/file name processed: '+ '$file_name');</script>";

			//$file_path=$this->file_dir.'/';

			//--------debug-----------//
			//echo "<script type='text/javascript'>alert('$file_path');</script>";

			//-------------------Generate .dl files for every file in preprocessed directory----------------------------------//
			$cmd='java'. ' -jar '. $netgen_path. 'NetGenL3.jar '. $file_path;
			//--------debug-----------//
			$message = "command: ".$cmd;
			//echo "<script type='text/javascript'>alert('$message');</script>";

			$output=shell_exec($cmd);
			if($output==''){
				$output="Netork Generation failed";
			}
			//echo "<script type='text/javascript'>alert('Output: '+ '$output');</script>";


			//--------debug-----------//
			//$this->file_dir=$this->file_dir.'/semantic_networks/';
			//echo "<script type='text/javascript'>alert('$this->file_dir');</script>";


			if(!file_put_contents($this->file_dir .'/semantic_networks/' .$file_name, $output))//Outputs to current users Preprocessed folder
			//if(!file_put_contents($this->file_dir . '/semantic_networks/', $output))//Outputs to current users Preprocessed folder
			//if(!file_put_contents($this->file_dir, $output))//Outputs to current users Preprocessed folder
				{
					$this->session->set_flashdata('flash_message', 'Could not write out file ');
					$this->load->view('preprocessed_uploads');
				}
		}
		//Try to get extensions, transfer to other folder if .dl

		
		/*foreach ($files as $file => $file_name) 
		{
			//$file_path=$this->file_dir.'/preprocessed/'.$file_name;
			$file_parts=pathinfo($file_name);
			switch ($file_parts['extension']) 
			{
				case "txt":
					echo "<script type='text/javascript'>alert('$file_name'+' is Text File');</script>";
					break;
				
				case "dl":
					echo "<script type='text/javascript'>alert('$file_name'+' is Graph File');</script>";
					$file_path=$this->file_dir.'/semantic_networks/';
					if(!file_put_contents($this->file_dir .'/semantic_networks/' .$file_name, $output))//Outputs to current users Preprocessed folder
					{
						$this->session->set_flashdata('flash_message', 'Could not write out file ');
						$this->load->view('preprocessed_uploads');
					}
					if(move_uploaded_file ($file_name , $file_path ))
					{
						echo "<script type='text/javascript'>alert('$file_name'+' moved to Semantic Networks Folder');</script>";
					}
					break;
			}
		}*/


		$this->session->set_flashdata('flash_message', 'Saved to Semantic Networks');
			$this->index();
	}

	public function display_file(){
		$file = $this->uri->segment(3);
		$file_path = $this->file_dir . "/preprocessed/" . $file;

		echo nl2br(file_get_contents($file_path));
		exit;
	}

	public function submit_files(){
		//$this->form_validation->set_rules('checkbox[]', 'Network Generation', 'required');
		//if($this->form_validation->run() == FALSE){
		//	$this->load->view('preprocessed_uploads', $user_info);
		//}
		//else{
			if($this->input->post('file_action') == "delete"){
				$this->delete_files($this->input->post('checkbox'));
			} else{
				$this->netgen($this->input->post('checkbox'));
			} 
		//}
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
