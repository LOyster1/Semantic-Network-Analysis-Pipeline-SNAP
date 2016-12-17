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

JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
JHtml::_('behavior.tooltip');

?>
<script type="text/javascript">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.getElementById('adminForm');

        // do field validation
        if (form.restore_file.value == ""){
            alert("<?php echo JText::_('COM_JDOWNLOADS_RESTORE_NO_FILE', true); ?>");
        } else {
            var answer = confirm("<?php echo JText::_('COM_JDOWNLOADS_RESTORE_RUN_FINAL'); ?>")
            if (answer){
                form.submit();
            }    
        }
    }
</script>  

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
   
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>   
    
    <div>
        <fieldset style="background-color: #ffffff;" class="uploadform">
            <legend><?php echo JText::_('COM_JDOWNLOADS_RESTORATION'); ?></legend>
  
            <div class="jdwarning"><?php echo JText::_('COM_JDOWNLOADS_RESTORE_WARNING'); ?>
             <br /><br /><?php 
             $version = '2.5';
             echo  sprintf(JText::_('COM_JDOWNLOADS_RESTORE_FILE_HINT'), $version); ?></div>                
            
            <label style="margin:20px;" for="install_package"><?php echo JText::_('COM_JDOWNLOADS_RESTORE_FILE').': '; ?>
            </label>
            
            <input style="margin:20px;" class="input_box" id="restore_file" name="restore_file" type="file" size="60" />
            <input style="margin:20px;" class="button" type="button" value="<?php echo JText::_('COM_JDOWNLOADS_RESTORE_RUN').'&nbsp; '; ?>" onclick="Joomla.submitbutton()" />
              
        </fieldset>
    </div>
  
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="com_jdownloads" />
    <input type="hidden" name="task" value="restore.runrestore" />
    <input type="hidden" name="view" value="restore" />
    <input type="hidden" name="hidemainmenu" value="0" />
    
    <?php echo JHtml::_('form.token'); ?>
   </form>
