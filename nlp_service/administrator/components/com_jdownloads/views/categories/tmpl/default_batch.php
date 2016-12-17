<?php
/**
 * @package jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2012 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

$published = $this->state->get('filter.published');
?>
<div class="modal hide fade in" id="collapseModal">
<fieldset class="batch jdfltlft">
    <legend><?php echo JText::_('COM_JDOWNLOADS_BATCH_OPTIONS');?></legend>
    <p><?php echo JText::_('COM_JDOWNLOADS_BATCH_FOLDER_NOTE'); ?><br />
       <?php echo JText::_('COM_JDOWNLOADS_BATCH_DESC'); ?></p>
    <?php 
    echo JHtml::_('batch.access');
    echo JHtml::_('batch.language'); ?>

    <?php if ($published >= 0) : ?>
        <?php 
             // display category list box 
              echo '<label id="batch-choose-action-lbl" for="batch-choose-action">'.JText::_('COM_JDOWNLOADS_BATCH_CATEGORY_LABEL').'</label>'; 
              echo '<fieldset id="batch-choose-action" class="combo">';
              $test =  JHtml::_('select.genericlist', $this->categories, 'batch[category_id]', 'name="batch[category_id]" class="inputbox"', 'value', 'text');
              echo $test;
              echo '<div class="control-group radio">';
              echo '<input id="batch[move_copy]c" type="radio" value="c" name="batch[move_copy]">'.
                   '<label id="batch[move_copy]c-lbl" class="radiobtn" for="batch[move_copy]c">Copy</label>'.
                   '<input id="batch[move_copy]m" type="radio" checked="checked" value="m" name="batch[move_copy]">'.
                   '<label id="batch[move_copy]m-lbl" class="radiobtn" for="batch[move_copy]m">Move</label>';
              echo '</div>';
              echo '</fieldset>'; ?>
    <?php endif; ?>

    <button type="submit" onclick="Joomla.submitbutton('category.batch');">
        <?php echo JText::_('COM_JDOWNLOADS_BATCH_PROCESS'); ?>
    </button>
    <button type="button" onclick="document.id('batch-category-id').value=''; document.id('batch-access').value=''; document.id('batch-language-id').value=''" data-dismiss="modal">
        <?php echo JText::_('COM_JDOWNLOADS_CANCEL'); ?>
    </button>
</fieldset>
</div>
