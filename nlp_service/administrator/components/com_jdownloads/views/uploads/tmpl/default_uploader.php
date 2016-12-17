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

/*
# This script is original from the component com_mediamu and is only modified to use it with jDownloads 
# ------------------------------------------------------------------------
@author Ljubisa - ljufisha.blogspot.com
@copyright Copyright (C) 2012 ljufisha.blogspot.com. All Rights Reserved.
@license - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
Technical Support: http://ljufisha.blogspot.com
*/

// no direct access
 defined('_JEXEC') or die('Restricted Access');

?>

<form action="" method="post" id="adminForm" name="adminForm">
	
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>    
    
    <div class="jdlists-header-info"><?php echo '<img align="left" src="'.JURI::root().'administrator/components/com_jdownloads/assets/images/info22.png" width="22" height="22" border="0" alt="" />&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_UPLOADER_DESC').'<br /><br />'.JText::_('COM_JDOWNLOADS_UPLOADER_DESC2'); ?> </div>
    <div class="clr"> </div>    
    
    <div id="uploader">
		<p><?php JText::printf('COM_JDOWNLOADS_ERROR_RUNTIME_NOT_SUPORTED', $this->runtime) ?></p>
	</div>
    <!-- we need here the 'task' field to get NOT an error message like: 'TypeError: b.task is undefined' -->
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
</form>
<?php if($this->enableLog) : ?>
<button id="log_btn"><?php echo JText::_('COM_JDOWNLOADS_UPLOADER_LOG_BTN'); ?></button>
<div id="log"></div>
<?php endif; ?>