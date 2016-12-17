<?php


defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * jdownloads user group model to change user downloads limits
 *
 */
class jdownloadsModelGroup extends JModelAdmin
{

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'group', $prefix = 'jdownloadsTable', $config = array())
	{
		$return = JTable::getInstance($type, $prefix, $config);
		return $return;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_jdownloads.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jdownloads.edit.group.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */
	public function save($data)
	{
        
        $result = false;
        
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
        $table        = $this->getTable();
        $pk           = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
        $isNew        = true;
        
        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('system');
        
        // Load the row if saving an existing download.
        if ($pk > 0) {
            $table->load($pk);
            $isNew = false;
        }

        // Bind the data.
        if (!$table->bind($data)) {
            $this->setError($table->getError());
            return false;
        }
        
        // Check the data.
        if (!$table->check()) {
            $this->setError($table->getError());
            return false;
        }
        
        // Trigger the on...BeforeSave event.
        $result = $dispatcher->trigger('onJDUserGroupSettingsBeforeSave', array($this->option.'.'.$this->name, &$table));
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
        $dispatcher->trigger('onJDUserGroupSettingsAfterSave', array($this->option.'.'.$this->name, &$table));
       
        $this->setState($this->getName().'.id', $table->id);

        // Clear the cache
        $this->cleanCache();

        return true;           
                
		//return parent::save($data);
	}
    
}
?>