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
 
defined('_JEXEC') or die('Restricted access');

global $jlistConfig;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4

jimport( 'joomla.html.html.tabs' );

// Create shortcuts
$params = $this->state->get('params');
$rules = $this->get('user_rules');
$limits = $this->get('user_limits');

    if (!is_null($this->item->file_id)){
        $new = false;  
    } else {
        $new = true;  
    }
    
    // This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
    $editoroptions = isset($params->show_publishing_options);
    if (!$editoroptions):
	    $params->show_urls_images_frontend = '0';
    endif;
    ?>

    <script type="text/javascript">
	    Joomla.submitbutton = function(task) {
		    if (task == 'download.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			    Joomla.submitform(task);
		    } else {
			    alert('<?php echo $this->escape(htmlspecialchars(JText::_('COM_JDOWNLOADS_VALIDATION_FORM_FAILED'), ENT_QUOTES, 'UTF-8'));?>');
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
    
<div class="edit jd-item-page<?php echo $this->pageclass_sfx; ?>">

    <?php if ($params->get('show_page_heading')) : ?>
        <h1>
	        <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php 
    if ($rules->uploads_form_text){
        echo JDHelper::getOnlyLanguageSubstring($rules->uploads_form_text);
    } ?> 
    
    <form action="<?php echo JRoute::_('index.php?option=com_jdownloads&a_id='.(int) $this->item->file_id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data" accept-charset="utf-8">
            
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ($rules->uploads_maxfilesize_kb * 1024); ?>" />
            
        <fieldset class=jd_fieldset_outer>
                 
                <div class="formelm-buttons">

                <button type="button" onclick="Joomla.submitbutton('download.save')">
                    <?php echo JText::_('COM_JDOWNLOADS_SAVE') ?>
                </button>

                <button type="button" onclick="Joomla.submitbutton('download.cancel')">
                    <?php echo JText::_('COM_JDOWNLOADS_CANCEL') ?>
                </button>

                <?php if (!$new && ($this->item->params->get('access-delete') == true)){ ?>
                    <button type="button" onclick="Joomla.submitbutton('download.delete')">
                        <?php echo JText::_('COM_JDOWNLOADS_DELETE') ?>
                    </button>
                <?php } ?>
                </div>
                
                <span class="jd-upload-form-hint"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_FIELD_INFO') ?></span>
                
		    <legend>
             <?php if (!$new){ ?> 
                <?php echo JText::_('COM_JDOWNLOADS_EDIT_DOWNLOAD'); ?>
             <?php } else { ?>
                <?php echo JText::_('COM_JDOWNLOADS_ADD_NEW_DOWNLOAD'); ?>
             <?php } ?>                
             </legend>
                
			    <div class="formelm">
			        <?php echo $this->form->getLabel('file_title'); ?>
			        <?php echo $this->form->getInput('file_title'); ?>
			    </div>

            <?php if ($rules->form_alias):?>
                <?php if ($new):?>
			        <div class="formelm">
			            <?php echo $this->form->getLabel('file_alias'); ?>
			            <?php echo $this->form->getInput('file_alias'); ?>
			        </div>
		        <?php endif; ?>
            <?php endif; ?>                
            
            <?php if ($rules->form_version):?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('release'); ?>
                    <?php echo $this->form->getInput('release'); ?>
                </div>
            <?php endif; ?>
                        
            <?php if ($rules->form_update_active):?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('update_active'); ?>
                    <?php echo $this->form->getInput('update_active'); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($rules->form_file_language || $rules->form_file_system):?>
            
                <?php if ($rules->form_file_language):?>
                    <div class="formelm">
                        <?php echo $this->form->getLabel('file_language'); ?>
                        <?php echo $this->form->getInput('file_language'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($rules->form_file_system):?>            
                    <div class="formelm">
                        <?php echo $this->form->getLabel('system'); ?>
                        <?php echo $this->form->getInput('system'); ?>
                    </div>
                <?php endif; ?>
                      
            <?php endif; ?>
            
            <?php if ($rules->form_license):?>             
                <div class="formelm">
                    <?php echo $this->form->getLabel('license'); ?>
                    <?php echo $this->form->getInput('license'); ?>
                </div>                        
            <?php endif; ?>

            <?php if ($rules->form_confirm_license):?>                         
                <div class="formelm">
                    <?php echo $this->form->getLabel('license_agree'); ?>
                    <?php echo $this->form->getInput('license_agree'); ?>
                </div>                        
            <?php endif; ?>
                                    
       </fieldset>      
       
       <?php
       if ($rules->uploads_use_tabs) {
       		echo JHtml::_('tabs.start', 'jdlayout-sliders-'.$this->item->file_id, array('useCookie'=>1));
       } 
       ?>      
      
<!-- Publishing TAB -->
    
       <?php
           if ($rules->uploads_use_tabs){
             echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_PUBLISHING'),'publishing');
           }
      ?>                       
        
        <fieldset class="jd_fieldset_outer">
            <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_PUBLISHING'); ?></legend>

                <div class="formelm">
                    <?php echo $this->form->getLabel('cat_id'); ?>
                    <span class="category">
                    <?php echo $this->form->getInput('cat_id'); ?>
                    </span>
                </div>
           
            <?php if ($this->item->params->get('access-change') || $this->item->params->get('access-create') || $this->item->params->get('access-edit')): ?>
                <?php if ($rules->form_access):?>
                    <div class="formelm">
                        <?php echo $this->form->getLabel('access'); ?>
                        <?php echo $this->form->getInput('access'); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>                
            
            <?php 
            if ($rules->form_tags):?>                        
                <div class="formelm_tags">
                    <?php echo $this->form->getLabel('tags'); ?>
                    <?php echo $this->form->getInput('tags'); ?>
                </div>              
            <?php endif; ?>                            

            <?php if ($rules->form_language):?>                        
                <div class="formelm">
                    <?php echo $this->form->getLabel('language'); ?>
                    <?php echo $this->form->getInput('language'); ?>
                </div>
            <?php endif; ?>            

            <?php if ($this->item->params->get('access-change') || $this->item->params->get('access-create') || $this->item->params->get('access-edit')): ?>
                <?php if ($rules->form_published):?>
                    <div class="formelm">
                        <?php echo $this->form->getLabel('published'); ?>
                        <?php echo $this->form->getInput('published'); ?>
                    </div>
                <?php endif; ?>            
            <?php endif; ?>
            
            <?php if ($this->item->params->get('access-change') || $this->item->params->get('access-create') || $this->item->params->get('access-edit')): ?>
                <?php if ($rules->form_featured):?>
                    <div class="formelm">
                        <?php echo $this->form->getLabel('featured'); ?>
                        <?php echo $this->form->getInput('featured'); ?>
                    </div>
                <?php endif; ?>            
            <?php endif; ?>                         
            
            <?php if ($rules->form_creation_date):?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('date_added'); ?>
                    <?php echo $this->form->getInput('date_added'); ?>
                </div>
            <?php endif; ?>            

            <?php if ($rules->form_modified_date):?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('modified_date'); ?>
                    <?php echo $this->form->getInput('modified_date'); ?>
                </div>
            <?php endif; ?>            
            
        <?php if ($this->item->params->get('access-change') || $this->item->params->get('access-create') || $this->item->params->get('access-edit')): ?>
                    
            <?php if ($rules->form_timeframe):?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('use_timeframe'); ?>
                    <?php echo $this->form->getInput('use_timeframe'); ?>
                </div>

                <div class="formelm">
                    <?php echo $this->form->getLabel('publish_from'); ?>
                    <?php echo $this->form->getInput('publish_from'); ?>
                </div>
                
                <div class="formelm">
                    <?php echo $this->form->getLabel('publish_to'); ?>
                    <?php echo $this->form->getInput('publish_to'); ?>
                </div>
                 
            <?php endif; ?>            

            <?php if ($rules->form_views):?>                
                <div class="formelm">
                    <?php echo $this->form->getLabel('views'); ?>
                    <?php echo $this->form->getInput('views'); ?>
                </div>
            <?php endif; ?>            

            <?php if ($rules->form_downloaded):?>            
                <div class="formelm">
                    <?php echo $this->form->getLabel('downloads'); ?>
                    <?php echo $this->form->getInput('downloads'); ?>
                </div>
            <?php endif; ?>            
                                    
        <?php endif; ?>

            <?php if ($rules->form_ordering):?> 
                <?php if ($new){?>
                    <div class="form-note">
                          <p><?php echo JText::_('COM_JDOWNLOADS_FORM_ORDERING'); ?></p>
                    </div>
                <?php } else { ?>
                    <div class="formelm">
                        <?php echo $this->form->getLabel('ordering'); ?>
                        <?php echo $this->form->getInput('ordering'); ?>
                    </div>
                <?php } ?>
            <?php endif; ?>
        </fieldset>      
      
<!-- Files TAB -->
      <?php
       if($rules->uploads_use_tabs){ 
        echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_FILES'),'files');
       }
      ?>        

        <fieldset class="jd_fieldset_outer">
           <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_FILES'); ?></legend>
        
           <?php
           if ($rules->form_select_main_file){
                if ($this->item->url_download != ''){ ?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('url_download'); ?>
                    <?php echo $this->form->getInput('url_download'); ?>
                    <span>
                        &nbsp;<input type="button" value="" class="button_rename" title="<?php echo JText::_('COM_JDOWNLOADS_FORM_RENAME_FILE_LABEL'); ?>" name="activateFileNameField" onClick="editFilename();" >&nbsp;
                        <?php echo ' <a href="index.php?option=com_jdownloads&amp;task=download.deletefile&amp;id='.$this->item->file_id.'"><img src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/'.'delete.png'.'" width="18px" height="18px" style="vertical-align:middle;border:0px;" alt="'.JText::_('COM_JDOWNLOADS_FORM_DELETE_FILE_LABEL').'" title="'.JText::_('COM_JDOWNLOADS_FORM_DELETE_FILE_LABEL').'" /></a>'; ?>
                    </span>
                </div>        
           <?php }                               
           } ?>
           
           <?php if ($rules->form_select_main_file):?> 
                <div class="formelm70">
                    <?php echo $this->form->getLabel('file_upload'); ?>
                    <?php echo $this->form->getInput('file_upload'); ?>              
				</div>
                 <div class="formelm30">

                    <?php echo '<small>'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_ALLOWED_FILETYPE').' '.str_replace(',', ', ', $rules->uploads_allowed_types).'</small><br />'; ?>

                    <?php echo '<small>'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_ALLOWED_MAX_SIZE').' '.$rules->uploads_maxfilesize_kb.' KB</small>'; ?>
                </div>
                             
            <?php endif; ?>
            
           <?php if ($rules->form_file_size):?>             
                <div class="formelm">
                    <?php echo $this->form->getLabel('size'); ?>
                    <?php echo $this->form->getInput('size'); ?>
                </div>
            <?php endif; ?>
                        
           <?php if ($rules->form_file_date):?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('file_date'); ?>
                    <?php echo $this->form->getInput('file_date'); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($rules->form_select_preview_file && $this->item->preview_filename != ''):?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('preview_filename'); ?>
                    <?php echo $this->form->getInput('preview_filename'); ?>
                    <span>
                        &nbsp;<input type="button" value="" class="button_rename" title="<?php echo JText::_('COM_JDOWNLOADS_FORM_RENAME_FILE_LABEL'); ?>" name="activateFilePrevNameField" onClick="editFilenamePreview();" >&nbsp;
                        <?php echo ' <a href="index.php?option=com_jdownloads&amp;task=download.deletefile&amp;id='.$this->item->file_id.'&amp;type=prev"><img src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/'.'delete.png'.'" width="18px" height="18px" style="vertical-align:middle;border:0px;" alt="'.JText::_('COM_JDOWNLOADS_FORM_DELETE_FILE_LABEL').'" title="'.JText::_('COM_JDOWNLOADS_FORM_DELETE_FILE_LABEL').'" /></a>'; ?>
                    </span>                    
                </div>        
            <?php endif;?>            
            
            <?php if ($rules->form_select_preview_file):?>            
                <div class="formelm">
                    <?php echo $this->form->getLabel('preview_file_upload'); ?>
                    <?php echo $this->form->getInput('preview_file_upload'); ?>
                </div>
                <div class="formelm">
                    <?php echo  '<small>'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_ALLOWED_FILETYPE').' '.str_replace(',', ', ', $rules->uploads_allowed_preview_types).'</small><br />'; ?>
                    <?php echo  '<small>'.JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_ALLOWED_MAX_SIZE').' '.$rules->uploads_maxfilesize_kb.' KB</small>'; ?>
                </div>        
            <?php endif;?>                                        
 <!--cam       </fieldset>  -->

        <?php if ($rules->form_external_file):?>
            <fieldset class="jd_fieldset_inner1">
               <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_EXTERNAL'); ?></legend>        
                <div class="formelm">
                    <?php echo $this->form->getLabel('extern_file'); ?>
                    <?php echo $this->form->getInput('extern_file'); ?>
                </div>          
                <div class="formelm">
                    <?php echo $this->form->getLabel('extern_site'); ?>
                    <?php echo $this->form->getInput('extern_site'); ?>
                </div>          
            </fieldset>        
        <?php endif; ?>        
        
        <?php if ($rules->form_mirror_1):?>
                <fieldset class="jd_fieldset_inner1">
                   <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_MIRRORS_1'); ?></legend>             
                    <div class="formelm">
                        <?php echo $this->form->getLabel('mirror_1'); ?>
                        <?php echo $this->form->getInput('mirror_1'); ?>
                    </div>          
                    <div class="formelm">
                        <?php echo $this->form->getLabel('extern_site_mirror_1'); ?>
                        <?php echo $this->form->getInput('extern_site_mirror_1'); ?>
                    </div>         
                </fieldset>
        <?php endif; ?>                    

        <?php if ($rules->form_mirror_2):?>
                <fieldset class="jd_fieldset_inner1">
                   <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_MIRRORS_2'); ?></legend>             
                    <div class="formelm">
                        <?php echo $this->form->getLabel('mirror_2'); ?>
                        <?php echo $this->form->getInput('mirror_2'); ?>
                    </div>          
                    <div class="formelm">
                        <?php echo $this->form->getLabel('extern_site_mirror_2'); ?>
                        <?php echo $this->form->getInput('extern_site_mirror_2'); ?>
                    </div>         
                </fieldset> 
        <?php endif; ?> 
     </fieldset>
        
<!-- Images TAB -->      

       <?php if ($rules->form_images){ 
                if($rules->uploads_use_tabs){
                 echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_IMAGES'),'images');
                }
       ?>   

                <fieldset class="jd_fieldset_outer">
                   <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_IMAGES'); ?></legend>
                    <?php $image_id = 0; ?>
                    <?php if ($this->item->images){ ?>    
                        <table class="admintable" style="width:100%;border:0px;" cellpadding="0" cellspacing="10">
                        <tr><td><?php if ($this->item->images) echo JText::_('COM_JDOWNLOADS_THUMBNAIL_LIST_INFO'); ?></td></tr>
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
                                 
                                 echo '<img style="position:relative;border:1px solid black; max-width:100px; max-height:100px;" align="middle" src="'.JURI::root().'images/jdownloads/screenshots/thumbnails/'.$image.'" alt="'.$image.'" title="'.$image.'" />';
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
                    if ($image_id < (int)$rules->uploads_max_amount_images){ ?>
                             
                             <label>
                             <?php  echo JHtml::_('tooltip', JText::_('COM_JDOWNLOADS_FORM_IMAGE_UPLOAD_DESC'), JText::_('COM_JDOWNLOADS_FORM_IMAGE_UPLOAD_LABEL').' '.JText::sprintf('COM_JDOWNLOADS_LIMIT_IMAGES_MSG', $rules->uploads_max_amount_images), '', JText::_('COM_JDOWNLOADS_FORM_IMAGE_UPLOAD_LABEL').' '.JText::sprintf('COM_JDOWNLOADS_LIMIT_IMAGES_MSG', $rules->uploads_max_amount_images) ); ?>
                             </label>
                            <table id="files_table" class="admintable" style="border:0px;" cellpadding="0" cellspacing="10">
                            <tr id="new_file_row">
                            <td class=""><input type="file" name="file_upload_thumb[0]" id="file_upload_thumb[0]" size="40" accept="image/gif,image/jpeg,image/jpg,image/png" onchange="add_new_image_file(this)" />
                            </td>
                            </tr>
                            </table> 
                     <?php
                     } else { 
                            // limit is reached - display a info message 
                            echo '<p>'.JText::_('COM_JDOWNLOADS_LIMIT_IMAGES_REACHED_MSG').'</p>'; 
                     }?>        
                </fieldset>      
                
      <?php } else {
               $image_id = 0;
            } ?>


<!-- Additional TAB --> 

      <?php
       if($rules->uploads_use_tabs){
        echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_ADDITIONAL'),'additional');
       }
      ?>

	    <fieldset class="jd_fieldset_outer">
		    <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_ADDITIONAL'); ?></legend>
            <?php if ($rules->form_password):?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('password'); ?>
                    <?php echo $this->form->getInput('password'); ?>
                </div>
            <?php endif; ?>
            <?php if ($rules->form_price):?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('price'); ?>
                    <?php echo $this->form->getInput('price'); ?>
                </div>
            <?php endif; ?>      
            <?php if ($rules->form_website):?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('url_home'); ?>
                    <?php echo $this->form->getInput('url_home'); ?>
                </div>
            <?php endif; ?>
            <?php if ($rules->form_author_name):?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('author'); ?>
                    <?php echo $this->form->getInput('author'); ?>
                </div>
            <?php endif; ?> 
            <?php if ($rules->form_author_mail):?> 
                <div class="formelm">
                    <?php echo $this->form->getLabel('url_author'); ?>
                    <?php echo $this->form->getInput('url_author'); ?>
                </div>	               
            <?php endif; ?>
            <?php if ($rules->form_file_pic):?> 
                <div class="formelm60">
                    <?php echo $this->form->getLabel('file_pic'); ?>
                    <?php echo $this->form->getInput('file_pic'); ?>
                </div>
                <div class="formelm40"> 
                   <!-- not used currently
                    <?php if ($this->item->file_pic != ''){ ?>
                        <img src="<?php echo JURI::root().'images/jdownloads/fileimages/'.$this->item->file_pic; ?>" name="imagelib" alt="<?php echo $this->item->file_pic; ?>" />
                    <?php } else { ?>
                         <img src="<?php echo JURI::root().'images/jdownloads/fileimages/'.$jlistConfig['file.pic.default.filename']; ?>" name="imagelib" alt="<?php echo $jlistConfig['file.pic.default.filename']; ?>" />
                    <?php } ?>
                   -->
                       
                    <script language="javascript" type="text/javascript">
                        if (document.adminForm.file_pic.options.value != ''){
                            jsimg = "<?php echo JURI::root().'images/jdownloads/fileimages/'; ?>" + getSelectedText( 'adminForm', 'file_pic' );
                        } else {
                            jsimg = '';
                        }
                        document.write('<img src="' + jsimg + '" name="imagelib" width="<?php echo $jlistConfig['file.pic.size']; ?>" height="<?php echo $jlistConfig['file.pic.size']; ?>" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_FORM_NO_SYMBOL_TEXT'); ?>" />');
                    </script>                        
                </div>
				<div style="clear:both"></div>
            <?php endif; ?>
            <?php if ($rules->form_changelog){ 	  
                     if ($rules->uploads_use_editor){ ?>
                        <label><?php echo '<b>'.$this->form->getLabel('changelog').'</b>'; ?></label>
                        <?php echo $this->form->getInput('changelog'); ?>
                        <div style="clear:both"></div>
                     <?php } else { ?>
                              <div class="formelm">
                                    <?php echo $this->form->getLabel('changelog'); ?>
                                    <?php echo $this->form->getInput('changelog'); ?>
                              </div>          
                     <?php } ?>
            <?php } ?>
             <?php
             if ($rules->form_extra_select_box_1 && $jlistConfig['custom.field.1.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_1'); ?>
                    <?php echo $this->form->getInput('custom_field_1'); ?>
                </div>
            <?php } ?>

            <?php
             if ($rules->form_extra_select_box_2 && $jlistConfig['custom.field.2.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_2'); ?>
                    <?php echo $this->form->getInput('custom_field_2'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_select_box_3 && $jlistConfig['custom.field.3.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_3'); ?>
                    <?php echo $this->form->getInput('custom_field_3'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_select_box_4 && $jlistConfig['custom.field.4.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_4'); ?>
                    <?php echo $this->form->getInput('custom_field_4'); ?>
                </div>
            <?php } ?>
            

             <?php
             if ($rules->form_extra_select_box_5 && $jlistConfig['custom.field.5.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_5'); ?>
                    <?php echo $this->form->getInput('custom_field_5'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_short_input_1 && $jlistConfig['custom.field.6.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_6'); ?>
                    <?php echo $this->form->getInput('custom_field_6'); ?>
                </div>
            <?php } ?>
            
             <?php
             if ($rules->form_extra_short_input_2 && $jlistConfig['custom.field.7.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_7'); ?>
                    <?php echo $this->form->getInput('custom_field_7'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_short_input_3 && $jlistConfig['custom.field.8.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_8'); ?>
                    <?php echo $this->form->getInput('custom_field_8'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_short_input_4 && $jlistConfig['custom.field.9.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_9'); ?>
                    <?php echo $this->form->getInput('custom_field_9'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_short_input_5 && $jlistConfig['custom.field.10.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_10'); ?>
                    <?php echo $this->form->getInput('custom_field_10'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_date_1 && $jlistConfig['custom.field.11.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_11'); ?>
                    <?php echo $this->form->getInput('custom_field_11'); ?>
                </div>
            <?php } ?>
            
            <?php
             if ($rules->form_extra_date_2 && $jlistConfig['custom.field.12.title'] != ''){ ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('custom_field_12'); ?>
                    <?php echo $this->form->getInput('custom_field_12'); ?>
                </div>
            <?php } ?>
           
            <?php 
            if ($rules->form_extra_large_input_1 && $jlistConfig['custom.field.13.title'] != ''){ 
                 if ($rules->uploads_use_editor){ ?>
                    <label>
                        <?php echo '<b>'.$this->form->getLabel('custom_field_13').'</b>'; ?>
                    </label>
                    <?php echo $this->form->getInput('custom_field_13'); ?>
                    <div style="clear:both"></div>
                    <br />
            <?php } else { ?>
                    <div class="formelm">
                        <?php $label = $this->form->getLabel('custom_field_13'); 
                              if ($pos = strrpos($label, 'custom_field_13')){
                                  // we must replace the label text in this case with the defined label text from the configuration 
                                  $label = substr_replace($label, trim($jlistConfig['custom.field.13.title']), $pos, 15); 
                              }  
                              echo $label;
                        ?>
                        <?php echo $this->form->getInput('custom_field_13'); ?>
                    </div>                        
            <?php } 
              } ?>
                    
            <?php 
            if ($rules->form_extra_large_input_2 && $jlistConfig['custom.field.14.title'] != ''){ 
                 if ($rules->uploads_use_editor){ ?>
                    <label>
                        <?php echo '<b>'.$this->form->getLabel('custom_field_14').'</b>'; ?>
                    </label>
                    <?php echo $this->form->getInput('custom_field_14'); ?>
                    <div style="clear:both"></div>
                    <br />
            <?php } else { ?>
                    <div class="formelm">
                        <?php $label = $this->form->getLabel('custom_field_14'); 
                              if ($pos = strrpos($label, 'custom_field_14')){
                                  // we must replace the label text in this case with the defined label text from the configuration 
                                  $label = substr_replace($label, trim($jlistConfig['custom.field.14.title']), $pos, 15); 
                              }  
                              echo $label;                        
                        ?>
                        <?php echo $this->form->getInput('custom_field_14'); ?>
                    </div>                        
            <?php } 
              } ?>

                    
                    
            </fieldset>


<!-- Description TAB -->      
       
      <?php
        if ($rules->form_short_desc || $rules->form_long_desc){
            if($rules->uploads_use_tabs){
				echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_FORM_LABEL_DESCRIPTIONS'),'descriptions');
            }
      ?> 

        <fieldset class="jd_fieldset_outer">
            <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_DESCRIPTIONS'); ?></legend>

            <?php if ($rules->form_short_desc){
                      if ($rules->uploads_use_editor){ ?>
                          <label><?php echo '<b>'.$this->form->getLabel('description').'</b>'; ?></label>
                          <?php echo $this->form->getInput('description'); ?>
                          <div style="clear:both"></div>
                          <br />
                      <?php } else { ?> 
                          <div class="formelm">
                            <?php echo $this->form->getLabel('description'); ?>
                            <?php echo $this->form->getInput('description'); ?>
                          </div>    
                      <?php } ?>                          
            <?php } ?>
            
            <?php if ($rules->form_long_desc){ 
                      if ($rules->uploads_use_editor){ ?>
                          <label><?php echo '<b>'.$this->form->getLabel('description_long').'</b>'; ?></label>
                          <?php echo $this->form->getInput('description_long'); ?>
                          <div style="clear:both"></div>
                          <br />
                      <?php } else { ?> 
                          <div class="formelm">
                            <?php echo $this->form->getLabel('description_long'); ?>
                            <?php echo $this->form->getInput('description_long'); ?>
                          </div>    
                      <?php } ?>                          
            <?php } ?>
      </fieldset>  
      <?php } ?>

           
<!-- Metadata TAB -->      

       <?php
             if ($rules->form_meta_desc || $rules->form_meta_key || $rules->form_robots){ 
                 if($rules->uploads_use_tabs){
                  echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_FORM_LABEL_TAB_META_DATA'),'meta'); 
                 }                
       ?>   
	            <fieldset class="jd_fieldset_outer">
		            <legend><?php echo JText::_('COM_JDOWNLOADS_FORM_LABEL_META_DATA'); ?></legend>
		            
                    <?php if ($rules->form_meta_desc):?>
                        <div class="formelm">
		                    <?php echo $this->form->getLabel('metadesc'); ?>
		                    <?php echo $this->form->getInput('metadesc'); ?>
		                </div>
                    <?php endif; ?>
		            
                    <?php if ($rules->form_meta_key):?>
                        <div class="formelm">
		                    <?php echo $this->form->getLabel('metakey'); ?>
		                    <?php echo $this->form->getInput('metakey'); ?>
		                </div>
                    <?php endif; ?>

                    <?php if ($rules->form_robots):?>
                        <div class="formelm">
                            <?php echo $this->form->getLabel('robots'); ?>
                            <?php echo $this->form->getInput('robots'); ?>
                        </div>
                    <?php endif; ?>            
	            </fieldset>
       <?php 
			
			if($rules->uploads_use_tabs){                
				echo JHtml::_('tabs.end'); 
			}
		}	
		?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="form" />
        <input type="hidden" name="image_file_count" id="image_file_count" value="0" />         
        <input type="hidden" name="cat_dir_org" value="<?php echo $this->item->cat_id; ?>" />
        <input type="hidden" name="sum_listed_images" id="sum_listed_images" value="<?php echo (int)$image_id; ?>" />
        <input type="hidden" name="max_sum_images" id="max_sum_images" value="<?php echo (int)$rules->uploads_max_amount_images; ?>" /> 
        <input type="hidden" name="filename" value="<?php echo $this->item->url_download; ?>" />        
        <input type="hidden" name="modified_date_old" value="<?php echo $this->item->modified_date; ?>" />
        <input type="hidden" name="submitted_by" value="<?php echo $this->item->submitted_by; ?>" />
        <input type="hidden" name="set_aup_points" value="<?php echo $this->item->set_aup_points; ?>" />
        <input type="hidden" name="filename_org" value="<?php echo $this->item->url_download; ?>" />          
        <input type="hidden" name="preview_filename_org" value="<?php echo $this->item->preview_filename; ?>" />
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" /> 

        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>