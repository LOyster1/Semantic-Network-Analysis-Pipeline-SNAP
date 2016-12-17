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

jimport('joomla.application.component.view');

/**
 * View class for a list of downloads
 */
class jdownloadsViewDownloads extends JViewLegacy
{
    protected $state = null;
    protected $item = null;
    protected $items = null;
    protected $pagination = null;

	/**
	 * Display the view
     * @return    mixed    False on error, null otherwise.
	 */
	public function display($tpl = null)
	{
        
        global $jlistConfig;
        
        $app    = JFactory::getApplication();
        $user    = JFactory::getUser();
        
        $jd_user_settings = JDHelper::getUserRules();

        // Initialise variables
        $state        = $this->get('State');
        $items        = $this->get('Items');
        $pagination   = $this->get('Pagination');
        
        // upload icon handling
        $this->view_upload_button = false;
        
        if ($jd_user_settings->uploads_view_upload_icon){
            // we must here check whether the user has the permissions to create new downloads 
            // this can be defined in the components permissions but also in any category
            // but the upload icon is only viewed when in the user groups settings is also activated the: 'display add/upload icon' option
                            
            // 1. check the component permissions
            if (!$user->authorise('core.create', 'com_jdownloads')){
                // 2. not global permissions so we must check now every category (for a lot of categories can this be very slow)
                $this->authorised_cats = JDHelper::getAuthorisedJDCategories('core.create', $user);
                if (count($this->authorised_cats) > 0){
                    $this->view_upload_button = true;
                }
            } else {
                $this->view_upload_button = true;
            }        
        }

        $this->ipad_user = false;
                
        // check whether we have an ipad/iphone user for flowplayer aso...
        if ((bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') || (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')){        
            $this->ipad_user = true;
        }        

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        if ($items === false) {
            return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));

        }

        // add all needed cripts and css files
        $document = JFactory::getDocument();

        $document->addScript(JURI::base().'components/com_jdownloads/assets/js/jdownloads.js');
        
        if ($jlistConfig['view.ratings']){
            $document->addScript(JURI::base().'components/com_jdownloads/assets/rating/js/ajaxvote.js');
        }
        
        // loadscript for flowplayer
        if ($jlistConfig['flowplayer.use']){
            $document->addScript(JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer-3.2.12.min.js');
            // load also the ipad plugin when required
             if ($this->ipad_user){
                $document->addScript(JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer.ipad-3.2.12.min.js');
            }
        }       
        
        if ($jlistConfig['use.lightbox.function']){
            JHtml::_('bootstrap.framework');            
            $document->addScript(JURI::base().'components/com_jdownloads/assets/lightbox/lightbox.js');
            $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/lightbox/lightbox.css", 'text/css', null, array() );
        }
            
        $document->addScriptDeclaration('var live_site = "'.JURI::base().'";');
        $document->addScriptDeclaration('function openWindow (url) {
                fenster = window.open(url, "_blank", "width=550, height=480, STATUS=YES, DIRECTORIES=NO, MENUBAR=NO, SCROLLBARS=YES, RESIZABLE=NO");
                fenster.focus();
                }');

        if ($jlistConfig['use.css.buttons.instead.icons']){
           $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_buttons.css", "text/css", null, array() ); 
        }
        
        $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_fe.css", "text/css", null, array() );
        
        if ($jlistConfig['view.ratings']){
            $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/rating/css/ajaxvote.css", "text/css", null, array() );         
        }

        $custom_css_path = JPATH_ROOT.'/components/com_jdownloads/assets/css/jdownloads_custom.css';
        if (JFile::exists($custom_css_path)){
            $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_custom.css", 'text/css', null, array() );                
        }   
        
        $this->jd_image_path = JPATH_ROOT  . '/images/jdownloads';        
        
        $params = &$state->params;
                
        // Compute the download slugs and prepare text (runs content plugins).
        for ($i = 0, $n = count($items); $i < $n; $i++)
        {
            $item = &$items[$i];
            
            $item->tags = new JHelperTags;
            $item->tags->getItemTags('com_jdownloads.download', $item->file_id);            
            
            $item->slug = $item->file_alias ? ($item->file_id . ':' . $item->file_alias) : $item->file_id;

            // No link for ROOT category
            if ($item->parent_alias == 'root') {
                $item->parent_slug = null;
            }

            // required for some content plugins
            $item->text = $item->description;
            $item->id = $item->file_id;
             
            $dispatcher = JDispatcher::getInstance();

            JPluginHelper::importPlugin('content');
            $dispatcher->trigger('onContentPrepare', array ('com_jdownloads.downloads', &$item, &$this->params, 0));

            $item->event = new stdClass();
                        
            $results = $dispatcher->trigger('onContentAfterTitle', array('com_jdownloads.downloads', &$item, &$item->params, 0));
            $item->event->afterDisplayTitle = trim(implode("\n", $results));

            $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_jdownloads.downloads', &$item, &$item->params, 0));
            $item->event->beforeDisplayContent = trim(implode("\n", $results));

            $results = $dispatcher->trigger('onContentAfterDisplay', array('com_jdownloads.downloads', &$item, &$item->params, 0));
            $item->event->afterDisplayContent = trim(implode("\n", $results));
            
            // we use a little trick to get always the changes from content plugins 
            if ($item->text != $item->description){
                $item->description = $item->text; 
            }
        }        
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

        $this->maxLevelcat = $params->get('maxLevelcat', -1);
        $this->assignRef('state', $state);        
        $this->assignRef('params',        $params);
        $this->assignRef('items',        $items);
        $this->assignRef('pagination', $pagination);

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus    = $app->getMenu();
        $title    = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_JDOWNLOADS_DOWNLOADS'));
        }        
        
        $title = $this->params->get('page_title', '');
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

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
