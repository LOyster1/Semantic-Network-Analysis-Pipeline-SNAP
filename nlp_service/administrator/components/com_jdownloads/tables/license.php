<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * License Table class
 */
class jdownloadsTablelicense extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__jdownloads_licenses', 'id', $db);
	}
    
    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return    boolean    True on success.
     */
    public function check()
    {

        // check for valid name
        if (trim($this->title) == '') {
            $this->setError(JText::_('COM_WEBLINKS_ERR_TABLES_TITLE'));
            return false;
        }

        // check for http, https, ftp on webpage
        if ((stripos($this->url, 'http://') === false)
            && (stripos($this->url, 'https://') === false)
            && (stripos($this->url, 'ftp://') === false)
            && $this->url != '')
        {
            $this->url = 'http://'.$this->url;
        }

        if (empty($this->alias)) {
            $this->alias = $this->title;
        }
        $this->alias = JApplication::stringURLSafe($this->alias);
        if (trim(str_replace('-','',$this->alias)) == '') {
            $this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
        }

        return true;
    }
    
}
?>