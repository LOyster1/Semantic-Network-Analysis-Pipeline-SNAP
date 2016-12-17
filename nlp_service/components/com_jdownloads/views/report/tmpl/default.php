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
 
defined('_JEXEC') or die('Restricted access');

    global $jlistConfig;

    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
   
    $jinput = JFactory::getApplication()->input; 
    
    $captcha_valid  = false;
    $captcha_invalid_msg = '';

    if ($this->user_rules->view_captcha){
        // get captcha plugin
        JPluginHelper::importPlugin('captcha');
        $plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');

        // Get plugin param
        $pluginParams = new JRegistry($plugin->params);
        $captcha_version = $pluginParams->get('version');        
        
        $dispatcher = JDispatcher::getInstance();
        $dummy = $jinput->getString('g-recaptcha-response');
        if (!$dummy) $dummy = $jinput->getString('recaptcha_response_field');        
        
        // check now whether user has used the captcha already
        if (isset($dummy)){
                $captcha_res = $dispatcher->trigger('onCheckAnswer', $dummy);
                if (!$captcha_res[0]){
                    // init again for next try
                    $dispatcher->trigger('onInit','dynamic_recaptcha_1');
                    $captcha_invalid_msg = JText::_('COM_JDOWNLOADS_FIELD_CAPTCHA_INCORRECT_HINT');
                } else {
                    $captcha_valid = true;
                }
        } else {
            // init for first try
            $exist_event = $dispatcher->trigger('onInit','dynamic_recaptcha_1');
            
            // When plugin event not exist, we must do the work without it. But give NOT a public info about this problem.
            if (!$exist_event){
                $captcha_valid = true;
            }
        }    
    } else {
        // we need this switch to handle the data output 
        $captcha_valid = true;
    }    

    JHtml::_('behavior.keepalive');
    JHtml::_('behavior.tooltip');
    JHtml::_('behavior.calendar');
    JHTML::_('behavior.formvalidation');
    // JHtml::_('behavior.formvalidator'); Joomla >= 3.4

    // required for captcha
    $form_uri = JFactory::getURI();
    $form_uri = $form_uri->toString();
    $form_uri = $this->escape($form_uri);    
    
    // Create shortcuts to some parameters.
    $params     = $this->item->params;
    $user       = JFactory::getUser();    

?>
    <script type="text/javascript">
        Joomla.submitbutton = function(task) {
            if (task == 'report.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
                Joomla.submitform(task);
            } else {
                alert('<?php echo $this->escape(JText::_('COM_JDOWNLOADS_VALIDATION_FORM_FAILED'));?>');
            }
        }
    </script>       


<div class="edit jd-item-page<?php echo $this->pageclass_sfx; ?>">

    <?php

    $is_admin   = false;

    if (JDHelper::checkGroup('8', true) || JDHelper::checkGroup('7', true)){
        $is_admin = true;
    }    

    // view offline message - but admins can view it always    
    if ($jlistConfig['offline'] && !$is_admin){
        if ($jlistConfig['offline.text'] != '') {
            echo JDHelper::getOnlyLanguageSubstring($jlistConfig['offline.text']).'</div>';
        }
    } else {         
    ?>

<form action="<?php echo $form_uri; ?>" name="adminForm" method="post" id="adminForm" class="form-validate" accept-charset="utf-8">

        <fieldset>
            <?php echo JText::_('COM_JDOWNLOADS_REPORT_INFO'); ?> 

            <legend>
                <?php echo JText::_('COM_JDOWNLOADS_FRONTEND_REPORT_FILE_LINK_TEXT'); ?>
            </legend>
            
            <?php 
                // view it only when captcha_valid var is false  
                if (!$captcha_valid){
                    echo ' '.JText::_('COM_JDOWNLOADS_FORM_VERIFY_HUMAN'); 
                          
                    // add captcha option when required
                    if ($captcha_valid === false){
                        // html code inside form tag
                        // we use for the form action the request_uri
                        $captcha = '<div id="jd_container" class="jd_recaptcha">';
                        $captcha .= '<div id="dynamic_recaptcha_1"></div>';
                        $captcha .= '<br /><input type="submit" name="submit" id="jd_captcha" class="button" value="'.JText::_('COM_JDOWNLOADS_FORM_BUTTON_TEXT').'" />';
                        if ($captcha_invalid_msg != ''){
                            $captcha .= $captcha_invalid_msg;
                        } 
                        $captcha .= '</div>';
                        echo $captcha;
                    }  
        
                } else { ?>
                
                    <div class="formelm-buttons">
                        <button type="button" onclick="Joomla.submitbutton('report.send')">
                            <?php echo JText::_('COM_JDOWNLOADS_SEND'); ?>
                        </button>
                        <button type="button" onclick="Joomla.submitbutton('report.cancel')">
                            <?php echo JText::_('COM_JDOWNLOADS_CANCEL'); ?>
                        </button>        
                    </div>
                    
                    <div class="formelm">
                        <?php echo $this->form->getLabel('name'); ?>
                        <?php echo $this->form->getInput('name'); ?>
                    </div>

                    <div class="formelm">
                        <?php echo $this->form->getLabel('email'); ?>
                        <?php echo $this->form->getInput('email'); ?>
                    </div>
                    
                    <div class="formelm">
                        <?php echo $this->form->getLabel('cat_id'); ?>
                        <?php echo $this->form->getInput('cat_id'); ?>
                    </div>

                    <div class="formelm">
                        <?php echo $this->form->getLabel('file_id'); ?>
                        <?php echo $this->form->getInput('file_id'); ?>
                    </div>                     

                    <div class="formelm">
                        <?php echo $this->form->getLabel('cat_title'); ?>
                        <?php echo $this->form->getInput('cat_title'); ?>
                    </div>
                    
                    <div class="formelm">
                        <?php echo $this->form->getLabel('file_title'); ?>
                        <?php echo $this->form->getInput('file_title'); ?>
                    </div>

                    <div class="formelm">
                        <?php echo $this->form->getLabel('url_download'); ?>
                        <?php echo $this->form->getInput('url_download'); ?>
                    </div>                    

                    <div class="formelm">
                        <?php echo $this->form->getLabel('reason'); ?>
                        <?php echo $this->form->getInput('reason'); ?>
                    </div>

                    <div class="formelm">
                        <?php echo $this->form->getLabel('text'); ?>
                        <?php echo $this->form->getInput('text'); ?>
                    </div>                
        
        <?php } ?>
        
        </fieldset>
        
        <input type="hidden" name="task" value="report" />
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />         
        
        <?php echo JHtml::_('form.token'); ?>

    <div class="clr"></div>
    </form>
    
    <?php } ?>

    </div>