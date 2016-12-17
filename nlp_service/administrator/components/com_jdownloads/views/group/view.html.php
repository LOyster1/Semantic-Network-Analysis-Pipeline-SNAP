<?php

defined('_JEXEC') or die;

/**
 * View to edit jDownloads limits from a Joomla user group.
 *
 */
class jdownloadsViewGroup extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        $this->form->title = jdownloadshelper::getUserGroupInfos($this->item->group_id);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));        
		$canDo		= jdownloadsHelper::getActions();
        
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');

		JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_USERGROUP_EDIT_TITLE'), 'jdgroups');

		if ($canDo->get('edit.user.limits')) {
			JToolBarHelper::apply('group.apply');
			JToolBarHelper::save('group.save');
		}

        JToolBarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');

		JToolBarHelper::divider();
        JToolBarHelper::help('help.group', true);
	}
}
