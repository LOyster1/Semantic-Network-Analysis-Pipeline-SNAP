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
 * Files View
 *
 * @package    jDownloads
 */
class jdownloadsViewfiles extends JViewLegacy
{
	
    protected $items;
    protected $pagination;
    protected $state;
    protected $canDo;
    
    
    /**
     * list files display method
     * @return void
     **/
    function display($tpl = null)
    {
        $this->state = $this->get('State');
        
        // Get the files data from the model
        $items = $this->get('Items');

        // Assign data to the view
        $this->items = $items;
        $this->pagination   = $this->get('Pagination');
        $this->state = $this->get('state');
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();        
        parent::display($tpl);
    }    
    
    
     /**
     * Add the page title and toolbar.
     *
     * 
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        JDownloadsHelper::addSubmenu('files');  
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_FILES'), 'jdfiles');

        JToolBarHelper::custom( 'files.uploads', 'upload.png', 'upload.png', JText::_('COM_JDOWNLOADS_FILESLIST_TITLE_FILES_UPLOAD'), false );
        JToolBarHelper::custom( 'files.downloads', 'folder.png', 'folder.png', JText::_('COM_JDOWNLOADS_DOWNLOADS'), false );
                    
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList(JText::_('COM_JDOWNLOADS_DELETE_LIST_ITEM_CONFIRMATION'), 'files.delete', 'COM_JDOWNLOADS_TOOLBAR_REMOVE');
            JToolBarHelper::divider();
        } 

        JToolBarHelper::divider();
        JToolBarHelper::help('help.files', true);
	}
}
?>