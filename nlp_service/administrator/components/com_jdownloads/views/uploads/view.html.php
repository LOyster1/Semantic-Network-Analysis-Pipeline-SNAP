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

jimport( 'joomla.application.component.view' );

/**
 * upload manager View
 *
 */
class jdownloadsViewuploads extends JViewLegacy
{
    protected $canDo;
    
    /**
	 * uploads display method
	 * @return void
	 **/
	function display($tpl = null)
	{
        global $jlistConfig; 
        
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        
        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = jdownloadsHelper::getActions();
        
        $language = JFactory::getLanguage();
        $lang = $language->getTag();
        
        $langfiles        = JPATH_COMPONENT_ADMINISTRATOR.'/assets/plupload/js/i18n/';        
        $PLdataDir        = JURI::root() . "administrator/components/com_jdownloads/assets/plupload/";
        $document         = JFactory::getDocument();
        $PLuploadScript   = new PLuploadScript($PLdataDir);
        $runtimeScript    = $PLuploadScript->runtimeScript;
        $runtime          = $PLuploadScript->runtime;
                
        //add default PL css
        $document->addStyleSheet($PLdataDir . 'css/plupload.css');
        
        //add PL styles and scripts
        $document->addStyleSheet($PLdataDir . 'js/jquery.plupload.queue/css/jquery.plupload.queue.css', 'text/css', 'screen');
        $document->addScript($PLdataDir . 'js/jquery.min.js');
		$document->addScript($PLdataDir . 'js/plupload.full.min.js');
		
        // load plupload language file
        if ($lang){
            if (JFile::exists($langfiles . $lang.'.js')){
                $document->addScript($PLdataDir . 'js/i18n/'.$lang.'.js');      
            } else {
                $document->addScript($PLdataDir . 'js/i18n/en-GB.js');      
            }
        } 
        $document->addScript($PLdataDir . 'js/jquery.plupload.queue/jquery.plupload.queue.js');
        $document->addScriptDeclaration( $PLuploadScript->getScript() );
        
        //set variables for the template
        $this->enableLog = $jlistConfig['plupload.enable.uploader.log'];
        $this->runtime = $runtime;
        $this->currentDir = $jlistConfig['files.uploaddir'].'/';
                
        //set toolbar
        $this->addToolBar();
        $this->sidebar = JHtmlSidebar::render();        
        // Display the template
        parent::display($tpl);
    }
    
    /**
     * Setting the toolbar
     */
    protected function addToolBar() 
    {
        
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        JDownloadsHelper::addSubmenu('files');  
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_FILESLIST_TITLE_FILES_UPLOAD'), 'jdupload');
        
        JToolBarHelper::custom( 'uploads.files', 'upload.png', 'upload.png', JText::_('COM_JDOWNLOADS_FILES'), false, true );
        JToolBarHelper::custom( 'uploads.downloads', 'folder.png', 'folder.png', JText::_('COM_JDOWNLOADS_DOWNLOADS'), false, false );
        
        JToolBarHelper::divider();
        JToolBarHelper::help('help.uploads', true);        
    }
}
?>