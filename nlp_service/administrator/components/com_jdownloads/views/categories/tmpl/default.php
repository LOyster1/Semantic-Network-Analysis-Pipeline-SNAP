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

defined('_JEXEC') or die;

global $jlistConfig;

JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user        = JFactory::getUser();
$userId      = $user->get('id');
$root        = JURI::root();

$listOrder   = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction')); 
$canOrder    = $user->authorise('core.edit.state', 'com_jdownloads');

$ordering     = ($listOrder == 'a.lft');
$saveOrder    = ($listOrder == 'a.lft' && $listDirn == 'asc');

?>
<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=categories');?>" method="POST" name="adminForm" id="adminForm">
    
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
            <select name="filter_level" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_MAX_LEVELS');?></option> 
                <?php echo JHtml::_('select.options', $this->levels, 'value', 'text', $this->state->get('filter.level'));?>
            </select>
            <select name="filter_state" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_STATUS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0, 'trash' => 0)), 'value', 'text', $this->state->get('filter.state'), true);?>
            </select>
            <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_LANGUAGE');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
            </select>

            <?php 
            // we must use a little trick to get the right input name and id
            $dummy = $this->pagination->getLimitBox(); 
            $limit_box = str_replace('id="limit"', 'id="list_limit"', $dummy);
            $limit_box = str_replace('name="limit"', 'name="list[limit]"', $limit_box);
            echo $limit_box; 
            ?>
        </div>
    </fieldset>
    <div class="jdclr"> </div>            
<div id="editcell">                                             
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5" align="left">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
            </th>
            
            <th class="title" align="left">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_TITLE', 'a.title', $listDirn, $listOrder ); ?>
            </th>
            
            <th class="title">
                <?php echo JText::_('COM_JDOWNLOADS_CATSLIST_COUNT'); ?>
            </th> 
            
            <th class="title">
                <?php  echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_CATSLIST_PIC', 'a.pic', $listDirn, $listOrder); ?> 
            </th>
            
            <th class="title" style="text-align: center">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_CATSLIST_PATH', 'a.cat_dir', $listDirn, $listOrder ); ?>
            </th>
            
            <th class="title" style="text-align: center">
                <?php echo JText::_('COM_JDOWNLOADS_CATSLIST_LINK')." "; ?>
            </th>
            
            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_STATUS', 'a.published', $listDirn, $listOrder); ?>
            </th>
            
            <th width="10%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
                <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'categories.saveorder'); ?>
                <?php endif; ?>
            </th>

            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_ACCESS', 'a.access', $listDirn, $listOrder ); ?>
            </th>

            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LANGUAGE', 'a.language', $listDirn, $listOrder ); ?>
            </th>
            
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_ID', 'a.id', $listDirn, $listOrder ); ?>
            </th>
    </tr>    
    </thead>
        <tfoot>
            <tr>
                <td colspan="11">
                    <?php echo '<br />'.$this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>    
        <?php 
            $originalOrders = array();
            
            foreach ($this->items as $i => $item) {
                $orderkey    = array_search($item->id, $this->ordering[$item->parent_id]);
                $link         = JRoute::_( 'index.php?option=com_jdownloads&task=category.edit&id='.(int) $item->id );
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
                <?php 
                echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>                
                
                
                <?php if ($item->checked_out) : ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
                <?php endif; ?>
                <?php if ($canEdit) : ?>
                    <a href="<?php echo $link; ?>">
                        <?php echo $this->escape($item->title); ?></a>
                <?php else : ?>
                        <?php echo $this->escape($item->title); ?>
                <?php endif; ?>
                <p class="smallsub"> 
                    <?php echo str_repeat('<span class="gtr">|&mdash;</span>', $item->level-1) ?>
                    <?php if (empty($item->notes)) : ?>
                        <?php echo JText::sprintf('COM_JDOWNLOADS_LIST_ALIAS', $this->escape($item->alias));?>
                    <?php else : ?>
                        <?php echo JText::sprintf('COM_JDOWNLOADS_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->notes));?>
                    <?php endif; ?></p>
                </td>
                
                <td class="center">
                    <?php echo JDownloadsHelper::getSumDownloadsFromCat($item->id); ?>
                </td>                
                
                <td class="center">
                    <?php if ($item->pic != '') { ?>
                        <img src="<?php echo JURI::root().JRoute::_( "images/jdownloads/catimages/$item->pic" ); ?>" width="32px" height="32px" align="middle" border="0"/>
                    <?php } ?>
                </td>
                    
                <td class="center">
                <?php
                    if ($item->parent_id > 1) {
                        // echo JHtml::_('tooltip', $jlistConfig['files.uploaddir'].DS.$item->cat_dir_parent.DS.$item->cat_dir, JText::_('COM_JDOWNLOADS_CATSLIST_PATH'));
                        echo JHtml::_('tooltip',strip_tags($jlistConfig['files.uploaddir'].DS.$item->cat_dir_parent.DS.$item->cat_dir), JText::_('COM_JDOWNLOADS_CATSLIST_PATH'), JURI::root().'administrator/components/com_jdownloads/assets/images/tooltip_blue.gif'); 
                    } else {
                        echo JHtml::_('tooltip',strip_tags($jlistConfig['files.uploaddir'].DS.$item->cat_dir), JText::_('COM_JDOWNLOADS_CATSLIST_PATH'), JURI::root().'administrator/components/com_jdownloads/assets/images/tooltip_blue.gif');
                    }    
                ?>
                </td>

                <td class="center">
                    <?php
                    if ($item->published) {
                        $url_cat_link =  JRoute::_('<a href="'.$root.'index.php?option=com_jdownloads&amp;view=category&amp;catid='.$item->id.'" target="_blank">'.JText::_('COM_JDOWNLOADS_CATSLIST_LINK_TEXT').'</a>');
                        echo $url_cat_link;
                    } else {
                        echo '';
                    }        
                    ?>
                </td>

                <td class="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.', $canChange); ?>
                </td>
                                
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) :?>
                                <span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php $originalOrders[] = $orderkey + 1; ?>
                    <?php else : ?>
                        <?php echo $orderkey + 1; ?>
                    <?php endif; ?>
                </td>
                
                <td class="center">
                    <?php echo $item->access_level; ?>
                </td>
                <td class="center nowrap">
                    <?php if ($item->language=='*'):?>
                        <?php echo JText::alt('JALL','language'); ?>
                    <?php else:?>
                        <?php echo $item->language ? $this->escape($item->language) : JText::_('COM_JDOWNLOADS_UNDEFINED'); ?>
                    <?php endif;?>
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
    
    <?php //Load the batch processing form. ?>
    <?php echo 
    $this->loadTemplate('batch'); ?>    
    
</div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
    <?php echo JHtml::_('form.token'); ?>    
</div>
</form>
