<?php
defined('_JEXEC') or die('Restricted access');

    jimport( 'joomla.html.html.tabs' );
    //jimport ('joomla.html.html.bootstrap');
    
    JHtml::_('behavior.tooltip');
    JHTML::_('behavior.formvalidation');
    // JHtml::_('behavior.formvalidator'); Joomla >= 3.4
    JHtml::_('jquery.framework');

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
        
    <div id="j-main-container" class="span10">
        <div class="adminform">
            <div class="jd-cpanel-left">
                <div id="cpanel">          
                    <?php
                        $option = 'com_jdownloads';
                        $link = 'index.php?option='.$option.'&amp;view=templates&amp;type=1';
                                jdownloadsViewlayouts::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_BACKEND_TEMP_TYP1' ) );
                        
                        $link = 'index.php?option='.$option.'&amp;view=templates&amp;type=4';
                                jdownloadsViewlayouts::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_BACKEND_TEMP_TYP4' ) );

                        $link = 'index.php?option='.$option.'&amp;view=templates&amp;type=2';
                                jdownloadsViewlayouts::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_BACKEND_TEMP_TYP2' ) );
                                
                        $link = 'index.php?option='.$option.'&amp;view=templates&amp;type=5';
                                jdownloadsViewlayouts::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_BACKEND_TEMP_TYP5' ) );                        

                        $link = 'index.php?option='.$option.'&amp;view=templates&amp;type=3';
                                jdownloadsViewlayouts::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_BACKEND_TEMP_TYP3' ) );
                                
                        $link = 'index.php?option='.$option.'&amp;view=templates&amp;type=7';
                                jdownloadsViewlayouts::quickiconButton( $link, 'layouts48.png', JText::_( 'COM_JDOWNLOADS_BACKEND_TEMP_TYP7' ) );                        

                        $link = 'index.php?option='.$option.'&amp;view=cssedit';
                                jdownloadsViewlayouts::quickiconButton( $link, 'css.png', JText::_( 'COM_JDOWNLOADS_BACKEND_EDIT_CSS_TITLE' ) );                        
                                                        
                        $link = 'index.php?option='.$option.'&amp;view=languageedit';
                                jdownloadsViewlayouts::quickiconButton( $link, 'langmanager.png', JText::_( 'COM_JDOWNLOADS_BACKEND_EDIT_LANG_TITLE' ) );
                    ?>
        
                    </div>
                <div style="clear:both">&nbsp;</div>
            </div>
        <div class="jd-cpanel-right"> 
            <div class="well">
        
                <?php //echo JHtml::_('bootstrap.startTabSet', 'jdlayout-sliders-layouts', array('active' => 'layouts_note')); ?>
                <?php //echo JHtml::_('bootstrap.addTab', 'jdlayout-sliders-layouts', 'layouts_note', JText::_('COM_JDOWNLOADS_BACKEND_TEMPPANEL_TABTEXT_INFO', true)); ?>
                
                <?php echo JHtml::_('tabs.start', 'jdlayout-sliders-layouts', array('useCookie' => true)); 
                      echo JHtml::_('tabs.panel', JText::_('COM_JDOWNLOADS_BACKEND_TEMPPANEL_TABTEXT_INFO'),'layouts_note'); 
                ?>                     

                <table class="jdadminpanel" width="95%" border="0">
                    <tr>
                        <td valign="top" align="left" width="100%">
                            <?php echo  JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_HEAD'); ?>
                            <?php echo  JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_HEAD_INFO').JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_HEAD_INFO2'); ?>
                        </td>
                    </tr>
                </table>
                
                <?php // echo JHtml::_('bootstrap.endTab'); ?>
                <?php // echo JHtml::_('bootstrap.endTabSet'); ?>                  
                
                <?php echo JHtml::_('tabs.end'); ?>
        </div>
        </div>
     </div>
     </div>

     <input type="hidden" name="option" value="com_jdownloads" />
     <input type="hidden" name="task" value="" />
     <input type="hidden" name="boxchecked" value="0" />
     <input type="hidden" name="controller" value="layouts" />
     </form>    
