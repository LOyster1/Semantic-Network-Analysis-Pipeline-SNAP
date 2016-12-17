<?php
/**
 * @package     com_jdownloads
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @modified    by Arno Betz for jDownloads
 */

defined('_JEXEC') or die;

/**
 * Search Model
 *
 */
class jdownloadsModelSearch extends JModelLegacy
{
	/**
	 * Sezrch data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Search total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Search areas
	 *
	 * @var integer
	 */
	var $_areas = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		//Get configuration
		$app	= JFactory::getApplication();
		$config = JFactory::getConfig();
        
        $quoted = false;
        
        // we use a session to store the required data for the pagination 
        $session = JFactory::getSession();

        // When '1' we do clean the prior stored session data  
        $reset = (int)$app->input->get('reset', '', 'string');
        
        if ( $reset == 1){
            $session->set('jd_searchword', '');
            $session->set('jd_searchphrase', '');
            $session->set('jd_ordering', '');
        } else {
            $old_searchword     = $session->get('jd_searchword');
            $old_searchphrase   = $session->get('jd_searchphrase');
            $old_ordering       = $session->get('jd_ordering');
        }
        
		// Get the pagination request variables
		$this->setState('limit', $app->getUserStateFromRequest('com_jdownloads.limit', 'limit', $config->get('list_limit'), 'uint'));
        $this->setState('limitstart', $app->input->get('limitstart', 0, 'uint'));

        // Set the search parameters
        $keyword  = urldecode($app->input->getString('searchword'));

        if ($keyword && !$reset){
            // slashes cause errors, <> get stripped anyway later on. # causes problems.
            $badchars = array('#', '>', '<', '\\');
            $searchword = trim(str_replace($badchars, '', $keyword));
            // if searchword enclosed in double quotes, strip quotes and do exact match
            if (substr($searchword, 0, 1) == '"' && substr($searchword, -1) == '"') {
                $keyword = substr($searchword, 1, -1);
                $quoted = true;
            } else {
                $keyword = $searchword;
            }
            $session->set('jd_searchword', $keyword);
        } else {
            if (isset($old_searchword) && $old_searchword != ''){
                $keyword = $old_searchword;
            } else {
                $keyword = '';
            }
        }

        if ($quoted){
            $searchphrase = 'exact';      
        } else {    
            $searchphrase	= $app->input->get('searchphrase', '', 'word');
        }    
        if ($searchphrase && !$reset){
            $session->set('jd_searchphrase', $searchphrase);
        } else {
            if (isset($old_searchphrase) && $old_searchphrase != ''){
                $searchphrase = $old_searchphrase;
            } else {
                $searchphrase = 'all';
            }
        }
		
        $ordering = $app->input->get('ordering', '', 'word');
		if ($ordering && !$reset){
            $session->set('jd_ordering', $ordering);
        } else {
            if (isset($old_ordering) && $old_ordering != ''){
                $ordering = $old_ordering;
            } else {
                $ordering = 'newest';
            }
        }
        
        $this->setSearch($keyword, $searchphrase, $ordering);
        
		//Set the search areas
		if (!$reset){
            $areas = $app->input->get('areas', null, 'array');
        } else {
            $areas = array();
        }
		$this->setAreas($areas);
	}

	/**
	 * Method to set the search parameters
	 *
	 * @access	public
	 * @param string search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 */
	function setSearch($keyword, $match = 'all', $ordering = 'newest')
	{
		if (isset($keyword)) {
			$this->setState('origkeyword', $keyword);
			if($match !== 'exact') {
				$keyword 		= preg_replace('#\xE3\x80\x80#s', ' ', $keyword);
			}
			$this->setState('keyword', $keyword);
		}

		if (isset($match)) {
			$this->setState('match', $match);
		}

		if (isset($ordering)) {
			$this->setState('ordering', $ordering);
		}
	}

	/**
	 * Method to set the search areas
	 *
	 * @access	public
	 * @param	array	Active areas
	 * @param	array	Search areas
	 */
	function setAreas($active = array(), $search = array())
	{
		$this->_areas['active'] = $active;
		$this->_areas['search'] = $search;
	}

	/**
	 * Method to get item data for the category
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets get the data if it doesn't already exist
		if (empty($this->_data))
		{
			$areas = $this->getAreas();

			$results = self::getSearchResults(
				$this->getState('keyword'),
				$this->getState('match'),
				$this->getState('ordering'),
				$areas['active']
			);

			$rows = array();
			
            $this->_total    = count($results);
            if ($this->getState('limit') > 0) {
                $this->_data    = array_splice($results, $this->getState('limitstart'), $this->getState('limit'));
            } else {
                $this->_data = $results;
            }            
		}
		return $this->_data;
	}

	/**
	 * Method to get the total number of download items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get a pagination object of the download items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to get the search areas
	 *
	 * @since 1.5
	 */
	function getAreas()
	{
		global $jlistConfig;
        
        // Load the Category data
		if (empty($this->_areas['search']))
		{
			$areas = array();

            $areas['title']         = JText::_('COM_JDOWNLOADS_SEARCH_IN_TITLES');
			$areas['description']   = JText::_('COM_JDOWNLOADS_SEARCH_IN_DESCRIPTIONS');
            $areas['changelog']     = JText::_('COM_JDOWNLOADS_SEARCH_IN_CHANGELOG');
            
            if ($jlistConfig['custom.field.13.title'] != ''){
                $areas['customtext1'] = $jlistConfig['custom.field.13.title'];    
            }

            if ($jlistConfig['custom.field.14.title'] != ''){
                $areas['customtext2'] = $jlistConfig['custom.field.14.title'];    
            }
                        
            $areas['author']        = JText::_('COM_JDOWNLOADS_SEARCH_IN_AUTHOR_NAME');
            $areas['metatags']       = JText::_('COM_JDOWNLOADS_SEARCH_IN_META_TAGS');
            $this->_areas['search'] = $areas;
		}

		return $this->_areas;
	}
    
    /**
     * Get the search result
     * The sql must return the following fields that are used in a common display
     * routine: href, title, section, created, text, browsernav
     * @param string Target search string
     * @param string mathcing option, exact|any|all
     * @param string ordering option, newest|oldest|popular|alpha|category
     * @param mixed An array if the search it to be restricted to areas, null if search all
     */    

      function getSearchResults($text, $phrase='', $ordering='', $areas=null) 
      {  
            global $jlistConfig;
          
            $db      = JFactory::getDbo();
            $app     = JFactory::getApplication();
            $user    = JFactory::getUser();
            $groups  = implode(',', $user->getAuthorisedViewLevels());
            $tag     = JFactory::getLanguage()->getTag();

            require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';
            require_once JPATH_SITE . '/components/com_jdownloads/helpers/search.php';
            require_once JPATH_SITE . '/components/com_jdownloads/helpers/jdownloadshelper.php';
            
            $user_rules = JDHelper::getUserRules();
            
            $searchText = $text;

            $limit      = $this->state->get('search_limit', 500);

            $nullDate   = $db->getNullDate();
            $date = JFactory::getDate();
            $now = $date->toSql();

            $text = trim($text);
            if ($text == '') {
                return array();
            }

            $wheres = array();
            switch ($phrase) {
                case 'exact':
                    $text        = $db->Quote('%'.$db->escape($text, true).'%', false);
                    $wheres2    = array();
                    if (!$areas || in_array('title', $areas)){
                        $wheres2[]    = 'a.file_title LIKE '.$text;
                    }
                    if (!$areas || in_array('description', $areas)){
                        $wheres2[]    = 'a.description LIKE '.$text;
                        $wheres2[]    = 'a.description_long LIKE '.$text;
                    }
                    if (!$areas || in_array('changelog', $areas)){                    
                        $wheres2[]    = 'a.changelog LIKE '.$text;
                    }                        
                    if ($jlistConfig['custom.field.6.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_6 LIKE '.$text;
                        }
                    }
                    if ($jlistConfig['custom.field.7.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_7 LIKE '.$text;
                        }
                    }
                    if ($jlistConfig['custom.field.8.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_8 LIKE '.$text;
                        }
                    }
                    if ($jlistConfig['custom.field.9.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_9 LIKE '.$text;
                        }
                    }
                    if ($jlistConfig['custom.field.10.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_10 LIKE '.$text;
                        }
                    }
                    if ($jlistConfig['custom.field.13.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_13 LIKE '.$text;
                        }
                    }        
                    if ($jlistConfig['custom.field.14.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_14 LIKE '.$text;
                        }
                    }
                    if (!$areas || in_array('author', $areas)){
                        $wheres2[]    = 'a.author LIKE '.$text;
                    }    
                    if (!$areas || in_array('metatags', $areas)){
                        $wheres2[]    = 'a.metakey LIKE '.$text;
                        $wheres2[]    = 'a.metadesc LIKE '.$text;                        
                    }    
                    $where        = '(' . implode(') OR (', $wheres2) . ')';
                    break;

                case 'all':
                case 'any':
                default:
                    $words = explode(' ', $text);
                    $wheres = array();
                    foreach ($words as $word) {
                        $word        = $db->Quote('%'.$db->escape($word, true).'%', false);
                        $wheres2    = array();
                        if (!$areas || in_array('title', $areas)){
                            $wheres2[]    = 'a.file_title LIKE '.$word;
                        }    
                    if (!$areas || in_array('description', $areas)){
                        $wheres2[]    = 'a.description LIKE '.$word;
                        $wheres2[]    = 'a.description_long LIKE '.$word;
                    }
                    if (!$areas || in_array('changelog', $areas)){                    
                        $wheres2[]    = 'a.changelog LIKE '.$word;
                    }
                    if ($jlistConfig['custom.field.6.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_6 LIKE '.$word;
                        }
                    }
                    if ($jlistConfig['custom.field.7.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_7 LIKE '.$word;
                        }
                    }
                    if ($jlistConfig['custom.field.8.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_8 LIKE '.$word;
                        }
                    }
                    if ($jlistConfig['custom.field.9.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_9 LIKE '.$word;
                        }
                    }
                    if ($jlistConfig['custom.field.10.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_10 LIKE '.$word;
                        }
                    }                    
                    if ($jlistConfig['custom.field.13.title'] != ''){
                        if (!$areas || in_array('customtext1', $areas)){
                            $wheres2[]    = 'a.custom_field_13 LIKE '.$word;
                        }
                    }        
                    if ($jlistConfig['custom.field.14.title'] != ''){
                        if (!$areas || in_array('customtext2', $areas)){
                            $wheres2[]    = 'a.custom_field_14 LIKE '.$word;
                        }
                    }
                    if (!$areas || in_array('author', $areas)){
                        $wheres2[]    = 'a.author LIKE '.$word;
                    }    
                    if (!$areas || in_array('metatags', $areas)){
                        $wheres2[]    = 'a.metakey LIKE '.$word;
                        $wheres2[]    = 'a.metadesc LIKE '.$word;                        
                    }    
                        $wheres[]    = implode(' OR ', $wheres2);
                    }
                    $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
                    break;
            }

            $morder = '';
            switch ($ordering) {
                case 'oldest':
                    $order = 'a.date_added ASC';
                    break;

                case 'popular':
                    $order = 'a.downloads DESC';
                    break;

                case 'alpha':
                    $order = 'a.file_title ASC';
                    break;

                case 'category':
                    $order = 'c.title ASC, a.file_title ASC';
                    $morder = 'a.file_title ASC';
                    break;

                case 'newest':
                default:
                    $order = 'a.date_added DESC';
                    break;
            }

            $uncategorised = JText::_('COM_JDOWNLOADS_SELECT_UNCATEGORISED');
            $rows = array();
            $query    = $db->getQuery(true);

            // search downloads
            if ($limit > 0)
            {
                $query->clear();
                //sqlsrv changes
                $case_when = ' CASE WHEN ';
                $case_when .= $query->charLength('a.file_alias');
                $case_when .= ' THEN ';
                $a_id = $query->castAsChar('a.file_id');
                $case_when .= $query->concatenate(array($a_id, 'a.file_alias'), ':');
                $case_when .= ' ELSE ';
                $case_when .= $a_id.' END as slug';

                $case_when1 = ' CASE WHEN ';
                $case_when1 .= $query->charLength('c.alias');
                $case_when1 .= ' THEN ';
                $c_id = $query->castAsChar('c.id');
                $case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
                $case_when1 .= ' ELSE ';
                $case_when1 .= $c_id.' END as catslug';

                $query->select('a.file_title AS title, a.metadesc, a.metakey, a.url_download, a.extern_file, a.other_file_id, a.license_agree, a.password, a.author, a.date_added AS created, a.language, a.custom_field_6, a.custom_field_7, a.custom_field_8, a.custom_field_9, a.custom_field_10');
                $query->select($query->concatenate(array('a.description', 'a.description_long', 'a.changelog', 'a.custom_field_13', 'a.custom_field_14')).' AS text');
                $query->select('CASE c.title WHEN \'root\' THEN '.$db->Quote($uncategorised).' ELSE c.title END AS section, '.$case_when.','.$case_when1.', '.'\'2\' AS browsernav');

                $query->from('#__jdownloads_files AS a');
                $query->innerJoin('#__jdownloads_categories AS c ON c.id = a.cat_id');
                $query->where('('. $where .')' . 'AND a.published = 1 AND c.published = 1 AND a.access IN ('.$groups.') '
                            .'AND c.access IN ('.$groups.') '
                            .'AND (a.publish_from = '.$db->Quote($nullDate).' OR a.publish_from <= '.$db->Quote($now).') '
                            .'AND (a.publish_to = '.$db->Quote($nullDate).' OR a.publish_to >= '.$db->Quote($now).')' );
                $query->group('a.file_id, a.file_title, a.metadesc, a.metakey, a.author, a.date_added, a.description, a.description_long, a.changelog, a.custom_field_6, a.custom_field_7, a.custom_field_8, a.custom_field_9, a.custom_field_10, a.custom_field_13, a.custom_field_14, c.title, a.file_alias, c.alias, c.id');
                $query->order($order);

                // Filter by language
                if ($app->isSite() && $app->getLanguageFilter()) {
                    $query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
                    $query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
                }

                $db->setQuery($query, 0, $limit);
                $list = $db->loadObjectList();
                $limit -= count($list);

                if (isset($list))
                {
                    foreach($list as $key => $item)
                    {
                        $direct_download = $jlistConfig['direct.download'];
                        if ((!$item->url_download && !$item->extern_file && !$item->other_file_id) || $item->password || $item->license_agree || $user_rules->view_captcha){
                            // this download is a simple document without a file so we can not use 'direct' download option
                            // or we need the summary page for password, captcha or license agree
                            $direct_download = 0;
                        }
                        
                        if ($jlistConfig['view.detailsite']){
                            // we must link to the details page
                            $list[$key]->href = JDownloadsHelperRoute::getDownloadRoute($item->slug, $item->catslug, $item->language);
                        } else {
                            if ($direct_download){
                                // we must start the download process directly
                                $list[$key]->href = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.(int)$item->slug.'&amp;catid='.(int)$item->catslug.'&amp;m=0');                                
                            } else {
                                if (!$item->url_download && !$item->extern_file && !$item->other_file_id){
                                    // Download is only a simple document without a file so we must link to the details page
                                    $list[$key]->href = JDownloadsHelperRoute::getDownloadRoute($item->slug, $item->catslug, $item->language);
                                } else {
                                    // we must link to the summary page 
                                    $list[$key]->href = JRoute::_('index.php?option=com_jdownloads&amp;view=summary&amp;id='.$item->slug.'&amp;catid='.(int)$item->catslug);                                                                
                                }
                            }    
                        }                        
                    }
                }
                $rows[] = $list;
            }



            $results = array();
            if (count($rows))
            {
                foreach($rows as $row)
                {
                    $new_row = array();
                    foreach($row as $key => $download) {
                        if (JDSearchHelper::checkNoHTML($download, $searchText, array('text', 'title', 'author', 'metadesc', 'metakey', 'custom_field_6', 'custom_field_7', 'custom_field_8', 'custom_field_9', 'custom_field_10'))) {
                            $new_row[] = $download;
                        }
                    }
                    $results = array_merge($results, (array) $new_row);
                }
            }

            return $results;
        }    
}
