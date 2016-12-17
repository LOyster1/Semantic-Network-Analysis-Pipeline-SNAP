<?php

// Check to ensure this categorie is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

//jimport( 'joomla.application.component.model' );
jimport('joomla.application.component.modeladmin');

class jdownloadsModellicense extends JModelAdmin
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
    public function getTable($type = 'license', $prefix = 'jdownloadsTable', $config = array()) 
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
        $form = $this->loadForm('com_jdownloads.license', 'license',
                                array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) 
        {
            return false;
        }
        return $form;
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
        $data = JFactory::getApplication()->getUserState('com_jdownloads.edit.license.data', array());
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

        if (empty($table->id)) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__jdownloads_licenses');
                $max = $db->loadResult();
                $table->ordering = $max+1;
            }
        }
        else {
            // Set the values
        }
    }
    
/**
     * Method to save the form data.
     *
     * @param    array    The form data.
     * @return    boolean    True on success.
     */
    public function save($data)
    {
        // Initialise variables;
        $app        = JFactory::getApplication();
        $dispatcher = JDispatcher::getInstance();
        $table      = $this->getTable();
        $pk         = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
        $isNew      = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');
        
        // Load the row if saving an existing item.
        if ($pk > 0) {
            $table->load($pk);
            $isNew = false;
        }

        // Alter the title for save as copy
        if ($app->input->get('task') == 'save2copy') {
             $data['title'] = JString::increment($data['title']);
        }               

        // Bind the data.
        if (!$table->bind($data)) {
            $this->setError($table->getError());
            return false;
        }

        // Prepare the row for saving
        $this->prepareTable($table);
        
        // Check the data.
        if (!$table->check($isNew)) {
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
        
        // Trigger the onContentAfterSave event.
        $dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));

        $this->setState($this->getName().'.id', $table->id);

        // Clear the cache
        $this->cleanCache();

        return true;
    }    
    
    
}
?>