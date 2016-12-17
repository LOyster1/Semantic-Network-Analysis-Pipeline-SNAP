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
class JFormFieldjdSystemSelect extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'jdSystemSelect';

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
        
        if ($app->isAdmin()){
            JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');        
        } else {
            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');        
        }         
		
		// Initialise variables.
		$options = array();
        $file_sys_values = '';

        // build system listbox
        if ($app->isAdmin()){        
            $file_sys_values = explode(',' , JDownloadsHelper::getOnlyLanguageSubstring($jlistConfig['system.list']));
        } else {
            $file_sys_values = explode(',' , JDHelper::getOnlyLanguageSubstring($jlistConfig['system.list']));
        }    
        for ($i=0; $i < count($file_sys_values); $i++) {
            $options[] = JHtml::_('select.option',  $i, $file_sys_values[$i] );
        }
        
        return $options;
	}
}