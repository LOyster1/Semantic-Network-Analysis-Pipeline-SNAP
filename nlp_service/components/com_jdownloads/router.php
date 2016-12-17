<?php
/**
 * @package jDownloads
 * @version 2.5  
 * @copyright (C) 2007 - 2014 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_jdownloads component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function JDownloadsBuildRoute(&$query)
{
	static $jd_menu_items;
    $segments	= array();
    $view = '';

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
    
    if (!$jd_menu_items){
        // $jd_menu_items = $menu->getItems('component', 'com_jdownloads');
        
    }    

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}

	if (isset($query['view'])) {
		$view = $query['view'];
	} else {
        // exist alternate the task value?
        if (!isset($query['task'])){
            // we need to have a view in the query or it is an invalid URL
            return $segments;
        }    
	}
    
    // categories page
    if ($view == 'categories'){
        if (!$menuItemGiven) {
            $segments[] = $view;
        }
        unset($query['view']);        
    }    
    
    // downloads list (all or only uncategorised)
    if ($view == 'downloads'){
        if (!$menuItemGiven) {
            $segments[] = $view;
        }

        if (isset($query['type']) && $query['type'] == 'uncategorised'){
            $segments[] = $query['type'];
            unset($query['type']);        
        } else {
            $segments[] = 'all';
        }
        unset($query['view']);        
    }    
    
    // category page
    if ($view == 'category'){
        $segments[] = $view;
        unset($query['view']);        
        
        if (isset($query['catid'])) {
            if (strpos($query['catid'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('alias')
                    ->from('#__jdownloads_categories')
                    ->where('id='.(int)$query['catid'])
                );
                $alias = $db->loadResult();
                $query['catid'] = $query['catid'].':'.$alias;
            }            
            
            $segments[] = $query['catid'];
            unset($query['catid']);
        } else {
			// we should have id set for this view.  If we don't, it is an error
			return $segments;
		}
    }

    // download page (single item)
    if ($view == 'download'){
        $segments[] = $view;
        unset($query['view']);

        if (isset($query['catid'])) {
            if (strpos($query['catid'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('alias')
                    ->from('#__jdownloads_categories')
                    ->where('id='.(int)$query['catid'])
                );
                $alias = $db->loadResult();
                $query['catid'] = $query['catid'].':'.$alias;
            }            
            
            $segments[] = $query['catid'];
            unset($query['catid']);
        }            
        
        if (isset($query['id'])) {
            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('file_alias')
                    ->from('#__jdownloads_files')
                    ->where('file_id='.(int)$query['id'])
                );
                $alias = $db->loadResult();
                $query['id'] = $query['id'].':'.$alias;
            }
            $segments[] = $query['id'];
            unset($query['id']); 
        } else {
            // we should have id set for this view.  If we don't, it is an error
            return $segments;
        }
    }     
    
    // search page
    if ($view == 'search'){
        $segments[] = $view;
        unset($query['view']);
        
        /* 
        if (!$menuItemGiven) {
            $segments[] = $view;
        }
        unset($query['view']);        
        */
    }
    
    // summary page
    if ($view == 'summary'){
        $segments[] = $view;
        unset($query['view']);        
        
        if (isset($query['catid'])) {
            if (strpos($query['catid'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('alias')
                    ->from('#__jdownloads_categories')
                    ->where('id='.(int)$query['catid'])
                );
                $alias = $db->loadResult();
                $query['catid'] = $query['catid'].':'.$alias;
            }            
            
            $segments[] = $query['catid'];
            unset($query['catid']);
        }    

        if (isset($query['id'])) {
            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('file_alias')
                    ->from('#__jdownloads_files')
                    ->where('file_id='.(int)$query['id'])
                );
                $alias = $db->loadResult();
                $query['id'] = $query['id'].':'.$alias;
            }
            $segments[] = $query['id'];
            unset($query['id']); 
        } else {
            // we should have id set for this view.  If we don't, it is an error
            return $segments;
        }
        
        // mirror link 
        if (isset($query['m']) && $query['m'] > 0){
            $segments[] = (int)$query['m'];
            unset($query['m']);
        }
        
    }

    // report page
    if ($view == 'report'){
        $segments[] = $view;
        unset($query['view']);        
        
        if (isset($query['catid'])) {
            if (strpos($query['catid'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('alias')
                    ->from('#__jdownloads_categories')
                    ->where('id='.(int)$query['catid'])
                );
                $alias = $db->loadResult();
                $query['catid'] = $query['catid'].':'.$alias;
            }            
            
            $segments[] = $query['catid'];
            unset($query['catid']);
        }    

        if (isset($query['id'])) {
            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('file_alias')
                    ->from('#__jdownloads_files')
                    ->where('file_id='.(int)$query['id'])
                );
                $alias = $db->loadResult();
                $query['id'] = $query['id'].':'.$alias;
            }
            $segments[] = $query['id'];
            unset($query['id']); 
        } else {
            // we should have id set for this view.  If we don't, it is an error
            return $segments;
        }
    }

	// if the layout is specified and it is the same as the layout in the menu item, we
	// unset it so it doesn't go into the query string.
	if (isset($query['layout'])) {
		if ($menuItemGiven && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {
				unset($query['layout']);
                unset($query['view']);
			}
		} else {
			if ($query['layout'] == 'edit') {
				//unset($query['layout']);
			}
		}
	}
    
    // send download task
    if (isset($query['task']) && $query['task'] == 'download.send'){
         $segments[] = 'send';
         unset($query['task']);

        if (isset($query['catid'])) {
            if (strpos($query['catid'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('alias')
                    ->from('#__jdownloads_categories')
                    ->where('id='.(int)$query['catid'])
                );
                $alias = $db->loadResult();
                $query['catid'] = $query['catid'].':'.$alias;
            }            
            
            $segments[] = $query['catid'];
            unset($query['catid']);
        }    

        if (isset($query['id'])) {
            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') === false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('file_alias')
                    ->from('#__jdownloads_files')
                    ->where('file_id='.(int)$query['id'])
                );
                $alias = $db->loadResult();
                $query['id'] = $query['id'].':'.$alias;
            }
            $segments[] = $query['id'];
            unset($query['id']); 
        } 
        
        if (isset($query['m']) && $query['m'] > 0){
            $segments[] = (int)$query['m'];
            unset($query['m']); 
        } else {
            unset($query['m']); 
        }

        if (isset($query['list'])){
            $value = preg_match("/[0-9,]+/", $query['list']);
            if ($value){
                $segments[] = $query['list'];
                unset($query['list']); 
            }    
        }
        
        if (isset($query['user'])){
            $segments[] = (int)$query['user'];
            unset($query['user']);
        }         
    }

	return $segments;
}



/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function JDownloadsParseRoute($segments)
{
	$vars = array();

    require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';    
    
	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$db = JFactory::getDBO();

	// Count route segments
	$count = count($segments);
/*
	if (!isset($item)) {
		$vars['view']	= $segments[0];
		$vars['id']		= $segments[$count - 1];

		return $vars;
	}

*/
    
    switch($segments[0])
        {
            case 'categories' :
                    $vars['view']   = 'categories';
            break;
            
            case 'uncategorised' :
                   $vars['view']    = 'downloads';
                   $vars['type']    = 'uncategorised';
            break;
            
            case 'all' :
                   $vars['view']    = 'downloads';
                   $vars['type']    = 'all';
            break;            
            
            case 'mydownloads' :
                    $vars['view']   = 'mydownloads';
            break;        
            
            case 'category'   :
                    $vars['view']   = $segments[$count-2];
                    $vars['catid']  = (int)$segments[$count-1];
            break;
            
            case 'download'   :
                    $vars['view']   = 'download';
                    $vars['catid']  = (int)$segments[$count-2];
                    $vars['id']     = (int)$segments[$count-1];
                
            break;
            
            case 'summary'   :
                    $vars['view']   = 'summary';
                    if ($count > 1){
                        $vars['catid']  = (int)$segments[$count-2];
                        $vars['id']     = (int)$segments[$count-1];
                    }    
            break;

            case 'report'   :
                    $vars['view']   = 'report';
                    $vars['catid']  = (int)$segments[$count-2];
                    $vars['id']     = (int)$segments[$count-1];
            break;

            
            case 'search'   :
                if($count == 1) {
                    $vars['view']   = 'search';
                }
            break;
            
            case 'send'   :
                    $vars['task']   = 'download.send';
                    $single_file = true;
                    foreach ($segments as $segment){
                        if (strpos($segment, ',')){
                            $single_file = false;
                        } 
                    }
                    if (!$single_file){
                        // mass download
                        $vars['catid']  = (int)$segments[1];
                        $vars['list']   = $segments[2];
                        $vars['user']   = (int)$segments[3];
                    } else {
                        // single download
                        $vars['catid']  = (int)$segments[1];
                        $vars['id']     = (int)$segments[2];
                        if (isset($segments[3]) && $segments[3] > 0){
                            $vars['m']  = (int)$segments[3];
                        }    
                    }
                    
            break;
            
            
        }
        
            
    
	return $vars;
}
?>