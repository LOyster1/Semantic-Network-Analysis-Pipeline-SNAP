<?php
/**
 * @package jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2012 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
 
/**
 * jDownloads (group) Table class
 */
class jdownloadsTablegroup extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__jdownloads_usergroups_limits', 'id', $db);
	}
    
    
    public function check()
    {
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
        
        // check for correct settings
        if ($this->use_private_area == 1) {
            if (!JFolder::exists($jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name'])){
                $this->use_private_area == 0; 
                $this->setError(JText::_('COM_JDOWNLOADS_USERGROUPS_PRIVATE_FILES_AREA_ERROR'));
                return false;
            } else {
                if (!is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name'])){
                    $this->use_private_area == 0;
                    $this->setError(JText::_('COM_JDOWNLOADS_USERGROUPS_PRIVATE_FILES_AREA_ERROR'));
                    return false;                    
                }
           }
        }        
        return true;
    }    

}
?>