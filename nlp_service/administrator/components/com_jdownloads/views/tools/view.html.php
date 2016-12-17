<?php


// Check to ensure this config is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Tools view
 *
 */
class jdownloadsViewtools extends JViewLegacy
{
    protected $canDo;
    
    /**
	 * tools display method
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
        
        JDownloadsHelper::addSubmenu('tools');  
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_TOOLS'), 'jdtools');
        
        JToolBarHelper::divider();
        JToolBarHelper::help('help.tools', true);
    }       
}
?>