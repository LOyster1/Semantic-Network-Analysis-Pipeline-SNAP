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
 
defined('_JEXEC') or die('Restricted access');


/**
 * HTML Downloads View class to send a report e-mail
 */
class jdownloadsViewReport extends JViewLegacy
{

	function display($tpl = null)
	{
        global $jlistConfig;
        
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
        
        // get jD User group settings and limitations
        $this->user_rules = JDHelper::getUserRules();

		// $dispatcher	= JDispatcher::getInstance();

        // Get data from the model
        $this->state  = $this->get('State');
        $this->item   = $this->get('Item');
        $this->form   = $this->get('Form');
        
        if ($this->item) {
            if (!$user->guest){
                $this->form->setFieldAttribute( 'name', 'default', htmlspecialchars($user->name, ENT_COMPAT, 'UTF-8'));
                $this->form->setFieldAttribute( 'name', 'readonly', 'true');
                $this->form->setFieldAttribute( 'name', 'class', 'readonly');
                $this->form->setFieldAttribute( 'email', 'default', htmlspecialchars($user->email, ENT_COMPAT, 'UTF-8'));
                $this->form->setFieldAttribute( 'email', 'readonly', 'true');
                $this->form->setFieldAttribute( 'email', 'class', 'readonly');
            }
        } else {
            JError::raiseWarning(100, JText::_('COM_JDOWNLOADS_DOWNLOAD_NOT_FOUND'));
            return false;
        }

        // Get the category title
        if ($this->item->cat_id == 1){
            $this->item->category_title = JText::_('COM_JDOWNLOADS_SELECT_UNCATEGORISED');
        } else {
            $cat = JDHelper::getSingleCategory($this->item->cat_id);
            $this->item->category_title = $cat->title;
        }
        // do it in the form
        $this->form->setFieldAttribute( 'cat_title', 'default', htmlspecialchars($this->item->category_title, ENT_COMPAT, 'UTF-8'));
        
        $this->state	= $this->get('State');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
        
        // add all needed cripts and css files
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'components/com_jdownloads/assets/js/jdownloads.js');
		
        $document->addScriptDeclaration('var live_site = "'.JURI::base().'";');
        $document->addScriptDeclaration('function openWindow (url) {
                fenster = window.open(url, "_blank", "width=550, height=480, STATUS=YES, DIRECTORIES=NO, MENUBAR=NO, SCROLLBARS=YES, RESIZABLE=NO");
                fenster.focus();
                }');

        $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_fe.css", "text/css", null, array() );
        
         if ($jlistConfig['view.ratings']){
             $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/rating/css/ajaxvote.css", "text/css", null, array() );         
         }       

        
        $custom_css_path = JPATH_ROOT.'/components/com_jdownloads/assets/css/jdownloads_custom.css';
        if (JFile::exists($custom_css_path)){
            $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_custom.css", 'text/css', null, array() );                
        }           
        
		// Check the report view access.
		if (!$this->user_rules->view_report_form) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		global $jlistConfig;
        
        $app	= JFactory::getApplication();
		$title = null;

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

        if ($jlistConfig['robots']){
            // use settings from jD-config
            $this->document->setMetadata('robots', $jlistConfig['robots']);    
        } else {
            // is not defined in item or jd-config - so we use the global config setting
            $this->document->setMetadata( 'robots' , $app->getCfg('robots' ));
        }
    }
}
