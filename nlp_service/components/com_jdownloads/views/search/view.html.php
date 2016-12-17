<?php
/**
 * @package     com_jdownloads
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @modified    by Arno Betz for jDownloads
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the search page
 *
 */
class jdownloadsViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{
		global $jlistConfig;
        
        require_once JPATH_COMPONENT.'/helpers/search.php';
        
		// Initialise some variables
		$app	= JFactory::getApplication();
        $user   = JFactory::getUser();
        $pathway = $app->getPathway();
		$uri	= JFactory::getURI();
        
        $jd_user_settings = JDHelper::getUserRules();

		$error	= null;
		$rows	= null;
		$results= null;
		$total	= 0;

		// Get some data from the model
		$areas	= $this->get('areas');
		$state		= $this->get('state');
		$searchword = $state->get('keyword');
		$params = $app->getParams();

		$menus	= $app->getMenu();
		$menu	= $menus->getActive();

        // add all needed cripts and css files
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'components/com_jdownloads/assets/js/jdownloads.js');
        $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_fe.css", "text/css", null, array() );
        $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_buttons.css", "text/css", null, array() );

        $custom_css_path = JPATH_ROOT.'/components/com_jdownloads/assets/css/jdownloads_custom.css';
        if (JFile::exists($custom_css_path)){
            $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_custom.css", 'text/css', null, array() );                
        }           
        
        $document->addScriptDeclaration('var live_site = "'.JURI::base().'";');
        $document->addScriptDeclaration('function openWindow (url) {
                fenster = window.open(url, "_blank", "width=550, height=480, STATUS=YES, DIRECTORIES=NO, MENUBAR=NO, SCROLLBARS=YES, RESIZABLE=NO");
                fenster.focus();
                }');        
        
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu)) {
			$menu_params = new JRegistry;
			$menu_params->loadString($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	JText::_('COM_JDOWNLOADS_SEARCH'));
			}
		}
		else {
			$params->set('page_title',	JText::_('COM_JDOWNLOADS_SEARCH'));
		}

		$title = $params->get('page_title');
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			$this->document->setMetadata('robots', $params->get('robots'));
		}
        
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
        
		// built select lists
		$orders = array();
		$orders[] = JHtml::_('select.option',  'newest',   JText::_('COM_JDOWNLOADS_SEARCH_NEWEST_FIRST'));
		$orders[] = JHtml::_('select.option',  'oldest',   JText::_('COM_JDOWNLOADS_SEARCH_OLDEST_FIRST'));
		$orders[] = JHtml::_('select.option',  'popular',  JText::_('COM_JDOWNLOADS_SEARCH_MOST_POPULAR'));
		$orders[] = JHtml::_('select.option',  'alpha',    JText::_('COM_JDOWNLOADS_SEARCH_ALPHABETICAL'));
		$orders[] = JHtml::_('select.option',  'category', JText::_('COM_JDOWNLOADS_SEARCH_CATEGORY'));

		$lists = array();
		$lists['ordering'] = JHtml::_('select.genericlist', $orders, 'ordering', 'class="inputbox"', 'value', 'text', $state->get('ordering'));

		$searchphrases		= array();
		$searchphrases[]	= JHtml::_('select.option',  'all', JText::_('COM_JDOWNLOADS_SEARCH_ALL_WORDS'));
		$searchphrases[]	= JHtml::_('select.option',  'any', JText::_('COM_JDOWNLOADS_SEARCH_ANY_WORDS'));
		$searchphrases[]	= JHtml::_('select.option',  'exact', JText::_('COM_JDOWNLOADS_SEARCH_EXACT_PHRASE'));
		$lists['searchphrase' ]= JHtml::_('select.radiolist',  $searchphrases, 'searchphrase', '', 'value', 'text', $state->get('match'));

		// log the search
        // not used currently
		// SearchHelper::logSearch($searchword);

		//limit searchword
		$lang = JFactory::getLanguage();
		$upper_limit = $lang->getUpperLimitSearchWord();
		$lower_limit = $lang->getLowerLimitSearchWord();
		if (JDSearchHelper::limitSearchWord($searchword)) {
			$error = JText::sprintf('COM_JDOWNLOADS_ERROR_SEARCH_MESSAGE', $lower_limit, $upper_limit);
		}

		//sanatise searchword
		if (JDSearchHelper::santiseSearchWord($searchword, $state->get('match'))) {
			$error = JText::_('COM_JDOWNLOADS_ERROR_IGNOREKEYWORD');
		}

		if (!$searchword && count(JRequest::get('post'))) {
			//$error = JText::_('COM_JDOWNLOADS_ERROR_ENTERKEYWORD');
		}

		// put the filtered results back into the model
		// for next release, the checks should be done in the model perhaps...
		$state->set('keyword', $searchword);
		if ($error == null) {
			$results	= $this->get('data');
			$total		= $this->get('total');
			$pagination	= $this->get('pagination');

			// require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';

			for ($i=0, $count = count($results); $i < $count; $i++)
			{
				$row = &$results[$i]->text;

				if ($state->get('match') == 'exact') {
					$searchwords = array($searchword);
					$needle = $searchword;
				}
				else {
					$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $searchword);
					$searchwords = preg_split("/\s+/u", $searchworda);
 					$needle = $searchwords[0];
				}

				$row = JDSearchHelper::prepareSearchContent($row, $needle);
				$searchwords = array_unique($searchwords);
				$searchRegex = '#(';
				$x = 0;

				foreach ($searchwords as $k => $hlword)
				{
					$searchRegex .= ($x == 0 ? '' : '|');
					$searchRegex .= preg_quote($hlword, '#');
					$x++;
				}
				$searchRegex .= ')#iu';

				$row = preg_replace($searchRegex, '<span class="highlight">\0</span>', $row);

				$result = &$results[$i];
				if ($result->created) {
					$created = JHtml::_('date', $result->created, JText::_('DATE_FORMAT_LC3'));
				}
				else {
					$created = '';
				}

				$result->text		= JHtml::_('content.prepare', $result->text, '', 'com_jdownloads.search');
				$result->created	= $created;
				$result->count		= $i + 1;
			}
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('pagination',  $pagination);
		$this->assignRef('results',		$results);
		$this->assignRef('lists',		$lists);
		$this->assignRef('params',		$params);

		$this->ordering = $state->get('ordering');
		$this->searchword = $searchword;
		$this->origkeyword = $state->get('origkeyword');
		$this->searchphrase = $state->get('match');
		$this->searchareas = $areas;

		$this->total = $total;
		$this->error = $error;
		$this->action = $uri;

		parent::display($tpl);
	}
}
