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

jimport('joomla.application.component.controller');

/**
 * jDownloads Config Controller
 *
 */
class jdownloadsControllerconfig extends jdownloadsController
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'apply', 	'save' );
	}

	/**
	 * logic for cancel the work on configuration
	 *
	 * @access public
	 * @return void
	 */
	public function cancel() {

		$this->setRedirect( 'index.php?option=com_jdownloads' );
	}	

	/**
	 * logic to save the config data
	 *
	 * @access public
	 * @return void
	 */
	public function save() {
 
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder'); 

        $jinput             = JFactory::getApplication()->input;
        $config             = JFactory::getConfig();
        $secret             = $config->get( 'secret' );

        $config_data        = $jinput->get('jlistConfig', array(), 'array');
        
        // clean text data
        //$config_data['google.adsense.code']             = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['google.adsense.code'], 'HTML'); // adsense code use also javascript...
        $config_data['user.message.when.zero.points']   = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['user.message.when.zero.points'], 'HTML');
        $config_data['fileplugin.offline_title']        = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['fileplugin.offline_title'], 'HTML');
        $config_data['fileplugin.offline_descr']        = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['fileplugin.offline_descr'], 'HTML');
        $config_data['send.mailto.template.download']   = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['send.mailto.template.download'], 'HTML');
        $config_data['send.mailto.template.upload']     = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['send.mailto.template.upload'], 'HTML');
        $config_data['report.mail.layout']              = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['report.mail.layout'], 'HTML');
        $config_data['offline.text']                    = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['offline.text'], 'HTML');
        $config_data['downloads.titletext']             = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['downloads.titletext'], 'HTML');
        $config_data['downloads.footer.text']           = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['downloads.footer.text'], 'HTML');
        $config_data['blocking.list']                   = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['blocking.list'], 'STRING');
        $config_data['allowed.leeching.sites']          = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['allowed.leeching.sites'], 'STRING');
        $config_data['mp3.player.config']               = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['mp3.player.config'], 'STRING');
        $config_data['mp3.info.layout']                 = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['mp3.info.layout'], 'HTML');
        $config_data['language.list']                   = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['language.list'], 'STRING');
        $config_data['system.list']                     = JFilterInput::getInstance(null, null, 1, 1)->clean($config_data['system.list'], 'STRING');

        $org_upload_path    = $jinput->get('root_dir',  '', 'string');
        $org_uncat_dir      = $jinput->get('uncat_dir', '', 'string');
        $org_preview_dir    = $jinput->get('preview_dir', '', 'string');
        $org_private_dir    = $jinput->get('private_dir', '', 'string');
        $org_temp_dir       = $jinput->get('temp_dir',  '', 'string');
        
        $new_cat_pic        = $jinput->get('cat_pic',  '', 'string');
        $config_data['cat.pic.default.filename'] = $new_cat_pic;
        
        $new_file_pic       = $jinput->get('file_pic', '', 'string');
        $config_data['file.pic.default.filename'] = $new_file_pic;

        $new_featured_pic   = $jinput->get('featured_pic', '', 'string');
        $config_data['featured.pic.filename'] = $new_featured_pic;
        
        $new_new_pic        = $jinput->get('new_pic', '', 'string');
        $config_data['picname.is.file.new'] = $new_new_pic;
        
        $new_hot_pic        = $jinput->get('hot_pic', '', 'string');
        $config_data['picname.is.file.hot'] = $new_hot_pic;
        
        $new_upd_pic        = $jinput->get('upd_pic', '', 'string');
        $config_data['picname.is.file.updated'] = $new_upd_pic;
        
        $new_down_pic       = $jinput->get('down_pic', '', 'string');
        $config_data['download.pic.details'] = $new_down_pic;

        $new_down_pic2      = $jinput->get('down_pic2', '', 'string');
        $config_data['download.pic.files'] = $new_down_pic2;
        
        $new_mirror_pic     = $jinput->get('mirror_1_pic', '', 'string');
        $config_data['download.pic.mirror_1'] = $new_mirror_pic;
        
        $new_mirror_pic2    = $jinput->get('mirror_2_pic', '', 'string');
        $config_data['download.pic.mirror_2'] = $new_mirror_pic2;
        
        $new_pic_plg        = $jinput->get('down_pic_plg',  '', 'string');
        $config_data['download.pic.plugin'] = $new_pic_plg;        

        
        $resize_thumbnails  = $jinput->get('resize_thumbs',  0, 'integer');
        $com                = $jinput->get('com', '', 'string');
        
        $task               = $jinput->get('task');
        $result             = true;

        // remove slash on the end from path
        $config_data['files.uploaddir'] = rtrim($config_data['files.uploaddir'],"/");
        $config_data['files.uploaddir'] = rtrim($config_data['files.uploaddir'],"\\");
        // replacing backslashes with slashes
        $config_data['files.uploaddir'] = str_replace('\\', '/', $config_data['files.uploaddir']);
        if (!JFolder::exists($config_data['files.uploaddir'])){
            JError::raiseWarning( 100, 'Error Upload root folder not found!' );
        }
        
        // is upload folder changed?
        /*if ($org_upload_path != $new_upload_path && $new_upload_path != ''){
            if (JFolder::exists($org_upload_path)){
                // rename the folder
                $result_root = JFolder::move($org_upload_path, $new_upload_path);            

                if ($result_root === true){
                    $config_data['files.uploaddir'] = $new_upload_path;
                } else {    
                    $config_data['files.uploaddir'] = $org_upload_path;
                    JError::raiseWarning( 100, 'Error! Can not rename or copy the root folder: '.$org_upload_path.' to: '.$new_upload_path );
                    $result = false;
                }
            } else {    
                    $config_data['files.uploaddir'] = $org_upload_path;
                    JError::raiseWarning( 100, 'Error Upload root folder not found!' );
                    $result = false;
            }   
        } */
         
        // remove slash on the end from folder name
        $config_data['uncategorised.files.folder.name'] = rtrim($config_data['uncategorised.files.folder.name'],"/");
        $config_data['uncategorised.files.folder.name'] = rtrim($config_data['uncategorised.files.folder.name'],"\\");

        // is sub folder name changed for uncategorised downloads?
        if ($org_uncat_dir != $config_data['uncategorised.files.folder.name'] && $config_data['uncategorised.files.folder.name'] != ''){
            // rename the folder
            $result_uncat = JFolder::move($config_data['files.uploaddir'].DS.$org_uncat_dir, $config_data['files.uploaddir'].DS.$config_data['uncategorised.files.folder.name']);
            if ($result_uncat !== true){
                JError::raiseWarning( 100, 'Error! Can not rename folder: '.$config_data['files.uploaddir'].DS.$org_uncat_dir );
                $result = false;
            }
        } 
        
        // remove slash on the end from folder name
        $config_data['preview.files.folder.name'] = rtrim($config_data['preview.files.folder.name'],"/");
        $config_data['preview.files.folder.name'] = rtrim($config_data['preview.files.folder.name'],"\\");

        // is sub folder name changed for preview files folder?
        if ($org_preview_dir != $config_data['preview.files.folder.name'] && $config_data['preview.files.folder.name'] != ''){
            // rename the folder
            $result_preview = JFolder::move($config_data['files.uploaddir'].DS.$org_preview_dir, $config_data['files.uploaddir'].DS.$config_data['preview.files.folder.name']);
            if ($result_preview !== true){
                JError::raiseWarning( 100, 'Error! Can not rename folder: '.$config_data['files.uploaddir'].DS.$org_preview_dir );
                $result = false;
            }
        }                 

        // remove slash on the end from folder name
        $config_data['private.area.folder.name'] = rtrim($config_data['private.area.folder.name'],"/");
        $config_data['private.area.folder.name'] = rtrim($config_data['private.area.folder.name'],"\\");         

        // is sub folder name changed for private user area?
        if ($org_private_dir != $config_data['private.area.folder.name'] && $config_data['private.area.folder.name'] != ''){
            // rename the folder
            $result_private = JFolder::move($config_data['files.uploaddir'].DS.$org_private_dir, $config_data['files.uploaddir'].DS.$config_data['private.area.folder.name']);
            if ($result_private !== true){
                JError::raiseWarning( 100, 'Error! Can not rename folder: '.$config_data['files.uploaddir'].DS.$org_private_dir );
                $result = false;
            }
        } 
        
        // remove slash on the end from folder name
        $config_data['tempzipfiles.folder.name'] = rtrim($config_data['tempzipfiles.folder.name'],"/");
        $config_data['tempzipfiles.folder.name'] = rtrim($config_data['tempzipfiles.folder.name'],"\\"); 

        // is sub folder name changed for temporary files folder?
        if ($org_temp_dir != $config_data['tempzipfiles.folder.name'] && $config_data['tempzipfiles.folder.name'] != ''){
            // rename the folder
            $result_temp = JFolder::move($config_data['files.uploaddir'].DS.$org_temp_dir, $config_data['files.uploaddir'].DS.$config_data['tempzipfiles.folder.name']);
            if ($result_temp !== true){
                JError::raiseWarning( 100, 'Error! Can not rename folder: '.$config_data['files.uploaddir'].DS.$org_temp_dir );
                $result = false;
            }    
        }         
        
        // shall we resize all thumbnails?
        if ($resize_thumbnails == 1 && ($config_data['thumbnail.size.height'] > 0) && ($config_data['thumbnail.size.width'] > 0) ){
            $msg = JDownloadsHelper::resizeAllThumbs( $config_data['thumbnail.size.height'], $config_data['thumbnail.size.width'] );
            JError::raiseNotice( 100, $msg );
        }
        
        // check folder protection situation
        $source = JPATH_SITE.'/administrator/components/com_jdownloads/htaccess.txt'; 
        $dest   = $config_data['files.uploaddir'].'/.htaccess'; 
        if ($config_data['anti.leech'] && !is_file($dest)){
            // if activated - copy and rename the htaccess
            if (JFile::exists($source)){ 
                JFile::copy($source, $dest);
                $msg .= ' - '.JText::_('COM_JDOWNLOADS_ACTIVE_ANTILEECH_OK');
           } else {
               $msg .= ' - '.JText::_('COM_JDOWNLOADS_ACTIVE_ANTILEECH_ERROR');
               
           }
        } else {
            // anti leech off? then delete the htaccess
            if (!$config_data['anti.leech']) { 
                if (JFile::exists($dest)){
                    if (JFile::delete($dest)){
                        $msg .= ' - '.JText::_('COM_JDOWNLOADS_ACTIVE_ANTILEECH_OFF_OK');                
                    } else {
                        $msg .= ' - '.JText::_('COM_JDOWNLOADS_ACTIVE_ANTILEECH_OFF_ERROR');                

                    }   
                }
            }  
        }
        
        // clean the flowplayer config settings string, before we can save it
        if ($config_data['flowplayer.control.settings'] != ''){
            $config_data['flowplayer.control.settings'] = trim(str_replace("'", '"', $config_data['flowplayer.control.settings']));
        }            
        
        // remove spaces from lists 
        $config_data['file.types.view'] = str_replace(' ', '', $config_data['file.types.view']);
        $config_data['file.types.autodetect'] = str_replace(' ', '',$config_data['file.types.autodetect']);
        $config_data['allowed.upload.file.types'] = str_replace(' ', '', $config_data['allowed.upload.file.types']);
        $config_data['allowed.leeching.sites'] = str_replace(' ', '', $config_data['allowed.leeching.sites']);        
        
        // check the given upload size and correct it
        $max_upload_php_ini = (int)ini_get('upload_max_filesize') * 1024; 
        if ($config_data['allowed.upload.file.size'] > $max_upload_php_ini) $config_data['allowed.upload.file.size'] = $max_upload_php_ini;        
        
        if ($com != ''){
            if ($com == $secret){
                $config_data['com'] = strrev($secret);
            }    
        }
        
        // make sure that all AUP options are set back to default, when the main option is set off.
        if (!$config_data['use.alphauserpoints']){
            $config_data['use.alphauserpoints.with.price.field'] = '0';
            $config_data['user.can.download.file.when.zero.points'] = '1';
        }   		
        
        // installed imagick is needed
        if ($config_data['create.pdf.thumbs']){
            if (!extension_loaded('imagick')){
                $config_data['create.pdf.thumbs'] = '0';
            }    
        }        
        
        // try now to save the data
        if ($result === true){
            $model = $this->getModel( 'config' );
		    
            if ($model->save($config_data)) {
                $msg = JText::_( 'COM_JDOWNLOADS_BACKEND_SETTINGS_SAVED' );
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JDOWNLOADS_BACKEND_SETTINGS_NOT_SAVED' ), 'error');
            }
        }
        
		if ($task == 'apply'){
            $this->setRedirect( 'index.php?option=com_jdownloads&view=config', $msg );        
        } else {
            $this->setRedirect( 'index.php?option=com_jdownloads', $msg );
        }	
	}  
}
?>