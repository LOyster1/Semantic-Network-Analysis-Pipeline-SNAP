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
?>



<form action="index.php" method="post" name="adminForm" id="adminForm">
   
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>   
    
    <div>
        <fieldset style="background-color: #ffffff; margin-top:5px;" class="infotext">
            <legend> <?php echo JText::_('COM_JDOWNLOADS_BACKEND_INFO_TEXT_TITLE')." "; ?> </legend>
            <div class="infotext"> <img src="components/com_jdownloads/assets/images/jdownloads.jpg" alt="jDownloads Logo"/><br /><br />
               <big>jDownloads - a Download Management Component for Joomla!</big><br />
                         Copyright 2007/2014 by Arno Betz - <a href="http://www.jdownloads.com" target="_blank">www.jDownloads.com</a> all rights reserved.
                         <br /><br />
                         <b>jDownloads logo</b> created and copyright by <a href="http://www.rkdesign.ch" target="_blank">rkdesign</a> - all rights reserved.<br /><br />
             </div>
        </fieldset>
    </div>          
    
    <div> 
        <fieldset style="background-color: #ffffff; margin-top:5px;" class="uploadform">
        <legend> <?php echo JText::_('COM_JDOWNLOADS_TERMS_OF_USE')." "; ?> </legend> 
        <div class="infotext">
                 <?php echo JText::_('COM_JDOWNLOADS_BACKEND_INFO_LICENSE_TITLE').'<br />';
                       echo JText::_('COM_JDOWNLOADS_BACKEND_INFO_LICENSE_TEXT'); 
                 ?>
        </div>
        </fieldset>
    </div> 
    <div> 
        <fieldset style="background-color: #ffffff; margin-top:5px;" class="uploadform">
        <legend> <?php echo JText::_('COM_JDOWNLOADS_BACKEND_TESTERS_TEXT_TITLE')." "; ?> </legend> 
        <div class="infotext">
            Colin Mercer, Zoker, mkhde, pansonic, Sergey, ati90210, rikau2, Papounet and others.<br />Many thanks at all testing team members and all translators! 
        </div>
        </fieldset>
    </div>    
       
    <div> 
        <fieldset style="background-color: #ffffff; margin-top:5px;" class="uploadform">
        <legend> <?php echo JText::_('COM_JDOWNLOADS_TRANSLATED_TITLE')." "; ?> </legend> 
        <div class="infotext">
        <b><?php echo JText::_('COM_JDOWNLOADS_TRANSLATED_BY_NAME')." "; ?></b><br />
        <?php echo JText::_('COM_JDOWNLOADS_TRANSLATED_BY_EMAIL')." "; ?><br />
        <?php echo JText::_('COM_JDOWNLOADS_TRANSLATED_BY_URL')." "; ?>  
        </div>
        </fieldset>
    </div>     
    
    <input type="hidden" name="option" value="com_jdownloads" />
    <input type="hidden" name="task" value="info" />
    <input type="hidden" name="view" value="info" />
    <input type="hidden" name="hidemainmenu" value="0" />
    
   </form>
