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

defined('_JEXEC') or die;

global $jlistConfig;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// JHtml::_('behavior.formvalidator'); Joomla >= 3.4
JHtml::_('behavior.keepalive');

jimport( 'joomla.form.form' );

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'category.cancel' || document.formvalidator.isValid(document.id('category-form'))) {
            Joomla.submitform(task, document.getElementById('category-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('COM_JDOWNLOADS_VALIDATION_FORM_FAILED'));?>');
        }
    }
    
    // get the selected file name to view the cat pic new 
    function getSelectedText( frmName, srcListName ) 
    {
        var form = eval( 'document.' + frmName );
        var srcList = eval( 'form.' + srcListName );

        i = srcList.selectedIndex;
        if (i != null && i > -1) {
            return srcList.options[i].text;
        } else {
            return null;
        }
    }    
</script>

<form accept-charset="utf-8" action="<?php echo JRoute::_('index.php?option=com_jdownloads&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="category-form" enctype="multipart/form-data" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo empty($this->item->id) ? JText::_('COM_JDOWNLOADS_EDIT_CAT_ADD') : JText::sprintf('COM_JDOWNLOADS_EDIT_CAT_EDIT', $this->item->id); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?></li>

                <li><?php echo $this->form->getLabel('alias'); ?>
                <?php echo $this->form->getInput('alias'); ?></li>
                
                <?php
                if ($jlistConfig['create.auto.cat.dir']){
                    if ($this->item->id){ 
                       // change category
                       ?>
                       <li>
                       <?php $this->form->setFieldAttribute( 'cat_dir',  'readonly', 'true' );
                             $this->form->setFieldAttribute( 'cat_dir', 'required', 'false' );
                             $this->form->setFieldAttribute( 'cat_dir', 'class', 'readonly' );
                             $this->form->setFieldAttribute( 'cat_dir', 'description', JText::_('COM_JDOWNLOADS_EDIT_CAT_DIR_TITLE_MSG') );
                             echo $this->form->getLabel('cat_dir'); ?>
                       <?php echo $this->form->getInput('cat_dir'); ?></li>
                       <?php 
                     } else { 
                       // add category 
                       ?>
                       <li>
                       <?php echo $this->form->getLabel('cat_dir_parent'); ?>
                       <?php echo $this->form->getInput('cat_dir_parent'); ?></li>
                       <?php
                     }    
                 } else {
                     // auto creation is set off
                     ?>
                       <li><?php echo $this->form->getLabel('cat_dir'); ?>
                           <?php echo $this->form->getInput('cat_dir'); ?></li>
                 <?php } ?>
                             
                <li><?php echo $this->form->getLabel('parent_id'); ?>
                <?php echo $this->form->getInput('parent_id'); ?></li>

                <li><?php echo $this->form->getLabel('published'); ?>
                <?php echo $this->form->getInput('published'); ?></li>

                <li><?php echo $this->form->getLabel('access'); ?>
                <?php echo $this->form->getInput('access'); ?></li>
                
                <?php if ($this->canDo->get('core.admin')): ?>
                    <li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
                        <div class="button2-left"><div class="blank">
                            <button type="button" onclick="document.location.href='#access-rules';">
                                <?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
                            </button>
                        </div></div>
                    </li>
                <?php endif; ?>                

                <!--<li><?php // echo $this->form->getLabel('ordering'); ?>
                <?php // echo $this->form->getInput('ordering'); ?></li>
                -->

                <li><?php echo $this->form->getLabel('tags'); ?>
                <?php echo $this->form->getInput('tags'); ?></li> 
                
                <li><?php echo $this->form->getLabel('language'); ?>
                <?php echo $this->form->getInput('language'); ?></li>
                
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
            </ul>
            <div>
                <?php echo $this->form->getLabel('description'); ?>
                 <?php 
                 if (!$jlistConfig['categories.editor']){ 
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

                <?php echo $this->form->getInput('level'); ?>
                <?php echo $this->form->getInput('lft'); ?>
                <?php echo $this->form->getInput('rgt'); ?>
                

           
        </fieldset>
    </div>                

    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start','category-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

        <!-- publishing details -->
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_PUBLISHING_DETAILS'), 'publishing-details'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('created_user_id'); ?>
                <?php echo $this->form->getInput('created_user_id'); ?></li>

                 <?php if ($this->item->created_time) : ?>
                <li><?php echo $this->form->getLabel('created_time'); ?>
                <?php echo $this->form->getInput('created_time'); ?></li>
                <?php endif; ?>
                
                <?php if ($this->item->modified_user_id) : ?>
                    <li><?php echo $this->form->getLabel('modified_user_id'); ?>
                    <?php echo $this->form->getInput('modified_user_id'); ?></li>

                    <li><?php echo $this->form->getLabel('modified_time'); ?>
                    <?php echo $this->form->getInput('modified_time'); ?></li>
                <?php endif; ?>

                <?php if ($this->item->views) : ?>
                    <li><?php echo $this->form->getLabel('views'); ?>
                    <?php echo $this->form->getInput('views'); ?></li>
                <?php endif; ?>
            </ul>
        </fieldset>         

        <!-- basic -->
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_ADDITIONAL_DATA'), 'basic-details'); ?>
        <fieldset class="panelform">
            <ul class="adminformlist">
                
                <li><?php echo $this->form->getLabel('pic'); ?>
                <?php echo $this->form->getInput('pic'); ?></li>
                
                <li><label></label>
                <script language="javascript" type="text/javascript">
                    if (document.adminForm.pic.options.value!=''){
                        jsimg="<?php echo JURI::root().'images/jdownloads/catimages/'; ?>" + getSelectedText( 'adminForm', 'pic' );
                    } else {
                        jsimg='';
                    }
                    document.write('<img src=' + jsimg + ' name="imagelib" width="<?php echo $jlistConfig['cat.pic.size']; ?>" height="<?php echo $jlistConfig['cat.pic.size']; ?>" border="1" alt="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_DEFAULT_CAT_FILE_NO_DEFAULT_PIC'); ?>" />');
                </script></li>

                <li><?php echo $this->form->getLabel('picnew'); ?> 
                <input name="picnew" size="30"  type="file"/>
                <?php // echo $this->form->getInput('picnew'); ?></li>
                
                 <li><?php echo $this->form->getLabel('spacer'); ?></li>
                
                <!--
                <li><?php echo $this->form->getLabel('password'); ?>
               <?php echo $this->form->getInput('password'); ?></li>
                -->
                
                <li><?php echo $this->form->getLabel('notes'); ?>
                <?php echo $this->form->getInput('notes'); ?></li>                                
            
            </ul>
        </fieldset>        
        
        <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_METADATA_OPTIONS'), 'meta-data'); ?>
        
        <fieldset class="panelform">
            <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('metadesc'); ?>
                    <?php echo $this->form->getInput('metadesc'); ?></li>

                    <li><?php echo $this->form->getLabel('metakey'); ?>
                    <?php echo $this->form->getInput('metakey'); ?></li>
                    
                    <li><?php echo $this->form->getLabel('robots'); ?>
                    <?php echo $this->form->getInput('robots'); ?></li>
            </ul>
        </fieldset> 

        <?php echo JHtml::_('sliders.end'); ?>
        </div>
        <!-- begin ACL definition-->
        <div class="clr"></div>
            <?php 
            if ($this->canDo->get('core.admin')): ?>
                  <?php if (empty($this->item->id)){ ?>
                        <div class="jdwarning"><?php echo JText::_('COM_JDOWNLOADS_SET_CREATE_PERMISSIONS_WARNING'); ?></div>                  
                  <?php } ?>
                  <div class="width-100 fltlft">
                       <?php echo JHtml::_('sliders.start', 'jd-category-permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
                       <?php echo JHtml::_('sliders.panel', JText::_('COM_JDOWNLOADS_CATEGORY_RULES').' : '. $this->form->getValue('title'), 'access-rules'); ?>
                       <fieldset class="panelform">
                           <?php echo $this->form->getLabel('rules'); ?>
                           <?php echo $this->form->getInput('rules'); ?>
                       </fieldset>
                       <?php echo JHtml::_('sliders.end'); ?>
                  </div>
            <?php endif; ?>
        <!-- end ACL definition-->        
        <div>
        <?php echo $this->form->getInput('level'); ?>
        <?php echo $this->form->getInput('lft'); ?>
        <?php echo $this->form->getInput('rgt'); ?>
        <?php
            if ($jlistConfig['create.auto.cat.dir']){
                if (!$this->item->id){            
                    // cat_dir is defined as required, so we need a default value here
                    echo '<input type="hidden" name="jform[cat_dir]" value="DUMMY" />';
                }         
            }        
        ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="category" />         
        <input type="hidden" name="cat_dir_org" value="<?php echo $this->item->cat_dir; ?>" />
        <input type="hidden" name="cat_dir_parent_org" value="<?php echo $this->item->cat_dir_parent; ?>" />
        <input type="hidden" name="cat_title_org" value="<?php echo $this->item->title; ?>" />
        <?php 
        echo JHtml::_('form.token'); ?>
    </div>
    <div class="clr"></div>    
</form>    
    