 <?php

// Check to ensure this category is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * View to edit a license 
 * 
 * 
 **/

 class jdownloadsViewlicense extends JViewLegacy
{
    protected $state;
    protected $item;
    protected $form;
    protected $canDo;
    
    /**
     * Display the view
     * 
     * 
     */
    public function display($tpl = null)
    {
        $this->state       = $this->get('State');
        $this->item        = $this->get('Item');
        $this->form        = $this->get('Form');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        
        $this->addToolbar();
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
        
        JRequest::setVar('hidemainmenu', true);

        $user        = JFactory::getUser();
        $isNew       = ($this->item->id == 0);
        $checkedOut  = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        $canDo       = JDownloadsHelper::getActions();
        
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        $title = ($isNew) ? JText::_('COM_JDOWNLOADS_LICEDIT_ADD') : JText::_('COM_JDOWNLOADS_LICEDIT_EDIT'); 
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.$title, 'jdlicenses'); 

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit')|| $canDo->get('core.create')))
        {
            JToolBarHelper::apply('license.apply');
            JToolBarHelper::save('license.save');
        }
        if (!$checkedOut && $canDo->get('core.create')){
            JToolBarHelper::save2new('license.save2new');
        }
        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolBarHelper::save2copy('license.save2copy');
        }
        if (empty($this->item->id)) {
            JToolBarHelper::cancel('license.cancel');
        }
        else {
            JToolBarHelper::cancel('license.cancel', 'JTOOLBAR_CLOSE');
        }
        JToolBarHelper::divider();
        JToolBarHelper::help('help.license', true);
    }    
   
}
?>