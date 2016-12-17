<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist' );


class jdownloadsModelcategories extends JModelList
{
	
/**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see      JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'cat_dir', 'a.cat_dir',
                'parent_id', 'a.parent_id',
                'title', 'a.title',
                'alias', 'a.alias',
                'description', 'a.description',
                'pic', 'a.pic',
                'access', 'a.access', 'access_level',
                'metakey', 'a.metakey',
                'metadesc', 'a.metadesc',
                'robots', 'a.robots',
                'created_user_id', 'a.created_user_id',
                'created_time', 'a.created_time',
                'modified_user_id', 'a.modified_user_id',
                'modified_time', 'a.modified_time',
                'language', 'a.language',
                'notes', 'a.notes',
                'views', 'a.views',
                'params', 'a.params',
                'password', 'a.password',
                'password_md5', 'a.password_md5',
                'ordering', 'a.ordering',
                'published', 'a.published',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'asset_id', 'a.asset_id',
            );
        }

        parent::__construct($config);
    }


/**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);
        
        $level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
        $this->setState('filter.level', $level);

        // Load the language state.
        $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);
        
        // Load the published filter state.                  
        $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $state);

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



        
        // Force a language
        /* $forcedLanguage = $app->input->get('forcedLanguage');

        if (!empty($forcedLanguage))
        {
            $this->setState('filter.language', $forcedLanguage);
            $this->setState('filter.forcedLanguage', $forcedLanguage);
        } */       
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param    string        $id    A prefix for the store id.
     * @return    string        A store id.
     * @since    1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');
        $id.= ':' . $this->getState('filter.language');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db        = $this->getDbo();
        $query     = $db->getQuery(true);
        $user      = JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.cat_dir, a.cat_dir_parent, a.parent_id, a.level, a.lft, a.rgt, a.title, a.alias, a.description, a.pic, a.access, '  .
                'a.language, a.notes, a.ordering, a.published, a.checked_out, a.checked_out_time, a.asset_id'
            )
        );
        $query->from('`#__jdownloads_categories` AS a');
        
        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
        

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
         
        // remove 'root' cat
        $query->where("a.title != 'ROOT'"); 

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $groups    = implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN ('.$groups.')');
        }
        
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('(a.title LIKE '.$search.')');
            }
        }

        // Filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where('a.language = ' . $db->quote($language));
        }
        
        // Filter on the level.
        if ($level = $this->getState('filter.level')) {
            $query->where('a.level <= '.(int) $level);
        }

        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.published = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.published IN (0, 1))');
        }
        
        // Add the list ordering clause.
        $orderCol    = $this->state->get('list.ordering');
        $orderDirn    = $this->state->get('list.direction');
        $query->order($db->escape($orderCol.' '.$orderDirn));
        
        return $query;
    }  
    
}
?>