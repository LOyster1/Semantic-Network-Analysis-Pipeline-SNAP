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

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist'); 
jimport('joomla.application.component.modeladmin'); 

class jdownloadsModelList extends JModelList
{
     /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'file_id', 'a.file_id',
                'file_title', 'a.file_title',
                'file_alias', 'a.file_alias',
                'description', 'a.description',
                'description_long', 'a.description_long',
                'file_pic', 'a.file_pic',
                'thumbnail', 'a.thumbnail',
                'thumbnail2', 'a.thumbnail2',
                'thumbnail3', 'a.thumbnail3',
                'price', 'a.price',
                'release', 'a.release',
                'file_language', 'a.file_language',
                'system', 'a.system',
                'license', 'a.license',
                'url_license', 'a.url_license',
                'license_agree', 'a.license_agree',
                'size', 'a.size',
                'date_added', 'a.date_added',
                'file_date', 'a.file_date',
                'publish_from', 'a.publish_from',
                'publish_to', 'a.publish_to',
                'use_timeframe', 'a.use_timeframe',
                'url_download', 'a.url_download',
                'other_file_id', 'a.other_file_id',
                'extern_file', 'a.extern_file',                
                'extern_site', 'a.extern_site',                                                                
                'mirror_1', 'a.mirror_1',
                'mirror_2', 'a.mirror_2',
                'extern_site_mirror_1', 'a.extern_site_mirror_1',
                'extern_site_mirror_2', 'a.extern_site_mirror_2',
                'url_home', 'a.url_home',
                'author', 'a.author',
                'url_author', 'a.url_author',
                'created_by', 'a.created_by',
                'created_id', 'a.created_id',
                'created_mail', 'a.created_mail',
                'modified_by', 'a.modified_by',
                'modified_id', 'a.modified_id',
                'modified_date', 'a.modified_date',
                'submitted_by', 'a.submitted_by',
                'set_aup_points', 'a.set_aup_points',                      
                'downloads', 'a.downloads',
                'cat_id', 'a.cat_id', 'category_title',
                'notes', 'a.notes',
                'changelog', 'a.changelog',
                'password', 'a.password',
                'password_md5', 'a.password_md5',
                'views', 'a.views',
                'metakey', 'a.metakey',
                'metadesc', 'a.metadesc',
                'robots', 'a.robots',
                'update_active', 'a.update_active',
                'custom_field_1', 'a.custom_field_1',
                'custom_field_2', 'a.custom_field_2',
                'custom_field_3', 'a.custom_field_3',
                'custom_field_4', 'a.custom_field_4',
                'custom_field_5', 'a.custom_field_5',
                'custom_field_6', 'a.custom_field_6',
                'custom_field_7', 'a.custom_field_7',
                'custom_field_8', 'a.custom_field_8',
                'custom_field_9', 'a.custom_field_9',
                'custom_field_10', 'a.custom_field_10',
                'custom_field_11', 'a.custom_field_11',
                'custom_field_12', 'a.custom_field_12',
                'custom_field_13', 'a.custom_field_13',
                'custom_field_14', 'a.custom_field_14',
                'access', 'a.access', 'access_level',
                'language', 'a.language',
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
     */
    protected function populateState($ordering = null, $direction = null)
    {
        
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout'))
        {
            $this->context .= '.' . $layout;
        }        
        
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);        
        
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);
        
        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
        $this->setState('filter.access', $access);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        $language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);
        
        // Load the parameters.
        $params = JComponentHelper::getParams('com_jdownloads');
        $this->setState('params', $params);
        
        // List state information.
        $limit = 0;

        $default_ordering = 'a.file_title';
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
        $app->setUserState($this->context . '.limitstart', $limitstart);
        $this->setState('list.start', $limitstart);        
        
        // Force a language
        /*$forcedLanguage = $app->input->get('forcedLanguage');

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
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        // Compile the store id.
        $id    .= ':'.$this->getState('filter.search');
        $id    .= ':'.$this->getState('filter.access');
        $id    .= ':'.$this->getState('filter.published');
        $id    .= ':'.$this->getState('filter.category_id');
        $id    .= ':'.$this->getState('filter.language');

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
        
        $app = JFactory::getApplication();
        $modal = $app->getUserState( 'jd_modal' );
        $modal_edit_file_id = (int)$app->getUserState( 'jd_edit_file_id' );
        
        
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.file_id, a.file_title, a.file_alias, a.description, a.file_pic, a.price, a.release, a.cat_id, '.
                'a.size, a.date_added, a.publish_from, a.publish_to, a.use_timeframe, a.url_download, a.other_file_id, a.extern_file, a.downloads, '.
                'a.extern_site, a.notes, a.access, a.language, a.checked_out, a.checked_out_time, a.ordering, a.published, a.asset_id'
            )
        );
        $query->from('`#__jdownloads_files` AS a');
        
        // Join over the users for the checked out user. 
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
        
        // Join over the files for other selected file
        $query->select('f.url_download AS other_file_name, f.file_title AS other_download_title');
        $query->join('LEFT', $db->quoteName('#__jdownloads_files').' AS f ON f.file_id = a.other_file_id');
        
        // Join over the language
        $query->select('l.title AS language_title');
        $query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');
       
        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');        
        
        // Join over the categories.
        $query->select('c.title AS category_title, c.parent_id AS category_parent_id');
        $query->join('LEFT', '#__jdownloads_categories AS c ON c.id = a.cat_id');
        
        $query->select('cc.title AS category_title_parent');
        $query->join('LEFT', '#__jdownloads_categories AS cc ON cc.id = c.parent_id');        
        
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = ' . (int) $published);
        }
        elseif ($published === '') {
            $query->where('(a.published = 0 OR a.published = 1)');
        }
        
        // Filter by a single or group of categories
        $baselevel = 1;
        $categoryId = $this->getState('filter.category_id');
        if ($categoryId > 0) {
            $cat_tbl = JTable::getInstance('Category', 'jdownloadsTable');
            $cat_tbl->load($categoryId);
            // add the additional where clause only when 'Uncategorised' (id=1) is not selected as filter
            if ($categoryId == 1){
                $rgt = $cat_tbl->rgt;
                $lft = $cat_tbl->lft;
                $baselevel = (int) $cat_tbl->level;
                $query->where('c.lft = '.(int) $lft);
                $query->where('c.rgt = '.(int) $rgt);
            } else {
                $rgt = $cat_tbl->rgt;
                $lft = $cat_tbl->lft;
                $baselevel = (int) $cat_tbl->level;
                $query->where('c.lft >= '.(int) $lft);
                $query->where('c.rgt <= '.(int) $rgt);
            }
        }
        elseif (is_array($categoryId)) {
            JArrayHelper::toInteger($categoryId);
            $categoryId = implode(',', $categoryId);
            $query->where('a.catid IN ('.$categoryId.')');
        }        
        
        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = ' . (int) $access);
        }        
        
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
                $query->where('(a.file_title LIKE '.$search.' OR a.description LIKE '.$search.' OR a.description_long LIKE '.$search.' OR a.notes LIKE '.$search.')');
            }
        }                                                   

        // Filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where('a.language = ' . $db->quote($language));
        }
        
        // Used only for 'modal' lists 
        // View only downloads with an assigned file or extern link 
        if ($modal){
            if ($modal_edit_file_id > 0){
                $query->where("a.file_id != '$modal_edit_file_id'");
            }
            $query->where("a.url_download != '' || a.extern_file != ''");
        }

        // Add the list ordering clause.
        $orderCol    = $this->state->get('list.ordering', 'a.file_title');
        $orderDirn    = $this->state->get('list.direction', 'asc');
        
        //sqlsrv change
        if($orderCol == 'language')
            $orderCol = 'l.title';
        
        $query->order($db->escape($orderCol.' '.$orderDirn));
        return $query;
    }
    
    /**
     * Method to get a list of articles.
     * Overridden to add a check for access levels.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getItems()
    {
        $items = parent::getItems();

        if (JFactory::getApplication()->isSite())
        {
            $user = JFactory::getUser();
            $groups = $user->getAuthorisedViewLevels();

            for ($x = 0, $count = count($items); $x < $count; $x++)
            {
                // Check the access level. Remove articles the user shouldn't see
                if (!in_array($items[$x]->access, $groups))
                {
                    unset($items[$x]);
                }
            }
        }

        return $items;
    }    
    
    
}
?>