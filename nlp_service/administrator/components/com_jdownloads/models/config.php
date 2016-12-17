<?php
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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' ); 

class jdownloadsModelconfig extends JModelLegacy
{
	// config data variables
	var $_data = null;
	var $_total = null;

	// Constructor
	function __construct()
	{
		parent::__construct();
	}
	

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery() {

		$query = ' SELECT config.*'
			. ' FROM #__jdownloads_config AS config'
			. ' ORDER BY config.id';
		return $query;
	}	
	
	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}
		return $this->_data;
	}
  
	
	/**
	 * Method to store the config
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save($config_data)
	{	
		if (!empty($config_data)) {
            foreach($config_data as $setting_name => $setting_value){
                 $db = JFactory::getDbo();
                 $query = $db->getQuery(true);
                 $query->update($db->quoteName('#__jdownloads_config'));
                 $query->set('setting_value = \''.$db->escape($setting_value).'\'');
                 $query->where('setting_name = \''.$setting_name.'\'');
                 $db->setQuery($query);

                 try {
                     $result = $db->execute();
                 } catch (Exception $e) {
                          $this->setError($e->getMessage());
                          return false;
                 }            
		    }
		    return true;
	    }
    }
    
}
?>