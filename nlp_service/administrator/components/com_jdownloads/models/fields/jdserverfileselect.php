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
class JFormFieldjdServerFileSelect extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'jdServerFileSelect';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
		
		// Initialise variables.
		$update_files_list = array();
        $update_files = array();
        $update_list_title = '';

        $jinput = JFactory::getApplication()->input;
        // new download clicked in manage files?
        if (($new_file_name =  $jinput->get('file', '', 'string') != '')) $new_file_from_list = true;
        
        // files list from upload root folder (for updates via ftp or create new from this list)
        $update_files = JFolder::files( $jlistConfig['files.uploaddir'], $filter= '.', $recurse=false, $fullpath=false, $exclude=array('index.htm', 'index.html', '.htaccess') );
        if ($update_files){
            $update_list_title = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_UPDATE_LIST_TITLE');
        } else {
            $update_list_title = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_NO_UPDATE_FILE_FOUND');
        }   
        $update_files_list[] = JHtml::_('select.option', '0', $update_list_title);
        foreach ($update_files as $file) {
            $update_files_list[] = JHtml::_('select.option', $file);
        }
        
        return $update_files_list;
	}
}