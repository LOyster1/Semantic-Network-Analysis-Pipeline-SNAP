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
 
 defined( '_JEXEC' ) or die( 'Restricted access' );  
 
setlocale(LC_ALL, 'C.UTF-8', 'C'); 
 
jimport( 'joomla.application.component.controller');
jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jdownloads'.DS.'tables');

class JDownloadsHelper
{	

    /*
     * Configure the Linkbar.
     *
     * @param    string    The name of the active view.
     */
    public static function addSubmenu($vName = 'jdownloads')
    {
        $canDo = self::getActions();
        
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_CPANEL' ), 'index.php?option=com_jdownloads', $vName == 'jdownloads');
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_CATEGORIES' ), 'index.php?option=com_jdownloads&view=categories', $vName == 'categories');
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_DOWNLOADS' ), 'index.php?option=com_jdownloads&view=downloads', $vName == 'downloads');    
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_FILES' ), 'index.php?option=com_jdownloads&view=files', $vName == 'files');
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_LICENSES' ), 'index.php?option=com_jdownloads&view=licenses', $vName == 'licenses');
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_LAYOUTS' ), 'index.php?option=com_jdownloads&view=layouts', $vName == 'layouts');
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_LOGS' ), 'index.php?option=com_jdownloads&view=logs', $vName == 'logs');
        
        if ($canDo->get('edit.user.limits')) {
            JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_USER_GROUPS' ), 'index.php?option=com_jdownloads&view=groups', $vName == 'groups');
        }
        
        if ($canDo->get('edit.config')) {
            JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_CONFIGURATION' ), 'index.php?option=com_jdownloads&view=config', $vName == 'config');
            JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_TOOLS' ), 'index.php?option=com_jdownloads&view=tools', $vName == 'tools');
        }    
        
        JHtmlSidebar::addEntry( JText::_( 'COM_JDOWNLOADS_TERMS_OF_USE' ), 'index.php?option=com_jdownloads&view=info', $vName == 'info');
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param    int     id
     * @param    string  $assetSection (access section name from access.xml)
     * @return   JObject
     */
    public static function getActions($id = 0, $assetSection = '')
    {
        jimport('joomla.access.access');

        $user    = JFactory::getUser();
        $result    = new JObject;
        
        if (empty($id)){
            $assetName = 'com_jdownloads';
            $section   = 'component';
        } else {
            $assetName = 'com_jdownloads.'.$assetSection.'.'.(int) $id;
            if ( $assetSection != '' ){
                if ($assetSection == 'category'){
                    $section   = 'category';
                } else {
                    $section   = 'download';
                }
            }       
        }
        
        $actions = JAccess::getActions('com_jdownloads', $section);
        foreach ($actions as $action){
                 $result->set($action->name, $user->authorise($action->name, $assetName));
        }
        return $result;        
    }
    
    /**
     * Method to get the versions number from jDownloads
     * @return string version value
     */
    public static function getjDownloadsVersion()
    {
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        
        $xmlitems = '';
        
        $file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jdownloads'.DS.'jdownloads.xml';
        if (JFile::exists($file)) {
            if ($data = JApplicationHelper::parseXMLInstallFile($file)) {
                foreach($data as $key => $value) {
                    $xmlitems[$key] = $value;
                }
                if (isset($xmlitems['version']) && $xmlitems['version'] != '' ) {
                    return $xmlitems['version'];
                } else {
                    return 'Not defined!';
                }
            }
        } else {
            return 'Can not get jDownloads version number!';
        }
    }
    
    // get the plugin info to view it in the logs table list header
    public static function getLogsHeaderInfo(){
         global $jlistConfig;
         
         if (!$jlistConfig['activate.download.log']){
               return JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_SETTINGS_OFF');
           } else {
               $plugin = JPluginHelper::getPlugin('system', 'jdownloads');
               if (!$plugin){
                   // plugin is set off
                   return JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_INFO').' '.JText::_('COM_JDOWNLOADS_SYSTEM_PLUGIN_OFF_MSG');
               }    
               $pluginParams = json_decode($plugin->params);

               $reduce_log_data = (int)$pluginParams->reduce_log_data_sets_to;
               if ($reduce_log_data > 0){
                  return JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_INFO').' '.sprintf(JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_REDUCE_ON'), $reduce_log_data);
               } else {
                  return JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_INFO').' '.JText::_('COM_JDOWNLOADS_BACKEND_LOG_LIST_REDUCE_OFF');
               }  
           }  
    }
    
    
    // get download stats data to view it in cpanel  
    public static function getDownloadStatsData() {
        $db = JFactory::getDBO();
        
        $db->setQuery('SELECT COUNT(*) FROM #__jdownloads_categories WHERE level > 0');
        $sum_cats = intval($db->loadResult());
        $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_files");
        $sum_files = intval($db->loadResult());
        $db->setQuery("SELECT SUM(downloads) FROM #__jdownloads_files");
        $sum_downloads = intval($db->loadResult());
        $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_files WHERE published = 0");
        $sum_files_unpublished = intval($db->loadResult());
        $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_categories WHERE published = 0");
        $sum_cats_unpublished = intval($db->loadResult());        
        $color = '#990000';
        $stats = str_replace('#1', '<font color="'.$color.'"><b>'.self::strToNumber($sum_files).'</b></font>', JText::_('COM_JDOWNLOADS_BACKEND_CP_STATS_TEXT'));
        $stats = str_replace('#2', '<font color="'.$color.'"><b>'.self::strToNumber($sum_cats).'</b></font>', $stats);
        $stats = str_replace('#3', '<font color="'.$color.'"><b>'.self::strToNumber($sum_downloads).'</b></font>', $stats);
        $stats = str_replace('#4', '<font color="'.$color.'"><b>'.self::strToNumber($sum_cats_unpublished).'</b></font>', $stats);
        $stats = str_replace('#5', '<font color="'.$color.'"><b>'.self::strToNumber($sum_files_unpublished).'</b></font>', $stats);
        $data['stats'] = $stats;
        $data['cats_public'] = self::strToNumber($sum_cats - $sum_cats_unpublished);
        $data['files_public'] = self::strToNumber($sum_files - $sum_files_unpublished);
        $data['cats_not_public'] = self::strToNumber($sum_cats_unpublished);
        $data['files_not_public'] = self::strToNumber($sum_files_unpublished);
        return $data;
    }

    // read sum of files for a given cat id
    public static function getSumDownloadsFromCat($catid) {       
       $db = JFactory::getDBO();
       $db->setQuery('SELECT COUNT(*) FROM #__jdownloads_files WHERE cat_id = '.$catid);
       $sum = $db->loadResult();
       return $sum;
    }

    // get the root and the current path from the given cat_dir
    public static function getSplittedCategoryDirectoryPath($cat_dir) {       
        $cat_dir_path = new JObject;
        $cat_dir_path->current = substr(strrchr($cat_dir,"/"),1);
        if (!$cat_dir_path->current){
            $cat_dir_path->current = $cat_dir;
        } else {   
            $path_pos = strrpos ( $cat_dir, "/" );
            $cat_dir_path->root = substr($cat_dir, 0, $path_pos + 1);
        }
        return $cat_dir_path;
    }    
        
    
    // build all select boxes for the config view
    public static function getConfigSelectFields() { 
        
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
        
        $db = JFactory::getDBO();
        
        $select_fields = array();
        
        // select box for use tabs option
        $tabs = array();
        $tabs[] = JHtml::_('select.option', '0', JText::_('COM_JDOWNLOADS_NO'));
        $tabs[] = JHtml::_('select.option', '1', JText::_('COM_JDOWNLOADS_BACKEND_SET_USE_TABS_BOX_SLIDERS'));
        $tabs[] = JHtml::_('select.option', '2', JText::_('COM_JDOWNLOADS_BACKEND_SET_USE_TABS_BOX_TABS'));
        $select_fields['tabs_box'] = JHtml::_('select.genericlist', $tabs, 'jlistConfig[use.tabs.type]', 'size="1" class="inputbox"', 'value', 'text',  $jlistConfig['use.tabs.type']);
        
        $robots = array();
        $robots[] = JHtml::_('select.option', '', JText::_('JGLOBAL_USE_GLOBAL'));
        $robots[] = JHtml::_('select.option', 'index, follow', JText::_('JGLOBAL_INDEX_FOLLOW'));
        $robots[] = JHtml::_('select.option', 'noindex, follow', JText::_('JGLOBAL_NOINDEX_FOLLOW'));
        $robots[] = JHtml::_('select.option', 'index, nofollow', JText::_('JGLOBAL_INDEX_NOFOLLOW'));            
        $robots[] = JHtml::_('select.option', 'noindex, nofollow', JText::_('JGLOBAL_NOINDEX_NOFOLLOW'));
        $select_fields['robots'] = $robots;            
        
        $list_sortorder = array();
        $list_sortorder[] = JHtml::_('select.option', '0', JText::_('COM_JDOWNLOADS_DOWNLOADS_ORDERING'));
        $list_sortorder[] = JHtml::_('select.option', '1', JText::_('COM_JDOWNLOADS_DOWNLOADS_MOST_RECENT_FIRST'));
        $list_sortorder[] = JHtml::_('select.option', '2', JText::_('COM_JDOWNLOADS_DOWNLOADS_OLDEST_FIRST'));
        $list_sortorder[] = JHtml::_('select.option', '3', JText::_('COM_JDOWNLOADS_TITLE_ALPHABETICAL'));
        $list_sortorder[] = JHtml::_('select.option', '4', JText::_('COM_JDOWNLOADS_TITLE_REVERSE_ALPHABETICAL'));
        $list_sortorder[] = JHtml::_('select.option', '5', JText::_('COM_JDOWNLOADS_DOWNLOADS_MOST_HITS'));
        $list_sortorder[] = JHtml::_('select.option', '6', JText::_('COM_JDOWNLOADS_DOWNLOADS_LEAST_HITS'));
        $list_sortorder[] = JHtml::_('select.option', '7', JText::_('COM_JDOWNLOADS_DOWNLOADS_AUTHOR_ALPHABETICAL'));
        $list_sortorder[] = JHtml::_('select.option', '8', JText::_('COM_JDOWNLOADS_DOWNLOADS_AUTHOR_REVERSE_ALPHABETICAL'));

                
        $select_fields['list_sortorder'] = $list_sortorder;
        
        $cats_sortorder = array();
        $cats_sortorder[] = JHtml::_('select.option', '0', JText::_('COM_JDOWNLOADS_CATEGORY_MANAGER_ORDER'));
        $cats_sortorder[] = JHtml::_('select.option', '1', JText::_('COM_JDOWNLOADS_TITLE_ALPHABETICAL'));
        $cats_sortorder[] = JHtml::_('select.option', '2', JText::_('COM_JDOWNLOADS_TITLE_REVERSE_ALPHABETICAL'));
        $select_fields['cats_sortorder'] = $cats_sortorder;

        $pluploader_runtime = array();
        $pluploader_runtime[] = JHtml::_('select.option', 'full', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_ALL'));
        $pluploader_runtime[] = JHtml::_('select.option', 'html5', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_HTML5'));
        $pluploader_runtime[] = JHtml::_('select.option', 'flash', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_FLASH'));
        $pluploader_runtime[] = JHtml::_('select.option', 'gears', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_GEARS'));
        $pluploader_runtime[] = JHtml::_('select.option', 'silverlight', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_SILVERLIGHT'));
        $pluploader_runtime[] = JHtml::_('select.option', 'browserplus', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_BROSWERPLUS'));
        $pluploader_runtime[] = JHtml::_('select.option', 'html4', JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_HTML4'));
        $select_fields['pluploader_runtime'] = $pluploader_runtime;
        
        $pluploader_unit = array();
        $pluploader_unit[] = JHtml::_('select.option', 'b', 'B');
        $pluploader_unit[] = JHtml::_('select.option', 'kb', 'KB');
        $pluploader_unit[] = JHtml::_('select.option', 'mb', 'MB');
        $select_fields['pluploader_unit'] = $pluploader_unit;        
        
        // select list for default catsymbol
        $cat_pic_dir = '/images/jdownloads/catimages/'; 
        $cat_pic_dir_path = JUri::root().'images/jdownloads/catimages/';
        $pic_files = JFolder::files( JPATH_SITE.$cat_pic_dir );
        $cat_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($pic_files as $file) {
            if (@preg_match( "/(gif|jpg|png)/i", $file )){ 
                $cat_pic_list[] = JHtml::_('select.option', $file );
            }
        }
        
        $select_fields['inputbox_pic'] = JHtml::_('select.genericlist', $cat_pic_list, 'cat_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.cat_pic.options[selectedIndex].value!='') {document.imagelib.src='$cat_pic_dir_path' + document.adminForm.cat_pic.options[selectedIndex].value} else {document.imagelib.src=''}\"", 'value', 'text', $jlistConfig['cat.pic.default.filename'] );
      
        // select list for default filesymbol
        $file_pic_dir = '/images/jdownloads/fileimages/';
        $file_pic_dir_path = JUri::root().'images/jdownloads/fileimages/';
        $pic_files = JFolder::files( JPATH_SITE.$file_pic_dir );
        $file_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($pic_files as $file) {
            if (@preg_match( "/(gif|jpg|png)/i", $file )){ 
                $file_pic_list[] = JHtml::_('select.option', $file );
            }
        }
        $select_fields['inputbox_pic_file'] = JHtml::_('select.genericlist', $file_pic_list, 'file_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.file_pic.options[selectedIndex].value!='') {document.imagelib2.src='$file_pic_dir_path' + document.adminForm.file_pic.options[selectedIndex].value} else {document.imagelib2.src=''}\"", 'value', 'text', $jlistConfig['file.pic.default.filename'] );

        // select list for default featured symbol
        $featured_pic_dir = '/images/jdownloads/featuredimages/';
        $featured_pic_dir_path = JUri::root().'images/jdownloads/featuredimages/';
        $pic_files = JFolder::files( JPATH_SITE.$featured_pic_dir );
        $featured_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($pic_files as $file) {
            if (@preg_match( "/(gif|jpg|png)/i", $file )){ 
                $featured_pic_list[] = JHtml::_('select.option', $file );
            }
        }
        $select_fields['inputbox_pic_featured'] = JHtml::_('select.genericlist', $featured_pic_list, 'featured_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.featured_pic.options[selectedIndex].value!='') {document.imagelib2b.src='$featured_pic_dir_path' + document.adminForm.featured_pic.options[selectedIndex].value} else {document.imagelib2b.src=''}\"", 'value', 'text', $jlistConfig['featured.pic.filename'] );

      
        // auswahlliste for hot image
        $hot_pic_dir = '/images/jdownloads/hotimages/';
        $hot_pic_dir_path = JUri::root().'images/jdownloads/hotimages/';
        $hot_files = JFolder::files( JPATH_SITE.$hot_pic_dir );
        $hot_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($hot_files as $hotfile) {
            if (@preg_match( "/(gif|jpg|png)/i", $hotfile )){ 
                $hot_pic_list[] = JHtml::_('select.option', $hotfile );
            }
        }    
        
        $select_fields['inputbox_hot'] = JHtml::_('select.genericlist', $hot_pic_list, 'hot_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.hot_pic.options[selectedIndex].value!='') {document.imagelib3.src='$hot_pic_dir_path' + document.adminForm.hot_pic.options[selectedIndex].value} else {document.imagelib3.src=''}\"", 'value', 'text', $jlistConfig['picname.is.file.hot'] );
          
        // auswahlliste for new image
        $new_pic_dir = '/images/jdownloads/newimages/';
        $new_pic_dir_path = JUri::root().'images/jdownloads/newimages/';
        $new_files = JFolder::files( JPATH_SITE.$new_pic_dir );
        $new_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($new_files as $newfile) {
            if (@preg_match( "/(gif|jpg|png)/i", $newfile )){ 
                $new_pic_list[] = JHtml::_('select.option', $newfile );
            }
        }    
        
        $select_fields['inputbox_new'] = JHtml::_('select.genericlist', $new_pic_list, 'new_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.new_pic.options[selectedIndex].value!='') {document.imagelib4.src='$new_pic_dir_path' + document.adminForm.new_pic.options[selectedIndex].value} else {document.imagelib4.src=''}\"", 'value', 'text', $jlistConfig['picname.is.file.new'] );
                
        // auswahlliste for download image
        $down_pic_dir = '/images/jdownloads/downloadimages/';
        $down_pic_dir_path = JUri::root().'images/jdownloads/downloadimages/'; 
        $down_files = JFolder::files( JPATH_SITE.$down_pic_dir );
        $down_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($down_files as $downfile) {
            if (@preg_match( "/(gif|jpg|png)/i", $downfile )){ 
                $down_pic_list[] = JHtml::_('select.option', $downfile );
            }
        }    
        
        $select_fields['inputbox_down'] = JHtml::_('select.genericlist', $down_pic_list, 'down_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.down_pic.options[selectedIndex].value!='') {document.imagelib5.src='$down_pic_dir_path' + document.adminForm.down_pic.options[selectedIndex].value} else {document.imagelib5.src=''}\"", 'value', 'text', $jlistConfig['download.pic.details'] ); 
      
        $select_fields['inputbox_down2'] = JHtml::_('select.genericlist', $down_pic_list, 'down_pic2', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.down_pic2.options[selectedIndex].value!='') {document.imagelib9.src='$down_pic_dir_path' + document.adminForm.down_pic2.options[selectedIndex].value} else {document.imagelib9.src=''}\"", 'value', 'text', $jlistConfig['download.pic.files'] ); 
      
        $select_fields['inputbox_mirror_1'] = JHtml::_('select.genericlist', $down_pic_list, 'mirror_1_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.mirror_1_pic.options[selectedIndex].value!='') {document.imagelib6.src='$down_pic_dir_path' + document.adminForm.mirror_1_pic.options[selectedIndex].value} else {document.imagelib6.src=''}\"", 'value', 'text', $jlistConfig['download.pic.mirror_1'] );
      
        $select_fields['inputbox_mirror_2'] = JHtml::_('select.genericlist', $down_pic_list, 'mirror_2_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.mirror_2_pic.options[selectedIndex].value!='') {document.imagelib7.src='$down_pic_dir_path' + document.adminForm.mirror_2_pic.options[selectedIndex].value} else {document.imagelib7.src=''}\"", 'value', 'text', $jlistConfig['download.pic.mirror_2'] );  
      
         // for plugin
         $select_fields['inputbox_down_plg'] = JHtml::_('select.genericlist', $down_pic_list, 'down_pic_plg', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.down_pic_plg.options[selectedIndex].value!='') {document.imagelib10.src='$down_pic_dir_path' + document.adminForm.down_pic_plg.options[selectedIndex].value} else {document.imagelib10.src=''}\"", 'value', 'text', $jlistConfig['download.pic.plugin'] ); 

        // auswahlliste for update image
        $upd_pic_dir = '/images/jdownloads/updimages/';
        $upd_pic_dir_path = JUri::root().'images/jdownloads/updimages/';
        $upd_files = JFolder::files( JPATH_SITE.$upd_pic_dir );
        $upd_pic_list[] = JHtml::_('select.option', '', '');
        foreach ($upd_files as $updfile) {
            if (@preg_match( "/(gif|jpg|png)/i", $updfile )){ 
                $upd_pic_list[] = JHtml::_('select.option', $updfile );
            }
        }    
        
        $select_fields['inputbox_upd'] = JHtml::_('select.genericlist', $upd_pic_list, 'upd_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.upd_pic.options[selectedIndex].value!='') {document.imagelib8.src='$upd_pic_dir_path' + document.adminForm.upd_pic.options[selectedIndex].value} else {document.imagelib8.src=''}\"", 'value', 'text', $jlistConfig['picname.is.file.updated'] );
        
        // for content file plugin by pelma
        // check if exists
        $file_plugin_path =  JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'jdownloads'.DS.'jdownloads.php';        
        if (file_exists($file_plugin_path)) {
            $db->setQuery("SELECT template_name  FROM #__jdownloads_templates WHERE template_typ = 2");
            $templaterows = $db->loadObjectList();
            $file_templates = array();
            $templatecnt = 0;
            foreach ($templaterows as $templaterow) {
                $file_templates[] = JHtml::_('select.option', $templaterow->template_name, $templaterow->template_name);
                $templatecnt++;
            }
            $select_fields['file_plugin_inputbox'] = JHtml::_('select.genericlist', $file_templates, "jlistConfig[fileplugin.defaultlayout]" , 'size="6" class="inputbox"', 'value', 'text', $jlistConfig['fileplugin.defaultlayout'] );
            $select_fields['file_plugin_inputbox2'] = JHtml::_('select.genericlist', $file_templates, "jlistConfig[fileplugin.layout_disabled]" , 'size="6" class="inputbox"', 'value', 'text', $jlistConfig['fileplugin.layout_disabled'] );  
        }                
        
        return $select_fields;
        
    }    
    
    
    public static function yesnoSelectList($tag_name, $tag_attribs, $selected)
    {
        $arr = array(
        JHtml::_('select.option', 1, JText::_('COM_JDOWNLOADS_YES' ) ),
        JHtml::_('select.option', 0, JText::_('COM_JDOWNLOADS_NO' ) ),
        );
        return JHtml::_('select.genericlist', $arr, $tag_name, $tag_attribs, 'value', 'text', (int) $selected );
    }
    
    
    /**
    * @desc   check whether the selected upload file is a picture
    * 
    * @return boolean
    * 
    */
    public function fileIsPicture($filename)
    {
        jimport( 'joomla.filesystem.file' );
        
        $types = array('png','gif','jpg','jpeg');
        $pictype = JFile::getExt($filename);
        
        if (in_array(strtolower($pictype), $types)){
            return true;
        } else {
            return false;
        }    
    }
    
    /**
    * @desc   check whether the selected upload file is a picture
    * 
    * @return boolean
    * 
    */
    public function fileIsImage($filetype)
    {
        if ((($filetype == 'image/gif') || ($filetype == "image/jpeg") || ($filetype == "image/jpg") || ($filetype == "image/png"))){
            return true;
        } else {
            return false;
        }
    }    

    /**
    * @desc     Check whether the selected upload file is a picture. 
    *           If so, try to get an image size, so we are sure that we have not a fake pic.
    * 
    * @param    array   $file
    * 
    * @return   boolean
    * 
    */    
    public function imageFileIsValid($file)
    {
        // GD lib is required
        try {
            $size = getimagesize($file);
            if ($size){
                $result = self::isBadImageFile($file);
                if ($result === true){
                    // bad code found
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false; 
        }
    }         
            
    public static function fsize($file) 
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
    
    public static function return_bytes ($size_str)
    {
        switch (substr ($size_str, -1))
        {
            case 'M': case 'm': return (int)$size_str * 1048576;
            case 'K': case 'k': return (int)$size_str * 1024;
            case 'G': case 'g': return (int)$size_str * 1073741824;
            default: return $size_str;
        }
    }
    
    /**
     * A function to check file from bad codes.
     *
     * @param (string) $file - file path.
     * @return boolean  true = bad code found
     * 
     */
    public static function isBadImageFile($file)
    {
        if (file_exists($file))
        {
            $filedata = fopen($file, 'rb');
            $contents = fread($filedata, filesize($file));
            fclose($filedata);
 
            $check = array('<script', 'javascript:', '<?php', '$_GET', '$_POST', '$_COOKIE', '$_SERVER', '$HTTP', 'system(', 'exec(', 'passthru', 'eval(', '<input', '<frame', '<iframe');
            foreach($check as $chk){
                if(strpos($contents, strtolower($chk)) !== false){
                    return true;
                } 
            } 
            return false;     
        } else {
           return false;
        }
    }
            
    /**
    * @desc   search by file name from backend files list
    * 
    * @return array  - result with founded files 
    * 
    */
    public static function arrayRegexSearch ( $strPattern, $arHaystack, $bTarget = TRUE, $bReturn = TRUE ) 
    { 
        $arResults = array (); 
        foreach ( $arHaystack as $strKey => $strValue ) 
        { 
          $strHaystack = $strValue['name']; 
          if ( !$bTarget ) 
          { 
            $strHaystack = $strKey; 
          } 
          if ( preg_match ( $strPattern, $strHaystack ) ) 
          { 
            if ( $bReturn ) 
            { 
              $arResults[] = $strKey; 
            } 
            else 
            { 
              $arResults[] = $strValue; 
            } 
          } 
        } 
        if ( count ( $arResults ) ) 
        { 
          return $arResults; 
        } 
        return FALSE; 
    }     
    
      
    // can be removed in next release
    // is moved to getStringURLSafe()
    //
    public function checkFileName($name){

        return $name;    
    } 
    
    /*
    * Read user group settings and limitations from jDownloads user groups table
    *
    * @return array     $jd_user_settings 
    */
    public static function getUserRules(){
        
         $db   = JFactory::getDBO();
         $user = JFactory::getUser();
         $groups_id = $user->getAuthorisedViewLevels();
         
         if (!$groups_id) $groups_id[] = 1; // user is not registered = guest
         
         $groups_ids = implode(',', $groups_id);
         $sql = 'SELECT * FROM #__jdownloads_usergroups_limits WHERE group_id IN (' . $groups_ids. ')';
         $db->setQuery($sql);
         $jd_user_settings = $db->loadObjectList();

         if (count($jd_user_settings) == 1){
             // user is only in a single group
             return $jd_user_settings[0];
         } else {
             // user is in multi groups
             // so we must get the group with the highest permission levels
             // default groups:
             // 1. super users ID = 8
             // 2. admin       ID = 7
             // 3. manager     ID = 6
             // 4. publisher   ID = 5
             // 5. editor      ID = 4
             // 6. author      ID = 3
             // 7. registered  ID = 2
             // 8. guest       ID = 9
             // 9. public      ID = 1
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
             if (in_array('4', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '4');
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
             if (in_array('9', $groups_id)) {
                 $key = self::findUserGroupID($jd_user_settings, '9');
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
    * Make sure that we have a valid data for user groups after installation
    *
    * @return  boolean
    */
    public static function setUserRules(){
        
         $db     = JFactory::getDBO();
         
        // check whether this is the first run, then the table is empty
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_usergroups_limits');
        $db->setQuery($query);
        $jd_groups = $db->loadObjectList();
        $amount_jd_groups = count($jd_groups);
         
        if ($amount_jd_groups  == 0){

                // get the joomla usergroups
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__usergroups');
                $db->setQuery($query);
                $joomla_groups = $db->loadObjectList();
                $amount_joomla_groups = count($joomla_groups);

                // add the missing joomla user groups in jD groups
                if ($joomla_groups){
                   for ($i=0; $i < count($joomla_groups); $i++) {
                        $query = $db->getQuery(true);
                        $query->select('*');
                        $query->from('#__jdownloads_usergroups_limits');
                        $query->where('group_id = '.(int)$joomla_groups[$i]->id);
                        $db->setQuery($query);
                        if (!$result = $db->loadResult()){
                            // add the joomla group to the jD groups
                            $query = $db->getQuery(true);
                            $query->insert('#__jdownloads_usergroups_limits');
                            // add group_id
                            $query->set('group_id = '.$db->quote($joomla_groups[$i]->id));
                            // add default msg for timer
                            $query->set('countdown_timer_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_COUNTDOWN_MSG_TEXT')));
                            // add default msg for limits
                            $query->set('download_limit_daily_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_DAILY_MSG')));
                            $query->set('download_limit_weekly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_WEEKLY_MSG')));
                            $query->set('download_limit_monthly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_MONTHLY_MSG')));
                            // volume
                            $query->set('download_volume_limit_daily_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_DAILY_MSG')));
                            $query->set('download_volume_limit_weekly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_WEEKLY_MSG')));
                            $query->set('download_volume_limit_monthly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_MONTHLY_MSG')));
                            
                            $query->set('how_many_times_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_HOW_MANY_TIMES_MSG')));
                            $query->set('upload_limit_daily_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_UPLOAD_LIMIT_DAILY_MSG')));
                            
                            $query->set('view_user_his_limits_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_USER_HIS_LIMITS_MSG')));
                            
                            // create some default values - also for editing or creating Downloads in Frontend
                            if ((int)$joomla_groups[$i]->id == 8){
                                $query->set('uploads_allowed_types = '.$db->quote('zip,rar,pdf,txt,doc,gif,png,jpg'));
                                $max = (int)ini_get('upload_max_filesize') * 1024;
                                $query->set('uploads_maxfilesize_kb = '.$db->quote($max));
                                $query->set('uploads_max_amount_images = '.$db->quote('10'));
                                $query->set('uploads_can_change_category = '.$db->quote('1'));
                                $query->set('uploads_auto_publish = '.$db->quote('1'));
                                $query->set('uploads_use_editor = '.$db->quote('1'));
                            } else {
                                $query->set('uploads_allowed_types = '.$db->quote('zip,rar,pdf,txt'));
                                $max = (int)ini_get('upload_max_filesize') * 1024;
                                if ($max > 5120) $max = 5120;
                                $query->set('uploads_maxfilesize_kb = '.$db->quote($max));
                                $query->set('uploads_max_amount_images = '.$db->quote('3'));
                                $query->set('uploads_can_change_category = '.$db->quote('1'));
                                $query->set('uploads_auto_publish = '.$db->quote('0'));
                                $query->set('uploads_use_editor = '.$db->quote('1'));
                            }
                            $query->set('uploads_allowed_preview_types = '.$db->quote('mp3,mp4'));
                            $query->set('download_limit_after_this_time = '.$db->quote('60'));
                            $query->set('transfer_speed_limit_kb = '.$db->quote('0'));
                            $query->set('download_limit_daily = '.$db->quote('0'));
                            $query->set('download_limit_weekly = '.$db->quote('0'));
                            $query->set('download_limit_monthly = '.$db->quote('0'));
                            $query->set('upload_limit_daily = '.$db->quote('0'));
                            $query->set('view_captcha = '.$db->quote('0'));
                            $query->set('view_report_form = '.$db->quote('0'));
                            $query->set('countdown_timer_duration = '.$db->quote('0'));
                            $query->set('download_volume_limit_daily = '.$db->quote('0'));
                            $query->set('download_volume_limit_weekly = '.$db->quote('0'));
                            $query->set('download_volume_limit_monthly = '.$db->quote('0'));                             
                            
                            if ((int)$joomla_groups[$i]->id == 1){ 
                               $query->set('importance = '.$db->quote(1)); 
                            } elseif ((int)$joomla_groups[$i]->id == 2){ 
                                $query->set('importance = '.$db->quote(20));
                            } elseif ((int)$joomla_groups[$i]->id == 3){ 
                                $query->set('importance = '.$db->quote(30));
                            } elseif ((int)$joomla_groups[$i]->id == 4){ 
                                $query->set('importance = '.$db->quote(40));
                            } elseif ((int)$joomla_groups[$i]->id == 5){ 
                                $query->set('importance = '.$db->quote(50));
                            } elseif ((int)$joomla_groups[$i]->id == 6){ 
                                $query->set('importance = '.$db->quote(60));
                            } elseif ((int)$joomla_groups[$i]->id == 7){ 
                                $query->set('importance = '.$db->quote(70));
                            } elseif ((int)$joomla_groups[$i]->id == 8){ 
                                $query->set('importance = '.$db->quote(100));
                            } else {
                                $query->set('importance = '.$db->quote(0));
                            }
                                                        
                            $db->setQuery($query);   
                            if (!$db->execute()){
                                $this->setError($db->getErrorMsg());
                                return false;
                            }                        
                        }               
                   }
                }        
        }
        return true; 

    }    
    
    public static function getXMLdata($fileandpath, $filename){
        global $jlistConfig;
        jimport( 'joomla.filesystem.archive' );
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');    
        $files = array();
        $xml_files = array();
        $xmltags = array();
        $path_parts = pathinfo($fileandpath);
        $destination_dir = $jlistConfig['files.uploaddir'].DS.'tempzipfiles'.DS.$path_parts['filename'];
        if ($ok = JFolder::create($destination_dir.DS)){
            if(JArchive::extract($fileandpath, $destination_dir.DS)){
                // get files list
                $xml_files = self::scan_dir($destination_dir.DS, $type=array('.xml','.XML'), $only=false, $allFiles=false, $recursive=TRUE, $onlyDir='', $except_folders='', $jd_root='', $files);
                if ($xml_files){            
                    foreach($xml_files as $key => $array2) {
                       $filepath[] = $xml_files[$key]['path'].DS.$xml_files[$key]['file'];
                    }
                    // $xml_file = usort($filepath, "cmp_str"); 
                    foreach($filepath as $fpath){
                       $xmltags = self::use_xml($fpath);
                       // get xml file tags
                       if ($xmltags[name] != ''){
                           self::delete_dir_and_allfiles($destination_dir.DS);
                           return $xmltags;
                           break; 
                       }    
                    }
               }    
            }
            // delete all unzipped files and folder
            self::delete_dir_and_allfiles($destination_dir.DS);
        } 
        return false;     
    }

    public static function use_xml($u_xml){
        // function by JoomTools
        $felder = array("name","author","authorUrl", "authorMail", "creationDate","copyright","license","version","description");
        foreach($felder as $feld){
            $wert =preg_replace("/\s\s+/","",stripslashes(self::read_xml("<$feld>(.*)</$feld>",$u_xml)));
            $wert =str_replace(chr(91), '-', str_replace(chr(93), '-', $wert));
            $wert =ereg_replace("<!-CDATA-", "", $wert);
            $wert =ereg_replace("-->", "", $wert);
            $tag[$feld] = $wert;
        }
        return $tag;
    }

    public static function read_xml($search,$xmlfile){
        // function by JoomTools
        $fp = fopen($xmlfile,"r");
        while(!feof($fp)){
            $r_xml .= fgets($fp);
        }
        fclose($fp);
        eregi($search, $r_xml, $search_result1);
        $search_result = trim($search_result1[1]);
        return $search_result;
    }

    // fill file data from a given xml install file
    public static function fillFileDateFromXML($row, $xmltags){
        $database = JFactory::getDBO();   
        $lic_id = '';
        if ($xmltags['license']){
            $database->setQuery("SELECT id FROM #__jdownloads_licenses WHERE title LIKE '%".$xmltags['license']."%' OR url LIKE '%".$xmltags['license']."%'");
            $lic_id = $database->loadResult();                                      
        }
        $row->file_title = htmlspecialchars_decode($xmltags['name'], ENT_QUOTES); 
        $row->file_alias = JApplication::stringURLSafe($row->file_title);

        if(trim(str_replace('-','',$row->file_alias)) == '') {
           // get current 'now' data with correct local time zone
           $datenow = JFactory::getDate()->toSql();  // True to return the date string in the local time zone, false to return it in GMT.
           $row->file_alias = $datenow;
        }
        $row->release          = htmlspecialchars_decode($xmltags['version'], ENT_QUOTES);
        $row->description      = htmlspecialchars_decode($xmltags['description'], ENT_QUOTES); 
        $row->description_long = $row->description;
        if (!$lic_id){                                                           
            $row->license      = '';
        } else {
            $row->license      = (int)$lic_id;
        }    
        if ($date = strtotime($xmltags['creationDate'])){
            $row->file_date    = JHtml::_('date', $xmltags['creationDate'],'Y-m-d H:i:s');
        } else {
            $row->file_date    = '0000-00-00 00:00:00';
        }     
        $row->url_home         = $xmltags['authorUrl'];
        $row->author           = $xmltags['author'];
        $row->url_author       = $xmltags['authorMail'];
        return $row->file_title;
    }                   
        
        
    // Get the filesize from a given file url
    public static function urlfilesize($url) {
        if (substr($url,0,4)=='http' || substr($url,0,3)=='ftp') {
            $size = array_change_key_case(get_headers($url, 1),CASE_LOWER);
            $size = $size['content-length'];
            if (is_array($size)) { $size = $size[1]; }
        } else {
            $size = @filesize($url); 
        }
        $a = array("B", "KB", "MB", "GB", "TB", "PB");

        $pos = 0;
        while ($size >= 1024) {
               $size /= 1024;
               $pos++;
        }
        return round($size,2)." ".$a[$pos];    
    } 
             
    /**
    *  Get the external file date
    * 
    * @param mixed $url
    */
    public static function urlfiledate($url){
        if (file_exists($url)){
            $aktuell = date("Y-m-d H:i:s",filemtime($url));
        } else {
            $aktuell = date("Y-m-d H:i:s");
        }    
      return $aktuell;
    }
    
    /**
    * Check whether we have a valid URL
    * 
    * @param mixed $url
    * @return boolean true when valid
    */
    public static function urlValidate($url)
    {
        $url = trim($url);
        if (preg_match('%^(?:(?:https?)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $url)){
            return true;
        }
        return false;
    }    
              

    /* Create a new thumb from a given pic
     *
     * @param mixed $hight_new  only used when in config is activated the 'create all thumbs new option')
     * @param mixed $width_new  only used when in config is activated the 'create all thumbs new option')
    */
    public static function create_new_thumb($picturepath, $height_new = 0, $width_new = 0) {
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
        
        // get info about GD installation
        if (function_exists('gd_info')) {
            $gda = gd_info();
            $gd['version'] = $gda['GD Version'];
            $gd['num'] = preg_replace('/[a-zA-Z\s()]+/','',$gda['GD Version']);
            $gd['freetype'] = $gda["FreeType Support"];
            $gd['gif_read'] = $gda["GIF Read Support"];
            $gd['gif_make'] = $gda["GIF Create Support"];
            $gd['jpg'] = $gda["JPEG Support"];
            $gd['png'] = $gda["PNG Support"];
        }
        
        $thumbpath = JPATH_SITE.'/images/jdownloads/screenshots/thumbnails/';
        
        if (!is_dir($thumbpath)){
            @mkdir("$thumbpath", 0755);
            // copy also an empty index.html
            JFile::copy(JPATH_SITE.'/images/jdownloads/screenshots/index.html', $thumbpath.'index.html');
        }    
        
        if ($width_new > 0){
            $newwidth = $width_new;
        } else {        
            $newwidth = $jlistConfig['thumbnail.size.width'];
        }
        $newsize = $newwidth;    

        if ($height_new > 0){
            $newheight = $height_new;
        } else {        
            $newheight = $jlistConfig['thumbnail.size.height'];
        }         

        $filename = basename($picturepath);
        $thumbfilename = $thumbpath.$filename;
        
        if (file_exists($thumbfilename)){
           return true;
        }   
        
        /* check that file exist */
        if(!file_exists($picturepath)) {
            return false;
        }
        
        /* get mime type */
        $size=getimagesize($picturepath);
        switch($size[2]) {
            case "1":
            $oldpic = imagecreatefromgif($picturepath);
            break;
            case "2":
            $oldpic = imagecreatefromjpeg($picturepath);
            break;
            case "3":
            $oldpic = imagecreatefrompng($picturepath);
            break;
            default:
            return false;
        }
        /* get old image dimensions */
        $width = $size[0];
        $height = $size[1]; 

        /* set new image dimensions */
        // but we will not 'stretch' smaller images
        if ($width < $newwidth || $height < $newheight){
            $newwidth  = $width;
            $newheight = $height;
        } else {
            if($width >= $height) {
                $newwidth = $newsize;
                $newheight = $newsize * $height / $width;
            } else {
                $newheight = $newsize;
                $newwidth = $newsize * $width / $height;
            }            
        }

        /* create new image with new dimensions */
        $newpic = imagecreatetruecolor($newwidth,$newheight);
        
        // Set alphablending to false to get a transparency background
        imagealphablending($newpic, false);
        imagesavealpha($newpic,true);
        
        /* resize it */
        // imagecopyresized will copy and scale and image. This uses a fairly primitive algorithm that tends to yield more pixelated results.
        //imagecopyresized($newpic,$oldpic,0,0,0,0,$newwidth,$newheight,$width,$height);
        // imagecopyresampled will copy and scale and image, it uses a smoothing and pixel interpolating algorithm that will generally yield much better results then imagecopyresized at the cost of a little cpu usage.
        imagecopyresampled($newpic,$oldpic,0,0,0,0,$newwidth,$newheight,$width,$height);  
        // store the image
        switch($size[2]){
            case "1":    return imagegif($newpic, $thumbfilename);
            break;
            case "2":    return imagejpeg($newpic, $thumbfilename);
            break;
            case "3":    return imagepng($newpic, $thumbfilename);
            break;
        }
        // delete the used memory
        imagedestroy($oldpic);
        imagedestroy($newpic);
    }
    
    /* Create a new image from a uploaded pic and store it in the screenshot folder
     *
     * 
     * 
     */
    public static function create_new_image($picturepath) {
        global $jlistConfig;
        
        $thumbpath = JPATH_SITE.'/images/jdownloads/screenshots/';
        
        if (!is_dir($thumbpath)){
            @mkdir("$thumbpath", 0755);
            // copy also an empty index.html
            JFile::copy(JPATH_SITE.'/images/jdownloads/index.html', $thumbpath.'index.html');
        }    
        
        $maxwidth = $jlistConfig['create.auto.thumbs.from.pics.image.width'];
        $maxheight = $jlistConfig['create.auto.thumbs.from.pics.image.height'];
        
        $thumbfilename = $thumbpath.basename($picturepath);
        
        if (file_exists($thumbfilename)){
           return true;
        }   
        
        /* check that file exist */
        if(!file_exists($picturepath)) {
            return false;
        }
        
        /* get mime type */
        $size=getimagesize($picturepath);
        switch($size[2]) {
            case "1":
            $oldpic = imagecreatefromgif($picturepath);
            break;
            case "2":
            $oldpic = imagecreatefromjpeg($picturepath);
            break;
            case "3":
            $oldpic = imagecreatefrompng($picturepath);
            break;
            default:
            return false;
        }
        /* get old image dimensions */
        $width = $size[0];
        $height = $size[1]; 
        
        /* set new image dimensions */
        // but we will not 'stretch' smaller images
        if ($width < $maxwidth || $height < $maxheight){
            $newwidth  = $width;
            $newheight = $height;
        } else {        
            if ($width/$maxwidth > $height/$maxheight) {
                $newwidth = $maxwidth;
                $newheight = $maxwidth*$height/$width;
            } else {
                $newheight = $maxheight;
                $newwidth = $maxheight*$width/$height;
            }
        }
        
        $newpic = imagecreatetruecolor($newwidth,$newheight);
        imagealphablending($newpic,false);
        imagesavealpha($newpic,true);
        
        // resize it 
        imagecopyresampled($newpic,$oldpic,0,0,0,0,$newwidth,$newheight,$width,$height); 
        // store the image
        switch($size[2]) {
            case "1":    return imagegif($newpic, $thumbfilename);
            break;
            case "2":    return imagejpeg($newpic, $thumbfilename);
            break;
            case "3":    return imagepng($newpic, $thumbfilename);
            break;
        }
        // delete the used memory
        imagedestroy($oldpic);
        imagedestroy($newpic);
    }


    /* Create a thumnail from a pdf file
     *
     * 
     * 
     *
     */ 
    public static function create_new_pdf_thumb($target_path, $only_name, $thumb_path, $screenshot_path){
        global $jlistConfig;    
        
        $pdf_thumb_file_name = '';
        
        if (extension_loaded('imagick')){ 
            // create small thumb
            $image = new Imagick($target_path.'[0]');
            $image -> setImageIndex(0);
            $image -> setImageFormat($jlistConfig['pdf.thumb.image.type']);
            $image -> scaleImage($jlistConfig['pdf.thumb.height'],$jlistConfig['pdf.thumb.width'],1);
            $pdf_thumb_file_name = $only_name.'.'.strtolower($jlistConfig['pdf.thumb.image.type']);
            $image->writeImage($thumb_path.$only_name.'.'.strtolower($jlistConfig['pdf.thumb.image.type']));
            $image->clear();
            $image->destroy();
            // create big thumb
            $image = new Imagick($target_path.'[0]');
            $image -> setImageIndex(0);
            $image -> setImageFormat($jlistConfig['pdf.thumb.image.type']);
            $image -> scaleImage($jlistConfig['pdf.thumb.pic.height'],$jlistConfig['pdf.thumb.pic.width'],1);
            $image->writeImage($screenshot_path.$only_name.'.'.strtolower($jlistConfig['pdf.thumb.image.type']));
            $image->clear();
            $image->destroy();    
        }
        return $pdf_thumb_file_name; 
    }        
    
    /* Recreate all thumbs with new size
     * Used when configuration saved 
     * 
     * 
    */ 
    public static function resizeAllThumbs($hight_new, $width_new)
    {
        // first delete all old thumbs
        $thumb_dir = JPATH_SITE.'/images/jdownloads/screenshots/thumbnails/';
        $screen_dir = JPATH_SITE.'/images/jdownloads/screenshots/';
        // this files shall not be delete
        $exceptions[] = 'index.html';
        self::delete_dir_and_allfiles($thumb_dir, false, $exceptions );
        $except_folders = array();  // folders which not shall be scanned
        $files     = array();
        $jd_root   = '';
        $only      = TRUE;
        $type      = array("png","jpg","gif");
        $allFiles  = false;
        $recursive = FALSE;
        $onlyDir   = FALSE;
        $ok = self::scan_dir($screen_dir, $type, $only, $allFiles, $recursive, $onlyDir, $except_folders, $jd_root, $files);
        if ($ok){
            foreach ($files as $pic){
                  $result = self::create_new_thumb($pic['path'].$pic['file'], $hight_new, $width_new);
            }
            return JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_CREATE_ALL_MESSAGE');         
        }                        
            
    }
    
    public function check_joomla_group($group, $inherited){
        $user = JFactory::getUser();
        $user_id = $user->get('id');
        
        if($inherited){
            //include inherited groups
            jimport( 'joomla.access.access' );
            $groups = JAccess::getGroupsByUser($user_id);
        }else{
            //exclude inherited groups
            $user =& JFactory::getUser($user_id);
            $groups = isset($user->groups) ? $user->groups : array();
        }
        $return = 0;
        
        if(in_array($group, $groups)){
           $return = true;
        }
        return $return;
    }

    
    // run download from backend
    public static function downloadFile($cid, $type = ''){
        global $jlistConfig;

        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        
        $app = JFactory::getApplication(); 
        $db = JFactory::getDBO();    
        clearstatcache(); 
        
        $view_types = array();
        $view_types = explode(',', $jlistConfig['file.types.view']);
        
        // get path
        $db->SetQuery("SELECT * FROM #__jdownloads_files WHERE file_id = $cid");
        $file = $db->loadObject();

        if ($type == 'prev'){
            if ($file->preview_filename){
                $file = $jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'].DS.$file->preview_filename; 
            }
        } else {
            if ($file->url_download){
                if ($file->cat_id > 1){ 
                    // 'uncategorised' download is NOT selected
                    $db->SetQuery("SELECT cat_dir, cat_dir_parent FROM #__jdownloads_categories WHERE id = $file->cat_id");
                    $cat_dirs = $db->loadObject();
                    // build the complete stored category path
                    if ($cat_dirs->cat_dir_parent != ''){
                        $cat_dir = $cat_dirs->cat_dir_parent.DS.$cat_dirs->cat_dir;
                    } else {
                        $cat_dir = $cat_dirs->cat_dir;
                    }
                    
                    $filename_direct = $jlistConfig['files.uploaddir'].DS.$cat_dir.DS.$file->url_download;
                    $file = $jlistConfig['files.uploaddir'].DS.$cat_dir.DS.$file->url_download; 
                } else {
                    // 'uncategorised' download IS selected
                    $file = $jlistConfig['files.uploaddir'].DS.$jlistConfig['uncategorised.files.folder.name'].DS.$file->url_download;
                }    
            }    
        } 

        if (!jFile::exists($file)){
            exit;
        }        
        
        $len = filesize($file);
        
        // if set the option for direct link to the file
        if (!$jlistConfig['use.php.script.for.download']){
            if (empty($filename_direct)) {
                $app->redirect($file);
            } else {
                $app->redirect($filename_direct);
            }
        } else {    
            $filename = basename($file);
            $file_extension = jFile::getExt($filename);
            $ctype = self::datei_mime($file_extension);
            ob_end_clean();
            // needed for MS IE - otherwise content disposition is not used?
            if (ini_get('zlib.output_compression')){
                ini_set('zlib.output_compression', 'Off');
            }
            
            header("Cache-Control: public, must-revalidate");
            header('Cache-Control: pre-check=0, post-check=0, max-age=0');
            // header("Pragma: no-cache");  // Problems with MS IE
            header("Expires: 0"); 
            header("Content-Description: File Transfer");
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
            header("Content-Type: " . $ctype);
            header("Content-Length: ".(string)$len);
            if (!in_array($file_extension, $view_types)){
                header('Content-Disposition: attachment; filename="'.$filename.'"');
            } else {
              // view file in browser
              header('Content-Disposition: inline; filename="'.$filename.'"');
            }   
            header("Content-Transfer-Encoding: binary\n");
            
            // set_time_limit doesn't work in safe mode
            if (!ini_get('safe_mode')){ 
                @set_time_limit(0);
            }
            @readfile($file);
        }
        exit;
    }

    public static function datei_mime($filetype) {
        
        switch ($filetype) {
            case "ez":  $mime="application/andrew-inset"; break;
            case "hqx": $mime="application/mac-binhex40"; break;
            case "cpt": $mime="application/mac-compactpro"; break;
            case "doc": $mime="application/msword"; break;
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
            case "ppt": $mime="application/vnd.ms-powerpoint"; break;
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
    
    /* Remove the assigned file from a download on the server and clean the url_download field
     *
     * @param   string  id 
     * 
     * @return    void
    */   
    public static function deleteFile($id){
        global $jlistConfig;

        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        
        $app = JFactory::getApplication(); 
        $db = JFactory::getDBO();    
        
        // get path
        $db->SetQuery("SELECT * FROM #__jdownloads_files WHERE file_id = $id");
        $file = $db->loadObject();

        if ($file->url_download){
            if ($file->cat_id > 1){
                // get the cat folder path
                $db->SetQuery('SELECT cat_dir, cat_dir_parent FROM #__jdownloads_categories WHERE id = '.$file->cat_id);
                $cat_dirs = $db->loadObject();
                if ($cat_dirs->cat_dir_parent != ''){
                    $cat_dir = $cat_dirs->cat_dir_parent.'/'.$cat_dirs->cat_dir;
                } else {
                    $cat_dir = $cat_dirs->cat_dir;
                }
                $filename = $jlistConfig['files.uploaddir'].DS.$cat_dir.DS.$file->url_download;
            } else {
                // uncategorised 
                $filename = $jlistConfig['files.uploaddir'].DS.$jlistConfig['uncategorised.files.folder.name'].DS.$file->url_download;
            }
            
            if (!jFile::exists($filename)){
                // file not exist - but we must always clear the data field 
                $db->SetQuery("UPDATE #__jdownloads_files SET url_download = '', size = '' WHERE file_id = '$id'");
                $db->execute();
                return false; 
            } else {
                if (jFile::delete($filename)){
                    $db->SetQuery("UPDATE #__jdownloads_files SET url_download = '', size = '' WHERE file_id = '$id'");
                    $db->execute();                    
                    return true;
                } else {
                    // delete error
                    return false;
                }    
            }
        } else {
            // url_download empty
            return false;
        }
    }        

    /* Remove the assigned preview file from a download on the server and clean the preview_filename field
     *
     * @param   string  id 
     * 
     * @return    void
    */   
    public static function deletePreviewFile($id){
        global $jlistConfig;

        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        
        $app = JFactory::getApplication(); 
        $db = JFactory::getDBO();    
        
        // get path
        $db->SetQuery("SELECT * FROM #__jdownloads_files WHERE file_id = $id");
        $file = $db->loadObject();

        if ($file->preview_filename){
            $filename = $jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'].DS.$file->preview_filename;

            // check whether other downloads use also this preview file
            $db->SetQuery("SELECT count(*) FROM #__jdownloads_files WHERE preview_filename = '$file->preview_filename'");
            $result = $db->loadResult();
            
            if (!jFile::exists($filename) || $result > 1){
                // file not exist - but we must always clear the data field 
                // the same when other downloads used also this file
                $db->SetQuery("UPDATE #__jdownloads_files SET preview_filename = '' WHERE file_id = '$id'");
                $db->execute();
                return false; 
            } else {
                if (jFile::delete($filename)){
                    $db->SetQuery("UPDATE #__jdownloads_files SET preview_filename = '' WHERE file_id = '$id'");
                    $db->execute();                    
                    return true;
                } else {
                    // delete error
                    return false;
                }    
            }
        } else {
            // preview_filename field empty
            return false;
        }
    }        
    
    /* Remove a folder from the download area (categories folder)
     *
     * @param   string  $cat_dir  Only the given sub path from the DB cat_dir field 
     * 
     * @return error_msg
    */
    public static function deleteCategoryFolder($cat_dir){

        global $jlistConfig; 
        jimport('joomla.filesystem.folder');   
    
        $dir = $jlistConfig['files.uploaddir'].'/'.$cat_dir;
        if (JFolder::exists($dir)){
            if (!JFolder::delete($dir)){
                JError::raiseWarning(100, JText::sprintf('COM_JDOWNLOADS_BE_DEL_CATS_DIRS_ERROR', $dir));
            } else {
                JError::raiseNotice(100, JText::sprintf('COM_JDOWNLOADS_BE_DEL_CATS_DIRS_OK', $dir));
            }
        } else {
            JError::raiseWarning(100, JText::sprintf('COM_JDOWNLOADS_BE_DEL_CATS_DIRS_ERROR', $dir));
        }
    }    
    
    /**
    * Methode to move all folders, subfolders and files to a other folder
    * 
    * @param mixed $source
    * @param mixed $dest
    * @param mixed $recursive
    * @param mixed $message
    * @param mixed $delete_source             when true, are all files and folders in the source path deleted after moving
    * @param mixed $delete_dest               when true, are all files and folders in the destination path deleted after moving 
    * @param mixed $delete_only_files         when true and $delete_source or $delete_source is true, are only the files in the selected subfolders deleted, excepts index.html
    * 
    * @return error_msg
    */
    public static function moveDirs($source, $dest, $recursive = true, $message, $delete_source = true, $delete_dest = false, $delete_only_files = false) {

        $error = false;
        
        if (!is_dir($dest)) { 
            @mkdir($dest); 
          } 
     
        $handle = @opendir($source);
        
        if(!$handle) {
            $message = JText::_('COM_JDOWNLOADS_BACKEND_CATSEDIT_ERROR_CAT_COPY');
            return $message;
        }
        
        while ($file = @readdir ($handle)) {
            if ($file == '.' || $file == '..'){
                continue;
            }
            
            if(!$recursive && $source != $source.$file."/") {
                if(is_dir($source.$file))
                    continue;
            }
            
            if(is_dir($source.$file)) {
                self::moveDirs($source.$file."/", $dest.$file."/", $recursive, $message, $delete_source, $delete_dest, $delete_only_files );
            } else {
                if (!@copy($source.$file, $dest.$file)) {
                    $error = true;
                }
            }
        }
        @closedir($handle);
        
        // delete $source when not an error
        if (!$error){
            if ($delete_dest){
                $path = $dest;
            } else {
                $path = $source;
            }
            if ($delete_source || $delete_dest){
                if ($delete_only_files){
                    // delete all files and folders from the source path
                    $exceptions = array('index.html');
                    $res = self::delete_dir_and_allfiles ($path, false, $exceptions);    
                    if ($res) {
                        $message = JText::sprintf('COM_JDOWNLOADS_BACKEND_CATSEDIT_ERROR_CAT_DEL_AFTER_COPY', $path);
                    }
                } else {   
                    // delete all files and folders from the source path
                    $res = self::delete_dir_and_allfiles ($path);    
                    if ($res) {
                        $message = JText::sprintf('COM_JDOWNLOADS_BACKEND_CATSEDIT_ERROR_CAT_DEL_AFTER_COPY', $path);
                    }
                }    
            }    
        } else {
            $message = JText::_('COM_JDOWNLOADS_BACKEND_CATSEDIT_ERROR_CAT_COPY');
        }
        return $message;
    } 

    
    /**
     * This method checked an given folder or file name
     * 
     * @param   string  $str                String to process
     *          boolean $is_monitoring      Is true, when this method is used from the auto monitoring function
     * 
     * @return  string  Processed string
     */
    public static function getCleanFolderFileName($str, $is_monitoring = false)
    {
        global $jlistConfig;
        
        // We must use always the transliteration.  
        // Here are replaced all accented UTF-8 characters by unaccented ASCII-7 "equivalents".
        // We can not use here the Joomla 'transliterate' function, since the Joomla function changed the string to lowercase 
        // So we use an own modified version
        include_once dirname(__FILE__) . '/transliterate.php';
        $str = JDTransliterate::utf8_latin_to_ascii($str);    
        
        if ($is_monitoring){
            // for auto monitoring is always used the 'fix.upload.filename.specials' option
            $str = preg_replace('/(\s|[^A-Za-z0-9._\-])+/', ' ', $str); 
        } else {
            if ($jlistConfig['use.unicode.path.names']){
                
                // Replace double byte whitespaces by single byte (East Asian languages)
                $str = preg_replace('/\xE3\x80\x80/', ' ', $str);

                // Replace forbidden characters by whitespaces
                $str = preg_replace('#[:\#\*"@+=;!><&\%()\]\/\'\\\\|\[]#', "\x20", $str);

                // Delete all '?'
                $str = str_replace('?', '', $str);
                
            } else {
                
                if ($jlistConfig['fix.upload.filename.specials']){       

                    // Is only done when the utf-8 option is not activated        
                    // Remove any duplicate whitespace, and ensure all characters are alphanumeric
                    $str = preg_replace('/(\s|[^A-Za-z0-9._\-])+/', ' ', $str); 
                }              
            }
        }    
        // Trim white spaces at beginning and end of string
        $str = trim($str);
        
        // make lowercase when this option is activated
        if ($jlistConfig['fix.upload.filename.uppercase']){
            $str = JString::strtolower($str);
        }          
        
        // Remove all whitespace
        if ($jlistConfig['fix.upload.filename.blanks']){
            $str = str_replace(' ', '_', $str);
        }
        
        if (strlen($str) == 0){
            // we can not use a empty folder/filename
            // so we use the current date for it
            if ( '\\' === DIRECTORY_SEPARATOR ){
                // windows system can not store a : character in name
                $str = date("Y-m-d H-i-s");
            } else {
                $str = date("Y-m-d H:i:s");
            }    
        } 
        
        return $str;
    }    
    
      
   /**
    *  Method to get the title from a user group 
    * 
    *  @param int       user group id
    *
    *  @return string   title
    * 
    */    
    public static function getUserGroupInfos($group_id)
    {
        $db = JFactory::getDBO();
        $result = '';
        
        $query = $db->getQuery(true);
        $query->select('title');
        $query->from('#__usergroups');
        $query->where('id = '.(int)$group_id);
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }    
    
    /**
    *  Method to get the id from jDownloads component in the assets table
    * 
    *  @param 
    *
    *  @return int   id
    * 
    */
    public static function getAssetRootID()
    {    
        $db = JFactory::getDBO();
        $result = '';
        
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__assets');
        $query->where('name = '.$db->Quote('com_jdownloads'));
        $query->where('parent_id = '.$db->Quote('1'));
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;    
    }                                           
    
    // get amount of downloads
    public static function getSumDownloads() {
        $db = JFactory::getDBO();
  
        $db->SetQuery("SELECT COUNT(*) FROM #__jdownloads_files");
        $sum = $db->loadResult();
        return $sum;
    }
    

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
    
    /**
     * Build the array with all finded file informations
     * (Path, Folder name, File name, File size, last update date 
     *
     * @param		string	$dir 			path to the folder
     * @param		string	$file			contains the file name
     * @param		string	$onlyDir		contains only the folder name
     * @param		array	$type		    search pattern for file types
     * @param		bool	$allFiles	    find all files and used not file types filter
     * @param		array	$files		    contains the complete folder structure
     * @return	    array					the complete results array
     * 
     */
    public static function buildArray($dir,$file,$onlyDir,$type,$allFiles,$files) {

	    $typeFormat = FALSE;
	    foreach ($type as $item)
      {
  	    if (strtolower($item) == substr(strtolower($file), -strlen($item)))
			    $typeFormat = TRUE;
	    }

	    if($allFiles || $typeFormat == TRUE)
	    {
		    if(empty($onlyDir))
			    $onlyDir = substr($dir, -strlen($dir), -1);
		    $files[$dir.$file]['path'] = $dir;
		    $files[$dir.$file]['file'] = $file;
		    $files[$dir.$file]['size'] = self::fsize($dir.$file);
		    $files[$dir.$file]['date'] = filemtime($dir.$file);
	    }
	    return $files;
    }

    /**
    *  Get all folders with files from a given path
    *  But files with a single or double quote character in the filename are ignored for security reasons ! 
    * 
    * @param mixed $dir
    * @param mixed $type
    * @param mixed $only
    * @param mixed $allFiles
    * @param mixed $recursive
    * @param mixed $onlyDir
    * @param mixed $except_folders
    * @param mixed $jd_root
    * @param mixed $files
    * 
    * @return array
    */
    public static function scan_dir($dir, $type=array(), $only=FALSE, $allFiles=FALSE, $recursive=TRUE, $onlyDir="", $except_folders, $jd_root, &$files)
    {
        $len = strlen($jd_root);
        
        $handle = @opendir($dir);
        if(!$handle) return false;
        
        while ($file = @readdir ($handle)){
            
            if ($file == '.' || $file == '..' || $file == 'index.html' || strpos($file, "'") > 0 || strpos($file, '"') > 0 || in_array($dir, $except_folders)){           
                continue;
            }
            
            if(!$recursive && $dir != $dir.$file."/"){
                if(is_dir($dir.$file)) 
                 continue;
            }
            
            if(is_dir($dir.$file)){
                self::scan_dir($dir.$file."/", $type, $only, $allFiles, $recursive, $file, $except_folders, $jd_root, $files);
            } else {
               if($only)
                 $onlyDir = $dir;
                 if ($dir != $jd_root){
                     $files = self::buildArray($dir,$file,$onlyDir,$type,$allFiles,$files);
                 }    
            }
        }
        
        @closedir($handle);
        return $files;
    }

    /**
    *  Get all folders and subfolders
    * 
    * @param mixed $path        path to browse
    * @param mixed $maxdepth    how deep to browse (-1=unlimited)
    * @param mixed $mode        "FULL"|"DIRS"|"FILES"
    * @param mixed $d           must not be defined
    * @param mixed $except_folders
    * 
    * @return array
    */
    public static function searchdir ( $path , $maxdepth = -1 , $mode = "DIRS" , $d = 0, $except_folders ) {
       
       if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
       $dirlist = array () ;
       if ( $mode != "FILES" ) {
           if (!in_array($path, $except_folders)){
               $dirlist[] = $path ;
           }    
       }
       if ( $handle = opendir ( $path ) ) {
           while ( false !== ( $file = readdir ( $handle ) ) ) {
               if ( $file != '.' && $file != '..' && substr($file, 0, 1) !== '.') {
                   $file = $path . $file ;
                   if ( ! is_dir ( $file ) ) {
                      if ( $mode != "DIRS" ) {
                       $dirlist[] = $file ;
                      }
                   }
                   elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) ) {
                       $result = self::searchdir ( $file . '/' , $maxdepth , $mode , $d + 1, $except_folders ) ;
                       $dirlist = array_merge ( $dirlist , $result ) ;
                   }
               }
           }
           closedir ( $handle ) ;
       }
       if ( $d == 0 ) { 
           natcasesort ( $dirlist ) ;
       }
       return ( $dirlist ) ;
   }

    /**
    * Delete a folder with all files and subfolders 
    * 
    * @param mixed $path           the path to the folder
    * @param mixed $delete_folder  true, when the folder shall also be deleted
    * @return mixed 
    * RESULTS:
    *   0 - ok
    *  -1 - no folder
    *  -2 - delete error
    *  -3 - a item was not a file/folder/Link
    */
    public static function delete_dir_and_allfiles ( $path, $delete_folder = true, $exceptions = array() ) {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');    

        if (!is_dir ($path)) {
            return -1;
        }
        $dir = @opendir ($path);
        if (!$dir) {
            return -2;
        }

        while (($entry = @readdir($dir)) !== false) {
            if ($entry == '.' || $entry == '..' || in_array($entry, $exceptions)) continue;
            if (is_dir ($path.'/'.$entry)) {
                $res = self::delete_dir_and_allfiles ($path.'/'.$entry, $delete_folder, $exceptions);
                // manage errors
                if ($res == -1) {
                    @closedir ($dir); 
                    return -2; 
                } else if ($res == -2) {
                    @closedir ($dir); 
                    return -2; 
                } else if ($res == -3) {
                    @closedir ($dir); 
                    return -3; 
                } else if ($res != 0) { 
                    @closedir ($dir); 
                    return -2; 
                }
            } else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {
                // delete file
                $res = JFile::delete($path.'/'.$entry);
                if (!$res) {
                    @closedir ($dir);
                    return -2; 
                }
            } else {
                @closedir ($dir);
                return -3;
            }
        }
        @closedir ($dir);
        
        // delete dir when defined
        if ($delete_folder && !$exceptions){
            $res = JFolder::delete($path);
            if (!$res) {
                return -2;
            }
        }
            
        return 0;
    }

    // get the value from a given downloads 'file date' field
    public static function getFieldDataFromDownload($id, $fieldname){
        $db = JFactory::getDBO();
        $db->setQuery("SELECT $fieldname FROM #__jdownloads_files WHERE file_id = '$id'");
        $value = $db->loadResult();
        return $value;        
    }
    
    /**
    * remove the language tag from a given text and return only the text
    *    
    * @param string     $msg
    */
    public static function getOnlyLanguageSubstring($msg)
    {
        // Get the current locale language tag
        $lang_key   = self::getLangKey();        
        
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
    * get the current used 'locale' language key 
    *    
    * @return string
    */    
    public static function getLangKey()
    {
        $lang        = JFactory::getLanguage();
        $locale      = $lang->getLocale();
        $lang_code   = null;

        if(empty($locale)){
            $lang_code = 'en-GB';
        } else {
            $lang_tag   = $locale[0];
            $lang_data  = explode('.', $lang_tag);
            $lang_code  = JString::str_ireplace('_', '-', $lang_data[0]);
        }
        return $lang_code;    
    }    
    
    
    /**
    * Auto Monitoring
    * Scan all Folders and Files use the reults to update the DB tables 
    * 
    */
    public static function runMonitoring()
    {
        global $jlistConfig;
        
        ini_set('max_execution_time', '600');
        ignore_user_abort(true);
        flush();

        $model_category = JModelLegacy::getInstance( 'Category', 'jdownloadsModel' );
        $model_download = JModelLegacy::getInstance( 'Download', 'jdownloadsModel' );
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        $db = JFactory::getDBO();
        
        //check if all files and dirs in the uploaddir directory are listed
        if($jlistConfig['files.autodetect']){
            
            if(file_exists($jlistConfig['files.uploaddir']) && $jlistConfig['files.uploaddir'] != ''){
              $startdir     = $jlistConfig['files.uploaddir'].'/';
              $dir_len      = strlen($startdir);

              // define the params for scan_dir() results
              $dir          = $startdir;
              $only         = FALSE;
              $type         = array();
              if ($jlistConfig['all.files.autodetect']){
                  $allFiles     = true;
              } else {   
                  $allFiles     = FALSE;
                  $type =  explode(',', $jlistConfig['file.types.autodetect']);
              }    
              $recursive    = TRUE; 
              $onlyDir      = TRUE; 
              $files        = array();
              $file         = array();

              
              $dirlist      = array();
              
              $new_files       = 0;
              $new_dirs_found  = 0;
              $new_dirs_create = 0;
              $new_dirs_errors = 0;
              $new_dirs_exists = 0;
              $new_cats_create = 0;
              $log_message     = '';
              $success         = FALSE;   
              
              $log_array = array();          
              
              // ********************************************   
              // first search new categories
              // ********************************************   
              
              clearstatcache();
              $jd_root      = $jlistConfig['files.uploaddir'].'/';
              $temp_dir     = $jd_root.$jlistConfig['tempzipfiles.folder.name'].'/';
              $uncat_dir    = $jd_root.$jlistConfig['uncategorised.files.folder.name'].'/';
              $preview_dir  = $jd_root.$jlistConfig['preview.files.folder.name'].'/';
              $private_dir  = $jd_root.$jlistConfig['private.area.folder.name'].'/';
              
              $except_folders = array($temp_dir, $uncat_dir, $preview_dir, $private_dir);
              
              $searchdirs   = array();
              $dirlist = self::searchdir($jd_root, -1, 'DIRS', 0, $except_folders);
              
              $no_writable = 0;
              for ($i=0; $i < count($dirlist); $i++) {
                  // no tempzifiles directory
                  if(strpos($dirlist[$i], $jlistConfig['private.area.folder.name'].'/') === FALSE) {
                      if (!is_writable($dirlist[$i])){
                          $no_writable++;
                      }
                      $dirlist[$i] = str_replace($jd_root, '', $dirlist[$i]);
                      // delete last slash /
                      if ($pos = strrpos($dirlist[$i], '/')){
                        $searchdirs[] = substr($dirlist[$i], 0, $pos);
                      }
                  }
              }  
              unset($dirlist);
              
              $count_cats = count($searchdirs);
              
              for ($i=0; $i < count($searchdirs); $i++) {
                 $dirs = explode('/', $searchdirs[$i]);
                 $sum = count($dirs);
                 
                   // check that folder exist
                   if ($sum == 1){
                       $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_categories WHERE cat_dir = '$searchdirs[$i]'");
                       $cat_dir_parent_value = '';
                       $cat_dir_value = $dirs[0];
                   } else {
                       $pos = strrpos($searchdirs[$i], '/');
                       $cat_dir_parent_value = substr($searchdirs[$i], 0, $pos);
                       $cat_dir_value = substr($searchdirs[$i], $pos +1);
                       $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_categories WHERE cat_dir = '$cat_dir_value' AND cat_dir_parent = '$cat_dir_parent_value'");                   
                   }    
                   $cat_exist = $db->loadResult(); 
                   
                   // when not exist - add it
                   if (!$cat_exist) {
                       $new_dirs_found++;
                       $parent_cat = '';
                       
                       // get the right parent_id value
                       if ($sum == 1){
                           // we have a new root cat
                           $parent_id = 1;
                       } else {
                           // find the parent category and get the cat ID
                           $pos = strrpos($cat_dir_parent_value, '/');

                           if ($pos){
                               // we have NOT a first level sub category
                               $cat_dir_parent_value2 = substr($cat_dir_parent_value, 0, $pos);
                               $cat_dir_value2 = substr($cat_dir_parent_value, $pos +1);
                               $db->setQuery("SELECT * FROM #__jdownloads_categories WHERE cat_dir = '$cat_dir_value2' AND cat_dir_parent = '$cat_dir_parent_value2'");
                           } else {
                               // we have a first level sub category
                               $cat_dir_parent_value2 = $cat_dir_parent_value;
                               $cat_dir_value2 = $cat_dir_value;
                               $db->setQuery("SELECT * FROM #__jdownloads_categories WHERE cat_dir = '$cat_dir_parent_value2' AND cat_dir_parent = ''");
                           }                            

                           $parent_cat = $db->loadObject();                   
                           if ($parent_cat){
                               $parent_id = $parent_cat->id;
                           } else {
                               // can not found the parents category for the new child
                               $log_array[] = JText::_('Abort. Can not find parents category for the new folder: ').' <b>'.$searchdirs[$i].'</b><br />';
                               break;
                           }
                       }
                       
                       $cat_dir_value = utf8_encode($cat_dir_value);
                       
                       // we need the original folder title as category title
                       $original_folder_name = $cat_dir_value;
                       
                       // check the founded folder name
                       $checked_cat_dir = self::getCleanFolderFileName( $cat_dir_value, true );
                       
                       // check the folder name result 
                       if ($cat_dir_value != $checked_cat_dir){
                           // build path
                           if ($parent_cat){
                               if ($parent_cat->cat_dir_parent){ 
                                   $cat_dir_path = $jd_root.$parent_cat->cat_dir_parent.'/'.$parent_cat->cat_dir.'/'.$checked_cat_dir;
                                   $new_cat_dir_name = $parent_cat->cat_dir_parent.'/'.$parent_cat->cat_dir.'/'.$checked_cat_dir;
                               } else {
                                   $cat_dir_path = $jd_root.$parent_cat->cat_dir.'/'.$checked_cat_dir;
                                   $new_cat_dir_name = $parent_cat->cat_dir.'/'.$checked_cat_dir;
                               }    
                           } else {
                                $cat_dir_path = $jd_root.$checked_cat_dir;
                                $new_cat_dir_name = $checked_cat_dir;
                           }
                           
                           // rename the folder - when he already exist: make it unique!
                           $num = 1;
                           while (JFolder::exists($cat_dir_path)){
                               $cat_dir_path    = $cat_dir_path.$num;
                               $checked_cat_dir = $checked_cat_dir.$num;
                               $num++;
                           }
                           
                           if (!JFolder::exists($cat_dir_path)){
                               $copied = JFolder::move($jd_root.$searchdirs[$i], $cat_dir_path);
                               if ($copied !== true){
                                   $log_array[] = JText::_('Error! Can not change folder name: ').' <b>'.$searchdirs[$i].'</b><br />';
                               }
                           } else {
                               $log_array[] = JText::_('Error! A folder with the same (cleaned) name exist already: ').' <b>'.$searchdirs[$i].'</b><br />';
                           }
                           $cat_dir_value = $checked_cat_dir;  
                           
                           // update the name in the folder list
                           $searchdirs[$i] = $new_cat_dir_name;
                       }
                       
                       // set access 
                       if ($parent_cat){
                           $access = $parent_cat->access;
                       } else {
                           $access = 1;
                       }

                       // set alias
                       $alias = JApplication::stringURLSafe($cat_dir_value);

                       // set note hint
                       $note = JText::_('COM_JDOWNLOADS_RUN_MONITORING_NOTE_TEXT');

                       // build table array
                       $data = array (
                            'id' => 0,
                            'parent_id' => $parent_id,
                            'title' => $original_folder_name,
                            'alias' => $alias,
                            'notes' => $note,
                            'description' => '',
                            'cat_dir' => $cat_dir_value,
                            'cat_dir_parent' => $cat_dir_parent_value,
                            'pic' => $jlistConfig['cat.pic.default.filename'],
                            'published' => (int)$jlistConfig['autopublish.founded.files'],
                            'access' => $access,
                            'metadesc' => '',
                            'metakey' => '',
                            'created_user_id' => '0',
                            'language' => '*',
                            'rules' => array(
                                'core.create' => array(),
                                'core.delete' => array(),
                                'core.edit' => array(),
                                'core.edit.state' => array(),
                                'core.edit.own' => array(),
                                'download' => array(),
                            ),
                            'params' => array(),
                       );                   
                       
                       // create new cat in table
                       $create_result = $model_category->createAutoCategory( $data );
                       if (!$create_result){
                           // error message
                           $log_array[] = JText::_('Error! Can not create new category for: ').' <b>'.$searchdirs[$i].'</b><br />';
                       }
                       
                       $new_cats_create++;
                       // copy index.html to the new folder
                       $index_copied = JFile::copy(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jdownloads'.DS.'index.html', $jlistConfig['files.uploaddir'].DS.$searchdirs[$i].DS.'index.html');
                       $log_array[] = JText::_('COM_JDOWNLOADS_AUTO_CAT_CHECK_ADDED').' <b>'.$searchdirs[$i].'</b><br />';
                   }

              }

              ob_flush();
              flush();      
              
              unset($dirs);
              unset($searchdirs);
              
              // ********************************************
              // Exists all published category folders?
              // ********************************************
              
              $mis_cats = 0;
              // get all published categories but not the root
              $db->setQuery("SELECT * FROM #__jdownloads_categories WHERE published = 1 AND id > 1");
              $cats = $db->loadObjectList();
              
              $count_cats = count($cats);
              
              foreach($cats as $cat){

                    if ($cat->cat_dir_parent != ''){
                        $cat_dir = $jd_root.$cat->cat_dir_parent.'/'.$cat->cat_dir;
                    } else {
                        $cat_dir = $jd_root.$cat->cat_dir;
                    }
                    
                    // when it not exist, we must unpublish the category
                    if(!JFolder::exists($cat_dir)){
                        $db->setQuery("UPDATE #__jdownloads_categories SET published = 0 WHERE id = '$cat->id'");
                        $db->execute();
                        $mis_cats++;
                        $log_array[] = '<font color="red">'.JText::_('COM_JDOWNLOADS_AUTO_CAT_CHECK_DISABLED').' <b>'.$cat->cat_dir.'</b></font><br />';
                   } 
              }
              
              unset($cats);
              
              // *********************************************************             
              //  Check all files and create for new founded new Downloads
              // *********************************************************
              
              unset($except_folders[1]);
                         
              $all_dirs = self::scan_dir($dir, $type, $only, $allFiles, $recursive, $onlyDir, $except_folders, $jd_root, $files);
              
              if ($all_dirs != FALSE) {

                  $count_files = count($files);
                  
                  reset ($files);
                  $new_files = 0;
                  
                  foreach($files as $key3 => $array2) {
                      
                      $filename = $files[$key3]['file'];
                      
                      if ($filename != '') {
                             $dir_path_total = $files[$key3]['path'];
                             $restpath = substr($files[$key3]['path'], $dir_len);
                             $only_dirs = substr($restpath, 0, strlen($restpath) - 1);
                             $upload_dir = $jlistConfig['files.uploaddir'].'/'.$only_dirs.'/';

                             $pos = strrpos($only_dirs, '/');
                             if ($pos){
                                $cat_dir_parent_value = substr($only_dirs, 0, $pos);
                                $cat_dir_value = substr($only_dirs, $pos +1);
                             } else {
                                $cat_dir_parent_value = '';
                                $cat_dir_value = $only_dirs;
                             }   
                             
                             // exist still a Download with this filename?
                             $exist_file = false;
                             $db->setQuery("SELECT cat_id FROM #__jdownloads_files WHERE url_download = '".$filename."'");
                             $row_file_exists = $db->loadObjectList();
                             
                             // when exist, get the category from the Download, when we have really assigned a category (ID > 1)
                             if ($row_file_exists && $row_file_exists[0]->cat_id > 1) {
                                foreach ($row_file_exists as $row_file_exist) {
                                    if (!$exist_file) { 
                                        $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_categories WHERE id = '$row_file_exist->cat_id' AND cat_dir = '$cat_dir_value' AND cat_dir_parent = '$cat_dir_parent_value'" );
                                        $row_cat_find = $db->loadResult();               
                                        if ($row_cat_find) {
                                            $exist_file = true;
                                        }     
                                    }
                                }     
                             }  else {
                                 // it can be an 'uncategorised'
                                 if ($row_file_exists && $row_file_exists[0]->cat_id == 1){
                                     $exist_file = true;
                                 } else {   
                                     $exist_file = false;
                                 }    
                             }    
                             
                             // Add the file here in a new Download
                             if(!$exist_file) {
                                    
                                // reset images var
                                $images = '';
                                
                                $only_name = utf8_encode(JFile::stripExt($filename));
                                $file_extension = JFile::getExt($filename); 

                               // $title =  JFilterInput::clean($only_name);
                                $title = JFilterInput::getInstance(null, null, 1, 1)->clean($only_name, 'STRING');
                                
                                // check filename 
                                $filename_new = self::getCleanFolderFileName( $only_name, true).'.'.$file_extension;                                
                                                         
                                if ($only_name == ''){
                                    $msgfile = $startdir.$only_dirs.'/'.$filename;
                                    $log_array[] = "Error. Filename empty after cleaning! Location is: ".$only_dirs.'/'.$filename;
                                }
                                 
                                if ($filename_new != $filename){
                                    $source = $startdir.$only_dirs.'/'.$filename;
                                    $target = $startdir.$only_dirs.'/'.$filename_new;
                                    $success = @rename($source, $target); 
                                    if ($success === true) {
                                        $filename = $filename_new; 
                                    } else {
                                        // could not rename filename
                                        $log_array[] = "Error. Could not rename: $filename to: $filename_new";
                                    }
                                }     

                                $target_path = $upload_dir.$filename;
                                 
                                // find the category for the new founded file in this folder
                                $db->setQuery("SELECT * FROM #__jdownloads_categories WHERE cat_dir = '$cat_dir_value' AND cat_dir_parent = '$cat_dir_parent_value'");
                                $cat = $db->loadObject();
                                 
                                if ($cat){
                                     $catid = $cat->id;
                                     $access = $cat->access;
                                } else {
                                     // it seems that we have a new file in 'uncategorised' folder found
                                     $catid = 1;
                                     $access = 1;
                                }    
                                     
                                $date = JFactory::getDate();
                                $tz = JFactory::getConfig()->get( 'offset' );
                                $date->setTimezone(new DateTimeZone($tz));
                                
                                 $file_extension = JFile::getExt($filename);
                                
                                 // set file size
                                 $file_size =  $files[$key3]['size'];
                                 
                                 // set note hint
                                 $note = JText::_('COM_JDOWNLOADS_RUN_MONITORING_NOTE_TEXT');
                                 
                                 // set creation date
                                 $creation_date = JFactory::getDate()->toSql();
                                 
                                 // set file mime pic
                                 $picpath = strtolower(JPATH_SITE.'/images/jdownloads/fileimages/'.$file_extension.'.png');
                                 if(file_exists($picpath)){
                                    $file_pic  = $file_extension.'.png';
                                 } else {
                                    $file_pic  = $jlistConfig['file.pic.default.filename'];
                                 }
                                 
                                 // create thumbs form pdf
                                 if ($jlistConfig['create.pdf.thumbs'] && $jlistConfig['create.pdf.thumbs.by.scan'] && $file_extension == 'pdf'){
                                       $thumb_path = JPATH_SITE.'/images/jdownloads/screenshots/thumbnails/';
                                       $screenshot_path = JPATH_SITE.'/images/jdownloads/screenshots/';
                                       $pdf_thumb_name = self::create_new_pdf_thumb($target_path, $only_name, $thumb_path, $screenshot_path);
                                       if ($pdf_thumb_name){
                                           $images = $pdf_thumb_name; 
                                       }    
                                 }    
                                     
                                 // create auto thumb when founded file is an image
                                 if ($jlistConfig['create.auto.thumbs.from.pics'] && $jlistConfig['create.auto.thumbs.from.pics.by.scan']){
                                     if ($file_is_image = self::fileIsPicture($filename)){
                                         $thumb_created = self::create_new_thumb($target_path);       
                                         if ($thumb_created){
                                             $images = $filename;
                                             // create new big image for full view
                                             $image_created = self::create_new_image($target_path);
                                         }
                                     }    
                                 }
                                 
                                 $sha1_value = sha1_file($target_path);
                                 $md5_value  =  md5_file($target_path);                             
                                
                                 // build data array
                                 $data = array (
                                    'file_id' => 0,
                                    'cat_id' => $catid,
                                    'file_title' => $title,
                                    'file_alias' => '',
                                    'notes' => $note,
                                    'url_download' => $filename,
                                    'size' => $file_size,
                                    'description' => self::getOnlyLanguageSubstring($jlistConfig['autopublish.default.description']),          
                                    'file_pic' => $file_pic,
                                    'images' => $images,
                                    'date_added' => $creation_date, 
                                    'published' => (int)$jlistConfig['autopublish.founded.files'],
                                    'access' => $access,
                                    'sha1_value' => $sha1_value,
                                    'md5_value' => $md5_value,
                                    'metadesc' => '',
                                    'metakey' => '',
                                    'created_user_id' => '0',
                                    'language' => '*',
                                    'rules' => array(
                                        'core.create' => array(),
                                        'core.delete' => array(),
                                        'core.edit' => array(),
                                        'core.edit.state' => array(),
                                        'core.edit.own' => array(),
                                        'download' => array(),
                                    ),
                                    'params' => array(),
                                 );                                
                                
                                 // create new download in table
                                 $create_result = $model_download->createAutoDownload( $data );
                                 if (!$create_result){
                                    // error message
                                    $log_array[] = "Error. Could not add download for: $filename";
                                 }
                                
                                 $new_files++;
                                 $log_array[] = JText::_('COM_JDOWNLOADS_AUTO_FILE_CHECK_ADDED').' <b>'.$only_dirs.'/'.$filename.'</b><br />';
                          }                   
                      }
                  }  
              }                    
              unset($files);
              flush();

              // ****************************************************             
              // Check whether the assigned files from all published downloads exists
              // - otherwise unpublish this downloads
              // ****************************************************
              
              $mis_files = 0;
              $db->setQuery("SELECT * FROM #__jdownloads_files WHERE published = 1");
              $files = $db->loadObjectList();
              $count_files = count($files);
              
              foreach($files as $file){
                  // we checked only intern stored files
                  if ($file->url_download <> ''){   
                      // get the category path only, when we have not an 'uncategorised' Download  
                      if ($file->cat_id > 1){
                            $db->setQuery("SELECT cat_dir, cat_dir_parent FROM #__jdownloads_categories WHERE id = '$file->cat_id'");
                            $cat = $db->loadObject();
                            if ($cat->cat_dir_parent != ''){
                                $cat_dir_path = $cat->cat_dir_parent.'/'.$cat->cat_dir;
                            } else {
                                $cat_dir_path = $cat->cat_dir;
                            }
                            $file_path = $jd_root.$cat_dir_path.'/'.$file->url_download;
                            $cat_dir = $cat->cat_dir.'/'.$file->url_download;
                      } else {
                          // file in 'uncategorised' folder
                          $file_path = $uncat_dir.$file->url_download;
                          $cat_dir = $file_path;
                      }
                      if(!file_exists($file_path)){
                            $db->setQuery("UPDATE #__jdownloads_files SET published = 0 WHERE file_id = '$file->file_id'");
                            $db->execute();
                            $mis_files++;
                            $log_array[] = '<font color="red">'.JText::_('COM_JDOWNLOADS_AUTO_FILE_CHECK_DISABLED').' <b>'.$cat_dir.'</b></font><br />';
                      }  
                  }
              }
              flush(); 
           
              // save log
              if (count($log_array) > 0){
                  array_unshift($log_array, date(JText::_('DATE_FORMAT_LC2')).':<br />');
              }
              foreach ($log_array as $log) {
                   $log_message .= $log;
              }
              
              // when we have changed anything, we store it in the config
              if ($log_message != ''){
                  $db->setQuery("UPDATE #__jdownloads_config SET setting_value = '$log_message' WHERE setting_name = 'last.log.message'");
                  $db->execute();
                  $jlistConfig['last.log.message'] = $log_message;
              }

                echo '<table width="100%"><tr><td><font size="1" face="Verdana">'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_TITLE').'</font><br />';
                if ($new_cats_create > 0){
                    echo '<font color="#FF6600" size="1" face="Verdana"><b>'.$new_cats_create.' '.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_NEW_CATS').'</b></font><br />';
                } else {
                    echo '<font color="green" size="1" face="Verdana"><b>'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_NO_NEW_CATS').'</b></font><br />';
                }
                
                if ($new_files > 0){
                    echo '<font color="#FF6600" size="1" face="Verdana"><b>'.$new_files.' '.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_NEW_FILES').'</b></font><br />';
                } else {
                    echo '<font color="green" size="1" face="Verdana"><b>'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_NO_NEW_FILES').'</b></font><br />';
                }            
                
                if ($mis_cats > 0){
                    echo '<font color="#990000" size="1" face="Verdana"><b>'.$mis_cats.' '.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_MISSING_CATS').'</b></font><br />';
                } else {
                    echo '<font color="green" size="1" face="Verdana"><b>'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_NO_MISSING_CATS').'</b></font><br />';
                }    
                
                if ($mis_files > 0){
                    echo '<font color="#990000"  size="1" face="Verdana"><b>'.$mis_files.' '.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_MISSING_FILES').'</b><br /></td></tr></table>';
                } else {
                    echo '<font color="green" size="1" face="Verdana"><b>'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_NO_MISSING_FILES').'</b><br /></td></tr></table>';
                }
            
                if ($log_message)  echo '<table width="100%"><tr><td><font size="1" face="Verdana">'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_LOG_TITLE').'<br />'.$log_message.'</font></td></tr></table>';

            } else {

                // error upload dir not exists
                echo '<font color="red"><b>'.JText::sprintf('COM_JDOWNLOADS_AUTOCHECK_DIR_NOT_EXIST', $jlistConfig['files.uploaddir']).'<br /><br />'.JText::_('COM_JDOWNLOADS_AUTOCHECK_DIR_NOT_EXIST_2').'</b></font>';
            }
        }            
    }
    
    /**
    * Rename older language files before we start the update to 2.5/3.x 
    * 
    * 
    *     
    */
    public static function renameOldLanguageFiles($dir)
    {
        
        if ($handle = dir($dir)) {
            while (false !== ($file = $handle->read())) {
                if (!is_dir($dir.'/'.$file)) {
                      if (strpos($file, 'com_jdownloads') !== false){
                           if (strpos($file, 'en-GB') === false && strpos($file, '.old') === false){ 
                               @rename("$dir/$file", "$dir/$file".'.old');
                           }    
                      }
                } elseif (is_dir($dir.'/'.$file) && $file != '.' && $file != '..') {
                    self::renameOldLanguageFiles($dir.'/'.$file);
                }
            }
            $handle->close();
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
    
    /**
     * Show the feature/unfeature links
     *
     * @param   int      $value      The state value
     * @param   int      $i          Row number
     * @param   boolean  $canChange  Is user allowed to change?
     *
     * @return  string       HTML code
     */
    public static function getFeatureHtml($value = 0, $i, $canChange = true)
    {
        JHtml::_('bootstrap.tooltip');

        // Array of image, task, title, action
        $states = array(
            0 => array('unfeatured', 'downloads.featured', 'COM_JDOWNLOADS_UNFEATURED', 'COM_JDOWNLOADS_TOGGLE_FEATURED'),
            1 => array('featured', 'downloads.unfeatured', 'COM_JDOWNLOADS_FEATURED', 'COM_JDOWNLOADS_TOGGLE_FEATURED'),
        );
        $state = JArrayHelper::getValue($states, (int) $value, $states[1]);
        $icon  = $state[0];

        if ($canChange)
        {
            $html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip'
                . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><span class="icon-' . $icon . '"></span></a>';
        }
        else
        {
            $html = '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="'
                . JHtml::tooltipText($state[2]) . '"><span class="icon-' . $icon . '"></span></a>';
        }

        return $html;
    }    
    
}
?>