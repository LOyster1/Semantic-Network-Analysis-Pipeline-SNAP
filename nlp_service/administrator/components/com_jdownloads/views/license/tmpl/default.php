<?php defined('_JEXEC') or die('Restricted access'); 

global $jlistConfig;

//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'license.cancel' || document.formvalidator.isValid(document.id('license-form'))) {
            <?php // echo $this->form->getField('license_text')->save(); ?>
            Joomla.submitform(task, document.getElementById('license-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('COM_JDOWNLOADS_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="license-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo empty($this->item->id) ? JText::_('COM_JDOWNLOADS_LICEDIT_ADD') : JText::sprintf('COM_JDOWNLOADS_LICEDIT_EDIT', $this->item->id); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?></li>

                <li><?php echo $this->form->getLabel('alias'); ?>
                <?php echo $this->form->getInput('alias'); ?></li>
                
                <li><?php echo $this->form->getLabel('url'); ?>
                <?php echo $this->form->getInput('url'); ?></li>
                
                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>
              <!--  
                <li><?php echo $this->form->getLabel('ordering'); ?>
                <?php echo $this->form->getInput('ordering'); ?></li>
              -->
                <li><?php echo $this->form->getLabel('language'); ?>
                <?php echo $this->form->getInput('language'); ?></li>
                
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
            </ul>

            <div>
                <?php echo $this->form->getLabel('description'); ?>
                 <?php 
                 if (!$jlistConfig['licenses.editor']){ 
                     // use a simple textarea instead editor
                     $this->form->setFieldAttribute( 'description', 'type', 'textarea' );
                     $this->form->setFieldAttribute( 'description', 'rows', '10' );
                     $this->form->setFieldAttribute( 'description', 'cols', '80' );
                 } else {
                     ?> <div class="clr"></div> <?php
                 }
                 echo $this->form->getInput('description'); 
                 ?>       
            </div>
        </fieldset>
    </div>                

<div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start', 'jdlicense-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_HELP_INFORMATIONS'), 'publishing-details'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li><p><?php echo JText::_('COM_JDOWNLOADS_LICEDIT_EDIT_LANGUAGE_NOTE'); ?></p>
                </li>   
            </ul>
        </fieldset>    

        <?php echo JHtml::_('sliders.end'); ?>
        
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="license" />         
        
        <?php echo JHtml::_('form.token'); ?>
    </div>
    <div class="clr"></div>    
</form>    
    
