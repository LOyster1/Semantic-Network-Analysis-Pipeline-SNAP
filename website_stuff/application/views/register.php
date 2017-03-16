<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>User Registration</title>
		<style>
			#warning{ color: red; font-weight: bold; }
		</style>
	</head>
	<body>
		<h1>New User</h1>
		<?php echo validation_errors(); ?>
		<?php $fattr = array('class' => 'form-signin'); 
		echo form_open('register/registerUser', $fattr); ?>
			<fieldset>
				<legend>Registration</legend>
				<div class='row'>
					<?php echo form_input(array('name' => 'firstName', 'id' => 'firstName', 'placeholder' => 'First Name', 'class' => 'form-control', 'value' => set_value('firstName'))); ?>
					<?php echo form_error('firstName'); ?>
					<?php echo form_input(array('name' => 'lastName', 'id' => 'lastName', 'placeholder' => 'Last Name', 'class' => 'form-control', 'value' => set_value('lastName'))); ?>
					<?php echo form_error('lastName'); ?>
					<!-- <label for="firstName">First Name:</label>
					<input type="text" size="25" id="firstName" name="firstName"/> 
					<label for="lastName">Last Name:</label>
					<input type="text" size="25" id="lastName" name="lastName"/>
					!-->
				</div>
				<div class='row'>
					<?php echo form_input(array('name' => 'email', 'id' => 'email', 'placeholder' => 'Email', 'class' => 'form-control', 'value' => set_value('email'))); ?>
					<?php echo form_error('email'); ?>
					<!-- <label for="email">Email:</label>
					<input type="text" size="25" id="email" name="email"/>
					!-->
				</div>
				<div class='container'>
					<?php echo form_submit(array('value' => 'Sign Up')); ?>
					<!-- <input type="submit" value="Register"/>
					!-->
				</div>
			</fieldset>
		<?php echo form_close(); ?>
		<?php $arr = $this->session->flashdata(); 
			if(!empty($arr['flash_message'])){
				$html = '<p id="warning">';
				$html .= $arr['flash_message'];
				$html .= '</p>';
				echo $html;
			}
		?>
	</body>
</html>
