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
    
   <!-- <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>    
    -->
    
    <div>
        <fieldset style="background-color: #ffffff;" class="uploadform">
            <legend style="margin-bottom:5px;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_TITLE'); ?></legend>
            <div class="infotext" style="font-size:100%; padding:5px;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_1'); ?></div> 
            <div class="infotext" style="font-size:100%; padding:5px;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_2'); ?></div> 
            <div class="infotext" style="font-size:100%; padding:5px;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_3'); ?></div> 
            <hr>           

             <label id="csstext-lbl" class="" title="" for="csstext">
             <strong>1. <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_FIELD_TITLE').': '.$this->cssfile; ?></strong><br />
             
             <small><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_WRITE_STATUS_TEXT')." ";
                if ($this->cssfile_writable) {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_YES');
                } else {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_NO'); ?>
                    <br /><strong>
                    <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_INFO'); ?></strong><br />
            <?php } ?></small>
            
            </label>
            
            <div class="clr"></div>
            <textarea class="input_box" name="cssfile" cols="100" rows="15"><?php echo $this->csstext; ?></textarea>
        </fieldset>
    </div>
    
    <div>
        <fieldset style="background-color: #ffffff;" class="uploadform">
            <legend><?php echo JText::_(''); ?></legend>

             <label id="csstext-lbl" class="" title="" for="csstext2">
             <strong>2. <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_FIELD_TITLE').': '.$this->cssfile2; ?></strong><br />
             
             <small><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_WRITE_STATUS_TEXT')." ";
                if ($this->cssfile_writable2) {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_YES');
                } else {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_NO'); ?>
                    <br /><strong>
                    <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_INFO'); ?></strong><br />
            <?php } ?></small>
            
            </label>
            
            <div class="clr"></div>
            <textarea class="input_box" name="cssfile2" cols="100" rows="15"><?php echo $this->csstext2; ?></textarea>
        </fieldset>
    </div>    
    
    <div>
        <fieldset style="background-color: #ffffff;" class="uploadform">
            <legend><?php echo JText::_(''); ?></legend>

             <label id="csstext-lbl" class="" title="" for="csstext3">
             <strong>3. <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_FIELD_TITLE').': '.$this->cssfile3; ?></strong><br />
             
             <small><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_WRITE_STATUS_TEXT')." ";
                if ($this->cssfile_writable3) {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_YES');
                } else {
                    echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_NO'); ?>
                    <br /><strong>
                    <?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_LANG_CSS_FILE_WRITABLE_INFO'); ?></strong><br />
            <?php } ?></small>
            
            </label>
            
            <div class="clr"></div>
            <textarea class="input_box" name="cssfile3" cols="100" rows="15"><?php echo $this->csstext3; ?></textarea>
        </fieldset>
    </div>    
    
    <input type="hidden" name="option" value="com_jdownloads" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="cssedit" />
    <input type="hidden" name="hidemainmenu" value="0" />
    
    <?php echo JHtml::_('form.token'); ?>
   </form>
