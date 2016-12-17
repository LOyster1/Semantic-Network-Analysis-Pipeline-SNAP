<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldjdPureText extends JFormField
{
	protected $type = 'jdPureText';

	protected function getInput()
	{
		$class = 'inputbox';
		if ((string) $this->element['class'] != '') {
			$class = $this->element['class'];
		}
		return  '<div class="'.$class.'" style="padding-top:5px">'.$this->value.'</div>';
	}


	protected function getLabel()
	{
		echo '<div class="clr"></div>';
			return parent::getLabel();
		echo '<div class="clr"></div>';
	}
}