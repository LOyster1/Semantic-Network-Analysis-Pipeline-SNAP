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

global $jlistConfig; 

JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
jimport( 'joomla.html.html.tabs' );
$canDo      = JDownloadsHelper::getActions();

if ($canDo->get('edit.config')){
?>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads');?>" method="post" name="adminForm" id="adminForm">

<?php echo JHtml::_('tabs.start', 'jdlayout-sliders-config', array('useCookie'=>1)); ?>
<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_DOWNLOADS'), 'downloads'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* Global config */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                      <td valign="top" align="left" width="100%">
                          <table width="100%">
                          <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_FILES_HEAD')." "; ?></th>
                          </tr>
                          <tr>
                           <td  valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR')." "; ?></strong><br />
                            <?php // echo JPATH_SITE.DS; ?>
                                <input name="jlistConfig[files.uploaddir]" value="<?php echo $jlistConfig['files.uploaddir']; ?>" size="50" />
                           <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_DESC');?><br />
                            <?php echo (is_writable($jlistConfig['files.uploaddir'])) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_DOWNLOAD_WRITABLE') : JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_DOWNLOAD_NOTWRITABLE');?>
                           </td>
                          </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_UNCATEGORISED')." "; ?></strong><br />
                             <?php echo $jlistConfig['files.uploaddir'].'/<br />'; ?> 
                             <input name="jlistConfig[uncategorised.files.folder.name]" value="<?php echo $jlistConfig['uncategorised.files.folder.name']; ?>" size="50" />
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_UNCATEGORISED_DESC');?><br />
                             <?php echo (is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['uncategorised.files.folder.name'])) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_WRITABLE') : JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_NOTWRITABLE');?>
                        </td>
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_PREVIEW')." "; ?></strong><br />
                             <?php echo $jlistConfig['files.uploaddir'].'/<br />'; ?> 
                             <input name="jlistConfig[preview.files.folder.name]" value="<?php echo $jlistConfig['preview.files.folder.name']; ?>" size="50" />
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_PREVIEW_DESC');?><br />
                             <?php echo (is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'])) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_WRITABLE') : JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_NOTWRITABLE');?>
                        </td>
                        </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_PRIVATE')." "; ?></strong><br />
                             <?php echo $jlistConfig['files.uploaddir'].'/<br />'; ?> 
                             <input name="jlistConfig[private.area.folder.name]" value="<?php echo $jlistConfig['private.area.folder.name']; ?>" size="50" />
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIR_PRIVATE_DESC').' '.JText::_('COM_JDOWNLOADS_VIEW_HINT_FOR_NOT_READY_FUNCTIONS');?><br />
                             <?php echo (is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name'])) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_WRITABLE') : JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_NOTWRITABLE');?>
                        </td>
                        </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIRTEMP')." "; ?></strong><br />
                            <?php echo $jlistConfig['files.uploaddir'].'/<br />'; ?> 
                            <input name="jlistConfig[tempzipfiles.folder.name]" value="<?php echo $jlistConfig['tempzipfiles.folder.name']; ?>" size="50" />
                          <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPLOADDIRTEMP_DESC');?><br />
                             <?php echo (is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['tempzipfiles.folder.name'])) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_WRITABLE') : JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_TEMP_NOTWRITABLE');?>
                        </td>
                        </tr>                        
                        
                        <tr><td colspan="2"><hr></td></tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DATETIME')." "; ?></strong><br />
                                <textarea name="jlistConfig[global.datetime]" rows="4" cols="40"><?php echo htmlspecialchars($jlistConfig['global.datetime'], ENT_QUOTES ); ?></textarea>
                        <td valign="top">
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DATETIME_DESC');?>
                        </td>
                        </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DATETIME_SHORT')." "; ?></strong><br />
                                <textarea name="jlistConfig[global.datetime.short]" rows="4" cols="40"><?php echo htmlspecialchars($jlistConfig['global.datetime.short'], ENT_QUOTES ); ?></textarea>
                        <td valign="top">
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DATETIME_SHORT_DESC');?>
                        </td>
                        </tr>                        
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_ZIPFILE_PREFIX_TEXT')." "; ?></strong><br />
                                <input name="jlistConfig[zipfile.prefix]" value="<?php echo $jlistConfig['zipfile.prefix']; ?>" size="30" maxlength="50"/></td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_ZIPFILE_PREFIX_DESC');?>
                        </td>
                          </tr>

                         <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEL_TEMPFILE_TIME')." "; ?></strong><br />
                                <input name="jlistConfig[tempfile.delete.time]" value="<?php echo $jlistConfig['tempfile.delete.time']; ?>" size="10" maxlength="10"/></td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEL_TEMPFILE_TIME_DESC');?>
                        </td>
                          </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DIRECT_DOWNLOAD_ACTIVE_TITLE')." "; ?></strong><br />
                            <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[direct.download]', 'class="inputbox"', $jlistConfig['direct.download']); ?>
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DIRECT_DOWNLOAD_ACTIVE_DESC').'<br />'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DIRECT_DOWNLOAD_ACTIVE_DESC2');?>
                        </td>
                          </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_SRIPT_FOR_DOWNLOAD_TITLE')." "; ?></strong><br />
                            <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.php.script.for.download]', 'class="inputbox"', $jlistConfig['use.php.script.for.download']); ?>
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_SRIPT_FOR_DOWNLOAD_DESC');?>
                        </td>
                          </tr>                                                      

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_LOGGING_TITLE')." "; ?></strong><br />
                            <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[activate.download.log]', 'class="inputbox"', $jlistConfig['activate.download.log']); ?>
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_LOGGING_DESC');?>
                        </td>
                          </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_VIEW_FILE_TYPES')." "; ?></strong><br />
                               <input name="jlistConfig[file.types.view]" value="<?php echo $jlistConfig['file.types.view']; ?>" size="50" maxlength="500"/>
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_VIEW_FILE_TYPES_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr><td colspan="2"><hr></td></tr>
                                                
                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_IMAGES_DELETE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[delete.also.images.from.downloads]', 'class="inputbox"', $jlistConfig['delete.also.images.from.downloads']); ?> 
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_IMAGES_DELETE_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_PREVIEW_DELETE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[delete.also.preview.files.from.downloads]', 'class="inputbox"', $jlistConfig['delete.also.preview.files.from.downloads']); ?> 
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_PREVIEW_DELETE_DESC');?>
                        </td>                
                        </tr>                                                
                                               
                    </table>
                   </td>
                  </tr>
                 </table>
            </td>
        </tr>
    </table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_TAB_TITLE'), 'autodetect'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* config for autodetect downloads/files */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                      <td valign="top" align="left" width="100%">
                          <table width="100%">
                          <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_HEADER_TITLE')." "; ?></th>
                          </tr>
                        <tr>
                        <td valign="top" width="330"><strong><font color="#990000"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO')." "; ?></font></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[files.autodetect]', 'class="inputbox"', $jlistConfig['files.autodetect']); ?>
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_DESC').'<br /><br />'.JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_SPECIAL_HINT');?>
                        </td>
                          </tr>
                        
                        <tr>
                        <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_ALL_FILES_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[all.files.autodetect]', 'class="inputbox"', $jlistConfig['all.files.autodetect']); ?>                            
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_ALL_FILES_DESC');?>
                        </td>
                        </tr> 
                        
                        <tr>
                        <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_ONLY_THIS_FILES_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[file.types.autodetect]" value="<?php echo $jlistConfig['file.types.autodetect']; ?>" size="50" maxlength="500"/>
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_ONLY_THIS_FILES_DESC');?>
                        </td>                
                        </tr>                       
                        
                        <tr>
                        <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DOWNLOADS_AUTOPUBLISH_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[autopublish.founded.files]', 'class="inputbox"', $jlistConfig['autopublish.founded.files']); ?>                            
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DOWNLOADS_AUTOPUBLISH_DESC');?>
                        </td>
                        </tr>
                        
                        <tr>
                        <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_DEFAULT_TEXT')." "; ?></strong><br />
                             <textarea name="jlistConfig[autopublish.default.description]" rows="10" cols="40"><?php echo htmlspecialchars($jlistConfig['autopublish.default.description'], ENT_QUOTES ); ?></textarea>
                        </td>
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FAUTO_DEFAULT_TEXT_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>
                        </tr>                        
                    </table>
                   </td>
                  </tr>
                 </table>
            </td>
        </tr>
    </table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_FRONTEND'), 'frontend'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* Frontend config */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                      <td valign="top" align="left" width="100%">
                        <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_FRONTEND_HEAD')." "; ?></th>
                        </tr>

                        <tr>
                        <td width="330"><strong><font color="#990000"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_OFFLINE_OPTION_TITLE')." "; ?></font></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[offline]', 'class="inputbox"', $jlistConfig['offline']); ?>                               
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_OFFLINE_OPTION_DESC');?>
                        </td>
                          </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_OFFLINE_MESSAGE_TITLE')." "; ?></strong><br />
                                <textarea name="jlistConfig[offline.text]" rows="10" cols="40"><?php echo htmlspecialchars($jlistConfig['offline.text'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_OFFLINE_MESSAGE_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                                </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_COMPO_TEXT')." "; ?></strong><br />
                                <textarea name="jlistConfig[downloads.titletext]" rows="4" cols="40"><?php echo htmlspecialchars($jlistConfig['downloads.titletext'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_COMPO_TEXT_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                                </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FOOTER_TEXT_TITLE')." "; ?></strong><br />
                                <textarea name="jlistConfig[downloads.footer.text]" rows="4" cols="40"><?php echo htmlspecialchars($jlistConfig['downloads.footer.text'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FOOTER_TEXT_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                                </td>
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_ROBOTS_LABEL')." "; ?></strong><br />
                             <?php
                             echo JHtml::_('select.genericlist', $this->select_fields['robots'], 'jlistConfig[robots]' , 'size="5" class="inputbox"', 'value', 'text', $jlistConfig['robots'] );
                             ?>
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_ROBOTS_CONFIG_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_ACTIVE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[show.header.catlist]', 'class="inputbox"', $jlistConfig['show.header.catlist']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_DESC');?>
                        </td>                
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_LEVELS')." "; ?></strong><br />
                             <input name="jlistConfig[show.header.catlist.levels]" value="<?php echo $jlistConfig['show.header.catlist.levels']; ?>" size="5" maxlength="2"/></td>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_LEVELS_DESC');?>
                        </td>                
                        </tr>
                        
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_UNCAT_ACTIVE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[show.header.catlist.uncategorised]', 'class="inputbox"', $jlistConfig['show.header.catlist.uncategorised']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_UNCAT_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_ALL_ACTIVE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[show.header.catlist.all]', 'class="inputbox"', $jlistConfig['show.header.catlist.all']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_ALL_DESC');?>
                        </td>                
                        </tr>
                        
                        <!--
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_TOP_ACTIVE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[show.header.catlist.topfiles]', 'class="inputbox"', $jlistConfig['show.header.catlist.topfiles']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_TOP_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_NEW_ACTIVE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[show.header.catlist.newfiles]', 'class="inputbox"', $jlistConfig['show.header.catlist.newfiles']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATLISTBOX_NEW_DESC');?>
                        </td>                
                        </tr>
                        -->
                        <tr><td colspan="2"><hr></td></tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_CAT_VIEW_INFO_IN_LISTS_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.category.info]', 'class="inputbox"', $jlistConfig['view.category.info']); ?>
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_CAT_VIEW_INFO_IN_LISTS_TEXT');?>
                        </td>
                        </tr>                                                
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_DETAILSITE_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.detailsite]', 'class="inputbox"', $jlistConfig['view.detailsite']); ?>
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_DETAILSITE_DESC');?>
                        </td>
                        </tr>                         

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_TITLE_AS_LINK_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.download.title.as.download.link]', 'class="inputbox"', $jlistConfig['use.download.title.as.download.link']); ?>
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_TITLE_AS_LINK_DESC');?>
                        </td>
                        </tr>                         
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PAGENAVI_TOP_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[option.navigate.top]', 'class="inputbox"', $jlistConfig['option.navigate.top']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PAGENAVI_TOP_TEXT');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PAGENAVI_BOTTOM_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[option.navigate.bottom]', 'class="inputbox"', $jlistConfig['option.navigate.bottom']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PAGENAVI_BOTTOM_TEXT');?>
                        </td>                
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PAGENAVI_SUBCATS_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.pagination.subcategories]', 'class="inputbox"', $jlistConfig['use.pagination.subcategories']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PAGENAVI_SUBCATS_TEXT');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_PAGENAVI_AMOUNT_SUBCATS_TITLE')." "; ?></strong><br />
                                <input name="jlistConfig[amount.subcats.per.page.in.pagination]" value="<?php echo $jlistConfig['amount.subcats.per.page.in.pagination']; ?>" size="5" maxlength="3"/></td>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_PAGENAVI_AMOUNT_SUBCATS_DESC');?>
                        </td>
                        </tr>                                                 
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_EMPTY_CATEGORIES')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.empty.categories]', 'class="inputbox"', $jlistConfig['view.empty.categories']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_EMPTY_CATEGORIES_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_NO_FILE_MSG')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.no.file.message.in.empty.category]', 'class="inputbox"', $jlistConfig['view.no.file.message.in.empty.category']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_NO_FILE_MSG_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_VIEW_FE_ORDERING_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.sort.order]', 'class="inputbox"', $jlistConfig['view.sort.order']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_VIEW_FE_ORDERING_DESC');?>
                        </td>                
                        </tr> 
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_BACK_BUTTON')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.back.button]', 'class="inputbox"', $jlistConfig['view.back.button']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_BACK_BUTTON_DESC');?>
                        </td>                
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_PIC_AND_TEXT_FOR_DOWNLOAD_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.also.download.link.text]', 'class="inputbox"', $jlistConfig['view.also.download.link.text']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_PIC_AND_TEXT_FOR_DOWNLOAD_TITLE_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_REMOVE_TITLE_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[remove.field.title.when.empty]', 'class="inputbox"', $jlistConfig['remove.field.title.when.empty']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_REMOVE_TITLE_DESC');?>
                        </td>                
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_DEL_EMPTY_TAGS_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[remove.empty.tags]', 'class="inputbox"', $jlistConfig['remove.empty.tags']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_DEL_EMPTY_TAGS_DESC');?>
                        </td>                
                        </tr>                        
                                                
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_LIGHTBOX_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.lightbox.function]', 'class="inputbox"', $jlistConfig['use.lightbox.function']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_LIGHTBOX_DESC');?>
                        </td>                
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CONTENT_SUPPORT_FOR_ALL_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[activate.general.plugin.support]', 'class="inputbox"', $jlistConfig['activate.general.plugin.support']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CONTENT_SUPPORT_FOR_ALL_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CONTENT_SUPPORT_FOR_DESC_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.general.plugin.support.only.for.descriptions]', 'class="inputbox"', $jlistConfig['use.general.plugin.support.only.for.descriptions']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CONTENT_SUPPORT_FOR_DESC_DESC');?>
                        </td>                
                        </tr>
                        
                        <!-- <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_SEF_ROUTER_OPTION_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.sef.with.file.titles]', 'class="inputbox"', $jlistConfig['use.sef.with.file.titles']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_SEF_ROUTER_OPTION_DESC');?>
                        </td>                
                        </tr> --> 
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_REAL_NAME_IN_FRONTEND')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.real.user.name.in.frontend]', 'class="inputbox"', $jlistConfig['use.real.user.name.in.frontend']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_REAL_NAME_IN_FRONTEND_DESC');?>
                        </td>                
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SHORTENED_FILENAME_TITLE')." "; ?></strong><br />
                                <input name="jlistConfig[shortened.filename.length]" value="<?php echo $jlistConfig['shortened.filename.length']; ?>" size="5" maxlength="4"/></td>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SHORTENED_FILENAME_DESC');?>
                        </td>
                        </tr>                          
                        <!--
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOW_MANY_IMAGES')." "; ?></strong><br />
                                <input name="jlistConfig[fe.upload.amount.of.pictures]" value="<?php echo $jlistConfig['fe.upload.amount.of.pictures']; ?>" size="5" maxlength="4"/></td>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOW_MANY_IMAGES_FE_DESC');?>
                        </td>
                        </tr>                          
                        -->
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_REDIRECT_FOR_DOWNLOAD_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[redirect.after.download]" value="<?php echo $jlistConfig['redirect.after.download']; ?>" size="10" maxlength="10"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_REDIRECT_FOR_DOWNLOAD_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr><td colspan="2"><hr></td></tr> 
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_USE_TABS_TITLE')." "; ?></strong><br />
                               <?php echo $this->select_fields['tabs_box']; ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_USE_TABS_DESC');?>
                        </td>                
                        </tr>
                         <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CUSTOM_TAB_TITLES_TITLE')." (1)"; ?></strong><br />
                               <input name="jlistConfig[additional.tab.title.1]" value="<?php echo htmlspecialchars($jlistConfig['additional.tab.title.1'], ENT_QUOTES ); ?>" size="50" maxlength="70"/>
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CUSTOM_TAB_TITLES_DESC');?>
                        </td>                
                        </tr>
                         <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CUSTOM_TAB_TITLES_TITLE')." (2)"; ?></strong><br />
                               <input name="jlistConfig[additional.tab.title.2]" value="<?php echo htmlspecialchars($jlistConfig['additional.tab.title.2'], ENT_QUOTES ); ?>" size="50" maxlength="70"/>
                        </td>
                        
                        </tr>
                         <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_CUSTOM_TAB_TITLES_TITLE')." (3)"; ?></strong><br />
                               <input name="jlistConfig[additional.tab.title.3]" value="<?php echo htmlspecialchars($jlistConfig['additional.tab.title.3'], ENT_QUOTES ); ?>" size="50" maxlength="70"/>
                        </td>
                        </tr>
                                                                                                
                        <tr><td colspan="2"> <hr> </td> </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_CUT_DESCRIPTION_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[auto.file.short.description]', 'class="inputbox"', $jlistConfig['auto.file.short.description']); ?>
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_CUT_DESCRIPTION_TITLE_DESC');?>
                        </td>
                        </tr>                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_CUT_DESCRIPTION_LENGTH_TITLE')." "; ?></strong><br />
                                <input name="jlistConfig[auto.file.short.description.value]" value="<?php echo $jlistConfig['auto.file.short.description.value']; ?>" size="10" maxlength="10"/></td>
                        <td>
                        <?php echo '';?>
                        </td>
                        </tr>                        
                        
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RATING_ON_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.ratings]', 'class="inputbox"', $jlistConfig['view.ratings']); ?>
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RATING_ON_DESC');?>
                        </td>
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RATING_ONLY_REGGED_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[rating.only.for.regged]', 'class="inputbox"', $jlistConfig['rating.only.for.regged']); ?>                            
                        </td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RATING_ONLY_REGGED_DESC');?>
                        </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>                                                
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CHECKBOX_TEXT')." "; ?></strong><br />
                                <textarea name="jlistConfig[checkbox.top.text]" rows="3" cols="40"><?php echo htmlspecialchars($jlistConfig['checkbox.top.text'], ENT_QUOTES ); ?></textarea>
                        </td>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CHECKBOX_TEXT_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT').'<br />'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_USE_CHECKBOX_INFO');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATS_PER_SIDE')." "; ?></strong><br />
                                <input name="jlistConfig[categories.per.side]" value="<?php echo $jlistConfig['categories.per.side']; ?>" size="10" maxlength="10"/></td>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATS_PER_SIDE_DESC');?>
                        </td>
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FILES_PER_SIDE')." "; ?></strong><br />
                                <input name="jlistConfig[files.per.side]" value="<?php echo $jlistConfig['files.per.side']; ?>" size="10" maxlength="10"/></td>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FILES_PER_SIDE_DESC');?>
                        </td>
                        </tr>                        
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_SORTCATSORDER_TEXT')." "; ?></strong><br />
                        <?php
                        $catsorder = (int)$jlistConfig['cats.order'];
                        $inputbox = JHtml::_('select.genericlist', $this->select_fields['cats_sortorder'], 'jlistConfig[cats.order]' , 'size="3" class="inputbox"', 'value', 'text', $catsorder );
                        echo $inputbox; ?>
                        </td>
                        <td valign="top">
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_SORTCATSORDER_DESC');?>
                        </td>
                          </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_SORTFILESORDER_TEXT')." "; ?></strong><br />
                        <?php
                        $filesorder = (int)$jlistConfig['files.order'];
                        $inputbox = JHtml::_('select.genericlist',$this->select_fields['list_sortorder'], 'jlistConfig[files.order]' , 'size="5" class="inputbox"', 'value', 'text', $filesorder );
                        echo $inputbox; ?>
                        </td>
                        <td valign="top">
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_SORTFILESORDER_DESC');?>
                        </td>
                        </tr>
                    
                        <tr><td colspan="2"><hr></td></tr>

                        <tr>
                        <td width="330"><font color="#990000"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_JCOMMENTS_TITLE')." "; ?></strong></font><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[jcomments.active]', 'class="inputbox"', $jlistConfig['jcomments.active']); ?>
                        </td>
                        <td>
                               <?php 
                               $jcomments = JPATH_SITE.'/components/com_jcomments/jcomments.php';
                               if (file_exists($jcomments)) {
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_JCOMMENTS_EXISTS_DESC');
                               } else {
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_JCOMMENTS_DESC');
                               } ?>
                        </td>
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_JCOMMENTS_VIEW_SUM_TITLE')." "; ?></strong><br />
                             <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[view.sum.jcomments]', 'class="inputbox"', $jlistConfig['view.sum.jcomments']); ?>
                        </td>
                        <td valign="top">
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_JCOMMENTS_VIEW_SUM_DESC');?>
                        </td>
                        </tr>
                        </table>
                      </td>
                  </tr>
                </table>
           </td>
       </tr>
</table>                        

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_BACKEND'), 'backend'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* Backend config */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">

                <tr>
                    <td valign="top" align="left" width="100%">
                       <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_BACKEND_HEAD')." "; ?></th>
                        </tr>
                       
                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_FILES')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[files.editor]', 'class="inputbox"', $jlistConfig['files.editor']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_FILES_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_CATS')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[categories.editor]', 'class="inputbox"', $jlistConfig['categories.editor']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_CATS_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_LICENSES')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[licenses.editor]', 'class="inputbox"', $jlistConfig['licenses.editor']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_LICENSES_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_LAYOUTS')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[layouts.editor]', 'class="inputbox"', $jlistConfig['layouts.editor']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_EDITOR_LAYOUTS_DESC');?>
                        </td>
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOW_MANY_IMAGES')." "; ?></strong><br />
                                <input name="jlistConfig[be.upload.amount.of.pictures]" value="<?php echo $jlistConfig['be.upload.amount.of.pictures']; ?>" size="5" maxlength="4"/></td>
                        <td>
                        <br />
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOW_MANY_IMAGES_BE_DESC');?>
                        </td>
                        </tr>                        
                          
                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILE_LANGUAGE_LIST')." "; ?></strong><br />
                                <textarea name="jlistConfig[language.list]" rows="4" cols="40"><?php echo htmlspecialchars($jlistConfig['language.list'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILE_LANGUAGE_LIST_DESC')." ".JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                                </td>
                        </tr>

                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILE_SYSTEM_LIST')." "; ?></strong><br />
                                <textarea name="jlistConfig[system.list]" rows="4" cols="40"><?php echo htmlspecialchars($jlistConfig['system.list'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILE_SYSTEM_LIST_DESC')." ".JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                                </td>
                        </tr>

                      </table>
                  </td>      
                </tr>                         
            </table>
            </td>
          </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TAB_FILES_FOLDERS'), 'foldersandfiles'); ?>
<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* Folders and Files msettings */ ?>
                
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">

                <tr>
                    <td valign="top" align="left" width="100%">
                       <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TAB_FILES_FOLDERS')." "; ?></th>
                        </tr>                
                
                        <tr>
                        <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_CREATE_AUTO_DIR_NAME_TITLE')." "; ?></strong><br />
                                <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[create.auto.cat.dir]', 'class="inputbox"', $jlistConfig['create.auto.cat.dir']); ?>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_CONFIG_CREATE_AUTO_DIR_NAME_DESC');?>
                        </td>
                        </tr>


                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_UNICODE_TITLE')." "; ?></strong><br />
                                <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.unicode.path.names]', 'class="inputbox"', $jlistConfig['use.unicode.path.names']); ?>
                        <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_UNICODE_DESC'); ?>
                        </td>
                        </tr> 

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_BLANK_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[fix.upload.filename.blanks]', 'class="inputbox"', $jlistConfig['fix.upload.filename.blanks']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_BLANK_DESC');?>
                        </td>                
                        </tr>                          

                       <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_LOW_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[fix.upload.filename.uppercase]', 'class="inputbox"', $jlistConfig['fix.upload.filename.uppercase']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_LOW_DESC');?>
                        </td>                
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_SPECIAL_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[fix.upload.filename.specials]', 'class="inputbox"', $jlistConfig['fix.upload.filename.specials']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_SPECIAL_DESC');?>
                        </td>                
                        </tr>
                        <tr><td colspan="2"><?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_IMAGE_FILENAME_NOTE');?></td></tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr><td colspan="2"><?php echo JText::_('COM_JDOWNLOADS_CONFIG_UPLOAD_FILENAME_SPECIAL_HINT');?></td></tr>                         
                          
                        
                      </table>
                  </td>      
                </tr>                         
            </table>
            </td>
          </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SET_TAB_ADD_FIELDS_TITLE'), 'customfields'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* custom fields config */ ?>
                <table width="100%" cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                    <td valign="top" align="left">
                       <table width="100%">
                           <tr>
                                  <th class="adminheading" colspan="3"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_TAB_ADD_FIELDS_TITLE')." "; ?></th>
                           </tr>

                           <tr>
                                <td colspan="3"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_DESC')." ".JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO'); ?>
                                </td>
                           </tr>

                           <tr>                                                                                     
                           <th class="adminheading" colspan="3"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TYPE')." ".JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_SELECT_FIELD_TYPE'); ?></th>
                           </tr>
                           <tr>
                                <td width="20px" valign="top"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_NUMBER'); ?><br />1. </td>
                                <td width="200px"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TITLE'); ?><br />
                                        <input class="jd-input" name="jlistConfig[custom.field.1.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.1.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_SELECT_FIELD_VALUE')." "; ?><br />
                                        <input class="jd-input" name="jlistConfig[custom.field.1.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.1.values'])); ?>" size="150" maxlength="2000"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">2. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.2.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.2.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.2.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.2.values'])); ?>" size="150" maxlength="2000"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">3. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.3.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.3.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.3.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.3.values'])); ?>" size="150" maxlength="2000"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">4. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.4.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.4.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.4.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.4.values'])); ?>" size="150" maxlength="2000"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">5. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.5.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.5.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.5.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.5.values'])); ?>" size="150" maxlength="2000"/>
                                </td>
                            </tr>
                            <tr>
                                 <td colspan="3"><font color="#990000">
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_SELECT_FIELD_VALUE_NOTE');?>
                                </font>
                                </td> 
                           </tr>
                           
                           <tr>
                          <th class="adminheading" colspan="3"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TYPE')." ".JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_INPUT_FIELD_TYPE'); ?></th>     
                          </tr>                       
                           <tr>
                                <td width="20px" valign="top"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_NUMBER'); ?><br />6. </td> 
                                <td width="200px"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TITLE')." "; ?><br />
                                        <input class="jd-input" name="jlistConfig[custom.field.6.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.6.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_INPUT_DEFAULT_VALUE')." "; ?><br />
                                        <input class="jd-input" name="jlistConfig[custom.field.6.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.6.values'])); ?>" size="150" maxlength="256"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">7. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.7.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.7.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.7.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.7.values'])); ?>" size="150" maxlength="256"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">8. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.8.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.8.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.8.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.8.values'])); ?>" size="150" maxlength="256"/>
                                </td>                                         
                            </tr>
                           <tr>
                                <td width="5px">9. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.9.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.9.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.9.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.9.values'])); ?>" size="150" maxlength="256"/>
                                </td>
                            </tr>
                           <tr>
                                <td width="5px">10. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.10.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.10.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                                <td width="230px">
                                        <input class="jd-input" name="jlistConfig[custom.field.10.values]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.10.values'])); ?>" size="150" maxlength="256"/>
                                </td>
                            </tr>
                            <tr>
                                 <td colspan="3"><font color="#990000">
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_INPUT_FIELD_VALUE_NOTE');?>
                                </font>
                                </td> 
                           </tr>                                                                                               
                           
                           <tr>
                           <th class="adminheading" colspan="3"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TYPE')." ".JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_DATE_FIELD_TYPE'); ?></th>     
                           </tr>                       
                           <tr>
                                <td width="20px" valign="top"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_NUMBER'); ?><br />11. </td> 
                                <td width="200px"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TITLE')." "; ?><br />
                                        <input class="jd-input" name="jlistConfig[custom.field.11.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.11.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                            </tr>
                            <tr>
                                <td width="5px">12. </td> 
                                <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.12.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.12.title'])); ?>" size="50" maxlength="256"/>
                                </td>
                            </tr>
                            
                          <tr>
                          <th class="adminheading" colspan="3"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TYPE')." ".JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_TEXT_FIELD_TYPE'); ?></th>     
                          </tr>                       
                          <tr>
                                <td width="20px" valign="top"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_NUMBER'); ?><br />13. </td> 
                                <td width="200px"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_ADD_FIELDS_FIELD_TITLE')." "; ?><br />
                                        <input class="jd-input" name="jlistConfig[custom.field.13.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.13.title'])); ?>" size="50" maxlength="100"/>
                                </td>
                          </tr><tr>
                            
                                <td width="5px">14. </td> <td width="200px">
                                        <input class="jd-input" name="jlistConfig[custom.field.14.title]" value="<?php echo stripslashes(htmlspecialchars($jlistConfig['custom.field.14.title'])); ?>" size="50" maxlength="256"/>
                                </td>
                          </tr>

                      </table> 
                  </td>      
                </tr>  
             </table>
        </td>
     </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_IMAGES'), 'pics'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* Images config */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                
                

                  <tr>
                      <td valign="top" align="left" width="100%">
                          <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_IMAGES_HEAD')." "; ?></th>
                          </tr>
                        <tr>
                            <td colspan="2">
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_IMAGES_NOTE')." "; ?>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_MINIPICS_SIZE')." "; ?></strong><br />
                              <input name="jlistConfig[info.icons.size]" value="<?php echo $jlistConfig['info.icons.size']; ?>" size="5" maxlength="5"/> px 
                              </td>
                            <td valign="top">
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_MINIPICS_SIZE_DESC').'<br />'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_USE_SYMBOLE_INFO').'<br />';
                                      $msize =  $jlistConfig['info.icons.size'];
                                      $sample_path = JURI::root().'images/jdownloads/miniimages/'; 
                                      $sample_pic = '<img src="'.$sample_path.'date.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" /> ';
                                      $sample_pic .= '<img src="'.$sample_path.'language.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" /> ';
                                      $sample_pic .= '<img src="'.$sample_path.'weblink.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" />';
                                      $sample_pic .= '<img src="'.$sample_path.'stuff.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" /> ';
                                      $sample_pic .= '<img src="'.$sample_path.'contact.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" /> ';
                                      $sample_pic .= '<img src="'.$sample_path.'system.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" />';
                                      $sample_pic .= '<img src="'.$sample_path.'currency.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" /> ';
                                      $sample_pic .= '<img src="'.$sample_path.'download.png" align="middle" width="'.$msize.'" height="'.$msize.'" border="0" alt="" />';
                                      echo $sample_pic; ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATPICS_SIZE')." "; ?></strong><br /><br />
                                    <p><input name="jlistConfig[cat.pic.size]" value="<?php echo $jlistConfig['cat.pic.size']; ?>" size="5" maxlength="5"/> px <?php echo JText::_('COM_JDOWNLOADS_WIDTH'); ?></p>
                                    <p><input name="jlistConfig[cat.pic.size.height]" value="<?php echo $jlistConfig['cat.pic.size.height']; ?>" size="5" maxlength="5"/> px <?php echo JText::_('COM_JDOWNLOADS_HEIGHT'); ?></p>
                            </td>
                            <td valign="top">
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_CATPICS_SIZE_DESC');?>
                            </td>
                        </tr>
                        <tr>
                          <td valign="top"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_PIC_TITLE')." "; ?></strong><br />
                             <?php echo $this->select_fields['inputbox_pic']; ?>
                          </td>
                          <td valign ="top"><?php echo ' '.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_PIC_DESC'); ?>
                          </td>
                        </tr>
                        <tr>
                             <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.cat_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/catimages/'; ?>" + getSelectedText( 'adminForm', 'cat_pic' );
                                } else {
                                    jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib" width="<?php echo $jlistConfig['cat.pic.size']; ?>" height="<?php echo $jlistConfig['cat.pic.size.height']; ?>" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                             </td>
                        </tr> 
                        <tr><td colspan="3"><hr></td></tr>
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FILEPICS_SIZE')." "; ?></strong><br /><br />
                                    <p><input name="jlistConfig[file.pic.size]" value="<?php echo $jlistConfig['file.pic.size']; ?>" size="5" maxlength="5"/> px <?php echo JText::_('COM_JDOWNLOADS_WIDTH'); ?></p>
                                    <p><input name="jlistConfig[file.pic.size.height]" value="<?php echo $jlistConfig['file.pic.size.height']; ?>" size="5" maxlength="5"/> px <?php echo JText::_('COM_JDOWNLOADS_HEIGHT'); ?></p>
                            </td>
                            <td valign="top">
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FILEPICS_SIZE_DESC');?>
                            </td>
                        </tr>
                        <tr>
                              <td valign="top"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_FILE_PIC_TITLE')." "; ?></strong><br />
                                 <?php echo $this->select_fields['inputbox_pic_file']; ?>
                              </td>
                              <td valign ="top"><?php echo ' '.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_FILE_PIC_DESC'); ?>
                              </td>
                        </tr>

                        <tr>
                             <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.file_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/fileimages/'; ?>" + getSelectedText( 'adminForm', 'file_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib2" width="<?php echo $jlistConfig['file.pic.size']; ?>" height="<?php echo $jlistConfig['file.pic.size.height']; ?>" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                             </td>
                        </tr>
                        <tr><td colspan="3"><hr></td></tr>
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_SETTINGS_FRONTEND_FEATURED_PIC_SIZE')." "; ?></strong><br /><br />
                                    <p><input name="jlistConfig[featured.pic.size]" value="<?php echo $jlistConfig['featured.pic.size']; ?>" size="5" maxlength="5"/> px <?php echo JText::_('COM_JDOWNLOADS_WIDTH'); ?></p>
                                    <p><input name="jlistConfig[featured.pic.size.height]" value="<?php echo $jlistConfig['featured.pic.size.height']; ?>" size="5" maxlength="5"/> px <?php echo JText::_('COM_JDOWNLOADS_HEIGHT'); ?></p>
                            </td>
                            <td valign="top">
                                <?php echo JText::_('COM_JDOWNLOADS_SETTINGS_FRONTEND_FEATURED_PIC_SIZE_DESC');?>
                            </td>
                        </tr>
                        <tr>
                              <td valign="top"><strong><?php echo JText::_('COM_JDOWNLOADS_SETTINGS_FRONTEND_FEATURED_TITLE')." "; ?></strong><br />
                                 <?php
                                  echo $this->select_fields['inputbox_pic_featured']; ?>
                              </td>
                              <td valign ="top"><?php echo ' '.JText::_('COM_JDOWNLOADS_SETTINGS_FRONTEND_FEATURED_DESC'); ?>
                              </td>
                        </tr>

                        <tr>
                             <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.featured_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/featuredimages/'; ?>" + getSelectedText( 'adminForm', 'featured_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib2b" width="<?php echo $jlistConfig['featured.pic.size']; ?>" height="<?php echo $jlistConfig['featured.pic.size.height']; ?>" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                             </td>
                        </tr>                        
                        <tr><td> </td></tr>
                        <tr>
                            <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_CSS_BUTTONS_HEAD')." "; ?></th>
                        </tr> 

                        <tr>
                            <td colspan="3">
                            <?php echo  '<strong><font color="red">'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_SIMPLE_CSS_BUTTONS_INFO').'</font></strong>'; ?>
                            </td>
                        </tr>
                            
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_SIMPLE_CSS_BUTTONS_TITLE'); ?></strong><br />
                                   <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.css.buttons.instead.icons]', 'class="inputbox"', $jlistConfig['use.css.buttons.instead.icons']); ?>
                            </td>
                            <td valign="top"><br />
                                   <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_USE_SIMPLE_CSS_BUTTONS_DESC');?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_DOWNLOAD_BUTTON')." "; ?></strong><br />                            
                            <?php                                 
                                    $colors = array(
                                    JHtml::_('select.option', '', JText::_('COM_JDOWNLOADS_NONE' ) ),
                                    JHtml::_('select.option', 'jblack', JText::_('COM_JDOWNLOADS_BUTTON_BLACK' ) ),
                                    JHtml::_('select.option', 'jwhite', JText::_('COM_JDOWNLOADS_BUTTON_WHITE' ) ),
                                    JHtml::_('select.option', 'jgray', JText::_('COM_JDOWNLOADS_BUTTON_GRAY' ) ),
                                    JHtml::_('select.option', 'jorange', JText::_('COM_JDOWNLOADS_BUTTON_ORANGE' ) ),
                                    JHtml::_('select.option', 'jred', JText::_('COM_JDOWNLOADS_BUTTON_RED' ) ),
                                    JHtml::_('select.option', 'jblue', JText::_('COM_JDOWNLOADS_BUTTON_BLUE' ) ),
                                    JHtml::_('select.option', 'jgreen', JText::_('COM_JDOWNLOADS_BUTTON_GREEN' ) ),
                                    JHtml::_('select.option', 'jrosy', JText::_('COM_JDOWNLOADS_BUTTON_ROSY' ) ),
                                    JHtml::_('select.option', 'jpink', JText::_('COM_JDOWNLOADS_BUTTON_PINK' ) ),                                                                                                            
                                    );
                                    echo JHtml::_('select.genericlist', $colors, 'jlistConfig[css.button.color.download]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.color.download'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_DOWNLOAD_BUTTON_INFO');?><br />
                                 <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE');?><br /><br />  
                                 <span class="jdbutton jblack jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLACK'); ?></span> <span class="jdbutton jwhite jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_WHITE'); ?></span> <span class="jdbutton jgray jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GRAY'); ?></span> <span class="jdbutton jorange jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ORANGE'); ?></span> <span class="jdbutton jred jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_RED'); ?></span>
                                 <span class="jdbutton jblue jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLUE'); ?></span> <span class="jdbutton jgreen jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GREEN'); ?></span> <span class="jdbutton jrosy jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ROSY'); ?></span> <span class="jdbutton jpink jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_PINK'); ?></span>
                            </td>                           
                        </tr>
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_DOWNLOAD_BUTTON_SIZE')." "; ?></strong><br />                            
                            <?php                                 
                                    $arr_size = array(
                                    JHtml::_('select.option', '', JText::_('COM_JDOWNLOADS_BUTTON_STANDARD' ) ),
                                    JHtml::_('select.option', 'jmedium', JText::_('COM_JDOWNLOADS_BUTTON_MEDIUM' ) ),
                                    JHtml::_('select.option', 'jsmall', JText::_('COM_JDOWNLOADS_BUTTON_SMALL' ) ),
                                    );                                    
                                    echo JHtml::_('select.genericlist', $arr_size, 'jlistConfig[css.button.size.download]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.size.download'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE_SIZE');?><br /><br />
                                 <span class="jdbutton jred"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_STANDARD'); ?></span> <span class="jdbutton jred jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_MEDIUM'); ?></span>
                                 <span class="jdbutton jred jsmall"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_SMALL'); ?></span>
                            </td>                           
                        </tr>
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_DOWNLOAD_BUTTON_SIZE_SMALL')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $arr_size, 'jlistConfig[css.button.size.download.small]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.size.download.small'] );
                             ?>     
                           </td>
                           <td valign="top">       
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_DOWNLOAD_BUTTON_SMALL_INFO');?><br />
                                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE_SIZE');?><br /><br />
                                 <span class="jdbutton jred"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_STANDARD'); ?></span> <span class="jdbutton jred jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_MEDIUM'); ?></span>
                                 <span class="jdbutton jred jsmall"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_SMALL'); ?></span>
                            </td>                           
                        </tr>                        
                        
                        <tr>
                            <td width="330"></td><td><hr></td>
                        </tr>                                                  
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_MIRROR_1_BUTTON')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $colors, 'jlistConfig[css.button.color.mirror1]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.color.mirror1'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE');?><br /><br />
                                 <span class="jdbutton jblack jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLACK'); ?></span> <span class="jdbutton jwhite jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_WHITE'); ?></span> <span class="jdbutton jgray jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GRAY'); ?></span> <span class="jdbutton jorange jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ORANGE'); ?></span> <span class="jdbutton jred jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_RED'); ?></span>
                                 <span class="jdbutton jblue jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLUE'); ?></span> <span class="jdbutton jgreen jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GREEN'); ?></span> <span class="jdbutton jrosy jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ROSY'); ?></span> <span class="jdbutton jpink jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_PINK'); ?></span>
                            </td>                           
                        </tr>
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_MIRROR_2_BUTTON')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $colors, 'jlistConfig[css.button.color.mirror2]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.color.mirror2'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE');?><br /><br />
                                 <span class="jdbutton jblack jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLACK'); ?></span> <span class="jdbutton jwhite jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_WHITE'); ?></span> <span class="jdbutton jgray jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GRAY'); ?></span> <span class="jdbutton jorange jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ORANGE'); ?></span> <span class="jdbutton jred jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_RED'); ?></span>
                                 <span class="jdbutton jblue jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLUE'); ?></span> <span class="jdbutton jgreen jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GREEN'); ?></span> <span class="jdbutton jrosy jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ROSY'); ?></span> <span class="jdbutton jpink jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_PINK'); ?></span>
                            </td>                           
                        </tr>                                                
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_MIRROR_BUTTON_SIZE')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $arr_size, 'jlistConfig[css.button.size.download.mirror]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.size.download.mirror'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE_SIZE');?><br /><br />
                                 <span class="jdbutton jred"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_STANDARD'); ?></span> <span class="jdbutton jred jmedium"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_MEDIUM'); ?></span>
                                 <span class="jdbutton jred jsmall"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_SMALL'); ?></span>
                            </td>                           
                        </tr>
                        
                        <tr>
                            <td width="330"></td><td><hr></td>
                        </tr>
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_HOT_BUTTON')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $colors, 'jlistConfig[css.button.color.hot]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.color.hot'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE_STATUS');?><br /><br />
                                 <span class="jdbutton jblack jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLACK'); ?></span> <span class="jdbutton jwhite jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_WHITE'); ?></span> <span class="jdbutton jgray jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GRAY'); ?></span> <span class="jdbutton jorange jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ORANGE'); ?></span> <span class="jdbutton jred jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_RED'); ?></span>
                                 <span class="jdbutton jblue jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLUE'); ?></span> <span class="jdbutton jgreen jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GREEN'); ?></span> <span class="jdbutton jrosy jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ROSY'); ?></span> <span class="jdbutton jpink jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_PINK'); ?></span>
                            </td>                           
                        </tr>
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_NEW_BUTTON')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $colors, 'jlistConfig[css.button.color.new]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.color.new'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE_STATUS');?><br /><br />
                                 <span class="jdbutton jblack jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLACK'); ?></span> <span class="jdbutton jwhite jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_WHITE'); ?></span> <span class="jdbutton jgray jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GRAY'); ?></span> <span class="jdbutton jorange jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ORANGE'); ?></span> <span class="jdbutton jred jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_RED'); ?></span>
                                 <span class="jdbutton jblue jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLUE'); ?></span> <span class="jdbutton jgreen jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GREEN'); ?></span> <span class="jdbutton jrosy jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ROSY'); ?></span> <span class="jdbutton jpink jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_PINK'); ?></span>
                            </td>                           
                        </tr> 
                        
                        <tr>
                            <td valign="top" width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SELECT_UPDATED_BUTTON')." "; ?></strong><br />                            
                            <?php                                 
                                    echo JHtml::_('select.genericlist', $colors, 'jlistConfig[css.button.color.updated]', 'class="radio"', 'value', 'text', $jlistConfig['css.button.color.updated'] );
                             ?>     
                           </td>
                           <td valign="top">
                                 <br /><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_BUTTON_EXAMPLE_STATUS');?><br /><br />
                                 <span class="jdbutton jblack jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLACK'); ?></span> <span class="jdbutton jwhite jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_WHITE'); ?></span> <span class="jdbutton jgray jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GRAY'); ?></span> <span class="jdbutton jorange jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ORANGE'); ?></span> <span class="jdbutton jred jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_RED'); ?></span>
                                 <span class="jdbutton jblue jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_BLUE'); ?></span> <span class="jdbutton jgreen jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_GREEN'); ?></span> <span class="jdbutton jrosy jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_ROSY'); ?></span> <span class="jdbutton jpink jstatus"><?php echo JText::_('COM_JDOWNLOADS_BUTTON_PINK'); ?></span>
                            </td>                           
                        </tr>                                                                          
                                               
                        
                        <tr><td> </td></tr>
                                                
                        <tr>
                            <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_BUTTONS_HEAD')." "; ?></th>
                        </tr>                        
                        
                         <tr>
                             <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_NEW_PIC_TITLE')." "; ?></strong><br />
                                <?php echo $this->select_fields['inputbox_new']." -----> " ?>     
                            
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.new_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/newimages/'; ?>" + getSelectedText( 'adminForm', 'new_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib4" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                            </td>
                            <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOT_NEW_PIC_DESC2');?>
                            </td>
                        </tr>  
                        <tr>
                          
                        </tr>  
                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOT_PIC_TITLE')." "; ?></strong><br />
                                <?php echo $this->select_fields['inputbox_hot']." -----> " ?>
                                <script language="javascript" type="text/javascript">
                                if (document.adminForm.hot_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/hotimages/'; ?>" + getSelectedText( 'adminForm', 'hot_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib3" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                            </td>
                            <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOT_NEW_PIC_DESC');?>
                            </td>
                        </tr>
                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPDATED_PIC_TITLE')." "; ?></strong><br />
                                <?php echo $this->select_fields['inputbox_upd']." -----> " ?>
                                <script language="javascript" type="text/javascript">
                                if (document.adminForm.upd_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/updimages/'; ?>" + getSelectedText( 'adminForm', 'upd_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib8" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                            </td>
                            <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOT_NEW_PIC_DESC3');?>
                            </td>
                        </tr>
         
                         <tr>
                            <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_SHOW_SYMBOL_HEAD')." "; ?></th>
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_NEW_TITLE')." "; ?></strong><br />
                                <input name="jlistConfig[days.is.file.new]" value="<?php echo $jlistConfig['days.is.file.new']; ?>" size="5" maxlength="5"/></td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_NEW_DESC');?>
                        </td>
                        </tr> 
                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOT_TITLE')." "; ?></strong><br />
                                    <input name="jlistConfig[loads.is.file.hot]" value="<?php echo $jlistConfig['loads.is.file.hot']; ?>" size="5" maxlength="10"/></td>
                            <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HOT_DESC');?>
                            </td>
                        </tr>  
                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPD_TITLE')." "; ?></strong><br />
                                    <input name="jlistConfig[days.is.file.updated]" value="<?php echo $jlistConfig['days.is.file.updated']; ?>" size="5" maxlength="5"/></td>
                            <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_UPD_DESC');?>
                            </td>
                        </tr> 

                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>   

                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_DOWNLOAD_BUTTON_TITLE')." "; ?></strong><br />
                                 <?php echo $this->select_fields['inputbox_down']; ?>     
                             <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_DOWNLOAD_BUTTON_DESC');?>
                            </td>
                        </tr>                            

                        <tr>
                          <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.down_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/downloadimages/'; ?>" + getSelectedText( 'adminForm', 'down_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib5" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                          </td>
                        </tr>

                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILES_DOWNLOAD_BUTTON_TITLE')." "; ?></strong><br />
                                 <?php echo $this->select_fields['inputbox_down2']; ?>     
                             <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILES_DOWNLOAD_BUTTON_DESC');?>
                            </td>
                        </tr>                            

                        <tr>
                          <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.down_pic2.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/downloadimages/'; ?>" + getSelectedText( 'adminForm', 'down_pic2' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib9" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                          </td>
                        </tr>

                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_MIRROR_BUTTON_TITLE1')." "; ?></strong><br />
                                 <?php echo $this->select_fields['inputbox_mirror_1']; ?>     
                             <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_DOWNLOAD_BUTTON_DESC');?>
                            </td>
                        </tr>                            

                        <tr>
                          <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.mirror_1_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/downloadimages/'; ?>" + getSelectedText( 'adminForm', 'mirror_1_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib6" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script>
                          </td>
                        </tr>

                        <tr>
                            <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_MIRROR_BUTTON_TITLE2')." "; ?></strong><br />
                                 <?php echo $this->select_fields['inputbox_mirror_2']; ?>     
                             <td>
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_DOWNLOAD_BUTTON_DESC');?>
                            </td>
                        </tr>                            

                        <tr>
                          <td valign="top">
                               <script language="javascript" type="text/javascript">
                                if (document.adminForm.mirror_2_pic.options.value!=''){
                                    jsimg="<?php echo JURI::root().'images/jdownloads/downloadimages/'; ?>" + getSelectedText( 'adminForm', 'mirror_2_pic' );
                                } else {
                                     jsimg='';
                                }
                                document.write('<img src=' + jsimg + ' name="imagelib7" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                               </script><br /><br /> 
                          </td>
                        </tr>
                        
                      <tr>
                      <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_THUMBNAILS_HEAD')." "; ?></th>
                      </tr>
                        
                        <tr><td colspan="3"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_TITLE')." "; ?></strong><br />
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_INFO')." "; ?><br /><br />
                            <?php if (function_exists('gd_info')){
                                        echo '<font color="green">'.JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_STATUS_GD_OK').'</font>';
                                  } else {
                                        echo '<font color="red">'.JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_STATUS_GD_NOT_OK').'</font>';
                                  } ?>   
                        </td></tr>
                        <tr>
                        <td width="330">
                                <input name="jlistConfig[thumbnail.size.height]" value="<?php echo $jlistConfig['thumbnail.size.height']; ?>" size="6" maxlength="5"/> px</td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_SIZE_HEIGHT');?>
                        </td>
                          </tr>
                          <tr>
                           <td width="330">
                                <input name="jlistConfig[thumbnail.size.width]" value="<?php echo $jlistConfig['thumbnail.size.width']; ?>" size="6" maxlength="5"/> px</td>
                           <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_SIZE_WIDTH');?>
                           </td>
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_CREATE_ALL_NEW_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'resize_thumbs', 'class="inputbox"', 0); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_CREATE_ALL_NEW_DESC').' '.JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_THUMBS_CREATE_ALL_NEW_DESC2');?>
                        </td>
                        </tr>
                        <tr><td colspan="3"><hr></td></tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_AUTO_THUMBS_BY_PICS_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[create.auto.thumbs.from.pics]', 'class="inputbox"', $jlistConfig['create.auto.thumbs.from.pics']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_AUTO_THUMBS_BY_PICS_DESC');?>
                        </td>
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_AUTO_THUMBS_BY_PICS_SCAN_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[create.auto.thumbs.from.pics.by.scan]', 'class="inputbox"', $jlistConfig['create.auto.thumbs.from.pics.by.scan']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_AUTO_THUMBS_BY_PICS_SCAN_DESC');?>
                        </td>
                        </tr> 

                        <tr>
                        <td width="330">
                                <input name="jlistConfig[create.auto.thumbs.from.pics.image.height]" value="<?php echo $jlistConfig['create.auto.thumbs.from.pics.image.height']; ?>" size="6" maxlength="5"/> px</td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_BIG_IMAGES_HEIGHT');?>
                        </td>
                          </tr>
                          <tr>
                           <td width="330">
                                <input name="jlistConfig[create.auto.thumbs.from.pics.image.width]" value="<?php echo $jlistConfig['create.auto.thumbs.from.pics.image.width']; ?>" size="6" maxlength="5"/> px</td>
                           <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_BIG_IMAGES_WIDTH');?>
                           </td>
                        </tr>                        

                        <tr><td colspan="3"><hr></td></tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_PDF_THUMB_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[create.pdf.thumbs]', 'class="inputbox"', $jlistConfig['create.pdf.thumbs']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_PDF_THUMB_DESC');?>
                        </td>
                        </tr>
                        <tr><td width="330">      
                               <?php if (extension_loaded('imagick')){
                                        echo '<font color="green">'.JText::_('COM_JDOWNLOADS_BACKEND_IMAGICK_STATE_ON').'</font>';
                                  } else {
                                        echo '<font color="red">'.JText::_('COM_JDOWNLOADS_BACKEND_IMAGICK_STATE_OFF').'</font>';
                                  } ?> 
                        </td>
                        </tr>
                        
                        <!-- imagick path option not used in this release
                        <tr>
                         <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_IMAGICK_PATH')." "; ?></strong><br />
                             <input name="jlistConfig[imagemagick.path]" value="<?php echo $jlistConfig['imagemagick.path']; ?>" size="50" maxlength="250"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_IMAGICK_PATH_DESC');?>
                        </td>
                        </tr>                        
                        -->
                        
                        <?php $types[] = JHtml::_('select.option', 'GIF', 'GIF');
                              $types[] = JHtml::_('select.option', 'PNG', 'PNG');
                              $types[] = JHtml::_('select.option', 'JPG', 'JPG');
                              $file_type_inputbox = JHtml::_('select.genericlist', $types, "jlistConfig[pdf.thumb.image.type]" , 'size="1" class="inputbox"', 'value', 'text', $jlistConfig['pdf.thumb.image.type'] );
                        ?>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_AUTO_THUMBS_BY_PICS_SCAN_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[create.pdf.thumbs.by.scan]', 'class="inputbox"', $jlistConfig['create.pdf.thumbs.by.scan']); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_CREATE_PDF_THUMB_SCAN_DESC');?>
                        </td>
                        </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_PDF_THUMBS_IMAGE_TYPE_TITLE')." "; ?></strong><br />
                              <?php echo $file_type_inputbox; ?>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_PDF_THUMBS_IMAGE_TYPE'); ?>
                        </td>
                        </tr>
                        <tr>
                        <td width="330">
                                <input name="jlistConfig[pdf.thumb.height]" value="<?php echo $jlistConfig['pdf.thumb.height']; ?>" size="6" maxlength="5"/> px</td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_PDF_THUMBS_HEIGHT');?>
                        </td>
                        </tr>
                        <tr>
                           <td width="330">
                                <input name="jlistConfig[pdf.thumb.width]" value="<?php echo $jlistConfig['pdf.thumb.width']; ?>" size="6" maxlength="5"/> px</td>
                           <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_PDF_THUMBS_WIDTH');?>
                           </td>
                        </tr>
                        <tr>
                        <td width="330">
                                <input name="jlistConfig[pdf.thumb.pic.height]" value="<?php echo $jlistConfig['pdf.thumb.pic.height']; ?>" size="6" maxlength="5"/> px</td>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_PDF_BIG_THUMBS_HEIGHT');?>
                        </td>
                          </tr>
                          <tr>
                           <td width="330">
                                <input name="jlistConfig[pdf.thumb.pic.width]" value="<?php echo $jlistConfig['pdf.thumb.pic.width']; ?>" size="6" maxlength="5"/> px</td>
                           <td>
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_SETTINGS_PDF_BIG_THUMBS_WIDTH');?>
                           </td>
                        </tr> 
                        
                        <tr><td colspan="3"><hr></td></tr>       
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PLACEHOLDER_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[thumbnail.view.placeholder]', 'class="inputbox"', $jlistConfig['thumbnail.view.placeholder']); ?> 
                               <br />
                               <?php 
                               $nopic = '<img src="'.JURI::root().'images/jdownloads/screenshots/thumbnails/no_pic.gif" width="'.$jlistConfig['thumbnail.size.width'].'" height="'.$jlistConfig['thumbnail.size.height'].'" />';
                               echo $nopic;
                               ?> 
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PLACEHOLDER_TEXT');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><br /><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PLACEHOLDER_IN_LIST_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[thumbnail.view.placeholder.in.lists]', 'class="inputbox"', $jlistConfig['thumbnail.view.placeholder.in.lists']); ?> 
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_VIEW_PLACEHOLDER_IN_LIST_TEXT');?>
                        </td>                
                        </tr>

                      </table>
                    </td>
                </tr>
            </table>
       </td>
    </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MEDIA_TAB_TITLE'), 'multimedia'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                      <td valign="top" align="left" width="100%">
                        <table width="100%">

                        <tr>
                              <th class="adminheading" colspan="2"><font color="#990000"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_TITLE')." "; ?></font></th>
                        </tr>

                       <tr>
                        <td colspan="2"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_DESC')." "; ?></strong>
                        </td>
                       </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_USE_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[html5player.use]', 'class="inputbox"', $jlistConfig['html5player.use']); ?>
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_USE_DESC');?>
                        </td>                
                        </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_ONLY_IN_DETAILS_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[html5player.view.video.only.in.details]', 'class="inputbox"', $jlistConfig['html5player.view.video.only.in.details']); ?>
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_ONLY_IN_DETAILS_DESC');?>
                        </td>                
                        </tr>                         
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_BOX_WIDTH_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[html5player.width]" value="<?php echo $jlistConfig['html5player.width']; ?>" size="5" maxlength="5"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_BOX_WIDTH_DESC');?>
                        </td>                
                        </tr>  

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_BOX_HEIGHT_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[html5player.height]" value="<?php echo $jlistConfig['html5player.height']; ?>" size="5" maxlength="5"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_BOX_HEIGHT_DESC');?>
                        </td>                
                        </tr> 

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_BOX_AUDIO_WIDTH_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[html5player.audio.width]" value="<?php echo $jlistConfig['html5player.audio.width']; ?>" size="5" maxlength="5"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_HTML5_BOX_AUDIO_WIDTH_DESC');?>
                        </td>                
                        </tr>  
                        

                        <tr><td><br /></td></tr>

                        <tr>
                              <th class="adminheading" colspan="2"><font color="#990000"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MEDIA_SECOND_TITLE')." "; ?></font></th>
                        </tr>

                       <tr>
                        <td colspan="2"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MEDIA_DESC')." "; ?></strong>
                        </td>
                       </tr>

                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_USE_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[flowplayer.use]', 'class="inputbox"', $jlistConfig['flowplayer.use']); ?>
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_USE_DESC');?>
                        </td>                
                        </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_ONLY_IN_DETAILS_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[flowplayer.view.video.only.in.details]', 'class="inputbox"', $jlistConfig['flowplayer.view.video.only.in.details']); ?>
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_ONLY_IN_DETAILS_DESC');?>
                        </td>                
                        </tr>                         
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_WIDTH_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[flowplayer.playerwidth]" value="<?php echo $jlistConfig['flowplayer.playerwidth']; ?>" size="5" maxlength="5"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_WIDTH_DESC');?>
                        </td>                
                        </tr>  

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_HEIGHT_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[flowplayer.playerheight]" value="<?php echo $jlistConfig['flowplayer.playerheight']; ?>" size="5" maxlength="5"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_HEIGHT_DESC');?>
                        </td>                
                        </tr> 
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_HEIGHT_MP3_TITLE')." "; ?></strong><br />
                               <input name="jlistConfig[flowplayer.playerheight.audio]" value="<?php echo $jlistConfig['flowplayer.playerheight.audio']; ?>" size="5" maxlength="5"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_BOX_HEIGHT_MP3_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_SETTINGS_TITLE')." "; ?></strong><br />
                            <textarea name="jlistConfig[flowplayer.control.settings]" rows="12" cols="40"><?php echo htmlspecialchars($jlistConfig['flowplayer.control.settings'], ENT_QUOTES); ?></textarea>
                        </td>
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FLOW_SETTINGS_DESC');?>
                        </td>
                        </tr>                        
                                                 

                        <tr>
                              <th class="adminheading" colspan="2"><font color="#990000"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MEDIA_TITLE')." "; ?></font></th>
                        </tr>

                       <tr>
                        <td colspan="2"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_DESC1')." "; ?></strong>
                        </td>
                       </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_CONFIG_TITLE')." "; ?></strong><br />
                            <textarea name="jlistConfig[mp3.player.config]" rows="6" cols="40"><?php echo htmlspecialchars($jlistConfig['mp3.player.config'], ENT_QUOTES); ?></textarea>
                        </td>
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_CONFIG_DESC');?>
                        </td>
                        </tr> 
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_CONFIG_VIEW_ID3_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[mp3.view.id3.info]', 'class="inputbox"', $jlistConfig['mp3.view.id3.info']); ?>
                        </td>
                        <td valign="top">
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_CONFIG_VIEW_ID3_DESC');?>
                        </td>                
                        </tr>
                       
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_CONFIG_VIEW_ID3_LAY_TITLE')." "; ?></strong><br />
                            <textarea name="jlistConfig[mp3.info.layout]" rows="12" cols="40"><?php echo htmlspecialchars($jlistConfig['mp3.info.layout'], ENT_QUOTES); ?></textarea>
                        </td>
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_CONFIG_VIEW_ID3_LAY_DESC');?>
                        </td>
                        </tr>
                        <tr>
                        <td colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MP3_DESC2')." "; ?>
                        </td>                
                        </tr>                       
                    </table>
                    
                    
                    
                   </td>
                </tr>
             </table>
         </td>
     </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FE_UPLOAD_TAB_TITLE'), 'upload'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">
<?php /* upload config */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                      <td valign="top" align="left" width="100%">
                          <table width="100%">
                        
                      <tr>                                                                                     
                      <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_TITLE_HEAD'); ?></th>
                      </tr>
                        
                        <tr>
                        <td colspan="2"><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_DESC2')." "; ?><br />
                        <?php
                        ?>
                        </td>
                        </tr>                         
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_LABEL')." "; ?></strong><br />
                        <?php
                        $runtime = $jlistConfig['plupload.runtime'];
                        $runtime_inputbox = JHtml::_('select.genericlist', $this->select_fields['pluploader_runtime'], 'jlistConfig[plupload.runtime]' , 'size="1" class="inputbox"', 'value', 'text', $runtime );
                        echo $runtime_inputbox; ?>
                        </td>
                        <td valign="top">
                        <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RUNTIME_LABEL_DESC');?>
                        </td>
                          </tr>                        
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_MAX_FILE_SIZE')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.max.file.size]" value="<?php echo $jlistConfig['plupload.max.file.size']; ?>" size="5" maxlength="10"/><br /><br />
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOAD_MAX_FILESIZE_INFO_TITLE').' '. ini_get('upload_max_filesize'); ?>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_MAX_FILE_SIZE_DESC');?>
                        </td>                
                        </tr>                       
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_CHUNK_SIZE')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.chunk.size]" value="<?php echo $jlistConfig['plupload.chunk.size']; ?>" size="5" maxlength="10"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_CHUNK_SIZE_DESC');?>
                        </td>                
                        </tr>                         

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_CHUNK_UNIT')." "; ?></strong><br />
                        <?php
                        $chunk = $jlistConfig['plupload.chunk.unit'];
                        $chunk_inputbox = JHtml::_('select.genericlist', $this->select_fields['pluploader_unit'], 'jlistConfig[plupload.chunk.unit]' , 'size="1" class="inputbox"', 'value', 'text', $chunk );
                        echo $chunk_inputbox; ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_CHUNK_UNIT_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RENAME')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[plupload.rename]', 'class="inputbox"', $jlistConfig['plupload.rename']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RENAME_DESC');?>
                        </td>                
                        </tr>                        

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_IMAGE_FILE_EXTENSIONS')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.image.file.extensions]" value="<?php echo $jlistConfig['plupload.image.file.extensions']; ?>" size="50" maxlength="500"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_IMAGE_FILE_EXTENSIONS_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_OTHER_FILE_EXTENSIONS')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.other.file.extensions]" value="<?php echo $jlistConfig['plupload.other.file.extensions']; ?>" size="50" maxlength="500"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_OTHER_FILE_EXTENSIONS_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_UNIQUE_NAMES')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[plupload.unique.names]', 'class="inputbox"', $jlistConfig['plupload.unique.names']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_UNIQUE_NAMES_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_ENABLE_IMAGE_RESIZING')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[plupload.enable.image.resizing]', 'class="inputbox"', $jlistConfig['plupload.enable.image.resizing']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_ENABLE_IMAGE_RESIZING_DESC');?>
                        </td>                
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RESIZE_WIDTH')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.resize.width]" value="<?php echo $jlistConfig['plupload.resize.width']; ?>" size="5" maxlength="10"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RESIZE_WIDTH_DESC');?>
                        </td>                
                        </tr>  
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RESIZE_HEIGHT')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.resize.height]" value="<?php echo $jlistConfig['plupload.resize.height']; ?>" size="5" maxlength="10"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RESIZE_HEIGHT_DESC');?>
                        </td>                
                        </tr>  
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RESIZE_QUALITY')." "; ?></strong><br />
                               <input name="jlistConfig[plupload.resize.quality]" value="<?php echo $jlistConfig['plupload.resize.quality']; ?>" size="5" maxlength="3"/>
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_RESIZE_QUALITY_DESC');?>
                        </td>                
                        </tr> 
                        
                        <tr>
                             <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_ENABLE_LOG')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[plupload.enable.uploader.log]', 'class="inputbox"', $jlistConfig['plupload.enable.uploader.log']); ?> 
                             </td>
                             <td>
                               <?php echo JText::_('COM_JDOWNLOADS_UPLOADER_ENABLE_LOG_DESC');?>
                             </td>
                        </tr>

                        </table>
                      </td>
                  </tr>
             </table>
       </td>
    </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_SECURITY'), 'security'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* Backend config */ ?>
                <table cellpadding="4" cellspacing="1" border="0" class="adminlist">

                <tr>
                      <td valign="top" align="left" width="100%">
                      <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_SECURITY_HEAD')." "; ?></th>
                        </tr>

                <tr>
                    <td width="330" valign="top">
                        <strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ANTILEECH_TITLE')." "; ?></strong><br />
                         <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[anti.leech]', 'class="inputbox"', $jlistConfig['anti.leech']); ?> 
                    </td>
                    <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ANTILEECH_DESK');?>
                    </td>
                  </tr> 
                <tr><td colspan="2"><hr></td></tr> 
                <tr>
                    <td width="330" valign="top">
                        <strong><?php echo JText::_('COM_JDOWNLOADS_STOP_LEECHING_OPTION_TITLE')." "; ?></strong><br />
                         <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[check.leeching]', 'class="inputbox"', $jlistConfig['check.leeching']); ?> 
                    </td>
                    <td>
                        <?php echo JText::_('COM_JDOWNLOADS_STOP_LEECHING_OPTION_DESC');?>
                    </td>
                  </tr>
                  <tr>
                    <td width="330" valign="top">
                        <strong><?php echo JText::_('COM_JDOWNLOADS_STOP_LEECHING_OPTION_NO_REFERER_TITLE')." "; ?></strong><br />
                         <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[block.referer.is.empty]', 'class="inputbox"', $jlistConfig['block.referer.is.empty']); ?> 
                    </td>
                    <td>
                        <?php echo JText::_('COM_JDOWNLOADS_STOP_LEECHING_OPTION_NO_REFERER_DESC');?>
                    </td>
                  </tr>
                  <tr>
                     <td width="330" valign="top"><strong><?php echo JText::_('COM_JDOWNLOADS_STOP_LEECHING_ALLOWED_SITES_OPTION_TITLE')." "; ?></strong><br />
                             <textarea name="jlistConfig[allowed.leeching.sites]" rows="4" cols="40"><?php echo $jlistConfig['allowed.leeching.sites']; ?></textarea>
                     </td>
                     <td valign="top">
                              <?php echo JText::_('COM_JDOWNLOADS_STOP_LEECHING_ALLOWED_SITES_OPTION_DESC');?>
                     </td>
                   </tr>                  
                <tr><td colspan="2"><hr></td></tr> 
                <tr>
                    <td width="330">
                        <strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MAIL_SECURITY_TITEL')." "; ?></strong><br />
                         <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[mail.cloaking]', 'class="inputbox"', $jlistConfig['mail.cloaking']); ?> 
                    </td>
                    <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_MAIL_SECURITY_DESC');?>
                    </td>
                  </tr>
                <tr><td colspan="2"><hr></td></tr>
                <tr>
                    <td width="330">
                        <strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_BLACKLIST_TITLE')." "; ?></strong><br />
                         <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.blocking.list]', 'class="inputbox"', $jlistConfig['use.blocking.list']); ?> 
                    </td>
                    <td>
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_BLACKLIST_DESC');?>
                    </td>
                  </tr>
                  <tr>
                     <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_BLACKLIST_TITLE')." "; ?></strong><br />
                             <textarea name="jlistConfig[blocking.list]" rows="15" cols="40"><?php echo $jlistConfig['blocking.list']; ?></textarea>
                     </td>
                     <td valign="top">
                              <?php echo JText::_('COM_JDOWNLOADS_BACKEND_BLACKLIST_DESC');?>
                     </td>
                   </tr>                
                   </table>
                   </td>
                   </tr>
                  </table>
              </td>
          </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_EMAIL'), 'email'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* E-Mail config */ ?>
            <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                  <tr>
                      <td valign="top" align="left" width="100%">
                       <table width="100%">
                          <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_HEAD')." "; ?></th>
                          </tr>
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_OPTION')." "; ?></strong><br />
                                <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[send.mailto.option]', 'class="inputbox"', $jlistConfig['send.mailto.option']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_OPTION_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_HTML')." "; ?></strong><br />
                                <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[send.mailto.html]', 'class="inputbox"', $jlistConfig['send.mailto.html']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_HTML_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO')." "; ?></strong><br />
                                <textarea name="jlistConfig[send.mailto]" rows="2" cols="40"><?php echo $jlistConfig['send.mailto']; ?></textarea> 
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_BETREFF')." "; ?></strong><br />
                                <input name="jlistConfig[send.mailto.betreff]" value="<?php echo htmlspecialchars($jlistConfig['send.mailto.betreff'], ENT_QUOTES ); ?>" size="50" maxlength="80"/></td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_BETREFF_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>
                        </tr>
                        
                        <tr>
                          <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_UPLOAD_TEMPLATE_TITLE')." "; ?></strong><br />
                              <textarea name="jlistConfig[send.mailto.template.download]" rows="10" cols="40"><?php echo htmlspecialchars($jlistConfig['send.mailto.template.download'], ENT_QUOTES ); ?></textarea>
                          </td>
                          <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_DOWNLOAD_TEMPLATE_DESC').'<br />'.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>
                        </tr>

                    </table>
                    </td>
                  </tr>
                  
                  <tr>
                      <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_UPLOAD_HEAD')." "; ?></th>
                  </tr>

                  <tr>
                      <td valign="top" align="left" width="100%">
                       <table width="100%">

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_OPTION')." "; ?></strong><br />
                                <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[send.mailto.option.upload]', 'class="inputbox"', $jlistConfig['send.mailto.option.upload']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_OPTION_UPLOAD_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_HTML')." "; ?></strong><br />
                                <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[send.mailto.html.upload]', 'class="inputbox"', $jlistConfig['send.mailto.html.upload']); ?> 
                        </td>
                        <td>
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_HTML_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO')." "; ?></strong><br />
                                <textarea name="jlistConfig[send.mailto.upload]" rows="2" cols="40"><?php echo $jlistConfig['send.mailto.upload']; ?></textarea> 
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_UPLOAD_DESC');?>
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_BETREFF')." "; ?></strong><br />
                                <input name="jlistConfig[send.mailto.betreff.upload]" value="<?php echo htmlspecialchars($jlistConfig['send.mailto.betreff.upload'], ENT_QUOTES ); ?>" size="50" maxlength="80"/></td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_BETREFF_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>
                        </tr>
                        <tr>
                          <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_UPLOAD_TEMPLATE_TITLE')." "; ?></strong><br />
                              <textarea name="jlistConfig[send.mailto.template.upload]" rows="10" cols="40"><?php echo htmlspecialchars($jlistConfig['send.mailto.template.upload'], ENT_QUOTES ); ?></textarea>
                          </td>
                          <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_UPLOAD_TEMPLATE_DESC').'<br />'.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>
                        </tr>                          
                          
                    </table>
                      </td>
                  </tr>

                  <tr>
                      <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_MAIL_REPORT_HEAD')." "; ?></th>
                  </tr>
                  
                  <tr>
                      <td valign="top" align="left" width="100%">
                       <table width="100%">
                                         
                        <tr>
                        <td colspan="2"><?php echo JText::_('COM_JDOWNLOADS_CONFIG_REPORT_FILE_INFO')." "; ?><br />
                        </td>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO')." "; ?></strong><br />
                                <textarea name="jlistConfig[send.mailto.report]" rows="2" cols="40"><?php echo htmlspecialchars($jlistConfig['send.mailto.report'], ENT_QUOTES ); ?></textarea> 
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_REPORT_FILE_MAIL_DESC');?>
                        </td>
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_BETREFF'); ?></strong><br />
                               <input name="jlistConfig[report.mail.subject]" value="<?php echo htmlspecialchars($jlistConfig['report.mail.subject'], ENT_QUOTES ); ?>" size="50" maxlength="100"/>
                        </td>
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_SEND_MAILTO_BETREFF_DESC').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>                        
                        </tr>
                        
                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_CONFIG_REPORT_FILE_MAIL_LAYOUT')." "; ?></strong><br />
                                <textarea name="jlistConfig[report.mail.layout]" rows="10" cols="40"><?php echo htmlspecialchars($jlistConfig['report.mail.layout'], ENT_QUOTES ); ?></textarea> 
                        <td valign="top">
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_CONFIG_REPORT_FILE_MAIL_LAYOUT_DESC').'<br />'.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                        </td>
                        </tr>

                </table>
                </td>
              </tr>
         </table>
      </td>
      </tr>
</table>
                
<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_SPECIALS'), 'specials'); ?>

<table width="100%" border="0">
    <tr>
        <td width="40%" valign="top">

<?php /* upload config */ ?>
           <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                <tr>
                    <td valign="top" align="left" width="100%">
                        <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ADSENSE_TITLE')." "; ?></th>
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ADSENSE_ACTIVATE_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[google.adsense.active]', 'class="inputbox"', $jlistConfig['google.adsense.active']); ?> 
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ADSENSE_ACTIVATE_DESC');?>
                        </td>                
                        </tr>

                        <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ADSENSE_CODE_TITLE')." "; ?></strong><br />
                                <textarea name="jlistConfig[google.adsense.code]" rows="8" cols="40"><?php echo htmlspecialchars($jlistConfig['google.adsense.code'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_ADSENSE_CODE_DESC');?>
                                </td>
                        </tr>
                        
                        </table>
                      </td>
                  </tr>
                </table>
       </td>
    </tr>
    <tr>
      <td>
         <table cellpadding="4" cellspacing="1" border="0" class="adminlist">
                <tr>
                  <td valign="top" align="left" width="100%">
                      <table width="100%">
                <tr>
                      <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_HEADER')." "; ?></th>
                </tr>      
                <tr><td colspan="2"><font color="#990000"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_HEADER_TEXT'); ?></font></td></tr>                      
                     <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.alphauserpoints]', 'class="inputbox"', $jlistConfig['use.alphauserpoints']); ?> 
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DESC');?>
                        </td>                
                      </tr>
                      <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DOWNLOAD_WHEN_ZERO_POINTS_TEXT')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[user.can.download.file.when.zero.points]', 'class="inputbox"', $jlistConfig['user.can.download.file.when.zero.points']); ?> 
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_DOWNLOAD_WHEN_ZERO_POINTS_DESC');?>
                        </td>                
                      </tr>
                      <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_FE_MESSAGE_NO_DOWNLOAD_EDIT')." "; ?></strong><br />
                                <textarea name="jlistConfig[user.message.when.zero.points]" rows="3" cols="40"><?php echo htmlspecialchars($jlistConfig['user.message.when.zero.points'], ENT_QUOTES ); ?></textarea>
                                </td>
                                <td valign="top"><br /><?php echo JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO_SHORT');?>
                                </td>
                      </tr>
                      <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_USE_FILE_PRICE_TITLE')." "; ?></strong><br />
                               <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[use.alphauserpoints.with.price.field]', 'class="inputbox"', $jlistConfig['use.alphauserpoints.with.price.field']); ?> 
                        </td>
                        <td>
                        <br />
                               <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_USE_FILE_PRICE_DESC');?>
                      </td>                
                      </tr>                                                
                      </table>     
                  </td>    
             </tr> 
                 
              <tr>
                 <td>

                  <tr>
                      <td valign="top" align="left" width="100%">
                       <table width="100%">
                        <tr>
                              <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_POST_COM_ID_TITLE')." "; ?></th>
                        </tr>                       
                     <?php
                     if ($jlistConfig['com'] == ''){ ?>   

                       <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_POST_COM_ID_TITLE2')." "; ?></strong><br />
                               <input name="com" value="" size="50" maxlength="50"/>
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_POST_COM_ID_DESC');?>
                        </td>
                        </tr>              
                     <?php } else { ?>
                       <tr>
                        <td>
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_POST_COM_ID_ACTIVE');?>
                        </td>
                        </tr>              
                     <?php } ?>
                        
                        </table>
                        </td>
                    </tr>
             </table>         
       </td>
    </tr>
</table>

<?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TABTEXT_PLUGINS'), 'plugins'); ?>

<table width="100%" border="0">
  <tr>
    <td width="40%" valign="top">

      <?php /* File Plugin config */ ?>
      <table cellpadding="4" cellspacing="1" border="0" class="adminlist">

         <tr>
           <td valign="top" align="left" width="50%">
              <table width="100%">
                 <tr>
                    <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_GLOBAL_FILEPLUGIN_HEAD'); ?></th>
                 </tr>
                <?php
                if (!isset($this->select_fields['file_plugin_inputbox'])){
                ?> <tr>
                     <td>
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_NOT_INSTALLED'); ?>
                     </td>
                   </tr>
                <?php
                } else {
                ?>
               <tr>
                <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_DEFAULTLAYOUT')." "; ?></strong><br />
                   <?php
                     echo( $this->select_fields['file_plugin_inputbox']);
                   ?>
                </td>
                <td>
                   <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_DEFAULTLAYOUT_DESC');?>
                </td>
               </tr>
               <tr>
               <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DETAILS_DOWNLOAD_BUTTON_TITLE')." "; ?></strong><br />
                  <?php echo $this->select_fields['inputbox_down_plg']; ?>     
                 <td>
                  
                </td>
                </tr>                            
                <tr>
                    <td valign="top">
                         <script language="javascript" type="text/javascript">
                          if (document.adminForm.down_pic_plg.options.value!=''){
                              jsimg="<?php echo JURI::root().'images/jdownloads/downloadimages/'; ?>" + getSelectedText( 'adminForm', 'down_pic_plg' );
                          } else {
                              jsimg='';
                          }
                          document.write('<img src=' + jsimg + ' name="imagelib10" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                          </script>
                     </td>
                </tr>
               <tr>
                <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_ENABLEPLUGIN')." "; ?></strong><br />
                   <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[fileplugin.enable_plugin]', 'class="inputbox"', $jlistConfig['fileplugin.enable_plugin']); ?> 
                </td>
                <td>
                   <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_ENABLEPLUGIN_DESC');?>
                </td>
               </tr>
               <tr>
                <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_SHOWDISABLED')." "; ?></strong><br />
                   <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[fileplugin.show_jdfiledisabled]', 'class="inputbox"', $jlistConfig['fileplugin.show_jdfiledisabled']); ?> 
                </td>
                <td>
                   <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_SHOWDISABLED_DESC');?>
                </td>
               </tr>
               <tr>
                <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_DOWNLOADTITLE')." "; ?></strong><br />
                   <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[fileplugin.show_downloadtitle]', 'class="inputbox"', $jlistConfig['fileplugin.show_downloadtitle']); ?> 
                </td>
                <td>
                   <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_DOWNLOADTITLE_DESC');?>
                </td>
               </tr>

               <tr>
                 <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_OFFLINETITLE')." "; ?></strong><br />
                     <textarea name="jlistConfig[fileplugin.offline_title]" rows="3" cols="40"><?php echo htmlspecialchars($jlistConfig['fileplugin.offline_title'], ENT_QUOTES ); ?></textarea>
                 </td>
                 <td valign="top"><br />
                    <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_OFFLINETITLE_DESC');?>
                 </td>
               </tr>

               <tr>
                 <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_OFFLINEDESC')." "; ?></strong><br />
                     <textarea name="jlistConfig[fileplugin.offline_descr]" rows="3" cols="40"><?php echo htmlspecialchars($jlistConfig['fileplugin.offline_descr'], ENT_QUOTES ); ?></textarea>
                 </td>
                 <td valign="top"><br />
                    <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FILEPLUGIN_OFFLINEDESC_DESC');?>
                 </td>
               </tr>
               <tr><td colspan="2"><hr></td></tr>
               <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_CUT_DESCRIPTION_TITLE')." "; ?></strong><br />
                            <?php echo JDownloadsHelper::yesnoSelectList( 'jlistConfig[plugin.auto.file.short.description]', 'class="inputbox"', $jlistConfig['plugin.auto.file.short.description']); ?> 
                        </td>
                        <td>
                            <br />
                            <?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_CUT_DESCRIPTION_TITLE_DESC');?>
                        </td>
               </tr>                        
               <tr>
                        <td width="330"><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_USE_CUT_DESCRIPTION_LENGTH_TITLE')." "; ?></strong><br />
                                <input name="jlistConfig[plugin.auto.file.short.description.value]" value="<?php echo $jlistConfig['plugin.auto.file.short.description.value']; ?>" size="10" maxlength="10"/></td>
                        <td>
                        <br />
                        <?php echo '';?>
                        </td>
               </tr> 

                <?php
                }
                ?>

              </table>
           </td>
         </tr>
      </table>
    </td>
  </tr>
</table>

<?php echo JHtml::_('tabs.end'); ?>

    <input type="hidden" name="root_dir" value="<?php echo $jlistConfig['files.uploaddir'];?>" />    
    <input type="hidden" name="uncat_dir" value="<?php echo $jlistConfig['uncategorised.files.folder.name'];?>" />
    <input type="hidden" name="preview_dir" value="<?php echo $jlistConfig['preview.files.folder.name'];?>" />
    <input type="hidden" name="private_dir" value="<?php echo $jlistConfig['private.area.folder.name'];?>" />
    <input type="hidden" name="temp_dir" value="<?php echo $jlistConfig['tempzipfiles.folder.name'];?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="config" />
    <?php echo JHtml::_('form.token'); ?> 
    </form>

<?php
   } else {
?>           
    <form action="index.php" method="post" name="adminForm" id="adminForm">
    <div>

            <div class="jdwarning">
                 <?php echo '<b>'.JText::_('COM_JDOWNLOADS_ALERTNOAUTHOR').'</b>'; ?>
            </div>

    </div>
    <?php echo JHtml::_('form.token'); ?>
    </form>           
<?php
   }    
?>
