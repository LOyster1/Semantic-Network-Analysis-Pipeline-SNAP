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
 
defined('_JEXEC') or die('Restricted access');

global $jlistConfig; 

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user        = JFactory::getUser();
$userId      = $user->get('id');
$root        = JURI::root();
?>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=files');?>" method="post" name="adminForm" id="adminForm">
    
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
            <?php 
            // we must use a little trick to get the right input name and id
            $dummy = $this->pagination->getLimitBox(); 
            $limit_box = str_replace('id="limit"', 'id="list_limit"', $dummy);
            $limit_box = str_replace('name="limit"', 'name="list[limit]"', $limit_box);
            echo $limit_box; 
            ?>              
        </div>    
    </fieldset>
    <div class="jdlists-header-info jdfltlft"><?php echo '<img align="left" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="22" height="22" border="0" alt="" />&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_MANAGE_FILES_DESC'); ?> </div>
    <div class="clr"> </div>            
    <div id="editcell">                                             
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5" align="left">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
            </th>
            
            <th class="title" align="left">
                <?php echo JText::_('COM_JDOWNLOADS_MANAGE_FILES_TITLE_NAME'); ?>
            </th>
            
            <th class="title" align="left">
                <?php echo JText::_('COM_JDOWNLOADS_MANAGE_FILES_TITLE_DATE'); ?>
            </th> 
            
            <th class="title" align="left">
                <?php echo JText::_('COM_JDOWNLOADS_MANAGE_FILES_TITLE_SIZE'); ?> 
            </th>
            
            <th class="title" style="text-align: center">
                <?php echo JText::_('COM_JDOWNLOADS_MANAGE_FILES_TITLE_CREATE_NEW_DOWNLOAD'); ?>
            </th>

        </tr>    
    </thead>
        <tfoot>
          <tr>
            <td colspan="5"><?php echo '<br />'.$this->pagination->getListFooter(); ?>
            </td>
          </tr>
        </tfoot>
        <tbody>  
        <?php 
            foreach ($this->items as $i => $item) {
                $canCreate    = $user->authorise('core.create',     'com_jdownloads');
                $canEdit      = $user->authorise('core.edit',       'com_jdownloads');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, htmlspecialchars($item['name'])); ?>
                </td>
                
                <td>
                <?php echo $this->escape($item['name']); ?>
                </td>
                
                <td class="left">
                    <?php echo JHtml::_('date',$this->escape($item['date']), JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                    
                <td class="left">
                     <?php echo $this->escape($item['size']) ?>
                </td>

                <td class="center">
                    <?php echo JRoute::_('<a href="index.php?option=com_jdownloads&amp;task=download.edit&amp;file='.$item['name'].'">'.JText::_('COM_JDOWNLOADS_MANAGE_FILES_TITLE_CREATE_NEW_DOWNLOAD').'</a>');
                    ?>
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
    
    <?php echo JHtml::_('form.token'); ?>    
</div>
</form>