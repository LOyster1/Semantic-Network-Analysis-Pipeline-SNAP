<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="<?php echo asset_url(); ?>css/menuStyle.css" type="text/css" />
		<title>Network Visualization</title>

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
		<li>Network Visualization</li>
		<li>Using the .gexf and .txt files from Gephi's Network Analysis, this page will generate files for use in Partiview</li>
		<li>These files include: nodes.speck, edges.speck, mesh.speck, .cf, .cmap, and .sct.</li>
		<br />
		<?php
			echo '<ul>';

			echo '<form id="checkbox_form" name="checkbox_form" method="post" action="partiview_generator/submit_files">';
			echo "<input type='checkbox' name='select_all' onClick='selectAll(this)' > Select All<br/>";
			foreach($files as $file => $file_name)
			{
				$file_parts=pathinfo($file_name);
				if(($file_parts['extension']=="gexf")
				|| ($file_parts['extension']=="pdf")
				|| ($file_parts['extension']=="speck")
				|| ($file_parts['extension']=="cf")
				|| ($file_parts['extension']=="cmap")
				|| ($file_parts['extension']=="sct")
				|| ($file_parts['extension']=="txt"))//Check File Extensions, display only produced files
				{
					
				
					echo form_checkbox(array(
						'name' => 'checkbox[]',
						'id' => 'checkbox[]',
						'value' => $file_name,
						'checked' => FALSE
					));

					$url = site_url() . '/partiview_generator/display_file/' . $file_name;
					echo '<a href="' .$url. '">' .$file_name. '</a><br/>';
				}
			}

			echo '<button name="file_action" value="visualize" type="submit">Network Visualization</button>';

			echo '<br/>';
			echo '<br/>';

			echo '<button name="file_action" value="delete" type="submit">Delete</button>	<button name="file_action" value="download" type="submit">Download</button>';

			echo '</form>';
			echo '</ul>';
		?>
	</body>
</html>