<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 *
 * @component jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2011 - Arno Betz - www.jdownloads.com
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
 * jDownloads layoutinstall Controller
 *
 */
class jdownloadsControllerLayoutinstall extends jdownloadsController
{
    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
	/**
	 * logic to store the data from the layout file in the database
	 *
	 */
	public function install()
    {
        global $jlistConfig;
        
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Access check.
        if (!JFactory::getUser()->authorise('edit.config','com_jdownloads')){            
            JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
            $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false));
            
        } else {       
        
            jimport('joomla.filesystem.file');
            
            $app = JFactory::getApplication();
            $db = JFactory::getDBO();
            
            ini_set('max_execution_time', '300');
            ignore_user_abort(true);
            flush(); 
            
            $original_upload_dir = $jlistConfig['files.uploaddir'];

            // get layout file
            $file = JArrayHelper::getValue($_FILES,'install_file',array('tmp_name'=>''));
            
            // when file is not valid exit
            if (!$file['type'] == 'text/xml'){
                $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false),  JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_WRONG_FILE_ERROR'), 'error');
            }
            
            // save it in tempzipfile folder
            $upload_path = $jlistConfig['files.uploaddir'].'/'.$jlistConfig['tempzipfiles.folder.name'].'/'.$file['name'];
            
            // check whether a file with the same name already exist
            if (JFile::exists($upload_path)){
                $res = JFile::delete($upload_path);
            }
            
            // since Joomla 3.4 we need additional params to allow unsafe file (backup file contains php content)
            //if (!JFile::upload($file['tmp_name'], $upload_path, false, true)){
            // we need unfiltered data in this case           
            if (!move_uploaded_file ($file['tmp_name'], $upload_path)){
                $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false), JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_STORE_ERROR'), 'error');
            }
            
            $xml = simplexml_load_file($upload_path);
            if ($xml->template_typ){
                if ($xml->targetjdownloads){
                    // versions check
                    $current_version = JDownloadsHelper::getjDownloadsVersion();
                    $result = version_compare($xml->targetjdownloads, $current_version, '<=');
                    if (!$result){
                        // installed version is to old for this layout
                        $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false),  JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_WRONG_VERSION_ERROR'), 'error');
                    }
                }
                
                switch ($xml->template_typ) {
                    case 'categories':
                        $xml->template_typ = '1';
                        break; 
                    case 'category':
                        $xml->template_typ = '4';                    
                        break;
                    case 'files':
                        $xml->template_typ = '2';                                        
                        break;
                    case 'details':
                        $xml->template_typ = '5';                                        
                        break;
                    case 'summary':
                        $xml->template_typ = '3';                                        
                        break;
                    case 'search':
                        $xml->template_typ = '7';                                        
                        break;
                    default:
                        // wrong layout type
                        $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false), JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_WRONG_FILE_ERROR'), 'error');
                }

                // remove \r\n
                $xml->note                      = trim($db->escape($xml->note));
                $xml->template_header_text      = trim($db->escape($xml->template_header_text));
                $xml->template_subheader_text   = trim($db->escape($xml->template_subheader_text));
                $xml->template_footer_text      = trim($db->escape($xml->template_footer_text));
                $xml->template_before_text      = trim($db->escape($xml->template_before_text));
                $xml->template_text             = trim($db->escape($xml->template_text));
                $xml->template_after_text       = trim($db->escape($xml->template_after_text));

                if ($xml->author != ''){
                    $note = $xml->note."\r\n{".JText::_('COM_JDOWNLOADS_BACKEND_FILESLIST_AUTHOR').': '.$xml->author;
                    if ($xml->creation_date != ''){
                        $note .= ' - '.$xml->creation_date.'}';
                    } else {
                        $note .= '}';
                    }
                } else {
                    $note = $xml->note;
                }
                
                $db->setQuery("INSERT INTO #__jdownloads_templates (`id`, `template_name`, `template_typ`, `template_header_text`, `template_subheader_text`, `template_footer_text`, `template_before_text`, `template_text`, `template_after_text`, `template_active`, `locked`, `note`, `cols`, `checkbox_off`, `use_to_view_subcats`, `symbol_off`, `language`)
                      VALUES ( 'NULL', '$xml->template_name', '$xml->template_typ', '$xml->template_header_text', '$xml->template_subheader_text', '$xml->template_footer_text', '$xml->template_before_text', '$xml->template_text', '$xml->template_after_text', '$xml->template_active', '$xml->locked', '$note', '$xml->cols', '$xml->checkbox_off', '$xml->use_to_view_subcats', '$xml->symbol_off', '$xml->language')");
                $result = $db->execute();
                if (!$result){
                    // MySQL error
                    $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false),  JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_MYSQL_ERROR'), 'error');
                } 
            } else {
                // invalid file
                $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false),  JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_WRONG_FILE_ERROR'), 'error');
            }                        
        }
        $app->redirect(JRoute::_('index.php?option=com_jdownloads&view=layouts', false), JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_MSG_SUCCESSFUL') );
    }
}
?>