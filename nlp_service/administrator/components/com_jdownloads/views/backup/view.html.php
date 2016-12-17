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
 * Category View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class jdownloadsViewbackup extends JViewLegacy
{
    protected $canDo;
    
    /**
     * restore display method
     * @return void
     **/
    function display($tpl = null)
    {
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        JDownloadsHelper::addSubmenu('backup');  
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_BACKUP'), 'jdbackup');
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::custom( 'backup.runbackup', 'new', 'new', JText::_('COM_JDOWNLOADS_BACKUP_CREATE_LINK_LABEL'), false, false ); 
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_jdownloads');
            JToolBarHelper::divider();
        }
        JToolBarHelper::help('help.backup', true);
    }   	
}
