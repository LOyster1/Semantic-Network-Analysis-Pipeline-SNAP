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

defined('_JEXEC') or die;

global $jlistConfig;

$ini_upload_max_filesize = JDownloadsHelper::return_bytes(ini_get('upload_max_filesize'));

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
JHtml::_('behavior.keepalive');

$admin_images_folder = JURI::root().'administrator/components/com_jdownloads/assets/images/';

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'download.cancel' || document.formvalidator.isValid(document.id('download-form'))) {
            Joomla.submitform(task, document.getElementById('download-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('COM_JDOWNLOADS_VALIDATION_FORM_FAILED'));?>');
        }
    }
    
    // get the selected file name to view the file type pic new
    function getSelectedText( frmName, srcListName ) 
    {
        var form = eval( 'document.' + frmName );
        var srcList = eval( 'form.' + srcListName );

        i = srcList.selectedIndex;
        if (i != null && i > -1) {
            return srcList.options[i].text;
        } else {
            return null;
        }
    }
    
    function editFilename(){
         document.getElementById('jform_url_download').readOnly = false;
         document.getElementById('jform_url_download').focus();
    }

    function editFilenamePreview(){
         document.getElementById('jform_preview_filename').readOnly = false;
         document.getElementById('jform_preview_filename').focus();
    }    
   
</script>

<style type="text/css">
    #displayimages li {
        float: left;
        padding: 0;
        margin: 0;
        width: 107px;
        position: relative;
        overflow: hidden;
        height: 127px;
    }
</style>


<form accept-charset="utf-8" action="<?php echo JRoute::_('index.php?option=com_jdownloads&layout=edit&file_id='.(int) $this->item->file_id); ?>" method="post" name="adminForm" id="download-form" enctype="multipart/form-data" class="form-validate">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ($ini_upload_max_filesize); ?>" />
    <?php if (isset($this->selected_filename) && $this->selected_filename != ''){ ?>
        <div class="jdlists-header-info"><?php echo '<img align="left" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="22" height="22" border="0" alt="" />&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_NOTE_FILE_SELECTED_IN_LIST'); ?> </div>
        <div class="clr"> </div> 
    <?php } ?>    
    
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo empty($this->item->file_id) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_ADD') : JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESEDIT_EDIT', $this->item->file_id); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('file_title'); ?>
                <?php echo $this->form->getInput('file_title'); ?></li>

                <li><?php echo $this->form->getLabel('file_alias'); ?>
                <?php echo $this->form->getInput('file_alias'); ?></li>

                <li><?php echo $this->form->getLabel('release'); ?>
                <?php echo $this->form->getInput('release'); ?></li>
                             
                <li><?php echo $this->form->getLabel('cat_id'); ?>
                <?php echo $this->form->getInput('cat_id'); ?></li>

                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>

                <li><?php echo $this->form->getLabel('featured'); ?>
                <?php echo $this->form->getInput('featured'); ?></li>
                
                <li><?php echo $this->form->getLabel('access'); ?>
                <?php echo $this->form->getInput('access'); ?></li>
                
                <?php if ($this->canDo->get('core.admin')): ?>
                    <li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
                        <div class="button2-left"><div class="blank">
                            <button type="button" onclick="document.location.href='#access-rules';">
                                <?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
                            </button>
                        </div></div>
                    </li>
                <?php endif; ?>                
                                
                <!--<li><?php echo $this->form->getLabel('ordering'); ?>
                <?php echo $this->form->getInput('ordering'); ?></li>
                -->

                <li><?php echo $this->form->getLabel('tags'); ?>
                <?php echo $this->form->getInput('tags'); ?></li>                
                
                <li><?php echo $this->form->getLabel('language'); ?>
                <?php echo $this->form->getInput('language'); ?></li>
                
                <li><?php echo $this->form->getLabel('file_language'); ?>
                <?php echo $this->form->getInput('file_language'); ?></li>                

                <li><?php echo $this->form->getLabel('license'); ?>
                <?php echo $this->form->getInput('license'); ?></li>

                <li><?php echo $this->form->getLabel('license_agree'); ?>
                <?php echo $this->form->getInput('license_agree'); ?></li>                                                

                <li><?php echo $this->form->getLabel('system'); ?>
                <?php echo $this->form->getInput('system'); ?></li>

                <li><?php echo $this->form->getLabel('update_active'); ?>
                <?php echo $this->form->getInput('update_active'); ?></li>

                <li><?php echo $this->form->getLabel('file_id'); ?>
                <?php echo $this->form->getInput('file_id'); ?></li>
            </ul>

            <div>
                <b><?php echo $this->form->getLabel('description'); ?></b>
                 <?php 
                 if (!$jlistConfig['files.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'description', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'description', 'rows', '10' );
                     $this->form->setFieldAttribute( 'description', 'cols', '80' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('description'); 
                 ?>       
            </div>
            <div class="clr"></div>
            <div>
                <b><?php echo $this->form->getLabel('description_long'); ?></b>
                 <?php 
                 if (!$jlistConfig['files.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'description_long', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'description_long', 'rows', '10' );
                     $this->form->setFieldAttribute( 'description_long', 'cols', '80' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('description_long'); 
                 ?>       
            </div>                

            <div>
                <b><?php echo $this->form->getLabel('changelog'); ?></b>
                 <?php 
                 if (!$jlistConfig['files.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'changelog', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'changelog', 'rows', '10' );
                     $this->form->setFieldAttribute( 'changelog', 'cols', '80' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('changelog'); 
                 ?>       
            </div>            
        </fieldset>
    </div>                

    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start','download-sliders-'.$this->item->file_id, array('useCookie'=>1)); ?> 

        <!-- publishing details -->
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_PUBLISHING_DETAILS'), 'publishing-details'); ?>
        
        <?php 
        /* if ($this->form->created_mail) { 
            // download was sent in from frontend
            echo '<font color="#666666"><small>'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_SENT_IN_FROM').'<font color="#990000">'.$this->row->created_by.'</font> '.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_SENT_IN_FROM_EMAIL').'<font color="#990000">'.$this->row->created_mail.'</font></small></font>';
        } */ ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('created_id'); ?>
                <?php echo $this->form->getInput('created_id'); ?></li>

                 <?php if ($this->item->file_id) : ?>
                <li><?php echo $this->form->getLabel('date_added'); ?>
                <?php echo $this->form->getInput('date_added'); ?></li>
                <?php endif; ?>
                
                <?php if ($this->item->modified_id) : ?>
                    <li><?php echo $this->form->getLabel('modified_id'); ?>
                    <?php echo $this->form->getInput('modified_id'); ?></li>

                    <li><?php echo $this->form->getLabel('modified_date'); ?>
                    <?php echo $this->form->getInput('modified_date'); ?></li>
                <?php endif; ?>

                    <li><?php echo $this->form->getLabel('use_timeframe'); ?>
                    <?php echo $this->form->getInput('use_timeframe'); ?></li>

                    <li><?php echo $this->form->getLabel('publish_from'); ?>
                    <?php echo $this->form->getInput('publish_from'); ?></li>                

                    <li><?php echo $this->form->getLabel('publish_to'); ?>
                    <?php echo $this->form->getInput('publish_to'); ?></li>                 

                    <li><?php echo $this->form->getLabel('views'); ?>
                    <?php echo $this->form->getInput('views'); ?></li>

                    <li><?php echo $this->form->getLabel('downloads'); ?>
                    <?php echo $this->form->getInput('downloads'); ?></li>                    

            </ul>
        </fieldset>         

        <!-- files data -->        
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_TABTITLE_3'), 'files-data'); ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">        

            <?php 
            if ($this->item->url_download != "") : ?>
                <li>
                    <?php echo $this->form->getLabel('url_download'); ?>
                    <?php echo $this->form->getInput('url_download'); ?>
                <span>
                <?php 
                     echo '<a href="index.php?option=com_jdownloads&amp;task=download.download&amp;id='.$this->item->file_id.'" target="_blank"><img src="'.$admin_images_folder.'download.png'.'" width="18px" height="18px" border="0" style="vertical-align:middle;" alt="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_DOWNLOAD').'" title="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_DOWNLOAD').'" /></a>&nbsp;';
                     ?>
                      <input type="button" value="" class="button_rename" title="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_RENAME'); ?>" name="activateFileNameField" onClick="editFilename();" >
                <?php 
                     echo ' <a href="index.php?option=com_jdownloads&amp;task=download.delete&amp;id='.$this->item->file_id.'"><img src="'.$admin_images_folder.'delete.png'.'" width="18px" height="18px" border="0" style="vertical-align:middle;" alt="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_REMOVE').'" title="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_REMOVE').'" /></a>';
                     ?>    
                </span>
                </li>            
            <?php 
                // url_download is not empty, so the external link field must be set to readonly
                $this->form->setFieldAttribute( 'extern_file', 'readonly', 'true' );
                $this->form->setFieldAttribute( 'extern_file', 'class="inputbox"', 'class="readonly"' );
            endif; ?>
                        
                <li><?php echo $this->form->getLabel('file_upload'); ?>
                <?php echo $this->form->getInput('file_upload'); ?></li>
                <li><?php echo '<small>'.JText::_('COM_JDOWNLOADS_UPLOAD_MAX_FILESIZE_INFO_TITLE').' '.($ini_upload_max_filesize / 1024).' KB</small>'; ?></li>

                <li><?php echo $this->form->getLabel('other_file_id'); ?>
                <?php echo $this->form->getInput('other_file_id'); ?>
                            <div class="button2-left"><div class="blank">
                            <!-- add remove button -->
                            <button type="button" onclick="document.getElementById('jform_other_file_id_name').value = ''; document.getElementById('jform_other_file_id_id').value = ''; document.getElementById('jform_size').value = ''; document.getElementById('jform_file_date').value = ''; document.getElementById('jform_md5_value').value = ''; document.getElementById('jform_sha1_value').value = '';">
                                <?php echo JText::_('COM_JDOWNLOADS_REMOVE'); ?>
                            </button>
                            </div></div>
                </li>
                
                <?php 
                // 
                if (isset($this->selected_filename) && $this->selected_filename != ''){
                    $this->form->setFieldAttribute( 'update_file', 'default', $this->selected_filename );
                } 
                ?>
                
                <li><?php echo $this->form->getLabel('update_file'); ?>
                <?php echo $this->form->getInput('update_file'); ?></li>        

                <li><?php echo $this->form->getLabel('use_xml'); ?>
                <?php echo $this->form->getInput('use_xml'); ?></li>
        
                <li><?php echo $this->form->getLabel('size'); ?>
                <?php echo $this->form->getInput('size'); ?></li>
                
                <li><?php echo $this->form->getLabel('file_date'); ?>
                <?php echo $this->form->getInput('file_date'); ?></li>

                <?php if ($this->item->url_download != "") : ?>
                    <li><?php echo $this->form->getLabel('md5_value'); ?>
                        <?php echo $this->form->getInput('md5_value'); ?></li>
                    <li><?php echo $this->form->getLabel('sha1_value'); ?>
                        <?php echo $this->form->getInput('sha1_value'); ?></li>
                <?php endif; ?> 
                                
                <li><?php echo $this->form->getLabel('spacer'); ?></li>

                <!-- for preview file -->
                <?php 
                if ($this->item->preview_filename != "") : ?>
                    <li>
                        <?php echo $this->form->getLabel('preview_filename'); ?>
                        <?php echo $this->form->getInput('preview_filename'); ?>
                    <span>
                    <?php 
                         echo '<a href="index.php?option=com_jdownloads&amp;task=download.download&amp;id='.$this->item->file_id.'&amp;type=prev" target="_blank"><img src="'.$admin_images_folder.'download.png'.'" width="18px" height="18px" border="0" style="vertical-align:middle;" alt="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_DOWNLOAD').'" title="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_DOWNLOAD').'" /></a>&nbsp;';
                         ?>
                          <input type="button" value="" class="button_rename" title="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_RENAME'); ?>" name="activateFilePrevNameField" onClick="editFilenamePreview();" >
                    <?php 
                         echo ' <a href="index.php?option=com_jdownloads&amp;task=download.delete&amp;id='.$this->item->file_id.'&amp;type=prev"><img src="'.$admin_images_folder.'delete.png'.'" width="18px" height="18px" border="0" style="vertical-align:middle;" alt="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_REMOVE').'" title="'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FILE_REMOVE').'" /></a>';
                         ?>    
                    </span>
                    </li>            
                <?php 
                endif; ?>
                            
                <li><?php echo $this->form->getLabel('preview_file_upload'); ?>
                <?php echo $this->form->getInput('preview_file_upload'); ?></li>
                <li><?php echo '<small>'.JText::_('COM_JDOWNLOADS_UPLOAD_MAX_FILESIZE_INFO_TITLE').' '.($ini_upload_max_filesize / 1024).' KB</small>'; ?></li>                

                <li><?php echo $this->form->getLabel('spacer'); ?></li>
                
                <li><?php echo $this->form->getLabel('extern_file'); ?>
                <?php echo $this->form->getInput('extern_file'); ?></li>
                
                <li><?php echo $this->form->getLabel('extern_site'); ?>
                <?php echo $this->form->getInput('extern_site'); ?></li>                

                <li><?php echo $this->form->getLabel('spacer'); ?></li>                                
                
                <li><?php echo $this->form->getLabel('mirror_1'); ?>
                <?php echo $this->form->getInput('mirror_1'); ?></li>
                
                <li><?php echo $this->form->getLabel('extern_site_mirror_1'); ?>
                <?php echo $this->form->getInput('extern_site_mirror_1'); ?></li>

                <li><?php echo $this->form->getLabel('mirror_2'); ?>
                <?php echo $this->form->getInput('mirror_2'); ?></li>
                
                <li><?php echo $this->form->getLabel('extern_site_mirror_1'); ?>
                <?php echo $this->form->getInput('extern_site_mirror_2'); ?></li>

            </ul>        
        </fieldset>        
        
        <!-- additional data -->
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_ADDITIONAL_DATA'), 'additional-data'); ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">

                <li><?php echo $this->form->getLabel('file_pic'); ?>
                <?php echo $this->form->getInput('file_pic'); ?></li>
                
                <li><label></label>
                <script language="javascript" type="text/javascript">
                    if (document.adminForm.file_pic.options.value!=''){
                        jsimg="<?php echo JURI::root().'images/jdownloads/fileimages/'; ?>" + getSelectedText( 'adminForm', 'file_pic' );
                    } else {
                        jsimg='';
                    }
                    document.write('<img src=' + jsimg + ' name="imagelib" width="<?php echo $jlistConfig['file.pic.size']; ?>" height="<?php echo $jlistConfig['file.pic.size']; ?>" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                </script></li>

                <li><?php echo $this->form->getLabel('picnew'); ?> 
                <input name="picnew" size="30"  type="file"/>
                </li>
                
                 <li><?php echo $this->form->getLabel('spacer'); ?></li>

                <li><?php echo $this->form->getLabel('price'); ?>
               <?php echo $this->form->getInput('price'); ?></li>

                <li><?php echo $this->form->getLabel('password'); ?>
               <?php echo $this->form->getInput('password'); ?></li>

                <li><?php echo $this->form->getLabel('notes'); ?>
                <?php echo $this->form->getInput('notes'); ?></li>
            </ul>
        </fieldset>

        <!-- custom data fields -->
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_BACKEND_SET_TAB_ADD_FIELDS_TITLE'), 'custom-data-fields'); ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">
                <?php
                 if ($jlistConfig['custom.field.1.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_1'); ?>
                        <?php echo $this->form->getInput('custom_field_1'); ?>
                    </li>
                <?php } ?>

                <?php
                 if ($jlistConfig['custom.field.2.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_2'); ?>
                        <?php echo $this->form->getInput('custom_field_2'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.3.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_3'); ?>
                        <?php echo $this->form->getInput('custom_field_3'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.4.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_4'); ?>
                        <?php echo $this->form->getInput('custom_field_4'); ?>
                    </li>
                <?php } ?>
                

                                            <?php
                 if ($jlistConfig['custom.field.5.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_5'); ?>
                        <?php echo $this->form->getInput('custom_field_5'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.6.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_6'); ?>
                        <?php echo $this->form->getInput('custom_field_6'); ?>
                    </li>
                <?php } ?>
                
                                                <?php
                 if ($jlistConfig['custom.field.7.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_7'); ?>
                        <?php echo $this->form->getInput('custom_field_7'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.8.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_8'); ?>
                        <?php echo $this->form->getInput('custom_field_8'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.9.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_9'); ?>
                        <?php echo $this->form->getInput('custom_field_9'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.10.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_10'); ?>
                        <?php echo $this->form->getInput('custom_field_10'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.11.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_11'); ?>
                        <?php echo $this->form->getInput('custom_field_11'); ?>
                    </li>
                <?php } ?>
                
                <?php
                 if ($jlistConfig['custom.field.12.title'] != ''){ ?>
                    <li>
                        <?php echo $this->form->getLabel('custom_field_12'); ?>
                        <?php echo $this->form->getInput('custom_field_12'); ?>
                    </li>
                <?php } ?>
                                                                                    
            </ul>
            <?php if ($jlistConfig['custom.field.13.title'] != ''){ ?>
                    <div>
                        <b><?php echo $this->form->getLabel('custom_field_13'); ?></b>
                         <?php 
                         if (!$jlistConfig['files.editor']){ 
                             // use a simple textarea instead editor
                             $this->form->setFieldAttribute( 'custom_field_13', 'type', 'textarea' );
                             $this->form->setFieldAttribute( 'custom_field_13', 'rows', '8' );
                             $this->form->setFieldAttribute( 'custom_field_13', 'cols', '35' );
                         } else {
                             ?> <div class="clr"></div> <?php
                         }
                         echo $this->form->getInput('custom_field_13'); 
                         ?>       
                    </div>
            <?php } ?>
                    
            <?php if ($jlistConfig['custom.field.14.title'] != ''){ ?>
                    <div>
                        <b><?php echo $this->form->getLabel('custom_field_14'); ?></b>
                         <?php 
                         if (!$jlistConfig['files.editor']){ 
                             // use a simple textarea instead editor
                             $this->form->setFieldAttribute( 'custom_field_14', 'type', 'textarea' );
                             $this->form->setFieldAttribute( 'custom_field_14', 'rows', '8' );
                             $this->form->setFieldAttribute( 'custom_field_14', 'cols', '35' );
                         } else {
                             ?> <div class="clr"></div> <?php
                         }
                         echo $this->form->getInput('custom_field_14'); 
                         ?>       
                    </div>
            <?php } ?>                    
        </fieldset>                
        
        <!-- author data -->        
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_AUTHOR_INFOS_TITLE'), 'authors-data'); ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">        
                <li><?php echo $this->form->getLabel('url_home'); ?>
                <?php echo $this->form->getInput('url_home'); ?></li>
                
                <li><?php echo $this->form->getLabel('author'); ?>
                <?php echo $this->form->getInput('author'); ?></li>

                <li><?php echo $this->form->getLabel('url_author'); ?>
                <?php echo $this->form->getInput('url_author'); ?></li>
            </ul>        
        </fieldset>                 

        <!-- files data -->        
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_TABTITLE_4'), 'images-data'); ?>
        
        <fieldset class="panelform">
            <?php $image_id = 0; ?>
            <?php if ($this->item->images){ ?>    
                <table class="admintable" width="100%" border="0" cellpadding="0" cellspacing="10">
                <tr><td><?php if ($this->item->images) echo JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_REMOVE'); ?></td></tr>
                <tr>
                <td valign="top">
                <?php 
                // display the selected images
                
                if ($this->item->images){
                    $images = array();
                    $images = explode("|", $this->item->images);
                    echo '<ul style="list-style-type: none; margin: 0px 0 0 0; padding: 0; width: 350px; overflow: visible;" id="displayimages">';
                    foreach ($images as $image){
                         $image_id ++;
                         echo '<li id="'.$image.'">';
                         echo '<input style="position:relative;
                                left: 7px;
                                top: 15px;
                                vertical-align: top;
                                z-index: 1;
                                margin: 0;
                                padding: 0;" type="checkbox" name="keep_image['.$image_id.']" value="'.$image.'" checked />';
                         echo '<a href="'.JURI::root().'images/jdownloads/screenshots/'.$image.'" target="_blank">';
                         
                         echo '<img border="0" style="position:relative;border:1px solid black; max-width:100px; max-height:100px;" align="middle" src="'.JURI::root().'images/jdownloads/screenshots/thumbnails/'.$image.'" alt="'.$image.'" title="'.$image.'" />';
                         echo '</a>';
                         echo '</li>';                         
                    }
                    echo '</ul>'; 
                }
                ?>
                </td>
                </tr>
                </table>                
            <?php } ?>
                 
            <?php 
            if ($image_id < (int)$jlistConfig['be.upload.amount.of.pictures']){ ?>
                <ul class="adminformlist">                    
                     <li><label>
                     <?php  echo JHtml::_('tooltip', JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_UPLOAD_DESC'), JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_UPLOAD_TITLE').'<br /><small>'.JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_UPLOAD_DESC_LIMIT', $jlistConfig['be.upload.amount.of.pictures']).'</small>', '', JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_UPLOAD_TITLE').'<br /><small>'.JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_UPLOAD_DESC_LIMIT', $jlistConfig['be.upload.amount.of.pictures']).'</small>' ); ?>
                     </label>
                     </li>
                </ul> 
                    <table id="files_table" class="admintable" border="0" cellpadding="0" cellspacing="10">
                    <tr id="new_file_row">
                    <td class=""><input type="file" name="file_upload_thumb[0]" id="file_upload_thumb[0]" size="40" accept="image/gif,image/jpeg,image/jpg,image/png" onchange="add_new_image_file(this)" />
                    </td>
                    </tr>
                    <tr><td><?php echo '<small>'.JText::_('COM_JDOWNLOADS_UPLOAD_MAX_FILESIZE_INFO_TITLE').' '.($ini_upload_max_filesize / 1024).' KB</small>'; ?></td></tr>
                    </table> 
             <?php
             } else { 
                    // limit is reached - display a info message 
                    echo '<p>'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_THUMBNAIL_LIMIT_REACHED').'</p>'; 
             }?>
         </fieldset>
            
        <!-- meta data -->        
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_METADATA_OPTIONS'), 'meta-data'); ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('metadesc'); ?>
                    <?php echo $this->form->getInput('metadesc'); ?></li>

                    <li><?php echo $this->form->getLabel('metakey'); ?>
                    <?php echo $this->form->getInput('metakey'); ?></li>
                    
                    <li><?php echo $this->form->getLabel('robots'); ?>
                    <?php echo $this->form->getInput('robots'); ?></li>
            </ul>
        </fieldset>        

        <?php echo JHtml::_('sliders.end'); ?>
        </div>
        
        <!-- begin ACL definition-->
        <div class="clr"></div>
        <div>
            <?php if ($this->canDo->get('core.admin')): ?>
                  <?php 
                  if (empty($this->item->file_id)){ ?>
                        <div class="jdwarning"><?php echo JText::_('COM_JDOWNLOADS_SET_CREATE_PERMISSIONS_WARNING'); ?></div>                  
                  <?php } ?>                  
                  <div class="width-100 fltlft">
                       <?php echo JHtml::_('sliders.start', 'jd-download-permissions-sliders-'.$this->item->file_id, array('useCookie'=>1)); ?>
                       <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_DOWNLOAD_RULES').' : '. $this->form->getValue('file_title'), 'access-rules'); ?>                        
                       <fieldset class="panelform">
                           <?php echo $this->form->getLabel('rules'); ?>
                           <?php echo $this->form->getInput('rules'); ?>
                       </fieldset>
                       <?php echo JHtml::_('sliders.end'); ?>
                  </div>
            <?php endif; ?>
        <!-- end ACL definition--> 
    <div>
        
        
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="download" />
        <input type="hidden" name="image_file_count" id="image_file_count" value="0" />         
        <input type="hidden" name="cat_dir_org" value="<?php echo $this->item->cat_id; ?>" />
        <input type="hidden" name="sum_listed_images" id="sum_listed_images" value="<?php echo (int)$image_id; ?>" />
        <input type="hidden" name="max_sum_images" id="max_sum_images" value="<?php echo (int)$jlistConfig['be.upload.amount.of.pictures']; ?>" /> 
        
        <!-- <input type="hidden" name="update_active" value="<?php echo $this->item->update_active;?>"/> 
        <input type="hidden" name="catid" value="<?php if ($this->item->cat_id) {echo $this->item->cat_id;}else{echo $cat_id;} ?>" /> -->
        
        <input type="hidden" name="filename" value="<?php echo $this->item->url_download; ?>" />        
        <input type="hidden" name="modified_date_old" value="<?php echo $this->item->modified_date; ?>" />
        <input type="hidden" name="submitted_by" value="<?php echo $this->item->submitted_by; ?>" />
        <input type="hidden" name="set_aup_points" value="<?php echo $this->item->set_aup_points; ?>" />
        <input type="hidden" name="filename_org" value="<?php echo $this->item->url_download; ?>" />          
        <input type="hidden" name="preview_filename_org" value="<?php echo $this->item->preview_filename; ?>" /> 
        <?php echo JHtml::_('form.token'); ?>
    
    </div>
    <div class="clr"></div>    
</form>    
    