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

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));


?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task);
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=groups');?>" method="post" name="adminForm" id="adminForm">

    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>	
    
    <fieldset id="jdfilter-bar">
		<div class="filter-search jdfltlft">
			<!--<label class="filter-search-lbl jdfltlft" for="filter_search"><?php echo JText::_('COM_JDOWNLOADS_USERGROUPS_SEARCH_GROUPS_LABEL'); ?></label>-->
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
        <div class="filter-select jdfltrt">
            <?php 
            // we must use a little trick to get the right input name and id
            $dummy = $this->pagination->getLimitBox(); 
            $limit_box = str_replace('id="limit"', 'id="list_limit"', $dummy);
            $limit_box = str_replace('name="limit"', 'name="list[limit]"', $limit_box);
            echo $limit_box; 
            ?>               
        </div>
	</fieldset>
    <div class="jdlists-header-info jdfltlft"><?php echo '<img style="align:top; float:left; margin-right:10px" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="32" height="32" border="0" alt="" />'.JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_TITLE_INFO'); ?> </div>    
	<div class="jdclr"> </div>

	<table class="adminlist">
		<thead><tr><td colspan="4"> </td><td colspan="9" style="color: #990000; background-color: yellow; text-align: center;"><?php echo JText::_('COM_JDOWNLOADS_USERGROUP_LIST_LIMITS_COL_HEADER'); ?></td><td colspan="6" <td colspan="1" style="color: #990000; background-color:orange;  text-align: center;"><?php echo JText::_('COM_JDOWNLOADS_USERGROUP_LIST_SETTINGS_COL_HEADER'); ?></td></tr></thead>
        <thead>
			<tr>
				<th width="1%" valign="top">
					<input type="checkbox" name="toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left" valign="top">
					<?php echo JText::_('COM_JDOWNLOADS_USERGROUPS_GROUP_TITLE'); ?>
				</th>
                <th width="3%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_IMPORTANCE_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_IMPORTANCE'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_IMPORTANCE')); ?>
                </th>
				<th width="3%" valign="top">
					<?php echo JText::_('COM_JDOWNLOADS_USERGROUPS_USERS_IN_GROUP'); ?>
				</th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_DAILY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_DAILY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_DAILY')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_WEEKLY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_WEEKLY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_WEEKLY')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_MONTHLY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_MONTHLY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_MONTHLY')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_DAILY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_DAILY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_DAILY')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_WEEKLY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_WEEKLY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_WEEKLY')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_MONTHLY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_MONTHLY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_MONTHLY')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_HOW_MANY_TIMES_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_HOW_MANY_TIMES'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_HOW_MANY_TIMES')); ?>
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_TRANSFER_SPEED_LIMIT_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_TRANSFER_SPEED_LIMIT'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_TRANSFER_SPEED_LIMIT')); ?>                    
                </th>                                                
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_UPLOAD_LIMIT_DAILY_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_UPLOAD_LIMIT_DAILY'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_UPLOAD_LIMIT_DAILY')); ?>                    
                </th> 
                
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_CAN_USE_PRIVATE_FILES_AREA_DESC').'<br /><br />'.JText::_('COM_JDOWNLOADS_VIEW_HINT_FOR_NOT_READY_FUNCTIONS'), JText::_('COM_JDOWNLOADS_USERGROUPS_CAN_USE_PRIVATE_FILES_AREA'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_CAN_USE_PRIVATE_FILES_AREA')); ?>                    
                </th> 

                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_CAPTCHA_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_CAPTCHA'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_CAPTCHA')); ?>                    
                </th> 
                <!--
                <th width="5%">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_FORM_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_FORM'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_FORM')); ?>                    
                </th> 
                <th width="5%">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_MUST_FORM_FILL_OUT_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_MUST_FORM_FILL_OUT'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_MUST_FORM_FILL_OUT')); ?>                    
                </th>
                --> 
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_REPORT_FORM_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_REPORT_FORM'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_REPORT_FORM')); ?>                    
                </th>
                <th width="5%" valign="top">
                    <?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_COUNTDOWN_DESC'), JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_COUNTDOWN'), '', JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_COUNTDOWN')); ?>                    
                </th>                                                                 
				<th width="2%" valign="top">
					<?php echo JHtml::tooltip( JText::_('COM_JDOWNLOADS_USERGROUP_LIST_COL_ID_DESC'), JText::_('JGRID_HEADING_ID'), '', JText::_('JGRID_HEADING_ID')); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="21">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
         foreach ($this->items as $i => $item) :
            $canCheckin = $user->authorise('core.manage',                   'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			$canEdit	= $user->authorise('edit.config',    'com_jdownloads');

			// If this group is super admin and this user is not super admin, $canEdit is false   !!!
			if (!$user->authorise('core.admin') && (JAccess::checkGroup($item->id, 'core.admin'))) {
				$canEdit = false;
			}
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					<?php endif; ?>
				</td>
                
				<td>
                    <?php if ($item->checked_out) : ?>
                        <?php 
                        echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'groups.', $canCheckin); ?>
                    <?php endif; ?>                
                
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level) ?>
					<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_jdownloads&task=group.edit&id='.$item->jd_user_group_id);?>">
						<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<?php /*if (JDEBUG) : ?>
						<div class="jdfltrt"><div class="button2-left smallsub"><div class="blank"><a href="<?php echo JRoute::_('index.php?option=com_users&view=debuggroup&group_id='.(int) $item->id);?>">
						<?php echo JText::_('COM_USERS_DEBUG_GROUP');?></a></div></div></div>
					<?php endif; */ ?>
				</td>
                <td class="center">
                    <?php echo $item->importance; ?>
                </td>
				<td class="center">
					<?php echo $item->user_count ? $item->user_count : ''; ?>
				</td>
                <td class="center">
                    <?php echo $item->download_limit_daily ? $item->download_limit_daily : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->download_limit_weekly ? $item->download_limit_weekly : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->download_limit_monthly ? $item->download_limit_monthly : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->download_volume_limit_daily ? $item->download_volume_limit_daily : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->download_volume_limit_weekly ? $item->download_volume_limit_weekly : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->download_volume_limit_monthly ? $item->download_volume_limit_monthly : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->how_many_times ? $item->how_many_times : '0'; ?>
                </td>
                <td class="center">
                    <?php echo $item->transfer_speed_limit_kb ? $item->transfer_speed_limit_kb : '0'; ?>
                </td>                                

                <td class="center">
                    <?php echo $item->upload_limit_daily ? $item->upload_limit_daily : '0'; ?>
                </td>                                

                <td class="center">
                    <?php echo $item->use_private_area ? '<font color="red">'.JText::_('COM_JDOWNLOADS_YES').'</font color>' : JText::_('COM_JDOWNLOADS_NO'); ?>
                </td>
                
                <td class="center">
                    <?php echo $item->view_captcha ? JText::_('COM_JDOWNLOADS_YES') : JText::_('COM_JDOWNLOADS_NO'); ?>
                </td>                 

                <!--
                <td class="center">
                    <?php echo $item->view_inquiry_form ? JText::_('COM_JDOWNLOADS_YES') : JText::_('COM_JDOWNLOADS_NO'); ?>
                </td>

                <td class="center">
                    <?php echo $item->must_form_fill_out ? JText::_('COM_JDOWNLOADS_YES') : JText::_('COM_JDOWNLOADS_NO'); ?>
                </td>
                -->                   

                <td class="center">
                    <?php echo $item->view_report_form ? JText::_('COM_JDOWNLOADS_YES') : JText::_('COM_JDOWNLOADS_NO'); ?>
                </td>
                
                <td class="center">
                    <?php echo $item->countdown_timer_duration ? $item->countdown_timer_duration : JText::_('COM_JDOWNLOADS_NO'); ?>
                </td>   
                
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
