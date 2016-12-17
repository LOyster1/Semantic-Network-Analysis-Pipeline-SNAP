<?php
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.controllerform');

/**
 * License controller class.
 *
 * @package        Joomla.Administrator
 * @subpackage    com_weblinks
 * @since        1.6
 */
class jdownloadsControllercategory extends JControllerForm
{
  
   /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct();

        // Register Extra task
        $this->registerTask( 'apply', 'save' );
        $this->registerTask( 'add',   'edit' );
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param    array    $data    An array of input data.
     * @return    boolean
     * @since    1.6
     */
    protected function allowAdd($data = array()) 
    {
        // Initialise variables. 
        $user        = JFactory::getUser();
        $allow        = null;
        $allow    = $user->authorise('core.create', 'com_jdownloads');
        
        if ($allow === null) {
            return parent::allowAdd($data);
        } else {
            return $allow;
        }
    }
    
    /**
     * Method to check if you can edit a record.
     *
     * @param    array    $data    An array of input data.
     * @param    string    $key    The name of the key for the primary key.
     *
     * @return    boolean
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        
        // Initialise variables. 
        $user        = JFactory::getUser();
        $allow        = null;
        $allow    = $user->authorise('core.edit', 'com_jdownloads');
        if ($allow === null) {
            return parent::allowEdit($data, $key);
        } else {
            return $allow;
        }
    }
    
    // copy or move a category (with all subcategories) to the same (copy) or a other position (move)
    public function batch() 
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model    = $this->getModel('category', '', array());
        
        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=categories'.$this->getRedirectToListAppend(), false));
        
        return parent::batch($model);
    } 
    
    /* Method to install the example data
     *
     *
     * @return    string  message
     */
    public function installSampleData()
    {        
        global $jlistConfig;

        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);
        $user  = JFactory::getUser();
        $error = false;

        $model = $this->getModel('category', '', array());
        $model_download = $this->getModel('download', '', array());
        
        // check user access right
        if (JFactory::getUser()->authorise('edit.config','com_jdownloads'))
        {
            $root_dir = $jlistConfig['files.uploaddir'];
            
            if(JFolder::exists($root_dir)) {
                if (is_writable($root_dir)) {      

                    // create it only when the folder for the sample category still not exists
                    if (!JFolder::exists($root_dir.'/'.JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CAT_NAME_ROOT'))){

                            // create root cat
                            $create_result = $model->createCategory(JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CAT_NAME_ROOT'), '1', '', JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CAT_NAME_TEXT'), 1);
                            if (!$create_result){
                               // error message: can not rebuild tree in DB
                               $error = true;
                               JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_SAMPLE_DATA_REBUILD_ERROR'));
                            } else {
                            
                                // create sub category
                                $create_result_sub = $model->createCategory(JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CAT_NAME_SUB'), $create_result->id, '', JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CAT_NAME_TEXT'));                            
                                if (!$create_result_sub){
                                   // error message: can not rebuild tree in DB
                                   $error = true;
                                   JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_SAMPLE_DATA_REBUILD_ERROR'));
                                } else {                            
                                
                                    // create download example
                                    $filename = 'example_data_file.zip';

                                    // copy first the file to the target folder
                                    $source_path = JPATH_COMPONENT_ADMINISTRATOR.'/assets/example_data_file.zip';
                                    $dest_path = $root_dir.'/'.$create_result_sub->cat_dir_parent.'/'.$create_result_sub->cat_dir.'/example_data_file.zip'; 
                                    $filesize = JDownloadsHelper::fsize($source_path);
                                    $result = JFile::copy($source_path, $dest_path);

                                    $new_download = $model_download->createDownload(JText::_('COM_JDOWNLOADS_SAMPLE_DATA_FILE_NAME'), $create_result_sub->id, '', JText::_('COM_JDOWNLOADS_SAMPLE_DATA_FILE_NAME_TEXT'), $filename, $filesize);                            
                                    if (!$new_download){
                                       $error = true;
                                       JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_SAMPLE_DATA_REBUILD_ERROR'));
                                    }
                                }
                            }    
                                
                    } else {
                        // error message: upload folder not writeable
                        $error = true;
                        JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_SAMPLE_DATA_EXISTS'));
                    } 
                } else {
                    // error message: upload folder not writeable
                    $error = true;
                    JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CREATE_ERROR'));
                } 
            } else {
                // error message: upload folder not found
                $error = true;
                JError::raiseWarning( 100, JText::_('COM_JDOWNLOADS_SAMPLE_DATA_CREATE_ERROR'));
            }
            if (!$error){
                // result successful
                JFactory::getApplication()->enqueueMessage(JText::_('COM_JDOWNLOADS_TOOLS_RESET_RESULT_OKAY_MSG'));
            }    
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=tools', false)); 
    }
    
    
}
?>    