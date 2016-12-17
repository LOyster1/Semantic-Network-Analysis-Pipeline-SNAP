<?php

defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class jdownloadsViewlicenses extends JViewLegacy
{
	
    protected $items;
    protected $pagination;
    protected $state;
    protected $canDo;
    
    /**
	 * licenses view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('Pagination');

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
        
        JDownloadsHelper::addSubmenu('licenses');
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_LICENSES'), 'jdlicenses');
        
        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('license.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('license.edit');
            JToolBarHelper::divider();
            JToolBarHelper::checkin('licenses.checkin');
        }    
        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::divider();
            JToolBarHelper::publish('licenses.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('licenses.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            
        } 
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList(JText::_('COM_JDOWNLOADS_DELETE_LIST_ITEM_CONFIRMATION'), 'licenses.delete', 'COM_JDOWNLOADS_TOOLBAR_REMOVE');
        } 

        JToolBarHelper::divider();
        JToolBarHelper::help('help.licenses', true);
    }    
}