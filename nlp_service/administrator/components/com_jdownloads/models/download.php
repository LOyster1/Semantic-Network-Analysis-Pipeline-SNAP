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


defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');
//jimport( 'joomla.database.tablenested' );  

class jdownloadsModelDownload extends JModelAdmin
{
	
    // @var        string    The prefix to use with controller messages.
    protected $text_prefix = 'COM_JDOWNLOADS';

    /**
     * Method to test whether a record can be deleted.
     *
     * @param    object    A record object.
     * @return    boolean    True if allowed to delete the record. Defaults to the permission set in the component.
     * @since    1.6
     */
    protected function canDelete($record)
    {
        return parent::canDelete($record);
    }
    
    /**
     * Method to test whether a record can have its state changed.
     *
     * @param    object    A record object.
     * @return    boolean    True if allowed to change the state of the record. Defaults to the permission set in the component.
     * @since    1.6
     */
    protected function canEditState($record)
    {
        return parent::canEditState($record);
    }
    
	
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     * @return    JTable    A database object
     * @since    1.6
     */
    public function getTable($type = 'download', $prefix = 'jdownloadsTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param    array    $data        Data for the form.
     * @param    boolean    $loadData    True if the form is to load its own data (default case), false if not.
     * @return    mixed    A JForm object on success, false on failure
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true) 
    {
        
        // Initialise variables.
        $app    = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm('com_jdownloads.download', 'download',
                                array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) 
        {
            return false;
        }
        return $form;
    }
    
    // overwrite getItem method
/*    public function getItem($pk = null){
        if ($item = parent::getItem($pk)) {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__jdownloads_files AS a');
            $query->where('a.file_id = '. (int) $item->file_id);
            $db->setQuery($query);          
            $item =  $db->loadObject();
        }   
        return $item;
    }    */
    
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData() 
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_jdownloads.edit.download.data', array());
        if (empty($data)) 
        {
            $data = $this->getItem();
        }
        return $data;
    }

    
    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        jimport('joomla.filter.output');

        $user = JFactory::getUser();
        
        // Set the publish date to now
        $db = $this->getDbo();
        if($table->published == 1 && intval($table->publish_from) == 0) {
            $table->publish_from = JFactory::getDate()->toSql(); // True to return the date string in the local time zone, false to return it in GMT.
        }
        
        $table->file_title        = htmlspecialchars_decode($table->file_title, ENT_QUOTES);
        $table->file_alias        = JApplication::stringURLSafe($table->file_alias);

        if (empty($table->file_alias)) {
            $table->file_alias = JApplication::stringURLSafe($table->file_title);
        }  

        if (!empty($table->password)) {
            $table->password_md5 = hash('sha256', $table->password);
        } else {
            $table->password_md5 = '';
        }
        
        if (!$table->language){
            $table->language = '*';            
        }
                
        // Set the default values for new created download
        if (empty($table->file_id)) {
            // Reorder the downloads within the category so the new download is first
            $table->reorder('cat_id = '.(int) $table->cat_id.' AND published >= 0');

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db->setQuery('SELECT MAX(ordering) FROM #__jdownloads_files');
                $max = $db->loadResult();
                $table->ordering = $max+1;
            }
        }
    }
    
    /**
     * Method to save the form data.
     *
     * @param    array    $data     The form data.
     * @param    boolean  $auto     true when the data comes from auto monitoring
     * @param    boolean  $import   true when the data comes from 1.9.x import process   
     * @return    boolean    True on success.
     */
    public function save($data, $auto = false, $import = false, $restore_in_progress = false)
    {
        global $jlistConfig;
        
        $result = false;
        
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
        $table        = $this->getTable();
        $pk            = (!empty($data['file_id'])) ? $data['file_id'] : (int)$this->getState($this->getName().'.file_id');
        $isNew        = true;
        
        $jinput = JFactory::getApplication()->input;
        
        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');
        
        // Load the row if saving an existing download. Not when auto monitoring is activated (also use for import from old version)
        if ($pk > 0 && !$auto || ($pk > 0 && $restore_in_progress) ) {
            $table->load($pk);
            $isNew = false;
        }

        // Alter the title for save as copy
        if ($jinput->get('task') == 'save2copy') {
            list($title,$alias) = $table->buildNewTitle($data['file_alias'], $data['file_title']);
            $data['file_title']    = $title;
            $data['file_alias']    = $alias;
        }
        
        if (!isset($data['rules'])){
            $data['rules'] = array(
                'core.create' => array(),
                'core.delete' => array(),
                'core.edit' => array(),
                'core.edit.state' => array(),
                'core.edit.own' => array(),
                'download' => array(),
            ); 
        }

       
        if ((!empty($data['tags']) && $data['tags'][0] != ''))
        {
            $table->newTags = $data['tags'];
        } 

        // Bind the data.
        if (!$table->bind($data)) {
            $this->setError($table->getError());
            return false;
        }

        // Prepare the row for saving
        $this->prepareTable($table);
        
        // Check the data and check the selected files and handle it.
        if (!$table->checkData($isNew, $auto)) {
            $this->setError($table->getError());
            return false;
        }
        
        // Trigger the onContentBeforeSave event.
        $result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$table, $isNew));
        if (in_array(false, $result, true)) {
            $this->setError($table->getError());
            return false;
        }

        // Store the data.
        if ($import === true){
            // set off this 
            $table->set('_autoincrement',false);
        }
        
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        } else {
            // folder handling functionality
            /*if (!$table->checkCategoryFolder()) {
                $this->setError(JText::_('COM_JDOWNLOADS_CATSEDIT_ERROR_CHECK_FOLDER'));
                return false;
            } */
            
            
            if (!$auto){
                // Update only the log table when we have a new download creation in frontend
                $app  = JFactory::getApplication();
                if ($app->isSite() && $isNew){
                    $upload_data                = new stdClass();
                    $upload_data->file_id       = $table->file_id;
                    $upload_data->url_download  = $table->url_download;
                    $upload_data->file_title    = $table->file_title;
                    $upload_data->size          = $table->size;
                    JDHelper::updateLog($type = 2, '', $upload_data);
                    
                    // send e-mail after new download creation in frontend
                    if ($jlistConfig['send.mailto.option.upload'] == '1'){
                        JDHelper::sendMailUpload($table);               
                    }
                }
            }    
        }    

        // Trigger the onContentAfterSave event.
        $dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));
       
        $this->setState($this->getName().'.id', $table->file_id);

        // Clear the cache
        $this->cleanCache();
        
        return true;
    }
    
     /**
     * Batch copy downloads to a new category
     *
     * @param   integer  $value     The new category.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  mixed  An array of new IDs on success, boolean false on failure.
     *
     */
    protected function batchCopy($value, $pks, $contexts)
    {
        $categoryId = (int) $value;

        $table = $this->getTable();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // check at first, that it is not always run a other batch job
        if ($jlistConfig['categories.batch.in.progress'] || $jlistConfig['downloads.batch.in.progress']){
            // generate the warning and return
            JError::raiseWarning(100, JText::_('COM_JDOWNLOADS_BATCH_IS_ALWAYS_STARTED')); 
            return false;
        } else {
            // actualize at first the batch progress setting 
            $jlistConfig['downloads.batch.in.progress'] = 1;
            $query = $db->getQuery(true);
            $query->update('#__jdownloads_config');
            $query->set('setting_value = 1');
            $query->where('setting_name = \'downloads.batch.in.progress\'');
            $db->setQuery($query);
            $result = $db->execute();
                   
            if ($error = $db->getErrorMsg()){
                $this->setError($error);
                return false;
            }        
        }
        
        $i = 0;
        
        // Check that the category exists
        if ($categoryId){
            $categoryTable = JTable::getInstance('category', 'jdownloadsTable');
            if (!$categoryTable->load($categoryId)){
                if ($error = $categoryTable->getError()){
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
                    return false;
                }
            }
        }

        if (empty($categoryId)){
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
            return false;
        }

        // Check that the user has create permission for the component
        $extension = JFactory::getApplication()->input->get('option', '');
        $user = JFactory::getUser();
        
        if (!$user->authorise('core.create', $extension . '.category.' . $categoryId)){
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
            return false;
        }

        // Parent exists so we let's proceed
        while (!empty($pks))
        {
            // Pop the first ID off the stack
            $pk = array_shift($pks);

            $table->reset();

            // Check that the row actually exists
            if (!$table->load($pk)){
                if ($error = $table->getError()){
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }
            
            // save the data for copy the file
            // when we have categoryID = 1 it is a 'uncategorisied' download
            $must_copy_file = false;
            if ($categoryId > 1 && $table->url_download != ''){
                $url_download_source = $table->url_download;
                $source_cat_id       = $table->cat_id;
                $target_cat_id       = $categoryId;
                // we must only copy when we have a different cat id.
                if ($source_cat_id != $target_cat_id){
                    $must_copy_file = true;
                }    
            }    

            // Build a new title and alias
            list($title,$alias) = $table->buildNewTitle($table->file_alias, $table->file_title);
            $table->file_title    = $title;
            $table->file_alias    = $alias;

            // Reset the ID because we are making a copy
            $table->file_id = 0;

            // New category ID
            $table->cat_id = $categoryId;
            
            // set correct new ordering
            $table->ordering = $this->getNewOrdering($categoryId); 
            
            $table->views = 0;
            $table->downloads = 0;
            $table->modified_id = 0;            
            $table->modified_date = '0000-00-00 00:00:00';
            $table->url_download = '';
            
            // Check the row.
            if (!$table->check(true)){
                $this->setError($table->getError());
                return false;
            }

            // Store the row.
            if (!$table->store()){
                $this->setError($table->getError());
                return false;
            }
            
            // Get the new item ID
            $newId = $table->get('file_id');

            // Add the new ID to the array
            $newIds[$i]    = $newId;
            $i++;

            // copy now at final step the downloads file (when exists)            
            if ($must_copy_file){
                $url_download_source = $table->url_download;
                $source_cat_id       = $table->cat_id;
                $target_cat_id       = $categoryId;
            }    
        }

        // Clean the cache
        $this->cleanCache();

        // actualize at last the batch progress setting 
        $jlistConfig['downloads.batch.in.progress'] = 0;
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = 0');
        $query->where('setting_name = \'downloads.batch.in.progress\'');
        $db->setQuery($query);
        $result = $db->execute();
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        }          
        
        return $newIds;
    } 
    
     /**
     * Batch move downloads to a new category
     *
     * @param   integer  $value  The new category ID.
     * @param   array    $pks    An array of row IDs.
     *
     * @return  booelan  True if successful, false otherwise and internal error is set.
     *
     */
    protected function batchMove($value, $pks, $dummy = 0)
    {
        $categoryId    = (int) $value;

        $table = $this->getTable();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $jinput = JFactory::getApplication()->input;
        
        // check at first, that it is not always run a other batch job
        if ($jlistConfig['categories.batch.in.progress'] || $jlistConfig['downloads.batch.in.progress']){
            // generate the warning and return
            JError::raiseWarning(100, JText::_('COM_JDOWNLOADS_BATCH_IS_ALWAYS_STARTED')); 
            return false;
        } else {
            // actualize at first the batch progress setting 
            $jlistConfig['downloads.batch.in.progress'] = 1;
            $query = $db->getQuery(true);
            $query->update('#__jdownloads_config');
            $query->set('setting_value = 1');
            $query->where('setting_name = \'downloads.batch.in.progress\'');
            $db->setQuery($query);
            $result = $db->execute();
                   
            if ($error = $db->getErrorMsg()){
                $this->setError($error);
                return false;
            }        
        }        

        // Check that the category exists
        if ($categoryId) {
            $categoryTable = JTable::getInstance('category', 'jdownloadsTable');
            if (!$categoryTable->load($categoryId)) {
                if ($error = $categoryTable->getError()) {
                    // Fatal error
                    $this->setError($error);
                    return false;
                }
                else {
                    $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
                    return false;
                }
            }
        }

        if (empty($categoryId)) {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
            return false;
        }

        // Check that user has create and edit permission for the component
        $extension   = $jinput->get('option');
        $user        = JFactory::getUser();
        if (!$user->authorise('core.create', $extension)) {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
            return false;
        }

        if (!$user->authorise('core.edit', $extension)) {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
            return false;
        }

        // Parent exists so we let's proceed
        foreach ($pks as $pk)
        {
            // Check that the row actually exists
            if (!$table->load($pk)) {
                if ($error = $table->getError()) {
                    // Fatal error
                    $this->setError($error);
                    return false;
                }
                else {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Set the new category ID
            $table->cat_id = $categoryId;

            // Check the row.
            if (!$table->check()) {
                $this->setError($table->getError());
                return false;
            }

            // Store the row.
            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }
        }

        // Clean the cache
        $this->cleanCache();

        // actualize at last the batch progress setting 
        $jlistConfig['downloads.batch.in.progress'] = 0;
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = 0');
        $query->where('setting_name = \'downloads.batch.in.progress\'');
        $db->setQuery($query);
        $result = $db->execute();
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        }            
        
        return true;
    }
    
    // compute the new ordering for batch copy
    public function getNewOrdering($categoryId) 
    {
        $ordering = 1;
        $this->_db->setQuery('SELECT MAX(ordering) FROM #__jdownloads_files WHERE cat_id='.(int)$categoryId);
        $max = $this->_db->loadResult();
        $ordering = $max + 1;
        return $ordering;
    }
    
    /**
    * Method to create a new download 
    * 
    * @param mixed $name
    * @param mixed $cat_id
    * @param mixed $note
    * @param mixed $description
    * @return JCategoryNode
    */

    public function createDownload( $name, $cat_id = 1, $note, $description, $filename, $filesize = 0 )
    {
        global $jlistConfig;
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_jdownloads/tables' );

        $download_model = JModelLegacy::getInstance( 'Download', 'jdownloadsModel' );

        $data = array (
            'file_id' => 0,
            'cat_id' => $cat_id,
            'file_title' => $name,
            'file_alias' => '',
            'notes' => $note,
            'url_download' => $filename,
            'size' => $filesize,
            'description' => $description,
            'file_pic' => $jlistConfig['file.pic.default.filename'],
            'published' => '1',
            'access' => '1',
            'metadesc' => '',
            'metakey' => '',
            'created_user_id' => '0',
            'language' => '*',
            'rules' => array(
                'core.create' => array(),
                'core.delete' => array(),
                'core.edit' => array(),
                'core.edit.state' => array(),
                'core.edit.own' => array(),
                'download' => array(),
            ),
            'params' => array(),
        );

        if( !$download_model->save( $data ) )
        {
            return NULL;
        }
        
        $download_id = $download_model->getState('download.id');
        $download = self::getItem($download_id);
        
        return $download;
    }                      

    /**
    * Method to create a new download from auto monitoring
    * 
    * @param array       $data
    *        boolean     $import    switch which is set true when import process is run   
    * @return boolean
    */

    public function createAutoDownload( $data, $import = false )
    {
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_jdownloads/tables' );

        $download_model = JModelLegacy::getInstance( 'Download', 'jdownloadsModel' );

        if( !$download_model->save( $data, true, $import ) )
        {
            return false;
        }
        
        return true;
    }                      

    
    /**
     * Method to get a single record.
     *
     * @param    integer    The id of the primary key.
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        
        if ($item->file_id){
            $registry = new JRegistry;
            // get the tags
            $item->tags = new JHelperTags;
            $item->tags->getTagIds($item->file_id, 'com_jdownloads.download');         
        }        

        return $item;
    }                           
    
    
    /**
     * Method to remove a download and his file, preview file and images
     *
     * @access    public
     * @return    boolean    True on success
     */
    public function delete(&$pks = array())
    {
        global $jlistConfig;
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        // Initialise variables.
        $dispatcher = JDispatcher::getInstance();
        $pks = (array) $pks;
        $table = $this->getTable('download');
        
        $app       = JFactory::getApplication();
        $db        = JFactory::getDbo();
        
        $jinput = JFactory::getApplication()->input;
        
        $total     = count($pks);
        $query     = '';
        $cids = implode( ',', $pks );
        $del_error = false;
        $del_image_error = false;
        
        $pics_folder   = JPATH_SITE.'/images/jdownloads/screenshots/';
        $thumbs_folder = JPATH_SITE.'/images/jdownloads/screenshots/thumbnails/';
        
        $preview_folder = $jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'].DS;        
        
        // get selected option value to delete also the file
        $file_delete = $jinput->get('delete_file_option', 1, 'integer');
        
        // Include the content plugins for the on delete events.
        JPluginHelper::importPlugin('content');
        
        $can_delete = false;
        
        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk)
        {

            if ($table->load($pk))
            {
                if ($app->isAdmin()){
                    $can_delete = $this->canDelete($table);
                }    
                if ($app->isSite() || $can_delete)
                {

                    $context = $this->option . '.' . $this->name;

                    // Trigger the onContentBeforeDelete event.
                    $result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
                    if (in_array(false, $result, true))
                    {
                        $this->setError($table->getError());
                        return false;
                    }
                    
                    // check file delete option - delete it at first when selected
                    if ($file_delete == 1){            
                          // only when no extern links are used
                          if ($table->url_download <> ''){
                              if ($table->cat_id > 1){
                                  // get cat_dir when 'categorised' download is not selected
                                  $db->setQuery("SELECT cat_dir, cat_dir_parent FROM #__jdownloads_categories WHERE id = '$table->cat_id'");
                                  $cat_dirs = $db->loadObject();
                                  if ($cat_dirs->cat_dir_parent != ''){
                                      $cat_dir = $cat_dirs->cat_dir_parent.'/'.$cat_dirs->cat_dir;
                                  } else {
                                      $cat_dir = $cat_dirs->cat_dir;
                                  }
                              } else {
                                  // remove 'uncategorised' download
                                  $cat_dir = $jlistConfig['uncategorised.files.folder.name'];
                              }  
                              if ($cat_dir && @file_exists($jlistConfig['files.uploaddir'].'/'.$cat_dir.'/'.$table->url_download)){
                                  // delete the file now
                                  if (!JFile::delete($jlistConfig['files.uploaddir'].'/'.$cat_dir.'/'.$table->url_download)) {
                                      $del_error = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_DEL_FILES_ERROR');
                                  } 
                              } else {
                                    $del_error = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_DEL_FILES_ERROR');                                
                              }   
                          }

                    }                   

                    // Delete also the assigned images from this download, when this option is activated in config
                    if ($jlistConfig['delete.also.images.from.downloads'] == 1){
                          if ($table->images) {
                              $pics = explode('|', $table->images);
                              foreach ($pics as $pic){
                                  if (!JFile::delete($pics_folder.$pic)) {
                                      $del_image_error = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_DEL_IMAGES_ERROR');
                                  }    
                                  if (!JFile::delete($thumbs_folder.$pic)) {
                                      $del_image_error = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_DEL_IMAGES_ERROR');
                                  }    
                              }
                          }
                    }                      
                      
                    // Delete also the assigned preview file from this download, when this option is activated in config
                    if ($jlistConfig['delete.also.preview.files.from.downloads'] == 1){
                          if ($table->preview_filename) {
                                  if (!JFile::delete($preview_folder.$table->preview_filename)) {
                                      $del_image_error = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_DEL_IMAGES_ERROR');
                                  }    
                          }
                    }                   
                    
                    if ($file_delete == 1 && $del_error){
                        // create error message
                        JError::raiseWarning(100, $del_error);
                    }
                    
                    if ($del_image_error){
                        // create error message
                        JError::raiseWarning(100, $del_image_error);
                    }                          
                    
                    // delete now the row in table
                    if (!$table->delete($pk))
                    {
                        $this->setError($table->getError());
                        return false;
                    }

                    // Trigger the onContentAfterDelete event.
                    $dispatcher->trigger($this->event_after_delete, array($context, $table));

                }
                else
                {

                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $error = $this->getError();
                    if ($error)
                    {
                        JError::raiseWarning(500, $error);
                        return false;
                    }
                    else
                    {
                        JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                        return false;
                    }
                }

            }
            else
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;      
    } 
    
    /**
     * Method to toggle the featured setting of Downloads.
     *
     * @param   array    $pks    The ids of the items to toggle.
     * @param   integer  $value  The value to toggle to.
     *
     * @return  boolean  True on success.
     */
    public function featured($pks, $value = 0)
    {
        // Sanitize the ids.
        $pks = (array) $pks;
        JArrayHelper::toInteger($pks);

        if (empty($pks)){
            $this->setError(JText::_('COM_JDOWNLOADS_NO_ITEM_SELECTED'));
            return false;
        }

        try
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                        ->update($db->quoteName('#__jdownloads_files'))
                        ->set('featured = ' . (int) $value)
                        ->where('file_id IN (' . implode(',', $pks) . ')');
            $db->setQuery($query);
            $db->execute();
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
        $this->cleanCache();
        return true;
    }
    
}
?>