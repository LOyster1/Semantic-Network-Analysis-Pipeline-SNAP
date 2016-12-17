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
 * HTML Downloads View class for the jDownloads component
 */
class jdownloadsViewDownload extends JViewLegacy
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;
    protected $user_rules;

	function display($tpl = null)
	{
        global $jlistConfig;
        
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
        
        $jd_user_settings = JDHelper::getUserRules();        
        
        // get jD User group settings and limitations
        $this->user_rules = JDHelper::getUserRules();

		$dispatcher	= JDispatcher::getInstance();

		$this->item		 = $this->get('Item');
		$this->state	 = $this->get('State');
		$this->user		 = $user;
        
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
        
        // Create a shortcut for $item.
		$item = $this->item;
        $item->tagLayout = new JLayoutFile('joomla.content.tags');

		// Add router helpers.
		$item->slug			= $item->file_alias ? ($item->file_id.':'.$item->file_alias) : $item->file_id;
		$item->catslug		= $item->category_alias ? ($item->cat_id.':'.$item->category_alias) : $item->cat_id;
		$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;

		// TODO: Change based on shownoauth
		//$item->readmore_link = JRoute::_(JdownloadsHelperRoute::getDownloadRoute($item->slug, $item->catslug));

		// Merge download params. If this is single-download view, menu params override download params
		// Otherwise, download params override menu item params
		$this->params	= $this->state->get('params');
		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);

		// Check to see which parameters should take priority
		if ($active) {
			$currentLink = $active->link;
			// If the current view is the active item and an download view for this download, then the menu item params take priority
			if (strpos($currentLink, 'view=download') && (strpos($currentLink, '&id='.(string) $item->file_id))) {
				// $item->params are the download params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout'])) {
					$this->setLayout($active->query['layout']);
				}
			}
			else {
				// Current view is not a single download, so the download params take priority here
				// Merge the menu item params with the download params so that the download params take priority
				$temp->merge($item->params);
				$item->params = $temp;
			}
		}
		else {
			// Merge so that download params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single-download menu item)
			// Single-download menu item layout takes priority over alt layout for an download
			if ($layout = $item->params->get('download_layout')) {
				$this->setLayout($layout);
			}
		}

		$offset = $this->state->get('list.offset');

		// Check the view access to the download (the model has already computed the values).
		if ($item->params->get('access-view') != true && ($item->params->get('show_noauth') != true &&  $user->get('guest') ) ) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
		}

        $item->tags = new JHelperTags;
        $item->tags->getItemTags('com_jdownloads.download', $this->item->file_id);

        // required for some content plugins
        if ($item->description_long != '')
        {
            $item->text = $item->description_long;
            $long_used = true;
        } else {
            $item->text = $item->description;
            $long_used = false;
        }
        $item->id = $item->file_id;
        
		// Process the content plugins.
		JPluginHelper::importPlugin('content');

        // this is the event to modify the download data.
		if ($jlistConfig['activate.general.plugin.support'] && !$jlistConfig['use.general.plugin.support.only.for.descriptions']) {
            $dispatcher->trigger('onContentPrepare', array ('com_jdownloads.download', &$item, &$this->params, $offset)); 
        }

		$item->event = new stdClass();
		
		// This is a request for information that should be placed immediately before the generated content.
        $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_jdownloads.download', &$item, &$this->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		// This is a request for information that should be placed immediately after the generated content.
        $results = $dispatcher->trigger('onContentAfterDisplay', array('com_jdownloads.download', &$item, &$this->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		// we use a little trick to get always the changes from content plugins 
        if ($long_used){
            if ($item->text != $item->description_long){
                $item->description_long = $item->text; 
            }
        } else {
            if ($item->text != $item->description){
                $item->description = $item->text; 
            }            
        }    
        
        // Increment the views counter of the download
        $model = $this->getModel();
		$model->view();

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

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
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_JDOWNLOADS_DOWNLOADS'));
		}

		$title = $this->params->get('page_title', '');

        if (isset($menu->query['catid'])){
		    $id = (int) @$menu->query['catid'];
        } else {
            $id = 0;
        }    

        // if the menu item does not concern this download
        if ($menu && ($menu->query['option'] != 'com_jdownloads' || $menu->query['view'] != 'download' || $id != $this->item->file_id))
        {
            // If this is not a single download menu item, set the page title to the download title
            if ($this->item->file_title) {
                $title = $this->item->file_title;
            }
            
            $path = array(array('title' => $this->item->file_title, 'link' => ''));
            
            $category = JDCategories::getInstance('Download')->get($this->item->cat_id);
            while ($category && ($menu->query['option'] != 'com_jdownloads' || $menu->query['view'] == 'download' || $id != $category->id) && $category->id > 1)
            {
                $path[] = array('title' => $category->title, 'link' => JdownloadsHelperRoute::getCategoryRoute($category->id, true));
                $category = $category->getParent();
            }
            $path = array_reverse($path);
            foreach($path as $item)
            {
                $pathway->addItem($item['title'], $item['link']);
            }
        }        
        
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
		if (empty($title)) {
			$title = $this->item->file_title;
		}
		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
        
        // use the Downloads description when the metadesc is still empty
        if (empty($this->item->metadesc)) 
        {
            $metadescription = strip_tags($this->item->description); 
            
            if (strlen($metadescription) >= 150)
            { 
               $metadescshort = substr($metadescription, 0, strpos($metadescription," ",150))." ...";  
            } 
            else
            {
               $metadescshort = $metadescription;
            }
            $this->document->setDescription($metadescshort);
        }            
        
		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		
        // use at first settings from download - alternate from jD configuration
		if ($this->item->robots)
        {
            $this->document->setMetadata('robots', $this->item->robots);    
        } elseif ($jlistConfig['robots']){
            // use settings from jD-config
            $this->document->setMetadata('robots', $jlistConfig['robots']);    
        } else {
            // is not defined in item or jd-config - so we use the global config setting
            $this->document->setMetadata( 'robots' , $app->getCfg('robots' ));
        }

	}
}
