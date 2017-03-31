<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" href="<?php echo asset_url(); ?>css/menuStyle.css" type="text/css" />
		<title>Semantic Networks</title>

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
		<li>Network Analysis</li>
		<li>This page takes all the resulting .dl files of Undirected Graphs, combines them into a single succinct graph using Gephi.</li>
		<li>Nodes are grouped into circles and colored based on their modularity class then sized and ordered along the circle by Betweenness Centrality. </li>
		<li>Resulting .gexf file can be used in Gephi if you so choose, .pdf file gives user a brief 2D visualized over view of the network, and the .txt file of time stamped dates is used for 3D visualization.</li>
		<br />
		<?php
			echo '<ul>';

			echo '<form id="checkbox_form" name="checkbox_form" method="post" action="semantic_networks/submit_files">';
			echo "<input type='checkbox' name='select_all' onClick='selectAll(this)' > Select All<br/>";
			foreach($files as $file => $file_name)
			{
				$file_parts=pathinfo($file_name);
				if($file_parts['extension']=="dl")//Check File Extensions, display only produced files
				{
					echo form_checkbox(array(
						'name' => 'checkbox[]',
						'id' => 'checkbox[]',
						'value' => $file_name,
						'checked' => FALSE
					));

				$url = site_url() . '/semantic_networks/display_file/' . $file_name;
				echo '<a href="' .$url. '">' .$file_name. '</a><br/>';
				}
			}

			echo '<button name="file_action" value="netgen" type="submit">Semantic Network Generation</button>';

			echo '<br/>';
			echo '<br/>';



			echo '<button name="file_action" value="delete" type="submit">Delete</button>	<button name="file_action" value="download" type="submit">Download</button>';

			echo '</form>';
			echo '</ul>';
		?>
	</body>
</html>
