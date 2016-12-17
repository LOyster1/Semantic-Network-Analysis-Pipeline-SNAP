<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Raw_uploads extends CI_Controller{
	public $data;
	public $file_dir;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		$this->data = $this->session->userdata;
		$this->file_dir = $this->data['file_dir'];
	}

	public function index(){
		if($this->session->userdata('logged_in'))//Depending on who is logged in, determines which folder system is used
		{
			$files = array_filter(scandir($this->file_dir. '/raw'), function($item)
			{
				return !is_dir($this->file_dir.'/' . $item);
			});

			$error = '';
			$user_info = array('files' => $files, 'error' => $error);

			$this->load->view('raw_uploads', $user_info);
		}
	}

	public function display_file()
	{
		$file = $this->uri->segment(3);
		$file_path = $this->file_dir ."/raw/". $file;

		$file_handle = fopen($file_path, "r");
		$file_contents = fread($file_handle, filesize($file_path));
		fclose($file_handle);

		$text_data = array('raw_text' => $file_contents,
			'output' => '',
			'file_name' => $file);

		$this->load->view('preprocess', $text_data);
	}

	public function build_command($framework, $post)
	{//Choose which processing framework to use then which functionality to make use of
		$cmd = '';
		if($framework == 'corenlp'){
			if($post['tokenize'] != ''){
				$cmd .= ' tokenize';
			}
			if($post['sent_split'] != ''){
				$cmd .= ' sent_split';
			}
			if($post['pos_tag'] != ''){
				$cmd .= ' pos_tag';
			}
			if($post['lemmatize'] != ''){
				$cmd .= ' lemmatize';
			}
			if($post['ner_tag'] != ''){
				$cmd .= ' ner_tag';
			}
			return $cmd;
		}
		else if($framework == 'nltk'){
			if($post['tokenize'] != ''){
				$cmd .= ' tokenize';
			}
			if($post['sent_split'] != ''){
				$cmd .= ' sent_split';
			}
			if($post['pos_tag'] != ''){
				$cmd .= ' pos_tag';
			}
			if($post['lemmatize'] != ''){
				$cmd .= ' lemmatize';
			}
			if($post['ner_tag'] != ''){
				$cmd .= ' ner_tag';
			}
			return $cmd;
		}
		else if($framework == 'spacy'){
			if($post['tokenize'] != ''){
				$cmd .= ' tokenize';
			}
			if($post['sent_split'] != ''){
				$cmd .= ' sent_split';
			}
			if($post['pos_tag'] != ''){
				$cmd .= ' pos_tag';
			}
			if($post['lemmatize'] != ''){
				$cmd .= ' lemmatize';
			}
			if($post['ner_tag'] != ''){
				$cmd .= ' ner_tag';
			}
			return $cmd;
		}
	}
/*------------------------- Unsure if this function is required, Batch process seems to do the same thing --------------*/
/*	public function preprocess(){
		//Always need to tokenize for any framework
		$this->form_validation->set_rules('tokenize', 'Tokenize', 'required');
		//This path can differ depending on the local environment
		$preprocess_path = '/Applications/MAMP/htdocs/website_stuff/assets/preprocess/';

		if($this->form_validation->run() == FALSE){
			$this->session->set_flashdata('flash_message', 'Validation error');
			$this->load->view('preprocess');
		} else 
		{
			$post = $this->input->post();

			$file_path = $this->file_dir . '/raw/'. $post['file_name'];//get files from current users "raw" folder
			$output = '';
			$cmd = '';

			if($post['tokenize'] == 'corenlp'){ //Map to the path of the CoreNLP java file
				$preprocess_path .= 'corenlp/';
				$cmd .= 'java -cp ' .$preprocess_path. '*:' .$preprocess_path. ' StanfordCoreNlpDemo ' .$file_path;
				//$cmd = 'java -cp ' . $preprocess_path . 'corenlp/* -Xmx2000m edu.stanford.nlp.pipeline.StanfordCoreNLP -annotators ';
				$cmd .= $this->build_command('corenlp', $post);
				//$cmd .= ' -file ' .$this->file_dir;
				$output = shell_exec($cmd);
				if($output == ''){
					$output = "corenlp preprocessing failed";
				}
			}
			else if($post['tokenize'] == 'nltk')//Map to the path of the NLTK python file
			{
				$cmd .= 'python ' . $preprocess_path . 'nltk/nltk-nlp.py ' . $file_path;
				$cmd .= $this->build_command('nltk', $post);

				$output = shell_exec($cmd);
				if($output == ''){
					//$output = "nltk preprocessing failed";
					$output = $cmd;
				}
			}
			else if($post['tokenize'] == 'spacy')//Map to the path of the Spacy python file
			{
				$cmd .= 'python ' . $preprocess_path . 'spacy/spacy-nlp.py ' . $file_path;
				$cmd .= $this->build_command('spacy', $post);
				$output = shell_exec($cmd);
				if($output == ''){
					$output = "spacy preprocessing failed";
				}
			}

			$data = array('output' => $output, 'raw_text' => $post['raw_textbox'], 'file_name' => $post['file_name']);
			$this->load->view('preprocess', $data);
		}
	}*/

	public function upload_text(){
		$data = $this->session->userdata;//Each users files is stored in its own folder so they don't access other users files
		$file_dir = $data['file_dir'] . '/raw';

		$config['upload_path'] = $file_dir;
		$config['allowed_types'] = 'txt';
		$config['max_size'] = '1000000';

		$files = $_FILES;
		$file_count = count($_FILES['raw_files']['name']);

		$this->load->library('upload');
		$this->upload->initialize($config);

		for($i = 0; $i < $file_count; $i++){
			$_FILES['raw_files']['name'] = $files['raw_files']['name'][$i];
			$_FILES['raw_files']['type'] = $files['raw_files']['type'][$i];
			$_FILES['raw_files']['tmp_name'] = $files['raw_files']['tmp_name'][$i];
			$_FILES['raw_files']['size'] = $files['raw_files']['size'][$i];
			$_FILES['raw_files']['error'] = $files['raw_files']['error'][$i];

			if($this->upload->do_upload('raw_files')){
				$this->session->set_flashdata('flash_message', 'Upload was successful!');
			} else{
				$error = array('error' => $this->upload->display_errors());
				$this->load->view('raw_uploads', $error);
			}
		}
		//redirect('raw_uploads', 'refresh');
		$this->index();
	}

	public function submit_files(){
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
			$this->batch_preprocess($this->input->post('checkbox'));

		}
	}

	public function download($files)
	{
		foreach($files as $file => $file_name)
		{
			$file_path=$this->file_dir.'/raw/'.$file_name;
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
			   // exit;
			}
			exit;
			
		}
		//exit;
		$this->index();
	}


	public function batch_preprocess($files)
	{
		$this->form_validation->set_rules('tokenize', 'Tokenize', 'required');
		//$this->form_validation->set_rules('raw_files[]', 'raw_files', 'required');

		if($this->form_validation->run() == FALSE){

			$this->session->set_flashdata('flash_message', 'Need to select at least tokenization or check at least one file for preprocessing.');
			$this->index();
		} else{
			$post = $this->input->post();
			foreach($files as $file => $file_name)
			{

				$preprocess_path = '/Applications/MAMP/htdocs/website_stuff/assets/preprocess/';
				$output = '';
				$cmd = '';

				$file_path = $this->file_dir . '/raw/' . $file_name;

				if($post['stemming'] != null){
					if($post['stemming'] == 'porter'){
					}
					else if($post['stemming'] == 'porter2'){
						$cmd = 'java ' . $preprocess_path . 'stem/porter2/SOMETHING HERE ' .$file_path;
						$output = shell_exec($cmd);
						if($output == ''){
							$output = "stemming failed";
						}
					}
					else if($post['stemming'] == 'lancaster'){
					}
				}

				if($post['tokenize'] == 'corenlp'){
					$preprocess_path .= 'corenlp/';
					$cmd .= 'java -cp ' .$preprocess_path. '*:' .$preprocess_path. ' StanfordCoreNlpDemo ' .$file_path;
					$cmd .= $this->build_command('corenlp', $post);
				}
				else if($post['tokenize'] == 'nltk'){
					$preprocess_path .= 'nltk/';
					$cmd .= 'python ' . $preprocess_path . 'nltk-nlp.py ' . $file_path;
					$cmd .= $this->build_command('nltk', $post);
				}
				else if($post['tokenize'] == 'spacy'){
					$preprocess_path .= 'spacy/';
					$cmd .= 'python ' . $preprocess_path . 'spacy-nlp.py ' . $file_path;
					$cmd .= $this->build_command('spacy', $post);
				}

				$output = shell_exec($cmd);
				
				if($output == ''){
					$output = "preprocessing failed";
				}

				if(!file_put_contents($this->file_dir . '/preprocessed/' . $file_name, $output))//Writes output to current users Preprocessed folder
				{
					$this->session->set_flashdata('flash_message', 'Could not write out file ' . $file_name);
					$this->load->view('raw_uploads');
				}

			}
			$this->session->set_flashdata('flash_message', 'Saved to Preprocessed');
			$this->index();
		}
	}

	//---------------TODO: fix this method, does nothing currently ----------------------------//
	public function delete_files($files_to_delete)
	{
		if(null != $this->input->post()){
			$file_path = $this->file_dir;
			
			foreach($files_to_delete as $file => $file_name){
				unlink($file_path . '/' . $file_name);
			}

			redirect('raw_uploads', 'refresh');
		}
	}
}
/* End of file raw_uploads.php */
/* Location: ./application/controllers/raw_uploads.php */
