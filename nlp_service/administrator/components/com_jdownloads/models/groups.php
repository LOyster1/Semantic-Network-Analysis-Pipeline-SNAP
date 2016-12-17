<?php


defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of user group records.
 *
 */
class jdownloadsModelGroups extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'importance', 'a.importance',
                'group_id', 'a.group_id',
				'title', 'a.title',
				'download_limit_daily', 'a.download_limit_daily',
                'download_limit_weekly', 'a.download_limit_weekly',
				'download_limit_monthly', 'a.download_limit_monthly',
			    'download_volume_limit_daily', 'a.download_volume_limit_daily',
                'download_volume_limit_weekly', 'a.download_volume_limit_weekly',
                'download_volume_limit_monthly', 'a.download_volume_limit_monthly',
                'how_many_times', 'a.how_many_times',
                'transfer_speed_limit_kb', 'a.transfer_speed_limit_kb',
                'upload_limit_daily', 'a.upload_limit_daily',
                'view_captcha', 'a.view_captcha',
                'view_inquiry_form', 'a.view_inquiry_form',
                'view_report_form', 'a.view_report_form',
                'must_form_fill_out', 'a.must_form_fill_out',
                'countdown_timer_duration', 'a.countdown_timer_duration',
                'may_edit_own_downloads', 'a.may_edit_own_downloads',
                'may_edit_all_downloads', 'a.may_edit_all_downloads',
                'use_private_area', 'a.use_private_area',
                'view_user_his_limits', 'a.view_user_his_limits',
                'view_user_his_limits_msg', 'a.view_user_his_limits_msg',
                'notes', 'a.notes',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time'
            );
		}

		parent::__construct($config);
        
        
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_jdownloads');
        $this->setState('params', $params);

        // List state information.
        $limit = 0;

        $default_ordering = 'a.lft';
        $default_direction = 'asc';
        
        // Receive & set list options
        $default_limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
        if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array')){
            if (isset($list['limit'])){
                $limit = (int)$list['limit'];
            } else {
                $limit = $default_limit;
            }
        } else {
             $limit = $default_limit;
        }
        $this->setState('list.limit', $limit);
         
        // Check if the ordering field is in the white list, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $default_ordering);

        if (!in_array($value, $this->filter_fields)){
            $value = $default_ordering;
            $app->setUserState($this->context . '.ordercol', $value);
        }
        $this->setState('list.ordering', $value);

        // Check if the ordering direction is valid, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $default_direction);

        if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))){
            $value = $direction;
            $app->setUserState($this->context . '.orderdirn', $value);
        }
        $this->setState('list.direction', $value);

        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
        $this->setState('list.start', $limitstart);        
	}
	
    /**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * Gets the list of groups and adds expensive joins to the result set.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 */
	public function getItems()
	{

        $db		= $this->getDbo();
		// Get a storage key.
		$store = $this->getStoreId();
        
        // check whether this is the first run, then the table is empty
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from('#__jdownloads_usergroups_limits');
        $this->_db->setQuery($query);
        $jd_groups = $this->_db->loadObjectList();
        $amount_jd_groups = count($jd_groups);
        
        // get the joomla usergroups
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from('#__usergroups');
        $query->order('id');
        
        $this->_db->setQuery($query);
        $joomla_groups = $this->_db->loadObjectList();
        $amount_joomla_groups = count($joomla_groups);
        
        if ($joomla_groups){
            $query = $this->_db->getQuery(true);
            $query->select('group_id, importance');
            $query->from('#__jdownloads_usergroups_limits');
            $query->order('group_id');
            $this->_db->setQuery($query);
            $importance_levels = $this->_db->loadAssocList('group_id');            
        }
        
        $importance_list = array();
        $importance_new  = 0;
            
        if ($amount_jd_groups != $amount_joomla_groups){
           if ($amount_jd_groups < $amount_joomla_groups){            
               // add the missing joomla user groups in jD groups
               if ($joomla_groups){
                   for ($i=0; $i < count($joomla_groups); $i++) {
                        $query = $this->_db->getQuery(true);
                        $query->select('*');
                        $query->from('#__jdownloads_usergroups_limits');
                        $query->where('group_id = '.(int)$joomla_groups[$i]->id);
                        $this->_db->setQuery($query);
                        if (!$result = $this->_db->loadObject()){
                            // add the joomla group to the jD groups
                            $query = $this->_db->getQuery(true);
                            $query->insert('#__jdownloads_usergroups_limits');
                            // add group_id
                            $query->set('group_id = '.$db->quote($joomla_groups[$i]->id));
                            // add default msg for timer
                            $query->set('countdown_timer_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_COUNTDOWN_MSG_TEXT')));
                            // add default msg for limits
                            $query->set('download_limit_daily_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_DAILY_MSG')));
                            $query->set('download_limit_weekly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_WEEKLY_MSG')));
                            $query->set('download_limit_monthly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_LIMIT_MONTHLY_MSG')));
                            // volume
                            $query->set('download_volume_limit_daily_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_DAILY_MSG')));
                            $query->set('download_volume_limit_weekly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_WEEKLY_MSG')));
                            $query->set('download_volume_limit_monthly_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_VOLUME_LIMIT_MONTHLY_MSG')));
                            
                            $query->set('how_many_times_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_DOWNLOAD_HOW_MANY_TIMES_MSG')));
                            $query->set('upload_limit_daily_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_UPLOAD_LIMIT_DAILY_MSG')));
                            
                            $query->set('view_user_his_limits_msg = '.$db->quote(JText::_('COM_JDOWNLOADS_USERGROUPS_VIEW_USER_HIS_LIMITS_MSG')));
                            
                            // search the parent id for the new user group and get from this group the importance value
                            /* not more use as this could upset existing rankings.   
                            if (array_key_exists($joomla_groups[$i]->parent_id, $importance_levels)){
                                // when found, increase the value
                                $importance_new = (int)$importance_levels[$joomla_groups[$i]->parent_id]['importance'] + 1;
                                while (in_array($importance_new, $importance_list)){
                                    // Increase the value until we have a valid value
                                    $importance_new++; 
                                }  
                            } else {
                                // we have not found a valid value
                                // we do nothing in this case and use the value 0
                            }
                            */
                            $query->set('importance = '.$db->quote($importance_new));
                            
                            $this->_db->setQuery($query);   
                            if (!$db->execute()){
                                $this->setError($db->getErrorMsg());
                                return false;
                            }                        
                        } else {
                            $importance_list[] = (int)$result->importance;
                        }              
                   }
               } else {
                   $this->setError('Error: Joomla user groups not found!');
                   return false;
               }
               
           } else {

                   // remove not longer existings Joomla user groups from jD groups
                   if ($jd_groups){
                       for ($i=0; $i < count($jd_groups); $i++) {
                            $query = $this->_db->getQuery(true);
                            $query->select('*');
                            $query->from($db->quoteName('#__usergroups'));
                            $query->where('id = '.(int)$jd_groups[$i]->group_id);
                            $this->_db->setQuery($query);
                            if (!$result = $this->_db->loadResult()){
                                // delete the joomla group from the jD groups
                                $query = $this->_db->getQuery(true);
                                $query->delete($db->quoteName('#__jdownloads_usergroups_limits'));
                                $query->where('id = '.(int)$jd_groups[$i]->id);
                                $this->_db->setQuery($query);
                                if (!$db->execute()){
                                    $this->setError($db->getErrorMsg());
                                    return false;
                                }                        
                            }               
                       }
                   } else {
                       $this->setError('Error: jDownloads user groups not found!');
                       return false;
                   }
               }
        }                

		// Try to load the data from internal storage.
		if (empty($this->cache[$store])) {
			$items = parent::getItems();

			// Bail out on an error or empty list.
			if (empty($items)) {
				$this->cache[$store] = $items;

				return $items;
			}

			// First pass: get list of the group id's and reset the counts.
			$groupIds = array();
			foreach ($items as $item)
			{
				$groupIds[] = (int) $item->id;
				$item->user_count = 0;
			}

			// Get the counts from the database only for the users in the list.
			$query	= $db->getQuery(true);

			// Count the objects in the user group.
			$query->select('map.group_id, COUNT(DISTINCT map.user_id) AS user_count')
				->from($db->quoteName('#__user_usergroup_map').' AS map')
				->where('map.group_id IN ('.implode(',', $groupIds).')')
				->group('map.group_id');

			$db->setQuery($query);

			// Load the counts into an array indexed on the user id field.
			$users = $db->loadObjectList('group_id');

			$error = $db->getErrorMsg();
			if ($error) {
				$this->setError($error);
				return false;
			}

			// Second pass: collect the group counts into the master items array.
			foreach ($items as &$item)
			{
				if (isset($users[$item->id])) {
					$item->user_count = $users[$item->id]->user_count;
				}
			}
			// Add the items to the internal cache.
			$this->cache[$store] = $items;
		}
		return $this->cache[$store];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from($db->quoteName('#__usergroups').' AS a');

		// Add the level in the tree.
		$query->select('COUNT(DISTINCT c2.id) AS level');
		$query->join('LEFT OUTER', $db->quoteName('#__usergroups').' AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt');
		$query->group('a.id, a.lft, a.rgt, a.parent_id, a.title');
         
        // get the limits from jD usergroups
        $query->select('f.id AS jd_user_group_id, f. importance, f.download_limit_daily as download_limit_daily,
                        f.download_limit_weekly as download_limit_weekly,
                        f.download_limit_monthly as download_limit_monthly,
                        f.download_volume_limit_daily as download_volume_limit_daily,
                        f.download_volume_limit_weekly as download_volume_limit_weekly,
                        f.download_volume_limit_monthly as download_volume_limit_monthly,
                        f.how_many_times as how_many_times,
                        f.transfer_speed_limit_kb as transfer_speed_limit_kb,
                        f.upload_limit_daily as upload_limit_daily,
                        f.view_captcha as view_captcha,
                        f.view_inquiry_form as view_inquiry_form,
                        f.view_report_form as view_report_form,
                        f.must_form_fill_out as must_form_fill_out,
                        f.countdown_timer_duration as countdown_timer_duration,
                        f.may_edit_own_downloads as may_edit_own_downloads,
                        f.may_edit_all_downloads as may_edit_all_downloads,
                        f.use_private_area as use_private_area,
                        f.view_user_his_limits as view_user_his_limits,
                        f.view_user_his_limits_msg as view_user_his_limits_msg, 
                        f.checked_out,
                        f.checked_out_time');
                        
        $query->join('LEFT', '#__jdownloads_usergroups_limits AS f ON f.group_id = a.id');
        
		// Filter the comments over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.title LIKE '.$search);
			}
		}

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id = f.checked_out');
        
        // Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.lft')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		return $query;
	}
    
    /**
     * Remove from all selected user groups the download limits
     *
     * @return    boolean 
     */    
    public static function resetLimits($cid)
    {
        $db = JFactory::getDBO(); 
        $query    = $db->getQuery(true);
        $id = join(",", $cid);
        
        // update data
        $query->clear();
        $query->update($db->quoteName('#__jdownloads_usergroups_limits'));
        $query->set($db->quoteName('download_limit_daily').' = '.$db->quote(0));
        $query->set($db->quoteName('download_limit_weekly').' = '.$db->quote(0));
        $query->set($db->quoteName('download_limit_monthly').' = '.$db->quote(0));
        $query->set($db->quoteName('download_volume_limit_daily').' = '.$db->quote(0));
        $query->set($db->quoteName('download_volume_limit_weekly').' = '.$db->quote(0));
        $query->set($db->quoteName('download_volume_limit_monthly').' = '.$db->quote(0));
        $query->set($db->quoteName('how_many_times').' = '.$db->quote(0));
        $query->set($db->quoteName('transfer_speed_limit_kb').' = '.$db->quote(0));
        $query->set($db->quoteName('upload_limit_daily').' = '.$db->quote(0));
        $query->set($db->quoteName('view_captcha').' = '.$db->quote(0));
        $query->set($db->quoteName('view_inquiry_form').' = '.$db->quote(0));
        $query->set($db->quoteName('view_report_form').' = '.$db->quote(0));
        $query->set($db->quoteName('must_form_fill_out').' = '.$db->quote(0));
        $query->set($db->quoteName('countdown_timer_duration').' = '.$db->quote(0));
        $query->set($db->quoteName('may_edit_own_downloads').' = '.$db->quote(0));
        $query->set($db->quoteName('may_edit_all_downloads').' = '.$db->quote(0));
        $query->set($db->quoteName('use_private_area').' = '.$db->quote(0));
        $query->set($db->quoteName('view_user_his_limits').' = '.$db->quote(0));
        // use here the default value
        $query->set($db->quoteName('download_limit_after_this_time').' = '.$db->quote(60));
        $query->where("group_id IN ($id)");
        $db->setQuery((string)$query);
        $db->loadObjectList();
        
        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
            return false;
        }
        return true;
    }

}
?>