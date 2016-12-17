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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * jDownloads Restore Controller
 *
 */
class jdownloadsControllertools extends jdownloadsController
{
	function __construct() {
        parent::__construct();
        
        // Register Extra task
        $this->registerTask( 'resetDownloadCounter',   'resetDownloadCounter' );        
        $this->registerTask( 'resetBatchSwitch',       'resetBatchSwitch' );        
        $this->registerTask( 'resetCom',               'resetCom' );
        $this->registerTask( 'cleanImageFolders',      'cleanImageFolders' ); 
        $this->registerTask( 'deleteBackupTables',     'deleteBackupTables' );
        $this->registerTask( 'resetCategoriesRules',   'resetCategoriesRules' );
        $this->registerTask( 'resetDownloadsRules',    'resetDownloadsRules' );
    }
    
    
    /**
     * Reset all download counters to zero
     */
    public function resetDownloadCounter()
    {        
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {        
         
             $db = JFactory::getDBO();
             $query = $db->getQuery(true);
             $query->update($db->quoteName('#__jdownloads_files'));
             $query->set('downloads = \'0\'');
             $db->setQuery($query);
             try {
                  $result = $db->execute();
             } catch (Exception $e) {
                      $this->setError($e->getMessage());
                      $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false));
             }            
            
             JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }

    /**
     * reset all categories permissions settings to 'inherited'
     */
    public function resetCategoriesRules()
    {        
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {        
            $result = array();
            
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__assets');
            $query->where('name LIKE '.$db->Quote('%jdownloads.category%'));
            //$query->where('rules NOT LIKE '.$db->Quote('%download":[]%'));
            $db->setQuery($query);
            $result = $db->loadColumn();            
            $count = count($result);
            
            if ($result){
                 $ids = implode(',', $result);
            
                 $db = JFactory::getDBO();
                 $query = $db->getQuery(true);
                 $query->update($db->quoteName('#__assets'));
                 $query->set('rules = '.$db->Quote('{"core.create":{"6":1,"3":1},"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},"core.edit.own":{"6":1,"3":1},"download":[]}'));
                 
                 $query->where('id IN ('.$ids.')');
                 $db->setQuery($query);
                 try {
                      $result = $db->execute();
                 } catch (Exception $e) {
                          $this->setError($e->getMessage());
                          $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false));
                 }            
            }    
            JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULTS_MSG'),(int)$count));
             
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }

    /**
     * reset all downloads permissions settings to 'inherited'
     */
    public function resetDownloadsRules()
    {        
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {        

            $result = array();
            
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__assets');
            $query->where('name LIKE '.$db->Quote('%jdownloads.download%'));
            //$query->where('rules NOT LIKE '.$db->Quote('%download":[]%'));
            $db->setQuery($query);
            $result = $db->loadColumn();
            $count = count($result);            
            
            if ($result){
                 $ids = implode(',', $result);
            
                 $db = JFactory::getDBO();
                 $query = $db->getQuery(true);
                 $query->update($db->quoteName('#__assets'));
                 $query->set('rules = '.$db->Quote('{"core.create":{"6":1,"3":1},"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},"core.edit.own":{"6":1,"3":1},"download":[]}'));
                 $query->where('id IN ('.$ids.')');
                 $db->setQuery($query);
                 try {
                      $result = $db->execute();
                 } catch (Exception $e) {
                          $this->setError($e->getMessage());
                          $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false));
                 }            
            }    
            JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULTS_MSG'),(int)$count));

        } 
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }

    /**
     * 
     */
    public function resetCom()
    {        
        global $jlistConfig;

        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {                
             $db = JFactory::getDBO();
             $query = $db->getQuery(true);
             $query->update($db->quoteName('#__jdownloads_config'));
             $query->set('setting_value = '.$db->quote(''));
             $query->where('setting_name = '.$db->quote('com'));
             $db->setQuery($query);
             try {
                  $result = $db->execute();
             } catch (Exception $e) {
                      $this->setError($e->getMessage());
                      $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false));
             }            
             JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));
        }    
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
        
    }
    
    /**
     * 
     */
    public function resetBatchSwitch()
    {        
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {                
            
            $db = JFactory::getDBO();
            $config_data['categories.batch.in.progress'] = 0; 
            $config_data['downloads.batch.in.progress']  = 0; 
            foreach($config_data as $setting_name => $setting_value){
                     $query = $db->getQuery(true);
                     $query->update($db->quoteName('#__jdownloads_config'));
                     $query->set('setting_value = \''.$db->escape($setting_value).'\'');
                     $query->where('setting_name = \''.$setting_name.'\'');
                     $db->setQuery($query);
                     try {
                          $result = $db->execute();
                     } catch (Exception $e) {
                              $this->setError($e->getMessage());
                              $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false));
                     }            
            }
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }
    
    /**
     * Clean the image folders 'screenshot' and 'thumbnails' and delete all not used images
     */
    public function cleanImageFolders()
    {        
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );

        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {        
            $pics_folder   = JPATH_SITE.'/images/jdownloads/screenshots/';
            $thumbs_folder = JPATH_SITE.'/images/jdownloads/screenshots/thumbnails/';
            
            $used_image_list    = array();
            $images             = array();
            $del_result         = false;
            $sum                = 0;
            
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('images');
            $query->from('#__jdownloads_files');
            $query->where('images != '.$db->Quote(''));
            $db->setQuery($query);
            $result = $db->loadObjectList();    
            
            if ($result){
                // create a array with all used images 
                for ($i=0; $i < count($result); $i++){
                     $images = explode('|', $result[$i]->images);
                     foreach ($images as $image){
                        if (!in_array($image, $used_image_list)){
                            $used_image_list[] = $image;
                        }
                        
                     }   
                } 
                
                // get a files list with all images from folder
                $files_list = JFolder::files( $pics_folder, $filter= '.', $recurse=false, $fullpath=false, $exclude=array('index.html', 'no_pic.gif') );     
                // compare and get the difference
                $delete_files_list = array_diff($files_list, $used_image_list);
                // delete the founded files
                if ($delete_files_list){
                    foreach ($delete_files_list as $delete_file_list){
                        $del_result = JFile::delete($pics_folder.$delete_file_list);
                                      JFile::delete($thumbs_folder.$delete_file_list);
                        if (!$del_result){
                            JError::raiseWarning( 100, JText::sprintf('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PICS_ERROR', $delete_file_list) );
                        } else {
                            $sum++;
                        }
                    }
                }   
            }
            JError::raiseNotice( 100, JText::sprintf('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PICS_SUM', $sum));
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));     
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }
    
    /**
     * Clean the preview folder and delete all not used files from it
     */
    public function cleanPreviewFolder()
    {        
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
        
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {        
            $preview_folder   = $jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'].DS;
            
            $used_files_list    = array();
            $images             = array();
            $del_result         = false;
            $sum                = 0;
            
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('preview_filename');
            $query->from('#__jdownloads_files');
            $query->where('preview_filename != '.$db->Quote(''));
            $db->setQuery($query);
            $result = $db->loadObjectList();    
            
            if ($result){
                // create a array with all used images 
                for ($i=0; $i < count($result); $i++){
                     $used_files_list[] = $result[$i]->preview_filename;
                } 
                
                // get a files list with all images from folder
                $files_list = JFolder::files( $preview_folder, $filter= '.', $recurse=false, $fullpath=false, $exclude=array('index.html') );     
                // compare and get the difference
                $delete_files_list = array_diff($files_list, $used_files_list);
                // delete the founded files
                if ($delete_files_list){
                    foreach ($delete_files_list as $delete_file_list){
                        $del_result = JFile::delete($preview_folder.$delete_file_list);
                        if (!$del_result){
                            JError::raiseWarning( 100, JText::sprintf('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PREVIEWS_ERROR', $delete_file_list) );
                        } else {
                            $sum++;
                        }
                    }
                }   
            }
            JError::raiseNotice( 100, JText::sprintf('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PREVIEWS_SUM', $sum));
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));     
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }
    
    /**
     * Delete the log data from the last auto monitoring action
     * Is stored in config DB table
     */
    public function deleteMLog()
    {
        global $jlistConfig;
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = '.$db->quote(''));
        $query->where('setting_name = \'last.log.message\'');
        $db->setQuery($query);                              
        $result = $db->execute();
        
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        }          
        
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads', false)); 
        
    }
    
    /**
     * Delete the log data from the last restoration action
     * Is stored in config DB table
     */
    public function deleteRLog()
    {
        global $jlistConfig;
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = '.$db->quote(''));
        $query->where('setting_name = \'last.restore.log\'');
        $db->setQuery($query);                              
        $result = $db->execute();
        
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        }          
        
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads', false)); 
        
    }          
         
    /**
     * Disable the switch for 'found old version'
     */
    public function deactivateUpdate()
    {        
        global $jlistConfig;
        
        $jinput = JFactory::getApplication()->input;
        $redirect = $jinput->getInt('x', 0);
        
        $jlistConfig['old.jd.release.found'] = 0;
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = 0');
        $query->where('setting_name = \'old.jd.release.found\'');
        $db->setQuery($query);
        $result = $db->execute();
        
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        }          
        
        if ($redirect){        
            $this->setRedirect(JRoute::_('index.php?option=com_jdownloads', false)); 
        } else {
            return;
        }    
    }
    
   /**
    * Import the data from the old version tables to the new created DB tables from new version
    * 
    * - We copy always the complete data from the files, categories and ratings table but delete first the tables. So we will not insert the data some times..             
    * - The data from the licences and templates are only added when it exist not a data set with the same name
    * @todo:    
    * 
    * 
    *    
    */
    public function runOldVersionUpdate()
    {        
        global $jlistConfig; 
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        $model_category = JModelLegacy::getInstance( 'Category', 'jdownloadsModel' );
        $model_download = JModelLegacy::getInstance( 'Download', 'jdownloadsModel' );     
     
        $db   = JFactory::getDBO();
        $user = JFactory::getUser();
     
        $log = array();
        $log[] = ' ';
        
        // At first we must rename all older langauge files
        JDownloadsHelper::renameOldLanguageFiles(JPATH_ADMINISTRATOR.'/language');
        JDownloadsHelper::renameOldLanguageFiles(JPATH_SITE.'/language');
         
        ini_set('max_execution_time', '600');
        ignore_user_abort(true);
        flush();     
     
        // Step 1
        // We will at first move important settings from the old configuration table to the new one
       
        // get the old settings
        $oldConfig = array();
        $query = $db->getQuery(true);
        $db->setQuery("SELECT setting_name, setting_value FROM #__jdownloads_config_backup");
        $jlistConfigObj = $db->loadObjectList();
        if(!empty($jlistConfigObj)){
            foreach ($jlistConfigObj as $jlistConfigRow){
                $oldConfig[$jlistConfigRow->setting_name] = $jlistConfigRow->setting_value;
            }
        }
        // labels for custom data fields
        $jlistConfig['custom.field.1.title'] = $oldConfig['custom.field.1.title'];
        $jlistConfig['custom.field.2.title'] = $oldConfig['custom.field.2.title'];
        $jlistConfig['custom.field.3.title'] = $oldConfig['custom.field.3.title'];
        $jlistConfig['custom.field.4.title'] = $oldConfig['custom.field.4.title'];
        $jlistConfig['custom.field.5.title'] = $oldConfig['custom.field.5.title'];
        $jlistConfig['custom.field.6.title'] = $oldConfig['custom.field.6.title'];
        $jlistConfig['custom.field.7.title'] = $oldConfig['custom.field.7.title'];
        $jlistConfig['custom.field.8.title'] = $oldConfig['custom.field.8.title'];
        $jlistConfig['custom.field.9.title'] = $oldConfig['custom.field.9.title'];
        $jlistConfig['custom.field.10.title'] = $oldConfig['custom.field.10.title'];
        $jlistConfig['custom.field.11.title'] = $oldConfig['custom.field.11.title'];
        $jlistConfig['custom.field.12.title'] = $oldConfig['custom.field.12.title'];
        $jlistConfig['custom.field.13.title'] = $oldConfig['custom.field.13.title'];
        $jlistConfig['custom.field.14.title'] = $oldConfig['custom.field.14.title'];
        // values for custom data fields
        $jlistConfig['custom.field.1.values'] = $oldConfig['custom.field.1.values'];
        $jlistConfig['custom.field.2.values'] = $oldConfig['custom.field.2.values'];
        $jlistConfig['custom.field.3.values'] = $oldConfig['custom.field.3.values'];
        $jlistConfig['custom.field.4.values'] = $oldConfig['custom.field.4.values'];
        $jlistConfig['custom.field.5.values'] = $oldConfig['custom.field.5.values'];
        $jlistConfig['custom.field.6.values'] = $oldConfig['custom.field.6.values'];
        $jlistConfig['custom.field.7.values'] = $oldConfig['custom.field.7.values'];
        $jlistConfig['custom.field.8.values'] = $oldConfig['custom.field.8.values'];
        $jlistConfig['custom.field.9.values'] = $oldConfig['custom.field.9.values'];
        $jlistConfig['custom.field.10.values'] = $oldConfig['custom.field.10.values'];
        // get the other settings
        $jlistConfig['global.datetime']         = $oldConfig['global.datetime'];
        $jlistConfig['send.mailto']             = $oldConfig['send.mailto'];
        $jlistConfig['send.mailto.option']      = $oldConfig['send.mailto.option'];
        $jlistConfig['send.mailto.betreff']     = $oldConfig['send.mailto.betreff'];
        $jlistConfig['send.mailto.from']        = $oldConfig['send.mailto.from'];
        $jlistConfig['send.mailto.fromname']    = $oldConfig['send.mailto.fromname'];
        $jlistConfig['zipfile.prefix']          = $oldConfig['zipfile.prefix'];
        $jlistConfig['checkbox.top.text']       = $oldConfig['checkbox.top.text'];
        $jlistConfig['info.icons.size']         = $oldConfig['info.icons.size'];
        $jlistConfig['cat.pic.size']            = $oldConfig['cat.pic.size'];
        $jlistConfig['file.pic.size']           = $oldConfig['file.pic.size'];
        $jlistConfig['offline.text']            = $oldConfig['offline.text'];
        $jlistConfig['system.list']             = $oldConfig['system.list'];
        $jlistConfig['language.list']           = $oldConfig['language.list'];
        $jlistConfig['file.types.view']         = $oldConfig['file.types.view'];
        $jlistConfig['tempfile.delete.time']    = $oldConfig['tempfile.delete.time'];
        $jlistConfig['show.header.catlist']     = $oldConfig['show.header.catlist'];
        $jlistConfig['direct.download']         = $oldConfig['direct.download'];
        $jlistConfig['days.is.file.new']        = $oldConfig['days.is.file.new'];
        $jlistConfig['picname.is.file.new']     = $oldConfig['picname.is.file.new'];
        $jlistConfig['loads.is.file.hot']       = $oldConfig['loads.is.file.hot'];
        $jlistConfig['picname.is.file.hot']     = $oldConfig['picname.is.file.hot'];
        $jlistConfig['download.pic.details']    = $oldConfig['download.pic.details'];
        $jlistConfig['autopublish.founded.files'] = $oldConfig['autopublish.founded.files'];
        $jlistConfig['all.files.autodetect']    = $oldConfig['all.files.autodetect'];
        $jlistConfig['file.types.autodetect']   = $oldConfig['file.types.autodetect'];
        $jlistConfig['jcomments.active']        = $oldConfig['jcomments.active'];
        $jlistConfig['fileplugin.defaultlayout'] = $oldConfig['fileplugin.defaultlayout'];
        $jlistConfig['fileplugin.layout_disabled'] = $oldConfig['fileplugin.layout_disabled'];
        $jlistConfig['send.mailto.upload']      = $oldConfig['send.mailto.upload'];
        $jlistConfig['send.mailto.option.upload'] = $oldConfig['send.mailto.option.upload'];
        $jlistConfig['send.mailto.betreff.upload'] = $oldConfig['send.mailto.betreff.upload'];
        $jlistConfig['send.mailto.from.upload'] = $oldConfig['send.mailto.from.upload'];
        $jlistConfig['send.mailto.fromname.upload'] = $oldConfig['send.mailto.fromname.upload'];
        $jlistConfig['send.mailto.template.upload'] = $oldConfig['send.mailto.template.upload'];
        $jlistConfig['send.mailto.template.download'] = $oldConfig['send.mailto.template.download'];
        $jlistConfig['download.pic.mirror_1']   = $oldConfig['download.pic.mirror_1'];
        $jlistConfig['download.pic.mirror_2']   = $oldConfig['download.pic.mirror_2'];
        $jlistConfig['picname.is.file.updated'] = $oldConfig['picname.is.file.updated'];
        $jlistConfig['days.is.file.updated']    = $oldConfig['days.is.file.updated'];
        $jlistConfig['thumbnail.size.width']    = $oldConfig['thumbnail.size.width'];
        $jlistConfig['thumbnail.size.height']   = $oldConfig['thumbnail.size.height'];
        $jlistConfig['option.navigate.top']     = $oldConfig['option.navigate.top'];
        $jlistConfig['option.navigate.bottom']  = $oldConfig['option.navigate.bottom'];
        $jlistConfig['view.category.info']      = $oldConfig['view.category.info'];
        $jlistConfig['view.subheader']          = $oldConfig['view.subheader'];
        $jlistConfig['view.detailsite']         = $oldConfig['view.detailsite'];
        $jlistConfig['anti.leech']              = $oldConfig['anti.leech'];
        $jlistConfig['google.adsense.active']   = $oldConfig['google.adsense.active'];
        $jlistConfig['google.adsense.code']     = $oldConfig['google.adsense.code'];
        $jlistConfig['send.mailto.report']      = $oldConfig['send.mailto.report'];
        $jlistConfig['download.pic.files']      = $oldConfig['download.pic.files'];
        $jlistConfig['downloads.titletext']     = $oldConfig['downloads.titletext'];
        $jlistConfig['downloads.footer.text']   = $oldConfig['downloads.footer.text'];
        $jlistConfig['create.auto.cat.dir']     = $oldConfig['create.auto.cat.dir'];
        $jlistConfig['view.ratings']            = $oldConfig['view.ratings'];
        $jlistConfig['rating.only.for.regged']  = $oldConfig['rating.only.for.regged'];
        $jlistConfig['use.alphauserpoints']     = $oldConfig['use.alphauserpoints'];
        $jlistConfig['use.alphauserpoints.with.price.field'] = $oldConfig['use.alphauserpoints.with.price.field'];
        $jlistConfig['user.can.download.file.when.zero.points'] = $oldConfig['user.can.download.file.when.zero.points'];
        $jlistConfig['user.message.when.zero.points'] = $oldConfig['user.message.when.zero.points'];
        $jlistConfig['view.sort.order']         = $oldConfig['view.sort.order'];
        $jlistConfig['activate.general.plugin.support'] = $oldConfig['activate.general.plugin.support'];
        $jlistConfig['categories.per.side']     = $oldConfig['categories.per.side'];
        $jlistConfig['use.tabs.type']           = $oldConfig['use.tabs.type'];
        $jlistConfig['additional.tab.title.1']  = $oldConfig['additional.tab.title.1'];
        $jlistConfig['additional.tab.title.2']  = $oldConfig['additional.tab.title.2'];
        $jlistConfig['additional.tab.title.3']  = $oldConfig['additional.tab.title.3'];
        $jlistConfig['remove.field.title.when.empty'] = $oldConfig['remove.field.title.when.empty'];
        $jlistConfig['use.download.title.as.download.link'] = $oldConfig['use.download.title.as.download.link'];
        $jlistConfig['use.sef.with.file.titles'] = $oldConfig['use.sef.with.file.titles'];
        $jlistConfig['use.general.plugin.support.only.for.descriptions'] = $oldConfig['use.general.plugin.support.only.for.descriptions'];
        $jlistConfig['com']                      = $oldConfig['com'];
        $jlistConfig['blocking.list']            = $oldConfig['blocking.list'];
        $jlistConfig['remove.empty.tags']        = $oldConfig['remove.empty.tags'];
        $jlistConfig['create.pdf.thumbs']        = $oldConfig['create.pdf.thumbs'];
        $jlistConfig['create.pdf.thumbs.by.scan'] = $oldConfig['create.pdf.thumbs.by.scan'];
        $jlistConfig['pdf.thumb.height']         = $oldConfig['pdf.thumb.height'];
        $jlistConfig['pdf.thumb.width']          = $oldConfig['pdf.thumb.width'];
        $jlistConfig['pdf.thumb.pic.height']     = $oldConfig['pdf.thumb.pic.height'];
        $jlistConfig['pdf.thumb.pic.width']      = $oldConfig['pdf.thumb.pic.width'];
        $jlistConfig['pdf.thumb.image.type']     = $oldConfig['pdf.thumb.image.type'];
        $jlistConfig['create.auto.thumbs.from.pics'] = $oldConfig['create.auto.thumbs.from.pics'];
        $jlistConfig['create.auto.thumbs.from.pics.image.height'] = $oldConfig['create.auto.thumbs.from.pics.image.height'];
        $jlistConfig['create.auto.thumbs.from.pics.image.width'] = $oldConfig['create.auto.thumbs.from.pics.image.width'];
        $jlistConfig['create.auto.thumbs.from.pics.by.scan'] = $oldConfig['create.auto.thumbs.from.pics.by.scan'];
        
        // write in DB
        foreach($jlistConfig as $setting_name => $setting_value){
                 $query = $db->getQuery(true);
                 $query->update($db->quoteName('#__jdownloads_config'));
                 $query->set('setting_value = \''.$db->escape($setting_value).'\'');
                 $query->where('setting_name = \''.$setting_name.'\'');
                 $db->setQuery($query);
                 $db->execute();
        }                
        $log[] = sprintf(JText::_('COM_JDOWNLOADS_UPDATE_CONFIG'), 110);
        
        // Step 2 
        // When it exists old licenses, create a copy from the new table and write in it the old licenses, so we have always the correct old id.
        // After this can we add in it the new licenses - but here not very useful
        // The old data are stored in jdownloads_license_backup
        
        $sum_moved_licenses = 0;
        $temp_table_lic = array();

        // read the old licenses
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_license_backup');
        $db->setQuery($query);
        $rows_old = $db->loadObjectList();          
        $sum_old = count($rows_old);
       
        $licerror = 0;
        
        if ($sum_old){
            // we have old licenses so we need a new empty table with the new structure
            $query = "CREATE TABLE IF NOT EXISTS `#__jdownloads_licenses_new` LIKE `#__jdownloads_licenses`";
            $db->setQuery($query);
            $result = $db->query();
            if (!$result){
                $log[] = "DB table 'jdownloads_licenses_new' copy error message: $result";
                $licerror ++;
            }            
            
            // we can now store the old licenses in the new table
            foreach ($rows_old as $row_old){
                   // add it in new table
                   $query = $db->getQuery(true);
                   // Insert columns.
                   $columns = array('id', 'title', 'description', 'url', 'language', 'published');
                   // Insert values.
                   $values = array($row_old->id, $db->quote($row_old->license_title), $db->quote($row_old->license_text), $db->quote($row_old->license_url), $db->quote('*'), 1);
                   // Prepare the insert query.
                   $query
                       ->insert($db->quoteName('#__jdownloads_licenses_new'))
                       ->columns($db->quoteName($columns))
                       ->values(implode(',', $values));
                   $db->setQuery($query);
                   $db->execute();
                   
                   $sum_moved_licenses ++;
            }   

            // delete the #__jdownloads_licenses so we can use the name for the above created new table
            $query = "DROP TABLE `#__jdownloads_licenses`";
            $db->setQuery($query);
            $result = $db->query();
            if (!$result){
                $log[] = "DB table 'jdownloads_licenses' delete error message: $result";
                $licerror ++;                
            }
            
            // rename now the new created to jdownloads_licenses 
            $query = "RENAME TABLE `#__jdownloads_licenses_new` TO `#__jdownloads_licenses`";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_licenses' rename error message: $result";
                $licerror ++;                
            }            
            
            if (!$licerror){
                $log[] = sprintf(JText::_('COM_JDOWNLOADS_UPDATE_LICENCE'), $sum_moved_licenses);
            }    
        } 
        
        // Step 3 
        // Get all the old templates and insert it in the new templates
        // The old data are stored in jdownloads_templates_backup
        
        $sum_moved_templates = 0;

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_templates_backup');
        $db->setQuery($query);
        $rows_old = $db->loadObjectList();          
        $sum_old = count($rows_old);
        
        if ($sum_old){
            foreach ($rows_old as $row_old){
               $new_title = $row_old->template_name.' (imported from v.1.9.x)';
               // add it in new table
               $query = $db->getQuery(true);
               // Insert columns but deactivate all old templates.
               $columns = array('template_name', 'template_typ', 'template_header_text', 'template_subheader_text', 'template_footer_text', 'template_text', 'template_active', 'locked', 'note', 'cols', 'checkbox_off', 'symbol_off', 'language');
               // Insert values.
               $values = array($db->quote($new_title), $row_old->template_typ, $db->quote($row_old->template_header_text), $db->quote($row_old->template_subheader_text), $db->quote($row_old->template_footer_text),  $db->quote($row_old->template_text), 0, 0, $db->quote($row_old->note), $row_old->cols, $row_old->checkbox_off, $row_old->symbol_off, $db->quote('*'));
               // Prepare the insert query.
               $query
                   ->insert($db->quoteName('#__jdownloads_templates'))
                   ->columns($db->quoteName($columns))
                   ->values(implode(',', $values));
                 
               $db->setQuery($query);
               $db->execute();
               
               $sum_moved_templates ++;
            }
            $log[] = sprintf(JText::_('COM_JDOWNLOADS_UPDATE_TEMPLATES'), $sum_moved_templates);
        }       
     
        // Step 4
        // Copy the complete old rating table (so we have still the backup table)
        //         

        $result = '';
        $error = 0;
        
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_rating_backup');
        $db->setQuery($query);
        $rows_old = $db->loadObjectList();          
        $sum_old = count($rows_old);
        
        // do it only when we have data in old table
        if ($sum_old) {
            $query = "CREATE TABLE IF NOT EXISTS `#__jdownloads_ratings_new` LIKE `#__jdownloads_rating_backup`";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 1) error message: $result";
                $error ++;
            }
            // copy now the data in the new table
            $query = " ALTER TABLE `#__jdownloads_ratings_new` DISABLE KEYS";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 2) error message: $result";
                $error ++;                
            }
            $query = "INSERT INTO `#__jdownloads_ratings_new` SELECT * FROM `#__jdownloads_rating_backup`";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 3) error message: $result";
                $error ++;                
            }
            $query = "ALTER TABLE `#__jdownloads_ratings_new` ENABLE KEYS";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 4) error message: $result";
                $error ++;                
            }
            // remove all not more used columns
            $query = "ALTER TABLE `#__jdownloads_ratings_new` DROP `jlanguage`";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 5) error message: $result";
                $error ++;                
            }
            // delete now the new created table from installation process
            $query = "DROP TABLE `#__jdownloads_ratings`";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 6) error message: $result";
                $error ++;                
            }
            // rename finally the above copied table
            $query = "RENAME TABLE `#__jdownloads_ratings_new` TO `#__jdownloads_ratings`";
            $db->setQuery($query);
            $result = $db->execute();
            if (!$result){
                $log[] = "DB table 'jdownloads_rating' copy (step 7) error message: $result";
                $error ++;                
            }
            
            // switch the table to innodb
            $query = "ALTER TABLE `#__jdownloads_ratings` ENGINE=innodb;";
            $db->setQuery($query);
            $db->execute();            
                                    
            if (!$error){
                $log[] = sprintf(JText::_('COM_JDOWNLOADS_UPDATE_RATINGS'), $sum_old);
            }
        }
        
        // Step 5
        // Move the Categories data
        // We delete at first the table so we are sure that it is always empty
        
        $sum_moved_cats = 0;
        $temp_table = array();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_cats_backup');
        $query->order('cat_id ASC');
        $db->setQuery($query);
        $cats_old = $db->loadObjectList();          
        $sum_old = count($cats_old);
        
        if ($sum_old){

            $query = "TRUNCATE TABLE `#__jdownloads_categories`";
            $db->setQuery($query);
            $db->execute();
            
            // create the base dataset
            $query = "INSERT INTO `#__jdownloads_categories` (`id`, `cat_dir`, `cat_dir_parent`, `parent_id`, `lft`, `rgt`, `level`, `title`, `alias`, `description`, `pic`, `access`, `metakey`, `metadesc`, `robots`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `language`, `notes`, `views`, `params`, `password`, `password_md5`, `ordering`, `published`, `checked_out`, `checked_out_time`, `asset_id`) VALUES
                      (1, '', '', 0, 0, 1, 0, 'ROOT', 'root', '', '', 1, '', '', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '*', '', 0, '', '', '', 0, 1, 0, '0000-00-00 00:00:00', 0);";
            $db->setQuery($query);
            $db->execute();            
                        
            foreach ($cats_old as $cat_old){
               $note = '';
               if (!isset($cat_old->cat_description)){
                   $cat_old->cat_description = '';
               }   
               
               // build cat and parent cat dir
               $cat_old->cat_dir = str_replace('\\', '/', $cat_old->cat_dir);
               $folders = explode('/', $cat_old->cat_dir);
               $cat_dir_value = $folders[count($folders)-1];
               // remove last element
               array_pop($folders);
               if (count($folders) > 0){
                    $cat_dir_parent_value = implode('/', $folders);
               } else {
                    $cat_dir_parent_value = '';
               } 
               
               if ($cat_old->parent_id == 0){
                   $cat_old_parent_id = 1;
               } else {
                   // we must get it from the prior stored temp table
                   $cat_old_parent_id = $temp_table[$cat_old->parent_id]; 
               } 
               
                   // build table array
                   $data = array (
                        'id'                => '',
                        'parent_id'         => $cat_old_parent_id,
                        'title'             => $cat_old->cat_title,
                        'alias'             => $cat_old->cat_alias,
                        'notes'             => $note,
                        'description'       => $cat_old->cat_description,
                        'cat_dir'           => $cat_dir_value,
                        'cat_dir_parent'    => $cat_dir_parent_value,
                        'pic'               => $cat_old->cat_pic,
                        'published'         => $cat_old->published,
                        'access'            => '1',
                        'metadesc'          => $cat_old->metadesc,
                        'metakey'           => $cat_old->metakey,
                        'created_user_id'   => (int)$user->id,
                        'language'          => '*',
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
                       $log[] = 'Category Results: Can not move category ID '.$cat_old->cat_id;
                   } else {
                       if ($create_result !== true && $create_result > 0){
                           // we have the id from the new cat
                           $temp_table[$cat_old->cat_id] = (int)$create_result;
                       } 
                   }              
               $sum_moved_cats ++;
            }
            $log[] = sprintf(JText::_('COM_JDOWNLOADS_UPDATE_CATEGORIES'), $sum_moved_cats);
        }          
        
        // Step 6
        // Move the Files data
        // We delete at first the table so we are sure that it is always empty        
        
        $sum_moved_files = 0;

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_files_backup');
        $query->order('file_id ASC');
        $db->setQuery($query);
        $files_old = $db->loadObjectList();          
        $sum_old = count($files_old);
        
        if ($sum_old){
            
            $query = "TRUNCATE TABLE `#__jdownloads_files`";
            $db->setQuery($query);
            $db->execute();             
            
            foreach ($files_old as $file_old){
               
               $images = array();

               $note = '';
               
               if (!isset($file_old->description)){
                   $file_old->description = '';
               }

               if (!isset($file_old->description_long)){
                   $file_old->description_long = '';
               }
               
               if (!isset($file_old->file_added)){
                   $file_old->file_added = '';
               }               
               
               if ($file_old->thumbnail != ''){
                   $images[] = $file_old->thumbnail;
               }
               if ($file_old->thumbnail2 != ''){
                   $images[] = $file_old->thumbnail2;
               }
               if ($file_old->thumbnail3 != ''){
                   $images[] = $file_old->thumbnail3;
               } 
               
               // we must get the right cat_id from the prior stored temp table
               $cat_id = $temp_table[$file_old->cat_id]; 
                
               // build table array
               $data = array (
                    'file_id'           => $file_old->file_id,
                    'cat_id'            => $cat_id,
                    'file_title'        => $file_old->file_title,
                    'file_alias'        => $file_old->file_alias,
                    'notes'             => $note,
                    'description'       => $file_old->description,
                    'description_long'  => $file_old->description_long,
                    'price'             => $file_old->price,
                    'release'           => $file_old->release,
                    'file_pic'          => $file_old->file_pic,
                    'system'            => $file_old->system,
                    'file_language'     => $file_old->language,
                    'license'           => $file_old->license,
                    'url_license'       => $file_old->url_license,
                    'license_agree'     => $file_old->license_agree,
                    'size'              => $file_old->size,
                    'date_added'        => $file_old->date_added,
                    'images'            => implode('|', $images),
                    'file_added'        => $file_old->file_added,
                    'publish_from'      => $file_old->publish_from,
                    'publish_to'        => $file_old->publish_to,
                    'use_timeframe'     => $file_old->use_timeframe,
                    'url_download'      => $file_old->url_download,
                    'extern_file'       => $file_old->extern_file,
                    'extern_site'       => $file_old->extern_site,
                    'mirror_1'          => $file_old->mirror_1,
                    'mirror_2'          => $file_old->mirror_2,
                    'extern_site_mirror_1' => $file_old->extern_site_mirror_1,
                    'extern_site_mirror_2' => $file_old->extern_site_mirror_2,
                    'url_home'          => $file_old->url_home,
                    'author'            => $file_old->author,
                    'url_author'        => $file_old->url_author,
                    'created_id'        => $file_old->created_id,
                    'created_mail'      => $file_old->created_mail,
                    'modified_id'       => $file_old->modified_id,
                    'modified_date'     => $file_old->modified_date,
                    'submitted_by'      => $file_old->submitted_by,
                    'set_aup_points'    => $file_old->set_aup_points,
                    'downloads'         => $file_old->downloads,
                    'update_active'     => $file_old->update_active,
                    'ordering'          => $file_old->ordering,
                    'published'         => $file_old->published,
                    'access'            => '1',
                    'metadesc'          => $file_old->metadesc,
                    'metakey'           => $file_old->metakey,
                    'language'          => '*',
                    'custom_field_1'    => $file_old->custom_field_1,
                    'custom_field_2'    => $file_old->custom_field_2,
                    'custom_field_3'    => $file_old->custom_field_3,
                    'custom_field_4'    => $file_old->custom_field_4,
                    'custom_field_5'    => $file_old->custom_field_5,
                    'custom_field_6'    => $file_old->custom_field_6,
                    'custom_field_7'    => $file_old->custom_field_7,
                    'custom_field_8'    => $file_old->custom_field_8,
                    'custom_field_9'    => $file_old->custom_field_9,
                    'custom_field_10'   => $file_old->custom_field_10,
                    'custom_field_11'   => $file_old->custom_field_11,
                    'custom_field_12'   => $file_old->custom_field_12,
                    'custom_field_13'   => $file_old->custom_field_13,
                    'custom_field_14'   => $file_old->custom_field_14,
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
                   $create_result = $model_download->createAutoDownload( $data, true );
                   if (!$create_result){
                       // error message
                       $log[] = 'Files Result: Can not move file ID '.$file_old->file_id;
                   }              
                   $sum_moved_files ++;
            }
            $log[] = sprintf(JText::_('COM_JDOWNLOADS_UPDATE_FILES'), $sum_moved_files);
        }        
        
        // remove the old tempzipfiles folder
        if (JFolder::exists($jlistConfig['files.uploaddir'].'/tempzipfiles')){
            JFolder::delete($jlistConfig['files.uploaddir'].'/tempzipfiles');
        }
        
        // import finished
        // deactivate the status
        self::deactivateUpdate();

        $log[] = JText::_('COM_JDOWNLOADS_UPDATE_FINISHED'); 
        
        // write log in file  
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $file = fopen('jdownloads_update_log.txt', 'a');
        fputs($file,"$date $time\n");
        foreach ($log as $line){
            fputs($file,"$line\n");
        }    
        fclose($file);
        
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads', false), implode('<br />', $log));
    }   
    
   /**
    * User will delete the backup tables from old jD 1.0.x version
    *  
    */
    public function deleteBackupTables()
    {        
       
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {          
            // get DB prefix string and table list to check whether it exists backup tables
            $db = JFactory::getDBO();
            $prefix     = JDownloadsHelper::getCorrectDBPrefix();
            $tablelist  = $db->getTableList();
            
            if (in_array ( $prefix.'jdownloads_config_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_config_backup ;'; 
            }
            if (in_array ( $prefix.'jdownloads_cats_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_cats_backup ;'; 
            }
            if (in_array ( $prefix.'jdownloads_files_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_files_backup ;'; 
            }
            if (in_array ( $prefix.'jdownloads_license_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_license_backup ;'; 
            }
            if (in_array ( $prefix.'jdownloads_groups_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_groups_backup ;'; 
            }        
            if (in_array ( $prefix.'jdownloads_log_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_log_backup ;'; 
            }
            if (in_array ( $prefix.'jdownloads_rating_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_rating_backup ;'; 
            }
            if (in_array ( $prefix.'jdownloads_templates_backup', $tablelist)){
               $query[] = 'DROP TABLE '.$prefix.'jdownloads_templates_backup ;'; 
            }
            
            $error = 0;
            
            foreach ($query as $data){
                $db->SetQuery($data);
                $result = $db->execute();
                if ($result !== true){
                    $error ++;
                }
            } 
            
            // deactivate the 'Update' status in config.
            self::deactivateUpdate();
            
            if ($error == 0){
                JError::raiseNotice( 100, JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));
            } else {
                JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_NOT_OKAY_MSG'));                
            } 
            
            $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false));                
        }
    }    
        
    
}
?>