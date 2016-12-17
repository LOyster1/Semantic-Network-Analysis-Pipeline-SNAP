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
 
defined('_JEXEC') or die;

/**
 * HTML View class for the jDownloads component
 *
 */
class JdownloadsViewCategory extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $category;
	protected $children;
	protected $pagination;

	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	protected $columns = 1;

	function display($tpl = null)
	{
		
        global $jlistConfig;
        
        $app	= JFactory::getApplication();
		$user	= JFactory::getUser();
        
        $jd_user_settings = JDHelper::getUserRules();

		// Get some data from the models
		$state		= $this->get('State');
		$params		= $state->params;
		$items		= $this->get('Items');      // get the category downloads
		$category	= $this->get('Category');   // get the selected category data
		$children	= $this->get('Children');   // get the categories sub categories
		$parent		= $this->get('Parent');     // get the categories parent categories
		$pagination = $this->get('Pagination');
        
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
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
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
        
        $document->addScriptDeclaration('live_site = "'.JURI::base().'";');
        
        $document->addScriptDeclaration('function openWindow (url) {
        fenster = window.open(url, "_blank", "width=550, height=480, STATUS=YES, DIRECTORIES=NO, MENUBAR=NO, SCROLLBARS=YES, RESIZABLE=NO");
        fenster.focus();
        }');
        
        if ($jlistConfig['use.lightbox.function']){
            JHtml::_('bootstrap.framework');
            $document->addScript(JURI::base().'components/com_jdownloads/assets/lightbox/lightbox.js');
            $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/lightbox/lightbox.css", 'text/css', null, array() );
        }
        
        // required only for subcategories pagination
        $document->addScript(JURI::base().'components/com_jdownloads/assets/pagination/jdpagination.js');

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

		if ($category == false)
		{
			return JError::raiseError(404, JText::_('COM_JDOWNLOADS_CATEGORY_NOT_FOUND'));
		}

		if ($parent == false)
		{
			return JError::raiseError(404, JText::_('COM_JDOWNLOADS_CATEGORY_PARENT_NOT_FOUND'));
		}

		// Setup the category parameters.
		$cparams = $category->getParams();
		$category->params = clone($params);
		$category->params->merge($cparams);
        
        $category->tags = new JHelperTags;
        $category->tags->getItemTags('com_jdownloads.category', $category->id);        

		// Check whether category access level allows access.
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		if (!in_array($category->access, $groups)) {
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		// Compute the download slugs and prepare introtext (runs content plugins).
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = &$items[$i];
			$item->slug = $item->file_alias ? ($item->file_id . ':' . $item->file_alias) : $item->file_id;

			// No link for ROOT category
			if ($item->parent_alias == 'root') {
				$item->parent_slug = null;
			}

            // required for some content plugins
            $item->text = $item->description;
            $item->id = $item->file_id; 

			$item->event = new stdClass();
            
			$dispatcher = JDispatcher::getInstance();

			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onContentPrepare', array ('com_jdownloads.category', &$item, &$this->params, 0));

			$results = $dispatcher->trigger('onContentAfterTitle', array('com_jdownloads.category', &$item, &$item->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_jdownloads.category', &$item, &$item->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentAfterDisplay', array('com_jdownloads.category', &$item, &$item->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));

            // check the result from content plugins
            if ($item->text != $item->description){
                $item->description = $item->text; 
            }             
        }

		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) || ((strpos($active->link, 'view=category') === false) || (strpos($active->link, '&catid=' . (string) $category->id) === false))) {
			// Get the layout from the merged category params
			if ($layout = $category->params->get('category_layout')) {
				$this->setLayout($layout);
			}
		}
		// At this point, we are in a menu item, so we don't override the layout
		elseif (isset($active->query['layout'])) {
			// We need to set the layout from the query in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		$children = array($category->id => $children);

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->maxLevel = $params->get('maxLevel', -1);
		$this->assignRef('state', $state);
		$this->assignRef('items', $items);
		$this->assignRef('category', $category);
		$this->assignRef('children', $children);
		$this->assignRef('params', $params);
		$this->assignRef('parent', $parent);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('user', $user);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		global $jlistConfig;
        
        $app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_JDOWNLOADS_DOWNLOADS'));
		}
        
        $title = $this->params->get('page_title', '');

		if (isset($menu->query['catid'])){
            $id = (int) @$menu->query['catid'];
        } else {
            $id = 0;
        }  

		if ($menu && ($menu->query['option'] != 'com_jdownloads' || $menu->query['view'] != 'category' || $id != $this->category->id)) {
			
            // If this is not a single category menu item, set the page title to the category title
            if ($this->category->title) {
                $title = $this->category->title;
            }
            
            $path = array(array('title' => $this->category->title, 'link' => ''));
			$category = $this->category->getParent();

			while (($menu->query['option'] != 'com_jdownloads' || $menu->query['view'] == 'download' || $id != $category->id) && $category->id > 1){
				$path[] = array('title' => $category->title, 'link' => JdownloadsHelperRoute::getCategoryRoute($category->id, true));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $item){
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title)){
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->category->metadesc){
			$this->document->setDescription($this->category->metadesc);
		}
		elseif (!$this->category->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->category->metakey){
			$this->document->setMetadata('keywords', $this->category->metakey);
		}
		elseif (!$this->category->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

        // use at first settings from download - alternate from jD configuration
        if ($this->category->robots){
            $this->document->setMetadata('robots', $this->category->robots);    
        } 
        elseif ($jlistConfig['robots']){
            // use settings from jD-config
            $this->document->setMetadata('robots', $jlistConfig['robots']);    
        } else {
            // is not defined in item or jd-config - so we use the global config setting
            $this->document->setMetadata( 'robots' , $app->getCfg('robots' ));
        }
	}
}
