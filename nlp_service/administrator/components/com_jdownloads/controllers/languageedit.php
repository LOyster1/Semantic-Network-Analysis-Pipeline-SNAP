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
 * jDownloads languageedit Controller
 *
 */
class jdownloadsControllerlanguageedit extends jdownloadsController
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
     * logic to save the language file
     *
     */    
    public function save()
    {
       // Check for request forgeries.
       JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));                
       $app = JFactory::getApplication();
       // since Joomla 2.5
       $jinput = JFactory::getApplication()->input;
       
       $file = $jinput->get('languagepath', '', 'STRING');
       // this get not the pure text with all html tags
       //$text = $jinput->get('cssfile', '', '');
       // also this get not the pure text with all html tags 
       //$text = JRequest::getVar('cssfile', '', 'post', 'STRING', JREQUEST_ALLOWHTML);
       // so we need this alternate (old way but works)
       $text = JArrayHelper::getValue($_POST,'cssfile', '', _MOS_ALLOWHTML);
       clearstatcache();

       if (!is_writable($file))
       {
            // when not possible to write - cancel action
            $this->setRedirect("index.php?option=com_jdownloads&view=layouts", JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_WRITE_STATUS_TEXT').JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_NO') );
            break;
       }

       if ($fp = fopen( $file, "w")) 
       {
            // write the changed text to the file
            fputs($fp,stripslashes($text));
            fclose($fp);
            $this->setRedirect("index.php?option=com_jdownloads&view=layouts", JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_SAVED'));
       }        
    }   
}
?>