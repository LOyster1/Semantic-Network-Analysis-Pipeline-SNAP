<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript" src="<?php echo asset_url(); ?>js/active_preprocess.js"></script>
		<link rel="stylesheet" href="/website_stuff/assets/css/menuStyle.css" type="text/css" />
		<title>Raw Uploads</title>

		<script type="text/javascript">
		    function selectAll(box) {
				var checkBoxes = document.getElementsByTagName('input');
				for(var i=0; i < checkBoxes.length; i++) {
					if(checkBoxes[i].type == 'checkbox') {
						checkBoxes[i].checked = box.checked;
					}
				}
			}
		</script>

	</head>
	<body>
		<?php include 'navi.php'; ?>
		<li>Natural Language Processing</li>
		<li >This page is for uploading raw .txt files and performing Natural Language Processing using provided tool kits, resulting in new .txt files.</li>
		<li>Provided toolkits allow for Stemming, Tokenization, Lemmatization, Sentence Splitting, Parts of Speech Recognition and Name Entity Recognition.</li>
		<li >File dates must be present in the file name in the form YYYY-MM-DD ie 2004-05-26.</li>
		<li >This date is used as time stamp to later represent the the file's nodes as a layer in 3D space.</li>
		<br />

		<?php 
		echo $error;
		$message = $this->session->flashdata();
		if(!empty($message['flash_message'])){
			$html = '<p id="warning">';
			$html .= $message['flash_message'];
			$html .= '</p>';
			echo $html;
		}

		echo validation_errors();
		?>

		<?php echo form_open_multipart('raw_uploads/upload_text'); ?>
		<div id="upload_area"> 
			<div class="upload_form" id="upload_form">
				<input type="file" name="raw_files[]" id="raw_files[]" multiple="multiple" accept="text/plain"/>
				<input type="submit" value="Upload" name="submit"/>
			</div>
		</div>
		</form>

		<?php 
			echo '<ul>';
			echo '<form id="checkbox_form" name="checkbox_form" method="post" action="raw_uploads/submit_files" >';
			//echo '<form id="checkbox_form" name="checkbox_form" method="post" action="/submit_files">';
			echo "<input type='checkbox' name='select_all' onClick='selectAll(this)' > Select All<br/>";
			foreach($files as $file => $file_name)
			{
				$file_parts=pathinfo($file_name);
				if($file_parts['extension']=="txt")//Check File Extensions, display only produced files
				{
					echo form_checkbox(array(
						'name' => 'checkbox[]',
						'id' => 'checkbox[]',
						'value' => $file_name,
						'checked' => FALSE
					));

					$url = site_url() . '/raw_uploads/display_file/' . $file_name;
					echo '<a href="'.$url.'">'.$file_name.'</a><br/>';
				}
			}

			echo '<br/>';
			 
			// <div>
			// 	<ul>
			// 		<li>Stemming</li><li>Tokenization</li><li>Sentence Splitting</li><li>POS Tagging</li><li>Lemmatization</li><li>Name-Entity-Recogition</li>
			// 	</ul>
			// </div>
			
			
			echo '<button name="file_action" value="batch_preprocess"  type="submit">Preprocess</button>';
			
			echo form_dropdown('stemming',
				array(
					'' => 'Stemming',
					'porter' => 'Porter',
					'porter2' => 'Porter2',
					'lancaster' => 'Lancaster'),
				'',
				array(
					'name' => 'stemming',
					'id' => 'stemming',
					'class' => 'stem',
					'data-active' => 'true'));
			echo form_dropdown('tokenize',
				array(
					'' => 'Tokenize',
					//'corenlp' => 'CoreNLP',
					'nltk' => 'NLTK',
					//'spacy' => 'spaCy'
					),
				'',
				array(
					'name' => 'tokenize',
					'id' => 'tokenize',
					'class' => 'preprocess',
					'data-active' => 'true'));
			echo form_dropdown('sent_split',
				array(
					'' => 'Sentence Split'),
				'',
				array(
					'name' => 'sent_split',
					'id' => 'sent_split',
					'class' => 'preprocess',
					'data-active' => 'false'));
			echo form_dropdown('pos_tag',
				array(
					'' => 'POS Tag'),
				'',
				array(
					'name' => 'pos_tag',
					'id' => 'pos_tag',
					'class' => 'preprocess',
					'data-active' => 'false'));
			echo form_dropdown('lemmatize',
				array(
					'' => 'Lemmatize'),
				'',
				array(
					'name' => 'lemmatize',
					'id' => 'lemmatize',
					'class' => 'preprocess',
					'data-active' => 'false'));
			echo form_dropdown('ner_tag',
				array(
					'' => 'NER Tag'),
				'',
				array(
					'name' => 'ner_tag',
					'id' => 'ner_tag',
					'class' => 'preprocess',
					'data-active' => 'false'));

			for($i = 0; $i < 5; $i++){
				echo '<br/>';
			}
			echo '<button name="file_action" value="delete" type="submit">Delete</button>	<button name="file_action" value="download" type="submit">Download</button>';

			echo '</form>';
			echo '</ul>';
		?>
	</body>
</html>
