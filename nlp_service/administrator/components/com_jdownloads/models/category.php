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

require_once JPATH_SITE.'/components/com_jdownloads/helpers/categories.php';
require_once JPATH_SITE.'/components/com_jdownloads/helpers/query.php';
require_once JPATH_SITE.'/administrator/components/com_jdownloads/helpers/jdownloadshelper.php';
jimport('joomla.application.component.modeladmin');
//jimport( 'joomla.database.tablenested' );  

class jdownloadsModelcategory extends JModelAdmin
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
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check for existing category.
        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', $record->extension . '.category.' . (int) $record->id);
        }
        // New category, so check against the parent.
        elseif (!empty($record->parent_id))
        {
            return $user->authorise('core.edit.state', $record->extension . '.category.' . (int) $record->parent_id);
        }
        // Default to component settings if neither category nor parent known.
        else
        {
            return $user->authorise('core.edit.state', $record->extension);
        }
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
    public function getTable($type = 'category', $prefix = 'jdownloadsTable', $config = array()) 
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
        $form = $this->loadForm('com_jdownloads.category', 'category',
                                array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) 
        {
            return false;
        }
        
        return $form;
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
        
        if ($item->id){
            $registry = new JRegistry;
            // get the tags
            $item->tags = new JHelperTags;
            $item->tags->getTagIds($item->id, 'com_jdownloads.category');         
        }        

        return $item;
    }      
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData() 
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_jdownloads.edit.category.data', array());
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
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $table->title        = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias        = JApplication::stringURLSafe($table->alias);

        if (empty($table->alias)) {
            $table->alias = JApplication::stringURLSafe($table->title);
        }
        
        if (!empty($table->password)) {
            $table->password_md5 = hash('sha256', $table->password);
        }

        if (empty($table->id)) {
            // Set ordering to the last item if not set - DEPRECATED in jD 2.0 and not really used.
            if (empty($table->ordering)) {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__jdownloads_categories WHERE parent_id = \''.$table->parent_id.'\'');
                $max = $db->loadResult();
                $table->ordering = $max+1;
            } 
        }
        else {
            // Set the values for an old category
            
        }
    }
    
    /**
     * Method to save the form data.
     *
     * @param    array    The form data.
     * @param    boolean  The switch for added by monitoring
     * @return    boolean    True on success.
     */
    public function save($data, $auto_added = false)
    {
        global $jlistConfig;
        
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
        $jinput        = JFactory::getApplication()->input;
        $table         = $this->getTable();
        $pk            = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
        $isNew         = true;
        $catChanged    = false;
        $title_changed = false;
        $cat_dir_changed_manually = false;
        $checked_cat_title = '';
        
        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');        
        
        // remove bad input values
        $data['parent_id'] = (int)$data['parent_id'];
       
        // Load the row if saving an existing category.
        if ($pk > 0) {
            $table->load($pk);
            $isNew = false;
            if ($table->parent_id != $data['parent_id']){
                // we must be here careful for the case that user has manipulated manually the parent_id
                if ($data['parent_id'] == 0){
                    // invalid value, so we do here nothing and use the old parent_id
                   $data['parent_id'] = $table->parent_id;
                } else {   
                   $catChanged = true;
                }   
            }
        }

        // parent id must have at minimum a 1 for 'root' category
        if ($data['parent_id'] == 0){
            $data['parent_id'] = 1;
        }         
        
        // is title changed?
        $org_title = $jinput->get('cat_title_org', '', 'string');
        if ($org_title != '' && $org_title != $data['title']) {
            $title_changed = true;
        }
        
        // cat_dir manually changed?
        $old_cat_dir = $jinput->get('cat_dir_org', '', 'string');
        if ($old_cat_dir != '' && $old_cat_dir != $data['cat_dir']) {
            $cat_dir_changed_manually = true;
        }
        
        if (!$auto_added){ 
            // we must check first the cat_dir content and remove some critical things
            if ($jlistConfig['create.auto.cat.dir']){
                // check whether we have a different title and cat_dir (as example when prior was activated the manually category name building)
                if (!$title_changed && ($data['title'] != $data['cat_dir'])){
                    // activate this switch
                    $title_changed = true;
                } 
                
                // the cat_dir name is managed by jD and builded from category title
                $checked_cat_title = JDownloadsHelper::getCleanFolderFileName($data['title']);
            } else {
                // the cat_dir name is managed by the user and the cat_dir field
                $checked_cat_title = JDownloadsHelper::getCleanFolderFileName($data['cat_dir']);
            }    
            
            $data['cat_dir'] = $checked_cat_title;
        }

        if ($isNew || $title_changed || $cat_dir_changed_manually){
            // make sure that we have a new (valid) folder name / same when changed title or manually cat_dir field
           $data['cat_dir'] = $this->generateNewFolderName($data['parent_id'], $data['cat_dir'], $data['id']);        
        }
        
        if (!$data['cat_dir']){
            // ERROR - we have a empty category folder name - not possible! 
            $this->setError(JText::_('COM_JDOWNLOADS_BACKEND_CATSEDIT_ERROR_FOLDER_NAME'));
            return false;
        }
        
        // Set the new parent id if parent id not matched OR while New/Save as Copy .
        if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) {
            $table->setLocation($data['parent_id'], 'last-child');
        }

        // Alter the title for save as copy
        if ($jinput->get('task') == 'save2copy') {
            list($title,$alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
            $data['title']    = $title;
            $data['alias']    = $alias;
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
        
        // Check the data.
        if (!$table->checkData($isNew)) {
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
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }
        
        // folder handling functionality - not used for auto added
        if (!$auto_added){
            if (!$table->checkCategoryFolder($isNew, $catChanged, $title_changed, $checked_cat_title, $cat_dir_changed_manually)) {
                if ($table->published = 1){
                    $table->published = 0; 
                    $table->store();
                } 
                //return false;
            }
        }            

        // Trigger the onContentAfterSave event.
        $dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));

        // Rebuild the path for the category:
        // but only when it is a sub category (parent_id > 1)
        if ($table->parent_id > 1) {
            if (!$table->rebuildPath($table->id)) {
                $this->setError($table->getError());
                return false;
            } 
        }
        
        // Rebuild the paths of the category's children:
        if ($table->hasChildren($table->id)){
            if ($table->cat_dir_parent != ''){
                $path = $table->cat_dir_parent.'/'.$table->cat_dir;
            } else {
                $path = $table->cat_dir;
            }
            if (!$table->rebuild($table->id, $table->lft, $table->level, $path)) {
                $this->setError($table->getError());
                return false;
            } 
        }
        
        $this->setState($this->getName().'.id', $table->id);

        // Clear the cache
        $this->cleanCache();

        return true;
    }
    
    /**
     * Method to save the reordered nested set tree.
     * First we save the new order values in the lft values of the changed ids.
     * Then we invoke the table rebuild to implement the new ordering.
     *
     * @param   array    $idArray    An array of primary key ids.
     * @param   integer  $lft_array  The lft value
     *
     * @return  boolean  False on failure or error, True otherwise
     *
    */
    public function saveorder($idArray = null, $lft_array = null)
    {
        // Get an instance of the table object.
        $table = $this->getTable();

        if (!$table->saveorder($idArray, $lft_array))
        {
            $this->setError($table->getError());
            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }
    
    /**
     * Batch copy categories to a new category.
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
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        // UTF-8 aware alternative functions
        jimport( 'joomla.utilities.string' );        
        
        $root_folder = $jlistConfig['files.uploaddir'];
        $new_parent_id = (int) $value;

        $table = $this->getTable();
        $db = $this->getDbo();
        $user = JFactory::getUser();

        // check at first, that it is not already a other batch job in progress
        if ($jlistConfig['categories.batch.in.progress'] || $jlistConfig['downloads.batch.in.progress']){
            // generate the warning and return
            JError::raiseWarning(100, JText::_('COM_JDOWNLOADS_BATCH_IS_ALWAYS_STARTED')); 
            return false;
        } else {
            // update at first the batch progress setting in config 
            $jlistConfig['categories.batch.in.progress'] = 1;
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__jdownloads_config'));
            $query->set('setting_value = 1');
            $query->where('setting_name = \'categories.batch.in.progress\'');
            $db->setQuery($query);
            $result = $db->execute();
                   
            if ($error = $db->getErrorMsg()){
                $this->setError($error);
                return false;
            }        
            
        }
        
        $i      = 0;
        $newId  = 0;
        $new_parent_dir_part = '';
        $only_subcat_selected = false;
        $copy_root_cat = false;
        $changed_cat_dir = '';
        
        $old_cat_dir        = '';
        $old_cat_dir_parent = '';
        
        // base category directory name - changed or not
        $new_target_base_folder_name = '';
          

        // Check that the parent exists
        if ($new_parent_id){
            if (!$table->load($new_parent_id)){
                if ($error = $table->getError()){
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    // Non-fatal error
                    $this->setError(JText::_('COM_JDOWNLOADS_BATCH_MOVE_PARENT_NOT_FOUND'));
                    $new_parent_id = 0;
                }
            }
        }

        // If the parent is 0, set it to the ID of the root item in the tree
        if (empty($new_parent_id) || $new_parent_id == 1){
            if (!$new_parent_id = $table->getRootId()){
                $this->setError($db->getErrorMsg());
                return false;
            } 
               // Make sure we can create in root
               elseif (!$user->authorise('core.create', 'com_jdownloads'))
            {
                $this->setError(JText::_('COM_JDOWNLOADS_BATCH_CANNOT_CREATE'));
                return false;
            }
        }

        // We need to log the parent ID
        $parents = array();

        // Calculate the emergency stop count as a precaution against a runaway loop bug
        $query = $db->getQuery(true);
        $query->select('COUNT(id)');
        $query->from($db->quoteName('#__jdownloads_categories'));
        $db->setQuery($query);
        $count = $db->loadResult();

        if ($error = $db->getErrorMsg()){
            $this->setError($error);
            return false;
        }

        // Parent exists so we let's proceed
        while (!empty($pks) && $count > 0)
        {
            // Pop the first id off the stack
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
                    $this->setError(JText::sprintf('COM_JDOWNLOADS_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Copy is a bit tricky, because we also need to copy the children
            $query->clear();
            $query->select('id');
            $query->from($db->quoteName('#__jdownloads_categories'));
            $query->where('lft > ' . (int) $table->lft);
            $query->where('rgt < ' . (int) $table->rgt);
            $db->setQuery($query);
            $childIds = $db->loadColumn();

            // Add child ID's to the array only if they aren't already there.
            foreach ($childIds as $childId){
                if (!in_array($childId, $pks)){
                    array_push($pks, $childId);
                }
            }

            // Make a copy of the old ID and Parent ID
            $oldId = $table->id;
            $oldParentId = $table->parent_id; 
            
            // Make a copy of the old category folder path
            $old_cat_dir = $table->cat_dir;
            $old_cat_dir_parent = $table->cat_dir_parent;
            if ($old_cat_dir_parent != ''){
                $old_cat_path = $old_cat_dir_parent.'/'.$old_cat_dir;
            } else {
                $old_cat_path = $old_cat_dir;
            }

            // Reset the id because we are making a copy.
            $table->id = 0;

            // If we a copying children, the Old ID will turn up in the parents list
            // otherwise it's a new top level item
            $table->parent_id = isset($parents[$oldParentId]) ? $parents[$oldParentId] : $new_parent_id;
            if ($table->parent_id == 1){
                $table->cat_dir_parent = '';
            }    

            // Set the new location in the tree for the node.
            $table->setLocation($table->parent_id, 'last-child');

            $table->level = null;
            $table->lft = null;
            $table->rgt = null;

            // Alter the title & alias when we have the first cat
            list($title, $alias) = $this->generateNewTitle($table->parent_id, $table->alias, $table->title);
            $table->title = $title;
            $table->alias = $alias;
        
            // build new cat_dir from the old one
            $cat_dir = $this->generateNewFolderName($table->parent_id, $table->cat_dir, $table->id);

            $replace = array ( '(' => '', ')' => '' );
            $cat_dir = strtr ( $cat_dir, $replace );
            
            if ($cat_dir != $table->cat_dir){
                $changed_cat_dir = $cat_dir;
            }
            
            // we need the correct  path for the field cat_dir_parent
            if ($table->parent_id > 1 || $oldParentId > 1){
                $new_parent_cat_path = $table->getParentCategoryPath($table->parent_id);
            } else {
                // root cat
                $new_parent_cat_path = '';
            }            
            
            // build the new parent cat path
            $table->cat_dir_parent = $new_parent_cat_path;

            // make sure that we have not twice the same category folder name (but not for childrens)
            if (!in_array($oldParentId, $parents) && !$parents[$oldParentId]){
                if ($new_parent_cat_path != ''){
                    while (JFolder::exists($root_folder.'/'.$new_parent_cat_path.'/'.$cat_dir)){
                        $title = JString::increment($cat_dir);
                        $cat_dir = strtr ( $title, $replace );
                    }
                } else {
                    while (JFolder::exists($root_folder.'/'.$cat_dir)){
                        $title = JString::increment($cat_dir);
                        $cat_dir = strtr ( $title, $replace );
                    }
                }
            }            
            $table->cat_dir = $cat_dir;
            
            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
                return false;
            }
            
            // build the new cat_dir_parent part for the childrens
            if ($newId == 0 && $table->cat_dir_parent != ''){
               $new_parent_dir_part = $table->cat_dir_parent.'/'.$table->cat_dir; 
            }
            
            // Get the new item ID - but only when it is a root cat
            if ($copy_root_cat){
                $newId = $table->get('id');
            }    

            // Add the new ID to the array
            $newIds[$i] = $newId;
            $i++;

            // Now we log the old 'parent' to the new 'parent'
            $parents[$oldId] = $table->id;
            $count--;


            // Rebuild the hierarchy.
            if (!$table->rebuild()){
                $this->setError($table->getError());
                return false;
            }

            // Rebuild the tree path.
            if (!$table->rebuildPath($table->id)){
                $this->setError($table->getError());
                return false;
            }
            
            // build the source path 
            if ($old_cat_dir != ''){
                $source_dir = $root_folder.'/'.$old_cat_path;
            } else {
                $source_dir = $root_folder;
            }
            // build the target path 
            if ($new_parent_cat_path != ''){
                $target_dir = $root_folder.'/'.$new_parent_cat_path.'/'.$cat_dir;
            } else {
                $target_dir = $root_folder.'/'.$cat_dir;
            }

            // copy only the dir when we have it not already copied with parent folder 
            if (!in_array($oldParentId, $parents) && !$parents[$oldParentId]){
                
                // move now the complete category folder to the new location!
                // the path must have at the end a slash
                $message = '';
                JDownloadsHelper::moveDirs($source_dir.'/', $target_dir.'/', true, $message, false, true, true );
                if ($message){
                    JError::raiseWarning(100, $message);
                }             
            }                
        
    } 
        
        // actualize at last the batch progress setting 
        $jlistConfig['categories.batch.in.progress'] = 0;
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = 0');
        $query->where('setting_name = \'categories.batch.in.progress\'');
        $db->setQuery($query);
        $result = $db->execute();
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        } 
        
        return $newIds;
    }

    /**
     * Batch move categories to a other category.
     *
     * @param   integer  $value     The new category ID.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  boolean  True on success.
     *
     */
    protected function batchMove($value, $pks, $contexts)
    {
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
        // UTF-8 aware alternative functions
        jimport( 'joomla.utilities.string' );                

        $root_folder = $jlistConfig['files.uploaddir'];
        $new_parent_id = (int) $value;
        
        $table = $this->getTable();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // check at first, that it is not always run a other batch job
        if ($jlistConfig['categories.batch.in.progress'] || $jlistConfig['downloads.batch.in.progress']){
            // generate the warning and return
            JError::raiseWarning(100, JText::_('COM_JDOWNLOADS_BATCH_IS_ALWAYS_STARTED')); 
            return false;
        } else {
            // actualize at first the batvh progress setting 
            $jlistConfig['categories.batch.in.progress'] = 1;
            $query = $db->getQuery(true);
            $query->update('#__jdownloads_config');
            $query->set('setting_value = 1');
            $query->where('setting_name = \'categories.batch.in.progress\'');
            $db->setQuery($query);
            $result = $db->execute();
                   
            if ($error = $db->getErrorMsg()){
                $this->setError($error);
                return false;
            }        
            
        }        

        // Check that the parent exists.
        if ($new_parent_id){
            if (!$table->load($new_parent_id)){
                if ($error = $table->getError()){
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    // Non-fatal error
                    $this->setError(JText::_('COM_JDOWNLOADS_BATCH_MOVE_PARENT_NOT_FOUND'));
                    $new_parent_id = 0;
                }
            }
        }
        
        // We are going to store all the children and just move the category
        $children = array();

        // Parent exists so we let's proceed
        foreach ($pks as $pk){
            // Check that the row actually exists
            if (!$table->load($pk)){
                if ($error = $table->getError()){
                    // Fatal error
                    $this->setError($error);
                    return false;
                } else {
                    // Not fatal error
                    $this->setError(JText::sprintf('COM_JDOWNLOADS_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            //$oldParentId = $table->parent_id;
            $oldParentId = 0;
            
            // Make a copy of the old category folder path
            $old_cat_dir = $table->cat_dir;
            $old_cat_dir_parent = $table->cat_dir_parent;
            if ($old_cat_dir_parent != ''){
                $old_cat_path = $old_cat_dir_parent.'/'.$old_cat_dir;
            } else {
                $old_cat_path = $old_cat_dir;
            }   

            $cat_dir = $table->cat_dir;             

            // Set the new location in the tree for the node.
            $table->setLocation($new_parent_id, 'last-child');

            // Check if we are moving to a different parent
            if ($new_parent_id != $table->parent_id){
                // Add the child node ids to the children array.
                $query->clear();
                $query->select('id');
                $query->from($db->quoteName('#__jdownloads_categories'));
                $query->where($db->quoteName('lft' ) .' BETWEEN ' . (int) $table->lft . ' AND ' . (int) $table->rgt);
                $db->setQuery($query);
                $children = array_merge($children, (array) $db->loadColumn());
            }

            if ($new_parent_id > 1 || $oldParentId > 1){
                $new_parent_cat_path = $table->getParentCategoryPath($new_parent_id);
            } else {
                // root cat
                $new_parent_cat_path = '';
            }
            
            // build the new parent cat path
            $table->cat_dir_parent = $new_parent_cat_path;

            // build new cat_dir name when it exists allways
            $replace = array ( '(' => '', ')' => '' );
            if ($new_parent_id > 1){
                while (JFolder::exists($root_folder.'/'.$new_parent_cat_path.'/'.$cat_dir)){
                    $title = JString::increment($cat_dir);
                    $cat_dir = strtr ( $title, $replace );
                }
            } else {
                while (JFolder::exists($root_folder.'/'.$cat_dir)){
                    $title = JString::increment($cat_dir);
                    $cat_dir = strtr ( $title, $replace );
                }                
            }    
            
            $table->cat_dir = $cat_dir;            
            
            // Store the row
            if (!$table->store()){
                $this->setError($table->getError());
                return false;
            }
            
            // Rebuild the hierarchy.
            if (!$table->rebuild()){
                $this->setError($table->getError());
                return false;
            }            

            // Rebuild the tree path.
            if (!$table->rebuildPath()){
                $this->setError($table->getError());
                return false;
            }
            
            // biuld the source path 
            if ($old_cat_dir != ''){
                $source_dir = $root_folder.'/'.$old_cat_path;
            } else {
                $source_dir = $root_folder;
            }
            // build the target path 
            if ($new_parent_cat_path != ''){
                $target_dir = $root_folder.'/'.$new_parent_cat_path.'/'.$cat_dir;
            } else {
                $target_dir = $root_folder.'/'.$cat_dir;
            }

            // move now the complete category folder to the new location!
            // the path must have at the end a slash
            $message = '';
            JDownloadsHelper::moveDirs($source_dir.'/', $target_dir.'/', true, $message, true, false, false);
            if ($message == ''){
                // check the really result:
                if (JFolder::exists($target_dir) && !JFolder::exists($source_dir)){
                    // JError::raiseNotice(100, JText::sprintf('COM_JDOWNLOADS_BATCH_CAT_MOVED_MSG', $source_dir));    
                } else {
                    if (JFolder::exists($source_dir)){
                        $res = JDownloadsHelper::delete_dir_and_allfiles($source_dir);
                        if (JFolder::exists($source_dir)){
                            JError::raiseWarning(100, JText::sprintf('COM_JDOWNLOADS_BATCH_CAT_NOT_MOVED_MSG', $source_dir));
                        }    
                    }    
                }
            } else {
                JError::raiseWarning(100, $message);
            }                                
        }

        // Process the child rows
        if (!empty($children)){
            // Remove any duplicates and sanitize ids.
            $children = array_unique($children);
            JArrayHelper::toInteger($children);

            // Check for a database error.
            if ($db->getErrorNum()){
                $this->setError($db->getErrorMsg());
                return false;
            }
        }
        
        // actualize at last the batch progress setting 
        $jlistConfig['categories.batch.in.progress'] = 0;
        $query = $db->getQuery(true);
        $query->update('#__jdownloads_config');
        $query->set('setting_value = 0');
        $query->where('setting_name = \'categories.batch.in.progress\'');
        $db->setQuery($query);
        $result = $db->execute();
        if ($error = $db->getErrorMsg()){
            $this->setError($error);
        }         
        
        return true;
    }    
    
    /**
     * Method to change the title & alias.
     *
     * @param   integer  $parent_id  The id of the parent.
     * @param   string   $alias      The alias.
     * @param   string   $title      The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    protected function generateNewTitle($parent_id, $alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();
        while ($table->load(array('alias' => $alias, 'parent_id' => $parent_id))){
            $title = JString::increment($title);
            $alias = JString::increment($alias, 'dash');
        }
        return array($title, $alias);
    } 
    
    /**
     * Method to get a valid category directory name, which not yet exists for the new created category
     *
     * @param   integer  $parent_id  The id of the parent category.
     * @param   string   $cat_dir    The given folder name
     * @param   integer  $id         The id of the category   
     *
     * @return  string  Contains the modified category name
     */    
    protected function generateNewFolderName($parent_id, $cat_dir, $id)
    {
        global $jlistConfig;
        
        $table = $this->getTable();
        
        if ($table->load(array('cat_dir' => $cat_dir, 'parent_id' => $parent_id)) && ($table->id != $id)){
            // do it only when the $table->id is not the same as the current - otherwise it found it always 
            while ($table->load(array('cat_dir' => $cat_dir, 'parent_id' => $parent_id))){
                // use the settings from config
                if ($jlistConfig['fix.upload.filename.blanks']){
                    $cat_dir = JString::increment($cat_dir, 'dash');
                } else {
                    $cat_dir = JString::increment($cat_dir, 'default');
                }    
            } 
        }
        return $cat_dir;
    }
    
    /**
     * Method to run categories tree rebuild
     *
     * @return  boolean  True on success.
     */        
    public function rebuildCategories()
    {
        $table = $this->getTable();
        
        // Rebuild the hierarchy.
        if (!$table->rebuild()){
            $this->setError($table->getError());
            return false;
        }            

        // Rebuild the tree path.
        if (!$table->rebuildPath()){
            $this->setError($table->getError());
            return false;
        }        
        return true;
    }
    
    /**
     * Method rebuild the entire nested set tree. Started by categories toolbar.
     *
     * @return  boolean  False on failure or error, true otherwise.
     *
     */
    public function rebuild()
    {
        // Get an instance of the table object.
        $table = $this->getTable();

        if (!$table->rebuild())
        {
            $this->setError($table->getError());
            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }
    
    /**
    * Method to create a new category 
    * 
    * @param mixed $name
    * @param mixed $parent_id
    * @param mixed $note
    * @param mixed $description
    * @param mixed $published
    * @return JCategoryNode
    */
    public function createCategory( $name, $parent_id = 1, $note, $description, $published = 1 )
    {
        global $jlistConfig;
        
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_jdownloads/tables' );

        $cat_model = JModelLegacy::getInstance( 'Category', 'jdownloadsModel' );

        $data = array (
            'id' => 0,
            'parent_id' => $parent_id,
            'title' => $name,
            'alias' => '',
            'notes' => $note,
            'description' => $description,
            'pic' => $jlistConfig['cat.pic.default.filename'],
            'published' => $published,
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

        if( !$cat_model->save( $data ) )
        {
            return NULL;
        }
        
        $options = array();
        $categories = JDCategories::getInstance('jdownloads', $options);
        $subcategory = $categories->get( $cat_model->getState( "category.id" ) );
        return $subcategory;
    }                      

    /**
    * Method to create a new category from monitoring script 
    * 
    * @param mixed $data    
    * @return JCategoryNode
    */
    public function createAutoCategory($data)
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_jdownloads/tables' );

        $cat_model = JModelLegacy::getInstance( 'Category', 'jdownloadsModel' );

        if (!$cat_model->save( $data, true ) ){
            return NULL;
        }
        
        $new_category_id = $cat_model->getState( "category.id" ) ;
        if ($new_category_id > 0){
            return $new_category_id;
        } 
        
        return true;
    }                      

    /**
     * Method to change the published state of one or more records.
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     */
    public function publish(&$pks, $value = 1)
    {
        // Initialise variables.
        $dispatcher = JDispatcher::getInstance();
        $user = JFactory::getUser();
        $table = $this->getTable('category', 'jdownloadsTable');
        $pks = (array) $pks;

        // Include the content plugins for the change of state event.
        JPluginHelper::importPlugin('content');

        // Access checks.
        foreach ($pks as $i => $pk)
        {
            $table->reset();

            if ($table->load($pk))
            {
                if (!$this->canEditState($table))
                {
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                    return false;
                } else {
                    if ($value == 1){
                        if (!$this->existCategoryFolder($table))
                        {
                            // Prune items which can't be published.
                            unset($pks[$i]);
                            JError::raiseWarning( 100, JText::sprintf('COM_JDOWNLOADS_CATS_PUBLISH_NO_FOLDER', $table->title));
                            return false;
                        }  
                    }
                }    
            }
        }

        // Attempt to change the state of the records.
        if (!$table->publish($pks, $value, $user->get('id')))
        {
            $this->setError($table->getError());
            return false;
        }

        $context = $this->option . '.' . $this->name;

        // Trigger the onContentChangeState event.
        $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

        if (in_array(false, $result, true))
        {
            $this->setError($table->getError());
            return false;
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
    
    /**
     * Method to check the presence from a categories folder
     *
     * @return  boolean  True on success.
     *
     */
    public function existCategoryFolder($table)
    {
        global $jlistConfig;

        jimport( 'joomla.filesystem.folder' );

        $root_dir = $jlistConfig['files.uploaddir'];
        
        if ($table->cat_dir_parent != ''){
            $path = $root_dir.'/'.$table->cat_dir_parent.'/'.$table->cat_dir;
        } else {
            $path = $root_dir.'/'.$table->cat_dir;
        }
        
        if (!JFolder::exists($path)){
            return false;
        } 
        
        return true;    
    }
    
        
}
?>