<?php
/**
 * @package jDownloads
 * @version 3.2  
 * @copyright (C) 2007 - 2013 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
 
defined('_JEXEC') or die('Restricted access');

/**
 * Install Script file of jDownloads component
 */
class com_jdownloadsInstallerScript
{
    
	private $new_version;
    private $new_version_short;
    private $target_joomla_version;
    
    /**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
        
        // try to set time limit
        @set_time_limit(0);

        // try to increase memory limit
        if ((int) ini_get( 'memory_limit' ) < 32){
            @ini_set( 'memory_limit', '32M' );
        }
        
        $db = JFactory::getDBO();
        $params   = JComponentHelper::getParams('com_languages');

        if (!defined('DS')){
           define('DS',DIRECTORY_SEPARATOR);
        }        
        
		// insert the new default header, subheader and footer layouts in every layout.
		require_once(JPATH_SITE."/administrator/components/com_jdownloads/helpers/jd_layouts.php");
        
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		define('JD_BACKEND_PATH' ,  JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jdownloads');
		define('JD_FRONTEND_PATH',  JPATH_ROOT.DS.'components'.DS.'com_jdownloads');
        
        /*
        / Copy frontend images to the joomla images folder
        */
        $target = JPATH_ROOT.DS.'images'.DS.'jdownloads';
        $source = dirname(__FILE__).DS.'site'.DS.'assets'.DS.'images'.DS.'jdownloads';
        
        $images_copy_result   = false;
        $images_folder_exists = false;
        
        if(!JFolder::exists($target))
        {
            $images_copy_result = JFolder::copy($source,$target);
        } else {
            $images_folder_exists = true;
        }       

        // check whether custom css file already exist
        $custom_css_path = JPATH_ROOT.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_custom.css';
        if (!JFile::exists($custom_css_path)){
            // create a new css file
            $text  = "/* Custom CSS File for jDownloads\n";
            $text .= "   If this file already exist then jDownloads does not overwrite it when installing or upgrading jDownloads.\n";
            $text .= "   This file is loaded after the standard jdownloads_fe.css.\n";   
            $text .= "   So you can use it to overwrite the standard css classes for your own customising.\n*/";               
            $x = file_put_contents($custom_css_path, $text, FILE_APPEND);
        }
        
        /*
        / install modules and plugins
        */
        jimport('joomla.installer.installer');
        $status = new JObject();
        $status->modules = array();
        $status->plugins = array();
        $src_modules = dirname(__FILE__).DS.'modules';
        $src_plugins = dirname(__FILE__).DS.'plugins';

        // plugins
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'plg_system_jdownloads');
        $status->plugins[] = array('name'=>'jDownloads System Plugin','group'=>'system', 'result'=>$result);
        
        // systemplugin must be enabled for user group limits and private areas
        $db->setQuery("UPDATE #__extensions SET enabled = '1' WHERE `name` = 'plg_system_jdownloads' AND `type` = 'plugin'");
        $db->execute();

        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'example_plugin_jdownloads');
        $status->plugins[] = array('name'=>'jDownloads Example Plugin','group'=>'jdownloads', 'result'=>$result);
        
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'jdownloads_search');
        $status->plugins[] = array('name'=>'jDownloads Search Plugin','group'=>'search', 'result'=>$result);        

        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'editor_button_plugin_jdownloads_downloads');
        $status->plugins[] = array('name'=>'jDownloads Download Content Button Plugin','group'=>'editors-xtd', 'result'=>$result);        
        
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'plg_content_jdownloads');
        $status->plugins[] = array('name'=>'jDownloads Content Plugin','group'=>'content', 'result'=>$result);        

        // modules
        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_latest');
        $status->modules[] = array('name'=>'mod_jdownloads_latest','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_top');
        $status->modules[] = array('name'=>'mod_jdownloads_top','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_last_updated');
        $status->modules[] = array('name'=>'mod_jdownloads_last_updated','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_most_recently_downloaded');
        $status->modules[] = array('name'=>'mod_jdownloads_most_recently_downloaded','client'=>'site', 'result'=>$result);
        
        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_stats');
        $status->modules[] = array('name'=>'mod_jdownloads_stats','client'=>'site', 'result'=>$result);        

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_tree');
        $status->modules[] = array('name'=>'mod_jdownloads_tree','client'=>'site', 'result'=>$result);
        
        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_related');
        $status->modules[] = array('name'=>'mod_jdownloads_related','client'=>'site', 'result'=>$result);        

/*        
        $installer = new JInstaller;
        $result = $installer->install($src.DS.'mod_jdownloads_rated');
        $status->modules[] = array('name'=>'mod_jdownloads_rated','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_admin_stats');
        $status->modules[] = array('name'=>'mod_jdownloads_admin_stats','client'=>'admin', 'result'=>$result);

  */

       
      ?>
      <hr>
      <div class="adminlist" style="">
        <h4 style="color:#555;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_0'); ?></h4>
        
        <ul>

       <?php
        
       // exist the tables?
       // get DB prefix string
       $prefix = self::getCorrectDBPrefix();
       $tablelist = $db->getTableList();
       
       if ( !in_array ( $prefix.'jdownloads_config', $tablelist ) ){
                Jerror::raiseWarning(null, JText::_('COM_JDOWNLOADS_INSTALL_ERROR_NO_TABLES'));         
                return false;  
       } else {
       
              $jd_version = $this->new_version_short;
              
              
               switch ($this->old_version_found){
                   
                   case '1.9':
                        // view messages when data from old 1.9 version is found 
                        foreach ($this->old_update_message as $upd_msg){
                            echo '<li><font color="green">'.$upd_msg.'</font></li>';
                        }
                        
                        $monitoring = '0';
                        $old_version_found = '1';  // old 1.9 exist
                        
                        // build upload root path
                        $db->setQuery('SELECT `setting_value` FROM #__jdownloads_config_backup WHERE `setting_name` = "files.uploaddir"');
                        $old_upload_dir_name = $db->loadResult();
                        if ($old_upload_dir_name){
                            $jd_upload_root = JPATH_ROOT.DS.$old_upload_dir_name;
                        } else {
                            // we have not found an old folder?
                            $jd_upload_root = JPATH_ROOT.DS.'jdownloads';
                        }                        
                        break;
                   
                   
                   case '3.2':
                        // view messages when data from prior 3.2.x version exist 
                        foreach ($this->old_update_message as $upd_msg){
                            echo $upd_msg;
                        }                        
                        
                        $monitoring = '0';
                        $old_version_found = '0';
                        
                        // build upload root path
                        $db->setQuery('SELECT `setting_value` FROM #__jdownloads_config WHERE `setting_name` = "files.uploaddir"');
                        $old_upload_dir_name = $db->loadResult();
                        if ($old_upload_dir_name){
                            $jd_upload_root = $old_upload_dir_name;
                        } else {
                            // we have not found an old folder?
                            $jd_upload_root = JPATH_ROOT.DS.'jdownloads';
                        }                        
                        break;
                   
                   default:
                        // fresh installation
                        // build upload root path
                        $jd_upload_root = JPATH_ROOT.DS.'jdownloads';
                        $monitoring = '1';
                        $old_version_found = '0';
                        
               } 
              
              if ($this->old_version_found == '1.9' || $this->old_version_found == 0){
                  /*
                  / install config default data - but only when we have really a 'fresh' installation and we have not found any older DB tables
                  */
                  $sum = 0;
                  $query = array();
                     
                  // Replacing backslashes with slashes
                  $jd_upload_root = str_replace('\\', '/', $jd_upload_root);
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('files.uploaddir', '".$jd_upload_root."');"."\n";  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('global.datetime', '".$db->escape(JText::_('COM_JDOWNLOADS_INSTALL_DEFAULT_DATE_FORMAT'))."');"."\n";  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('files.autodetect', '".$monitoring."');"."\n";  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_5'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.option', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.betreff', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_3'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.from', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_4'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.fromname', 'jDownloads');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.html', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('zipfile.prefix', 'downloads_');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('files.order', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('checkbox.top.text', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_1'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('downloads.titletext', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('layouts.editor', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('licenses.editor', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('files.editor', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('categories.editor', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('info.icons.size', '20');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('cat.pic.size', '48');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('file.pic.size', '32');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('offline', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('offline.text', '".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_OFFLINE_MESSAGE_DEFAULT'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('system.list', '".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_SYSTEM_DEFAULT_LIST'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('language.list', '".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_LANGUAGE_DEFAULT_LIST'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('file.types.view', 'html,htm,txt,pdf,doc,jpg,jpeg,png,gif');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('directories.autodetect', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('mail.cloaking', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('tempfile.delete.time', '20');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('frontend.upload.active', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('allowed.upload.file.types', 'zip,rar');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('allowed.upload.file.size', '2048');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('upload.access', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('files.per.side', '10');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('upload.form.text', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('jd.header.title', 'Downloads');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('files.per.side.be', '15');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('last.log.message', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('last.restore.log', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('anti.leech', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('direct.download', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('days.is.file.new', '15');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('picname.is.file.new', 'blue.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('loads.is.file.hot', '100');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('picname.is.file.hot', 'red.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('download.pic.details', 'download_blue.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('upload.auto.publish', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('cats.order', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('autopublish.founded.files', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('all.files.autodetect', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('file.types.autodetect', 'zip,rar,exe,pdf,doc,gif,jpg,png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('jcomments.active', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.defaultlayout','".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NAME'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.show_hot', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.show_new', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.enable_plugin', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.show_jdfiledisabled', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.layout_disabled','".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NAME'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.show_downloadtitle', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.offline_title','".$db->escape(JText::_('COM_JDOWNLOADS_FRONTEND_SETTINGS_FILEPLUGIN_OFFLINE_FILETITLE'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fileplugin.offline_descr','".$db->escape(JText::_('COM_JDOWNLOADS_FRONTEND_SETTINGS_FILEPLUGIN_DESCRIPTION'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('cat.pic.default.filename','folder.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('file.pic.default.filename','zip.png');"."\n";
                  
                  foreach ($query as $data){
                        $db->SetQuery($data);
                        $db->execute();
                  }      
                  unset($query);
                  
                  $query[]  = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('jd.version','$jd_version');"."\n";
                  //$sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('jd.version.state','$jd_version_state');"."\n";
                  //$sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('jd.version.svn','$jd_version_svn');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.upload', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_5'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.option.upload', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.betreff.upload', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_6'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.from.upload', '".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_INSTALL_4'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.fromname.upload', 'jDownloads');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.html.upload', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.template.upload', '".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_UPLOAD_TEMPLATE'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.template.download', '".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_MAIL_DEFAULT'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('download.pic.mirror_1', 'mirror_blue1.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('download.pic.mirror_2', 'mirror_blue2.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('picname.is.file.updated', 'green.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('days.is.file.updated', '15');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('thumbnail.size.width', '125');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('thumbnail.size.height', '125');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('thumbnail.view.placeholder', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('thumbnail.view.placeholder.in.lists', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('option.navigate.bottom', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('option.navigate.top', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.category.info', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('save.monitoring.log', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.subheader', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.detailsite', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('check.leeching', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('allowed.leeching.sites', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('block.referer.is.empty', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.author', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.author.url', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.release', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.price', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.license', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.language', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.system', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.pic.upload', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.desc.long', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('mp3.player.config', 'loop=0;showvolume=1;showstop=1;bgcolor1=006699;bgcolor2=66CCFF');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('mp3.view.id3.info', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.php.script.for.download', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('mp3.info.layout', '".$JLIST_BACKEND_SETTINGS_TEMPLATES_ID3TAG."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('google.adsense.active', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('google.adsense.code', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('countdown.active', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('countdown.start.value', '15');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('countdown.text', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.extern.file', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.select.file', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.view.desc.short', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fix.upload.filename.blanks', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fix.upload.filename.uppercase', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fix.upload.filename.specials', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.report.download.link', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('send.mailto.report', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('download.pic.files', 'download2.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.sum.jcomments', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('be.new.files.order.first', '1');"."\n";
                  
                  foreach ($query as $data){
                        $db->SetQuery($data);
                        $db->execute();
                  }      
                  unset($query);

                  $query[]  = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('downloads.footer.text', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.back.button', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.auto.cat.dir', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('reset.counters', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('report.link.only.regged', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.ratings', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('rating.only.for.regged', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.also.download.link.text', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('auto.file.short.description', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('auto.file.short.description.value', '200');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.jom.comment', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.lightbox.function', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.alphauserpoints', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.alphauserpoints.with.price.field', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('user.can.download.file.when.zero.points', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('user.message.when.zero.points', '".$db->escape(JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_FE_MESSAGE_NO_DOWNLOAD'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('limited.download.number.per.day', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('limited.download.reached.message', '".$db->escape(JText::_('COM_JDOWNLOADS_FE_MESSAGE_AMOUNT_FILES_LIMIT'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('download.pic.plugin', 'download2.png');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plugin.auto.file.short.description', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plugin.auto.file.short.description.value', '200');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.sort.order', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('activate.general.plugin.support', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('activate.download.log', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('categories.per.side', '5');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('upload.access.group', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('redirect.after.download', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.tabs.type', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('additional.tab.title.1', '".$db->escape(JText::_('COM_JDOWNLOADS_FE_TAB_CUSTOM_TITLE'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('additional.tab.title.2', '".$db->escape(JText::_('COM_JDOWNLOADS_FE_TAB_CUSTOM_TITLE'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('additional.tab.title.3', '".$db->escape(JText::_('COM_JDOWNLOADS_FE_TAB_CUSTOM_TITLE'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('remove.field.title.when.empty', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.download.title.as.download.link', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.1.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.2.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.3.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.4.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.5.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.6.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.7.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.8.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.9.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.10.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.11.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.12.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.13.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.14.title', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.1.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.2.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.3.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.4.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.5.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.6.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.7.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.8.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.9.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('custom.field.10.values', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('group.can.edit.fe', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('uploader.can.edit.fe', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.sef.with.file.titles', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.general.plugin.support.only.for.descriptions', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('com', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.blocking.list', '0');"."\n";
                  
                  $blocking_list = file_get_contents ( JPATH_SITE.'/administrator/components/com_jdownloads/assets/blacklist.txt' );
                  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('blocking.list', '$blocking_list');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('remove.empty.tags', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.pdf.thumbs', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.pdf.thumbs.by.scan', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('pdf.thumb.height', '200');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('pdf.thumb.width', '200');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('pdf.thumb.pic.height', '400');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('pdf.thumb.pic.width', '400');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('pdf.thumb.image.type', 'GIF');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.auto.thumbs.from.pics', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.auto.thumbs.from.pics.image.height', '400');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.auto.thumbs.from.pics.image.width', '400');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('create.auto.thumbs.from.pics.by.scan', '0');"."\n";
                  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('fe.upload.amount.of.pictures', '10');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('be.upload.amount.of.pictures', '10');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('imagemagick.path', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('uncategorised.files.folder.name', '_uncategorised_files');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('tempzipfiles.folder.name', '_tempzipfiles');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('categories.batch.in.progress', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('downloads.batch.in.progress', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.unicode.path.names', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('report.mail.subject', '".$db->escape(JText::_('COM_JDOWNLOADS_CONFIG_REPORT_FILE_MAIL_SUBJECT_DEFAULT'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('report.mail.layout', '".$db->escape(JText::_('COM_JDOWNLOADS_CONFIG_REPORT_FILE_MAIL_LAYOUT_DEFAULT'))."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('report.form.layout', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('report.form.layout.css', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('robots', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.real.user.name.in.frontend', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('global.datetime.short', '".JText::_('COM_JDOWNLOADS_INSTALL_DEFAULT_DATE_FORMAT_SHORT')."');"."\n";                                                                                                                                                                         
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.css.buttons.instead.icons', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.empty.categories', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.empty.sub.categories', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('cat.pic.size.height', '48');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('file.pic.size.height', '32');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('autopublish.default.description', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('view.no.file.message.in.empty.category', '0');"."\n";                           
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.runtime', 'full');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.max.file.size', '10');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.chunk.size', '0');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.chunk.unit', 'mb');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.rename', '0');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.image.file.extensions', 'gif,png,jpg,jpeg,GIF,PNG,JPG,JPEG');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.other.file.extensions', 'zip,rar,pdf,doc,txt,ZIP,RAR,PDF,DOC,TXT');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.unique.names', '0');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.enable.image.resizing', '0');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.resize.width', '640');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.resize.height', '480');"."\n";  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.resize.quality', '90');"."\n";  
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('plupload.enable.uploader.log', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('private.area.folder.name', '_private_user_area');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('delete.also.images.from.downloads', '0');"."\n";                
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('preview.files.folder.name', '_preview_files');"."\n"; 
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('delete.also.preview.files.from.downloads', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.hot', 'jred');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.new', 'jorange');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.updated', 'jblue');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.download', 'jorange');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.mirror1', 'jgray');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.mirror2', 'jgray');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.size.download', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.size.download.mirror', 'jmedium');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.color.download.mirror', 'jorange');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('css.button.size.download.small', 'jsmall');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('flowplayer.use', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('flowplayer.playerwidth', '300');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('flowplayer.playerheight', '200');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('flowplayer.playerheight.audio', '30');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('flowplayer.control.settings', '');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('flowplayer.view.video.only.in.details', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('use.pagination.subcategories', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('amount.subcats.per.page.in.pagination', '5');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('shortened.filename.length', '15');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('old.jd.release.found', '".$old_version_found."');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.uncategorised', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.all', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.topfiles', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.newfiles', '0');"."\n";
                  // added in 3.2.37
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.levels', '0');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.use', '1');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.width', '320');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.height', '240');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.audio.width', '250');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.view.video.only.in.details', '0');"."\n";
                  // added in 3.2.41
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('featured.pic.size', '48');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('featured.pic.size.height', '48');"."\n";
                  $sum++; $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('featured.pic.filename', 'featured_orange_star.png');"."\n";                            
                  foreach ($query as $data){
                        $db->SetQuery($data);
                        $db->execute();
                  }      
                  unset($query);           
              
                  echo '<li><font color="green">'.JText::sprintf('COM_JDOWNLOADS_INSTALL_2', $sum).'</font></li>';

                  // write default layouts in database      
                  $sum_layouts = 13;

                  // categories
                  $cats_layout       = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_CATS_DEFAULT);
                  $cats_header       = stripslashes($cats_header);
                  $cats_subheader    = stripslashes($cats_subheader);
                  $cats_footer       = stripslashes($cats_footer);
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DEFAULT_NAME')."', 1, '".$cats_layout."', '".$cats_header."', '".$cats_subheader."', '".$cats_footer."', 1, 1, '*')");
                  $db->execute();

                  // category
                  $cat_layout       = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_CAT_DEFAULT);
                  $cat_header       = stripslashes($cat_header);
                  $cat_subheader    = stripslashes($cat_subheader);
                  $cat_footer       = stripslashes($cat_footer);
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CAT_DEFAULT_NAME')."', 4, '".$cat_layout."', '".$cat_header."', '".$cat_subheader."', '".$cat_footer."', 1, 1, '*')");
                  $db->execute();              
                  
                  // files
                  $file_layout        = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT);
                  $files_header       = stripslashes($files_header);
                  $files_subheader    = stripslashes($files_subheader);
                  $files_footer       = stripslashes($files_footer);
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NAME')."', 2, '".$file_layout."', '".$files_header."', '".$files_subheader."', '".$files_footer."', 0, 1, '*')");
                  $db->execute();
                   
                  // summary
                  $summary_layout       = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_SUMMARY_DEFAULT);
                  $$summary_header      = stripslashes($summary_header);
                  $summary_subheader    = stripslashes($summary_subheader);
                  $summary_footer       = stripslashes($summary_footer);              
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_SUMMARY_DEFAULT_NAME')."', 3, '".$summary_layout."', '".$summary_header."', '".$summary_subheader."', '".$summary_footer."', 1, 1, '*')");
                  $db->execute();

                  // download details 
                  $detail_layout        = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT);
                  $details_header       = stripslashes($details_header);
                  $details_subheader    = stripslashes($details_subheader);
                  $details_footer       = stripslashes($details_footer);               
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT_NAME')."', 5, '$detail_layout', '".$details_header."', '".$details_subheader."', '".$details_footer."', 1, 1, '*')");
                  $db->execute();
                  
                  // layout for download details with tabs
                  $detail_layout = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT_WITH_TABS);
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_DETAILS_WITH_TABS_TITLE')."', 5, '$detail_layout', '".$details_header."', '".$details_subheader."', '".$details_footer."', '0', 1, '*')");
                  $db->execute();
                  
                  // layout for download details with all new data fields 2.5
                  $detail_layout = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT_NEW_25);
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_DETAILS_25_TITLE')."', 5, '$detail_layout', '".$details_header."', '".$details_subheader."', '".$details_footer."', '0', 1, '*')");
                  $db->execute();              
                        
                  // Simple layout with Checkboxes for files
                  $file_layout = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_SIMPLE_1); 
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, note, checkbox_off, symbol_off, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_SIMPLE_1_NAME')."', 2, '".$file_layout."', '".$files_header."', '".$files_subheader."', '".$files_footer."', 0, 1, '', 0, 1, '*')");
                  $db->execute();
                        
                  // Simple layout without Checkboxes for files
                  $file_layout = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_SIMPLE_2); 
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, note, checkbox_off, symbol_off, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_SIMPLE_2_NAME')."', 2, '".$file_layout."', '".$files_header."', '".$files_subheader."', '".$files_footer."', 1, 1, '', 1, 1, '*')");
                  $db->execute();
                        
                  // categories layout with 4 columns
                  $file_layout = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_CATS_COL_DEFAULT); 
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, note, cols, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_COL_TITLE')."', 1, '".$file_layout."', '".$files_header."', '".$files_subheader."', '".$files_footer."', 0, 1, '".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_COL_NOTE')."', 4, '*')");
                  $db->execute();

                        
                  //This layout is used to view the subcategories from a category with pagination. 
                  $cats_layout        = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_SUBCATS_PAGINATION_DEFAULT);
                  $cats_layout_before = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_SUBCATS_PAGINATION_BEFORE);
                  $cats_layout_after  = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_SUBCATS_PAGINATION_AFTER);
                  $cats_header       = '';
                  $cats_subheader    = '';
                  $cats_footer       = '';
                  $note              = stripslashes(JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_USE_SUBCATS_NOTE'));
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_before_text, template_after_text, note, template_active, locked, language, use_to_view_subcats)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DEFAULT_PAGINATION_NAME')."', 1, '".$cats_layout."', '".$cats_header."', '".$cats_subheader."', '".$cats_footer."', '".$cats_layout_before."', '".$cats_layout_after."', '".$db->escape($note)."', 0, 1, '*', 1)");
                  $db->execute();
                  
                  // New alternate layout (used CSS classes) 
                  $file_layout        = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_NEW_ALTERNATE_1);
                  $file_layout_before = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_NEW_ALTERNATE_1_BEFORE);
                  $file_layout_after  = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_NEW_ALTERNATE_1_AFTER);
                  $files_header       = stripslashes($files_header);
                  $files_subheader    = stripslashes($files_subheader);
                  $files_footer       = stripslashes($files_footer);
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_before_text, template_after_text, note, checkbox_off, symbol_off, template_active, locked, language, use_to_view_subcats)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_ALTERNATE_1_NAME')."', 2, '".$file_layout."', '".$files_header."', '".$files_subheader."', '".$files_footer."', '".$file_layout_before."', '".$file_layout_after."', '', 1, 1, 0, 1, '*', 0)");
                  $db->execute();                            
                
                  // default search results layout
                  $search_result_layout = stripslashes($JLIST_BACKEND_SETTINGS_TEMPLATES_SEARCH_DEFAULT);
                  $search_header       = stripslashes($search_header);
                  $search_subheader    = stripslashes($search_subheader);
                  $search_footer       = stripslashes($search_footer);  
                  $db->setQuery("INSERT INTO #__jdownloads_templates (template_name, template_typ, template_text, template_header_text, template_subheader_text, template_footer_text, template_active, locked, note, cols, language)  VALUES ('".JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_SEARCH_DEFAULT_NAME')."', 7, '".$search_result_layout."', '".$search_header."', '".$search_subheader."', '".$search_footer."', 1, 1, '', 4, '*')");
                  $db->execute();
                  
                  echo '<li><font color="green">'.JText::sprintf('COM_JDOWNLOADS_INSTALL_4', $sum_layouts).'</font></li>';
              
                  // Write default licenses in database      
          
                  $lic_total = (int)JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE_TOTAL');                                 
                  $sum_licenses = 7;

                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE1_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE1_TITLE'))."', '', '".JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE1_URL')."', '*', 1, 1)");
                  $db->execute();

                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE2_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE2_TITLE'))."', '', '".JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE2_URL')."', '*', 1, 1)");
                  $db->execute();
                  
                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE3_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE3_TITLE'))."', '".JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE3_TEXT')."', '', '*', 1, 1)");
                  $db->execute();
          
                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE4_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE4_TITLE'))."', '".JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE4_TEXT')."', '', '*', 1, 1)");
                  $db->execute();

                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE5_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE5_TITLE'))."', '".JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE5_TEXT')."', '', '*', 1, 1)");
                  $db->execute();

                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE6_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE6_TITLE'))."', '', '', '*', 1, 1)");
                  $db->execute();

                  $db->setQuery("INSERT INTO #__jdownloads_licenses (title, alias, description, url, language, published, ordering)  VALUES ('".$db->escape(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE7_TITLE'))."', '".JApplication::stringURLSafe(JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE7_TITLE'))."', '', '".JText::_('COM_JDOWNLOADS_SETTINGS_LICENSE7_URL')."', '*', 1, 1)");
                  $db->execute();

                  echo '<li><font color="green">'.JText::sprintf('COM_JDOWNLOADS_INSTALL_6', $sum_licenses).'</font></li>';
              }              
              
              // final checks
              
              // Checked if exist Falang - if yes, move the files

              if (JFolder::exists(JPATH_SITE.'/administrator/components/com_falang/contentelements') && !JFile::exists(JPATH_SITE.'/administrator/components/com_falang/contentelements/jdownloads_files.xml')){
                  $fishresult = 1;
                  JFile::copy( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang/jdownloads_categories.xml", JPATH_SITE."/administrator/components/com_falang/contentelements/jdownloads_categories.xml");
                  JFile::copy( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang/jdownloads_config.xml", JPATH_SITE."/administrator/components/com_falang/contentelements/jdownloads_config.xml");
                  JFile::copy( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang/jdownloads_files.xml", JPATH_SITE."/administrator/components/com_falang/contentelements/jdownloads_files.xml");
                  JFile::copy( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang/jdownloads_templates.xml", JPATH_SITE."/administrator/components/com_falang/contentelements/jdownloads_templates.xml");
                  JFile::copy( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang/jdownloads_licenses.xml", JPATH_SITE."/administrator/components/com_falang/contentelements/jdownloads_licenses.xml");
                  JFile::copy( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang/jdownloads_usergroups_limits.xml", JPATH_SITE."/administrator/components/com_falang/contentelements/jdownloads_usergroups_limits.xml");
                  JFolder::delete( JPATH_SITE."/administrator/components/com_jdownloads/assets/falang"); 
              } else { 
                  $fishresult = 0;
              }               
              
              if ($fishresult) {
                  echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_17')." ".JPATH_SITE.'/administrator/components/com_falang/contentelements'.'</font></li>';
              } else {
                  echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_18')." ".JPATH_SITE.'/administrator/components/com_jdownloads/assets/falang'.'<br />'.JText::_('COM_JDOWNLOADS_INSTALL_19').'</font></li>';
              }        
        
            
            if ($this->old_version_found == '3.2'){
                // update the stored version when we have db data from older 3.2 version 
                $query  = "UPDATE #__jdownloads_config SET setting_value = '$jd_version' WHERE setting_name = 'jd.version'";
                $db->SetQuery($query);
                $db->execute();
            }
            
            // Check default upload directory 
            $dir_exist = JFolder::exists($jd_upload_root);
            
            $indexhtml_source = dirname(__FILE__).DS.'index.html'; 
            
            if ($dir_exist) {
                if (is_writable($jd_upload_root)) {
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_7').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_8').'</strong></font></li>';
                    
                }
            } else {
                if ($makedir =  JFolder::create($jd_upload_root, 0755)) {
                    // copy the index.html to the new folder
                    JFile::copy($indexhtml_source, $jd_upload_root.DS.'index.html');
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_9').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_10').'</strong></font></li>'; 
                }
            }

            // Check default directory for uncategorisied downloads
            $dir_exist_uncat = JFolder::exists($jd_upload_root.DS.'_uncategorised_files');

            if($dir_exist_uncat) {
                if (is_writable($jd_upload_root.DS.'_uncategorised_files')) {
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_22').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_23').'</strong></font></li>';
                }
            } else {
                if ($makedir =  JFolder::create($jd_upload_root.DS.'_uncategorised_files', 0755)) {
                    // copy the index.html to the new folder
                    JFile::copy($indexhtml_source, $jd_upload_root.DS.'_uncategorised_files'.DS.'index.html');
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_20').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_21').'</strong></font></li>'; 
                }
            }
            
            // Check default directory for preview files like mp3 or avi
            $dir_exist_preview = JFolder::exists($jd_upload_root.DS.'_preview_files');

            if($dir_exist_preview) {
                if (is_writable($jd_upload_root.DS.'_preview_files')) {
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_30').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_31').'</strong></font></li>';
                }
            } else {
                if ($makedir =  JFolder::create($jd_upload_root.DS.'_preview_files', 0755)) {
                    // copy the index.html to the new folder
                    JFile::copy($indexhtml_source, $jd_upload_root.DS.'_preview_files'.DS.'index.html');
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_28').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_29').'</strong></font></li>';
                }
            }            
            
            // Check default directory for private user area
            $dir_exist_private = JFolder::exists($jd_upload_root.DS.'_private_user_area');

            if($dir_exist_private) {
                if (is_writable($jd_upload_root.DS.'_private_user_area')) {
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_26').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_27').'</strong></font></li>';
                }
            } else {
                if ($makedir =  JFolder::create($jd_upload_root.DS.'_private_user_area', 0755)) {
                    // copy the index.html to the new folder
                    JFile::copy($indexhtml_source, $jd_upload_root.DS.'_private_user_area'.DS.'index.html');
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_24').'</font></li>';
                } else {
                     echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_25').'</strong></font></li>';
                }
            }                          
            
            // tempzipfiles
            $dir_existzip = JFolder::exists($jd_upload_root.DS.'_tempzipfiles');

            if($dir_existzip) {
               if (is_writable($jd_upload_root.DS.'_tempzipfiles')) {
                   echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_11').'</font></li>';
               } else {
                   echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_12').'</strong></font></li>';
               }
            } else {
                if ($makedir = JFolder::create($jd_upload_root.DS.'_tempzipfiles'.DS, 0755)) {
                    // copy the index.html to the new folder
                    JFile::copy($indexhtml_source, $jd_upload_root.DS.'_tempzipfiles'.DS.'index.html');
                    echo '<li><font color="green">'.JText::_('COM_JDOWNLOADS_INSTALL_13').'</font></li>';
                } else {
                    echo '<li><font color="red"><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_14').'</strong></font></li>';
                }
             }
       
       
              echo '</ul>';
              echo '<font color="555">'.JText::_('COM_JDOWNLOADS_INSTALL_DB_TIP').'</font>';

        /*
        / Display the results from the extension installation
        /
        / 
        /
        */ 
        
        $rows = 0;
        ?>                           

        
        </div>
        <hr>

        <table class="adminlist" width="100%" style="margin:10px 10px 10px 10px;">
            <thead>
                <tr>
                    <th class="title" style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_EXTENSION'); ?></th>
                    <th width="50%"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($status->modules)) : ?>
                <tr>
                    <th style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_MODULE'); ?></th>
                </tr>
                <?php foreach ($status->modules as $module) : ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td style="text-align:center;"><?php echo ($module['result'])?JText::_('COM_JDOWNLOADS_INSTALL_INSTALLED'):JText::_('COM_JDOWNLOADS_INSTALL_NOT_INSTALLED'); ?></td>
                </tr>
                <?php endforeach;?>
                <?php endif;?>
                <?php if (count($status->plugins)) : ?>
                <tr>
                    <th style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_PLUGIN'); ?></th>
                </tr>
                <?php foreach ($status->plugins as $plugin) : ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td style="text-align:center;"><?php echo ($plugin['result'])?JText::_('COM_JDOWNLOADS_INSTALL_INSTALLED'):JText::_('COM_JDOWNLOADS_INSTALL_NOT_INSTALLED'); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
       }
		
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
	
        jimport('joomla.installer.installer');
        $db = JFactory::getDBO();
        
        $status = new JObject();
        $status->modules = array();
        $status->plugins = array();
        $src = $src = dirname(__FILE__);

        // Top Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_top" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Top Module','client'=>'site', 'result'=>$result);
        }

        // Latest Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_latest" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Latest Module','client'=>'site', 'result'=>$result);
        }

        // Last Upadated Downloads Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_last_updated" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Last Updated Module','client'=>'site', 'result'=>$result);
        }        

        // Most Recently Downloaded Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_most_recently_downloaded" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Most Recently Downloaded Module','client'=>'site', 'result'=>$result);
        }  
        
        // Stats Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_stats" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Stats Module','client'=>'site', 'result'=>$result);
        }        
        
        // Tree Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_tree" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Tree Module','client'=>'site', 'result'=>$result);
        }        

        // Tree Module
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_jdownloads_related" AND `type` = "module"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('module',$id,1);
            $status->modules[] = array('name'=>'jDownloads Tree Module','client'=>'site', 'result'=>$result);
        } 
        
        // System Plugin
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `name` = "plg_system_jdownloads" AND `folder` = "system"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads System Plugin','group'=>'system', 'result'=>$result);
        }

        // Search Plugin
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `name` = "plg_search_jdownloads" AND `folder` = "search"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads Search Plugin','group'=>'search', 'result'=>$result);
        }        
        
        // Example Plugin
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "example_plugin_jdownloads" AND `folder` = "jdownloads"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads Example Plugin','group'=>'jdownloads', 'result'=>$result);
        }
        
        // Button Plugin Download Link
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "downloadlink" AND `folder` = "editors-xtd"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads Download Link Button Plugin','group'=>'editors-xtd', 'result'=>$result);
        }        		

        // Button Plugin Download Content
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "jdownloads" AND `folder` = "editors-xtd"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads Download Content Button Plugin','group'=>'editors-xtd', 'result'=>$result);
        } 
		
        // Content Plugin
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `name` = "Content - jDownloads" AND `folder` = "content"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads Content Plugin','group'=>'content', 'result'=>$result);
        }

        $rows = 0;
        ?>
        
        <h4><?php echo JText::_('COM_JDOWNLOADS_DEINSTALL_0'); ?></h4>
        
        <?php
        $msg = '';
        $msg = '<hr><p align="center"><b><span style="color:#00CC00">The download folder and all subfolders still exists!</b></p>' 
               .'<p align="center"><b><span style="color:#00CC00">Folder images/jdownloads/ still exists! </b></p>'
               .'<p align="center"><b><span style="color:#00CC00">All jDownloads database tables still exist!</b></p>'
               .'<p align="center">Please delete it (them) manually, if you want.</p><hr>';
        echo $msg;
        ?>
                
        <table class="adminlist" width="100%">
            <thead>
                <tr>
                    <th class="title" style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_EXTENSION'); ?></th>
                    <th width="50%"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="row0">
                    <td class="key"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_COMPONENT').' '.JText::_('COM_JDOWNLOADS_INSTALL_JDOWNLOADS'); ?></td>
                    <td style="text-align:center;"><?php echo JText::_('COM_JDOWNLOADS_DEINSTALL_REMOVED'); ?></td>
                </tr>
                <?php if (count($status->modules)) : ?>
                <tr>
                    <th style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_MODULE'); ?></th>
                </tr>
                <?php foreach ($status->modules as $module) : ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td style="text-align:center"><?php echo ($module['result'])?JText::_('COM_JDOWNLOADS_DEINSTALL_REMOVED'):JText::_('COM_JDOWNLOADS_DEINSTALL_NOT_REMOVED'); ?></td>
                </tr>
                <?php endforeach;?>
                <?php endif;?>
                <?php if (count($status->plugins)) : ?>
                <tr>
                    <th style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_PLUGIN'); ?></th>
                </tr>
                <?php foreach ($status->plugins as $plugin) : ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td style="text-align:center;"><?php echo ($plugin['result'])?JText::_('COM_JDOWNLOADS_DEINSTALL_REMOVED'):JText::_('COM_JDOWNLOADS_DEINSTALL_NOT_REMOVED'); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <hr>
        <?php
		
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
        
        if (!defined('DS')){
           define('DS',DIRECTORY_SEPARATOR);
        }
                
        $db = JFactory::getDBO();
        $params   = JComponentHelper::getParams('com_jdownloads');

        $prefix = self::getCorrectDBPrefix();
        $tablelist = $db->getTableList();
        
        $rows = 0;
       
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');        

        // updated in 3.2.8
        // add new options in config for category select box in frontend
        $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'show.header.catlist.uncategorised'");
        $is_option = $db->loadResult();
        if (!isset($is_option)){
             $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.uncategorised', '1');"."\n";
             $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.all', '1');"."\n";
             $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.topfiles', '0');"."\n";
             $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.newfiles', '0');"."\n";
            
            foreach ($query as $data){
                $db->SetQuery($data);
                $db->execute();            
            }      
            unset($query);
        }        
        
        // install the new modules when this not already exists
        jimport('joomla.installer.installer');

        $status = new JObject();
        $status->modules = array();
        $status->plugins = array();
        
        $src_modules = dirname(__FILE__).DS.'modules';
        $src_plugins = dirname(__FILE__).DS.'plugins';
        $path_top           = JPATH_ROOT.'/modules/mod_jdownloads_top';
        $path_latest        = JPATH_ROOT.'/modules/mod_jdownloads_latest';
        $path_last_updated  = JPATH_ROOT.'/modules/mod_jdownloads_last_updated';
        $button_downloadlink = JPATH_ROOT.'/plugins/editors-xtd/downloadlink';
        $button_download     = JPATH_ROOT.'/plugins/editors-xtd/jdownloads';
        $content_plugin      = JPATH_ROOT.'/plugins/content/jdownloads';        

        // we must install again all modules and plugins since it can be that we must also install here an update
        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_latest');
        $status->modules[] = array('name'=>'mod_jdownloads_latest','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_top');
        $status->modules[] = array('name'=>'mod_jdownloads_top','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_last_updated');
        $status->modules[] = array('name'=>'mod_jdownloads_last_updated','client'=>'site', 'result'=>$result);

        $installer = new JInstaller;
        $result = $installer->install($src_modules.DS.'mod_jdownloads_most_recently_downloaded');
        $status->modules[] = array('name'=>'mod_jdownloads_most_recently_downloaded','client'=>'site', 'result'=>$result);
        
        $installer = new JInstaller;                                                                                     
        $result = $installer->install($src_modules.DS.'mod_jdownloads_stats');
        $status->modules[] = array('name'=>'mod_jdownloads_stats','client'=>'site', 'result'=>$result); 

        $installer = new JInstaller;                                                                                     
        $result = $installer->install($src_modules.DS.'mod_jdownloads_tree');
        $status->modules[] = array('name'=>'mod_jdownloads_tree','client'=>'site', 'result'=>$result);         

        $installer = new JInstaller;                                                                                     
        $result = $installer->install($src_modules.DS.'mod_jdownloads_related');
        $status->modules[] = array('name'=>'mod_jdownloads_related','client'=>'site', 'result'=>$result);
        
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'plg_system_jdownloads');
        $status->plugins[] = array('name'=>'jDownloads System Plugin','group'=>'system', 'result'=>$result);
                
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'jdownloads_search');
        $status->plugins[] = array('name'=>'jDownloads Search Plugin','group'=>'search', 'result'=>$result);       
        
        /*
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'editor_button_plugin_jdownloads_downloadlink');
        $status->plugins[] = array('name'=>'jDownloads Download Link Button Plugin','group'=>'editors-xtd', 'result'=>$result);               
        */
        // we must uninstall the downloadlink plugin
        // Button Plugin Download Link
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "downloadlink" AND `folder` = "editors-xtd"');
        $id = $db->loadResult();
        if($id)
        {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin',$id,1);
            $status->plugins[] = array('name'=>'jDownloads Download Link Button Plugin','group'=>'editors-xtd', 'result'=>$result);
        }        
        
        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'editor_button_plugin_jdownloads_downloads');
        $status->plugins[] = array('name'=>'jDownloads Download Content Button Plugin','group'=>'editors-xtd', 'result'=>$result);               

        $installer = new JInstaller;
        $result = $installer->install($src_plugins.DS.'plg_content_jdownloads');
        $status->plugins[] = array('name'=>'jDownloads Content Plugin','group'=>'content', 'result'=>$result);               
                
        // updated in 3.2.9
        // We must add default values for some user groups 'importance' fields.        
        $db->setQuery("SELECT COUNT(*) FROM #__jdownloads_usergroups_limits WHERE importance > 0");
        $importance_values_exists = $db->loadResult();
        
        if (!$importance_values_exists){
            // Get all rules
            $db->setQuery("SELECT * FROM #__jdownloads_usergroups_limits");
            $jd_groups = $db->loadObjectList();
            
            // Create the default values
            if ($jd_groups){
                   for ($i=0; $i < count($jd_groups); $i++) {
                       if ((int)$jd_groups[$i]->group_id == 1){ 
                           $importance = 1; 
                        } elseif ((int)$jd_groups[$i]->group_id == 2){ 
                           $importance = 20;
                        } elseif ((int)$jd_groups[$i]->group_id == 3){ 
                            $importance = 30;
                        } elseif ((int)$jd_groups[$i]->group_id == 4){ 
                            $importance = 40;
                        } elseif ((int)$jd_groups[$i]->group_id == 5){ 
                            $importance = 50;
                        } elseif ((int)$jd_groups[$i]->group_id == 6){ 
                            $importance = 60;
                        } elseif ((int)$jd_groups[$i]->group_id == 7){ 
                            $importance = 70;
                        } elseif ((int)$jd_groups[$i]->group_id == 8){ 
                            $importance = 100;
                        } else {
                            $importance = 0;
                        }
                        $id = (int)$jd_groups[$i]->id;
                        $db->SetQuery("UPDATE #__jdownloads_usergroups_limits SET importance = '$importance' WHERE id = '$id'");
                        $db->execute();
                   }
            }           
        } // end jdownloads_usergroups_limits update 
        
        // updated in 3.2.14
        // we had forget in the 3.2.12 sql install file the new options data field for using tabs
        // but in an possible update process can it be always installed
        // so we must check it (and add when not exist) here manually
        $tablefields = $db->getTableColumns($prefix.'jdownloads_usergroups_limits'); 
        if ( !isset($tablefields['uploads_use_tabs']) ){
           // create the missing field
           $db->SetQuery("ALTER TABLE `#__jdownloads_usergroups_limits` ADD `uploads_use_tabs` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `uploads_use_editor`");
           $db->execute();
        } 
        
        // updated in 3.2.21        
        // check whether custom css file already exist
        $custom_css_path = JPATH_ROOT.DS.'components'.DS.'com_jdownloads'.DS.'assets'.DS.'css'.DS.'jdownloads_custom.css';
        if (!JFile::exists($custom_css_path)){
            // create a new css file
            $text  = "/* Custom CSS File for jDownloads\n";
            $text .= "   If this file already exist then jDownloads does not overwrite it when installing or upgrading jDownloads.\n";
            $text .= "   This file is loaded after the standard jdownloads_fe.css.\n";   
            $text .= "   So you can use it to overwrite the standard css classes for your own customising.\n*/";             
            $x = file_put_contents($custom_css_path, $text, FILE_APPEND);
        }
        
        // checking process added in 3.2.32
        $tablefields = $db->getTableColumns($prefix.'jdownloads_usergroups_limits'); 
        if ( !isset($tablefields['uploads_default_access_level']) ){
           // create the missing field
           $db->SetQuery("ALTER TABLE `#__jdownloads_usergroups_limits` ADD `uploads_default_access_level` INT( 10 ) NOT NULL DEFAULT '0' AFTER `uploads_can_change_category`");
           $db->execute();
        }

        // updated in 3.2.37
        // add new options in config to use HTML5 elements for video and audio
        $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'html5player.use'");
        $is_option = $db->loadResult();
        if (!isset($is_option)){
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('show.header.catlist.levels', '0');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.use', '0');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.width', '320');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.height', '240');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.audio.width', '250');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('html5player.view.video.only.in.details', '0');"."\n";                                   
            
            foreach ($query as $data){
                $db->SetQuery($data);
                $db->execute();            
            }      
            unset($query);
        }

        // updated in 3.2.41
        // add new featured options in config
        $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'featured.pic.filename'");
        $is_option = $db->loadResult();
        if (!isset($is_option)){
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('featured.pic.size', '48');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('featured.pic.size.height', '48');"."\n";
            $query[] = "INSERT INTO ".$db->quoteName('#__jdownloads_config')." (setting_name, setting_value) VALUES ('featured.pic.filename', 'featured_orange_star.png');"."\n";                                    
            foreach ($query as $data){
                $db->SetQuery($data);
                $db->execute();            
            }      
            unset($query);
        }
        
        $target = JPATH_ROOT.DS.'images'.DS.'jdownloads'.DS.'featuredimages';
        $source = dirname(__FILE__).DS.'site'.DS.'assets'.DS.'images'.DS.'jdownloads'.DS.'featuredimages';
        
        if (!JFolder::exists(JPATH_ROOT.DS.'images'.DS.'jdownloads'.DS.'featuredimages')){
            JFolder::copy($source, $target);
        }          
        
        // $parent is the class calling this method
        echo '<h4 style="color:#555;">' . JText::_('COM_JDOWNLOADS_UPDATE_TEXT') . '</h4>';
       
        if (count($status->modules) || count($status->plugins)){
        ?>    

        <hr>

        <table class="adminlist" width="100%" style="margin:10px 10px 10px 10px;">
            <thead>
                <tr>
                    <th class="title" style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_EXTENSION'); ?></th>
                    <th width="50%"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($status->modules)) : ?>
                <tr>
                    <th style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_MODULE'); ?></th>
                </tr>
                <?php foreach ($status->modules as $module) : ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td style="text-align:center;"><?php echo ($module['result'])?JText::_('COM_JDOWNLOADS_INSTALL_INSTALLED'):JText::_('COM_JDOWNLOADS_INSTALL_NOT_INSTALLED'); ?></td>
                </tr>
                <?php endforeach;?>
                <?php endif;?>
                <?php if (count($status->plugins)) : ?>
                <tr>
                    <th style="text-align:left;"><?php echo JText::_('COM_JDOWNLOADS_INSTALL_PLUGIN'); ?></th>
                </tr>
                <?php foreach ($status->plugins as $plugin) : ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td style="text-align:center;"><?php echo ($plugin['result'])?JText::_('COM_JDOWNLOADS_INSTALL_INSTALLED'):JText::_('COM_JDOWNLOADS_INSTALL_NOT_INSTALLED'); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>            

        <?php            
            
        }

	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
        
        $manifest = $parent->get("manifest");
        $parent   = $parent->getParent();
        $source   = $parent->getPath("source");
        $db = JFactory::getDBO();        
        
        $pos = strpos($manifest->version, ' ');
        if ($pos){
            $this->new_version_short     = substr($manifest->version, 0, $pos);
        } else {
            $this->new_version_short     = $manifest->version;
        }    
        $this->new_version           = (string)$manifest->version;
        $this->target_joomla_version = (string)$manifest->targetjoomla;
        
        // check whether it exist an old jD version like 1.9        
        // search the config table and when it exist, get the stored versions number
        
        $prefix = self::getCorrectDBPrefix();
        $tablelist = $db->getTableList();
        
        $this->old_version_found = 0;  
        // when != 0 exists leftover data from a prior installed version. So only the component was deinstalled prior 
        // is the value 1.9 we will migrate to the new 3.2 series.
        // is the value 3.2 we have it exist data from the new 3.2 series.
       
        if (in_array ( $prefix.'jdownloads_config', $tablelist)) {
            $db->setQuery('SELECT `setting_value` FROM #__jdownloads_config WHERE `setting_name` = "jd.version"');
            $old_version = $db->loadResult();
            // be careful when this result is 3.2.x - it is possible that this was not always correct updated! 
            if (isset($old_version) && $old_version != ''){
                $compare_str = substr($old_version, 0, 5);
                if ($compare_str == '1.9.1' || $compare_str == '1.9.2'){
                    $this->old_version_found = '1.9';

                    // make sure that the older version is really uninstalled 
                    $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "com_jdownloads" AND `type` = "component"');
                    $exist = $db->loadResult();
                    if ($exist){
                         Jerror::raiseWarning(null, JText::_('COM_JDOWNLOADS_INSTALL_WRONG_OLD_JD_RELEASE'));
                         return false; 
                    }
                    
                    // add message
                    $this->old_update_message[] = JText::_('COM_JDOWNLOADS_UPDATE_START_INFO1');
                    $this->old_update_message[] = JText::_('COM_JDOWNLOADS_UPDATE_START_INFO3');
                    
                    // rename at first the older tables
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_cats` TO `'.$prefix.'jdownloads_cats_backup`');
                    $db->execute();
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_config` TO `'.$prefix.'jdownloads_config_backup`');
                    $db->execute();
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_files` TO `'.$prefix.'jdownloads_files_backup`');
                    $db->execute();                    
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_groups` TO `'.$prefix.'jdownloads_groups_backup`');
                    $db->execute();                
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_license` TO `'.$prefix.'jdownloads_license_backup`');
                    $db->execute();                
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_log` TO `'.$prefix.'jdownloads_log_backup`');
                    $db->execute();            
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_rating` TO `'.$prefix.'jdownloads_rating_backup`');
                    $db->execute();
                    $db->setQuery('RENAME TABLE `'.$prefix.'jdownloads_templates` TO `'.$prefix.'jdownloads_templates_backup`');
                    $db->execute();
                } else {
                    // the DB data from any other older installation seems to exist 
                    $this->old_version_found = '3.2'; 
                    // add message
                    $this->old_update_message[] = '<li><span style="background-color:yellow; color:red;">'.JText::_('COM_JDOWNLOADS_REINSTALL_WITH_PRIOR_DATA_INFO1').'</span></li>';
                    $this->old_update_message[] = '<li><span style="background-color:yellow; color:red;">'.JText::_('COM_JDOWNLOADS_REINSTALL_WITH_PRIOR_DATA_INFO2').'</span></li>';
                    $this->old_update_message[] = '<li><span style="background-color:yellow; color:red;">'.JText::_('COM_JDOWNLOADS_REINSTALL_WITH_PRIOR_DATA_INFO3').'</span></li>';
                }       
            }  
        }
        
        if ( $type == 'install' || $type == 'update' ) {
            // this component does only work with Joomla release 2.5 - otherwise abort
            $jversion = new JVersion();
            if (version_compare( $jversion->RELEASE,  $this->target_joomla_version, 'ge' ) == FALSE ) {
                // is not the required joomla target version
                Jerror::raiseWarning(null, JText::_('COM_JDOWNLOADS_INSTALL_WRONG_JOOMLA_RELEASE'));
                return false;
            }
         
            if ( $type == 'update' ) {
                $component_header = JText::_('COM_JDOWNLOADS_DESCRIPTION');
                $typetext = JText::_('COM_JDOWNLOADS_INSTALL_TYPE_UPDATE');
                $db->setQuery('SELECT * FROM #__extensions WHERE `element` = "com_jdownloads" AND `type` = "component"');
                $item = $db->loadObject();
                $old_manifest = json_decode($item->manifest_cache); 
                $pos = strpos($old_manifest->version, ' ');
                $old_version_short  = substr($old_manifest->version, 0, $pos);
                $rel = $old_version_short . ' to ' . $this->new_version;

                if ( !version_compare($this->new_version_short, $old_version_short, '>=' ) ) {
                    // abort if the release being installed is not newer (or equal) than the currently installed jDownloads version
                    JError::raiseWarning(null, JText::_('COM_JDOWNLOADS_UPDATE_ERROR_INCORRECT_VERSION').' '.$rel);         
                    return false;
                }
                
            } else {
                $component_header = JText::_('COM_JDOWNLOADS_DESCRIPTION');
                $typetext =  JText::_('COM_JDOWNLOADS_INSTALL_TYPE_INSTALL');
                $rel = $this->new_version; 
            }
            
            ?>
            <table class="adminlist" width="100%">
                <thead>
                    <tr>
                        <th class="title"><img src="<?php echo JURI::base(); ?>components/com_jdownloads/assets/images/jdownloads.jpg" border="0" alt="jDownloads Logo" /><br />
                        <p><b><?php 
                        echo $component_header; ?></b></p>
                        <p><?php echo $typetext . ' ' . $rel; ?></p>
                        </th>
                    </tr>
                </thead>
           </table>     
        
        <?php  // end install/update
        
        } else {
            
            if ($type == 'uninstall'){
                       
            }
           
        }
        // afterwards are copied the component files 
	}
 
	/**
	 * method to run after an install/update/discover_install method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install) 
        
        if ( $type == 'install'){
            
              // write default permission settings in the assets table when not exist already
              $db = JFactory::getDBO();
              $query = $db->getQuery(true);
              $query->select('rules');
              $query->from('#__assets');
              $query->where('name = '.$db->Quote('com_jdownloads'));
              $db->setQuery($query);
              $jd_component_rule = $db->loadResult();              
              
              if ($jd_component_rule = '' || $jd_component_rule == '{}'){              
                  $query = $db->getQuery(true);
                  $query->update($db->quoteName('#__assets'));
                  $query->set('rules = '.$db->Quote('{"core.admin":[],"core.manage":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[],"download":{"1":1},"edit.config":[],"edit.user.limits":[]}'));
                  $query->where('name = '.$db->Quote('com_jdownloads'));
                  $db->setQuery($query);
                  if (!$db->execute()){
                      $this->setError($db->getErrorMsg());
                  }    
                  
              }            
            
        }
        
        // write for the tags feature the jd data in the #__content_types table
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__content_types');
        $query->where('type_alias = '.$db->Quote('com_jdownloads.download'));
        $db->setQuery($query);
        $type_download = $db->loadResult();              
        
        if (!$type_download){              
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__content_types'))
                    ->columns(array($db->quoteName('type_title'), $db->quoteName('type_alias'), $db->quoteName('table'), $db->quoteName('field_mappings'), $db->quoteName('router')))
                    ->values($db->quote('jDownloads Download'). ', ' .$db->quote('com_jdownloads.download'). ',' .$db->quote('{"special":{"dbtable":"#__jdownloads_files","key":"file_id","type":"Download","prefix":"JdownloadsTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Download","prefix":"JTable","config":"array()"}}', false).', '.$db->quote('{"common":{"core_content_item_id":"file_id","core_title":"file_title","core_state":"published","core_alias":"file_alias","core_created_time":"null","core_modified_time":"null","core_body":"description", "core_hits":"views","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"null", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"null", "core_ordering":"null", "core_metakey":"null", "core_metadesc":"null", "core_catid":"cat_id", "core_xreference":"null", "asset_id":"null"}, "special":{"parent_id":"parent_id","lft":"null","rgt":"null","level":"null","path":"null","extension":"null","note":"null"}}', false).', ' .$db->quote('JdownloadsHelperRoute::getDownloadRoute'));
            $db->setQuery($query);
            if (!$db->execute()){
                $this->setError($db->getErrorMsg());
            }    
            
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__content_types'))
                    ->columns(array($db->quoteName('type_title'), $db->quoteName('type_alias'), $db->quoteName('table'), $db->quoteName('field_mappings'), $db->quoteName('router')))
                    ->values($db->quote('jDownloads Category'). ', ' .$db->quote('com_jdownloads.category'). ',' .$db->quote('{"special":{"dbtable":"#__jdownloads_categories","key":"id","type":"Category","prefix":"JdownloadsTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Category","prefix":"JTable","config":"array()"}}', false).', '.$db->quote('{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"views","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"null", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"null", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"null", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"null","extension":"null","note":"null"}}', false).', ' .$db->quote('JdownloadsHelperRoute::getCategoryRoute'));
            $db->setQuery($query);
            if (!$db->execute()){
                $this->setError($db->getErrorMsg());
            }    
        }                    
    
        echo '<p align="center"><br /><br /><a href="index.php?option=com_jdownloads"><big><strong>'.JText::_('COM_JDOWNLOADS_INSTALL_16').'</strong></big></a><br /><br /></p>';
	}

    /**
     * Method to get the correct db prefix (problem with getTablelist() which always/sometimes has lowercase prefix names in array)
     *
     * @return string
     */
    function getCorrectDBPrefix() 
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
}
