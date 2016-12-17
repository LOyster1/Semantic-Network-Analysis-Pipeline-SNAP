<?php

 
defined('_JEXEC') or die('Restricted access');

global $jlistConfig; 

JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
jimport( 'joomla.form.form' );
$canDo = jdownloadsHelper::getActions();

// get DB prefix string and table list to check whether it exists backup tables
$db = JFactory::getDBO();
$prefix     = JDownloadsHelper::getCorrectDBPrefix();
$tablelist  = $db->getTableList();
$old_version_found = false;  
if (in_array ( $prefix.'jdownloads_config_backup', $tablelist)){
    $old_version_found = true;
}

?>

<script type="text/javascript">
    function confirmAction(task)
    {
        if (task == 'resetDownloadCounter' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.resetDownloadCounter"
            }
        }
        
        if (task == 'cleanImageFolders' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.cleanImageFolders"
            }
        }
        
        if (task == 'cleanPreviewFolder' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.cleanPreviewFolder"
            }
        }                 
        
        if (task == 'resetCom' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.resetCom"
            }
        }
        
        if (task == 'resetCategoriesRules' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.resetCategoriesRules"
            }
        }
        
        if (task == 'resetDownloadsRules' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.resetDownloadsRules"
            }
        }         
        
        if (task == 'resetBatchSwitch' ){
                window.location="index.php?option=com_jdownloads&task=tools.resetBatchSwitch"
        }
        
        if (task == 'installSampleData' ){
                window.location="index.php?option=com_jdownloads&task=category.installSampleData"
        }        
        
        if (task == 'runBackup' ){
                window.location="index.php?option=com_jdownloads&view=backup"
        }        
        
        if (task == 'runRestore' ){
                window.location="index.php?option=com_jdownloads&view=restore"
        } 
        
        if (task == 'deleteBackupTables' ){
            var x = confirm("<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_CONFIRM'); ?>")
            if (x == true){     
                window.location="index.php?option=com_jdownloads&task=tools.deleteBackupTables"
            }
        }         

    }
</script>


<?php

// check user access rights
if ($canDo->get('edit.config'))
{ 

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
            <legend> <?php echo JText::_('COM_JDOWNLOADS_TOOLS')." "; ?> </legend>
            <div class="infotext">
            <table class="adminForm" width="90%" cellspacing="2" cellpadding="10" border="0">
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="runBackup" value="<?php echo JText::_('COM_JDOWNLOADS_BACKUP'); ?>" onclick="confirmAction('runBackup')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_BACKUP_INFO_SHORT_DESC'); ?>
                </td>
                </tr>                

                <tr>
                <td width="25%">
                  <input type="button" class="button" name="runRestore" value="<?php echo JText::_('COM_JDOWNLOADS_RESTORATION'); ?>" onclick="confirmAction('runRestore')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_RESTORE_FILE_DESC'); ?>
                </td>
                </tr>                   
                
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="installSample" value="<?php echo JText::_('COM_JDOWNLOADS_SAMPLE_DATA_BE_OPTION_LINK_TEXT'); ?>" onclick="confirmAction('installSampleData')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_SAMPLE_DATA_BE_OPTION_LINK_TEXT_DESC'); ?>
                </td>
                </tr>
                
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="resetcounter" value="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_TITEL'); ?>" onclick="confirmAction('resetDownloadCounter')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_RESET_COUNTER_DESC'); ?>
                </td>
                </tr>

                <tr>
                <td width="25%">
                  <input type="button" class="button" name="resetCategoriesRules" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_CAT_RULES_TITLE'); ?>" onclick="confirmAction('resetCategoriesRules')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_CAT_RULES_DESC'); ?>
                </td>
                </tr>

                <tr>
                <td width="25%">
                  <input type="button" class="button" name="resetDownloadsRules" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_DOWNLOADS_RULES_TITLE'); ?>" onclick="confirmAction('resetDownloadsRules')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_DOWNLOADS_RULES_DESC'); ?>
                </td>
                </tr>
                
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="cleanImageFolder" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PICS'); ?>" onclick="confirmAction('cleanImageFolders')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PICS_DESC'); ?>
                </td>
                </tr>
                
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="cleanPreviewFolder" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PREVIEWS'); ?>" onclick="confirmAction('cleanPreviewFolder')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_DELETE_NOT_USED_PREVIEWS_DESC'); ?>
                </td>
                </tr>                                                 

                <?php if ($old_version_found){ ?>
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="deleteBackupTables" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_DELETE_BACKUP_TABLES'); ?>" onclick="confirmAction('deleteBackupTables')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_DELETE_BACKUP_TABLES_DESC'); ?>
                </td>
                </tr>
                <?php } ?>
                
                <tr>
                <td width="25%">
                  <?php 
                        if ((int)$jlistConfig['categories.batch.in.progress'] == 1 || (int)$jlistConfig['downloads.batch.in.progress'] == 1) { ?>
                            <input type="button" class="button" name="resetbatch" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_BATCH'); ?>" onclick="confirmAction('resetBatchSwitch')">
                  <?php } else {
                            echo '<input type="button" class="button" disabled value="'.JText::_('COM_JDOWNLOADS_TOOLS_RESET_BATCH').'">'; 
                        } 
                  ?>
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_BATCH_DESC'); ?>
                </td>
                </tr>

            </table>
            </div>
        </fieldset>
    </div>          
    <?php if ($jlistConfig['com'] != ''){ ?>
    <div> 
        <fieldset style="background-color: #ffffff; margin-top:5px;" class="uploadform">
            <legend></legend> 
                <div class="infotext">
                <table class="adminForm" width="90%" cellspacing="2" cellpadding="10" border="0">
                <tr>
                <td width="25%">
                  <input type="button" class="button" name="resetbatch" value="<?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_COM'); ?>" onclick="confirmAction('resetCom')">
                </td>
                <td width="75%">
                  <?php echo JText::_('COM_JDOWNLOADS_TOOLS_RESET_COM_DESC'); ?>
                </td>
                </tr>
                </table>
                </div>
         </fieldset>
    </div> 
    <?php } ?>
    
    <input type="hidden" name="option" value="com_jdownloads" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="tools" />
    <input type="hidden" name="hidemainmenu" value="0" />
   </form>

<?php
   } else {
?>           
    <form action="index.php" method="post" name="adminForm" id="adminForm">
    <div>
        <fieldset style="background-color: #ffffff; margin-top:5px;" class="infotext">
            <div class="jdwarning">
                 <?php echo '<b>'.JText::_('COM_JDOWNLOADS_ALERTNOAUTHOR').'</b>'; ?>
            </div>
        </fieldset>
    </div>
    </form>           
<?php
   }    
?>