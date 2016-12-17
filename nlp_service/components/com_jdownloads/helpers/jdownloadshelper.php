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

setlocale(LC_ALL, 'C.UTF-8', 'C');

jimport( 'joomla.application.component.controller');
jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jdownloads'.DS.'tables');

class JDHelper
{


    /*
    * Read configuration parameter
    *
    * @return jlistConfig
    */
    public static function buildjlistConfig(){
        $db = JFactory::getDBO(); 

        $jlistConfig = array();
        $db->setQuery("SELECT setting_name, setting_value FROM #__jdownloads_config");
        $jlistConfigObj = $db->loadObjectList();
        if(!empty($jlistConfigObj)){
            foreach ($jlistConfigObj as $jlistConfigRow){
                $jlistConfig[$jlistConfigRow->setting_name] = $jlistConfigRow->setting_value;
            }
        }
        return $jlistConfig;
    }
    
    /*
    * Read user group settings and limitations from jDownloads user groups table
    * Since a user can be a member in multiple groups we must get the values from the 'most important' group.
    * This are defined by admin in a sorted list (significance) with numbers in ascending order.
    * 
    * Example from Joomla 3.3:        Significance/Importance:
    * 
    * 1 : public                                  1
    * 13: guest                                   2
    * 2 : registered                              3
    * 3 : - author                                4
    * 12: - customer group (Example)              5
    * 4 : - - editor                              6
    * 10: - - shop suppliers (Example)            7
    * 5 : - - - publisher                         8
    * 14: - downloader                            9
    * 6 : manager                                 10
    * 7 : administrator                           11
    * 8 : super user                              12
    * 
    * - the user is a member in 'registered' and 'downloader'
    * - the registered has a significance from '3' the list
    * - the downloader has a significance from '9' the list
    * as result is used always the ID from highest value in the list. So in our case above: ID = 14. 
    * 
    * @return array     $jd_user_settings 
    */
    public static function getUserRules(){
        
         $db   = JFactory::getDBO();
         $user = JFactory::getUser();
         
         jimport( 'joomla.access.access' );
         $groups_id = JAccess::getGroupsByUser($user->id);
         
         if (!$groups_id) $groups_id[] = 1; // user is not registered = guest
         
         // load the needed data for the importance 
         $query = $db->getQuery(true)
            ->select('group_id, importance, id')
            ->from('#__jdownloads_usergroups_limits')
            ->order('importance');
         $db->setQuery($query);
         $groups_levels = $db->loadAssocList('group_id');
                      
         // user is a member in multiple groups so we mucg search the 'most important' group
         if (count($groups_id) > 1){
             $value = 0;
             $dummy = 0;
             foreach ($groups_id as $group_id){
                 if (isset($groups_levels[$group_id])){
                     if ($groups_levels[$group_id]['importance'] > $dummy){
                         $dummy = $groups_levels[$group_id]['importance'];
                         $value = $groups_levels[$group_id]['group_id'];
                     }   
                 }
             }
             if ($value > 0){
                 // we have found the most important group so update the value
                 unset($groups_id);
                 $groups_id[] = $value;
             }
         }             
         
         $groups_ids = implode(',', $groups_id);
         $sql = 'SELECT * FROM #__jdownloads_usergroups_limits WHERE group_id IN (' . $groups_ids. ')';
         $db->setQuery($sql);
         $jd_user_settings = $db->loadObjectList();

         if (count($jd_user_settings) == 1){
             // user is only in a single group
             // this should be the normal case!
             return $jd_user_settings[0];
         } else {
             // fallback option for special situations !!!
             
             // user is in multi groups
             // so we must get the group with the highest permission levels from the default group: 
             // 1. super users      ID = 8
             // 2. admin            ID = 7
             // 3. manager          ID = 6
             // 4. publisher        ID = 5
             // 5. shop suppliers   ID = 10
             // 6. editor           ID = 4
             // 7. customer group   ID = 12
             // 8. author           ID = 3
             // 9. registered       ID = 2
             // 10 guest            ID = 13 
             // 11 public           ID = 1
             if (in_array('8', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '8');
                 return $jd_user_settings[$key];
             }
             if (in_array('7', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '7');
                 return $jd_user_settings[$key];
             } 
             if (in_array('6', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '6');
                 return $jd_user_settings[$key];
             } 
             if (in_array('5', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '5');
                 return $jd_user_settings[$key];
             } 
             if (in_array('10', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '10');
                 return $jd_user_settings[$key];
             }                                                       
             if (in_array('4', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '4');
                 return $jd_user_settings[$key];
             }
             if (in_array('12', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '12');
                 return $jd_user_settings[$key];
             } 
             if (in_array('3', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '3');
                 return $jd_user_settings[$key];
             } 
             if (in_array('2', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '2');
                 return $jd_user_settings[$key];
             } 
             if (in_array('13', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '13');
                 return $jd_user_settings[$key];
             } 
             if (in_array('1', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '1');
                 return $jd_user_settings[$key];
             }                                                     
         }
         return $jd_user_settings[0];
    }
    

    /*
    * find the correct index value for a given group ID from a array with jD user groups settings 
    *
    * @param mixed $jd_user_settings
    * @param mixed $id
    * @return mixed
    */
    public static function findUserGroupID($jd_user_settings, $id)
    {
        for ($i=0, $n=count($jd_user_settings); $i<$n; $i++){
             if ($jd_user_settings[$i]->group_id == $id){
                 return $i;
             }
        }
        return 0;
    }    
    
    
    /*
    * Get the menu item IDs for the header links (when it exists) 
    *
    * @return array     $jdItemids
    */
    public static function getMenuItemids($catid = 0){
        
        $db     = JFactory::getDBO();
        $user   = JFactory::getUser();
        $access_groups = implode(',', $user->getAuthorisedGroups()); 
        $access_levels = implode(',', $user->getAuthorisedViewLevels()); 
     
        $jdItemids = array();
        $jdItemids['root'] = '';
        
        // search for a jD root menu item
        if ($catid == 0){
            $sql = 'SELECT id FROM #__menu WHERE (link LIKE ' . $db->Quote('index.php?option=com_jdownloads&Itemid='). ' OR link LIKE ' . $db->Quote('index.php?option=com_jdownloads&view=categories'). '  ) AND published = 1 AND access IN ('.$access_levels.')' ;
            $db->setQuery($sql);
            $jdItemids['root'] = $db->loadResult();
        }    
        
        // search for a jD category menu item
        if (!$jdItemids['root'] && $catid > 1){
            $sql = 'SELECT id FROM #__menu WHERE (link = ' . $db->Quote('index.php?option=com_jdownloads&view=category&catid='.$catid). ') AND published = 1 AND access IN ('.$access_levels.')' ;
            $db->setQuery($sql);
            $jdItemids['root'] = $db->loadResult();
        }
        
        // search for a jD list type 'all downloads'
        if (!$jdItemids['root'] && $catid == 0){
            $sql = 'SELECT id FROM #__menu WHERE (link = ' . $db->Quote('index.php?option=com_jdownloads&view=downloads'). ') AND published = 1 AND access IN ('.$access_levels.')' ;
            $db->setQuery($sql);
            $jdItemids['root'] = $db->loadResult();
        }

        // search for a jD list type 'only uncategorisied'
        if (!$jdItemids['root'] && $catid == 0){
                $sql = 'SELECT id FROM #__menu WHERE (link = ' . $db->Quote('index.php?option=com_jdownloads&view=downloads&type=uncategorised'). ') AND published = 1 AND access IN ('.$access_levels.')' ;
                $db->setQuery($sql);
                $jdItemids['root'] = $db->loadResult();
        }        

        // try to use at latest again the early used Itemid
        if (!$jdItemids['root']){
            $jdItemids['root'] = JRequest::getInt('Itemid', 0);
        }    

        $sql = 'SELECT id FROM #__menu WHERE (link LIKE ' . $db->Quote('index.php?option=com_jdownloads&Itemid='). ' OR link LIKE ' . $db->Quote('index.php?option=com_jdownloads&view=categories'). '  ) AND published = 1 AND access IN ('.$access_levels.')' ;
        $db->setQuery($sql);
        $jdItemids['base'] = $db->loadResult();
        if (!$jdItemids['base']){
            $jdItemids['base'] = -1;
        }        
                
        // search for a jD search menu item
        $sql = 'SELECT id FROM #__menu WHERE link = ' . $db->Quote('index.php?option=com_jdownloads&view=search'). ' AND published = 1 AND access IN ('.$access_levels.')' ;
        $db->setQuery($sql);
        $jdItemids['search'] = $db->loadResult();
                
        // search for a jD upload menu item
        $sql = 'SELECT id FROM #__menu WHERE link = ' . $db->Quote('index.php?option=com_jdownloads&view=form&layout=edit'). ' AND published = 1 AND access IN ('.$access_levels.')' ;
        $db->setQuery($sql);
        $jdItemids['upload'] = $db->loadResult();
        
        // get assigned category id
        if ($catid > 0){
            $sql = 'SELECT parent_id FROM #__jdownloads_categories WHERE id = ' . $catid. ' AND published = 1 AND access IN ('.$access_levels.')' ;
            $db->setQuery($sql);
            $jdItemids['upper'] = $db->loadResult();
        } else {
            $jdItemids['upper'] = $jdItemids['base'];
        }    
        
        /*if (!$jdItemids['upper']){
            $jdItemids['upper'] = false;
        } else {
            $jdItemids['upper'] = true;
        } */
        
        return $jdItemids; 
        
    } 
    
    /*
    * Get all jD menu items for a single category
    *
    * @return array     $cat_link_itemids 
    */
    public static function getAllJDCategoryMenuIDs()
    {
        $db = JFactory::getDBO();
        $user   = JFactory::getUser();
       
        $access_groups = implode(',', $user->getAuthorisedGroups()); 
        $access_levels = implode(',', $user->getAuthorisedViewLevels());                  

        $cat_link_itemids = array();
               
        // get all published single category menu links
        $db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=category&catid%' AND published = 1 AND access IN (".$access_levels.')');
        $cat_link_itemids = $db->loadAssocList();
        if ($cat_link_itemids){
            for ($i=0; $i < count($cat_link_itemids); $i++){
                 $cat_link_itemids[$i]['catid'] = substr( strrchr ( $cat_link_itemids[$i]['link'], '=' ), 1);
            }    
        }
        return $cat_link_itemids;
    } 
    
   /*
    * Get a menu item ID for a single jD category
    *
    * @return string     $cat_itemid 
    */
    public static function getSingleCategoryMenuID($cat_link_itemids, $cat_id, $root_itemid)
    {
        $cat_itemid = '';
        
        if ($cat_link_itemids){                 
            $cat_itemids = JDHelper::array_multi_search($cat_id, $cat_link_itemids, 'catid');                
            if ($cat_itemids){
                $cat_itemid = $cat_itemids[0]['id'];
            }
        }
        
        if (!$cat_itemid){
            // use global menu itemid when no single link exists
            $cat_itemid = $root_itemid;
        }
        return $cat_itemid;
    }

    /*
    * Get all jD menu items for a single download
    *
    * @return array     $file_link_itemids 
    */
    public static function getAllJDDownloadMenuIDs()
    {
        $db = JFactory::getDBO();
        $user   = JFactory::getUser();
       
        $access_groups = implode(',', $user->getAuthorisedGroups()); 
        $access_levels = implode(',', $user->getAuthorisedViewLevels());                  

        $file_link_itemids = array();
               
        // get all published single category menu links
        $db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=download&id%' AND published = 1 AND access IN (".$access_levels.')');
        $file_link_itemids = $db->loadAssocList();
        if ($file_link_itemids){
            for ($i=0; $i < count($file_link_itemids); $i++){
                 $file_link_itemids[$i][file_id] = substr( strrchr ( $file_link_itemids[$i][link], '=' ), 1);
            }    
        }
        return $file_link_itemids;
    }       

   /*
    * Get a menu item ID for a single jD download
    *
    * @return string     $file_itemid 
    */
    public static function getSingleDownloadMenuID($file_link_itemids, $file_id, $root_itemid)
    {
        $file_itemid = '';
        
        if ($file_link_itemids){                 
            $file_itemids = JDHelper::array_multi_search($file_id, $file_link_itemids, 'file_id');                
            if ($file_itemids){
                $file_itemid = $file_itemids[0]['id'];
            }
        }
        
        return $file_itemid;
    } 
    
        
    /*
    * Read the activated layout for the frontend output
    * 
    * @param    int        The layout type
    * @param    boolean    When true is tried to get a categories layout with this setting = on / the layout must then not be activated
    *
    * @return layout text
    */
    public static function getLayout($type = 0, $use_to_view_subcats = false){
        
        global $jlistConfig;
        
        $db = JFactory::getDBO(); 
        $layout = '';
        
        if (!$use_to_view_subcats){
            $db->setQuery("SELECT * FROM #__jdownloads_templates WHERE template_typ = '".(int)$type."' AND template_active = '1'");
            $layout = $db->loadObject(); 
        } else {
            if ($type == (int)1 && $jlistConfig['use.pagination.subcategories']){
                $db->setQuery("SELECT * FROM #__jdownloads_templates WHERE template_typ = '".(int)$type."' AND use_to_view_subcats = '1'");
                $layout = $db->loadObject(); 
            }
            if (!$layout){
                $db->setQuery("SELECT * FROM #__jdownloads_templates WHERE template_typ = '".(int)$type."' AND template_active = '1'");
                $layout = $db->loadObject(); 
            }
        }    
        
        if (!$db->execute()) {
            //JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_DB_ERROR'), 'error');
            return false;
        }
        
        if (isset($layout)) {
            return $layout;
        }
        return '';
    }
    
    /**
     * Gets a list of the actions that can be performed.
     *
     * @param    int        The category ID.
     * @param    int        The download ID.
     *
     * @return    JObject
     */
    public static function getActions($categoryId = 0, $articleId = 0)
    {
        // Reverted a change for version 2.5.6
        $user    = JFactory::getUser();
        $result  = new JObject;

        if (empty($articleId) && empty($categoryId)) {
            $assetName = 'com_jdownloads';
        }
        elseif (empty($articleId)) {
            $assetName = 'com_jdownloads.category.'.(int) $categoryId;
        }
        else {
            $assetName = 'com_jdownloads.article.'.(int) $articleId;
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete', 'download', 'upload', 
        );

        foreach ($actions as $action) {
            $result->set($action,  $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
    * Applies the content tag filters to arbitrary text as per settings for current user group
    * @param text The string to filter
    * @return string The filtered string
    */
    public static function filterText($text)
    {
        // Filter settings
        $config         = JComponentHelper::getParams('com_config');
        $user           = JFactory::getUser();
        $userGroups     = JAccess::getGroupsByUser($user->get('id'));

        $filters = $config->get('filters');

        $blackListTags          = array();
        $blackListAttributes    = array();

        $customListTags         = array();
        $customListAttributes   = array();

        $whiteListTags          = array();
        $whiteListAttributes    = array();

        $noHtml                 = false;
        $whiteList              = false;
        $blackList              = false;
        $customList             = false;
        $unfiltered             = false;

        // Cycle through each of the user groups the user is in.
        // Remember they are included in the Public group as well.
        foreach ($userGroups as $groupId)
        {
            // May have added a group but not saved the filters.
            if (!isset($filters->$groupId)) {
                continue;
            }

            // Each group the user is in could have different filtering properties.
            $filterData = $filters->$groupId;
            $filterType    = strtoupper($filterData->filter_type);

            if ($filterType == 'NH') {
                // Maximum HTML filtering.
                $noHtml = true;
            }
            elseif ($filterType == 'NONE') {
                // No HTML filtering.
                $unfiltered = true;
            }
            else {
                // Black, white or custom list.
                // Preprocess the tags and attributes.
                $tags               = explode(',', $filterData->filter_tags);
                $attributes         = explode(',', $filterData->filter_attributes);
                $tempTags           = array();
                $tempAttributes     = array();

                foreach ($tags as $tag)
                {
                    $tag = trim($tag);

                    if ($tag) {
                        $tempTags[] = $tag;
                    }
                }

                foreach ($attributes as $attribute)
                {
                    $attribute = trim($attribute);

                    if ($attribute) {
                        $tempAttributes[] = $attribute;
                    }
                }

                // Collect the black or white list tags and attributes.
                // Each lists is cummulative.
                if ($filterType == 'BL') {
                    $blackList                = true;
                    $blackListTags            = array_merge($blackListTags, $tempTags);
                    $blackListAttributes      = array_merge($blackListAttributes, $tempAttributes);
                }
                elseif ($filterType == 'CBL') {
                    // Only set to true if Tags or Attributes were added
                    if ($tempTags || $tempAttributes) {
                        $customList                = true;
                        $customListTags            = array_merge($customListTags, $tempTags);
                        $customListAttributes    = array_merge($customListAttributes, $tempAttributes);
                    }
                }
                elseif ($filterType == 'WL') {
                    $whiteList                = true;
                    $whiteListTags            = array_merge($whiteListTags, $tempTags);
                    $whiteListAttributes    = array_merge($whiteListAttributes, $tempAttributes);
                }
            }
        }

        // Remove duplicates before processing (because the black list uses both sets of arrays).
        $blackListTags          = array_unique($blackListTags);
        $blackListAttributes    = array_unique($blackListAttributes);
        $customListTags         = array_unique($customListTags);
        $customListAttributes   = array_unique($customListAttributes);
        $whiteListTags          = array_unique($whiteListTags);
        $whiteListAttributes    = array_unique($whiteListAttributes);

        // Unfiltered assumes first priority.
        if ($unfiltered) {
            // Dont apply filtering.
        }
        else {
            // Custom blacklist precedes Default blacklist
            if ($customList) {
                $filter = JFilterInput::getInstance(array(), array(), 1, 1);

                // Override filter's default blacklist tags and attributes
                if ($customListTags) {
                    $filter->tagBlacklist = $customListTags;
                }
                if ($customListAttributes) {
                    $filter->attrBlacklist = $customListAttributes;
                }
            }
            // Black lists take third precedence.
            elseif ($blackList) {
                // Remove the white-listed attributes from the black-list.
                $filter = JFilterInput::getInstance(
                    array_diff($blackListTags, $whiteListTags),             // blacklisted tags
                    array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
                    1,                                                      // blacklist tags
                    1                                                       // blacklist attributes
                );
                // Remove white listed tags from filter's default blacklist
                if ($whiteListTags) {
                    $filter->tagBlacklist = array_diff($filter->tagBlacklist, $whiteListTags);
                }
                // Remove white listed attributes from filter's default blacklist
                if ($whiteListAttributes) {
                    $filter->attrBlacklist = array_diff($filter->attrBlacklist);
                }

            }
            // White lists take fourth precedence.
            elseif ($whiteList) {
                $filter    = JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
            }
            // No HTML takes last place.
            else {
                $filter = JFilterInput::getInstance();
            }

            $text = $filter->clean($text, 'html');
        }

        return $text;
    }
    
    public static function array_multi_search($mSearch, $aArray, $sKey = "")
    {
        $aResult = array();
        foreach( (array) $aArray as $aValues)
        {
            if($sKey === "" && in_array($mSearch, $aValues)){
               $aResult[] = $aValues;
            } else {
                if(isset($aValues[$sKey]) && $aValues[$sKey] == $mSearch){
                   $aResult[] = $aValues;
                }
            }       
        }
        return $aResult;
    }
    
    public static function isPlayable($filename)
    {
        $ext = self::getFileExtension($filename);
        
        switch($ext) {
            case 'mp3':  // audio
            case 'mp4':  // video
            case 'flv':  // video 
            case 'ogg':  // audio OR video?
            case 'oga':  // audio
            case 'ogv':  // video
            case 'wav':  // audio
            case 'webm': // video
                return true;
                break;
            
            default:
                return false;
                break;
        }

        return false;
    }
    
    /**
    * Create the data to get a valid HTML5-Player for audio or video
    * 
    * @param mixed $html5player
    */
    public static function getHTML5Player($file, $media_path)
    {
        global $jlistConfig;
        
        if (!$file->itemtype) return ''; 

        $player = '';
        
        switch($file->itemtype){
            // audio formats
            case 'mp3' :
                    $playertype = 'audio';
                    $mediatype  = 'type="audio/mpeg"';
                    $playerwidth  = 'style="width:'.(int)$jlistConfig['html5player.audio.width'].'px;" ';
                    $playerheight = '';
            break;        

            case 'wav' :
                    $playertype = 'audio';
                    $mediatype  = 'type="audio/wav"';
                    $playerwidth  = 'style="width:'.(int)$jlistConfig['html5player.audio.width'].'px;" ';
                    $playerheight = '';
            break;

            case 'oga' :
                    $playertype = 'audio';
                    $mediatype  = 'type="audio/ogg"';
                    $playerwidth  = 'style="width:'.(int)$jlistConfig['html5player.audio.width'].'px;" ';
                    $playerheight = '';
            break; 

            // video formats
            // an ogg file can be an audio or a video file - so we must use it always as video, as we can not find out what is it really                       
            case 'ogg' :
                    $playertype = 'video';
                    $mediatype  = 'type="video/ogg"';
                    $playerwidth  = 'width="'.(int)$jlistConfig['html5player.width'].'" ';
                    $playerheight = 'height="'.(int)$jlistConfig['html5player.height'].'" ';
            break; 
            
            case 'ogv' :
                    $playertype = 'video';
                    $mediatype  = 'type="video/ogg"';
                    $playerwidth  = 'width="'.(int)$jlistConfig['html5player.width'].'" ';
                    $playerheight = 'height="'.(int)$jlistConfig['html5player.height'].'" ';
            break; 

            case 'mp4' :
                    $playertype   = 'video';
                    $mediatype    = 'type="video/mp4"';
                    $playerwidth  = 'width="'.(int)$jlistConfig['html5player.width'].'" ';
                    $playerheight = 'height="'.(int)$jlistConfig['html5player.height'].'" ';
            break;
             
            case 'webm' :
                    $playertype   = 'video';
                    $mediatype    = 'type="video/webm"';
                    $playerwidth  = 'width="'.(int)$jlistConfig['html5player.width'].'" ';
                    $playerheight = 'height="'.(int)$jlistConfig['html5player.height'].'" ';
            break;
        }
       
        if ($playertype == 'video'){
            $player = '<'.$playertype.' style="width: 100%;" '.'controls>';
        } else {
            $player = '<'.$playertype.' style="width:'.(int)$jlistConfig['html5player.audio.width'].'px; "'.' controls>';
        }
            
        $player .= '<source src="'.$media_path.'" '.$mediatype.">";
        $player .= JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_NOT_SUPPORTED_MSG');
        $player .= '</'.$playertype.'>';
        
        if ($playertype == 'video'){
            $player_maxwidth  = 'max-width:'.(int)$jlistConfig['html5player.width'].'px; ';
            $player_maxheight = 'max-height:'.(int)$jlistConfig['html5player.height'].'px; '; 
            $player = '<div class="jd_video_wrapper" style="'.$player_maxwidth.' '.$player_maxheight.'"><div class="jd_video_container">'.$player.'</div></div>';
        }
        
        return $player;   
    }    
    
    /**
     * Returns an array of the categories 
     *
     * @param   boolean     $show_empty_categories  the selected value in menu item or configuration
     *          string      $orderby_pri            the primary sort order
     *
     * @return  array
     *
     */
    public static function getCategoriesList($show_empty_categories, $orderby_pri = '')
    {
        global $jlistConfig;
        
        // use default sort order or menu order settings
        if (empty($orderby_pri) || !isset($orderby_pri)){
            // use config settings
            switch ($jlistConfig['cats.order']){
                case '1':
                     // files title field asc 
                     $orderCol = 'c.title ';
                     $categoryOrderby = 'alpha';
                     break;
                case '2':
                     // files title field desc 
                     $orderCol = 'c.title DESC ';
                     $categoryOrderby = 'ralpha';
                     break;
                default:
                     // files ordering field
                     $orderCol = 'c.lft ';
                     $categoryOrderby = '';
                     break;                
            }
        }  else {
            // use order from menu settings 
            $categoryOrderby    = $orderby_pri;
            $orderCol           = str_replace(', ', '', JDContentHelperQuery::orderbyPrimary($categoryOrderby));
        }  

        $user = JFactory::getUser();
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('c.*, c.parent_id AS parent');
        $query->from('#__jdownloads_categories AS c');
        $query->where('c.parent_id > 0');
        $query->where('c.published = 1');
        $query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

        // The number of child category levels
        $levels = (int)$jlistConfig['show.header.catlist.levels'];
        if ($levels > 0){
            $query->where('c.level BETWEEN 1 AND '.$levels);
        }         
        
        $query->leftJoin($db->quoteName('#__jdownloads_files') . ' AS files ON files.cat_id = c.id AND files.published = 1');
        $query->select('COUNT(files.' . $db->quoteName('file_id') . ') as numitems ');
        
        $query->group('c.id, c.title, c.cat_dir_parent');
        
        if ($categoryOrderby == 'alpha'){
            $query->order('c.level ASC, c.parent_id ASC, c.title ASC');
        } elseif ($categoryOrderby == 'ralpha'){
            $query->order('c.level ASC, c.parent_id ASC, c.title DESC');
        } else {
            $query->order('c.lft');
        }  

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        // use selected value for 'view empty category' option
        if (!$show_empty_categories){ 
            foreach ($rows as &$row){
                if ($row->numitems > 0){            
                    $cats[] = $row; 
                }
            } 
        } else {
            $cats = $rows;
        } 

        // Order subcategories
        if (count($cats)) {
            if ($categoryOrderby == 'alpha' || $categoryOrderby == 'ralpha') {
                $i = 0;
                $depth = 0;
                $parent_id = 0;
                $parents = array();
                
                foreach($cats as $cat) {
                    if($depth < $cat->level || $parent_id < $cat->parent_id) {
                        $i = @$parents["{$cat->parent_id}"] + 1;
                    }
                    $tree[$i] = $cat;
                    $parents["{$cat->id}"] = $i;
                    $depth = $cat->level;
                    $parent_id = $cat->parent_id;
                    $i += (($cat->rgt - $cat->lft - 1) / 2) + 1;
                }
                ksort($tree);
                $cats = $tree;
            }
        }        

        foreach ($cats as &$cat){
            $repeat = ($cat->level - 1 >= 0) ? $cat->level - 1 : 0;
            $cat->title = str_repeat('- ', $repeat) . $cat->title;
        }
        
        return $cats;       
    }

    /**
     * Returns an object with data from a single category 
     *
     * @param   integer     category ID
     *
     * @return  object
     *
     */
    public static function getSingleCategory($cat_id)
    {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__jdownloads_categories AS a');
        $query->where('a.id = '.$db->quote($cat_id));
        $query->where('a.published = 1');
        $db->setQuery($query);
        $row = $db->loadObject();
        return $row;
    }    
    
    
    /*  Build categories list box for header
     *
     *  @param integer   $catlistid             the get current selected cat id from listbox
     *         array     $cat_link_itemids      menu Itemid for every category (when exist - otherwise the id from root menu
     *         integer   $root_itemid           the menu Itemid from the jDownloads main menu item
     *         boolean   $show_empty_categories whether we shall view also empty categories (=1) or not (=0)
    */
    public static function buildCategorySelectBox($catlistid, $cat_link_itemids, $root_itemid, $show_empty_categories, $orderby_pri)
    {
        global $jlistConfig;
        
        $db         = JFactory::getDBO(); 
        $user       = JFactory::getUser();
        $preload    = array();
        $selected   = array();
        $root_url   = '';
        $url        = array();
        $data       = array();
        $catx_itemid = '';
        
        $preload[] = JHtml::_('select.option', 0, JText::_('COM_JDOWNLOADS_FE_SELECT_OVERVIEW'));
        if ($jlistConfig['show.header.catlist.uncategorised']){
            $preload[] = JHtml::_('select.option', 1, JText::_('COM_JDOWNLOADS_FE_SELECT_UNCATEGORISED'));
        }    
        if ($jlistConfig['show.header.catlist.all']){
            $preload[] = JHtml::_('select.option', -1, JText::_('COM_JDOWNLOADS_FE_SELECT_ALL_DOWNLOADS'));
        }    
        if ($jlistConfig['show.header.catlist.newfiles']){
                $preload[] = JHtml::_('select.option', -2, JText::_('COM_JDOWNLOADS_SELECT_NEWEST_DOWNLOADS'));
        }        
        if ($jlistConfig['show.header.catlist.topfiles']){
            $preload[] = JHtml::_('select.option', -3, JText::_('COM_JDOWNLOADS_SELECT_HOTTEST_DOWNLOADS'));
        }    
                                          
        $selected[] = JHtml::_('select.option', $catlistid );
        
        // get the categories data
        $categories_list = self::getCategoriesList( $show_empty_categories, $orderby_pri );
        $categories = @array_merge($preload, $categories_list); 
                   
        
        foreach($categories as $key=>$value){
            if (isset($value->value)){
                $options[] = JHtml::_('select.option', $value->value, JText::_($value->text));
            } else {
                $options[] = JHtml::_('select.option', $value->id, JText::_($value->title));
            }    
        }

        // get  
        $query = $db->getQuery(true);
        $query->select('a.id, a.parent_id as parent, a.title');
        $query->from('#__jdownloads_categories AS a');
        $query->where('a.parent_id > 0');
        $query->where('a.published = 1');
        $query->where('a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
        $query->order('a.id');
        $db->setQuery( $query );
        $src_for_url_list = $db->loadObjectList();

        if ($src_for_url_list){
            $max_cat_id = $src_for_url_list[count($src_for_url_list)-1]->id;
        } else {
            $max_cat_id = 0;
        }    
        $x = 0;
        
        // create array with all sef url's for listbox
        for ($i=0; $i < $max_cat_id; $i++){ 
            if ($src_for_url_list[$x]->id == ($i+1)){
                // exists a single category menu link for it? 
                if ($cat_link_itemids){  
                    $catx_itemid = '';
                    for ($i2=0; $i2 < count($cat_link_itemids); $i2++) {
                         if ($cat_link_itemids[$i2]['catid'] == $src_for_url_list[$x]->id){
                             $catx_itemid = $cat_link_itemids[$i2]['id'];
                         }     
                    }
                }    
                if (!$catx_itemid){
                    // use global itemid when no single link exists
                    $catx_itemid = $root_itemid;
                }                
                
                $url[$src_for_url_list[$x]->id] = jRoute::_("index.php?option=com_jdownloads&view=category&catid=".$src_for_url_list[$x]->id.'&Itemid='.$catx_itemid);
                $x++;
            } else {
                $url[$i+1] = 'null';
            }    
        }            
        
        $data['url']     = implode(',',$url);
        $data['options'] = $options;
        $data['selected'] = $selected;                                     
        return $data;
    }
    
    /*
     * 
     * 
    */
    public static function existsCustomFieldsTitles()
    {
        global $jlistConfig;
    
        // check that any field is activated (has title)
        $custom_arr = array();
        $custom_array = array();
        $custom_titles = array();
        $custom_values = array();

        for ($i=1; $i<15; $i++){
            if ($jlistConfig["custom.field.$i.title"] != ''){
               $custom_array[] = $i;
               $custom_titles[] = $jlistConfig["custom.field.$i.title"];
               if(isset($jlistConfig["custom.field.$i.values"])) {
                  $custom_values[] = explode(',', $jlistConfig["custom.field.$i.values"]);   
                  array_unshift($custom_values[$i-1],"select");
               }
               
            } else {
               $custom_array[] = 0;
               $custom_titles[] = '';
               $custom_values[] = '';
            }   
        }    
        $custom_arr[]=$custom_array;
        $custom_arr[]=$custom_titles;
        $custom_arr[]=$custom_values;

        return $custom_arr;
    }    
    
    
    /*
     * 
     * 
    */
    public static function buildFieldTitles($html, $file)
    {
        global $jlistConfig;
        
        // required for plugin
        if (!isset($jlistConfig)){
            $jlistConfig = self::buildjlistConfig();
        }        
        
        if ($jlistConfig['remove.field.title.when.empty']){
            $html = ($file->license) ? str_replace('{license_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_LICENSE_TITLE'), $html) : str_replace('{license_title}', '', $html);
            $html = ($file->price) ? str_replace('{price_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_PRICE_TITLE'), $html) : str_replace('{price_title}', '', $html);                                          
            $html = ($file->file_language) ? str_replace('{language_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_LANGUAGE_TITLE'), $html) : str_replace('{language_title}', '', $html);
            $html = ($file->size) ? str_replace('{filesize_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE'), $html) : str_replace('{filesize_title}', '', $html);
            $html = ($file->system) ? str_replace('{system_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_SYSTEM_TITLE'), $html) : str_replace('{system_title}', '', $html);
            $html = ($file->author) ? str_replace('{author_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_AUTHOR_TITLE'), $html) : str_replace('{author_title}', '', $html);
            $html = ($file->url_home) ? str_replace('{author_url_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_AUTHOR_URL_TITLE'), $html) : str_replace('{author_url_title}', '', $html);
            $html = ($file->date_added != '0000-00-00 00:00:00') ? str_replace('{created_date_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_CREATED_DATE_TITLE'), $html) : str_replace('{created_date_title}', '', $html);
            $html = ($file->downloads != '') ? str_replace('{hits_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_HITS_TITLE'), $html) : str_replace('{hits_title}', '', $html);
            $html = ($file->created_id) ? str_replace('{created_by_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_CREATED_BY_TITLE'), $html) : str_replace('{created_by_title}', '', $html);
            $html = ($file->modified_id) ? str_replace('{modified_by_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_MODIFIED_BY_TITLE'), $html) : str_replace('{modified_by_title}', '', $html);
            $html = ($file->modified != '0000-00-00 00:00:00') ? str_replace('{modified_date_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_MODIFIED_DATE_TITLE'), $html) : str_replace('{modified_date_title}', '', $html);
            $html = ($file->file_date != '0000-00-00 00:00:00') ? str_replace('{file_date_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_FILE_DATE_TITLE'), $html) : str_replace('{file_date_title}', '', $html);
            $html = ($file->url_download) ? str_replace('{file_name_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_FILE_NAME_TITLE'), $html) : str_replace('{file_name_title}', '', $html);          
            $html = ($file->views) ? str_replace('{views_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_VIEWS_TITLE'), $html) : str_replace('{views_title}', '', $html);          
            $html = ($file->changelog) ? str_replace('{changelog_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_CHANGELOG_TITLE'), $html) : str_replace('{changelog_title}', '', $html);
            $html = ($file->md5_value) ? str_replace('{md5_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_MD5_TITLE'), $html) : str_replace('{md5_title}', '', $html);          
            $html = ($file->sha1_value) ? str_replace('{sha1_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_SHA1_TITLE'), $html) : str_replace('{sha1_title}', '', $html);                    
            $html = ($file->release) ? str_replace('{release_title}', JText::_('COM_JDOWNLOADS_FRONTEND_VERSION_TITLE'), $html) : str_replace('{release_title}', '', $html);                    
        } else {    
            $html = str_replace('{license_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_LICENSE_TITLE'), $html);
            $html = str_replace('{price_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_PRICE_TITLE'), $html);
            $html = str_replace('{language_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_LANGUAGE_TITLE'), $html);
            $html = str_replace('{filesize_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE'), $html);
            $html = str_replace('{system_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_SYSTEM_TITLE'), $html);
            $html = str_replace('{author_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_AUTHOR_TITLE'), $html);
            $html = str_replace('{author_url_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_AUTHOR_URL_TITLE'), $html);
            $html = str_replace('{created_date_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_CREATED_DATE_TITLE'), $html);
            $html = str_replace('{hits_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_HITS_TITLE'), $html);
            $html = str_replace('{created_by_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_CREATED_BY_TITLE'), $html);
            $html = str_replace('{modified_by_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_MODIFIED_BY_TITLE'), $html);
            $html = str_replace('{modified_date_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_MODIFIED_DATE_TITLE'), $html);
            $html = str_replace('{file_date_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_FILE_DATE_TITLE'), $html);
            $html = str_replace('{file_name_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_FILE_NAME_TITLE'), $html);   
            $html = str_replace('{views_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_VIEWS_TITLE'), $html);
            $html = str_replace('{changelog_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_CHANGELOG_TITLE'), $html);
            $html = str_replace('{md5_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_MD5_TITLE'), $html);
            $html = str_replace('{sha1_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_SHA1_TITLE'), $html);
            $html = str_replace('{release_title}', JText::_('COM_JDOWNLOADS_FRONTEND_VERSION_TITLE'), $html);
        }
        return $html;
    }

    /*
     * 
     * 
    */
    public static function getRatings($id, $count = 0, $sum = 0)
    {    
        global $jlistConfig;

        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid = max ($user->getAuthorisedViewLevels());
        $document = JFactory::getDocument();
        $html = '';
         

        if ($count != 0 && $sum != 0){
            $result = number_format(intval($sum) / intval( $count ),2)*20;
        } else {
            $result = 0;
        }   
        $rating_sum = intval($sum);
        $rating_count = intval($count);
        // rating only for registered?
        if (($jlistConfig['rating.only.for.regged'] && $aid > 1) || !$jlistConfig['rating.only.for.regged']) {
            $script='
            <script type="text/javascript">
            var live_site = \''.JURI::base().'\';
            var jwajaxvote_lang = new Array();
            jwajaxvote_lang[\'UPDATING\'] = \''.JText::_('COM_JDOWNLOADS_JDVOTE_UPDATING').'\';
            jwajaxvote_lang[\'THANKS\'] = \''.JText::_('COM_JDOWNLOADS_JDVOTE_THANKS').'\';
            jwajaxvote_lang[\'ALREADY_VOTE\'] = \''.JText::_('COM_JDOWNLOADS_JDVOTE_ALREADY_VOTE').'\';
            jwajaxvote_lang[\'VOTES\'] = \''.JText::_('COM_JDOWNLOADS_JDVOTE_VOTES').'\';
            jwajaxvote_lang[\'VOTE\'] = \''.JText::_('COM_JDOWNLOADS_JDVOTE_VOTE').'\';
            </script>
            <script type="text/javascript" src="'.JUri::base().'components/com_jdownloads/assets/rating/js/ajaxvote.php"></script>
            ';    
            if(!isset($addScriptJWAjaxVote)){ 
                $addScriptJWAjaxVote = 1;
                if($app->getCfg('caching') > 0) {
                    $html = $script;
                } else {
                    $document->addCustomTag($script);
                }
            }        

            $html .='
            <div class="jwajaxvote-inline-rating">
            <ul class="jwajaxvote-star-rating">
            <li id="rating'.$id.'" class="current-rating" style="width:'.$result.'%;"></li>
            <li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote('.$id.',1,'.$rating_sum.','.$rating_count.');" title="1 '.JText::_('COM_JDOWNLOADS_JDVOTE_STAR').' 5" class="one-star"></a></li>
            <li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote('.$id.',2,'.$rating_sum.','.$rating_count.');" title="2 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="two-stars"></a></li>
            <li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote('.$id.',3,'.$rating_sum.','.$rating_count.');" title="3 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="three-stars"></a></li>
            <li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote('.$id.',4,'.$rating_sum.','.$rating_count.');" title="4 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="four-stars"></a></li>
            <li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote('.$id.',5,'.$rating_sum.','.$rating_count.');" title="5 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="five-stars"></a></li>
            </ul>
            <div id="jwajaxvote'.$id.'" class="jwajaxvote-box">
            ';
        } else {
            // view only the results
            $html .='
            <div class="jwajaxvote-inline-rating">
            <ul class="jwajaxvote-star-rating">
            <li id="rating'.$id.'" class="current-rating" style="width:'.$result.'%;"></li>
            <li><a href="javascript:void(null)" onclick="" title="1 '.JText::_('COM_JDOWNLOADS_JDVOTE_STAR').' 5" class="one-star"></a></li>
            <li><a href="javascript:void(null)" onclick="" title="2 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="two-stars"></a></li>
            <li><a href="javascript:void(null)" onclick="" title="3 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="three-stars"></a></li>
            <li><a href="javascript:void(null)" onclick="" title="4 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="four-stars"></a></li>
            <li><a href="javascript:void(null)" onclick="" title="5 '.JText::_('COM_JDOWNLOADS_JDVOTE_STARS').' 5" class="five-stars"></a></li>
            </ul>
            <div id="jwajaxvote'.$id.'" class="jwajaxvote-box">
            ';
        }
        if($rating_count!=1) {
           $html .= "(".$rating_count." ".JText::_('COM_JDOWNLOADS_JDVOTE_VOTES').")";
        } else { 
           $html .= "(".$rating_count." ".JText::_('COM_JDOWNLOADS_JDVOTE_VOTE').")";
        }
        $html .= '
            </div>
            </div>
            <div class="jwajaxvote-clr"></div>'; 
        return $html;       
    }         
    
    /* Verify whether a user is a member from a given group.
     * 
     * @param    integer    $group        The ID from the user group
     * @param    boolean    $inherited    
     * 
     * @return    Boolean    True if user is a member, 0 otherwise.
    */
    public static function checkGroup($group, $inherited)
    {
       $user = JFactory::getUser();
       $user_id = $user->get('id');
       if ($inherited){
          //include inherited groups
          jimport( 'joomla.access.access' );
          $groups = JAccess::getGroupsByUser($user_id);
       }
       else {
          //exclude inherited groups
          $user = JFactory::getUser($user_id);
          $groups = isset($user->groups) ? $user->groups : array();
       }
       return (in_array($group, $groups)) ? true : 0;
    }
    
    /*
     * 
     * 
    */
    public static function computeDateDifference($start, $end)
    {
        $day1   =(int) substr($start, 8, 2);
        $month1 =(int) substr($start, 5, 2);
        $year1  =(int) substr($start, 0, 4);
        
        $day2   =(int) substr($end, 8, 2);
        $month2 =(int) substr($end, 5, 2);
        $year2  =(int) substr($end, 0, 4);

        if (checkdate($month1, $day1, $year1)and checkdate($month2, $day2, $year2)){
            $date1 = mktime(0,0,0,$month1, $day1, $year1);
            $date2 = mktime(0,0,0,$month2, $day2, $year2);

            $diff=(Integer) (($date1 - $date2)/3600/24);
            return $diff;
        } else {
            return -1;
        }
    }

    /**
    *  Remove all defined 'empty' html tags from a text. 
    * 
    * @param string     $string 
    * @result string    cleaned text
    */
    public static function removeEmptyTags($string)
    {
        // define which tags shall be checked
        $tags = array('p', 'strong', 'b', 'tr', 'td', 'div', 'span', 'small', 'br' );
        
        if (!is_string ($string) || trim ($string) == '')
            return $string;
        
        $p_o_tag = '<('.implode('|', $tags).')(?:\s[^>]*[^\/])?';
        $p_o_tag_short_tag = '\/>';
        $p_o_tag_long_tag = '>';
        $p_empty = '(?:&nbsp;|\x00|\xa0|\s)*';
        $p_cl_tag = '<\/\\1>';
        $pattern =
            $p_o_tag
            .'(?:'.$p_o_tag_short_tag
            .'|'.$p_o_tag_long_tag.$p_empty.$p_cl_tag.')';
        while ( $string != ($val = preg_replace('/'.$pattern.'/iS', '', $string)) && is_string($val)) {
                $string = $val;
        }
        return $string;
    } 
    
    /* Build the ID3Tags information from the file 
     * 
     * 
    */
    public static function getID3v2Tags($file, $blnAllFrames=0)
    {
        if (is_file($file)){
            $arrTag['_file'] = $file;
            $fp = fopen($file,"rb");
            if($fp){
                $id3v2 = fread($fp,3);
                if($id3v2 == "ID3"){ // a ID3v2 tag always starts with 'ID3'
                    $arrTag['_ID3v2'] = 1;
                    $arrTag['_version'] = ord(fread($fp,1)).".".ord(fread($fp,1)); // = version.revision
                    fseek($fp,6); // skip 1 'flag' byte, because i don't need it :)
                    $tagSize = '';
                    for ($i=0; $i<4; $i++){
                        $tagSize = $tagSize.base_convert(ord(fread($fp,1)),10,16);
                    }
                    $tagSize = hexdec($tagSize);
                    if ($tagSize > filesize($file)){
                        $arrTag['_error'] = 4;  // = tag is bigger than file
                    }
                    fseek($fp, 10);
                    
                    while (preg_match("/^[A-Z][A-Z0-9]{3}$/", $frameName = fread($fp,4))){
                        $frameSize = '';
                        for ($i=0; $i<4; $i++){
                            $frameSize = $frameSize.base_convert(ord(fread($fp,1)),10,16);
                        }
                        $frameSize = hexdec($frameSize);
                        if ($frameSize > $tagSize){
                            $arrTag['_error'] = 5; // = frame is bigger than tag
                            break;
                        }
                        fseek ($fp, ftell($fp)+2); // skip 2 'flag' bytes, because i don't need them :)
                        if ($frameSize < 1){
                            $arrTag['_error'] = 6; // = frame size is smaller then 1
                            break;
                        }
                        if ($blnAllFrames == 0){
                            if (!preg_match("/^T/",$frameName)){ // = not a text frame, they always starts with 'T'
                                unset ($arrTag[$frameName]);
                                fseek($fp, ftell($fp) + $frameSize); // go to next frame
                                continue; // read next frame
                            }
                        }
                        $frameContent = fread($fp, $frameSize);
                        if (!isset($arrTag[$frameName])){
                            $arrTag[$frameName] = trim(utf8_encode($frameContent)); // the frame content (always?) starts with 0, so it's better to remove it
                        } else {
                            $arrTag[$frameName] = $arrTag[$frameName]."~".trim($frameContent);
                        }
                    }
                } else {
                    $arrTag['_ID3v2']   =   0;// = no ID3v2 tag found
                    $arrTag['_error']   =   3;// = no ID3v2 tag found
                    $arrTag['_version'] =   0;
                    $arrTag['TLEN']     =   '';
                    $arrTag['TALB']     =   '';
                    $arrTag['TPE1']     =   '';
                    $arrTag['TCON']     =   '';
                    $arrTag['TYER']     =   '';
                }
            } else {
                $arrTag['_error'] = 2;  // can't open file
            }
            fclose($fp);
        } else {
            $arrTag['_error'] = 1;  // = file doesn't exists or isn't a mp3
        }
        // convert lenght
        if (isset($arrTag['TLEN'])){
            if ($arrTag['TLEN'] > 0){
                $arrTag['TLEN'] = round(($arrTag['TLEN'] / 1000)/60,2);
            }
        }    
        if (!isset($arrTag['TLEN'])) $arrTag['TLEN'] = '';
        if (!isset($arrTag['TALB'])) $arrTag['TALB'] = '';
        if (!isset($arrTag['TPE1'])) $arrTag['TPE1'] = '';
        if (!isset($arrTag['TCON'])) $arrTag['TCON'] = '';
        if (!isset($arrTag['TYER'])) $arrTag['TYER'] = '';
        return $arrTag;
    }
    
    /* Place the thumbnails in the layout  
    *
    * @param mixed $body
    * @param mixed $images
    * @param mixed $type
    */
    public static function placeThumbs($body, $images, $type = 'list')
    {
        global $jlistConfig;
        
        // required for plugin
        if (!isset($jlistConfig)){
            $jlistConfig = self::buildjlistConfig();
        }

        $x = '';
 
        if ($images){ 
        
            $images = explode('|', $images);
            
            foreach ($images as $image){
                $thumbnail =  JUri::base().'images/jdownloads/screenshots/thumbnails/'.$image; 
                $screenshot = JUri::base().'images/jdownloads/screenshots/'.$image; 
                $body = str_replace("{thumbnail$x}", $thumbnail, $body);
                $body = str_replace("{screenshot$x}", $screenshot, $body);
                $body = str_replace("{screenshot_end$x}", '', $body);
                $body = str_replace("{screenshot_begin$x}", '', $body);
                if (!$x){
                    $x = 2;
                } else {
                    $x ++;
                }
            }
            
        } else {
            $thumbnail = JUri::base().'images/jdownloads/screenshots/thumbnails/no_pic.gif';
            $screenshot = JUri::base().'images/jdownloads/screenshots/no_pic.gif';
            if ($type == 'list'){
                if ($jlistConfig["thumbnail.view.placeholder.in.lists"]) {
                    $body = str_replace("{thumbnail$x}", $thumbnail, $body);
                    $body = str_replace("{screenshot$x}", $screenshot, $body);
                    $body = str_replace("{screenshot_end$x}", '', $body);
                    $body = str_replace("{screenshot_begin$x}", '', $body);
                    $x ++;                
                }
            } else {
                // type must be 'detail' (page)
                if ($jlistConfig["thumbnail.view.placeholder"]) {
                    $body = str_replace("{thumbnail$x}", $thumbnail, $body);
                    $body = str_replace("{screenshot$x}", $screenshot, $body);
                    $body = str_replace("{screenshot_end$x}", '', $body);
                    $body = str_replace("{screenshot_begin$x}", '', $body);
                    $x ++;                
                }
            }
        }  
        
        // remove now all not used image placeholders from layout
        for ($x=1; $x <= $jlistConfig['be.upload.amount.of.pictures']; $x++){
            if ($x == 1){
                $b = '';
                $length = 16; //  for the first image (no number) 
            } elseif ($x < 10) {
                $b = $x;
                $length = 17; //  for image number 2-9
            } else {
                $b = $x;
                $length = 18; //  for image number 10-99
            }
            $pos_end = strpos($body, "{screenshot_end$b}");
            $pos_beg = strpos($body, "{screenshot_begin$b}");
            if ($pos_beg && $pos_end){     
                 $body = substr_replace($body, '', $pos_beg, ($pos_end - $pos_beg) + $length);
            }
        }
        
        // remove also this simple placeholders from older layouts
        $body = str_replace('{screenshot}', '', $body);
        $body = str_replace('{thumbnail}', '', $body);
        
        return $body;     
         
    } 
    
    /**
     * Display an edit icon for the download
     *
     * Edit access checks must be performed in the calling code.
     *
     * @param    object    $item    The downloads data.
     *
     * @return    string    The HTML for the article edit icon.
     */
    public static function getEditIcon($item)
    {
        // Initialise variables.
        $user    = JFactory::getUser();
        $userId  = $user->get('id');
        $uri     = JFactory::getURI();

        JHtml::_('behavior.tooltip');

        $author = '';
                
        // Show checked_out icon if the article is checked out by a different user
        if (property_exists($item, 'checked_out') && property_exists($item, 'checked_out_time') && $item->checked_out > 0 && $item->checked_out != $user->get('id')) {
            $checkoutUser = JFactory::getUser($item->checked_out);
            $button = JHtml::_('image', 'system/checked_out.png', NULL, NULL, true);
            $date = JHtml::_('date', $item->checked_out_time);
            $tooltip = JText::_('COM_JDOWNLOADS_CHECKED_OUT').' :: '.JText::sprintf('COM_JDOWNLOADS_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
            return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
        }

        $url    = 'index.php?option=com_jdownloads&task=download.edit&a_id='.$item->file_id.'&return='.base64_encode(urlencode($uri));
        $icon   = $item->state ? 'edit.png' : 'edit_unpublished.png';
        $text   = '<img src="'.JUri::base().'components/com_jdownloads/assets/images/'.$icon.'" alt="'.JText::_('COM_JDOWNLOADS_EDIT_DOWNLOAD') .'" />';

        if ($item->state == 0) {
            $overlib = JText::_('COM_JDOWNLOADS_UNPUBLISHED');
        }
        else {
            $overlib = JText::_('COM_JDOWNLOADS_PUBLISHED');
        }

        $date = JHtml::_('date', $item->date_added);

        if (isset($item->creator)){
            $author = $item->creator;
        }    

        $overlib .= '&lt;br /&gt;';
        $overlib .= $date;
        $overlib .= '&lt;br /&gt;';
        $overlib .= JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_CREATED_BY').' '.htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

        $button = JHtml::_('link', JRoute::_($url), $text);

        $output = '<span class="hasTip" title="'.JText::_('COM_JDOWNLOADS_EDIT_DOWNLOAD').' :: '.$overlib.'">'.$button.'</span>';

        return $output;
    }
    
    /**
     * Display an icon to add a new download
     *
     * Edit access checks must be performed in the calling code.
     *
     * @param    object    $category    The current category ID. 
     *
     * @return    string    The HTML for the download NEW icon.
     */
    public static function getNewIcon($category)
    {
        $uri = JFactory::getURI();

        $url = 'index.php?option=com_jdownloads&task=download.add&return='.base64_encode(urlencode($uri)).'&a_id=0&catid=' . $category->id;
        $text = '<img src="'.JUri::base().'components/com_jdownloads/assets/images/new.png" alt="'.JText::_('COM_JDOWNLOADS_ADD_NEW_DOWNLOAD') .'" />';
        $button =  JHtml::_('link', JRoute::_($url), $text);

        $output = '<span class="hasTip" title="'.JText::_('COM_JDOWNLOADS_ADD_NEW_DOWNLOAD').'">'.$button.'</span>';
        
        return $output;
    }
    
    /**
     * Get all logged informations about the current user from the logs table 
     * 
     * Important: We can use alternate also: "COUNT(DISTINCT(log_file_id)) AS sumfiles," for add only values from different files
     *
     * @return   array    The consumed volume values and the remaining values
     */
    public static function getUserLimits($user_rules, $marked_files_id){
        
        global $jlistConfig;        
        
        $consumed = array();
        $download_volume_limit_daily  = 0;
        $download_volume_limit_weekly  = 0;
        $download_volume_limit_monthly = 0;
        
        
        
        $user   = JFactory::getUser();
        $db     = JFactory::getDBO();
        $query  = $db->getQuery(true);
        
        if (is_array($marked_files_id)){
            $id_text = implode(',', $marked_files_id);
        } else {
            $id_text = $marked_files_id;
        }    

        /**
        *  ==============================
        *  Get logged files for today
        *  ==============================
        */

        $query->select('COUNT(log_file_id) AS sumfiles, ROUND(SUM(log_file_size)) AS sumsize');
        $query->from('#__jdownloads_logs');
        // filter by log type (1 = downloads)
        $query->where('type = ' .$db->Quote('1'));
        // filter by user id
        $query->where('log_user = ' .$db->Quote($user->id));
        // filter by today only
        $query->where('(log_datetime >= CURRENT_DATE AND log_datetime < CURRENT_DATE + INTERVAL 1 DAY)');
        $query->group('log_user');
        $db->setQuery($query);
        $consumed['today'] = $db->loadObject();

        if ((int)$user_rules->download_limit_daily > 0)
        {
            if ($consumed['today']){
                $consumed['today_remaining'] = (int)$user_rules->download_limit_daily - (int)$consumed['today']->sumfiles; 
            } else {
                $consumed['today_remaining'] = (int)$user_rules->download_limit_daily; 
            }
        } else {
             $consumed['today_remaining'] = -1; //JText::_('COM_JDOWNLOADS_NO_LIMITS');
        }

        if ((int)$user_rules->download_volume_limit_daily > 0)
        {        
            if ($consumed['today']->sumsize){
                $consumed['today_volume_remaining'] = ((int)$user_rules->download_volume_limit_daily * 1024) - (int)$consumed['today']->sumsize; 
            } else {
                $consumed['today_volume_remaining'] = ((int)$user_rules->download_volume_limit_daily * 1024); 
            }
        } else {
             $consumed['today_volume_remaining'] = -1; //JText::_('COM_JDOWNLOADS_NO_LIMITS');
        }       
        
        /**
        *  ===============================
        *  Get logged files for last week
        *  ===============================
        */
        $query  = $db->getQuery(true);
        $query->select('COUNT(log_file_id) AS sumfiles, ROUND(SUM(log_file_size)) AS sumsize');
        $query->from('#__jdownloads_logs');
        // filter by log type (1 = downloads)
        $query->where('type = ' .$db->Quote('1'));
        // filter by user id
        $query->where('log_user = ' .$db->Quote($user->id));
        // filter by last week
        $query->where('(log_datetime >= CURRENT_DATE - INTERVAL 6 DAY) AND (log_datetime <= CURRENT_DATE + INTERVAL 1 DAY)');
        $query->group('log_user');
        $db->setQuery($query);
        $consumed['week'] = $db->loadObject();        

        if ((int)$user_rules->download_limit_weekly > 0)
        {  
            if ($consumed['week']){
                $consumed['week_remaining'] = (int)$user_rules->download_limit_weekly - (int)$consumed['week']->sumfiles; 
            } else {
                $consumed['week_remaining'] = (int)$user_rules->download_limit_weekly; 
            }
        
        } else {
             $consumed['week_remaining'] = -1; //JText::_('COM_JDOWNLOADS_NO_LIMITS');
        }            

        if ((int)$user_rules->download_volume_limit_weekly > 0)
        {
            if ($consumed['week']->sumsize){
                $consumed['week_volume_remaining'] = ((int)$user_rules->download_volume_limit_weekly * 1024) - (int)$consumed['week']->sumsize; 
            } else {
                $consumed['week_volume_remaining'] = ((int)$user_rules->download_volume_limit_weekly * 1024); 
            }
        } else {
             $consumed['week_volume_remaining'] = -1; //JText::_('COM_JDOWNLOADS_NO_LIMITS');
        }                
                
        /**
        *  ===============================
        *  Get logged files for last month
        *  ===============================
        */
        $query  = $db->getQuery(true);
        $query->select('COUNT(log_file_id) AS sumfiles, ROUND(SUM(log_file_size)) AS sumsize');
        $query->from('#__jdownloads_logs');
        // filter by log type (1 = downloads)
        $query->where('type = ' .$db->Quote('1'));
        // filter by user id
        $query->where('log_user = ' .$db->Quote($user->id));
        // filter by last week
        $query->where('(log_datetime >= CURRENT_DATE - INTERVAL 30 DAY) AND (log_datetime <= CURRENT_DATE + INTERVAL 1 DAY)');
        $query->group('log_user');
        $db->setQuery($query);
        $consumed['month'] = $db->loadObject();
        
        if ((int)$user_rules->download_limit_monthly > 0)
        {        
            if ($consumed['month']){
                $consumed['month_remaining'] = (int)$user_rules->download_limit_monthly - (int)$consumed['month']->sumfiles; 
            } else {
                $consumed['month_remaining'] = (int)$user_rules->download_limit_monthly; 
            }
        } else {
             $consumed['month_remaining'] = -1; //JText::_('COM_JDOWNLOADS_NO_LIMITS');            
        } 
        
        if ((int)$user_rules->download_volume_limit_monthly > 0)
        {
            if ($consumed['month']->sumsize){
                $consumed['month_volume_remaining'] = ((int)$user_rules->download_volume_limit_monthly * 1024) - (int)$consumed['month']->sumsize; 
            } else {
                $consumed['month_volume_remaining'] = ((int)$user_rules->download_volume_limit_monthly * 1024); 
            }
        } else {
             $consumed['month_volume_remaining'] = -1; // JText::_('COM_JDOWNLOADS_NO_LIMITS');
        }                               
        
        /**
        *  ==============================
        *  Get logged uploaded files for today
        *  ==============================
        */
        $query  = $db->getQuery(true);
        $query->select('COUNT(log_file_id) AS sumfiles');
        $query->from('#__jdownloads_logs');
        // filter by log type (1 = downloads)
        $query->where('type = ' .$db->Quote('2'));
        // filter by user id
        $query->where('log_user = ' .$db->Quote($user->id));
        // filter by today only
        $query->where('(log_datetime >= CURRENT_DATE AND log_datetime < CURRENT_DATE + INTERVAL 1 DAY)');
        $query->group('log_user');
        $db->setQuery($query);
        $consumed['upload'] = $db->loadObject();
        if ($consumed['upload']){
            $consumed['upload_remaining'] = (int)$user_rules->upload_limit_daily - (int)$consumed['upload']->sumfiles; 
        } else {
            $consumed['upload_remaining'] = (int)$user_rules->upload_limit_daily; 
        }

        /**
        *  ==============================
        *  Get the amount of downloads for every selected file until now
        *  ==============================
        */        
        if ($id_text){
            $query  = $db->getQuery(true);
            $query->select('log_file_id, COUNT(log_file_id) as count');
            $query->from('#__jdownloads_logs');
            // filter by log type (1 = downloads)
            $query->where('type = ' .$db->Quote('1'));
            // filter by user id
            $query->where('log_user = ' .$db->Quote($user->id));
            // filter by today only
            $query->where('log_file_id IN ('.$id_text.')');
            $query->group('log_file_id');
            $db->setQuery($query);
            $consumed['filescount'] = $db->loadObjectList();
        }
        
        if (!$user_rules->download_limit_daily && !$user_rules->download_limit_weekly && !$user_rules->download_limit_monthly && !$user_rules->download_volume_limit_daily
             && !$download_volume_limit_weekly && !$download_volume_limit_monthly)
        { 
            // no limits are defined for this user group
            $limits = ''; 
        } else {
            // create the user info  
            $limits = $user_rules->view_user_his_limits_msg;                                
            
            $limits = str_replace('{msg_title}', JText::_('COM_JDOWNLOADS_LIMITS_INFO_MSG_TITLE'), $limits);
            $limits = str_replace('{files_daily_label}', JText::_('COM_JDOWNLOADS_LIMITS_FILES_DAILY_LABEL'), $limits);
            $limits = str_replace('{files_weekly_label}', JText::_('COM_JDOWNLOADS_LIMITS_FILES_WEEKLY_LABEL'), $limits);
            $limits = str_replace('{files_monthly_label}', JText::_('COM_JDOWNLOADS_LIMITS_FILES_MONTHLY_LABEL'), $limits);
            $limits = str_replace('{volume_daily_label}', JText::_('COM_JDOWNLOADS_LIMITS_VOLUME_DAILY_LABEL'), $limits);
            $limits = str_replace('{volume_weekly_label}', JText::_('COM_JDOWNLOADS_LIMITS_VOLUME_WEEKLY_LABEL'), $limits);
            $limits = str_replace('{volume_monthly_label}', JText::_('COM_JDOWNLOADS_LIMITS_VOLUME_MONTHLY_LABEL'), $limits);
            $limits = str_replace('{upload_daily_label}', JText::_('COM_JDOWNLOADS_LIMITS_UPLOAD_DAILY_LABEL'), $limits);
            
            $limits = str_replace('{remaining_label}', JText::_('COM_JDOWNLOADS_LIMITS_REMAINING'), $limits);
           
            $limits = str_replace('{files_daily_value}',    $user_rules->download_limit_daily, $limits);
            $limits = str_replace('{files_weekly_value}',   $user_rules->download_limit_weekly, $limits);
            $limits = str_replace('{files_monthly_value}',  $user_rules->download_limit_monthly, $limits);
            $limits = str_replace('{volume_daily_value}',   ($user_rules->download_volume_limit_daily * 1024), $limits);
            $limits = str_replace('{volume_weekly_value}',  ($user_rules->download_volume_limit_weekly * 1024), $limits);
            $limits = str_replace('{volume_monthly_value}', ($user_rules->download_volume_limit_monthly * 1024), $limits);
            $limits = str_replace('{upload_daily_value}',   $user_rules->upload_limit_daily, $limits);
            
            $limits = str_replace('{files_daily_rest_value}',    $consumed['today_remaining'], $limits);
            $limits = str_replace('{files_weekly_rest_value}',   $consumed['week_remaining'], $limits);
            $limits = str_replace('{files_monthly_rest_value}',  $consumed['month_remaining'], $limits);
            $limits = str_replace('{volume_daily_rest_value}',   $consumed['today_volume_remaining'], $limits);
            $limits = str_replace('{volume_weekly_rest_value}',  $consumed['week_volume_remaining'], $limits);
            $limits = str_replace('{volume_monthly_rest_value}', $consumed['month_volume_remaining'], $limits);
            $limits = str_replace('{upload_daily_rest_value}',   $consumed['upload_remaining'], $limits);
            
            $limits = str_replace('{transfer_speed_limit}',   $user_rules->transfer_speed_limit_kb, $limits);
        }
        $consumed['limits_info'] = $limits;

        return $consumed;
    }
    
    /**
    * Check whether this user has reached his download limits 
    * 
    * @param array $user_rules
    * @param array $total_consumed
    * 
    * @return  mixed    TRUE when user may download - limitation message when not
    */
    public static function checkUserDownloadLimits($user_rules, $total_consumed, $sum_selected_files, $sum_selected_volume, $marked_files_id)
    {
        $id_text = implode(',', $marked_files_id); 

        if ( (int)$total_consumed['today_remaining'] !== -1){
            if ($sum_selected_files > (int)$total_consumed['today_remaining']){
                return self::getOnlyLanguageSubstring($user_rules->download_limit_daily_msg);
            }
        }
        
        if ( (int)$total_consumed['week_remaining'] !== -1){
            if ($sum_selected_files > (int)$total_consumed['week_remaining']){
                return self::getOnlyLanguageSubstring($user_rules->download_limit_weekly_msg);
            }
        }
        
        if ( (int)$total_consumed['month_remaining'] !== -1){
            if ($sum_selected_files > (int)$total_consumed['month_remaining']){
                return self::getOnlyLanguageSubstring($user_rules->download_limit_monthly_msg);
            }
        }

        if (isset($total_consumed['today_volume_remaining']) && $total_consumed['today_volume_remaining'] != -1){
            if ($sum_selected_volume > (int)$total_consumed['today_volume_remaining']){
                return self::getOnlyLanguageSubstring($user_rules->download_volume_limit_daily_msg);
            }
        }

        if (isset($total_consumed['week_volume_remaining']) && $total_consumed['week_volume_remaining'] != -1){
            if ($sum_selected_volume > (int)$total_consumed['week_volume_remaining']){
                return self::getOnlyLanguageSubstring($user_rules->download_volume_limit_weekly_msg);
            }
        }
                
        if (isset($total_consumed['month_volume_remaining']) && $total_consumed['month_volume_remaining'] != -1){
            if ($sum_selected_volume > (int)$total_consumed['month_volume_remaining']){
                return self::getOnlyLanguageSubstring($user_rules->download_volume_limit_monthly_msg);
            }
        }
        
        // finally check the amount of downloads for every file
        if (isset($total_consumed['filescount'])){
            foreach ($total_consumed['filescount'] as $filecount){
                if (in_array($filecount->log_file_id, $marked_files_id)){
                    if ($user_rules->how_many_times > 0 && ($filecount->count >= $user_rules->how_many_times)){
                        return sprintf(self::getOnlyLanguageSubstring($user_rules->how_many_times_msg), $user_rules->how_many_times);
                    }
                }
            }
        }    
        
        // When we are here, no limits are reached. So user may download the file(s)
        return true;
    }
    


    /**
    * Check whether a user may download the file, when AlphaUserPoints are used
    *    
    * @param integer     $sum_aup_price_points   the sum of points from price field, for the requested files
    *        array       $marked_files_id
    * 
    * @return  array
    */
    public static function checkUserPoints($sum_aup_price_points, $marked_files_id)
    {
        global $jlistConfig;        
        
        $user   = JFactory::getUser();
        $db     = JFactory::getDBO();
        $query  = $db->getQuery(true);
        
        $sum_aup_points = 0;
        $aup_result     = array();        
        
        $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';

        if (file_exists($api_AUP)){
            require_once ($api_AUP);
            
            // get current user points - stored in $profil->points
            $aup = new AlphaUserPointsHelper; 
            $profil = $aup->getUserInfo('', $user->id);
            
            // get standard points value from AUP jDownloads rule
            $db->setQuery("SELECT points FROM #__alpha_userpoints_rules WHERE published = 1 AND plugin_function = 'plgaup_jdownloads_user_download'");
            $aup_fix_points = floatval($db->loadResult());
            //$aup_fix_points = strToNumber($aup_fix_points);
            
            if ($jlistConfig['use.alphauserpoints.with.price.field']){
                $sum_aup_points = $sum_aup_price_points;
            } else {
                // fis points for every download are used
                $sum_aup_points = ($aup_fix_points * count($marked_files_id));
                // we need a positive value
                if ($sum_aup_points < 0) $sum_aup_points = -$sum_aup_points;
            }
            
            if ($profil){
                // we have a member
                if ($jlistConfig['user.can.download.file.when.zero.points']){
                        // he can download it after all
                        $aup_result['points_info']  = sprintf( str_replace('%d', '%s', JText::_('COM_JDOWNLOADS_FE_VIEW_AUP_SUM_POINTS')), self::strToNumber($sum_aup_points), self::strToNumber($profil->points));
                        $aup_result['may_download'] = true;                        
                } elseif ($sum_aup_points > 0 && $sum_aup_points <= $profil->points){
                        // view it only when we have a result and user may download it
                        $aup_result['points_info']  = sprintf( str_replace('%d', '%s', JText::_('COM_JDOWNLOADS_FE_VIEW_AUP_SUM_POINTS')), self::strToNumber($sum_aup_points), self::strToNumber($profil->points));
                        $aup_result['may_download'] = true;
                } elseif ($sum_aup_points > 0 && $sum_aup_points > $profil->points) {
                        // user may not download
                          $aup_result['points_info'] = '<div style="text-align:center" class="jd_div_aup_message">'.stripslashes(self::getOnlyLanguageSubstring($jlistConfig['user.message.when.zero.points'])).'</div>'. 
                                                       '<div style="text-align:center" class="jd_div_aup_message">'.JText::_('COM_JDOWNLOADS_FE_SUMMARY_YOUR_POINTS').' '.self::strToNumber($profil->points).'<br />'.JText::_('COM_JDOWNLOADS_FE_SUMMARY_NEEDED_POINTS').' '.self::strToNumber($sum_aup_points).'</div>';
                          $aup_result['may_download'] = false;
                } else {    
                        // this download is free but we create still the user info, so the user can read that he costs nothing!    
                        $aup_result['points_info']  = sprintf( str_replace('%d', '%s', JText::_('COM_JDOWNLOADS_FE_VIEW_AUP_SUM_POINTS')), abs(($aup_fix_points * count($marked_files_id))), self::strToNumber($profil->points));
                        $aup_result['may_download'] = true;                        
                }    
            } else {
                if ($sum_aup_points > 0){
                    // view it only when we have a result
                    // but we have here an unregistered visitor - he can not have aup points! 
                    if ($jlistConfig['user.can.download.file.when.zero.points']){
                        // he can download it after all
                        $aup_result['points_info']  = sprintf( str_replace('%d', '%s', JText::_('COM_JDOWNLOADS_FE_VIEW_AUP_SUM_POINTS')), self::strToNumber($sum_aup_points), 0);
                        $aup_result['may_download'] = true;                        
                    } else {
                        // now way to doenload it
                        $aup_result['points_info']  = sprintf( str_replace('%d', '%s', JText::_('COM_JDOWNLOADS_FE_VIEW_AUP_SUM_POINTS_FOR_VISITOR')), self::strToNumber($sum_aup_points), 0);
                        $aup_result['may_download'] = false;
                    }    
                } else {
                    // remove placeholder
                    $aup_result['points_info']  = '';
                    $aup_result['may_download'] = true; 
                }  
            }    
        } else {
            $aup_result['points_info']  = '';
            $aup_result['may_download'] = true; 
        }
        return $aup_result;
    }
    
    /**
    * Get the names from all Joomla user groups where the user is a member
    * 
    * @return  array
    */
    public static function getUserGroupsNames()
    {
        $db     = JFactory::getDBO();
        $user   = JFactory::getUser();

        $db->setQuery($db->getQuery(true)
            ->select('*')
            ->from("#__usergroups")
        );
        $groups = $db->loadRowList();
        
        $userGroups = $user->groups;
        $return = array();

        foreach ($groups as $key=>$g){
            if (array_key_exists($g[0],$userGroups)) array_push($return,$g[4]);
        }

        return $return;
    }
     
     

    /**
    * remove the language tag from a given text and return only the text
    *    
    * @param string     $msg
    */
    public static function getOnlyLanguageSubstring($msg)
    {
        // Get the current locale language tag
        $lang       = JFactory::getLanguage();
        $lang_key   = $lang->getTag();        
        
        // remove the language tag from the text
        $startpos = strpos($msg, '{'.$lang_key.'}') +  strlen( $lang_key) + 2 ;
        $endpos   = strpos($msg, '{/'.$lang_key.'}') ;
        
        if ($startpos !== false && $endpos !== false){
            return substr($msg, $startpos, ($endpos - $startpos ));
        } else {    
            return $msg;
        }    
    }
    
    /**
    *  Creates a compressed zip file
    * 
    * @param mixed $files
    * @param mixed $destination
    * @param mixed $overwrite
    * 
    * PHP >= 5.2
    */
    
    public static function createZipFile( $files = array(), $destination = '', $overwrite = true ) 
    {
        jimport( 'joomla.filesystem.folder' ); 
        jimport( 'joomla.filesystem.file' );        
        
        // if the zip file already exists and overwrite is false, return false
        if(JFile::exists($destination) && !$overwrite) {
           return false;
        }
        
        // vars
        $valid_files = array();
        
        // if files were passed in...
        if(is_array($files)) {
            // cycle through each file
            foreach($files as $file) {
                // make sure the file exists
                if(JFile::exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        
        // if we have valid files
        if(count($valid_files)) {
            // create the archive
            $zip = new ZipArchive();
            $limit = 250;
            
            $result = $zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE);
            if (!$result === TRUE){
                return false;
            }
            
            // add the files
            foreach($valid_files as $file) {
                
                // special function for trouble when we have to many files
                /*if (($zip->numfile % $limit) == 0){
                   $zip->close();
                   $zip->open($destination, ZIPARCHIVE::CREATE);
                } */

                $only_filename = basename($file); 
                //$zip->addFile($file, $only_filename);
                $zip->addFile($file, iconv('UTF-8', 'CP866', $only_filename)); // bugfix by 'Makulia'
            }
            
            // close the zip -- done!
            $zip->close();
            
            // check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }
    
    /**
    * Get the filesize from a given file path
    * 
    * @param string
    *      
    * @return string    formatted file size
    */
        
    public static function getFileSize($file) 
    {
        $a = array("B", "KB", "MB", "GB", "TB", "PB");

        $pos = 0;
        $size = filesize($file);
        while ($size >= 1024) {
                $size /= 1024;
                $pos++;
        }

        return round($size,2)." ".$a[$pos];
    }
    
    /**
    * Get the filesize from a given file url
    * 
    * @param mixed $url
    * @return integer  filesize in byte
    */
    public static function getUrlFilesize($url)
    {
        if (substr($url,0,4)=='http' || substr($url,0,3)=='ftp') {
            $size = array_change_key_case(get_headers($url, 1),CASE_LOWER);
            $size = $size['content-length'];
            if (is_array($size)) { $size = $size[1]; }
        } else {
            $size = @filesize($url); 
        }
        return $size;    
    }
    
    /**
    * Check whether we have a valid URL
    * 
    * @param mixed $url
    * @return boolean true when valid
    * Regex creation by diego perini
    */
    public static function urlValidate($url)
    {
        $url = trim($url);
        if (preg_match('%^(?:(?:https?)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $url)){
            return true;
        }
        return false;
    } 
    
    /**
    * 
    * 
    */
    public static function getFileExtension($filename)
    {
        return strtolower(substr(strrchr($filename, '.'), 1));
    }

    /**
    * Get the mime type from a given filename
    * 
    * @param mixed $filetype
    * @return mixed mime type
    */
    public static function getMimeTyp($filename)
    {
        switch ($filename) {
            case "ez":  $mime="application/andrew-inset"; break;
            case "hqx": $mime="application/mac-binhex40"; break;
            case "cpt": $mime="application/mac-compactpro"; break;
            case "doc": $mime="application/msword"; break;
            case "docx": $mime="application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
            case "bin": $mime="application/octet-stream"; break;
            case "dms": $mime="application/octet-stream"; break;
            case "lha": $mime="application/octet-stream"; break;
            case "lzh": $mime="application/octet-stream"; break;
            case "exe": $mime="application/octet-stream"; break;
            case "class": $mime="application/octet-stream"; break;
            case "dll": $mime="application/octet-stream"; break;
            case "oda": $mime="application/oda"; break;
            case "pdf": $mime="application/pdf"; break;
            case "ai":  $mime="application/postscript"; break;
            case "eps": $mime="application/postscript"; break;
            case "ps":  $mime="application/postscript"; break;
            case "xls": $mime="application/vnd.ms-excel"; break;
            case "xml": $mime="application/xml"; break;
            case "xlsx": $mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
            case "ppt": $mime="application/vnd.ms-powerpoint"; break;
            case "pptx": $mime="application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
            case "wbxml": $mime="application/vnd.wap.wbxml"; break;
            case "wmlc": $mime="application/vnd.wap.wmlc"; break;
            case "wmlsc": $mime="application/vnd.wap.wmlscriptc"; break;
            case "vcd": $mime="application/x-cdlink"; break;
            case "pgn": $mime="application/x-chess-pgn"; break;
            case "csh": $mime="application/x-csh"; break;
            case "dvi": $mime="application/x-dvi"; break;
            case "spl": $mime="application/x-futuresplash"; break;
            case "gtar": $mime="application/x-gtar"; break;
            case "hdf": $mime="application/x-hdf"; break;
            case "js":  $mime="application/x-javascript"; break;
            case "nc":  $mime="application/x-netcdf"; break;
            case "cdf": $mime="application/x-netcdf"; break;
            case "swf": $mime="application/x-shockwave-flash"; break;
            case "tar": $mime="application/x-tar"; break;
            case "tcl": $mime="application/x-tcl"; break;
            case "tex": $mime="application/x-tex"; break;
            case "texinfo": $mime="application/x-texinfo"; break;
            case "texi": $mime="application/x-texinfo"; break;
            case "t":   $mime="application/x-troff"; break;
            case "tr":  $mime="application/x-troff"; break;
            case "roff": $mime="application/x-troff"; break;
            case "man": $mime="application/x-troff-man"; break;
            case "me":  $mime="application/x-troff-me"; break;
            case "ms":  $mime="application/x-troff-ms"; break;
            case "ustar": $mime="application/x-ustar"; break;
            case "src": $mime="application/x-wais-source"; break;
            case "zip": $mime="application/x-zip"; break;
            case "au":  $mime="audio/basic"; break;
            case "snd": $mime="audio/basic"; break;
            case "mid": $mime="audio/midi"; break;
            case "midi": $mime="audio/midi"; break;
            case "kar": $mime="audio/midi"; break;
            case "mpga": $mime="audio/mpeg"; break;
            case "mp2": $mime="audio/mpeg"; break;
            case "mp3": $mime="audio/mpeg"; break;
            case "mp4": $mime="video/mp4"; break;
            case "aif": $mime="audio/x-aiff"; break;
            case "aiff": $mime="audio/x-aiff"; break;
            case "aifc": $mime="audio/x-aiff"; break;
            case "m3u": $mime="audio/x-mpegurl"; break;
            case "ram": $mime="audio/x-pn-realaudio"; break;
            case "rm":  $mime="audio/x-pn-realaudio"; break;
            case "rpm": $mime="audio/x-pn-realaudio-plugin"; break;
            case "ra":  $mime="audio/x-realaudio"; break;
            case "wav": $mime="audio/x-wav"; break;
            case "pdb": $mime="chemical/x-pdb"; break;
            case "xyz": $mime="chemical/x-xyz"; break;
            case "bmp": $mime="image/bmp"; break;
            case "gif": $mime="image/gif"; break;
            case "ief": $mime="image/ief"; break;
            case "jpeg": $mime="image/jpeg"; break;
            case "jpg": $mime="image/jpeg"; break;
            case "jpe": $mime="image/jpeg"; break;
            case "png": $mime="image/png"; break;
            case "tiff": $mime="image/tiff"; break;
            case "tif": $mime="image/tiff"; break;
            case "wbmp": $mime="image/vnd.wap.wbmp"; break;
            case "ras": $mime="image/x-cmu-raster"; break;
            case "pnm": $mime="image/x-portable-anymap"; break;
            case "pbm": $mime="image/x-portable-bitmap"; break;
            case "pgm": $mime="image/x-portable-graymap"; break;
            case "ppm": $mime="image/x-portable-pixmap"; break;
            case "rgb": $mime="image/x-rgb"; break;
            case "xbm": $mime="image/x-xbitmap"; break;
            case "xpm": $mime="image/x-xpixmap"; break;
            case "xwd": $mime="image/x-xwindowdump"; break;
            case "msh": $mime="model/mesh"; break;
            case "mesh": $mime="model/mesh"; break;
            case "silo": $mime="model/mesh"; break;
            case "wrl": $mime="model/vrml"; break;
            case "vrml": $mime="model/vrml"; break;
            case "css": $mime="text/css"; break;
            case "asc": $mime="text/plain"; break;
            case "txt": $mime="text/plain"; break;
            case "gpg": $mime="text/plain"; break;
            case "rtx": $mime="text/richtext"; break;
            case "rtf": $mime="text/rtf"; break;
            case "wml": $mime="text/vnd.wap.wml"; break;
            case "wmls": $mime="text/vnd.wap.wmlscript"; break;
            case "etx": $mime="text/x-setext"; break;
            case "xsl": $mime="text/xml"; break;
            case "flv": $mime="video/x-flv"; break;
            case "mpeg": $mime="video/mpeg"; break;
            case "mpg": $mime="video/mpeg"; break;
            case "mpe": $mime="video/mpeg"; break;
            case "qt":  $mime="video/quicktime"; break;
            case "mov": $mime="video/quicktime"; break;
            case "mxu": $mime="video/vnd.mpegurl"; break;
            case "avi": $mime="video/x-msvideo"; break;
            case "movie": $mime="video/x-sgi-movie"; break;
            case "asf": $mime="video/x-ms-asf"; break;
            case "asx": $mime="video/x-ms-asf"; break;
            case "wm":  $mime="video/x-ms-wm"; break;
            case "wmv": $mime="video/x-ms-wmv"; break;
            case "wvx": $mime="video/x-ms-wvx"; break;
            case "ice": $mime="x-conference/x-cooltalk"; break;
            case "rar": $mime="application/x-rar"; break;
            default:    $mime="application/octet-stream"; break; 
        }
        return $mime;
    } 

    /**
    * Get a mime type from a remote file
    * 
    * @param mixed $url
    */
    public static function getMimeTypeRemote($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_exec($ch);

        return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    }    
    
    /**
    * check whether we must build a shorter filename version. Sometimes required for output. 
    * 
    * @param string      $filename
    * 
    * @result string    shorted filename with real filename in tooltip
    */
    public static function getShorterFilename($filename)
    {
        global $jlistConfig;
          
        if ($jlistConfig['shortened.filename.length'] > 14 && $filename){
            if (strlen($filename) > $jlistConfig['shortened.filename.length']) {
                $short = substr($filename, 0, ($jlistConfig['shortened.filename.length'] - 10)) . '...' . substr($filename, -7);
                // use tooltip to view the rael filename
                $filename = JHtml::_('tooltip', $filename, '', '', $short);
            }
        }
        return $filename;    
    } 
    
    
    /**
    * Convert a giveen file size value to a pure Kilo Byte value
    * 
    * @param mixed $filesize 
    * 
    * @return integer    file size in KB 
    */
    
    public static function convertFileSizeToKB($filesize)
    {
        if (strpos(strtoupper($filesize), 'KB')){
            return round((int) $filesize);
        }
        
        if (strpos(strtoupper($filesize), 'MB')){
            return round((int) $filesize * 1024);
        }        

        if (strpos(strtoupper($filesize), 'GB')){
            return round(((int) $filesize * 1024) * 1024);
        }        

        if (strpos(strtoupper($filesize), 'TB')){
            return round((((int) $filesize * 1024) * 1024) * 1024);
        }  
                
        if (strpos(strtoupper($filesize), 'B')){
            return round((int) $filesize / 1024);
        }
        
        return (int) $filesize;
    }    
    
    /**
    * Delete all temporary zip files, which are older as defined in configuration
    * 
    * @param mixed $dir
    * @return boolean
    */

    public static function deleteOldZipFiles($dir)
    {
        global $jlistConfig;
        jimport('joomla.filesystem.file');
        
       $del_ok = false;
       $time = gettimeofday();

       foreach (glob($dir."*.*") as $file) {
          if ( $time['sec'] - date(filemtime($file)) >= ($jlistConfig['tempfile.delete.time'] * 60) )
               $del_ok = JFile::delete($file);
          }
        return $del_ok;
    } 
        
    /**
    * build a random integer number
    * @return integer
    */
    
    public static function buildRandomNumber()
    {
       mt_srand((double)microtime()*1000000);
       mt_getrandmax();
       $random_id = mt_rand();
       return $random_id;
    }              
        
    /**
    * 
    * 
    */
    
    public static function checkCom()
    {
        
        global $jlistConfig;
       
        $config = JFactory::getConfig();
        $secret = $config->get( 'secret' );
        $line = '';
        
        if (strrev($jlistConfig['com']) != $secret){
            $power = 'Powered&nbsp;by&nbsp;jDownloads';
            $line .= '<div style="text-align:center" class="jd_footer"><a href="http://www.jDownloads.com" target="_blank" title="www.jDownloads.com">'.$power.'</a></div>';
        }
        return $line;            
        
    }

    /**
    *  anti leeching 
    * 
    * @return boolean
    */
    public static function useAntiLeeching()
    {
        global $jlistConfig;
        
       $url = JUri::base( false );

       list($remove, $stuff2) = explode('//', $url, 2);
       list($domain, $stuff2) = explode('/', $stuff2, 2); 
       $domain = str_replace('www.', '', $domain);        
       
       $refr = getenv("HTTP_REFERER");
       list($remove, $stuff) = explode('//', $refr, 2);
       list($home, $stuff) = explode('/', $stuff, 2);
       $home = str_replace('www.', '', $home); 
       
       $blocking = false; 
       
       if ($home != $domain) {
           $allowed_urls = explode(',' , $jlistConfig['allowed.leeching.sites']);
           if ($jlistConfig['check.leeching']) {
               if ($jlistConfig['block.referer.is.empty']) {
                   if (!$refr) {
                       $blocking = true;
                   }
               } else {
                   if  (!$refr){
                       $blocking = false;
                   }    
               }    
               
               if (in_array($home,$allowed_urls)) {
                  $blocking = false;
               } else {
                 $blocking = true;        
               }  
           } 
       }

       // check blacklist
       if ($jlistConfig['use.blocking.list'] && $jlistConfig['blocking.list'] != '') {
           $user_ip = self::getRealIp();
           if (stristr($jlistConfig['blocking.list'], $user_ip)){
               $blocking = true;
           }    
       }
       return $blocking;    
    }
    
    /**
    *  Write data encoded in session 
    * 
    * @param  string     $string = data, $name = name for the session data field
    * @return none
    */
    public static function writeSessionEncoded($string, $name)
    {
        $session = JFactory::getSession();
        if ($string){
            $session->set($name, base64_encode($string));
        }    
    }
    
    /**
    *  Get a decooded data field value from session 
    * 
    * @param  string     $name = name for the session data field
    * @return string
    */
    public static function getSessionDecoded($name)
    {
        $db     = JFactory::getDBO();
        $string = '';
        
        $session = JFactory::getSession();
        
        if ($name && $session->has($name)){
            $string = $db->escape($session->get($name, ''));
            $string = base64_decode($string);
        }
        return $string;
    } 
    
    /**
    *  Remove a data field from session 
    * 
    * @param  string     $name = name for the session data field
    * 
    */
    public static function writeSessionClear($name)
    {
        $session = JFactory::getSession();
        $session->clear($name);
    }
    
    /**
     * Function to get the client ip address    * 
    */
    public static function getRealIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
     
        return $ipaddress;
    }

    /**
    * added (or reduce) points to the alphauserpoints when is activated in the jD config
    *     
    * @param mixed $submitted_by    user ID after upload a file
    * @param mixed $file_title      the title from new download
    */
    public static function setAUPPointsUploads($submitted_by, $file_title)
    {
        global $jlistConfig;
    
        if ($jlistConfig['use.alphauserpoints'] && $submitted_by){
            $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
    
            if (file_exists($api_AUP)){
                require_once ($api_AUP);
                $aupid = AlphaUserPointsHelper::getAnyUserReferreID( $submitted_by );
    
                if ($aupid){
                    $text = JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_UPLOAD_TEXT');
                    $text = sprintf($text, $file_title);
                    AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_user_upload_published', $aupid, $file_title, $text);
                }     
            }    
        }
    }    

    /**
    * added (or reduce) points to the alphauserpoints when is activated in the jD config
    * 
    * @param mixed $user_id      user ID from the file downloader
    * @param mixed $file_title
    * @param mixed $file_id
    * @param mixed $price
    */
    public static function setAUPPointsDownload($user_id, $file_title, $file_id, $price, $profile = array())
    {
        global $jlistConfig;

        if ($jlistConfig['use.alphauserpoints'] && $user_id){

            $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';

            if (file_exists($api_AUP)){
                require_once ($api_AUP);
                
                $referreid = AlphaUserPointsHelper::getAnyUserReferreID( $user_id );
                if ($referreid){
                    $key_reference = AlphaUserPointsHelper::buildKeyreference( 'plgaup_jdownloads_user_download_use_price', $file_id, $user_id );
                    $rule_id = AlphaUserPointsHelper::getRuleID('plgaup_jdownloads_user_download_use_price');
                    $check_aup_reference = AlphaUserPointsHelper::checkReference($profile->referreid, $key_reference, $rule_id);
                    // check the method when a prior download process is found
                    if ($check_aup_reference > 0){
                         $method = (int)AlphaUserPointsHelper::getMethod('plgaup_jdownloads_user_download_use_price');
                         switch ($method){
                                // has already payed
                            case 1:          // ONCE PER USER
                                return true;
                                break;
                            case '2':        // ONCE PER DAY AND PER USER'        
                                return true;
                                break;
                            case '3':        // ONCE A DAY FOR A SINGLE USER ON ALL USERS
                                return true;
                                break;
                            case '5':        // ONCE PER USER PER WEEK
                                return true; 
                                break;
                            case '6':        // ONCE PER USER PER MONTH
                                return true;
                                break;
                            case '7':        // ONCE PER USER PER YEAR
                                return true;
                                break;
                            /*
                            case '4':        // WHENEVER
                            case '0':
                            default:                            
                                // points must be payed always
                            */    
                         }
                    }
                    
                    $text = JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DOWNLOAD_TEXT');
                    $text = sprintf($text, $file_title);
                    
                    if ($jlistConfig['user.can.download.file.when.zero.points'] || $profile->points > 0 || $price == 0){
                        if ($price){
                            // price as points activated
                            if ($profile->points >= $price){
                                if ($jlistConfig['use.alphauserpoints.with.price.field']){
                                AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_user_download_use_price', $referreid, $key_reference, $text, '-'.$price, $text);
                                return true;
                                } else {
                                    AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_user_download', $referreid, $key_reference, $text);
                                    return true;
                                }    
                            } else {
                                // not enough points . no download
                                return false;
                            }    
                        } else {
                            // use points set in AUP plugin
                            AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_user_download', $referreid, '', $text);
                            return true;
                        }    
                    } else {
                        // not enough points . no download
                        return false;
                    }   
                }     
            } else {
                return true;
            }    
        } else {
            if ($price){
                // not registered user
                return false;
            } else {     
                // guest but no price
                return true;  
            }    
        }                    
    }

    /**
    *  added (or reduce) points to the alphauserpoints when is activated in the jD config
    * 
    * @param mixed $user_id         user ID from the file download
    * @param mixed $file_title
    * @param mixed $file_id
    * @param mixed $price
    */
    public static function setAUPPointsDownloads($user_id, $file_title, $file_id, $price, $profile = array())
    {
        // added (or reduce) points to the alphauserpoints when is activated in the jD config
        // $user_id = user ID from the file download
        global $jlistConfig;

        if ($jlistConfig['use.alphauserpoints'] && $user_id){
            $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
            if (file_exists($api_AUP)){
                require_once ($api_AUP);
                
                $referreid = AlphaUserPointsHelper::getAnyUserReferreID( $user_id );
                if ($referreid){
                    $key_reference = AlphaUserPointsHelper::buildKeyreference( 'plgaup_jdownloads_user_download_use_price', $file_id, $user_id );
                    $rule_id = AlphaUserPointsHelper::getRuleID('plgaup_jdownloads_user_download_use_price');
                    $check_aup_reference = AlphaUserPointsHelper::checkReference($profile->referreid, $key_reference, $rule_id);
                    // check the method when a prior download process is found
                    if ($check_aup_reference > 0){
                         $method = (int)AlphaUserPointsHelper::getMethod('plgaup_jdownloads_user_download_use_price');
                         switch ($method){
                            case 1: // ONCE PER USER
                                // has already payed 
                                return true;
                                break;
                            case '2':        // ONCE PER DAY AND PER USER'        
                                return true;
                                break;
                            case '3':        // ONCE A DAY FOR A SINGLE USER ON ALL USERS
                                return true;
                                break;
                            case '5':       // ONCE PER USER PER WEEK
                                return true;
                                break;
                            case '6':       // ONCE PER USER PER MONTH
                                return true;
                                break;
                            case '7':       // ONCE PER USER PER YEAR
                                return true;
                                break;
                            /*
                            case '4':       // WHENEVER
                            case '0':
                            default:                            
                                // points must be payed always
                            */    
                         }
                    }                
                    
                    $text = JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DOWNLOAD_TEXT');
                    $text = sprintf($text, $file_title);
                    
                    if ($jlistConfig['user.can.download.file.when.zero.points'] || $profile->points > 0 || $price == 0){
                        if ($price){
                            // price as points activated
                                AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_user_download_use_price', $referreid, $key_reference, $text, '-'.$price, $text);
                                return true;
                        } else {
                            AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_user_download', $referreid, $key_reference, $text);
                            return true;
                        }    
                    } else {
                        return false;
                    }   
                }     
            } else {
               return true;
            }   
        } else {
          return true;
        }
    }

    /**
    * Assign points to the file uploader when a user download his file
    * 
    * @param mixed $files
    */
    public static function setAUPPointsDownloaderToUploader($files)
    {
        $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';

        if (file_exists($api_AUP)){
            require_once ($api_AUP);

            foreach ($files as $file){  
                if ($file->submitted_by){
                    $referreid = AlphaUserPointsHelper::getAnyUserReferreID( (int)$file->submitted_by );
                    if ($referreid){
                        $key_reference = AlphaUserPointsHelper::buildKeyreference( 'plgaup_jdownloads_downloader_to_uploader', $file->file_id, (int)$file->submitted_by );
                        $rule_id = AlphaUserPointsHelper::getRuleID('plgaup_jdownloads_downloader_to_uploader');
                        $check_aup_reference = AlphaUserPointsHelper::checkReference($referreid, $key_reference, $rule_id);
                        // check the method when a prior download process is found
                        if ($check_aup_reference > 0){
                             $method = (int)AlphaUserPointsHelper::getMethod('plgaup_jdownloads_downloader_to_uploader');
                             switch ($method){
                                case 1: // ONCE PER USER
                                    // has already payed 
                                    return;
                                    break;
                                case '2':        // ONCE PER DAY AND PER USER'        
                                    return;
                                    break;
                                case '3':        // ONCE A DAY FOR A SINGLE USER ON ALL USERS
                                    return;
                                    break;
                                case '5':       // ONCE PER USER PER WEEK
                                    return;
                                    break;
                                case '6':       // ONCE PER USER PER MONTH
                                    return;
                                    break;
                                case '7':       // ONCE PER USER PER YEAR
                                    return;
                                    break;
                                /*
                                case '4':       // WHENEVER
                                case '0':
                                default:                            
                                    // points must be payed always
                                */    
                             }
                        }                
                                
                        $text = JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DOWNLOAD_TEXT');
                        $text = sprintf($text, $file->file_title);
                        
                        AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_downloader_to_uploader', $referreid, $key_reference, $text, $price, $text);
                   }                             
                }
            }
        }        
    }

    /**
    * Assign points to the file uploader when a user download his file and use the price field  
    * 
    * @param mixed $files
    */
    
	public static function setAUPPointsDownloaderToUploaderPrice($files)
    {
        $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';

        if (file_exists($api_AUP)){
            require_once ($api_AUP);

            foreach ($files as $file){  
                if ($file->submitted_by){
                    $referreid = AlphaUserPointsHelper::getAnyUserReferreID( (int)$file->submitted_by );
                    if ($referreid){
                        $key_reference = AlphaUserPointsHelper::buildKeyreference( 'plgaup_jdownloads_downloader_to_uploader_use_price', $file->file_id, (int)$file->submitted_by );
                        $rule_id = AlphaUserPointsHelper::getRuleID('plgaup_jdownloads_downloader_to_uploader_use_price');
                        $check_aup_reference = AlphaUserPointsHelper::checkReference($referreid, $key_reference, $rule_id);
                        // check the method when a prior download process is found
                        if ($check_aup_reference > 0){
                             $method = (int)AlphaUserPointsHelper::getMethod('plgaup_jdownloads_downloader_to_uploader_use_price');
                             switch ($method){
                                case 1: // ONCE PER USER
                                    // has already payed 
                                    return;
                                    break;
                                case '2':        // ONCE PER DAY AND PER USER'        
                                    return;
                                    break;
                                case '3':        // ONCE A DAY FOR A SINGLE USER ON ALL USERS
                                    return;
                                    break;
                                case '5':       // ONCE PER USER PER WEEK
                                    return;
                                    break;
                                case '6':       // ONCE PER USER PER MONTH
                                    return;
                                    break;
                                case '7':       // ONCE PER USER PER YEAR
                                    return;
                                    break;
                                /*
                                case '4':       // WHENEVER
                                case '0':
                                default:                            
                                    // points must be payed always
                                */    
                             }
                        }                
                                
                        $text = JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DOWNLOAD_TEXT');
						$text = sprintf($text, $file->file_title);
                        
                        $price = floatval($file->price); 
						AlphaUserPointsHelper::newpoints( 'plgaup_jdownloads_downloader_to_uploader_use_price', $referreid, $key_reference, $text, '+'.$price, $text);
				   } 							
                }
            }
        }        
    }

    /**
    * Send  after download an e-mail to the selected addresses
    * 
    * @param mixed $files
    */
    public static function sendMailDownload($files)
    {
        global $jlistConfig;
        
        $user           = JFactory::getUser();
    
        $config         = JFactory::getConfig();
        $mailfrom       = $config->get( 'mailfrom' );
        $mailfromname   = $config->get( 'fromname' );
        
        $mail_files = "<div><ul>";

        for ($i=0; $i<count($files); $i++) {
           if ($files[$i]->license > 0){
               $mail_files .= "<div><li>".$files[$i]->file_title.' '.$files[$i]->release.'&nbsp;&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_FE_DETAILS_LICENSE_TITLE').': '.$files[$i]->license_title.'&nbsp;&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE').': '.$files[$i]->size.'</li></div>';
           } else {
               $mail_files .= "<div><li>".$files[$i]->file_title.' '.$files[$i]->release.'&nbsp;&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE').': '.$files[$i]->size.'</li></div>';
           }  
        }

        $mail_files .= "</ul></div>";
     
        // get IP
        $ip = self::getRealIp();

        // date and time
        $timestamp = time();
        $date_format = self::getDateFormat();
        $date_time = date($date_format['long'], $timestamp);

        $user_downloads = '<br />';

        // get user
        if ($user->guest) {
           $user_name = JText::_('COM_JDOWNLOADS_MAIL_DOWNLOADER_NAME_VISITOR');
           $user_group = JText::_('COM_JDOWNLOADS_MAIL_DOWNLOADER_GROUP');
        } else {
           $user_name = $user->get('username');
           $user_email = $user->get('email');
           $groups = self::getUserGroupsNames();
           foreach ($groups as $group){
               if ($user_group) $user_group .= ', '; 
               $user_group .= $group;
           }         
        }

        // Get all users email addresses in an array
        $jlistConfig['send.mailto'] = str_replace(' ', '', $jlistConfig['send.mailto']);
        $recipients = explode( ';', $jlistConfig['send.mailto']);

        // Check to see if there are any users in this group before we continue
        if (!count($recipients)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_NO_EMAIL_RECIPIENT_FOUND'), 'error');
            return false;
        }

        // Get the Mailer
        $mailer = JFactory::getMailer();

        // Build email message format.
        $mailer->setSender(array($mailfrom, $mailfromname));
        $mailer->setSubject(JDHelper::getOnlyLanguageSubstring($jlistConfig['send.mailto.betreff']));
        $html_format = true;
        
        $text = "";
        $text = stripslashes(JDHelper::getOnlyLanguageSubstring($jlistConfig['send.mailto.template.download']));
        $text = str_replace('{file_list}', $mail_files, $text);
        $text = str_replace('{ip_address}', $ip, $text);
        $text = str_replace('{user_name}', $user_name, $text);
        $text = str_replace('{user_group}', $user_group, $text);
        $text = str_replace('{date_time}', $date_time, $text);
        $text = str_replace('{user_email}', $user_email, $text);
        if (!$jlistConfig['send.mailto.html']){
            $html_format = false;
            $text = strip_tags($text);
        }        

        $mailer->setBody($text);
        
        // Needed for use HTML 
        $mailer->IsHTML($html_format);
        $mailer->Encoding = 'base64';

        // Add recipients
        $mailer->addRecipient($recipients);

        // Send the Mail
        $result = $mailer->Send();

        if ( $result !== true ) {
            //JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_DB_ERROR'), 'error');
            return false;
        } else {
            return true;
        }         
    } 
    
/**
    * Send after new download creation an e-mail to the selected addresses
    * 
    * @param mixed $files
    */
    public static function sendMailUpload($data)
    {
        global $jlistConfig;
        
        $user           = JFactory::getUser();

        $config         = JFactory::getConfig();
        $mailfrom       = $config->get( 'mailfrom' );
        $mailfromname   = $config->get( 'fromname' );
        
        // get IP
        $ip = self::getRealIp();

        // date and time
        $timestamp = time();
        $date_format = self::getDateFormat();
        $date_time = date($date_format['long'], $timestamp);

        $user_downloads = '<br />';

        // get user
        $user_name = $user->get('username');
        $user_email = $user->get('email');

        $jlistConfig['send.mailto.upload'] = str_replace(' ', '', $jlistConfig['send.mailto.upload']);
        $recipients = explode(';', $jlistConfig['send.mailto.upload']);
        
        // Check to see if there are any users in this group before we continue
        if (!count($recipients)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_NO_EMAIL_RECIPIENT_FOUND'), 'error');
            return false;
        }

        // Get the Mailer
        $mailer = JFactory::getMailer();

        // Build email message format.
        $mailer->setSender(array($mailfrom, $mailfromname));
        $mailer->setSubject(JDHelper::getOnlyLanguageSubstring($jlistConfig['send.mailto.betreff.upload']));
        $html_format = true;        
        
        $text = "";
        $text = stripslashes(JDHelper::getOnlyLanguageSubstring($jlistConfig['send.mailto.template.upload']));
        $text = str_replace('{file_title}', $data->file_title, $text);
        
        if ($data->url_download){
            $text = str_replace('{file_name}', $data->url_download, $text);    
        } elseif ($data->filename_from_other_download){
            $text = str_replace('{file_name}', $data->filename_from_other_download, $text);    
        }
        
        $text = str_replace('{description}', $data->description, $text);
        $text = str_replace('{ip}', $ip, $text);
        $text = str_replace('{name}', $user_name, $text);
        $text = str_replace('{date}', $date_time, $text);
        $text = str_replace('{mail}', $user_email, $text);
        if (!$jlistConfig['send.mailto.html.upload']){
            $html_format = false;
            $text = strip_tags($text);
        }
        
        $mailer->setBody($text);
        
        // Needed for use HTML 
        $mailer->IsHTML($html_format);
        $mailer->Encoding = 'base64';

        // Add recipients
        $mailer->addRecipient($recipients);

        // Send the Mail
        $result = $mailer->Send();

        if ( $result !== true ) {
            //JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_DB_ERROR'), 'error');
            return false;
        } else {
            return true;
        }            
    } 
        
    /**
    * Check whether the user has in the last hour before started the same download 
    * 
    * @param mixed $user_ip
    * @return boolean
    */
    public static function getLastDownloadActivity($user_id, $files_list, $file_id, $duration)
    {
        
        $db     = JFactory::getDBO();
        $query  = $db->getQuery(true);
  
        $query->select('COUNT(*)');
        $query->from('#__jdownloads_logs');
        // filter by log type (1 = downloads)
        $query->where('type = ' .$db->Quote('1'));
       
        if ($user_id){
            // filter by user id
            $query->where('log_user = ' .$db->Quote($user_id));
        } else {
            // filter by ip (guest)
            $ip = self::getRealIp();
            $query->where('log_ip = ' .$db->Quote($ip));
        }   
        
        if ($files_list){
            $query->where( 'log_file_id IN (' .$files_list. ')' );
        } else {
            $query->where('log_file_id = ' .$db->Quote($file_id));
        }
        
        // make sure that duration has a value otherwise get we a MySQL error
        if (!$duration) $duration = 0;
            
        $query->where('log_datetime <= NOW() AND log_datetime >= (NOW() - INTERVAL '.$duration.' MINUTE)');
        $db->setQuery($query);
        $exist = $db->loadResult();
        
        return $exist;
    }
    
    /**
    * Write the download activity in the log table 
    * 
    * @param integer  $type (1:download or 2:upload/creation in frontend)
    * @param mixed  $files
    * @param mixed  $upload_data
    */
    public static function updateLog($type, $files, $upload_data)
    {
        $db     = JFactory::getDBO();
        $query  = $db->getQuery(true);
        $user   = JFactory::getUser();
        
        $ip     = self::getRealIp();
        $app    = JFactory::getApplication();

        // get current 'now' data with correct local time zone
        $date = JFactory::getDate('now')->format('Y-m-d H:i:s', true);  // True to return the date string in the local time zone, false to return it in GMT.
                
        if ($type == 1){
            foreach ($files as $file){
                $filesize = JDHelper::convertFileSizeToKB($file->size);
                if ($file->extern_file != ''){
                    $db->setQuery("INSERT INTO #__jdownloads_logs (type, log_file_id, log_file_size, log_file_name, log_title, log_ip, log_datetime, log_user) VALUES ( '".$type."', '".$file->file_id."', '".$filesize."', '".$file->extern_file."', '".$db->escape($file->file_title)."',  '".$ip."', '".$date."', '".$user->get('id')."')");
                } else {
                    $db->setQuery("INSERT INTO #__jdownloads_logs (type, log_file_id, log_file_size, log_file_name, log_title, log_ip, log_datetime, log_user) VALUES ( '".$type."', '".$file->file_id."', '".$filesize."', '".$db->escape($file->url_download)."', '".$db->escape($file->file_title)."',  '".$ip."', '".$date."', '".$user->get('id')."')");
                }    
                $db->execute();
            }
        } else {
            if ($type = 2){
                $filesize = JDHelper::convertFileSizeToKB($upload_data->size);
                $db->setQuery("INSERT INTO #__jdownloads_logs (type, log_file_id, log_file_size, log_file_name, log_title, log_ip, log_datetime, log_user) VALUES ( '".$type."', '".$upload_data->file_id."', '".$filesize."', '".$db->escape($upload_data->url_download)."', '".$db->escape($upload_data->file_title)."',  '".$ip."', '".$date."', '".$user->get('id')."')");
                $db->execute();                
            }
            
        }
      
    } 
    
    /**
    * Check whether a user may download a file within his limitations
    * 
    * @param mixed $cat_id
    * @param mixed $file_id
    * @param mixed $files_list
    * @param mixed $user_rules
    * @param mixed $sum_selected_files
    * @param mixed $sum_selected_volume
    */
    public static function checkDirectDownloadLimits($cat_id, $file_id, $marked_files_id, $user_rules, $sum_selected_files, $sum_selected_volume)
    {
        global $jlistConfig;
      
        // we need the filed id when not used checkboxes
        if (!$marked_files_id){
            $marked_files_id = array($file_id);
        }
        $marked_files_id_string = implode(',', $marked_files_id);
        
        // We must compute up to this point, what this user has downloaded before and compare it then later with the defined user limitations 
        // Important: Please note, that we can check it only for registered users. By visitors it is not really useful, then we have here only a changeable IP.  

        $total_consumed = JDHelper::getUserLimits($user_rules, $marked_files_id);
        
        // When $total_consumed['limits_info'] has a value, we must check whether this user may download the selected files
        // If so, then the result is: TRUE - otherwise: the limitations message
        // Has $total_consumed['limits_info'] not any value, it exists not any limitations for this user  

        if ($total_consumed['limits_info']){ 
            $may_download = JDHelper::checkUserDownloadLimits($user_rules, $total_consumed, $sum_selected_files, $sum_selected_volume, $marked_files_id);
        } else {
            $may_download = true;
        }
        
        // check whether user has enough points from alphauserpoints (when used and installed)                
        if ($may_download === true && $jlistConfig['use.alphauserpoints']){
            $aup_result = JDHelper::checkUserPoints($sum_aup_points, $marked_files_id);
            if ($aup_result['may_download'] === true){
                $may_download = true;
            } else {
                $may_download = $aup_result['points_info']; 
            }    
        }    
        
        // write data in session
        if ($may_download === true){
            if ($user_random_id){    
                JDHelper::writeSessionEncoded($user_random_id, 'jd_random_id');
                JDHelper::writeSessionEncoded($marked_files_id_string, 'jd_list');
                JDHelper::writeSessionClear('jd_fileid');
            } else {
                // single file download
                if ($file_id){
                    JDHelper::writeSessionEncoded($file_id, 'jd_fileid');    
                } else {
                    JDHelper::writeSessionEncoded($marked_files_id[0], 'jd_fileid');    
                }
                JDHelper::writeSessionClear('jd_random_id');
                JDHelper::writeSessionClear('jd_list');                        
            }
            JDHelper::writeSessionEncoded($cat_id, 'jd_catid');
            
            return true;
        } else {
            return $may_download;
        }                   
    }
    
    /**
     * Method to get the correct db prefix (problem with getTablelist() which always/sometimes has lowercase prefix names in array)
     *
     * @return string
     */
    public static function getCorrectDBPrefix() 
    {
        $db = JFactory::getDBO();

        // get DB prefix string and table list
        $prefix     = $db->getPrefix();
        $prefix_low = strtolower($prefix);
        $tablelist  = $db->getTableList();

        if (!in_array ( $prefix.'assets', $tablelist)) {
            if (in_array ( $prefix_low.'assets', $tablelist)) {
                return $prefix_low;
            } else {
                // assets table not found? 
                return '';
            } 
        } else {
            return $prefix;
        }        

    }
    
    /**
     * Method to return a list of all categories that a user has permission for a given action
     *
     * @param   string  $action     The name of the section within the component from which to retrieve the actions.
     *
     * @return  array  List of categories that this group can do this action to (empty array if none). Categories must be published.
     *
     */
    public static function getAuthorisedJDCategories($action, $user)
    {
        $session = JFactory::getSession();
        $allowedCategories = $session->get('jd_allowed_create_categories', '');
            
        if (!$allowedCategories){
            // Brute force method: get all published category rows for the component and check each one
            // TODO: Modify the way permissions are stored in the db to allow for faster implementation and better scaling
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)->select('c.id AS id, a.name AS asset_name')->from('#__jdownloads_categories AS c')
                ->innerJoin('#__assets AS a ON c.asset_id = a.id')->where('c.published = 1');
            $db->setQuery($query);
            $allCategories = $db->loadObjectList('id');
            $allowedCategories = array();
            foreach ($allCategories as $category)
            {
                if ($user->authorise($action, $category->asset_name))
                {
                    $allowedCategories[] = (int) $category->id;
                }
            }
            $session->set('jd_allowed_create_categories', $allowedCategories);
        }
        return $allowedCategories;
    }        
    
    
    /**
    * Correct the ordering values from jD configuration
    * (We have use in the jD configuration the old numerical values (to be compatible) so we must correct it before we can build the query...
    * 
    * @param mixed $default_ordering
    */
    public static function getCorrectedOrderbyValues($type, $default_ordering)
    {
        $orderby = '';
        
        if ($type == 'primary'){
            // category sort ordering
            switch ($default_ordering)
            {
                case '0' :
                    $orderby = 'order';
                    break;

                case '1' :
                    $orderby = 'alpha';
                    break;

                case '2' :
                    $orderby = 'ralpha';
                    break;

                default :
                    $orderby = 'order';
                    break;                
            }
            return $orderby;
        }
        
        if ($type == 'secondary'){
            // 'Downloads' sort ordering
            switch ($default_ordering)
            {
                case '0' :
                    $orderby = 'order';
                    break;

                case '1' :
                    $orderby = 'alpha';
                    break;

                case '2' :
                    $orderby = 'ralpha';
                    break;

                default :
                    $orderby = 'order';
                    break;                
            }
            return $orderby;
        }            
    } 
    
    /**
    * Converts a string into Float while taking the given or locale number format into account
    * Used as default the defined separator characters from the Joomla main language ini file (as example: en-GB.ini)  
    * 
    * @param mixed $str
    * @param mixed $dec_point
    * @param mixed $thousands_sep
    * @param mixed $decimals
    * @return mixed
    */
    public static function strToNumber( $str, $dec_point=null, $thousands_sep=null, $decimals = 0 )
    {
        if( is_null($dec_point) || is_null($thousands_sep) ) {
            if( is_null($dec_point) ) {
                $dec_point = JText::_('DECIMALS_SEPARATOR');
            }
            if( is_null($thousands_sep) ) {
                $thousands_sep = JText::_('THOUSANDS_SEPARATOR');
            }
        }
        // in this case use we as default the en-GB format
        if (!$dec_point || $dec_point == 'DECIMALS_SEPARATOR') $dec_point = '.'; 
        if (!$thousands_sep || $thousands_sep == 'THOUSANDS_SEPARATOR') $thousands_sep = ','; 

        // we will not round a value so we must check it
        if (is_numeric($str) && !is_int($str) && !is_double($str) && $decimals == 0){
            $decimals = 2;
        }
        
        $number = number_format($str, $decimals, $dec_point, $thousands_sep);
        return $number;
    }        
 
    /**
    * Compute which date format shall be used for the output
    * 
    * @return mixed
    */
    public static function getDateFormat(){
        
        global $jlistConfig;
        
        $format = array();
        
        // check at first the long format 
        // when defined get the format from the current language
        if ($jlistConfig['global.datetime']){
            $format['long'] = self::getOnlyLanguageSubstring($jlistConfig['global.datetime']);
            if (!$format['long']){
                $format['long'] = JText::_('DATE_FORMAT_LC2');
            }
        } else {
            // format is not defined in configuration so we use a standard format from the language file (LC2)
            $format['long'] = JText::_('DATE_FORMAT_LC2');
        }

        // check now the short format field
        // when defined get the format from the current language
        if ($jlistConfig['global.datetime.short']){
            $format['short'] = self::getOnlyLanguageSubstring($jlistConfig['global.datetime.short']);
            if (!$format['short']){
                $format['short'] = JText::_('DATE_FORMAT_LC4');
            }
        } else {
            // format is not defined in configuration so we use a standard format from the language file (LC4)
            $format['short'] = JText::_('DATE_FORMAT_LC4');
        }

        return $format;    
    } 
      
}    
    
?>