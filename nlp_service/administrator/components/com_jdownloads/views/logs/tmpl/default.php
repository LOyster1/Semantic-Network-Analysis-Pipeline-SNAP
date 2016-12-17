<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 *
 * @component jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2011 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */


defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user        = JFactory::getUser();
$userId      = $user->get('id');

$listOrder   = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction')); 
$saveOrder   = 'a.log_datetime'; 
$canOrder    = $user->authorise('core.edit.state', 'com_jdownloads'); 

$options = array();
$options[] = JHtml::_('select.option', 0, JText::_('COM_JDOWNLOADS_LOGS_SELECT_TYPE_ALL'));
$options[] = JHtml::_('select.option', 1, JText::_('COM_JDOWNLOADS_LOGS_SELECT_TYPE_DOWNLOADS'));
$options[] = JHtml::_('select.option', 2, JText::_('COM_JDOWNLOADS_LOGS_SELECT_TYPE_UPLOADS'));
$listbox_type = JHtml::_('select.genericlist', $options, 'filter_type', 'class="inputbox" onchange="this.form.submit()"', 'value', 'text', $this->state->get('filter.type'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=logs');?>" method="POST" name="adminForm" id="adminForm">
    
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
            <!--<label class="filter-search-lbl jdfltlft" for="filter_search"><?php echo JText::_('COM_JDOWNLOADS_FILTER_LABEL'); ?></label>-->
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            <button type="submit"><?php echo JText::_('COM_JDOWNLOADS_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('COM_JDOWNLOADS_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select jdfltrt">
            <?php echo $listbox_type; ?>
            <?php 
            // we must use a little trick to get the right input name and id
            $dummy = $this->pagination->getLimitBox(); 
            $limit_box = str_replace('id="limit"', 'id="list_limit"', $dummy);
            $limit_box = str_replace('name="limit"', 'name="list[limit]"', $limit_box);
            echo $limit_box; 
            ?>             
        </div>
    </fieldset>
    <div class="jdlists-header-info jdfltlft"><?php echo '<img align="top" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="24" height="24" border="0" alt="" />&nbsp;&nbsp;'.$this->logs_header_info; ?> </div>
    <div class="jdclr"> </div>            
<div id="editcell">                                             
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5" align="left">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
            </th>
			
            <th class="title" align="left">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LOGS_COL_DATE_LABEL', 'a.log_datetime', $listDirn, $listOrder ); ?>
            </th>
			
            <th class="title" align="left">
                <?php echo  JText::_('COM_JDOWNLOADS_LOGS_COL_USER_LABEL'); ?>
            </th>
			
            <th class="title" align="left">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LOGS_COL_IP_LABEL', 'a.log_ip', $listDirn, $listOrder ); ?>
            </th>
            
            <th class="title" align="left">
                <?php echo  JText::_('COM_JDOWNLOADS_LOGS_COL_FILETITLE_LABEL'); ?>
            </th>

            <th class="title" align="left">
                <?php echo  JText::_('COM_JDOWNLOADS_LOGS_COL_FILENAME_LABEL'); ?>
            </th>            

            <th class="title" align="left">
                <?php echo  JText::_('COM_JDOWNLOADS_LOGS_COL_FILESIZE_LABEL'); ?>
            </th>
            
            <th class="title" align="left">                                            
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LOGS_COL_TYPE_LABEL', 'a.type', $listDirn, $listOrder ); ?>
            </th>
            
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_ID', 'a.id', $listDirn, $listOrder ); ?>
            </th>
    </tr>	
	</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo '<br />'.$this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>	
		<?php 
            foreach ($this->items as $i => $item) {
                $ordering     = ($listOrder == 'a.ordering');
                $canCheckin   = $user->authorise('core.manage',     'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
                $canChange    = $user->authorise('core.edit.state', 'com_jdownloads') && $canCheckin;
                $canCreate    = $user->authorise('core.create',     'com_jdownloads');
                $canEdit      = $user->authorise('core.edit',       'com_jdownloads');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                
                <td>
                     <?php echo JHtml::_('date',$this->escape($item->log_datetime), JText::_('DATE_FORMAT_LC2')); ?>
                </td>
                
                <td align="left">
                    <?php if ($item->username == ''){
                      echo JText::_('COM_JDOWNLOADS_LOGS_COL_USER_ANONYMOUS');
                } else {
                      echo $this->escape($item->username);
                } ?>
                </td>

                <td align="left">
                    <?php echo $item->log_ip; ?>
                </td>

                <td align="left">
                    <?php echo  $this->escape($item->log_title); ?>
                </td>
                
                <td align="left">
                    <?php echo  $this->escape($item->log_file_name); ?>
                </td>

                <td align="left">
                    <?php echo  $this->escape($item->log_file_size).' KB'; ?>
                </td>

                <td align="left">
                <?php if ($item->type == '1'){
                      echo JText::_('COM_JDOWNLOADS_LOGS_COL_TYPE_DOWNLOAD');
                } else {
                      echo JText::_('COM_JDOWNLOADS_LOGS_COL_TYPE_UPLOAD');
                } ?> 
                </td>    
                <td>
                    <?php echo (int) $item->id; ?>
                </td>
		    </tr>
		    <?php 
             }
            ?>
	    </tbody>
	</table>
</div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>    
</div>
</form>
