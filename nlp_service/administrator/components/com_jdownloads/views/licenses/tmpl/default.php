<?php 
/*
* @version 2.0
* @package jDownloads
* @copyright (C) 2008/2011 Arno Betz - www.jdownloads.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* 
*
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user        = JFactory::getUser();
$userId      = $user->get('id');

$listOrder   = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction')); 
$saveOrder   = 'a.ordering'; 
$canOrder    = $user->authorise('core.edit.state', 'com_jdownloads');


?>
<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=licenses');?>" method="POST" name="adminForm" id="adminForm">
    
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
            <!--<label class="jdfilter-search-lbl jdfltlft" for="filter_search"><?php echo JText::_('COM_JDOWNLOADS_FILTER_LABEL'); ?></label>-->
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            <button type="submit"><?php echo JText::_('COM_JDOWNLOADS_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('COM_JDOWNLOADS_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select jdfltrt">
            <select name="filter_state" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_STATUS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0, 'trash' => 0)), 'value', 'text', $this->state->get('filter.published'), true);?>
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
			
            <th class="title" align="left">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LINK_LABEL', 'a.url', $listDirn, $listOrder ); ?>
            </th>
			
            <th class="title" align="left" width="30%">
                <?php echo JText::_('COM_JDOWNLOADS_DESCRIPTION'); ?>
            </th>

            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_PUBLISH', 'a.published', $listDirn, $listOrder); ?>
            </th>
            
            <th width="10%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'licenses.saveorder'); ?>
                <?php endif; ?>
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
				<td colspan="8">
					<?php echo '<br />'.$this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>	
		<?php 
            foreach ($this->items as $i => $item) {
                $ordering     = ($listOrder == 'a.ordering');
                $link         = JRoute::_( 'index.php?option=com_jdownloads&task=license.edit&id='.(int) $item->id );
                $canCheckin   = $user->authorise('core.manage',     'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
                $canChange    = $user->authorise('core.edit.state', 'com_jdownloads') && $canCheckin;
                $canCreate    = $user->authorise('core.create',     'com_jdownloads');
                $canEdit      = $user->authorise('core.edit',       'com_jdownloads');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                
                <td align="left">
                <?php if ($item->checked_out) : ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'licenses.', $canCheckin); ?>
                <?php endif; ?>
                <?php if ($canEdit) : ?>
                    <a href="<?php echo $link; ?>">
                        <?php echo $this->escape($item->title); ?></a>
                <?php else : ?>
                        <?php echo $this->escape($item->title); ?>
                <?php endif; ?>
                <p class="smallsub">
                    <?php echo JText::sprintf('COM_JDOWNLOADS_LIST_ALIAS', $this->escape($item->alias));?></p>
                </td>
                
                <td align="left">
                    <a href="<?php echo $item->url; ?>" target="_blank"><?php echo $item->url; ?></a>
                </td>
                <td>
                    <?php
                        if (strlen($item->description) > 200 ) {
                            echo substr($item->description, 0, 197).'...';
                        } else {
                            echo $item->description;
                        }
                    ?>
                </td>

                <td class="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'licenses.', $canChange); ?>
                </td>
                                
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) :?>
                            <?php if ($listDirn == 'asc') : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'licenses.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'licenses.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'licenses.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'licenses.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                    <?php else : ?>
                        <?php echo $item->ordering; ?>
                    <?php endif; ?>
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
</div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>    
</div>
</form>
