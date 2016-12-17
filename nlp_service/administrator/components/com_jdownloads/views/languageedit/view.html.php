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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Edit Language View
 *
 */
class jdownloadsViewlanguageedit extends JViewLegacy
{
	
    protected $canDo;    
    
    /**
	 * languageedit display method
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->setFile();
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();        
        parent::display($tpl);
    }
    
     /**
     * 
     *
     * 
     */
     protected function setFile()
     {
        $params   = JComponentHelper::getParams('com_languages');
        $frontend_lang = $params->get('site', 'en-GB');
        $language = JLanguage::getInstance($frontend_lang);    
        
        // get language file for edit 
        $language = JFactory::getLanguage();
        $language->load('com_jdownloads');
        $lang_file = JLanguage::getLanguagePath(JPATH_SITE);
        
        $lang_file .= DS.$frontend_lang.DS.$frontend_lang.'.com_jdownloads.ini';        
         
        @chmod ($lang_file, 0755);
        clearstatcache();

        if ( is_writable( $lang_file ) == false ) {
          $language_writable = false;
        } else {
          $language_writable = true;
        }         
        
        if ($language_writable){
            $f=fopen($lang_file,"r");
            $language_text = fread($f, filesize($lang_file));
            $this->languagetext = htmlspecialchars($language_text);
        } else {
            $this->languagetext = '';
        }

        $this->languagefile = $lang_file;
        $this->languagefile_writable = $language_writable;         
     }

    /**
     * Add the page title and toolbar.
     *
     * 
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';
        
        JRequest::setVar('hidemainmenu', true);

        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        //JDownloadsHelper::addSubmenu('templates');  
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_TITLE_EDIT'), 'jdlogo');
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::save('languageedit.save');
            JToolBarHelper::cancel('languageedit.cancel');
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_jdownloads');
            JToolBarHelper::divider();
        }
        JToolBarHelper::help('help.languageedit', true);
    }       
}
?>