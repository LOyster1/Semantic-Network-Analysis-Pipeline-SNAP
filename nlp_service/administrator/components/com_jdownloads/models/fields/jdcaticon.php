<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');


/**
 * Supports an HTML select list of articles
 * @since 1.6
 */
class JFormFieldjdcaticon extends JFormField
{
     /**
     * The form field type.
     *
     * @var string
     * @since 1.6
     */
     protected $type = 'jdcaticon';

     protected function getInput()
     {
     
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
         
        // create icon select box for category symbol
        $cat_pic_dir = '/images/jdownloads/catimages/';
        $cat_pic_dir_path = JURI::root().'images/jdownloads/catimages/';
        $pic_files = JFolder::files( JPATH_SITE.$cat_pic_dir );
        $cat_pic_list[] = JHtml::_('select.option', '', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_PIC_TEXT'));
        foreach ($pic_files as $file) {
            if (@preg_match( "/(gif|jpg|png)/i", $file )){
                $cat_pic_list[] = JHtml::_('select.option',  $file );
            }
        } 
        
        // use the default icon when is selected in configuration
        $pic_default = '';
        $pic_default = $this->form->getValue('pic');
        if ($jlistConfig['cat.pic.default.filename'] && !$pic_default) {
            $pic_default = $jlistConfig['cat.pic.default.filename'];
        }    
      
      
        $inputbox_pic = JHtml::_('select.genericlist', $cat_pic_list, 'pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.pic.options[selectedIndex].value!='') {document.imagelib.src='$cat_pic_dir_path' + document.adminForm.pic.options[selectedIndex].value} else {document.imagelib.src=''}\"", 'value', 'text', $pic_default );
          
        return $inputbox_pic;
    
    }  
}    
     
?>