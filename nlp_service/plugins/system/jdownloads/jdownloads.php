<?php
/**
* @version 2.5
* @package JDownloads
* @copyright (C) 2013 www.jdownloads.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Plugin to handle some special features from jDownloads.
* In v1.2 added the hide function.
* In 2.5 added the new events: onJDUserGroupSettingsBeforeSave and onJDUserGroupSettingsAfterSave
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
//Error_Reporting(E_ERROR);   

jimport('joomla.plugin.plugin');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

if (!defined('DS')){
     define('DS',DIRECTORY_SEPARATOR);
} 

class plgSystemjdownloads extends JPlugin 
{ 
    
     private $caching = 0;
     
    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $params An array that holds the plugin configuration
     */
     public function __construct(& $subject, $params)
     {
             parent::__construct($subject, $params);
             $this->loadLanguage();
             
            // get jD language admin file
            $language = JFactory::getLanguage();
            $language->load('com_jdownloads');
     } 
     
     /**
     * This event is triggered after the framework has dispatched the application.
     * 
     * @param none
     * @return none
     */
     public function onAfterDispatch() {
        
         // function to deactivate partially the Joomla 'cache option' for defined sections
         // inspired by cacheControl plugin from www.crosstec.de
         if( JFactory::getApplication()->isSite() && $this->checkCacheRules()){
            $plugin = JPluginHelper::getPlugin('system', 'jdownloads');
            jimport( 'joomla.html.parameter' );
            $pluginParams = $this->params;
            if($pluginParams->def('enable_again_after_dispatch', 0)){
                JFactory::getConfig()->set('caching', $this->caching);
            }
         }
     }     

     /**
     * This event is triggered after the framework has loaded and the application initialise method has been called.
     * 
     * @param none
     * @return none
     */     
     public function onAfterInitialise() { 
     
         $app = JFactory::getApplication();
         $database = JFactory::getDBO();

         // exist the tables?
         $prefix = $database->getPrefix(); 
         $prefix2 = strtolower($prefix);
         $tablelist = $database->getTableList();
         if ( !in_array ( $prefix.'jdownloads_files', $tablelist ) && !in_array ( $prefix2.'jdownloads_files', $tablelist ) ){
             return;
         } 
         
         $plugin = JPluginHelper::getPlugin('system', 'jdownloads');
         jimport( 'joomla.utilities.utility' );
         // get params
         $params = $this->params;
                  
         //$use_hider = $params->get( 'reduce_log_data_sets_to' );
         //if (!$use_hider) $return = true;   
         
         // No need in admin
         //if( $app->isAdmin() ) return;

         $j = date('Y');
         $m = date('m');
         $d = date('d');
         $h = date('H');
         $min = date('i');
         $sec = date('s');
         
         $unpublish_time = date('Y-m-d H:i:s',mktime($h,$min,$sec,$m,$d-1,$j));
         $now = date('Y-m-d H:i:s');
         
         // get all published files with use the timeframe options
         $database->setQuery("SELECT file_id from #__jdownloads_files WHERE published = 1 AND use_timeframe = 1 AND publish_to != '0000-00-00 00:00:00' AND publish_to <= '$now'");
         $files = $database->loadColumn();
         if ($files){
                $fileslist = implode(',', $files);  
                $database->setQuery("UPDATE #__jdownloads_files SET published = '0', use_timeframe = '0' WHERE file_id IN ('$fileslist')"); 
                $database->execute(); 
         }
         // get all unpublished files with use the timeframe options
         $database->setQuery("SELECT file_id from #__jdownloads_files WHERE published = 0 AND use_timeframe = 1 AND publish_from != '0000-00-00 00:00:00' AND publish_from <= '$now'");
         $files = $database->loadColumn();
         if ($files){
                $fileslist = implode(',', $files);  
                $database->setQuery("UPDATE #__jdownloads_files SET published = '1' WHERE file_id IN ('$fileslist')"); 
                $database->execute(); 
         }
         return;  
     }
     
     public function onJDUserGroupSettingsBeforeSave($type, $table) {
         return true;
     }

     public function onJDUserGroupSettingsAfterSave($type, $table) {
         global $jlistConfig;
         $app = JFactory::getApplication();
         $db  = JFactory::getDBO();
         $result = array();
         
         if ($table->use_private_area == 1) {
             if (is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name'])){
                // get all user IDs from this user group
                $query = $db->getQuery(true);          
                $db->setQuery("SELECT a.id FROM #__users as a LEFT JOIN #__user_usergroup_map AS map ON map.user_id = a.id WHERE map.group_id = '".(int)$table->group_id."'");
                $users = $db->loadObjectList();

                $error = $db->getErrorMsg();
                if ($error){
                    $this->setError($error);
                    return false;
                } 
                
                if (count($users) > 0){
                    $rootfolder = $jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name'].DS;         
                    
                    // create the subfolder for every user when it not exist
                    foreach ($users as $user){
                       if (!JFolder::exists($rootfolder.(int)$user->id)){
                           $result = JFolder::create($rootfolder.(int)$user->id, 0755);
                           if ($result){
                               // copy index.html
                               JFile::copy($rootfolder.'index.html',$rootfolder.(int)$user->id.DS.'index.html');
                           }
                       }
                        
                    }
                }                   
                 
             } else {
                 // folder not writable
                 JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_USERGROUPS_PRIVATE_FILES_AREA_ERROR') );
                 return false;
             }
         }
         return true;
     }
     
     /**
     *   When we use the private area for jD users, we must add for every new user the private folder (when he has the correct usergroup) 
     */
     
     public function onUserAfterSave($user, $isNew, $success) {
         $app = JFactory::getApplication();
         $db  = JFactory::getDBO();
         $groups = array();
         $x = null;
         
         // check at first that we use the private folder option
         $query = $db->getQuery(true);          
         $db->setQuery("SELECT group_id FROM #__jdownloads_usergroups_limits a WHERE use_private_area = '1'");
         $groups = $db->loadRowList();
         if ($groups){
             // get folder path from config
             $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'files.uploaddir'");
             $root_dir = $db->loadResult();
             $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'private.area.folder.name'");
             $private_dir = $db->loadResult();
             // build array which usergroups are activated in jD usergroups_limits table
             foreach ($groups as $group){
                 $used_groups_for_private[] = $group[0];
             }
             // build array which usergroups are selected for the updated or new added user
             foreach ($user['groups'] as $usergroup){
                 $user_groups[] = $usergroup[0];
             }             

             for ($i=0; $i < count($user_groups); $i++){
                 $x = array_search ( $user_groups[$i], $used_groups_for_private );
                 if ($x !== false){
                     $private_folder = $root_dir.DS.$private_dir.DS.$user['id'];
                     if (!JFolder::exists($private_folder)){
                         if (JFolder::create($private_folder, 0755)){
                             JFile::copy($root_dir.DS.'index.html',$private_folder.DS.'index.html');
                         }
                     }
                 }
             }    
         }       
     }     
    
    
    /**
     *   When we use the private area for jD users, we must remove the users folder, when his user account is deleted
     */
     public function onUserAfterDelete($user, $isNew, $success) {
         $app = JFactory::getApplication();
         $db  = JFactory::getDBO();

         // get folder path from config
         $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'files.uploaddir'");
         $root_dir = $db->loadResult();
         $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'private.area.folder.name'");
         $private_dir = $db->loadResult();
         $private_folder = $root_dir.DS.$private_dir.DS.$user['id'];
         if (JFolder::exists($private_folder)){
             if (JFolder::delete($private_folder)){
                 JError::raiseNotice( 100, JText::_('COM_JDOWNLOADS_USERGROUPS_PRIVATE_FILES_FOLDER_REMOVED') );
             } else {
                 JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_USERGROUPS_PRIVATE_FILES_FOLDER_REMOVED_ERROR') );
             }
             
         }             
     }

    /**
    * This event is triggered after the framework has rendered the application.
    * Rendering is the process of pushing the document buffers into the template placeholders, retrieving data from the document and pushing it into the JResponse buffer.
    * When this event is triggered the output of the application is available in the response buffer.
    * 
    * @param none
    * @return none
    */     
    public function onAfterRender() { 
         $app = JFactory::getApplication();
         $database = JFactory::getDBO();
         $return = false;
         
         // exist the tables?
         $prefix = strtolower($database->getPrefix()); 
         $tablelist = $database->getTableList();
         if ( !in_array ( $prefix.'jdownloads_files', $tablelist ) ){
             $return = true;
         }     
         $plugin = JPluginHelper::getPlugin('system', 'jdownloads');
         jimport( 'joomla.utilities.utility' );
         // get params
         $params = $this->params;;
         $use_hider = $params->get( 'use_hider' );
         if (!$use_hider) $return = true;
    
         // No need in admin
         if (!$app->isAdmin()) {
             $body = JResponse::getBody();
             if (!$return){
             
                function _getParameter( $name, $default='' ) {
                    $return = "";
                    $return = $this->params->get( $name, $default );
                }
                
                // define the regular expression
                $regex1 = "#{jdreg}(.*?){/jdreg}#s";
                $regex2 = "#{jdpub}(.*?){/jdpub}#s";
    
                $regex3 = "#{jdauthor}(.*?){/jdauthor}#s";
                $regex4 = "#{jdeditor}(.*?){/jdeditor}#s";
                $regex5 = "#{jdpublisher}(.*?){/jdpublisher}#s";
                $regex6 = "#{jdmanager}(.*?){/jdmanager}#s";
                $regex7 = "#{jdadmin}(.*?){/jdadmin}#s";
                $regex8 = "#{jdsuper}(.*?){/jdsuper}#s";
                $regex9 = "#{jdspecial}(.*?){/jdspecial}#s";
                
                // replacement for _reg
                $body = preg_replace_callback( $regex1, array('plgSystemjdownloads', '_reg'), $body );
                // replacement for _pub
                $body = preg_replace_callback( $regex2, array('plgSystemjdownloads', '_pub'), $body );
                // replacements for groups by name
                $body = preg_replace_callback( $regex9, array('plgSystemjdownloads', '_special'), $body );
                $body = preg_replace_callback( $regex3, array('plgSystemjdownloads', '_author'), $body );
                $body = preg_replace_callback( $regex4, array('plgSystemjdownloads', '_editor'), $body );
                $body = preg_replace_callback( $regex5, array('plgSystemjdownloads', '_publisher'), $body );
                $body = preg_replace_callback( $regex6, array('plgSystemjdownloads', '_manager'), $body );
                $body = preg_replace_callback( $regex7, array('plgSystemjdownloads', '_admin'), $body );
                $body = preg_replace_callback( $regex8, array('plgSystemjdownloads', '_super'), $body );
                

                JResponse::setBody($body);
             
             } else {
                // Hide option is deactivated - so we must remove maybe the prior inserted placeholder
                $body = str_replace('{jdreg}', '', $body);
                $body = str_replace('{/jdreg}', '', $body);
                $body = str_replace('{jdpub}', '', $body);
                $body = str_replace('{/jdpub}', '', $body);
                $body = str_replace('{jdauthor}', '', $body);
                $body = str_replace('{/jdauthor}', '', $body);
                $body = str_replace('{jdeditor}', '', $body);
                $body = str_replace('{/jdeditor}', '', $body);
                $body = str_replace('{jdpublisher}', '', $body);
                $body = str_replace('{/jdpublisher}', '', $body);
                $body = str_replace('{jdmanager}', '', $body);
                $body = str_replace('{/jdmanager}', '', $body);
                $body = str_replace('{jdadmin}', '', $body);
                $body = str_replace('{/jdadmin}', '', $body);
                $body = str_replace('{jdsuper}', '', $body);
                $body = str_replace('{/jdsuper}', '', $body);
                $body = str_replace('{jdspecial}', '', $body);
                $body = str_replace('{/jdspecial}', '', $body);
                
                JResponse::setBody($body);
             }     
         }
    } 

    /**
    * This event is triggered after the framework has loaded and initialised and the router has routed the client request.
    * Routing is the process of examining the request environment to determine which component should receive the request. The component optional parameters are then set in the request object that will be processed when the application is being dispatched.
    * When this event triggers, the router has parsed the route and pushed the request parameters into JRequest to be retrieved by the application.
    * 
    * @param none
    * @return none
    */  
	public function onAfterRoute(){

         // deactivate Joomla caching when required
         if( JFactory::getApplication()->isSite() && $this->checkCacheRules()){
             $this->caching = JFactory::getConfig()->get('caching');
             JFactory::getConfig()->set('caching', 0);
         }   

         // reduce download log data sets when a maximum value exists
         $database = JFactory::getDBO();
         // exist the table?
         $prefix = strtolower($database->getPrefix()); 
         $tablelist = $database->getTableList();
         if ( !in_array ( $prefix.'jdownloads_logs', $tablelist ) ){
             return;
         } 
         
         $plugin = JPluginHelper::getPlugin('system', 'jdownloads');
         jimport( 'joomla.utilities.utility' );
         // get params
         $params = $this->params;;
         $reduce_data_to = (int)$params->get('reduce_log_data_sets_to');
         if ($reduce_data_to == 0) return;
         
         // reduce data
         $database->setQuery("SELECT COUNT(*) FROM #__jdownloads_logs");
         $sum = $database->loadResult();
         $sum_delete = $sum - $reduce_data_to;
         if ($sum_delete > 0){
            $database->setQuery("DELETE FROM #__jdownloads_logs ORDER BY id LIMIT $sum_delete");
            $database->execute();   
         }
         
         return;
    }    

     /**
     *  
     *      
     */
     function checkCacheRules(){
          
        $plugin = JPluginHelper::getPlugin('system', 'jdownloads');
        jimport( 'joomla.html.parameter' );
        $params = $this->params;
        $defs = trim(str_replace("\r","", $params->def('rules','')));
        $defs = explode("\n", $defs);
        
        foreach($defs as $def){
            if ($def != ''){
                $result = $this->parseQueryString($def);
                if(is_array($result)){
                    $found = 0;
                    $required = count($result);
                    foreach($result As $key => $value){
                        if( JRequest::getVar($key) == $value || ( JRequest::getVar($key, null) !== null && $value == '?' ) ){
                            $found++;
                        }
                    }
                    if($found == $required){
                        return true;
                    }
                }
            }
        }
        
        return false;
     } 
     
     /**
     * 
     * 
     * @param mixed $str
     */
     function parseQueryString($str) {
        $op = array();
        $pairs = explode("&", $str);
        foreach ($pairs as $pair) {
            list($k, $v) = array_map("urldecode", explode("=", $pair));
            $op[$k] = $v;
        }
        return $op;
     }
     
    /**
    *  Functions for hide elements from output for special user groups
    * 
    *  Inspired by the hider content plugin from Dioscouri Design
    *  Part of this functions are copyright by Dioscouri Design - www.dioscouri.com 
    * 
    * 
    */
    
    function _reg( &$matches ) {
        $user = JFactory::getUser();
        $return = '';
        if ($user->id) { $return = $matches[1]; }
        return $return;
    }

    function _pub( &$matches ) {
        $user = JFactory::getUser();
        $return = $matches[1];
        if ($user->id) { $return = ''; }
        return $return;
    }

    function _author( &$matches ) {
        $user = JFactory::getUser();
        $coreUserGroups = $user->getAuthorisedGroups();
        $return = $matches[1];
        if (!in_array(3,$coreUserGroups) && !in_array(8,$coreUserGroups)){
            $return = '';
        }
        return $return;
    }

    function _editor( &$matches ) {
        $user = JFactory::getUser();
        $coreUserGroups = $user->getAuthorisedGroups();
        $return = $matches[1];
        if (!in_array(4,$coreUserGroups) && !in_array(8,$coreUserGroups)){
            $return = '';
        }
        return $return;
    }

    function _publisher( &$matches ) {
        $user = JFactory::getUser();
        $coreUserGroups = $user->getAuthorisedGroups();
        $return = $matches[1];
        if (!in_array(5,$coreUserGroups) && !in_array(8,$coreUserGroups)){
            $return = '';
        }
        return $return;
    }

    function _manager( &$matches ) {
        $user = JFactory::getUser();
        $coreUserGroups = $user->getAuthorisedGroups();
        $return = $matches[1];
        if (!in_array(6,$coreUserGroups) && !in_array(8,$coreUserGroups)){
            $return = '';
        }
        return $return;
    }

    function _admin( &$matches ) {
        $user = JFactory::getUser();
        $coreUserGroups = $user->getAuthorisedGroups();
        $return = $matches[1];
        if (!in_array(7,$coreUserGroups) && !in_array(8,$coreUserGroups)){
            $return = '';
        }
        return $return;
    }

    function _super( &$matches ) {
        $user = JFactory::getUser();
        $coreUserGroups = $user->getAuthorisedGroups();
        $return = $matches[1];
        if (!in_array(8,$coreUserGroups)){
            $return = '';
        }
        return $return;
    }
    
    function _special( &$matches ) {
        $user = JFactory::getUser();
        $aid = max ($user->getAuthorisedViewLevels());
        $return = $matches[1];
        if ($aid != 3){
            $return = '';
        }
        return $return;
    }    
        
  }
?>