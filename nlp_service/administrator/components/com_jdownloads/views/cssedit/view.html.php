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
 * Edit CSS File View
 *
 */
class jdownloadsViewcssedit extends JViewLegacy
{
    protected $canDo;

	/**
	 * cssedit display method
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
     protected function setFile(){
        $css_file = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_fe.css';
        @chmod ($css_file, 0755);

        $css_file2 = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_buttons.css';
        @chmod ($css_file2, 0755);        
        
        $css_file3 = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_custom.css';
        @chmod ($css_file3, 0755);        
        
        clearstatcache();
        

        if ( is_writable( $css_file ) == false ) {
          $css_writable = false;
        } else {
          $css_writable = true;
        }         

        if ( is_writable( $css_file2 ) == false ) {
          $css_writable2 = false;
        } else {
          $css_writable2 = true;
        }        

        if ( is_writable( $css_file3 ) == false ) {
          $css_writable3 = false;
        } else {
          $css_writable3 = true;
        }        
        
        if ($css_writable){
            $f=fopen($css_file,"r");
            $css_text = fread($f, filesize($css_file));
            $this->csstext = htmlspecialchars($css_text);
        } else {
            $this->csstext = '';
        }
        $this->cssfile = $css_file;
        $this->cssfile_writable = $css_writable;         
        
        
        if ($css_writable2){
            $f=fopen($css_file2,"r");
            $css_text2 = fread($f, filesize($css_file2));
            $this->csstext2 = htmlspecialchars($css_text2);
        } else {
            $this->csstext2 = '';
        }
        $this->cssfile2 = $css_file2;
        $this->cssfile_writable2 = $css_writable2;        
        
        if ($css_writable3){
            $f=fopen($css_file3,"r");
            $css_text3 = fread($f, filesize($css_file3));
            $this->csstext3 = htmlspecialchars($css_text3);
        } else {
            $this->csstext3 = '';
        }
        $this->cssfile3 = $css_file3;
        $this->cssfile_writable3 = $css_writable3;        
        
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
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_TITLE_EDIT'), 'jdlogo');
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::save('cssedit.save');
            JToolBarHelper::cancel('cssedit.cancel');
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_jdownloads');
            JToolBarHelper::divider();
        }
        JToolBarHelper::help('help.cssedit', true);
    }       
}
?>