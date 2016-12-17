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
 * Config View
 *
 */
class jdownloadsViewconfig extends JViewLegacy
{
	/**
	 * config view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		//initialise variables
		$user 		= JFactory::getUser();
        $document	= JFactory::getDocument();
		$db  		= JFactory::getDBO();
        $canDo      = JDownloadsHelper::getActions();
        
		if ($canDo->get('edit.config')){
            // Get data from the model
		    $items		= $this->get( 'Data');
		    $config_select_fields = JDownloadsHelper::getConfigSelectFields();
            
            $this->assignRef('items',	$items);   	
            $this->assignRef('select_fields', $config_select_fields);
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

        $state    = $this->get('State');
        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('../components/com_jdownloads/assets/css/jdownloads_buttons.css');
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        $document->addScriptDeclaration('function openWindow (url) {
        fenster = window.open(url, "_blank", "width=550, height=480, STATUS=YES, DIRECTORIES=NO, MENUBAR=NO, SCROLLBARS=YES, RESIZABLE=NO");
        fenster.focus();
        }');
        $document->addScriptDeclaration('function getSelectedText( frmName, srcListName ) {
                                         var form = eval( \'document.\' + frmName );
                                         var srcList = eval( \'form.\' + srcListName );
                                         i = srcList.selectedIndex;
                                         if (i != null && i > -1) {
                                            return srcList.options[i].text;
                                         } else {
                                            return null;
                                         }
                                         }');
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_BACKEND_CPANEL_SETTINGS'), 'jdconfig');
        
        if ($canDo->get('edit.config')) {
            JToolBarHelper::apply('config.apply');
            JToolBarHelper::save('config.save');
            JToolBarHelper::cancel('config.cancel');
            JToolBarHelper::divider();
        }
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_jdownloads');
            JToolBarHelper::divider();
        }   
        if ($canDo->get('edit.config')) {
            JToolBarHelper::help('help.config', true);
        }
        
    }       
}
?>