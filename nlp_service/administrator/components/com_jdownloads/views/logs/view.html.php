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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class jdownloadsViewlogs extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $canDo;
    
    /**
	 * logs view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->logs_header_info     = JDownloadsHelper::getLogsHeaderInfo();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

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

        $state    = $this->get('State');
        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        JDownloadsHelper::addSubmenu('logs');
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_LOGS_TITLE_HEAD'), 'jdlogs');
        
        if ($canDo->get('core.edit')) {
            JToolBarHelper::custom( 'logs.blockip', 'cancel.png', 'cancel.png', JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_BLOCK_IP'), true ); 
        }    
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList(JText::_('COM_JDOWNLOADS_DELETE_LIST_ITEM_CONFIRMATION'), 'logs.delete', 'COM_JDOWNLOADS_TOOLBAR_REMOVE');
            JToolBarHelper::divider();
        } 

        JToolBarHelper::divider();
        JToolBarHelper::help('help.logs', true);
    }    
}