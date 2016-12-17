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


 defined('_JEXEC') or die('Restricted access');

    global $jlistConfig;
    
    jimport( 'joomla.html.html.tabs' );
   
    JHtml::_('behavior.tooltip');
    JHTML::_('behavior.formvalidation');
    // JHtml::_('behavior.formvalidator'); Joomla >= 3.4
    JHtml::_('jquery.framework');
    
    $user    = JFactory::getUser();
    
    $db = JFactory::getDBO();
    $db->setQuery("SELECT `rules` FROM #__assets WHERE `name` = 'com_jdownloads' AND `title` = 'com_jdownloads' AND `level` = '1'");
    $component_rules = $db->loadResult();    

    // get download stats
    $stats_data = jdownloadsHelper::getDownloadStatsData();
    $sum_downloads = jdownloadsHelper::getSumDownloads();
    $user_rules = jdownloadsHelper::getUserRules();
    
    $canDo = jdownloadsHelper::getActions();
    $option = 'com_jdownloads';
    
    // check that we have valid user rules
    // when not, create it from joomla users
    if (!$user_rules){
        $user_result = jdownloadsHelper::setUserRules();
    }
    
    // view not the control panel when we must move the data from old release (< 2.5)
    if (!$jlistConfig['old.jd.release.found']){
    ?>
    
    <form action="index.php" method="post" name="adminForm">
    
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>    
        
    <div id="j-main-container" class="span10">
        <div class="adminform">
            <div class="jd-cpanel-left">
                <div id="cpanel">        
                    <?php // are global permissions defined?
                    if ($component_rules == '{}'){
                        echo '<div class="jdlists-header-info" style="margin-bottom:10px; padding-bottom: 10px; width: 96%; font-size: 12px; text-align: yustify;">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/warning.png'.'" border="0" alt="warning" style="float:left;" />'
                        .JText::_('COM_JDOWNLOADS_PERMISSIONS_NOT_FOUND_INFO').' '.JText::_('COM_JDOWNLOADS_SET_PERMISSIONS_INFO_HINT').'<br /><small>'.JText::_('COM_JDOWNLOADS_ACCESS_ONLY_FOR_SUPER_USERS').'</small></div>';
                    }
    	 
                    $link = 'index.php?option='.$option.'&amp;view=categories';
                            jdownloadsViewjdownloads::quickiconButton( $link, 'categories48.png', JText::_( 'COM_JDOWNLOADS_CATEGORIES' ) );
                    
                    $link = 'index.php?option='.$option.'&amp;view=downloads';
						    jdownloadsViewjdownloads::quickiconButton( $link, 'downloads48.png', JText::_( 'COM_JDOWNLOADS_DOWNLOADS' ) );

                    $link = 'index.php?option='.$option.'&amp;view=files';
                            jdownloadsViewjdownloads::quickiconButton( $link, 'files48.png', JText::_( 'COM_JDOWNLOADS_FILES' ) );
                            
    	   		    $link = 'index.php?option='.$option.'&amp;view=licenses';
						    jdownloadsViewjdownloads::quickiconButton( $link, 'licenses48.png', JText::_( 'COM_JDOWNLOADS_LICENSES' ) );						

                    $link = 'index.php?option='.$option.'&amp;view=layouts';
						    jdownloadsViewjdownloads::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_LAYOUTS' ) );

                    $link = 'index.php?option='.$option.'&amp;view=logs';
                            jdownloadsViewjdownloads::quickiconButton( $link, 'logs48.png', JText::_( 'COM_JDOWNLOADS_LOGS' ) );                        
                                                    
                    if ($canDo->get('edit.user.limits')) {                
                            $link = 'index.php?option='.$option.'&amp;view=groups';
                            jdownloadsViewjdownloads::quickiconButton( $link, 'groups48.png', JText::_( 'COM_JDOWNLOADS_USER_GROUPS' ) );
                    }                       
                   
                    if ($canDo->get('edit.config')) {
    	   		            $link = 'index.php?option='.$option.'&amp;view=config';
						    jdownloadsViewjdownloads::quickiconButton( $link, 'config48.png', JText::_( 'COM_JDOWNLOADS_CONFIGURATION' ) );
                            $link = 'index.php?option='.$option.'&amp;view=tools';
                            jdownloadsViewjdownloads::quickiconButton( $link, 'tools48.png', JText::_( 'COM_JDOWNLOADS_TOOLS' ) );                        
                    }        

    	   		    $link = 'index.php?option='.$option.'&amp;view=info';
						    jdownloadsViewjdownloads::quickiconButton( $link, 'info48.png', JText::_( 'COM_JDOWNLOADS_TERMS_OF_USE' ) );
				    ?>

                </div>
                <div style="clear:both">&nbsp;</div>
            </div>
	    <div class="jd-cpanel-right"> 
            <div class="well">
                <?php 
                    // exist the defined upload root folder?
                    if (!is_dir($jlistConfig['files.uploaddir']) &&  $jlistConfig['files.uploaddir'] != ''){ ?>
                        <div style="align:left; margin:10px;"><font color="red"><b><?php echo JText::sprintf('COM_JDOWNLOADS_AUTOCHECK_DIR_NOT_EXIST', $jlistConfig['files.uploaddir']).'<br /><br />'.JText::_('COM_JDOWNLOADS_AUTOCHECK_DIR_NOT_EXIST_2'); ?></b></font></div> 
                <?php }  ?>
                
                <?php
                      echo JHtml::_('tabs.start', 'jdlayout-sliders-jdcp', array('useCookie' => true)); 
                      echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_PANEL_TABTEXT_STATUS'),'status');
                ?>

                <table class="jdadminpanel" width="95%" border="0">
                    <tr>
                        <th class="adminheading"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_OFFLINE_HEADER')." "; ?></th>
                    </tr>
                    <tr>
                         <td valign="top" align="left" width="100%">
                        <?php echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_TITEL').' ';
                            if ($jlistConfig['offline']) {
                                echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_OFFLINE'); ?><br /><br />
                                <?php echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_DESC_OFFLINE'); ?><br /><br />
                                <?php
                            } else {
                                echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_ONLINE'); ?><br /><br />
                                <!-- <?php // echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_DESC_ONLINE'); ?><br /><br /> -->
                                <?php
                            }
                            ?>
                            <table class="adminlist">
                                <tr>
                                    <td class="title" width="40%">
                                        <strong><?php echo JText::_( 'COM_JDOWNLOADS_STATUS' ); ?></strong>
                                    </td>
                                    <td class="title"  width="20%">
                                        <strong><?php echo JText::_( 'COM_JDOWNLOADS_PUBLISHED' ); ?></strong>
                                    </td>
                                    <td class="title" width="20%">
                                        <strong><?php echo JText::_( 'COM_JDOWNLOADS_UNPUBLISHED' ); ?></strong>
                                    </td>
                                    <td class="title"  width="20%">
                                        <strong><?php echo JText::_( 'COM_JDOWNLOADS_TOTAL' ); ?></strong>
                                    </td>                                
                                </tr>
                                <tr>
                                    <td>                    
                                        <?php echo JText::_( 'COM_JDOWNLOADS_CATEGORIES' ); ?>
                                    </td>
                                    <td style="text-align:right !IMPORTANT;">
                                        <?php echo (int)$stats_data['cats_public'];?>
                                    </td>
                                    <td style="text-align:right !IMPORTANT;">
                                        <?php echo (int)$stats_data['cats_not_public'];?>
                                    </td>
                                    <td style="text-align:right !IMPORTANT;">
                                        <?php echo (int)$stats_data['cats_public'] + (int)$stats_data['cats_not_public'];?>
                                    </td>
                                </tr>                            
                                <tr>
                                    <td>                    
                                        <?php echo JText::_( 'COM_JDOWNLOADS_DOWNLOADS' ); ?>
                                    </td>
                                    <td style="text-align:right !IMPORTANT;">
                                        <?php echo (int)$stats_data['files_public'];?>
                                    </td>
                                    <td style="text-align:right !IMPORTANT;">
                                        <?php echo (int)$stats_data['files_not_public'];?>
                                    </td>
                                    <td style="text-align:right !IMPORTANT;">
                                        <?php echo (int)$stats_data['files_public'] + (int)$stats_data['files_not_public'];?>
                                    </td>                              
                                </tr>                            
                                <tr>
                                    <td colspan="4">
                                        <?php echo $stats_data['stats']; ?>
                                    </td>
                                </tr>        
                            </table>    
                            
                      </td>
                   </tr>
                   
                   <tr>
                        <th class="adminheading"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_DOWNLOADS_HEADER')." "; ?></th>
                    </tr>
                   <tr>
                    <td>
                        <?php
                         if($jlistConfig['files.autodetect']){
                             // start the auto monitoring 
                             JDownloadsHelper::runMonitoring();
                         } else {
                           echo '<b><font color="#FF6600">'.JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_DOWNLOADS_OFF_DESC').'</font></b><br />';
                         }
                         
                         // get the secret key then we need it as link param
                         // so nobody else outside can run the script (or he know the key value - e.g. to start it via a cronjob)
                         $config = JFactory::getConfig();
                         $key = $config->get( 'secret' );                         
                         
                        ?>
                        <br />
                        <div><a href="<?php echo JURI::base();?>components/com_jdownloads/helpers/scan.php?key=<?php echo $key; ?>"  target="_blank" onclick="openWindow(this.href); return false" title="<?php echo JText::_('COM_JDOWNLOADS_RUN_MONITORING_BUTTON_TEXT');?>"><?php echo JText::_('COM_JDOWNLOADS_RUN_MONITORING_BUTTON_TEXT');?></a></div> 
                        
                        <?php echo JText::_('COM_JDOWNLOADS_RUN_MONITORING_INFO'); ?><br />
                    </td>
                   </tr>
                   
                  </table>
                  <?php
                      echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_PANEL_TABTEXT_3'),'log'); 
                  ?>
                  <table class="jdadminpanel" width="95%" border="0">
                   <tr>
                        <th class="adminheading"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_AUTOCHECK_LOG_TAB_TITLE'); 
                               if ($jlistConfig['last.log.message']) { ?> 
                                   <div><a href="index.php?option=com_jdownloads&task=tools.deletemlog"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_DELETE_LOG_LINK_TEXT');?></a></div>
                           <?php } ?>        
                         </th>
                   </tr>

                      <tr>
                         <td valign="top" align="left" width="100%">
                             <?php echo $jlistConfig['last.log.message']; ?>
                      </td>
                   </tr>
                  </table>
                  <?php
                     echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_PANEL_TABTEXT_4'),'restore_log'); 
                  ?>
                  <table class="jdadminpanel" width="95%" border="0">
                   <tr>
                        <th class="adminheading"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_RESTORE_LOG_TAB_TITLE'); 
                               if ($jlistConfig['last.restore.log']) { ?>
                                   <div><a href="index.php?option=com_jdownloads&task=tools.deleterlog"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_DELETE_LOG_LINK_TEXT');?></a></div>
                         <?php } ?>   
                        </th>
                   </tr>

                      <tr>
                         <td valign="top" align="left" width="100%">
                             <?php echo $jlistConfig['last.restore.log']; ?>
                      </td>
                   </tr>
                  </table>
                  <?php
                     echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_PANEL_TABTEXT_5'),'server_limits'); 
                  ?>
                  <table class="adminlist" width="95%" border="0">
                   <tr>
                        <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_TITLE')." "; ?></th>
                   </tr>

                      <tr class="row0">
                         <td colspan="2" valign="top" align="left" width="100%">
                             <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_DESC').'<br /><br />'; ?>
                      </td>
                      
                   </tr>
                   <tr class="row1">
                     <td width="80%">
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_FILE_UPLOADS'); ?>
                     </td>
                     <td width="20%" style="text-align:right;">
                     <?php if (get_cfg_var('file_uploads')){ echo JText::_('COM_JDOWNLOADS_YES'); } else { echo JText::_('COM_JDOWNLOADS_NO'); } ?> 
                     </td>
                   </tr>
                   <tr class="row0">  
                     <td width="80%">
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_MAX_FILESIZE'); ?>
                     </td>
                     <td width="20%" style="text-align:right;">
                     <?php echo get_cfg_var ('upload_max_filesize'); ?>
                     </td>
                   </tr>  
                   <tr class="row1">  
                     <td width="80%">
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_POST_MAX_SIZE'); ?>
                     </td>
                     <td width="20%" style="text-align:right;">
                     <?php echo get_cfg_var ('post_max_size'); ?>
                     </td>
                   </tr>  
                   <tr class="row0">  
                     <td width="80%">
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_MEMORY_LIMIT'); ?>
                     </td>
                     <td width="20%" style="text-align:right;">
                     <?php echo get_cfg_var ('memory_limit'); ?>
                     </td>
                   </tr>  
                   <tr class="row1">  
                     <td width="80%">
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_MAX_INPUT_TIME'); ?>
                     </td>
                     <td width="20%" style="text-align:right;">
                     <?php echo get_cfg_var ('max_input_time'); ?>
                     </td>
                   </tr>  
                   <tr class="row0">  
                     <td width="80%">
                     <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SERVER_INFOS_TAB_MAX_EXECUTION_TIME'); ?>
                     </td>
                     <td width="20%" style="text-align:right;">
                     <?php echo get_cfg_var ('max_execution_time'); ?>
                     </td>
                   </tr>  
                  </table>
                  <?php
                      echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_PANEL_TABTEXT_2'),'version'); 
                  ?>
                  <table class="jdadminpanel" width="95%" border="0">
                   <tr>
                        <th class="adminheading"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_PANEL_STATUS_VERSION_HEADER')." "; ?></th>
                   </tr>

                      <tr>
                         <td valign="top" align="left" width="100%">
                             <?php echo '<b><font color="#990000">jDownloads '.JText::_('COM_JDOWNLOADS_BACKEND_PANEL_TABTEXT_2').' '. $this->jdVersion.'</font></b>';?>
                      </td>
                   </tr>
                  </table>
                  <?php
                    echo JHtml::_('tabs.end');                    
                  ?>    
                
            </div>
        </div>
     </div>
     </div>

     <input type="hidden" name="option" value="com_jdownloads" />
     <input type="hidden" name="task" value="" />
     <input type="hidden" name="boxchecked" value="0" />
     <input type="hidden" name="controller" value="jdownloads" />
     </form>
     
    <?php 
    
    } else {
        // we must try to move the old data 
        
        $update_task    = 'index.php?option=com_jdownloads&task=tools.runOldVersionUpdate'; 
        $no_update_task = 'index.php?option=com_jdownloads&task=tools.deactivateUpdate&x=1'; 

        ?>
        <form action="index.php" method="post" name="adminForm">
            <div id="editcell">
                <div class="adminform">
                      <div id="cpanel" style="font-size:12px;">
                          <?php 
                            if ($component_rules == '{}'){
                                // 1. view msg when not permissions are defined
                                echo '<p align="center"><big>'.JText::_('COM_JDOWNLOADS_UPDATE_FOUND_VERSION_INFO').'<br /><br />';
                                echo  JText::_('COM_JDOWNLOADS_UPDATE_SET_PERMISSIONS_INFO').'<br />';
                                echo  JText::_('COM_JDOWNLOADS_SET_PERMISSIONS_INFO').'<br /><br /></big>';
                                echo  JText::_('COM_JDOWNLOADS_SET_PERMISSIONS_INFO_HINT').'<br /><br />';
                                echo  JText::_('COM_JDOWNLOADS_ACCESS_ONLY_FOR_SUPER_USERS').'</p>';
                            } else {
                                 // 2. rules are defined so we can view the import buttons
                                 echo '<p align="center"><big>'.JText::_('COM_JDOWNLOADS_UPDATE_FOUND_VERSION_INFO').'<br /><br />';
                                 echo JText::_('COM_JDOWNLOADS_UPDATE_RUN_UPDATE_INFO').'<br /></p>';
                                 echo '<p align="center"><a href="'.$update_task.'">'.JText::_('COM_JDOWNLOADS_UPDATE_RUN_UPDATE_LINK').'</a><br /><br />';
                                 echo JText::_('COM_JDOWNLOADS_UPDATE_NOT_RUN_UPDATE_INFO').'<br /></p>';
                                 echo '<p align="center"><a href="'.$no_update_task.'">'.JText::_('COM_JDOWNLOADS_UPDATE_NOT_RUN_UPDATE_LINK').'</a><br />'.'</big></p>';
                            }
                          ?>
                      </div>
                </div>
            </div>
        </form>        
                
    <?php } ?>