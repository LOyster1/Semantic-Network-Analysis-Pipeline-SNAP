<?php


defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @since		1.6
 */
class JFormFieldjdCustomField7 extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'jdCustomField7';
    
    // we need the label text from the config table
    // TODO: a tooltip description is supported here /so we can add it later for jD custom data fields
    public function getLabel() {
        global $jlistConfig;
        
        $app = JFactory::getApplication();

        JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');        
        JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');         

        $label = '';
        $replace = '';
 
        // Get the label text from the XML element, defaulting to the element name.
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $jlistConfig['custom.field.7.title'];;
 
        // Build the class for the label.
        $class = !empty($this->description) ? 'hasTip' : '';
        $class = $this->required == true ? $class.' required' : $class;
        $req   = $this->required == true ? '<span class="star">&#160;*</span>' : '';
 
        // Add the opening label tag and main attributes attributes.
        $label .= '<label id="'.$this->id.'-lbl" for="'.$this->id.'" class="'.$class.'"';
 
        // If a description is specified, use it to build a tooltip.
        if (!empty($this->description)) {
                $label .= ' title="'.htmlspecialchars(trim(JText::_($text), ':').'::' .
                                JText::_($this->description), ENT_COMPAT, 'UTF-8').'"';
        }
 
        // Add the label text and closing tag.
        $label .= '>'.JText::_($text).$req.'</label>';
 
        return $label; 
    }    

    /**
     * Method to get the field input markup.
     *
     * @return    string    The field input markup.
     * @since    1.6
     */
	protected function getInput()
	{
		global $jlistConfig;
        
        $app = JFactory::getApplication();

        JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');        
        JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');        
		
        $field = JFormHelper::loadFieldType('Text');
        $field->setForm($this->form);

        if ($this->value != ''){
            $field->setup($this->element, $this->value);
        } else {
            $field->setup($this->element, htmlspecialchars(trim($jlistConfig['custom.field.7.values']), ENT_COMPAT, 'UTF-8'));
        }
        return $field->getInput();               
	}
}