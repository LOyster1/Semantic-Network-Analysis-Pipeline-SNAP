<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');


/**
 * Supports an HTML select list of articles
 * @since 1.6
 */
class JFormFieldjdfileicon extends JFormField
{
     /**
     * The form field type.
     *
     * @var string
     * @since 1.6
     */
     protected $type = 'jdfileicon';

     protected function getInput()
     {
     
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
         
        // create icon select box for file (mime) symbol
        $pic_dir = '/images/jdownloads/fileimages/';
        $pic_dir_path = JURI::root().'images/jdownloads/fileimages/';
        $pic_files = JFolder::files( JPATH_SITE.$pic_dir );
        $pic_list[] = JHtml::_('select.option', '', JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_FRONTEND_FPIC_TEXT'));
        foreach ($pic_files as $file) {
            if (@preg_match( "/(gif|jpg|png)/i", $file )){
                $pic_list[] = JHtml::_('select.option',  $file );
            }
        } 
        
        // use the default icon when is selected in configuration
        $pic_default = '';
        $pic_default = $this->form->getValue('file_pic');
        if ($jlistConfig['cat.pic.default.filename'] && !$pic_default) {
            $pic_default = $jlistConfig['file.pic.default.filename'];
        }    
      
        $inputbox_pic = JHtml::_('select.genericlist', $pic_list, 'file_pic', "class=\"inputbox\" size=\"1\""
      . " onchange=\"javascript:if (document.adminForm.file_pic.options[selectedIndex].value!='') {document.imagelib.src='$pic_dir_path' + document.adminForm.file_pic.options[selectedIndex].value} else {document.imagelib.src=''}\"", 'value', 'text', $pic_default );
          
        return $inputbox_pic;
    }  
}    
     
?>