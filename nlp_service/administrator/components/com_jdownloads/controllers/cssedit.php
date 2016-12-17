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


defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * jDownloads cssedit Controller
 *
 */
class jdownloadsControllercssedit extends jdownloadsController
{
	/**
	 * Constructor
	 *
	 */
	    public function __construct($config = array())
    {
        parent::__construct($config);
        
        $jinput = JFactory::getApplication()->input;
        
        // Access check.
        if (!JFactory::getUser()->authorise('core.admin', $jinput->get('jdownloads'))) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
	}

	/**
	 * logic to cancel the edit page
	 *
	 */
	public function cancel()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));                
        $app = JFactory::getApplication();
        $this->setRedirect('index.php?option=com_jdownloads&view=layouts');
    }
    
    /**
     * logic to save the css file
     *
     */
    public function save()
    {
       // Check for request forgeries.
       JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));                
       $app = JFactory::getApplication();
       $css_file = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_fe.css';
       $css_text = JArrayHelper::getValue($_POST,'cssfile', '');
       $css_file2 = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_buttons.css';
       $css_text2 = JArrayHelper::getValue($_POST,'cssfile2', '');
       $css_file3 = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_custom.css';
       $css_text3 = JArrayHelper::getValue($_POST,'cssfile3', '');
       clearstatcache();

       if (!is_writable($css_file) || !is_writable($css_file2) || !is_writable($css_file3)) {
            $this->setRedirect("index.php?option=com_jdownloads&view=layouts", JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_WRITE_STATUS_TEXT').JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_NO') );
        break;
      }

      if ($fp = fopen( $css_file, "w")) {
        fputs($fp,stripslashes($css_text));
        fclose($fp);
      }        

      if ($fp = fopen( $css_file2, "w")) {
        fputs($fp,stripslashes($css_text2));
        fclose($fp);
      }        

      if ($fp = fopen( $css_file3, "w")) {
        fputs($fp,stripslashes($css_text3));
        fclose($fp);
            $this->setRedirect("index.php?option=com_jdownloads&view=layouts", JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_SAVED'));
      }        
      
        
    }
	
}
?>