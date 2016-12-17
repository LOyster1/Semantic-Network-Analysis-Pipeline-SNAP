<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>UAA NLP Login</title>
	<style>
		#warning{color: red; font-weight: bold;}
	</style>
</head>
<body>
	<h1>UAA NLP Tools</h1>
<?php 
	echo validation_errors(); 
	$fattr = array('class' => 'form-signin');
	echo form_open('login/verifylogin', $fattr);
?>
		<fieldset>
			<legend>Login</legend>
			<div class='container'>
				<?php echo form_input(array('name' => 'email', 'id' => 'email', 'placeholder' => 'Email', 'class' => 'form-control', 'value' => set_value('email'))); ?>
				<?php echo form_error('email'); ?>
			</div>
			<div class='container'>
				<?php echo form_password(array('name' => 'password', 'id' => 'password', 'placeholder' => 'Password', 'class' => 'form-control', 'value' => set_value('password'))); ?>
				<?php echo form_error('password'); ?>
			</div>
			<div class='container'>
				<?php echo form_submit(array('value' => 'Login')); ?>
			</div>
			<p>Don't have an account? Click to <a href="<?php echo site_url(); ?>/register">Register</a></p>
			<p>Forgot your password? Click <a href="<?php echo site_url(); ?>/forgotpass">Here</a></p>
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
