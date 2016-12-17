<?php
/**
 * @copyright    Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * @package jDownloads
 * @version 2.5  
 * @copyright (C) 2007 - 2013 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */


defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @since		1.6
 */
class JFormFieldjdCustomField1 extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'jdCustomField1';

	
    // we need the label text from the config table
    public function getLabel() {
        global $jlistConfig;

        $app = JFactory::getApplication();

        JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');        
        JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');        
        
        $label = '';
        $replace = '';
 
        // Get the label text from the XML element, defaulting to the element name.
        if ($app->isAdmin()){
            $text = $this->element['label'] ? (string) $this->element['label'] : (string) JDownloadsHelper::getOnlyLanguageSubstring($jlistConfig['custom.field.1.title']);
        } else {
            $text = $this->element['label'] ? (string) $this->element['label'] : (string) JDHelper::getOnlyLanguageSubstring($jlistConfig['custom.field.1.title']);
        }    
 
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
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		global $jlistConfig;
		
        $app = JFactory::getApplication();

        JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');        
        JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');        
        
        $custom_field_list = array();
        $values             = array();
        $x                  = 0;
        
        if ($jlistConfig['custom.field.1.title'] != ''){
            if ($app->isAdmin()){
                $values = explode(',', JDownloadsHelper::getOnlyLanguageSubstring($jlistConfig['custom.field.1.values']));
            } else {
                $values = explode(',', JDHelper::getOnlyLanguageSubstring($jlistConfig['custom.field.1.values']));
            }    
        }
        
        $custom_field_list[] = JHtml::_('select.option', $x,  JText::_('COM_JDOWNLOADS_SELECT'));

        foreach ($values as $value) {
            $x++;
            $custom_field_list[] = JHtml::_('select.option', $x, $value );
        }
        return $custom_field_list;
	}
}