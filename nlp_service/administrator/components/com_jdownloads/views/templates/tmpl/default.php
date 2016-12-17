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
$canOrder    = $user->authorise('core.edit.state', 'com_jdownloads');


?>
<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=templates&type='.$this->jd_tmpl_type.'');?>" method="POST" name="adminForm" id="adminForm">

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
    <div class="clr"> </div>            
    
    <div class="jdlists-header-info"><?php echo '<img align="top" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="24" height="24" border="0" alt="" />&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_LOCKED_DESC'); ?> </div>
    <div class="clr"> </div>
    
    <div id="editcell">                                             
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5" align="left">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
            </th>
			
            <th class="title" align="left" width="50%">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_BACKEND_TEMPLIST_TITLE', 'a.template_name', $listDirn, $listOrder ); ?>
            </th>
			
            <th class="title">
                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_TYP'); ?> 
            </th>

            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_BACKEND_TEMPLIST_LOCKED', 'a.locked', $listDirn, $listOrder); ?>
            </th>
            
            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_BACKEND_TEMPLIST_ACTIVE', 'a.template_active', $listDirn, $listOrder); ?>
            </th>
            
            <?php if ($this->jd_tmpl_type == 1) { ?>
                   <th width="5%">   
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_COLS');  ?> 
                   </th>                        
            <?php } ?>            

            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LANGUAGE', 'a.language', $listDirn, $listOrder ); ?>
            </th>
            
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_ID', 'a.cols', $listDirn, $listOrder ); ?>
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
                $link         = JRoute::_( 'index.php?option=com_jdownloads&task=template.edit&id='.(int) $item->id.'&type='.(int) $item->template_typ);
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
                <?php if ($item->checked_out) : ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $user->name, $item->checked_out_time, 'templates.', $canCheckin); ?>
                <?php endif; ?>
                <?php if ($canEdit) : ?>
                    <a href="<?php echo $link; ?>">
                        <?php echo $this->escape($item->template_name); ?></a>
                <?php else : ?>
                        <?php echo $this->escape($item->template_name); ?>
                <?php endif; ?>
                <p class="smallsub">
                    <?php echo JText::sprintf('COM_JDOWNLOADS_BACKEND_TEMPLIST_DESCRIPTION', $this->escape($item->note));?></p>
                </td>

                <td class="center">   
                    <?php echo $this->temp_type_name[$item->template_typ]; ?>
                </td>
                
                <td class="center"> 
                    <?php
                        if ($item->locked > 0) { ?>
                            <img src="components/com_jdownloads/assets/images/active.png" width="12px" height="12px" align="middle" border="0"/>
                    <?php } ?>                
                </td>
                
                <td class="center"> 
                    <?php
                        if ($item->template_active > 0) { ?>
                            <img src="components/com_jdownloads/assets/images/active.png" width="12px" height="12px" align="middle" border="0"/>
                    <?php } ?>
                </td>
                
                <?php if ($this->jd_tmpl_type == 1) { ?>
                    <td class="center">   
                        <?php echo $item->cols; ?> 
                    </td>                        
                <?php } ?>

                <td class="center nowrap">
                    <?php 
                    if ($item->language=='*'):?>
                        <?php echo JText::alt('COM_JDOWNLOADS_ALL','language'); ?>
                    <?php else:?>
                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('COM_JDOWNLOADS_UNDEFINED'); ?>
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
    <input type="hidden" name="type" value="<?php echo (int)$this->jd_tmpl_type; ?>" />
    <?php echo JHtml::_('form.token'); ?>    
</div>
</form>
