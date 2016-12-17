<!--  modified cam 2014-05-20  circa line 152 with <ul> addition
    changed fltlft to jdfltlft and fltrt to jdfltrt-->
<?php

defined('_JEXEC') or die;

global $jlistConfig;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
$canDo = jdownloadsHelper::getActions();

jimport( 'joomla.form.form' );

$star = '<span class="star">*</span>';

?>

    <script type="text/javascript">
	    Joomla.submitbutton = function(task)
	    {
		    if (task == 'group.cancel' || document.formvalidator.isValid(document.id('group-form'))) {
			    Joomla.submitform(task, document.getElementById('group-form'));
		    }
	    }
    </script>

<?php
    if ($canDo->get('edit.user.limits')) {
?>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="group-form" class="form-validate">
	<div>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPPANEL_TABTEXT_INFO'); ?></legend>
            <div class="jdlists-header-info"><?php echo '<img style="margin-right:15px;" align="top" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="24" height="24" border="0" alt="" />'.JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_LOG_OPTIONS_INFO').' '.JText::_('COM_JDOWNLOADS_MULTILANGUAGE_TEXT_FIELD_INFO'); ?> </div>
        </fieldset>
    </div>
    <div  class="width-60 jdfltlft">
		<fieldset class="adminform">
			<span class="jd-grouplim-title"><?php echo JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_LIMITS');?></span>
			<ul class="adminformlist">
				<li><?php $this->form->setValue( 'title', $group=null, $this->form->title );
                          echo $this->form->getLabel('title'); ?>
				<?php     echo $this->form->getInput('title'); ?></li>

                <li><?php echo $this->form->getLabel('importance'); ?>
                <?php echo $this->form->getInput('importance'); ?></li>                
                
                <li><?php echo $this->form->getLabel('download_limit_daily'); ?>
                <?php echo $this->form->getInput('download_limit_daily'); ?></li>

                <li><?php echo $this->form->getLabel('download_limit_daily_msg'); ?>
                <?php echo $this->form->getInput('download_limit_daily_msg'); ?></li>

                <li><?php echo $this->form->getLabel('download_limit_weekly'); ?>
                <?php echo $this->form->getInput('download_limit_weekly'); ?></li>
                
                <li><?php echo $this->form->getLabel('download_limit_weekly_msg'); ?>
                <?php echo $this->form->getInput('download_limit_weekly_msg'); ?></li>                
                
                <li><?php echo $this->form->getLabel('download_limit_monthly'); ?>
                <?php echo $this->form->getInput('download_limit_monthly'); ?></li>
                
                <li><?php echo $this->form->getLabel('download_limit_monthly_msg'); ?>
                <?php echo $this->form->getInput('download_limit_monthly_msg'); ?></li>

                <li><?php echo $this->form->getLabel('download_volume_limit_daily'); ?>
                <?php echo $this->form->getInput('download_volume_limit_daily'); ?></li>

                <li><?php echo $this->form->getLabel('download_volume_limit_daily_msg'); ?>
                <?php echo $this->form->getInput('download_volume_limit_daily_msg'); ?></li>
                
                <li><?php echo $this->form->getLabel('download_volume_limit_weekly'); ?>
                <?php echo $this->form->getInput('download_volume_limit_weekly'); ?></li>

                <li><?php echo $this->form->getLabel('download_volume_limit_weekly_msg'); ?>
                <?php echo $this->form->getInput('download_volume_limit_weekly_msg'); ?></li>                
                
                <li><?php echo $this->form->getLabel('download_volume_limit_monthly'); ?>
                <?php echo $this->form->getInput('download_volume_limit_monthly'); ?></li>
                
                <li><?php echo $this->form->getLabel('download_volume_limit_monthly_msg'); ?>
                <?php echo $this->form->getInput('download_volume_limit_monthly_msg'); ?></li>                

                <li><?php echo $this->form->getLabel('how_many_times'); ?>
                <?php echo $this->form->getInput('how_many_times'); ?></li>
                
                <li><?php echo $this->form->getLabel('how_many_times_msg'); ?>
                <?php echo $this->form->getInput('how_many_times_msg'); ?></li>
                
                <li><?php echo $this->form->getLabel('download_limit_after_this_time'); ?>
                <?php echo $this->form->getInput('download_limit_after_this_time'); ?></li>                

                <li><?php echo $this->form->getLabel('upload_limit_daily'); ?>
                <?php echo $this->form->getInput('upload_limit_daily'); ?></li>

                <li><?php echo $this->form->getLabel('upload_limit_daily_msg'); ?>
                <?php echo $this->form->getInput('upload_limit_daily_msg'); ?></li>

                <li><?php echo $this->form->getLabel('transfer_speed_limit_kb'); ?>
                <?php echo $this->form->getInput('transfer_speed_limit_kb'); ?></li>
                
            </ul>
        </fieldset>
        </div>
        
        <div class="width-40 jdfltrt">
        <?php echo JHtml::_('sliders.start','usergroups-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_SETTINGS'), 'usergroups-settings'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">


                <li><?php echo $this->form->getLabel('use_private_area'); ?>
                <?php echo $this->form->getInput('use_private_area'); ?></li>
                
                
                <?php
                    if ($this->item->use_private_area == 1){
                ?>
                    <li><label><?php echo JText::_('COM_JDOWNLOADS_USERGROUPS_PRIVATE_FILES_AREA_FOLDER_NAME').'<br />'.$jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name']; ?>
                    </label>
                    </li>                
                
                    <li><label><?php echo (is_writable($jlistConfig['files.uploaddir'].DS.$jlistConfig['private.area.folder.name'])) ? JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_DOWNLOAD_WRITABLE') : JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_URL_DOWNLOAD_NOTWRITABLE'); ?></label></li>
                <?php } ?>
                
                <li><?php echo $this->form->getLabel('myspacer'); ?></li>


                <li><?php echo $this->form->getLabel('view_captcha'); ?>
                <?php echo $this->form->getInput('view_captcha'); ?></li>
                <!--
                <li><?php echo $this->form->getLabel('view_inquiry_form'); ?>
                <?php echo $this->form->getInput('view_inquiry_form'); ?></li>
                
                <li><?php echo $this->form->getLabel('must_form_fill_out'); ?>
                <?php echo $this->form->getInput('must_form_fill_out'); ?></li>
                -->
                <li><?php echo $this->form->getLabel('view_report_form'); ?>
                <?php echo $this->form->getInput('view_report_form'); ?></li>
                
                <li><?php echo $this->form->getLabel('countdown_timer_duration'); ?>
                <?php echo $this->form->getInput('countdown_timer_duration'); ?></li>                                                                                                                                                                                                
                <li><?php echo $this->form->getLabel('countdown_timer_msg'); ?>
                <?php echo $this->form->getInput('countdown_timer_msg'); ?></li> 
			</ul>
			<div class="jdclr"></div>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('view_user_his_limits'); ?>
                <?php echo $this->form->getInput('view_user_his_limits'); ?></li>
 
                <li><?php echo $this->form->getLabel('view_user_his_limits_msg'); ?>
                <?php echo $this->form->getInput('view_user_his_limits_msg'); ?></li>                 
             
                <li><?php echo $this->form->getLabel('myspacer'); ?></li>                
                
                <li><?php echo $this->form->getLabel('notes'); ?>
                <?php echo $this->form->getInput('notes'); ?></li>
                
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>

                <li><?php echo $this->form->getLabel('group_id'); ?>
                <?php echo $this->form->getInput('group_id'); ?></li>  
			
            </ul>
		</fieldset>

        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_CREATION_SETTINGS'), 'usergroups-creation-settings'); ?>

        <fieldset class="panelform">
            <ul class="adminformlist">

            <!-- To get the forms fields data also when we use a unmarked checkbox, we must use the trick with the hidden input field (value="0") -->
            <!-- See also here: http://docs.joomla.org/Talk:Checkbox_form_field_type -->
            <!-- If we will use a checkbox field only as 'readonly', we must use the hidden value="1" for it --> 
            
                <?php echo '<p class="inputbox">'.JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_CREATION_SETTINGS_NOTE').'</p>'; ?>
                
                <li><input type="hidden" name="jform[uploads_view_upload_icon]" value="0">
                <?php echo $this->form->getLabel('uploads_view_upload_icon'); ?>
                <?php echo $this->form->getInput('uploads_view_upload_icon'); ?></li>

                <li><input type="hidden" name="jform[uploads_can_change_category]" value="0">
                <?php echo $this->form->getLabel('uploads_can_change_category'); ?>
                <?php echo $this->form->getInput('uploads_can_change_category'); ?></li>

                <li><input type="hidden" name="jform[uploads_allow_custom_tags]" value="0">
                <?php echo $this->form->getLabel('uploads_allow_custom_tags'); ?>
                <?php echo $this->form->getInput('uploads_allow_custom_tags'); ?></li>
                
                <li><input type="hidden" name="jform[uploads_auto_publish]" value="0">
                <?php echo $this->form->getLabel('uploads_auto_publish'); ?>
                <?php echo $this->form->getInput('uploads_auto_publish'); ?></li>        

                <li><input type="hidden" name="jform[uploads_use_editor]" value="0">
                <?php echo $this->form->getLabel('uploads_use_editor'); ?>
                <?php echo $this->form->getInput('uploads_use_editor'); ?></li>
            
                <li><input type="hidden" name="jform[uploads_use_tabs]" value="0">
                <?php echo $this->form->getLabel('uploads_use_tabs'); ?>
                <?php echo $this->form->getInput('uploads_use_tabs'); ?></li>            

                <li>
                <?php echo $this->form->getLabel('uploads_default_access_level'); ?>
                <?php echo $this->form->getInput('uploads_default_access_level'); ?></li>                 
            
                <li>
                <?php echo $this->form->getLabel('uploads_allowed_types'); ?>
                <?php echo $this->form->getInput('uploads_allowed_types'); ?></li> 

                <li>
                <?php echo $this->form->getLabel('uploads_allowed_preview_types'); ?>
                <?php echo $this->form->getInput('uploads_allowed_preview_types'); ?></li> 
                                
                <li>
                <?php echo $this->form->getLabel('uploads_maxfilesize_kb'); ?>
                <?php echo $this->form->getInput('uploads_maxfilesize_kb'); ?></li> 
                
                <li>
                <?php echo $this->form->getLabel('uploads_max_amount_images'); ?>
                <?php echo $this->form->getInput('uploads_max_amount_images'); ?></li> 
                
                <li>
                <?php echo $this->form->getLabel('uploads_form_text'); ?>
                <?php echo $this->form->getInput('uploads_form_text'); ?></li> 
                
                <?php echo $this->form->getLabel('spacer1'); ?>
                <?php echo '<p class="inputbox">'.JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_CREATION_SETTINGS_DESC').'</p>'; ?>
                
                <!-- To change the label tag data from a xml definition, we use this simple trick with 'str_replace' --> 
                
                <li><input type="hidden" name="jform[form_title]" value="1">
                <?php $x = $this->form->getLabel('form_title');
                      echo str_replace('</label>', $star.'</label>', $x); ?>
                <?php echo $this->form->getInput('form_title'); ?></li>                   

                <li><input type="hidden" name="jform[form_alias]" value="0">
                <?php echo $this->form->getLabel('form_alias'); ?> 
                <?php echo $this->form->getInput('form_alias'); ?>
                <input type="hidden" name="jform[form_alias_x]" value="0">
                <?php echo $this->form->getInput('form_alias_x'); ?></li>
                
                <li><input type="hidden" name="jform[form_category]" value="1">
                <?php $x = $this->form->getLabel('form_category');
                      echo str_replace('</label>', $star.'</label>', $x); ?>
                <?php echo $this->form->getInput('form_category'); ?></li>                  
                
                <li><input type="hidden" name="jform[form_version]" value="0">
                <?php echo $this->form->getLabel('form_version'); ?>
                <?php echo $this->form->getInput('form_version'); ?>
                <input type="hidden" name="jform[form_version_x]" value="0">
                <?php echo $this->form->getInput('form_version_x'); ?></li>

                <li><input type="hidden" name="jform[form_update_active]" value="0">
                <?php echo $this->form->getLabel('form_update_active'); ?>
                <?php echo $this->form->getInput('form_update_active'); ?></li>

                <li><input type="hidden" name="jform[form_access]" value="0">
                <?php echo $this->form->getLabel('form_access'); ?>
                <?php echo $this->form->getInput('form_access'); ?></li>
                
                <li><input type="hidden" name="jform[form_file_language]" value="0">
                <?php echo $this->form->getLabel('form_file_language'); ?>
                <?php echo $this->form->getInput('form_file_language'); ?>
                <input type="hidden" name="jform[form_file_language_x]" value="0">
                <?php echo $this->form->getInput('form_file_language_x'); ?></li>
                
                <li><input type="hidden" name="jform[form_file_system]" value="0">
                <?php echo $this->form->getLabel('form_file_system'); ?>
                <?php echo $this->form->getInput('form_file_system'); ?>
                <input type="hidden" name="jform[form_file_system_x]" value="0">
                <?php echo $this->form->getInput('form_file_system_x'); ?></li>                
                
                <li><input type="hidden" name="jform[form_license]" value="0">
                <?php echo $this->form->getLabel('form_license'); ?>
                <?php echo $this->form->getInput('form_license'); ?>
                <input type="hidden" name="jform[form_license_x]" value="0">
                <?php echo $this->form->getInput('form_license_x'); ?></li>                
                
                <li><input type="hidden" name="jform[form_confirm_license]" value="0">
                <?php echo $this->form->getLabel('form_confirm_license'); ?>
                <?php echo $this->form->getInput('form_confirm_license'); ?></li>
                
                <li><input type="hidden" name="jform[form_short_desc]" value="0">
                <?php echo $this->form->getLabel('form_short_desc'); ?>
                <?php echo $this->form->getInput('form_short_desc'); ?>
                <input type="hidden" name="jform[form_short_desc_x]" value="0">
                <?php echo $this->form->getInput('form_short_desc_x'); ?></li>                
                
                <li><input type="hidden" name="jform[form_long_desc]" value="0">
                <?php echo $this->form->getLabel('form_long_desc'); ?>
                <?php echo $this->form->getInput('form_long_desc'); ?>
                <input type="hidden" name="jform[form_long_desc_x]" value="0">
                <?php echo $this->form->getInput('form_long_desc_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_changelog]" value="0">
                <?php echo $this->form->getLabel('form_changelog'); ?>
                <?php echo $this->form->getInput('form_changelog'); ?>
                <input type="hidden" name="jform[form_changelog_x]" value="0">
                <?php echo $this->form->getInput('form_changelog_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_language]" value="0">
                <?php echo $this->form->getLabel('form_language'); ?>
                <?php echo $this->form->getInput('form_language'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_published]" value="0">
                <?php echo $this->form->getLabel('form_published'); ?>
                <?php echo $this->form->getInput('form_published'); ?></li>
                
                <li><input type="hidden" name="jform[form_featured]" value="0">
                <?php echo $this->form->getLabel('form_featured'); ?>
                <?php echo $this->form->getInput('form_featured'); ?></li>
                
                <li><input type="hidden" name="jform[form_creation_date]" value="0">
                <?php echo $this->form->getLabel('form_creation_date'); ?>
                <?php echo $this->form->getInput('form_creation_date'); ?> 
                <input type="hidden" name="jform[form_creation_date_x]" value="0">
                <?php echo $this->form->getInput('form_creation_date_x'); ?></li>   
                
                <li><input type="hidden" name="jform[form_modified_date]" value="0">
                <?php echo $this->form->getLabel('form_modified_date'); ?>
                <?php echo $this->form->getInput('form_modified_date'); ?></li> 
                
                <li><input type="hidden" name="jform[form_timeframe]" value="0">
                <?php echo $this->form->getLabel('form_timeframe'); ?>
                <?php echo $this->form->getInput('form_timeframe'); ?></li> 
                
                <li><input type="hidden" name="jform[form_views]" value="0">
                <?php echo $this->form->getLabel('form_views'); ?>
                <?php echo $this->form->getInput('form_views'); ?></li> 
                
                <li><input type="hidden" name="jform[form_downloaded]" value="0">
                <?php echo $this->form->getLabel('form_downloaded'); ?>
                <?php echo $this->form->getInput('form_downloaded'); ?></li> 
                
                <li><input type="hidden" name="jform[form_ordering]" value="0">
                <?php echo $this->form->getLabel('form_ordering'); ?>
                <?php echo $this->form->getInput('form_ordering'); ?></li> 
                
                <li><input type="hidden" name="jform[form_password]" value="0">
                <?php echo $this->form->getLabel('form_password'); ?>
                <?php echo $this->form->getInput('form_password'); ?>
                <input type="hidden" name="jform[form_password_x]" value="0">
                <?php echo $this->form->getInput('form_password_x'); ?></li>                                                                  

                <li><input type="hidden" name="jform[form_price]" value="0">
                <?php echo $this->form->getLabel('form_price'); ?>
                <?php echo $this->form->getInput('form_price'); ?>
                <input type="hidden" name="jform[form_price_x]" value="0">
                <?php echo $this->form->getInput('form_price_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_website]" value="0">
                <?php echo $this->form->getLabel('form_website'); ?>
                <?php echo $this->form->getInput('form_website'); ?>
                <input type="hidden" name="jform[form_website_x]" value="0">
                <?php echo $this->form->getInput('form_website_x'); ?></li>                   
                
                <li><input type="hidden" name="jform[form_author_name]" value="0">
                <?php echo $this->form->getLabel('form_author_name'); ?>
                <?php echo $this->form->getInput('form_author_name'); ?>
                <input type="hidden" name="jform[form_author_name_x]" value="0">
                <?php echo $this->form->getInput('form_author_name_x'); ?></li>                   
                
                <li><input type="hidden" name="jform[form_author_mail]" value="0">
                <?php echo $this->form->getLabel('form_author_mail'); ?>
                <?php echo $this->form->getInput('form_author_mail'); ?>
                <input type="hidden" name="jform[form_author_mail_x]" value="0">
                <?php echo $this->form->getInput('form_author_mail_x'); ?></li>                                                                   

                <li><input type="hidden" name="jform[form_file_pic]" value="0">
                <?php echo $this->form->getLabel('form_file_pic'); ?>
                <?php echo $this->form->getInput('form_file_pic'); ?></li> 
                
                <li><input type="hidden" name="jform[form_select_main_file]" value="0">
                <?php echo $this->form->getLabel('form_select_main_file'); ?>
                <?php echo $this->form->getInput('form_select_main_file'); ?>
                <input type="hidden" name="jform[form_select_main_file_x]" value="0">
                <?php echo $this->form->getInput('form_select_main_file_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_file_size]" value="0">
                <?php echo $this->form->getLabel('form_file_size'); ?>
                <?php echo $this->form->getInput('form_file_size'); ?></li> 
                
                <li><input type="hidden" name="jform[form_file_date]" value="0">
                <?php echo $this->form->getLabel('form_file_date'); ?>
                <?php echo $this->form->getInput('form_file_date'); ?>
                <input type="hidden" name="jform[form_file_date_x]" value="0">
                <?php echo $this->form->getInput('form_file_date_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_select_preview_file]" value="0">
                <?php echo $this->form->getLabel('form_select_preview_file'); ?>
                <?php echo $this->form->getInput('form_select_preview_file'); ?>
                <input type="hidden" name="jform[form_select_preview_file_x]" value="0">
                <?php echo $this->form->getInput('form_select_preview_file_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_external_file]" value="0">
                <?php echo $this->form->getLabel('form_external_file'); ?>
                <?php echo $this->form->getInput('form_external_file'); ?>
                <input type="hidden" name="jform[form_external_file_x]" value="0">
                <?php echo $this->form->getInput('form_external_file_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_mirror_1]" value="0">
                <?php $x = $this->form->getLabel('form_mirror_1');
                      echo str_replace('</label>', ' 1</label>', $x); ?>
                <?php echo $this->form->getInput('form_mirror_1'); ?>
                <input type="hidden" name="jform[form_mirror_1_x]" value="0">
                <?php echo $this->form->getInput('form_mirror_1_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_mirror_2]" value="0">
                <?php $x = $this->form->getLabel('form_mirror_2');
                      echo str_replace('</label>', ' 2</label>', $x); ?>
                <?php echo $this->form->getInput('form_mirror_2'); ?>
                <input type="hidden" name="jform[form_mirror_2_x]" value="0">
                <?php echo $this->form->getInput('form_mirror_2_x'); ?></li> 

                <li><input type="hidden" name="jform[form_images]" value="0">
                <?php echo $this->form->getLabel('form_images'); ?>
                <?php echo $this->form->getInput('form_images'); ?>
                <input type="hidden" name="jform[form_images_x]" value="0">
                <?php echo $this->form->getInput('form_images_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_meta_desc]" value="0">
                <?php echo $this->form->getLabel('form_meta_desc'); ?>
                <?php echo $this->form->getInput('form_meta_desc'); ?></li> 
                
                <li><input type="hidden" name="jform[form_meta_key]" value="0">
                <?php echo $this->form->getLabel('form_meta_key'); ?>
                <?php echo $this->form->getInput('form_meta_key'); ?></li> 
                
                <li><input type="hidden" name="jform[form_robots]" value="0">
                <?php echo $this->form->getLabel('form_robots'); ?>
                <?php echo $this->form->getInput('form_robots'); ?></li>
                
                <li><input type="hidden" name="jform[form_tags]" value="0">
                <?php echo $this->form->getLabel('form_tags'); ?>
                <?php echo $this->form->getInput('form_tags'); ?></li>                

                <?php echo $this->form->getLabel('spacer1'); ?>
                <?php echo '<p class="inputbox">'.JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_CREATION_SETTINGS_DESC2').'</p>'; ?>
                
                <li><input type="hidden" name="jform[form_extra_select_box_1]" value="0">
                <?php $x = $this->form->getLabel('form_extra_select_box_1'); 
                      echo str_replace('</label>', ' 1</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_select_box_1'); ?>
                <input type="hidden" name="jform[form_extra_select_box_1_x]" value="0">
                <?php echo $this->form->getInput('form_extra_select_box_1_x'); ?></li>                 

                <li><input type="hidden" name="jform[form_extra_select_box_2]" value="0">
                <?php $x = $this->form->getLabel('form_extra_select_box_2'); 
                      echo str_replace('</label>', ' 2</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_select_box_2'); ?>
                <input type="hidden" name="jform[form_extra_select_box_2_x]" value="0">
                <?php echo $this->form->getInput('form_extra_select_box_2_x'); ?></li>                

                <li><input type="hidden" name="jform[form_extra_select_box_3]" value="0">
                <?php $x = $this->form->getLabel('form_extra_select_box_3'); 
                      echo str_replace('</label>', ' 3</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_select_box_3'); ?>
                <input type="hidden" name="jform[form_extra_select_box_3_x]" value="0">
                <?php echo $this->form->getInput('form_extra_select_box_3_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_select_box_4]" value="0">
                <?php $x = $this->form->getLabel('form_extra_select_box_4'); 
                      echo str_replace('</label>', ' 4</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_select_box_4'); ?>
                <input type="hidden" name="jform[form_extra_select_box_4_x]" value="0">
                <?php echo $this->form->getInput('form_extra_select_box_4_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_select_box_5]" value="0">
                <?php $x = $this->form->getLabel('form_extra_select_box_5'); 
                      echo str_replace('</label>', ' 5</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_select_box_5'); ?>
                <input type="hidden" name="jform[form_extra_select_box_5_x]" value="0">
                <?php echo $this->form->getInput('form_extra_select_box_5_x'); ?></li>                 

                <li><input type="hidden" name="jform[form_extra_short_input_1]" value="0">
                <?php $x = $this->form->getLabel('form_extra_short_input_1'); 
                      echo str_replace('</label>', ' 1</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_short_input_1'); ?>
                <input type="hidden" name="jform[form_extra_short_input_1_x]" value="0">
                <?php echo $this->form->getInput('form_extra_short_input_1_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_short_input_2]" value="0">
                <?php $x = $this->form->getLabel('form_extra_short_input_2'); 
                      echo str_replace('</label>', ' 2</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_short_input_2'); ?>
                <input type="hidden" name="jform[form_extra_short_input_2_x]" value="0">
                <?php echo $this->form->getInput('form_extra_short_input_2_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_short_input_3]" value="0">
                <?php $x = $this->form->getLabel('form_extra_short_input_3'); 
                      echo str_replace('</label>', ' 3</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_short_input_3'); ?>
                <input type="hidden" name="jform[form_extra_short_input_3_x]" value="0">
                <?php echo $this->form->getInput('form_extra_short_input_3_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_short_input_4]" value="0">
                <?php $x = $this->form->getLabel('form_extra_short_input_4'); 
                      echo str_replace('</label>', ' 4</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_short_input_4'); ?>
                <input type="hidden" name="jform[form_extra_short_input_4_x]" value="0">
                <?php echo $this->form->getInput('form_extra_short_input_4_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_short_input_5]" value="0">
                <?php $x = $this->form->getLabel('form_extra_short_input_5'); 
                      echo str_replace('</label>', ' 5</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_short_input_5'); ?>
                <input type="hidden" name="jform[form_extra_short_input_5_x]" value="0">
                <?php echo $this->form->getInput('form_extra_short_input_5_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_large_input_1]" value="0">
                <?php $x = $this->form->getLabel('form_extra_large_input_1'); 
                      echo str_replace('</label>', ' 1</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_large_input_1'); ?>
                <input type="hidden" name="jform[form_extra_large_input_1_x]" value="0">
                <?php echo $this->form->getInput('form_extra_large_input_1_x'); ?></li>                 

                <li><input type="hidden" name="jform[form_extra_large_input_2]" value="0">
                <?php $x = $this->form->getLabel('form_extra_large_input_2'); 
                      echo str_replace('</label>', ' 2</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_large_input_2'); ?>
                <input type="hidden" name="jform[form_extra_large_input_2_x]" value="0">
                <?php echo $this->form->getInput('form_extra_large_input_2_x'); ?></li>                 
                
                <li><input type="hidden" name="jform[form_extra_date_1]" value="0">
                <?php $x = $this->form->getLabel('form_extra_date_1'); 
                      echo str_replace('</label>', ' 1</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_date_1'); ?>
                <input type="hidden" name="jform[form_extra_date_1_x]" value="0">
                <?php echo $this->form->getInput('form_extra_date_1_x'); ?></li>                 

                <li><input type="hidden" name="jform[form_extra_date_2]" value="0">
                <?php $x = $this->form->getLabel('form_extra_date_2'); 
                      echo str_replace('</label>', ' 2</label>', $x); ?>
                <?php echo $this->form->getInput('form_extra_date_2'); ?>
                <input type="hidden" name="jform[form_extra_date_2_x]" value="0">
                <?php echo $this->form->getInput('form_extra_date_2_x'); ?></li>                 
            </ul>    
        </fieldset>                                                                                 
        
        <?php echo JHtml::_('sliders.end'); ?>
 
        <input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
    <div class="clr"></div>
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
    </form>           
<?php
   }    
?>
