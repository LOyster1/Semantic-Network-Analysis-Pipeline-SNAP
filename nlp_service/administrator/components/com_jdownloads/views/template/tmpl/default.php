<?php defined('_JEXEC') or die('Restricted access'); 

global $jlistConfig;

JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
jimport( 'joomla.html.html.tabs' );

if ($this->item->template_typ == NULL ){
    // add a new layout - so we need the layout type number 
    $session = JFactory::getSession();
    $this->item->template_typ = (int) $session->get( 'jd_tmpl_type', '' );
}

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'template.cancel' || document.formvalidator.isValid(document.id('template-form'))) {
            <?php // echo $this->form->getField('template_text')->save(); ?>
            Joomla.submitform(task, document.getElementById('template-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('COM_JDOWNLOADS_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=template&layout=edit&id='.(int) $this->item->id.'&type='.(int)$this->item->template_typ); ?>" method="post" name="adminForm" id="template-form" accept-charset="utf-8" class="form-validate">
    <?php echo JHtml::_('tabs.start', 'jdlayout-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
    <?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_EDIT'), 'layout'); ?>
    <table width="100%"><tr><td>
    <div class="width-60 fltlft">
        
        <fieldset class="adminform">
            
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('template_name'); ?>
                <?php echo $this->form->getInput('template_name'); ?></li>

                <li><?php echo $this->form->getLabel('note'); ?>
                <?php echo $this->form->getInput('note'); ?></li>
                
                <?php if($this->item->template_typ == 1 || $this->item->template_typ == 2 || $this->item->template_typ == 4) { ?>
                    <li><?php echo $this->form->getLabel('cols'); ?>
                    <?php echo $this->form->getInput('cols'); ?></li>
                <?php } ?>
                
                <?php if($this->item->template_typ == 1) { ?>    
                    <li><?php echo $this->form->getLabel('use_to_view_subcats'); ?>
                    <?php echo $this->form->getInput('use_to_view_subcats'); ?></li>
                <?php } ?>
                
                <?php if($this->item->template_typ == 2) { ?>
                    <li><?php echo $this->form->getLabel('checkbox_off'); ?>
                    <?php echo $this->form->getInput('checkbox_off'); ?></li>
                <?php } ?>    

                <?php if($this->item->template_typ == 2) { ?>
                    <li><?php echo $this->form->getLabel('symbol_off'); ?>
                    <?php echo $this->form->getInput('symbol_off'); ?></li>
                <?php } ?>
                
                <li><?php echo $this->form->getLabel('language'); ?>
                <?php echo $this->form->getInput('language'); ?></li>
                
                <li><?php echo $this->form->getLabel('template_typ'); ?>
                <?php echo $this->form->getInput('template_typ'); ?></li>
                
                <li><?php echo $this->form->getLabel('locked'); ?>
                <?php echo $this->form->getInput('locked'); ?></li>
                
                <?php if (!empty($this->item->id)){ ?>
                    <li><?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?></li>
                <?php } ?>
            </ul>

            <?php if($this->item->template_typ == 1 || $this->item->template_typ == 2 || $this->item->template_typ == 4) { ?> 
                <div>
                    <?php echo $this->form->getLabel('template_before_text'); ?>
                     <?php 
                     if (!$jlistConfig['layouts.editor']){ 
                         // use a simple textarea instead editor
                         $this->form->setFieldAttribute( 'template_before_text', 'type', 'textarea' );
                         $this->form->setFieldAttribute( 'template_before_text', 'rows', '5' );
                         $this->form->setFieldAttribute( 'template_before_text', 'cols', '100' );
                     } else {
                         ?> <div class="clr"></div> <?php
                     }
                     echo $this->form->getInput('template_before_text'); 
                     ?>       
                </div>            
            <?php } ?> 
                            
            <div>
                <?php 
                if ($this->item->template_typ == 7){
                    $this->form->setFieldAttribute( 'template_text', 'description', '' ); 
                } 
                
                echo $this->form->getLabel('template_text'); ?>
                
                 <?php 
                 if (!$jlistConfig['layouts.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'template_text', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'template_text', 'rows', '30' );
                     $this->form->setFieldAttribute( 'template_text', 'cols', '100' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('template_text'); 
                 ?>       
            </div>

            <?php if($this->item->template_typ == 1 || $this->item->template_typ == 2 || $this->item->template_typ == 4) { ?> 
                <div>
                    <?php echo $this->form->getLabel('template_after_text'); ?>
                     <?php 
                     if (!$jlistConfig['layouts.editor']){ 
                         // use a simple textarea instead editor
                         $this->form->setFieldAttribute( 'template_after_text', 'type', 'textarea' );
                         $this->form->setFieldAttribute( 'template_after_text', 'rows', '5' );
                         $this->form->setFieldAttribute( 'template_after_text', 'cols', '100' );
                     } else {
                         ?> <div class="clr"></div> <?php
                     }
                     echo $this->form->getInput('template_after_text'); 
                     ?>       
                </div>            
            <?php } ?> 
                       
        </fieldset>

       <fieldset class="adminform">
            <ul class="adminformlist">
                <?php 
                switch ($this->item->template_typ){
                    case 1:
                        // categories
                        ?><li><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT'); ?></li>
                        <li><img src="components/com_jdownloads/assets/images/categories_layout.gif" alt="Example" border="1" /></li>
                        <?php 
                        break;
                    case 2:
                        // files
                        ?><li><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT'); ?></li>
                        <li><img src="components/com_jdownloads/assets/images/files_layout.gif" alt="Example" border="1" /></li>
                        <?php 
                        break;
                    case 3:
                        // summary 
                        ?><li><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT'); ?></li>
                        <li><img src="components/com_jdownloads/assets/images/summary_layout.gif" alt="Example" border="1" /></li>
                        <?php 
                        break;
                    case 4:
                        // category
                        ?><li><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT'); ?></li>
                        <li><img src="components/com_jdownloads/assets/images/category_layout.gif" alt="Example" border="1" /></li>
                        <?php 
                        break;
                    case 5:
                        // details
                        ?><li><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT'); ?></li>
                        <li><img src="components/com_jdownloads/assets/images/details_layout.gif" alt="Example" border="1" /></li>
                        <?php 
                        break;
                    case 7:
                        // search
                        ?><li><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT'); ?></li>
                        <li><img src="components/com_jdownloads/assets/images/search_layout.gif" alt="Example" border="1" /></li>
                        <?php 
                        break;
                } ?>
                    
                

            </ul>         
        
        </fieldset>
       
    </div>                

    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start', 'jdlayout-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_HELP_INFORMATIONS'), 'help-details'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li>
                <?php if($this->item->template_typ == 1) {      // Categories
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK').'</p>'; ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC'); ?></p>
                    <!-- <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC2'); ?></p> -->
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC3'); ?></p>
                <?php } ?>    

                <?php if($this->item->template_typ == 2) {      // Files/Downloads
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DESC'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FILES_DESC2'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_CUSTOM_FIELD_INFO'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_INFO_LIGHTBOX'); ?></p>
                <?php } ?>                 

                <?php if($this->item->template_typ == 3) {    // Summary
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_FINAL_DESC'); ?></p>
                <?php } ?>                 
                <?php if($this->item->template_typ == 4) {      // Category
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CAT_DESC'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC2'); ?></p> 
                <?php } ?>                 
                <?php if($this->item->template_typ == 5) {      // Details View
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_DETAILS_DESC'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_DETAILS_DESC_FOR_TABS'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_CUSTOM_FIELD_INFO'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_INFO_LIGHTBOX'); ?></p> 
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_INFO_LIGHTBOX2'); ?></p>                    
                <?php } ?>                 
                <?php if($this->item->template_typ == 6) {      // Upload Form
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_UPLOADS_DESC'); ?></p>
                    <p><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_CUSTOM_FIELD_INFO'); ?></p>
                <?php } ?>                 
                <?php if($this->item->template_typ == 7) {      // Search Result
                             echo '<p><b>'.JText::_('COM_JDOWNLOADS_TITLE').'</b>:<br />';
                             echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); ?>
                    <p><?php echo '<b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT').'</b>:<br />'; ?>                                       
                       <?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_SEARCH_DESC'); ?></p>
                <?php }
                             echo '<p>'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_TAG_TIP').'</p>';
                ?>                 
                
                </li>   
            </ul>
        </fieldset>    
        </div>    
        <?php echo JHtml::_('sliders.end'); ?>
     </td></tr></table>
        
     <!-- start 2. panel -->   
        <?php echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TABTEXT_EDIT_HEADER'), 'layout2'); ?>
        <table width="100%"><tr><td>
        <div class="width-60 fltlft">
        <fieldset class="adminform">
            <div>
                <?php echo $this->form->getLabel('template_header_text'); ?>
                 <?php 
                 if (!$jlistConfig['layouts.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'template_header_text', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'template_header_text', 'rows', '10' );
                     $this->form->setFieldAttribute( 'template_header_text', 'cols', '80' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('template_header_text'); 
                 ?>       
            </div>
            
            <div>
                <?php 
                if ($this->item->template_typ == 7){
                    $this->form->setFieldAttribute( 'template_subheader_text', 'description', JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_SEARCH_DESC') ); 
                } 
               
                echo $this->form->getLabel('template_subheader_text'); ?>

                 <?php 
                 if (!$jlistConfig['layouts.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'template_subheader_text', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'template_subheader_text', 'rows', '10' );
                     $this->form->setFieldAttribute( 'template_subheader_text', 'cols', '80' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('template_subheader_text'); 
                 ?>       
            </div>
            
            <div>
                <?php 
                if ($this->item->template_typ == 7){
                    $this->form->setFieldAttribute( 'template_footer_text', 'description', JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_FOOTER_OTHER_DESC') );
                }
                
                echo $this->form->getLabel('template_footer_text'); ?>
                <?php 
                if (!$jlistConfig['layouts.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'template_footer_text', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'template_footer_text', 'rows', '10' );
                     $this->form->setFieldAttribute( 'template_footer_text', 'cols', '80' );
                } else {
                     ?> <div class="clr"></div> <?php
                }
                echo $this->form->getInput('template_footer_text'); 
                ?>       
            </div>            
            
        </fieldset>           
        </div>
 <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start', 'jdlayout-sliders-2'.$this->item->id, array('useCookie'=>1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_HELP_INFORMATIONS'), 'help-details2'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li>
                      <?php echo '<p><b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_HEADER_TEXT').'</b>:<br />';
                            echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_HEADER_DESC').'</p>'; ?>
                             
                      <?php echo '<p><b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_TEXT').'</b>:<br />';
                            switch ($this->item->template_typ) {
                                case 1:  //cats
                                case 2:  //files
                                case 4:  //cat
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_DESC').'</p>';
                                    break;
                                case 5:  //details                                   
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_DETAIL_DESC').'</p>';
                                    break;                                     
                                case 3:  //summary                                   
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_SUMMARY_DESC').'</p>';
                                    break;
                                case 6:  //upload form                                   
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_DESC').'</p>';
                                    break;                                    
                                case 7:  //search results                                   
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_SUBHEADER_SEARCH_DESC').'</p>';
                                    break;                                    
                       } ?>                       
                       <?php echo '<p><b>'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_FOOTER_TEXT').'</b>:<br />';
                            switch ($this->item->template_typ) {
                                case 1:  //cats
                                case 2:  //files
                                case 4:  //cat
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_FOOTER_FILES_CATS_DESC').'</p>';
                                    break;
                                default:  //other types                                   
                                    echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_FOOTER_OTHER_DESC').'</p>';
                                    break;
                        } ?>
                </li>   
            </ul>
        </fieldset>    
        </div>    
        <?php echo JHtml::_('sliders.end'); ?>

        </td></tr></table>
            
        <?php echo JHtml::_('tabs.end'); ?>
    
        <input type="hidden" name="task" value="" />
        
        <input type="hidden" name="hidemainmenu" value="0">        
        <input type="hidden" name="templocked" value="<?php echo $this->item->locked; ?>">
        <input type="hidden" name="tempname" value="<?php echo $this->item->template_name; ?>">
        <input type="hidden" name="type" value="<?php echo $this->item->template_typ; ?>">
        <input type="hidden" name="view" value="" />
        <?php echo JHtml::_('form.token'); ?>
    
    <div class="clr"></div>    
</form>
    
