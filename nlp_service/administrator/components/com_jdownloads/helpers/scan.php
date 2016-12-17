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

define( '_JEXEC', 1); 

define('JPATH', dirname(__FILE__) );

if (!defined('DS')){
    define( 'DS', DIRECTORY_SEPARATOR );
}    

$parts = explode( DS, JPATH );  
$script_root =  implode( DS, $parts ) ;

// check path
$x = array_search ( 'administrator', $parts  );
if (!$x) exit;

$path = '';

for ($i=0; $i < $x; $i++){
    $path = $path.$parts[$i].DS; 
}
// remove last DS
$path = substr($path, 0, -1);

if (!defined('JPATH_BASE')){
    define('JPATH_BASE', $path );
}    

if (!defined('JPATH_SITE')){
    define('JPATH_SITE', $path );
}    

/* Required Files */
require_once ( JPATH_SITE . DS . 'includes' . DS . 'defines.php' );
require_once ( JPATH_SITE . DS . 'includes' . DS . 'framework.php' );
require_once ( JPATH_SITE . DS . 'libraries' . DS . 'joomla' . DS . 'factory.php' );
require_once ( JPATH_SITE . DS . 'libraries' . DS . 'joomla' . DS . 'database'.DS.'database.php' );
require_once ( JPATH_SITE . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS . 'categories.php');
require_once ( JPATH_SITE . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS . 'query.php');
require_once ( JPATH_SITE . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS . 'query.php');
require_once ( JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS . 'ProgressBar.class.php');
require_once ( JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS . 'jdownloadshelper.php');

/* Create the Application */
$app = JFactory::getApplication('site')->initialise();

//require_once 'ProgressBar.class.php';
$database = JFactory::getDBO();
$document = JFactory::getDocument();
$user = JFactory::getUser();

JLoader::import('joomla.application.component.modeladmin');
JLoader::import('joomla.application.component.model');

// Import jDownloads model
// located in administrator section of the website.
// if you want access a model from public section, then remove JPATH_ADMINISTRATOR . DS .
JLoader::import( 'category', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jdownloads' . DS . 'models' );
JLoader::import( 'download', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jdownloads' . DS . 'models' );

// get jd config
$jlistConfig = buildjlistConfig();
$task = 'scan.files';

$backend_lang = JComponentHelper::getParams('com_languages')->get('administrator');
$language = JFactory::getLanguage();
$language->load('com_jdownloads', JPATH_ADMINISTRATOR, $backend_lang, true);

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$backend_lang.'" lang="'.$backend_lang.'" dir="ltr">
<head><meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Type" content="text/html;  charset=utf-8">
<meta http-equiv="content-language" content="en"
<title>jDownloads</title>
</head>

<body style="FONT-FAMILY: Verdana; FONT-SIZE: 8pt; COLOR: #222222;  background-color: #F5F6CE; padding: 15;">

<?php

$document->setTitle(JText::_('COM_JDOWNLOADS_RUN_MONITORING_TITLE'));

// check whether we may do the job
$config = JFactory::getConfig();
$secret = $config->get( 'secret' );

$jinput = JFactory::getApplication()->input;
$param = $jinput->get('key', '', 'string');
if ($param != $secret){
    echo '<b>'.JText::_('COM_JDOWNLOADS_NOT_ALLOWED_ACTION_MSG').'</b>';
    exit;
}
echo '<br />';
echo '<div  style="font-family:Verdana; font-size:10"><b>'.JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO2').'</b><br />'.JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO').'<br /><br /></div>';
flush();

$time_start = microtime_float();
checkFiles($task);
$time_end = microtime_float();
$time = $time_end - $time_start;

echo '<br /><small>The scan duration: '.number_format ( $time, 2).' seconds.</small>';
echo '<br /><br /><small>'.JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO8').'</small>'; 
echo '</body></html>';


/* checkFiles
/
/ check uploaddir and subdirs for variations
/ 
/
*/
function checkFiles($task) {
    
    global $jlistConfig, $lang;
    
    $limits = remove_server_limits();
    if (!$limits){
        echo '<p>';
        echo '*******************************************************';
        echo '<br />Note: The time limit on the server could not be changed/increased!<br />';
        echo '*******************************************************';
        echo '</p>';
    }
    
    ignore_user_abort(true);
    // ob_flush();
    flush();

    $model_category = JModelLegacy::getInstance( 'Category', 'jdownloadsModel' );
    $model_download = JModelLegacy::getInstance( 'Download', 'jdownloadsModel' );
    
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
    
    $db = JFactory::getDBO();
    $lang = JFactory::getLanguage();
    $lang->load('com_jdownloads', JPATH_SITE.DS);
    
    //check if all files and dirs in the uploaddir directory are listed
    if($jlistConfig['files.autodetect'] || $task == 'restore.run' || $task == 'scan.files'){
        
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
          $dirlist = JDownloadsHelper::searchdir($jd_root, -1, 'DIRS', 0, $except_folders);
          
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
          
          // first progressbar for cats
          $title1 = JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO3');
          $bar = new ProgressBar();
          $bar->setMessage($title1);
          $bar->setAutohide(false);
          $bar->setSleepOnFinish(0);
          $bar->setPrecision(100);
          $bar->setForegroundColor('#990000');
          $bar->setBackgroundColor('#CCCCCC');
          $bar->setBarLength(300);
          $bar->initialize($count_cats-1); // print the empty bar

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
                   $checked_cat_dir = JDownloadsHelper::getCleanFolderFileName( $cat_dir_value, true );
                   
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

             $bar->increase(); // calls the bar with every processed element    
          }
          echo '<small><br />'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_SUM_FOLDERS').' '.count($searchdirs).'<br /><br /></small>';   
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
          // first progressbar for cats
          $bar = new ProgressBar();
          $title2 = JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO4');  
          $bar->setMessage($title2);
          $bar->setAutohide(false);
          $bar->setSleepOnFinish(0);
          $bar->setPrecision(100);
          $bar->setForegroundColor('#990000');
          $bar->setBarLength(300);
          $bar->initialize($count_cats); // print the empty bar          
          
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
               $bar->increase(); // calls the bar with every processed element  
          }
          echo '<br /><br />';   
          
          unset($cats);
          
          // *********************************************************             
          //  Check all files and create for new founded new Downloads
          // *********************************************************
          
          unset($except_folders[1]);
                     
          $all_dirs = JDownloadsHelper::scan_dir($dir, $type, $only, $allFiles, $recursive, $onlyDir, $except_folders, $jd_root, $files);
          
          if ($all_dirs != FALSE) {

              $count_files = count($files);
              
              // first progressbar for cats
              $bar = new ProgressBar();
              $title3 = JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO5');  
              $bar->setMessage($title3);
              $bar->setAutohide(false);
              $bar->setSleepOnFinish(0);
              $bar->setPrecision(100);
              $bar->setForegroundColor('#990000');
              $bar->setBarLength(300);
              $bar->initialize($count_files); // print the empty bar          
              
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
                             // not check the filename when restore backup file
                             if ($task != 'restore.run'){  
                                
                                // reset images var
                                $images = '';
                                
                                $only_name = utf8_encode(JFile::stripExt($filename));
                                $file_extension = JFile::getExt($filename); 

                               // $title =  JFilterInput::clean($only_name);
                                $title = JFilterInput::getInstance(null, null, 1, 1)->clean($only_name, 'STRING');
                                
                                // check filename 
                                $filename_new = JDownloadsHelper::getCleanFolderFileName( $only_name, true ).'.'.$file_extension;                                 
                                                                 
                                if ($only_name == ''){
                                    echo "<script> alert('Error: Filename empty after cleaning: ".$dir_path_total."'); </script>\n";
                                    continue;    // go to next foreach item
                                }
                                 
                                if ($filename_new != $filename){
                                    $source = $startdir.$only_dirs.'/'.$filename;
                                    $target = $startdir.$only_dirs.'/'.$filename_new;
                                    $success = @rename($source, $target); 
                                    if ($success === true) {
                                        $filename = $filename_new; 
                                    } else {
                                        // could not rename filename
                                        echo "<script> alert('Error: Could not rename $filename'); </script>\n";
                                        continue;    // go to next foreach item
                                    }
                                }     
                             }
                             
                             $target_path = $upload_dir.$filename;
                             
                             // find the category for the new founded file in this folder
                             $db->setQuery("SELECT * FROM #__jdownloads_categories WHERE cat_dir = '$cat_dir_value' AND cat_dir_parent = '$cat_dir_parent_value'");
                             $cat = $db->loadObject();
                             
                             if ($cat){
                                 $id = $cat->id;
                                 $access = $cat->access;
                             } else {
                                 // it seems that we have a new file in 'uncategorised' folder found
                                 $id = 1;
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
                                   $pdf_thumb_name = jdownloadsHelper::create_new_pdf_thumb($target_path, $only_name, $thumb_path, $screenshot_path);
                                   if ($pdf_thumb_name){
                                       $images = $pdf_thumb_name; 
                                   }    
                             }    
                                 
                             // create auto thumb when founded file is an image
                             if ($jlistConfig['create.auto.thumbs.from.pics'] && $jlistConfig['create.auto.thumbs.from.pics.by.scan']){
                                 if ($file_is_image = JDownloadsHelper::fileIsPicture($filename)){
                                     $thumb_created = jdownloadsHelper::create_new_thumb($target_path);       
                                     if ($thumb_created){
                                         $images = $filename;
                                         // create new big image for full view
                                         $image_created = jdownloadsHelper::create_new_image($target_path);
                                     }
                                 }    
                             }
                             
                             $sha1_value = sha1_file($target_path);
                             $md5_value  =  md5_file($target_path);                             
                            
                             // build data array
                             $data = array (
                                'file_id' => 0,
                                'cat_id' => $id,
                                'file_title' => $title,
                                'file_alias' => '',
                                'notes' => $note,
                                'url_download' => $filename,
                                'size' => $file_size,
                                'description' => JDownloadsHelper::getOnlyLanguageSubstring($jlistConfig['autopublish.default.description']),          
                                'file_pic' => $file_pic,
                                'images' => $images,
                                'date_added' => $creation_date,
                                'sha1_value' => $sha1_value,
                                'md5_value' => $md5_value, 
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
                            
                             // create new download in table
                             $create_result = $model_download->createAutoDownload( $data );
                             if (!$create_result){
                                // error message
                                echo "<script> alert('Error: Could not add download for: $filename'); window.history.go(-1); </script>\n";
                                exit();
                             }
                            
                             $new_files++;
                             $log_array[] = JText::_('COM_JDOWNLOADS_AUTO_FILE_CHECK_ADDED').' <b>'.$only_dirs.'/'.$filename.'</b><br />';
                         }                   
                  }
                  $bar->increase(); // calls the bar with every processed element
              }  
          }                    
          echo '<small><br />'.JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_SUM_FILES').' '.count($files).'<br /><br /></small>';   
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
          
          // first progressbar for cats
          $bar = new ProgressBar();
          $title4 = JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO6');
          $bar->setMessage($title4);
          $bar->setAutohide(false);
          $bar->setSleepOnFinish(0);
          $bar->setPrecision(100);
          $bar->setForegroundColor('#990000');
          $bar->setBarLength(300);
          $bar->initialize($count_files); // print the empty bar

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
              $bar->increase(); // calls the bar with every processed element 
          }
          
          
          echo '<br /><br />';
          echo '<div style="font-family:Verdana; font-size:10"><b>'.JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO7').'</b><br /><br /></div>';
          flush(); 
       
          // build log message
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
          }     
                  
          if ($task == 'scan.files') {
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

          } 
        } else {
            // error upload dir not exists
            echo '<font color="red"><b>'.JText::sprintf('COM_JDOWNLOADS_AUTOCHECK_DIR_NOT_EXIST', $jlistConfig['files.uploaddir']).'<br /><br />'.JText::_('COM_JDOWNLOADS_AUTOCHECK_DIR_NOT_EXIST_2').'</b></font>';
        }
    }            
}

/**
* Read the configuration data in an array
* 
* @return array 
*   
*/
function buildjlistConfig(){
    $database = JFactory::getDBO();
    $jlistConfig = array();
    $database->setQuery("SELECT setting_name, setting_value FROM #__jdownloads_config");
    $jlistConfigObj = $database->loadObjectList();
    if(!empty($jlistConfigObj)){
        foreach ($jlistConfigObj as $jlistConfigRow){
            $jlistConfig[$jlistConfigRow->setting_name] = $jlistConfigRow->setting_value;
        }
    }
    return $jlistConfig;
}
   
/*
 * Simple function to replicate PHP 5 behaviour
 */
function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function remove_server_limits() {
    if (!ini_get('safe_mode')) {
            @set_time_limit(0);
            @ini_set('memory_limit', '256M');
            @ini_set('post_max_size', '256M');
            @ini_set('max_execution_time', 0);
            return true;
    }
    return false;
}

?>