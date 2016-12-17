<?php


defined('_JEXEC') or die;

/**
 * View class for a list of user groups.
 *
 */
class jdownloadsViewGroups extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

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
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        JDownloadsHelper::addSubmenu('groups');		
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_USER_GROUPS'), 'jdgroups');

        $canDo    = jdownloadsHelper::getActions();
        
        if ($canDo->get('edit.config')) {
			JToolBarHelper::editList('group.edit', 'COM_JDOWNLOADS_USERGROUPS_CHANGE_LIMITS_TITLE');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
            JToolBarHelper::custom( 'groups.resetLimits', 'refresh.png', 'refresh.png', JText::_('COM_JDOWNLOADS_USERGROUPS_RESET_LIMITS_TITLE'), true, false );
			JToolBarHelper::divider();
		}
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_jdownloads');
            JToolBarHelper::divider();
        }         
        
        if ($canDo->get('edit.config')) {        
		    JToolBarHelper::help('help.groups', true);
        }    
	}
}
