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
 
defined('_JEXEC') or die('Restricted access');

global $jlistConfig; 

JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads');?>" method="post" name="adminForm" id="adminForm">
    
    <!--<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>        -->
        
        <div>
        <fieldset style="background-color: #ffffff;" class="uploadform">
            <legend><?php echo JText::_(''); ?></legend>
                       
             <label id="text-lbl" class="" title="" for="text">
             
             <strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_FIELD_TITLE').': </strong>'.$this->languagefile; ?><br />            
            
             <small>
             <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_WRITE_STATUS_TEXT')." ";
                if ($this->languagefile_writable) {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_YES');
                } else {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_NO'); ?>
                    <br /><strong>
                    <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_INFO'); ?></strong>
            <?php } ?></small>
            
            </label>
            
            <div class="clr"></div>
            <textarea class="input_box" name="cssfile" cols="120" rows="30"><?php echo $this->languagetext; ?></textarea>
        </fieldset>
    </div>
  
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="com_jdownloads" />
    <input type="hidden" name="languagepath" value="<?php echo $this->languagefile; ?>" />
    <input type="hidden" name="view" value="languageedit" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="hidemainmenu" value="0" />
    
    <?php echo JHtml::_('form.token'); ?>
   </form>