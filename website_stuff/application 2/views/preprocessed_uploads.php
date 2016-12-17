<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="<?php echo asset_url(); ?>css/menuStyle.css" type="text/css" />
		<title>Preprocessed Uploads</title>
	</head>
	<body>
		<?php include 'navi.php'; ?>

		<?php
			echo '<ul>';

			echo '<form id="checkbox_form" name="checkbox_form" method="post" action="preprocessed_uploads/submit_files">';
			foreach($files as $file => $file_name)
			{
				echo form_checkbox(array(
					'name' => 'checkbox[]',
					'id' => 'checkbox[]',
					'value' => $file_name,
					'checked' => FALSE
				));

			$url = site_url() . '/preprocessed_uploads/display_file/' . $file_name;
			echo '<a href="' .$url. '">' .$file_name. '</a><br/>';
			}

			echo '<button name="file_action" value="netgen" type="submit">Network Generation</button>';

			echo '<br/>';
			echo '<br/>';

			echo '<button name="file_action" value="delete" type="submit">Delete</button>';

			echo '</form>';
			echo '</ul>';
		?>
	</body>
</html>
