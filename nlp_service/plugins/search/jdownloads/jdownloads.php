<?php
/**
 * @copyright Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * Modified for jDownloads search plugin
 */
 
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE.'/components/com_jdownloads/helpers/jdownloadshelper.php';
jimport( 'joomla.plugin.plugin' ); 

class plgSearchJdownloads extends JPlugin
{
    
        /**
         * Constructor
         *
         * @access      protected
         * @param       object  $subject The object to observe
         * @param       array   $config  An array that holds the plugin configuration
         */
        public function __construct(& $subject, $config)
        {
            parent::__construct($subject, $config);
            $this->loadLanguage();
        }    
    
        /**
         * @return array An array of search areas
         */
        function onContentSearchAreas()
        {
            static $areas = array(
                'jdownloads' => 'PLG_SEARCH_JDOWNLOADS_JDOWNLOADS'
                );
                return $areas;
        }
        
        /**
         * Content Search method
         * The sql must return the following fields that are used in a common display
         * routine: href, title, section, created, text, browsernav
         * @param string Target search string
         * @param string mathcing option, exact|any|all
         * @param string ordering option, newest|oldest|popular|alpha|category
         * @param mixed An array if the search it to be restricted to areas, null if search all
         */    

          function onContentSearch($text, $phrase='', $ordering='', $areas=null) 
          {  
                $db      = JFactory::getDbo();
                $app     = JFactory::getApplication();
                $user    = JFactory::getUser();
                $groups  = implode(',', $user->getAuthorisedViewLevels());
                $tag     = JFactory::getLanguage()->getTag();

                require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';
                require_once JPATH_ADMINISTRATOR . '/components/com_search/helpers/search.php';

                $searchText = $text;
                if (is_array($areas)) {
                    if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
                        return array();
                    }
                }

                $limit      = $this->params->def('search_limit', 50);

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
                        $wheres2[]    = 'a.file_title LIKE '.$text;
                        $wheres2[]    = 'a.description LIKE '.$text;
                        $wheres2[]    = 'a.description_long LIKE '.$text;
                        $wheres2[]    = 'a.changelog LIKE '.$text;
                        $wheres2[]    = 'a.author LIKE '.$text;
                        $wheres2[]    = 'a.metakey LIKE '.$text;
                        $wheres2[]    = 'a.metadesc LIKE '.$text;
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
                            $wheres2[]    = 'a.file_title LIKE '.$word;
                            $wheres2[]    = 'a.description LIKE '.$word;
                            $wheres2[]    = 'a.description_long LIKE '.$word;
                            $wheres2[]    = 'a.changelog LIKE '.$word;
                            $wheres2[]    = 'a.author LIKE '.$word;
                            $wheres2[]    = 'a.metakey LIKE '.$word;
                            $wheres2[]    = 'a.metadesc LIKE '.$word;
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
                        $order = 'c.title ASC, a.title ASC';
                        $morder = 'a.title ASC';
                        break;

                    case 'newest':
                    default:
                        $order = 'a.date_added DESC';
                        break;
                }

                $uncategorised = JText::_('PLG_SEARCH_JDOWNLOADS_UNCATEGORISED');
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

                    $query->select('a.file_title AS title, a.metadesc, a.metakey, a.date_added AS created, a.language, a.author, a.url_download, a.extern_file, a.password, a.license_agree, a.other_file_id');
                    $query->select($query->concatenate(array('a.description', 'a.description_long')).' AS text');
                    $query->select('CASE c.title WHEN \'root\' THEN '.$db->Quote($uncategorised).' ELSE c.title END AS section, '.$case_when.','.$case_when1.', '.'\'2\' AS browsernav');

                    $query->from('#__jdownloads_files AS a');
                    $query->innerJoin('#__jdownloads_categories AS c ON c.id = a.cat_id');
                    $query->where('('. $where .')' . 'AND a.published = 1 AND c.published = 1 AND a.access IN ('.$groups.') '
                                .'AND c.access IN ('.$groups.') '
                                .'AND (a.publish_from = '.$db->Quote($nullDate).' OR a.publish_from <= '.$db->Quote($now).') '
                                .'AND (a.publish_to = '.$db->Quote($nullDate).' OR a.publish_to >= '.$db->Quote($now).')' );
                    $query->group('a.file_id, a.file_title, a.metadesc, a.metakey, a.date_added, a.description, a.description_long, c.title, a.file_alias, c.alias, c.id, a.author');                    
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
                        $jlistConfig = JDHelper::buildjlistConfig();
                        $user_rules = JDHelper::getUserRules();
                        
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
                            if (searchHelper::checkNoHTML($download, $searchText, array('text', 'title', 'metadesc', 'metakey', 'author'))) {
                                $new_row[] = $download;
                            }
                        }
                        $results = array_merge($results, (array) $new_row);
                    }
                }

                return $results;
            }
}
?>