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
 * View to edit a template 
 * 
 * 
 **/

 class jdownloadsViewtemplate extends JViewLegacy
{
    protected $state;
    protected $item;
    protected $form;
    protected $id_type;
    protected $canDo;
    // protected $id_new;
    
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

        $session = JFactory::getSession();
        $type    = (int) $session->get( 'jd_tmpl_type', '' );
        $this->assignRef('jd_tmpl_type',        $type);
        
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
        
        // set the correct text in title for every layout type
        switch ($this->jd_tmpl_type) {
            case '1':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP1'); break;
            case '2':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP2'); break;
            case '3':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP3'); break;
            case '4':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP4'); break;
            case '5':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP5'); break;
            case '6':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP6'); break;
            case '7':  $layout_type = JText::_('COM_JDOWNLOADS_BACKEND_TEMP_TYP7'); break;
        }
        $title = ($isNew) ? JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_ADD').': '.$layout_type : JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_EDIT').': '.$layout_type; 
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.$title, 'jdlayouts'); 

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit')|| $canDo->get('core.create')))
        {
            JToolBarHelper::apply('template.apply');
            JToolBarHelper::save('template.save');
        }
        if (!$checkedOut && $canDo->get('core.create')){
            JToolBarHelper::save2new('template.save2new');
        }
        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolBarHelper::save2copy('template.save2copy');
        }
        if (empty($this->item->id)) {
            JToolBarHelper::cancel('template.cancel');
        }
        else {
            JToolBarHelper::cancel('template.cancel', 'JTOOLBAR_CLOSE');
        }
        JToolBarHelper::divider();
        JToolBarHelper::help('help.template', true);
    }    
   
}
?>