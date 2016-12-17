<?php
/**
* jDownloads content plugin
* Version 3.2 
* For Joomla 3.x
* Original created by Marco Pelozzi - marco.u3@bluewin.ch - www.redorion.com
* Modified and reworked for Joomla 2.5/3.x by Arno Betz - jDownloads.com - 2014-11-25
*  
* Usage:
*  {jd_file file==*ID}                  where *ID stands for the ID-number of the download in jDownloads.
*  {jd_file category==1 count==5}       category==1 is the chosen category id. count==5 is die number of viewed downloads from this category, when it is 0 all files are listed.
*  {jd_file cat_hottest==1 count==5}    Views the 5 most downloaded files from a given category ID.
*  {jd_file cat_latest==1 count==5}     Views the 5 last added files from a given category ID.
*  {jd_file latest==5}                  Views the 5 newest downloads.
*  {jd_file hottest==5}                 Views the 5 top downloads.
*  {jd_file updated==5}                 Views the 5 last updated downloads.
*  {jd_file onlinelayout==layout name}  Additional to the placeholders above, you can use this to select for the view a different file layout. 
*                                       So it is possible to use in the same content various layouts. 
*                                       Example:  {jd_file onlinelayout==Simple File List}{jd_file latest==5}
*
* 
* 
*/

defined( '_JEXEC' ) or die( 'Restricted access' ); 

setlocale(LC_ALL, 'C.UTF-8', 'C');

jimport( 'joomla.plugin.plugin' );

if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
} 

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS .'route.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_jdownloads' . DS . 'helpers' . DS .'jdownloadshelper.php' );

global $cat_link_itemidsPlg;
$db = JFactory::getDBO();
$app = JFactory::getApplication(); 

if ($app->isSite()){

    // get all published single category menu links
    $db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=category%' AND published = 1");
    $cat_link_itemidsPlg = $db->loadAssocList();
    if ($cat_link_itemidsPlg){
        for ($i=0; $i < count($cat_link_itemidsPlg); $i++){
             $cat_link_itemidsPlg[$i]['catid'] = substr( strrchr ( $cat_link_itemidsPlg[$i]['link'], '=' ), 1);
        }    
    }
    
    // get current category menu ID when exist and all needed menu IDs for the header links
    global $menuItemids;
    $menuItemids = JDHelper::getMenuItemids();
    
    // get all other menu category IDs so we can use it when we needs it
    global $cat_link_itemids;
    $cat_link_itemids = JDHelper::getAllJDCategoryMenuIDs();
    
    // "Home" menu link itemid
    global $root_itemid;
    $root_itemid =  $menuItemids['root'];
    
    global $date_format;
    $date_format = JDHelper::getDateFormat();    

    //Globals definition
    $GLOBALS['jDFPitemid'] = jd_CalcItemid();
    $GLOBALS['jDFPOnlineLayout'] = '';
    $GLOBALS['jlistConfigM'] = buildjlistConfigM();
    $GLOBALS['jDownloadsMessage'] = 0;
    $GLOBALS['jDownloadsTested'] = 0;
    $GLOBALS['jDownloadsInstalled'] = 0;
    $GLOBALS['jDownloadsVersion'] = 0;
    $GLOBALS['jDFPconsiderrights'] = 1;
    $GLOBALS['jDFPv14'] = 0;
    $GLOBALS['jDFPsfolders'] = jd_SymbolFolders();
    $GLOBALS['jDFPpluginversion'] = '1.5';
    $GLOBALS['jDFPrank'] = 1;
    $GLOBALS['jDFPison'] = 1;
    $GLOBALS['jDFPcatids'] = '';
    $GLOBALS['jDFPloaded'] = 0;
    $GLOBALS['jDLayoutTitleExists'] = false;
}

class plgContentJdownloads extends JPlugin
{

    function plgContentJdownloads(&$subject, $params)
    {
        parent::__construct($subject, $params);
    }

    function onContentPrepare($context, &$article, &$params)
    {
        global $app, $jDFPplugin_live_site, $jDFPloaded, $jlistConfigM;         
        
        if ($app->isSite()){        
       
            // Simple performance check to determine whether bot should process further
            if (strpos($article->text, '{jd_file') === false) {
                    return true;
            }
            
            // Load language
            $lang = JFactory::getLanguage();
            $lang->load('com_jdownloads', JPATH_SITE);
            
            // Live site
            $GLOBALS['jDFPlive_site'] = JURI::base();
            // Live site of plugin
            $GLOBALS['jDFPplugin_live_site'] = $GLOBALS['jDFPlive_site'].'plugins/';

            // Absolute path
            $GLOBALS['jDFPabsolute_path'] = JPATH_SITE.'/';
         
            $ipad_user = false;
            // check whether we have an ipad/iphone user for flowplayer aso...
            if ((bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') || (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')){        
                $ipad_user = true;
            }
            
            $document = JFactory::getDocument(); 
            
            if ($jDFPloaded == 0){
                $document->addStyleSheet( $jDFPplugin_live_site."content/jdownloads/jdownloads/css/mos_jdownloads_file.css", 'text/css', null, array() );
                $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_fe.css", 'text/css', null, array() );
                $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_buttons.css", 'text/css', null, array() );
                
                if ($jlistConfigM['view.ratings']){
                    $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/rating/css/ajaxvote.css", "text/css", null, array() );         
                }
                
                if ($jlistConfigM['use.lightbox.function']){
                    // Only when lightbox is activated in jD
                    JHtml::_('bootstrap.framework');
                    $document->addScript($jDFPplugin_live_site.'content/jdownloads/jdownloads/lightbox/lightbox.js');
                    $document->addStyleSheet($jDFPplugin_live_site."content/jdownloads/jdownloads/lightbox/lightbox.css", 'text/css', null, array() );
                }
                
                $custom_css_path = JPATH_ROOT.'/components/com_jdownloads/assets/css/jdownloads_custom.css';
                if (JFile::exists($custom_css_path)){
                    $document->addStyleSheet( JURI::base()."components/com_jdownloads/assets/css/jdownloads_custom.css", 'text/css', null, array() );                
                }                
                
                // loadscript for flowplayer
                if ($jlistConfigM['flowplayer.use']){
                    $document->addScript(JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer-3.2.12.min.js');
                    // load also the ipad plugin when required
                    if ($ipad_user){
                        $document->addScript(JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer.ipad-3.2.12.min.js');
                    }
                }
                
                // add rating script
                if ($jlistConfigM['view.ratings']){
                    $document->addScript(JURI::base().'components/com_jdownloads/assets/rating/js/ajaxvote.js');
                }  
                                
            }     
            $jDFPloaded = 1;
            if (isset( $_GET['mjdfpp'])){
                if ($_GET['mjdfpp'] == 'show'){
                    $article->text = jd_file_parameters().$row->text;
                    return true;
                }
            }
            $regex = "#{jd_file (.*?)==(.*?)}#s";
            $article->text = preg_replace_callback($regex, "jd_file_callback", $article->text);

            return true;
        }
    }
}

    // Calculate Symbolfolders depending on jDownloads
    function jd_SymbolFolders(){
       global $jlistConfigM;
       
       $jd_l_folders                    = array();
       $jd_l_folders['thumb']           = 'images/jdownloads/screenshots/thumbnails/';
       $jd_l_folders['screenshot']      = 'images/jdownloads/screenshots/';
       $jd_l_folders['symbolfolder']    = 'images/jdownloads/';
       $jd_l_folders['cat']             = 'images/jdownloads/catimages/';
       $jd_l_folders['download']        = 'images/jdownloads/downloadimages/';
       $jd_l_folders['file']            = 'images/jdownloads/fileimages/';
       $jd_l_folders['hot']             = 'images/jdownloads/hotimages/';
       $jd_l_folders['mini']            = 'images/jdownloads/miniimages/';
       $jd_l_folders['new']             = 'images/jdownloads/newimages/';
       $jd_l_folders['upd']             = 'images/jdownloads/updimages/';
       return $jd_l_folders;
    }

    // Read configuration of jDownloads from database
    function buildjlistConfigM(){
      global $jDFPOnlineLayout;
      
      $db = JFactory::getDBO();
      $jlistConfig = array();
      
      $db->setQuery("SELECT setting_name, setting_value FROM #__jdownloads_config");
      $jlistConfigObj = $db->loadObjectList();
      
      if(!empty($jlistConfigObj)){
            foreach ($jlistConfigObj as $jlistConfigRow){
              $jlistConfig[$jlistConfigRow->setting_name] = $jlistConfigRow->setting_value;
            }
            if (!$jlistConfig['days.is.file.updated']){
              $jlistConfig['days.is.file.updated'] = 0;
            }
      }
      if ($jDFPOnlineLayout == '') {
              $jDFPOnlineLayout = $jlistConfig['fileplugin.defaultlayout'];
      }
      return $jlistConfig;
    }

    function DatumsDifferenz_JDm($Start,$Ende) {
        $Tag1=(int) substr($Start, 8, 2);
        $Monat1=(int) substr($Start, 5, 2);
        $Jahr1=(int) substr($Start, 0, 4);

        $Tag2=(int) substr($Ende, 8, 2);
        $Monat2=(int) substr($Ende, 5, 2);
        $Jahr2=(int) substr($Ende, 0, 4);

        if (checkdate($Monat1, $Tag1, $Jahr1)and checkdate($Monat2, $Tag2, $Jahr2)){
            $Datum1=mktime(0,0,0,$Monat1, $Tag1, $Jahr1);
            $Datum2=mktime(0,0,0,$Monat2, $Tag2, $Jahr2);

            $Diff=(Integer) (($Datum1-$Datum2)/3600/24);
            return $Diff;
        } else {
            return -1;
        }
    }

    function jd_file_callback($matches){
      global $jDownloadsTested, $jDownloadsMessage, $jDownloadsInstalled, $jDownloadsVersion, $jlistConfigM, $jDFPOnlineLayout, $jDFPrank, $jDFPison;
      $db =  JFactory::getDBO();
      $jdf_whatcontent = $matches[1];

      if ($jdf_whatcontent == 'plugin'){
         switch ($matches[2]){
	     case 'on':
           $jDFPison = 1;
           break;
	     case 'off':
           $jDFPison = 0;
           break;
	     case 'silent':
           $jDFPison = 2;
           break;
         }
         return '';
      }
      if ($jDFPison == 0){
	    return $matches[0];
      }
      if ($jDFPison == 2){
	    return '';
      }

      // Get the jDownloads version
      if ($jDownloadsTested == 0) {
        $jDownloadsTested = true;
        $comSQLquery = "SELECT * FROM #__extensions WHERE type = 'component' AND element = 'com_jdownloads'";
        $db->setQuery( $comSQLquery );
        $comrows = $db->loadObjectList();
        if (!$comrows) {
          $jDownloadsInstalled = 0;
        } else {
          $jDownloadsInstalled = 1;
          $jDiVersion = substr($jlistConfigM['jd.version'],0,3);
        }
      }

      // sinon message d'erreur (1 fois) et abandon du programme.
      if ($jDownloadsInstalled == 0) {
        if ($jdf_whatcontent != 'mjdfpp') {
          if ($jDownloadsMessage == 0){
            $jDownloadsMessage = 1;
            return _JDPLUGIN_FRONTEND_JDOWNLOADS_NOTINSTALLED;
          } else {
            return "";
          }
        }
      } 

      // Layouts laden
      if ($jDFPOnlineLayout == '') {
        $jDFPOnlineLayout = $jlistConfigM['fileplugin.defaultlayout'];
      }

      switch ($jdf_whatcontent) {
        case 'file':
          $jDFPrank = '';
          $id_result = jd_file_createdownload($matches);
          break;
        case 'durl':
          $jDFPrank = '';
          $id_result = jd_file_createdownload($matches);
          break;
        case 'mjdfpp':
          $id_result = jd_file_parameters();
          break;
        case 'onlinelayout':
          jd_set_newlayout($matches);
          $id_result = '';
          break;
        case 'latest':
          $id_result =jd_file_latest_hottest($matches);
          break;
        case 'hottest':
          $id_result = jd_file_latest_hottest($matches);
          break;
        case 'updated':
          $id_result = jd_file_latest_updated($matches);
          break;       
        case 'considerrights':
          $id_result = jd_file_changerights($matches);
          break;
        case 'showunpublished':
          $id_result = jd_file_changeunpublished($matches);
          break;
        case 'category':
          $id_result = jd_file_createcategory($matches, '');
          break;
        case 'cat_hottest':
          $id_result = jd_file_createcategory($matches, 'hottest');
          break;
        case 'cat_latest':
          $id_result = jd_file_createcategory($matches, 'latest');
          break;        
        }
      return $id_result;
    }

    function jd_file_changerights($matches){
	    global $jDFPconsiderrights;
        if ($matches[2] == 'off'){
           $jDFPconsiderrights = 0;
        }
        if ($matches[2] == 'on'){
           $jDFPconsiderrights = 1;
        }
    }

    function jd_file_parameters (){
      global $jlistConfigM, $jDFPOnlineLayout, $jDFPpluginversion, $jDFPsfolders, $jDFPabsolute_path, $jDFPplugin_live_site, $jDFP_JLanguage, $jDFPitemid, $jDFPloaded;
      $db =  JFactory::getDBO();

      $showConfig = "";
      $mytdleft = '<tr><td class="jdpf_parameters_l">';
      $mytdmiddle = '</td><td class="jdpf_parameters_r">';
      $mytdright = '</td></tr>';
      $mytdlefth = '<tr><td class="jdpf_parameters_title_l">';
      $mytdmiddleh = '</td><td class="jdpf_parameters_title_r">';
      $mytdrighth = '</td></tr>';
      $showConfig .= '<table class="jdpf_parameters">';
      $showConfig .= '<tr><td colspan=2 class="jdpf_parameters_header"> Current Plugin-Parameters for mos_jdownloads_file:</td></tr>';
      $showConfig .= $mytdlefth."Parameter".$mytdmiddleh."Value".$mytdrighth;
      $showConfig .= $mytdleft."Plugin version".$mytdmiddle.$jDFPpluginversion.$mytdright;
      $showConfig .= $mytdleft."jd.version".$mytdmiddle.$jlistConfigM['jd.version']." (".$jlistConfigM['jd.version.state']." SVN:".$jlistConfigM['jd.version.svn'].")".$mytdright;
      $showConfig .= $mytdleft."Flag is v14".$mytdmiddle.'1'.$mytdright;
      $showConfig .= $mytdleft."Database prefix".$mytdmiddle.$db->getPrefix().$mytdright;
      $showConfig .= $mytdleft."Absolute path".$mytdmiddle.$jDFPabsolute_path.$mytdright;
      $showConfig .= $mytdleft."Live site of plugin".$mytdmiddle.$jDFPplugin_live_site.$mytdright;
      $showConfig .= $mytdleft."ItemID".$mytdmiddle.$jDFPitemid.$mytdright;
      $showConfig .= $mytdleft."Plugin loaded".$mytdmiddle.$jDFPloaded.$mytdright;
      $showConfig .= $mytdleft."Symbols folder".$mytdmiddle.$jDFPsfolders['symbolfolder']."...".$mytdright;

      $db->setQuery("SELECT setting_name, setting_value FROM #__jdownloads_config WHERE setting_name LIKE 'fileplugin%'");
      $jlistConfigObj = $db->loadObjectList();
      if(!empty($jlistConfigObj)){
        foreach ($jlistConfigObj as $jlistConfigRow){
          $s_value = $jlistConfigRow->setting_value;
          if (($jlistConfigRow->setting_name == 'fileplugin.layout_disabled') || ($jlistConfigRow->setting_name == 'fileplugin.show_hot') || ($jlistConfigRow->setting_name == 'fileplugin.show_new')){
            $s_value .= '&nbsp;&nbsp;<font color="#000088">(Notice: Obsolete since 1.4)</font>';
          }

          $showConfig .= $mytdleft.$jlistConfigRow->setting_name.$mytdmiddle.$s_value.$mytdright;
        }
      } else {
        $showConfig .= $mytdleft."Database error".$mytdmiddle."Database jdownloads_config missing!".$mytright;
      }
      //$showConfig .= $mytdleft."Access level".$mytdmiddle.jd_checkAccess().$mytdright;
      $showConfig .= $mytdleft."&nbsp;".$mytdmiddle."&nbsp;".$mytdright;

      $tSQLquery = "SELECT * FROM #__jdownloads_templates WHERE (template_name = '".$jDFPOnlineLayout."') AND (template_typ = 2)";
      $db->setQuery($tSQLquery);
      $onlrows = $db->loadObjectList();
      if (!$onlrows) {
        $OnlineLayoutComment = '<font color="#FF0000"> MISSING !</font>';
      }
      else{
        $OnlineLayoutComment = '<font color="#008800"> AVAILABLE !</font>';
      }
      $showConfig .= $mytdlefth."Layout online".$mytdmiddleh."&quot;".$jDFPOnlineLayout."&quot;".$OnlineLayoutComment.$mytdrighth;
      $onltext = $onlrows[0]->template_text;
      $showConfig .= $mytdleft."Layout online text".$mytdmiddle.'<textarea cols="45" rows="5">'.$onltext."</textarea>".$mytdright;
      $showConfig .= '</table>';
      return $showConfig;
    }

    function jd_set_newlayout($matches){
      global $jDFPOnlineLayout;
      $jDFPOnlineLayout = $matches[2];
      return '';
    }

    function inh_rights($pcatid){
        $db =  JFactory::getDBO();
        $sql = "SELECT id, parent_id, access FROM #__jdownloads_categories WHERE id = ".$pcatid;
        $db->setQuery($sql);
        $crow = $db->loadObjectList();
        if (!$crow){
	      return '00';
        }
        if ($crow[0]->parent_id == 0){
	      return $crow[0]->access;
        }
        $therights = $crow[0]->access;

        while ( $crow[0]->parent_id > 0 ){
          $sql = "SELECT id, parent_id, access FROM #__jdownloads_categories WHERE id = ".$crow[0]->parent_id;
          $db->setQuery($sql);
          $crow = $db->loadObjectList();
          if ($crow[0]->access > $therights){
      	    $therights = $crow[0]->access;
	      }
        }
        return $therights;
    }

    // Check whether the assigned category (until the root) from the download is also 'published'
    function inh_published($pcatid){
        $db =  JFactory::getDBO();
        $sql = "SELECT id, parent_id, published FROM #__jdownloads_categories WHERE id = '".$pcatid."'";
        $db->setQuery($sql);
        $crow = $db->loadObjectList();
        if (!$crow){
	      return '1';
        }
        if ($crow[0]->parent_id == 0){
	      return $crow[0]->published;
        }
        $therights = $crow[0]->published;

        while ( $crow[0]->parent_id > 0 ){
          $sql = "SELECT id, parent_id, published FROM #__jdownloads_categories WHERE id = ".$crow[0]->parent_id;
          $db->setQuery($sql);
          $crow = $db->loadObjectList();
          if ($crow[0]->published < $therights){
      	    $therights = $crow[0]->published;
	      }
        }
        return $therights;
    }

    // Create a list with all valid category IDs which has the correct access settings 
    function getCategoryIDs($p_subcat){
        global $jDFPcatids, $jDFPconsiderrights;
      
        $db   =  JFactory::getDBO();
        $user = JFactory::getUser();
      
        $query  = $db->getQuery(true);
        $groups = implode(',', $user->getAuthorisedViewLevels());
              
        $db->setQuery("SELECT id FROM #__jdownloads_categories WHERE published = 1 AND access IN ($groups) ORDER BY ordering");
        $rows = $db->loadObjectList('id'); 
        if ($rows){
            foreach ($rows as $row){
                $jDFPcatids .= $row->id.',';
            }
            $jDFPcatids = substr($jDFPcatids, 0, -1); 
        }
    }

    // Create the output list for the last updated Downloads
    function jd_file_latest_updated($matches){
       global $jDFPrank, $jDFPcatids, $jlistConfigM;
       
       $db =  JFactory::getDBO();
       $user = JFactory::getUser();
      
       $query  = $db->getQuery(true);
       $groups = implode(',', $user->getAuthorisedViewLevels());
       
       $jDFPcatids = '';
       $bidon = getCategoryIDs(0);

       $days = $jlistConfigM['days.is.file.updated'];
       if (!$days) $days = 15;

       $until_day = mktime(0,0,0,date("m"), date("d")-$days, date("Y"));
       $until = date('Y-m-d H:m:s', $until_day);

       $filesql ="SELECT file_id FROM #__jdownloads_files WHERE cat_id IN (".$jDFPcatids.") AND (update_active = 1) AND (modified_date >= '.$until.') AND access IN ($groups) AND published = 1 ORDER BY {dado} DESC LIMIT ".$db->escape($matches[2]).";";
       
       if ($matches[1] == 'updated'){
           $filesql = str_replace("{dado}",'modified_date',$filesql);
       } else {
           $filesql = str_replace("{dado}",'downloads',$filesql);
       }

       $db->setQuery($filesql);
       $files = $db->loadObjectList();
       
       $filetable = '';
       $jDFPrank = 1;
       
       if ($files){
           foreach ($files as $thefile){
               $sim_matches = array("", "file", $thefile->file_id);
               $filetable .= jd_file_createdownload($sim_matches);
               $jDFPrank++;
           }
       }
       return $filetable;
    }

    // Create the output list for the hottest or latest Downloads
    function jd_file_latest_hottest($matches){
       global $jDFPrank, $jDFPcatids;

       $db =  JFactory::getDBO();
       $user = JFactory::getUser();
      
       $query  = $db->getQuery(true);
       $groups = implode(',', $user->getAuthorisedViewLevels());
       
       $jDFPcatids = '';
       $bidon = getCategoryIDs(0);

       $filesql = "SELECT file_id FROM #__jdownloads_files WHERE cat_id IN (".$jDFPcatids.") AND access IN ($groups) AND published = 1 ORDER BY {dado} DESC LIMIT ".$db->escape($matches[2]).";";
      
       if ($matches[1] == 'latest'){
           $filesql = str_replace("{dado}",'date_added',$filesql);
       }
       else{
           $filesql = str_replace("{dado}",'downloads',$filesql);
       }

       $db->setQuery($filesql);
       $files = $db->loadObjectList();
       
       $filetable = '';
       $jDFPrank = 1;
       
       if ($files){
   	    foreach ($files as $file){
   		    $sim_matches = array("", "file", $file->file_id);
            // create the output
            $filetable .= jd_file_createdownload($sim_matches);
            $jDFPrank++;
   	    }
       }
       return $filetable;
    }

    // Build the data output for a file placeholder 
    function jd_file_createdownload($matches){
      global $jlistConfigM, $jDFPOnlineLayout, $jDFPsfolders;
      
      $db = JFactory::getDBO();
      $user = JFactory::getUser();

      // Import jDownloads site model
      JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jdownloads/models', 'jdownloadsModel');
      $model_download = JModelLegacy::getInstance( 'Download', 'jdownloadsModel' );
      
      // Load the layout
      $jdLayout = $jDFPOnlineLayout;
      
      $query = "SELECT * FROM #__jdownloads_templates WHERE (template_name = '".$jdLayout."') AND (template_typ = 2)";
      $db->setQuery($query);
      $layout = $db->loadObject();

      // It was not possible to load the layout - abort
      if (!$layout) {
        $ReturnValue = str_replace("{thelayout}",$jdLayout,JText::_('COM_JDOWNLOADS_FRONTEND_SETTINGS_FILEPLUGIN_LAYOUTUNKNOWN')).'<br />';
         return $ReturnValue;
      }

      $jd_template              = $layout->template_text;
      $jd_template_header       = $layout->template_header_text;
      $jd_template_subheader    = $layout->template_subheader_text;
      $jd_template_footer       = $layout->template_footer_text;
      $jd_template_symbol_off   = $layout->symbol_off;
      
      $jd_template = $layout->template_before_text.$jd_template.$layout->template_after_text;

      // Get the data from the model
      $file_id = (int) $matches[2];
      $files = $model_download->getItem($file_id, true); // we use the second param as switch for plugin == true
      
      if (!$files) {
          // Not possible to get this download 
          $jd_filepic = JURI::base().'/plugins/content/jdownloads/jdownloads/images/offline.gif';
          $jd_filetitle = str_replace("{fileid}",$matches[2],JText::_('COM_JDOWNLOADS_FRONTEND_SETTINGS_FILEPLUGIN_FILEUNKNOWN'));
          $jd_template = jd_file_fill_nodownload($jd_template,$jd_filetitle,'',$jd_filepic);
          
          if ($jlistConfigM['fileplugin.enable_plugin'] == 0) {
    	      if ($jlistConfigM['fileplugin.show_jdfiledisabled'] == 0){
                  $jd_template = '';
    	      }
          }
          return $jd_template;
      }
      
      // Check the 'access' rights
      $access = $files->params->get('access-view');
      if (!$access){
          // No access 
          return '';
      }

      // Check the 'download' permissions
      $download = $files->params->get('access-download');
      if (!$download){
          // Not allowed 
          $download_allowed = 0;
      } else {
          // Allowed
          $download_allowed = 1;
      }
      
      $files->tags = new JHelperTags;
      $files->tags->getItemTags('com_jdownloads.download', $files->file_id);                                       
      
      // Plugin enabled or disabled
      if ($jlistConfigM['fileplugin.enable_plugin'] == 0){
        if ($jlistConfigM['fileplugin.show_jdfiledisabled'] == 0){
            $jd_template = '';
        } else {
            $jd_filetitle = $jlistConfigM['fileplugin.offline_title'];
            $jd_filepic = JURI::base().'/plugins/content/jdownloads/jdownloads/images/offline.gif';
            $jd_filedescription = $jlistConfigM['fileplugin.offline_descr'];
            if ($jlistConfigM['fileplugin.show_downloadtitle'] == 1){
              $jd_filetitle = $files->file_title;
              $jd_filepic = $jDFPsfolders['file'].$files->file_pic;
              $jd_filedescription = $jlistConfigM['fileplugin.offline_title'].'&nbsp;'.$jlistConfigM['fileplugin.offline_descr'];
            }
            $jd_template = jd_file_fill_nodownload($jd_template,$jd_filetitle,$jd_filedescription,$jd_filepic);
        }
        return $jd_template;
      }

      $jd_template = jd_file_fill_downloadok($jd_template, $files, $jd_template_symbol_off, $matches[1], $download_allowed);

      return '<div class="jd_content">'.$jd_template.'</div>';
    }

    function jd_file_fill_nodownload($p_Template, $p_Title, $p_Description, $p_Filepic){
      global $jlistConfigM, $jDFPplugin_live_site, $jDFPlive_site;

      $l_Template = str_replace("{{{","[[[",$p_Template);
      $jd_file_pic = '<img src="'.$jDFPlive_site.$p_Filepic.'" align="absmiddle" border="0" height="'.$jlistConfigM['file.pic.size'].'" width="'.$jlistConfigM['file.pic.size'].'" alt="" />';
      $l_Template = str_replace("{file_pic}",$jd_file_pic,$l_Template);
      $jd_file_pic = '<img src="'.$jDFPplugin_live_site.'content/jdownloads/jdownloads/images/nodownload.gif">';
      $l_Template = str_replace("{checkbox_list}",$jd_file_pic,$l_Template);
      $l_Template = str_replace("{file_title}",$p_Title,$l_Template);
      $l_Template = str_replace("{file_title_only}",$p_Title,$l_Template);
      $l_Template = str_replace("{description}",$p_Description,$l_Template);
      $l_Template = str_replace("{release}",'',$l_Template);
      $l_Template = str_replace("{release_title}",'',$l_Template);
      $l_Template = str_replace("{size}",'',$l_Template);
      $l_Template = str_replace("{downloads}",'',$l_Template);
      $l_Template = str_replace("{pic_is_new}",'',$l_Template);
      $l_Template = str_replace("{pic_is_hot}",'',$l_Template);
      $l_Template = str_replace("{license}",'',$l_Template);
      $l_Template = str_replace("{date_added}",'',$l_Template);
      $l_Template = str_replace("{language}",'',$l_Template);
      $l_Template = str_replace("{system}",'',$l_Template);
      $l_Template = str_replace("{url_download}",'',$l_Template);
      $l_Template = str_replace("{file_id}",'',$l_Template);
      $l_Template = str_replace("{ordering}",'',$l_Template);
      $l_Template = str_replace("{published}",'',$l_Template);
      $l_Template = str_replace("{cat_id}",'',$l_Template);
      $l_Template = str_replace("{mirror_1}",'',$l_Template);
      $l_Template = str_replace("{mirror_2}",'',$l_Template);
      $l_Template = str_replace("{link_to_details}",'',$l_Template);
      $l_Template = str_replace("{thumbnail}",'',$l_Template);
      $l_Template = str_replace("{screenshot}",'',$l_Template);
      $l_Template = str_replace("{pic_is_updated}",'',$l_Template);
      $l_Template = str_replace("{rank}",'',$l_Template);

      $l_Template = str_replace("{hits_title}",'',$l_Template);
      $l_Template = str_replace("{hits_value}",'',$l_Template);
      
      $l_Template = str_replace('{category_title}', '', $l_Template);
      $l_Template = str_replace('{category_name}', '', $l_Template);
      
      // remove images placeholders
      if (strpos($l_Template, "{screenshot_end}") > 0) {
        $pos_end = strpos($l_Template, '{screenshot_end}');
        $pos_beg = strpos($l_Template, '{screenshot_begin}');
        $l_Template = substr_replace($l_Template, '', $pos_beg, ($pos_end - $pos_beg) + 16);
      }
      for ($i=2; $i < 20; $i++){
          if (strpos($l_Template, "{screenshot_end$i}") > 0) {
            $pos_end = strpos($l_Template, "{screenshot_end$i}");
            $pos_beg = strpos($l_Template, "{screenshot_begin$i}");
            if ($i < 10){
                $l_Template = substr_replace($l_Template, '', $pos_beg, ($pos_end - $pos_beg) + 17);
            } else {
                $l_Template = substr_replace($l_Template, '', $pos_beg, ($pos_end - $pos_beg) + 18);
            }    
          }
      }

      $l_Template = str_replace("{thumbnail_lightbox}",'',$l_Template);
      $l_Template = str_replace("{thumbnail_gallery}",'',$l_Template);
      $l_Template = str_replace("{created_by_title}",'',$l_Template);
      $l_Template = str_replace("{created_by_value}",'',$l_Template);
      $l_Template = str_replace("{created_date_title}",'',$l_Template);
      $l_Template = str_replace("{created_date_value}",'',$l_Template);
      $l_Template = str_replace("{modified_by_title}",'',$l_Template);
      $l_Template = str_replace("{modified_by_value}",'',$l_Template);
      $l_Template = str_replace("{modified_date_title}",'',$l_Template);
      $l_Template = str_replace("{modified_date_value}",'',$l_Template);
      $l_Template = str_replace("{price_title}",'',$l_Template);
      $l_Template = str_replace("{price_value}",'',$l_Template);
      $l_Template = str_replace("{system_title}",'',$l_Template);
      $l_Template = str_replace("{system_text}",'',$l_Template);
      $l_Template = str_replace("{license_title}",'',$l_Template);
      $l_Template = str_replace("{license_text}",'',$l_Template);
      $l_Template = str_replace("{language_title}",'',$l_Template);
      $l_Template = str_replace("{language_text}",'',$l_Template);
      $l_Template = str_replace("{filesize_title}",'',$l_Template);
      $l_Template = str_replace("{filesize_value}",'',$l_Template);
      $l_Template = str_replace("{author}",'',$l_Template);
      $l_Template = str_replace("{url_author}",'',$l_Template);
      $l_Template = str_replace("{author_title}",'',$l_Template);
      $l_Template = str_replace("{author_text}",'',$l_Template);
      $l_Template = str_replace("{url_home}",'',$l_Template);
      $l_Template = str_replace("{author_url_title}",'',$l_Template);
      $l_Template = str_replace("{author_url_text}",'',$l_Template);
      $l_Template = str_replace("{files_title_begin}",'',$l_Template);
      $l_Template = str_replace("{files_title_text}",'',$l_Template);
      $l_Template = str_replace("{files_title_end}",'',$l_Template);
      $l_Template = str_replace("{mp3_player}",'',$l_Template);
      $l_Template = str_replace("{mp3_id3_tag}",'',$l_Template);
      $l_Template = str_replace("{google_adsense}",'',$l_Template);
      $l_Template = str_replace("{report_link}",'',$l_Template);
      $l_Template = str_replace("{sum_jcomments}",'',$l_Template);
      $l_Template = str_replace("{rating}",'',$l_Template);
      $l_Template = str_replace("{rating_title}",'',$l_Template);
      $l_Template = str_replace("{file_date}", '', $l_Template); 
      $l_Template = str_replace("{file_date_title}", '', $l_Template);
      $l_Template = str_replace("{tags_title}", '', $l_Template);
      $l_Template = str_replace("{tags}", '', $l_Template);
      $l_Template = str_replace("{featured_class}", '', $l_Template);
      $l_Template = str_replace("{featured_detail_class}", '', $l_Template);
      $l_Template = str_replace("{featured_pic}", '', $l_Template);
      
       // delete the tabs placeholder 
       $l_Template = str_replace('{tabs begin}', '', $l_Template);
       $l_Template = str_replace('{tab description}', '', $l_Template);
       $l_Template = str_replace('{tab description end}', '', $l_Template);
       $l_Template = str_replace('{tab pics}', '', $l_Template);
       $l_Template = str_replace('{tab pics end}', '', $l_Template);
       $l_Template = str_replace('{tab mp3}', '', $l_Template);
       $l_Template = str_replace('{tab mp3 end}', '', $l_Template);
       $l_Template = str_replace('{tab data}', '', $l_Template);
       $l_Template = str_replace('{tab data end}', '', $l_Template);
       $l_Template = str_replace('{tab download}', '', $l_Template);
       $l_Template = str_replace('{tab download end}', '', $l_Template);
       $l_Template = str_replace('{tab custom1}', '', $l_Template);
       $l_Template = str_replace('{tab custom1 end}', '', $l_Template);      
       $l_Template = str_replace('{tab custom2}', '', $l_Template);
       $l_Template = str_replace('{tab custom2 end}', '', $l_Template);
       $l_Template = str_replace('{tab custom3}', '', $l_Template);
       $l_Template = str_replace('{tab custom3 end}', '', $l_Template);
       $l_Template = str_replace('{tabs end}', '', $l_Template);
           
        // remove custom fields
        for ($x=1; $x<15; $x++){
             @$l_Template = str_replace("{custom_title_$x}", '', $l_Template);
             @$l_Template = str_replace("{custom_value_$x}", '', $l_Template);
        }             
      
      $l_Template = JDHelper::removeEmptyTags($l_Template);
      return str_replace("[[[","{",$l_Template);
    }

    function jd_file_fill_downloadok($p_Template, $files, $p_Symbol_Off, $p_DownloadType, $download_allowed){
        global $jlistConfigM, $jDFPsfolders, $jDFPrank, $jDFPlive_site, $jDFPplugin_live_site, $jDFPabsolute_path, $cat_link_itemidsPlg, $jDLayoutTitleExists, $root_itemid, $date_format;

        $db     = JFactory::getDBO();
        $user   = JFactory::getUser();
        
        $jd_user_settings = JDHelper::getUserRules();
        
        $jdlink_author_text   = '';
        $createdbyname        = '';
        $modifiedbyname       = '';
      
        $l_Template = str_replace("{{{","[[[", $p_Template);

        $jdpic_license = '';
        $jdpic_date = '';
        $jdpic_author = '';
        $jdpic_website = '';
        $jdpic_system = '';
        $jdpic_language = '';
        $jdpic_download = '';
        $jdpic_hits = ''; 
        $jdpic_size = '';
        $jdpic_price = '';
        $cat_itemid = 0;
      
        if ($p_Symbol_Off == 0){
            $msize = $jlistConfigM['info.icons.size'];
            $jdpic_license = '<img src="'.JURI::base().$jDFPsfolders['mini'].'license.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'" />&nbsp;';
            $jdpic_date = '<img src="'.JURI::base().$jDFPsfolders['mini'].'date.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DATE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DATE').'" />&nbsp;';
            $jdpic_author = '<img src="'.JURI::base().$jDFPsfolders['mini'].'contact.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_AUTHOR').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_AUTHOR').'" />&nbsp;';
            $jdpic_website = '<img src="'.JURI::base().$jDFPsfolders['mini'].'weblink.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_WEBSITE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_WEBSITE').'" />&nbsp;';
            $jdpic_system = '<img src="'.JURI::base().$jDFPsfolders['mini'].'system.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_SYSTEM').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_SYSTEM').'" />&nbsp;';
            $jdpic_language = '<img src="'.JURI::base().$jDFPsfolders['mini'].'language.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LANGUAGE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LANGUAGE').'" />&nbsp;';
            $jdpic_download = '<img src="'.JURI::base().$jDFPsfolders['mini'].'download.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD').'" />&nbsp;';
            $jdpic_hits = '<img src="'.JURI::base().$jDFPsfolders['mini'].'download.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD_HITS').'" />&nbsp;';
            $jdpic_size = '<img src="'.JURI::base().$jDFPsfolders['mini'].'stuff.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_FILESIZE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_FILESIZE').'" />&nbsp;';
            $jdpic_price = '<img src="'.JURI::base().$jDFPsfolders['mini'].'currency.png" style="vertical-align:middle;" width="'.$msize.'" height="'.$msize.'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_PRICE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_PRICE').'" />&nbsp;';
        }

        // Build a little pic for extern links
        $jdextern_url_pic = '<img src="'.$jDFPplugin_live_site.'content/jdownloads/jdownloads/images/link_extern.gif" style="vertical-align:middle;" alt="" title="" />';
      
        $jd_file_pic    = '<img src="'.JURI::base().$jDFPsfolders['file'].$files->file_pic.'" style="vertical-align:middle;" border="0" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" alt="" title="" />';
        
        // Pics for: new file / hot file /updated
        $hotpic = '<img src="'.JURI::base().'images/jdownloads/hotimages/'.$jlistConfigM['picname.is.file.hot'].'" alt="" />';
        $newpic = '<img src="'.JURI::base().'images/jdownloads/newimages/'.$jlistConfigM['picname.is.file.new'].'" alt="" />';
        $updatepic = '<img src="'.JURI::base().'images/jdownloads/updimages/'.$jlistConfigM['picname.is.file.updated'].'" alt="" />';
        
        // Alternate CSS buttons when selected in configuration
        $status_color_hot        = $jlistConfigM['css.button.color.hot'];
        $status_color_new        = $jlistConfigM['css.button.color.new'];
        $status_color_updated    = $jlistConfigM['css.button.color.updated'];
        $download_color          = $jlistConfigM['css.button.color.download'];
        $download_size           = $jlistConfigM['css.button.size.download'];
        $download_size_mirror    = $jlistConfigM['css.button.size.download.mirror'];        
        $download_color_mirror1  = $jlistConfigM['css.button.color.mirror1'];        
        $download_color_mirror2  = $jlistConfigM['css.button.color.mirror2']; 
        $download_size_listings  = $jlistConfigM['css.button.size.download.small'];        
        
        $jd_cat_id      = $files->cat_id;
        $jd_filename    = $files->url_download;                                                                              
        $jd_file_language  = $files->file_language;
        $jd_system      = $files->system;
        
        if ($files->category_cat_dir_parent){
            $category_dir = $files->category_cat_dir_parent.'/'.$files->category_cat_dir;
        } else {
            $category_dir = $files->category_cat_dir;
        }        
        
        // Has this Download really a file?
        if (!$files->url_download && !$files->other_file_id && !$files->extern_file){
            // only a document without file
            $no_file_info = JText::_('COM_JDOWNLOADS_FRONTEND_ONLY_DOCUMENT_USER_INFO');
            $download_has_a_file = false;
        } else {
            $download_has_a_file = true;
            $no_file_info = '';
        }         
        
        
        // Compute the download slugs
        $files->slug = $files->file_alias ? ($files->file_id . ':' . $files->file_alias) : $files->file_id;
        
        // create all file titles
        $l_Template = JDHelper::buildFieldTitles($l_Template, $files);
        
        // When we have a simple document, view only the info not any buttons.
        if (!$download_has_a_file){
            if (strpos($l_Template, '{url_download}')){
                $l_Template = str_replace('{url_download}', $no_file_info, $l_Template);    
            } else {
                $l_Template = str_replace('{checkbox_list}', $no_file_info, $l_Template);    
            }

            // Place the images
            $l_Template = JDHelper::placeThumbs($l_Template, $files->images);      

            if ($jlistConfigM['view.detailsite']){
                $title_link = JRoute::_(JDownloadsHelperRoute::getDownloadRoute($files->slug, $files->cat_id, $files->language));
                $title_link_text = '<a href="'.$title_link.'">'.$db->escape($files->file_title).'</a>';
                $detail_link_text = '<a href="'.$title_link.'">'.JText::_('COM_JDOWNLOADS_FE_DETAILS_LINK_TEXT_TO_DETAILS').'</a>';
                // Build the file symbol (with link)
                if ($files->file_pic != '' ) {
                    $filepic = '<a href="'.$title_link.'">'.'<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files->file_pic.'" align="top" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" border="0" alt="" /></a> ';
                } else {
                    $filepic = '';
                }
                $l_Template = str_replace('{file_pic}',$filepic, $l_Template);
                // link to details view at the end
                $l_Template = str_replace('{link_to_details}', $detail_link_text, $l_Template);
                $l_Template = str_replace('{file_title}', $title_link_text, $l_Template);
            
            } else {
                // no links
                if ($files->file_pic != '' ) {
                    $filepic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files->file_pic.'" align="top" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" border="0" alt="" />';
                } else {
                    $filepic = '';
                }
                $l_Template = str_replace('{file_pic}',$filepic, $l_Template);
                // remove link to details view at the end
                $l_Template = str_replace('{link_to_details}', '', $l_Template);
                $l_Template = str_replace('{file_title}', $files->file_title, $l_Template);
            }
            $l_Template = str_replace('{checkbox_list}', '', $l_Template);
            $l_Template = str_replace('{mirror_1}', '', $l_Template);
            $l_Template = str_replace('{mirror_2}', '', $l_Template);
            $l_Template = str_replace('{hits_value}', '', $l_Template);
            $l_Template = str_replace('{filesize_value}', '', $l_Template); 
            
        } 

        // category title        
        $l_Template = str_replace('{category_title}', JText::_('COM_JDOWNLOADS_CATEGORY_LABEL'), $l_Template);
        $l_Template = str_replace('{category_name}', $files->category_title, $l_Template);
        
        // insert rating system
        if ($jlistConfigM['view.ratings']){
            $rating_system = JDHelper::getRatings($files->file_id, $files->rating_count, $files->rating_sum);
            $l_Template = str_replace('{rating}', $rating_system, $l_Template);
            $l_Template = str_replace('{rating_title}', JText::_('COM_JDOWNLOADS_RATING_LABEL'), $l_Template);
        } else {
            $l_Template = str_replace('{rating}', '', $l_Template);
            $l_Template = str_replace('{rating_title}', '', $l_Template);
        }
        
        // replace 'featured' placeholders
        if ($files->featured){
            // add the css class
            $l_Template = str_replace('{featured_class}', 'jd_featured', $l_Template);
            $l_Template = str_replace('{featured_detail_class}', 'jd_featured_detail', $l_Template);            
            // add the pic
            if ($jlistConfigM['featured.pic.filename']){
                $featured_pic = '<img class="jd_featured_star" src="'.JURI::base().'images/jdownloads/featuredimages/'.$jlistConfigM['featured.pic.filename'].'" width="'.$jlistConfigM['featured.pic.size'].'" height="'.$jlistConfigM['featured.pic.size.height'].'" alt="'.$jlistConfigM['featured.pic.filename'].'" />';
                $l_Template = str_replace('{featured_pic}', $featured_pic, $l_Template);
            } else {
                $l_Template = str_replace('{featured_pic}', '', $l_Template);
            }
        } else {
            $l_Template = str_replace('{featured_class}', '', $l_Template);
            $l_Template = str_replace('{featured_detail_class}', '', $l_Template);
            $l_Template = str_replace('{featured_pic}', '', $l_Template);
        }
         
        
        // Build the license info data and build link
        if ($files->license == '') $files->license = 0;
        $lic_data = '';

        if ($files->license_url != '') {
             $lic_data = $jdpic_license.'<a href="'.$files->license_url.'" target="_blank" rel="nofollow" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'">'.$files->license_title.'</a> '.$jdextern_url_pic;
        } else {
            if ($files->license_title != '') {
                 if ($files->license_text != '') {
                      $lic_data = $jdpic_license.$files->license_title;
                      $lic_data .= JHtml::_('tooltip', $files->license_text, $files->license_title);
                 } else {
                      $lic_data = $files->license_title;
                 }
            } else {
                $lic_data = '';
            }
        }
        $l_Template = str_replace('{license_text}', $lic_data, $l_Template);
        $l_Template = str_replace('{license}', $lic_data, $l_Template); // old placeholder

        // Build the 'files language' data
        $file_lang_values = explode(',' , JDHelper::getOnlyLanguageSubstring($jlistConfigM['language.list']));

        if ($jd_file_language == 0 ) {
            $jd_showlanguage = '';
        } else {
            $jd_showlanguage = $jdpic_language.$file_lang_values[$jd_file_language];
        }
        $l_Template = str_replace("{language}",$jd_showlanguage,$l_Template); // old placeholder
        $l_Template = str_replace("{language_text}",$jd_showlanguage,$l_Template);
      
        // Build the 'System' data
        $file_sys_values = explode(',' , $jlistConfigM['system.list']);
        if ($jd_system == 0 ) {
            $jd_showsystem = '';
        } else {
            $jd_showsystem = $jdpic_system.$file_sys_values[$jd_system];
        }
        $l_Template = str_replace("{system}",$jd_showsystem,$l_Template); // old placeholder
        $l_Template = str_replace("{system_text}",$jd_showsystem,$l_Template);
      
        // Build hits values
        $numbers_downloads = JDHelper::strToNumber((int)$files->downloads);
        $jd_showhits = $jdpic_hits.$numbers_downloads;
        $l_Template = str_replace("{hits_value}",$jd_showhits,$l_Template);

        // Build website url
        if (!$files->url_home == '') {
             if (strpos($files->url_home, 'http://') !== false) {    
                 $l_Template = str_replace('{url_home}',$jdpic_website.'<a href="'.$files->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$jdextern_url_pic, $l_Template);
                 $l_Template = str_replace('{author_url_text} ',$jdpic_website.'<a href="'.$files->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$jdextern_url_pic, $l_Template);
             } else {
                 $l_Template = str_replace('{url_home}',$jdpic_website.'<a href="http://'.$files->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$jdextern_url_pic, $l_Template);
                 $l_Template = str_replace('{author_url_text}',$jdpic_website.'<a href="http://'.$files->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$jdextern_url_pic, $l_Template);
             }    
        } else {
            $l_Template = str_replace('{url_home}', '', $l_Template);
            $l_Template = str_replace('{author_url_text}', '', $l_Template);
        }

        // Encode is link a mail
        if (strpos($files->url_author, '@') && $jlistConfigM['mail.cloaking']){
            if (!$files->author) { 
                $mail_encode = JHtml::_('email.cloak', $files->url_author);
            } else {
                $mail_encode = JHtml::_('email.cloak', $files->url_author, true, $files->author, false);
            }        
        }
                
        // Build author link
        if ($files->author <> ''){
            if ($files->url_author <> '') {           
                if ($mail_encode) {
                    $link_author = $jdpic_author.$mail_encode;
                } else {
                    if (strpos($files->url_author, 'http://') !== false) {    
                        $link_author = $jdpic_author.'<a href="'.$files->url_author.'" target="_blank">'.$files->author.'</a> '.$extern_url_pic;
                    } else {
                        $link_author = $jdpic_author.'<a href="http://'.$files->url_author.'" target="_blank">'.$files->author.'</a> '.$extern_url_pic;
                    }        
                }
                $l_Template = str_replace('{author}',$link_author, $l_Template);
                $l_Template = str_replace('{author_text}',$link_author, $l_Template);
                $l_Template = str_replace('{url_author}', '', $l_Template);
            } else {
                $link_author = $jdpic_author.$files->author;
                $l_Template = str_replace('{author}',$link_author, $l_Template);
                $l_Template = str_replace('{author_text}',$link_author, $l_Template);
                $l_Template = str_replace('{url_author}', '', $l_Template);
            }
        } else {
                $l_Template = str_replace('{url_author}', $jdpic_author.$files->url_author, $l_Template);
                $l_Template = str_replace('{author}','', $l_Template);
                $l_Template = str_replace('{author_text}','', $l_Template); 
        }
                     
        // Place the images
        $l_Template = JDHelper::placeThumbs($l_Template, $files->images, 'list');      
        
        // Compute for HOT symbol
        if ($jlistConfigM['loads.is.file.hot'] > 0 && $files->downloads >= $jlistConfigM['loads.is.file.hot'] ){
            // Is the old button pic used?
            if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){
                $l_Template = str_replace('{pic_is_hot}', $hotpic, $l_Template);
            } else {
                // CSS Button is selected
                $l_Template = str_replace('{pic_is_hot}', '<span class="jdbutton '.$status_color_hot.' jstatus">'.JText::_('COM_JDOWNLOADS_HOT').'</span>', $l_Template);
            }    
        } else {    
            $l_Template = str_replace('{pic_is_hot}', '', $l_Template);
        }
        
        // Compute for NEW symbol
        $days_diff = JDHelper::computeDateDifference(date('Y-m-d H:i:s'), $files->date_added);
        if ($jlistConfigM['days.is.file.new'] > 0 && $days_diff <= $jlistConfigM['days.is.file.new']){
            // Is the old button used?
            if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){            
                $l_Template = str_replace('{pic_is_new}', $newpic, $l_Template);
            } else {
                // CSS Button is selected
                $l_Template = str_replace('{pic_is_new}', '<span class="jdbutton '.$status_color_new.' jstatus">'.JText::_('COM_JDOWNLOADS_NEW').'</span>', $l_Template);
            }    
        } else {    
            $l_Template = str_replace('{pic_is_new}', '', $l_Template);
        }
        
        // Compute for UPDATED symbol
        // View it only when in the download is activated the 'updated' option
        if ($files->update_active) {
            $days_diff = JDHelper::computeDateDifference(date('Y-m-d H:i:s'), $files->modified);
            if ($jlistConfigM['days.is.file.updated'] > 0 && $days_diff >= 0 && $days_diff <= $jlistConfigM['days.is.file.updated']){
                if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){
                    $l_Template = str_replace('{pic_is_updated}', $updatepic, $l_Template);
                } else {
                    // CSS Button is selected
                    $l_Template = str_replace('{pic_is_updated}', '<span class="jdbutton '.$status_color_updated.' jstatus">'.JText::_('COM_JDOWNLOADS_UPDATED').'</span>', $l_Template);
                }    
            } else {    
                $l_Template = str_replace('{pic_is_updated}', '', $l_Template);
            }
        } else {
           $l_Template = str_replace('{pic_is_updated}', '', $l_Template);
        }    
      
        // file size
        if ($files->size == '' || $files->size == '0 B') {
            $l_Template = str_replace('{size}', '', $l_Template);
            $l_Template = str_replace('{filesize_value}', '', $l_Template);
        } else {
            $l_Template = str_replace('{size}', $jdpic_size.$files->size, $l_Template);
            $l_Template = str_replace('{filesize_value}', $jdpic_size.$files->size, $l_Template);
        } 
        
        // price
        if ($files->price != '') {
            $l_Template = str_replace('{price_value}', $jdpic_price.$files->price, $l_Template);
        } else {
            $l_Template = str_replace('{price_value}', '', $l_Template);
        }

        // file_date
        if ($files->file_date != '0000-00-00 00:00:00') {
             if ($files->params->get('show_date') == 0){ 
                 $filedate_data = $jdpic_date.JHtml::_('date',$files->file_date, $date_format['long']);
                 $filedate_data_title = JText::_('COM_JDOWNLOADS_EDIT_FILE_FILE_DATE_TITLE'); 
             } else {
                 $filedate_data = $jdpic_date.JHtml::_('date',$files->file_date, $date_format['short']);
                 $filedate_data_title = '';
             }    
        } else {
             $filedate_data = '';
             $filedate_data_title = '';
        }
        $l_Template = str_replace('{file_date}',$filedate_data, $l_Template);
        
        
        // date_added
        if ($files->date_added != '0000-00-00 00:00:00') {
            if ($files->params->get('show_date') == 0){ 
                // use 'normal' date-time format field
                $date_data = $jdpic_date.JHtml::_('date',$files->date_added, $date_format['long']);
            } else {
                // use 'short' date-time format field
                $date_data = $jdpic_date.JHtml::_('date',$files->date_added, $date_format['short']);
            }    
        } else {
             $date_data = '';
        }
        $l_Template = str_replace('{date_added}',$date_data, $l_Template);
        $l_Template = str_replace('{created_date_value}',$date_data, $l_Template);
        
        if ($files->creator){
            $l_Template = str_replace('{created_by_value}', $files->creator, $l_Template);
        } else {
            $l_Template = str_replace('{created_by_value}', '', $l_Template);
        }                
        if ($files->modifier){
            $l_Template = str_replace('{modified_by_value}', $files->modifier, $l_Template);
        } else {                              
            $l_Template = str_replace('{modified_by_value}', '', $l_Template);
        }
        
        // modified_date
        if ($files->modified != '0000-00-00 00:00:00') {
            if ($files->params->get('show_date') == 0){ 
                $modified_data = $jdpic_date.JHtml::_('date',$files->modified, $date_format['long']);
            } else {
                $modified_data = $jdpic_date.JHtml::_('date',$files->modified, $date_format['short']);
            }    
        } else {
            $modified_data = '';
        }
        $l_Template = str_replace('{modified_date_value}',$modified_data, $l_Template);
   
        if ($files->release == '') {
            $l_Template = str_replace('{release}', '', $l_Template);
        } else {
            $l_Template = str_replace('{release}', $files->release, $l_Template); 
            // with versions text from language file
            // $l_Template = str_replace('{release}', JText::_('COM_JDOWNLOADS_FRONTEND_VERSION_TITLE').$files->release, $l_Template);
        }
        
        // Create an additional hint for the description footer when this download has a file but user has not the download permissions 
        if ($download_has_a_file){
            if ($user->guest){
                // when CSS3 buttons are activate, we use it also for the message
                if ($jlistConfigM['use.css.buttons.instead.icons']){               
                    $first_reg_msg = '<div class="'.$jlistConfigM['css.button.color.download'].' '.$jlistConfigM['css.button.size.download'].'">'.JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED').'</div>';
                } else { 
                    $first_reg_msg = '<div class="jdpf_not_logged_in">'.JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED').'</div>';
                }    
            } else {
                // when CSS3 buttons are activate, we use it also for the message
                if ($jlistConfigM['use.css.buttons.instead.icons']){               
                    $first_reg_msg = '<div class="'.$jlistConfigM['css.button.color.download'].' '.$jlistConfigM['css.button.size.download'].'">'.JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED2').'</div>';
                } else {                 
                    $first_reg_msg = '<div class="jdpf_not_logged_in">'.JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED2').'</div>';
                }    
            }         
        } else {
            $first_reg_msg = '';            
        }
        
        // Check and build the description text
        if ($jlistConfigM['plugin.auto.file.short.description'] && $jlistConfigM['plugin.auto.file.short.description.value'] > 0){
            if (strlen($files->description) > $jlistConfigM['plugin.auto.file.short.description.value']){ 
                // Cut description text
                $shorted_text=preg_replace("/[^ ]*$/", '..', substr($files->description, 0, $jlistConfigM['plugin.auto.file.short.description.value']));
                if (!$download_allowed){
                    //$l_Template = str_replace('{description}', $shorted_text.$first_reg_msg, $l_Template);
                    $l_Template = str_replace('{description}', $shorted_text, $l_Template);
                } else {
                    $l_Template = str_replace('{description}', $shorted_text, $l_Template);
                }    
            } else {
                if (!$download_allowed){
                     // $l_Template = str_replace('{description}', $files->description.$first_reg_msg, $l_Template);
                     $l_Template = str_replace('{description}', $files->description, $l_Template);
                } else {     
                     $l_Template = str_replace('{description}', $files->description, $l_Template);
                }     
            }    
        } else {
            if (!$download_allowed){
                 //$l_Template = str_replace("{description}",$files->description.$first_reg_msg, $l_Template);
                 $l_Template = str_replace("{description}",$files->description, $l_Template);
            } else {
                 $l_Template = str_replace("{description}",$files->description,$l_Template);     
            }     
        } 
      
        // create filename
        if ($files->url_download){
            $l_Template = str_replace('{file_name}', JDHelper::getShorterFilename($files->url_download), $l_Template);
        } elseif (isset($files->filename_from_other_download) && $files->filename_from_other_download != ''){    
            $l_Template = str_replace('{file_name}', JDHelper::getShorterFilename($files->filename_from_other_download), $l_Template);
        } else {
            $l_Template = str_replace('{file_name}', '', $l_Template);
        }
        
        // google adsense
        if ($jlistConfigM['google.adsense.active']){
            $l_Template = str_replace('{google_adsense}', stripslashes($jlistConfigM['google.adsense.code']), $l_Template);
        } else {
            $l_Template = str_replace('{google_adsense}', '', $l_Template);
        } 

        // report download link
        if ($jd_user_settings->view_report_form){
            $report_link = '<a href="'.JRoute::_("index.php?option=com_jdownloads&amp;view=report&amp;id=".$files->slug."&amp;catid=".$files->cat_id."&amp;Itemid=".$root_itemid).'" rel="nofollow">'.JText::_('COM_JDOWNLOADS_FRONTEND_REPORT_FILE_LINK_TEXT').'</a>';
            $l_Template = str_replace('{report_link}', $report_link, $l_Template);
        } else {
            $l_Template = str_replace('{report_link}', '', $l_Template);
        }      
      
        // media player
        if ($files->preview_filename){
            // we use the preview file when exist  
            $is_preview = true;
            $files->itemtype = JDHelper::getFileExtension($files->preview_filename);
            $is_playable    = JDHelper::isPlayable($files->preview_filename);
            $extern_media = false;
        } else {                  
            $is_preview = false;
            if ($files->extern_file){
                $extern_media = true;
                $files->itemtype = JDHelper::getFileExtension($files->extern_file);
                $is_playable    = JDHelper::isPlayable($files->extern_file);
            } else {    
                $files->itemtype = JDHelper::getFileExtension($files->url_download);
                $is_playable    = JDHelper::isPlayable($files->url_download);
                $extern_media = false;
            }  
        }            
        
        if ( !$jlistConfigM['flowplayer.use'] && !$jlistConfigM['html5player.use'] && $files->itemtype == 'mp3' ){
            // we use only the 'OLD' mp3 player
            if ($extern_media){
                $mp3_path = $files->extern_file;
            } else {        
                if ($is_preview){
                    // we need the path to the "previews" folder
                    $mp3_path = JUri::base().basename($jlistConfigM['files.uploaddir']).'/'.$jlistConfigM['preview.files.folder.name'].'/'.$files->preview_filename;
                } else {
                    // we use the normal download file for the player
                    $mp3_path = JUri::base().basename($jlistConfigM['files.uploaddir']).'/'.$category_dir.'/'.$files->url_download;
                }   
            }    
            $mp3_config = trim($jlistConfigM['mp3.player.config']);
            $mp3_config = str_replace(';', '&amp;', $mp3_config);
            
            $mp3_player =  
            '<object type="application/x-shockwave-flash" data="components/com_jdownloads/assets/mp3_player_maxi.swf" width="200" height="20">
            <param name="movie" value="components/com_jdownloads/assets/mp3_player_maxi.swf" />
            <param name="wmode" value="transparent"/>
            <param name="FlashVars" value="mp3='.$mp3_path.'&amp;'.$mp3_config.'" />
            </object>';   
           
            if (strpos($l_Template, '{mp3_player}')){
                $l_Template = str_replace('{mp3_player}', $mp3_player, $l_Template);
                $l_Template = str_replace('{preview_player}', '', $l_Template);
            } else {                
                $l_Template = str_replace('{preview_player}', $mp3_player, $l_Template);
            }            
            
        } 
        
        if ( $is_playable ){
            
            if ($jlistConfigM['html5player.use']){
                // we will use the new HTML5 player option
                if ($extern_media){
                    $media_path = $files->extern_file;
                } else {        
                    if ($is_preview){
                        // we need the relative path to the "previews" folder
                        $media_path = JUri::base().basename($jlistConfigM['files.uploaddir']).'/'.$jlistConfigM['preview.files.folder.name'].'/'.$files->preview_filename;
                    } else {
                        // we use the normal download file for the player
                        $media_path = JUri::base().basename($jlistConfigM['files.uploaddir']).'/'.$category_dir.'/'.$files->url_download;
                    }   
                }    
                        
                // create the HTML5 player
                $player = JDHelper::getHTML5Player($files, $media_path);
                
                // we use the player for video files only in listings, when the option allowed this
                if ($jlistConfigM['html5player.view.video.only.in.details'] && $files->itemtype != 'mp3' && $files->itemtype != 'wav' && $files->itemtype != 'oga'){
                    $l_Template = str_replace('{mp3_player}', '', $l_Template);
                    $l_Template = str_replace('{preview_player}', '', $l_Template);
                } else {                            
                    if ($files->itemtype == 'mp4' || $files->itemtype == 'webm' || $files->itemtype == 'ogg' || $files->itemtype == 'ogv' || $files->itemtype == 'mp3' || $files->itemtype == 'wav' || $files->itemtype == 'oga'){
                        // We will replace at first the old placeholder when exist
                        if (strpos($l_Template, '{mp3_player}')){
                            $l_Template = str_replace('{mp3_player}', $player, $l_Template);
                            $l_Template = str_replace('{preview_player}', '', $l_Template);
                        } else {                
                            $l_Template = str_replace('{preview_player}', $player, $l_Template);
                        }    
                    } else {
                        $l_Template = str_replace('{mp3_player}', '', $l_Template);
                        $l_Template = str_replace('{preview_player}', '', $l_Template);
                    }    
                }                
            
            } else {        
    
                if ( $jlistConfigM['flowplayer.use'] && $is_playable ){
                    // we will use the new flowplayer option
                    if ($extern_media){
                        $media_path = $files->extern_file;
                    } else {        
                        if ($is_preview){
                            // we need the relative path to the "previews" folder
                            $media_path = basename($jlistConfigM['files.uploaddir']).'/'.$jlistConfigM['preview.files.folder.name'].'/'.$files->preview_filename;
                        } else {
                            // we use the normal download file for the player
                            $media_path = basename($jlistConfigM['files.uploaddir']).'/'.$category_dir.'/'.$files->url_download;
                        }   
                    }    

                    $ipadcode = '';

                    if ($files->itemtype == 'mp3'){
                        $fullscreen = 'false';
                        $autohide = 'false';
                        $playerheight = (int)$jlistConfigM['flowplayer.playerheight.audio'];
                        // we must use also the ipad plugin identifier when required
                        // see http://flowplayer.blacktrash.org/test/ipad-audio.html and http://flash.flowplayer.org/plugins/javascript/ipad.html
                        if ((bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') || (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')){        
                            $ipadcode = '.ipad();'; 
                        }
                    } else {
                        $fullscreen = 'true';
                        $autohide = 'true';
                        $playerheight = (int)$jlistConfigM['flowplayer.playerheight'];
                    }
                    
                    $player = '<a href="'.$media_path.'" style="display:block;width:'.$jlistConfigM['flowplayer.playerwidth'].'px; height:'.$playerheight.'px;" class="player" id="player'.$files->file_id.'"></a>';
                    $player .= '<script language="JavaScript">
                    // install flowplayer into container
                                flowplayer("player'.$files->file_id.'", "'.JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer-3.2.16.swf",  
                                 {  
                        plugins: {
                            controls: {
                                // insert at first the config settings
                                '.$jlistConfigM['flowplayer.control.settings'].'
                                // and now the basics
                                fullscreen: '.$fullscreen.',
                                height: '.(int)$jlistConfigM['flowplayer.playerheight.audio'].',
                                autoHide: '.$autohide.',
                            }
                            
                        },
                        clip: {
                            autoPlay: false,
                            // optional: when playback starts close the first audio playback
                             onBeforeBegin: function() {
                                $f("player'.$files->file_id.'").close();
                            }
                        }
                    })'.$ipadcode.'; </script>';
                    // the 'ipad code' above is only required for ipad/iphone users                
                    
                    // we use the player for video files only in listings, when the option allowed this
                    if ($jlistConfigM['flowplayer.view.video.only.in.details'] && $files->itemtype != 'mp3'){ 
                        $l_Template = str_replace('{mp3_player}', '', $l_Template);
                        $l_Template = str_replace('{preview_player}', '', $l_Template);            
                    } else {    
                        if ($files->itemtype == 'mp4' || $files->itemtype == 'flv' || $files->itemtype == 'mp3'){    
                            // We will replace at first the old placeholder when exist
                            if (strpos($l_Template, '{mp3_player}')){
                                $l_Template = str_replace('{mp3_player}', $player, $l_Template);
                                $l_Template = str_replace('{preview_player}', '', $l_Template);
                            } else {
                                $l_Template = str_replace('{preview_player}', $player, $l_Template);
                            }                                
                        } else {
                            $l_Template = str_replace('{mp3_player}', '', $l_Template);
                            $l_Template = str_replace('{preview_player}', '', $l_Template);
                        }
                    }
                }
            }
        }                 
            
        if ($jlistConfigM['mp3.view.id3.info'] && $files->itemtype == 'mp3' && !$extern_media){
           // read mp3 infos
            if ($is_preview){
                // get the path to the preview file
                $mp3_path_abs = $jlistConfigM['files.uploaddir'].'/'.$jlistConfigM['preview.files.folder.name'].'/'.$files->preview_filename;
            } else {
                // get the path to the downloads file
                $mp3_path_abs = $jlistConfigM['files.uploaddir'].'/'.$category_dir.'/'.$files->url_download;
            }
            $info = JDHelper::getID3v2Tags($mp3_path_abs);
            if ($info){
                // add it
                $mp3_info = stripslashes($jlistConfigM['mp3.info.layout']);
                $mp3_info = str_replace('{name_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_TITLE'), $mp3_info);
                if ($is_preview){
                    $mp3_info = str_replace('{name}', $files->preview_filename, $mp3_info);
                } else {
                    $mp3_info = str_replace('{name}', $files->url_download, $mp3_info);
                } 
                $mp3_info = str_replace('{album_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_ALBUM'), $mp3_info);
                if (isset($info['TALB'])) {
                    $mp3_info = str_replace('{album}', $info['TALB'], $mp3_info);
                } else {
                    $mp3_info = str_replace('{album}', '', $mp3_info);
                }    
                $mp3_info = str_replace('{artist_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_ARTIST'), $mp3_info);
                if (isset($info['TPE1'])) {
                    $mp3_info = str_replace('{artist}', $info['TPE1'], $mp3_info);
                } else {
                    $mp3_info = str_replace('{artist}', '', $mp3_info);
                }    
                $mp3_info = str_replace('{genre_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_GENRE'), $mp3_info);
                if (isset($info['TCON'])) {
                    $mp3_info = str_replace('{genre}', $info['TCON'], $mp3_info);
                } else {
                    $mp3_info = str_replace('{genre}', '', $mp3_info);
                }    
                $mp3_info = str_replace('{year_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_YEAR'), $mp3_info);
                if (isset($info['TYER'])) {
                    $mp3_info = str_replace('{year}', $info['TYER'], $mp3_info);
                } else {
                    $mp3_info = str_replace('{year}', '', $mp3_info);                 
                }    
                $mp3_info = str_replace('{length_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_LENGTH'), $mp3_info);
                if (isset($info['TLEN'])) {
                    $mp3_info = str_replace('{length}', $info['TLEN'].' '.JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_MINS'), $mp3_info);
                } else {
                    $mp3_info = str_replace('{length}', '', $mp3_info);
                }    
                $l_Template = str_replace('{mp3_id3_tag}', $mp3_info, $l_Template); 
            }     
        }
        
        // replace the {preview_url}
        if ($files->preview_filename){
            // we need the relative path to the "previews" folder
            $media_path = basename($jlistConfigM['files.uploaddir']).'/'.$jlistConfigM['preview.files.folder.name'].'/'.$files->preview_filename;
            $l_Template = str_replace('{preview_url}', $media_path, $l_Template);
        } else {
            $l_Template = str_replace('{preview_url}', '', $l_Template);
        }
        
        // render the tags
        if (!empty($files->tags->itemTags)){ 
            $files->tagLayout = new JLayoutFile('joomla.content.tags');
            $l_Template = str_replace('{tags}', $files->tagLayout->render($files->tags->itemTags), $l_Template);
            $l_Template = str_replace('{tags_title}', JText::_('COM_JDOWNLOADS_TAGS_LABEL'), $l_Template);
        } else {
            $l_Template = str_replace('{tags}', '', $l_Template);
            $l_Template = str_replace('{tags_title}', '', $l_Template);
        }        
                         
        $user_can_see_download_url = false;
       
        // only view download link when user has correct access level
        if ($files->params->get('access-download') == true){     
            $user_can_see_download_url = true;
            $blank_window = '';
            $blank_window1 = '';
            $blank_window2 = '';
            // get file extension
            $view_types = array();
            $view_types = explode(',', $jlistConfigM['file.types.view']);
            $only_file_name = basename($files->url_download);
            $filesextension = JDHelper::getFileExtension($only_file_name);
            if (in_array($filesextension, $view_types)){
                $blank_window = 'target="_blank"';
            }    
            // check is set link to a new window?
            if ($files->extern_file && $files->extern_site   ){
                $blank_window = 'target="_blank"';
            }
            // is 'direct download' activated?
            if ($jlistConfigM['direct.download'] == '0'){ 
                // when not, we must link to the summary page
                $url_task = 'summary';
                $blank_window = '';
                $download_link = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files->slug, $files->cat_id, $files->language, $url_task));
            } else {
                if ($files->license_agree || $files->password || $jd_user_settings->view_captcha) {
                     // user must agree the license - fill out a password field - or fill out the captcha human check - so we must view the summary page!
                    $url_task = 'summary';
                    $download_link = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files->slug, $files->cat_id, $files->language, $url_task));
                } else {     
                    // start the download promptly
                    $url_task = 'download.send';
                    $download_link = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$files->file_id.'&amp;catid='.$files->cat_id.'&amp;m=0');
                }
            }
            
            if ($url_task == 'download.send'){
                // is the old button used?
                if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){   
                    $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfigM['download.pic.details'].'" border="0" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" /></a>';
                } else {
                    // we use the new css button
                     $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color.' '.$download_size_listings.'">'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a>';
                }    
            } else {
                // is the old button used?
                if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){ 
                    $download_link_text = '<a href="'.$download_link.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfigM['download.pic.details'].'" border="0" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" /></a>';
                } else {
                    // we use the new css button                    
                    $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color.' '.$download_size_listings.'">'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a>';
                }    
            }
            if (strpos($l_Template, '{url_download}')){
                $l_Template = str_replace('{url_download}', $download_link_text, $l_Template);
            } else {
                $l_Template = str_replace('{checkbox_list}', $download_link_text, $l_Template);
            }    
            
            // mirrors
            if ($files->mirror_1) {
                if ($files->extern_site_mirror_1 && $url_task == 'download.send'){
                    $blank_window1 = 'target="_blank"';
                }
                $mirror1_link_dum = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$files->file_id.'&amp;catid='.$files->cat_id.'&amp;m=1');
                //$mirror1_link_dum = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files->slug, $files->cat_id, $files->language, $url_task, 1));
                // is the old button used?
                if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){                
                    $mirror1_link = '<a '.$blank_window1.' href="'.$mirror1_link_dum.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfigM['download.pic.mirror_1'].'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_1').'" /></a>';
                } else {
                    // we use the new css button 
                    $mirror1_link = '<a '.$blank_window1.' href="'.$mirror1_link_dum.'" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color_mirror1.' '.$download_size_mirror.'">'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_1').'</a>'; 
                }    
                $l_Template = str_replace('{mirror_1}', $mirror1_link, $l_Template);
            } else {
                $l_Template = str_replace('{mirror_1}', '', $l_Template);
            }
            if ($files->mirror_2) {
                if ($files->extern_site_mirror_2 && $url_task == 'download.send'){
                    $blank_window2 = 'target="_blank"';
                }            
                $mirror2_link_dum = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$files->file_id.'&amp;catid='.$files->cat_id.'&amp;m=2');
                //$mirror2_link_dum = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files->slug, $files->cat_id, $files->language, $url_task, 2));
                // is the old button used?
                if ($jlistConfigM['use.css.buttons.instead.icons'] == '0'){                
                    $mirror2_link = '<a '.$blank_window2.' href="'.$mirror2_link_dum.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfigM['download.pic.mirror_2'].'" border="0" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_2').'" /></a>';
                } else {
                    // we use the new css button 
                    $mirror2_link = '<a '.$blank_window2.' href="'.$mirror2_link_dum.'" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color_mirror2.' '.$download_size_mirror.'">'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_2').'</a>'; 
                }                
                $l_Template = str_replace('{mirror_2}', $mirror2_link, $l_Template);
            } else {
                $l_Template = str_replace('{mirror_2}', '', $l_Template);
            }            

        } else {

            // visitor has not access to download this item - so we will inform him

            if (strpos($l_Template, '{url_download}')){
                $l_Template = str_replace('{url_download}', $first_reg_msg, $l_Template);    
            } else {
                $l_Template = str_replace('{checkbox_list}', $first_reg_msg, $l_Template);    
            }
            
            $l_Template = str_replace('{mirror_1}', '', $l_Template); 
            $l_Template = str_replace('{mirror_2}', '', $l_Template); 
        }
        
        if ($jlistConfigM['view.detailsite']){
            $title_link = JRoute::_(JDownloadsHelperRoute::getDownloadRoute($files->slug, $files->cat_id, $files->language));
            $title_link_text = '<a href="'.$title_link.'">'.$db->escape($files->file_title).'</a>';
            $detail_link_text = '<a href="'.$title_link.'">'.JText::_('COM_JDOWNLOADS_FE_DETAILS_LINK_TEXT_TO_DETAILS').'</a>';
            // Build the file symbol (with link)
            if ($files->file_pic != '' ) {
                $filepic = '<a href="'.$title_link.'">'.'<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files->file_pic.'" align="top" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" border="0" alt="" /></a> ';
            } else {
                $filepic = '';
            }
            $l_Template = str_replace('{file_pic}',$filepic, $l_Template);
            // link to details view at the end
            $l_Template = str_replace('{link_to_details}', $detail_link_text, $l_Template);
            $l_Template = str_replace('{file_title}', $title_link_text, $l_Template);
            
        } elseif ($jlistConfigM['use.download.title.as.download.link']){
            
            if ($user_can_see_download_url){
                // build title link as download link
               if ($url_task == 'download.send'){ 
                  $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jd_download_url">'.$files->file_title.'</a>';
               } else {
                  $download_link_text = '<a href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'">'.$files->file_title.'</a>';                  
               }
               // View file icon also with link
               if ($files->file_pic != '' ) {
                    $filepic = '<a href="'.$download_link.'"><img src="'.JURI::base().'images/jdownloads/fileimages/'.$files->file_pic.'" align="top" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" border="0" alt="" /></a>';
               } else {
                    $filepic = '';
               }
               $l_Template = str_replace('{file_pic}',$filepic, $l_Template);
               $l_Template = str_replace('{link_to_details}', '', $l_Template);
               $l_Template = str_replace('{file_title}', $download_link_text, $l_Template);
            } else {
                // user may not use download link
                $l_Template = str_replace('{file_title}', $files->file_title, $l_Template);
                if ($files->file_pic != '' ) {
                    $filepic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files->file_pic.'" align="top" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" border="0" alt="" />';
                } else {
                    $filepic = '';
                }
                $l_Template = str_replace('{file_pic}',$filepic, $l_Template);
            }    
        } else {
            // no links
            if ($files->file_pic != '' ) {
                $filepic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files->file_pic.'" align="top" width="'.$jlistConfigM['file.pic.size'].'" height="'.$jlistConfigM['file.pic.size.height'].'" border="0" alt="" />';
            } else {
                $filepic = '';
            }
            $l_Template = str_replace('{file_pic}',$filepic, $l_Template);
            // remove link to details view at the end
            $l_Template = str_replace('{link_to_details}', '', $l_Template);
            $l_Template = str_replace('{file_title}', $files->file_title, $l_Template);
        }             
      

      $l_Template = str_replace('{file_title_only}', $files->file_title, $l_Template);
      
      $l_Template = str_replace('{checkbox_list}', '',$l_Template);
      
      $l_Template = str_replace('{file_id}', $files->file_id,$l_Template);
      $l_Template = str_replace('{ordering}', $files->ordering,$l_Template);
      $l_Template = str_replace('{published}', $files->published,$l_Template);
      $l_Template = str_replace('{cat_id}', $files->cat_id,$l_Template);
      
      $l_Template = str_replace('{rank}',$jDFPrank, $l_Template);
      
      $l_Template = str_replace('{mp3_player}', '', $l_Template);
      $l_Template = str_replace('{mp3_id3_tag}', '', $l_Template);      
      $l_Template = str_replace('{preview_player}', '', $l_Template);
      $l_Template = str_replace('{report_link}','', $l_Template);
      $l_Template = str_replace('{sum_jcomments}','', $l_Template);
      $l_Template = str_replace('{rating}','', $l_Template);
      $l_Template = str_replace('{rating_title}','', $l_Template);
      
      // custom fields
      $custom_fields_arr = existsCustomFieldsTitlesPlg();
      $row_custom_values = array('dummy',$files->custom_field_1, $files->custom_field_2, $files->custom_field_3, $files->custom_field_4, $files->custom_field_5,
                                   $files->custom_field_6, $files->custom_field_7, $files->custom_field_8, $files->custom_field_9, $files->custom_field_10, $files->custom_field_11, $files->custom_field_12, $files->custom_field_13, $files->custom_field_14);
      for ($x=1; $x<15; $x++){
          // replace placeholder with title and value
          if (in_array($x,$custom_fields_arr[0]) && $row_custom_values[$x] && $row_custom_values[$x] != '0000-00-00'){
              $l_Template = str_replace("{custom_title_$x}", $custom_fields_arr[1][$x-1], $l_Template);
              if ($x > 5){
                  $l_Template = str_replace("{custom_value_$x}", stripslashes($row_custom_values[$x]), $l_Template);
              } else {
                  $l_Template = str_replace("{custom_value_$x}", $custom_fields_arr[2][$x-1][$row_custom_values[$x]], $l_Template);
              }    
          } else {
              // remove placeholder
              if ($jlistConfigM['remove.field.title.when.empty']){
                  $l_Template = str_replace("{custom_title_$x}", '', $l_Template);
              } else {
                  $l_Template = str_replace("{custom_title_$x}", $custom_fields_arr[1][$x-1], $l_Template);
              }    
              $l_Template = str_replace("{custom_value_$x}", '', $l_Template);
          }    
      }
      
      // insert files title area
      if (!$jDLayoutTitleExists){
            $l_Template = str_replace('{files_title_begin}', '', $l_Template);
            $l_Template = str_replace('{files_title_end}', '', $l_Template);  
            $l_Template = str_replace('{files_title_text}', JText::_('COM_JDOWNLOADS_FE_FILELIST_TITLE_OVER_FILES_LIST'), $l_Template);
            $jDLayoutTitleExists = true;
      } else {
            if (strpos($l_Template, "{files_title_end}") > 0){
                $pos_end = strpos($l_Template, '{files_title_end}');
                $pos_beg = strpos($l_Template, '{files_title_begin}');
                $l_Template = substr_replace($l_Template, '', $pos_beg, ($pos_end - $pos_beg) + 17);
            }
      }      
                
      // support for other content plugins
      // $l_Template = JHTML::_('content.prepare', $l_Template);

      // remove empty html tags
      if ($jlistConfigM['remove.empty.tags']){
          $l_Template = JDHelper::removeEmptyTags($l_Template);
      }
      
      return str_replace("[[[","{",$l_Template);
    }


    // Build the data output for a category placeholder  
    function jd_file_createcategory($matches, $type){
       $db = JFactory::getDBO();
       $user = JFactory::getUser();

       $count = '';
       $cat_result = array();
       $sum = strrchr($matches[2] , ' count==');
       $matches[2] = str_replace($sum, '', $matches[2]);
       $sum = (int)str_replace(' count==', '', $sum);
       if ($sum > 0) $count = 'LIMIT '.$sum; 
      
      // Get the data from the model
      // and convert to array of integer
      $cat_id = implode(',', array_map( 'intval', array_filter( explode(',', $matches[2]), 'is_numeric' ) ));
      
      $query    = $db->getQuery(true);
      $groups    = implode(',', $user->getAuthorisedViewLevels());
      $asset    = 'com_jdownloads.category.'.$cat_id;
       
      // Check at first whether this user may view the items from this category.
      $db->setQuery("SELECT count(*) FROM #__jdownloads_categories WHERE published = 1 AND id IN ($cat_id) AND access IN ($groups)");
      $cat = $db->loadResult();
       
       if ($cat){
           if ($type == 'hottest'){
               // we will view only the most downloaded files from a single category 
               $db->setQuery("SELECT * FROM #__jdownloads_files WHERE published = 1 AND cat_id IN ($cat_id) AND access IN ($groups) ORDER BY downloads desc ".$count);
           } elseif ($type == 'latest'){
               // we will view only the newest files from a single category
               $db->setQuery("SELECT * FROM #__jdownloads_files WHERE published = 1 AND cat_id IN ($cat_id) AND access IN ($groups) ORDER BY date_added desc ".$count);
           } else {
               // only category placeholder is used - so we use the ordering field 
               $db->setQuery("SELECT * FROM #__jdownloads_files WHERE published = 1 AND cat_id IN ($cat_id) AND access IN ($groups) ORDER BY ordering ".$count);
           }       
           $cat_result = '';
           $files = $db->loadObjectList();
           
           if ($files){
               foreach ($files as $file){
                    $matches[1]  = 'file';
                    $matches[2]  = $file->file_id;
                    $file_result = jd_file_createdownload($matches); 
                    $cat_result .= $file_result;   
               }    
           }    

           if ($cat_result) {
               return $cat_result; 
           } else {
             return NULL;
           }
       } else {
           // seems that user have not the permissions to view items from this category/categories
           // or wrong category IDs used
           return '';
       }     
    } 

    function existsCustomFieldsTitlesPlg(){
        global $jlistConfigM;
        // check that any field is activated (has title)
        $custom_arr = array();
        $custom_array = array();
        $custom_titles = array();
        $custom_values = array();
        for ($i=1; $i<15; $i++){
            if ($jlistConfigM["custom.field.$i.title"] != ''){
               $custom_array[] = $i;
               $custom_titles[] = $jlistConfigM["custom.field.$i.title"];
               if (isset($jlistConfigM["custom.field.$i.values"])){
                    $values = explode(',', $jlistConfigM["custom.field.$i.values"]);
                    $custom_values[] = $values; //explode(',', $jlistConfigM["custom.field.$i.values"]);
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

    // Calculate root ItemID of jDownloads component
    function jd_CalcItemid(){
            $db = JFactory::getDBO();
            $db->setQuery("SELECT id from #__menu WHERE link = 'index.php?option=com_jdownloads&view=categories' and published = 1");
            $l_Itemid = $db->loadResult();
            if (!$l_Itemid) $l_Itemid = 0;
            return $l_Itemid;
    }
?>