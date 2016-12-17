<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 *
 * @component jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2011 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist'); 


class jdownloadsModellogs extends JModelList
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
                'type', 'a.type',
                'log_file_id', 'a.log_file_id',
                'log_file_size', 'a.log_file_size',
                'log_file_name', 'a.log_file_name',
                'log_title', 'a.log_title',
                'log_ip', 'a.log_ip',
                'log_datetime', 'a.log_datetime',
                'log_user', 'a.log_user',
                'log_browser', 'a.log_browser',
                'language', 'a.language',
                'ordering', 'a.ordering',
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
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        // Load the type state.
        $type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);
        
        // Load the parameters.
        $params = JComponentHelper::getParams('com_jdownloads');
        $this->setState('params', $params);

        // List state information.
        $limit = 0;

        $default_ordering = 'a.log_datetime';
        $default_direction = 'desc';
        
        // Receive & set list options
        $default_limit = (int)$app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
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

        $value = (int)$app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
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
     * @param    string        $id    A prefix for the store id.
     * @return    string        A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');
        $id.= ':' . $this->getState('filter.type');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
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
                'a.id, a.type, a.log_file_id, a.log_file_size, a.log_file_name, a.log_title, a.log_ip, a.log_datetime, a.log_user, a.log_browser, '  .
                'a.language, a.ordering'
            )
        );
                
        $query->from('`#__jdownloads_logs` AS a');
        
        // Join over the users to get the user name.
        $query->select('uc.name AS username');
        $query->join('LEFT', '#__users AS uc ON uc.id = a.log_user');

        // get the download title, filename and filesize.  (removed since we need full data in logs)
        //$query->select('f.file_title AS filetitle, f.url_download as filename, f.size as filesize');
        //$query->join('LEFT', '#__jdownloads_files AS f ON f.file_id = a.log_file_id');
        
        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $where = array();                                                    
                $where2 = array();
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where(" LOWER (a.log_title) LIKE $search OR LOWER (a.log_file_name) LIKE $search OR LOWER (uc.name) LIKE $search");
           }                      
        }                   
        
        // Filter by log type
        $type = $this->getState('filter.type');
        if (is_numeric($type)) {
            if ($type > 0) {
                $query->where('a.type = '.(int) $type);
            } else {
                 $query->where('(a.type IN (1, 2))');
            }   
        } 
            
        // Add the list ordering clause.
        $orderCol    = $this->state->get('list.ordering', 'a.log_datetime');
        $orderDirn    = $this->state->get('list.direction', 'desc');
        $query->order($db->escape($orderCol.' '.$orderDirn));
        
        return $query;
    }
    
    /**
     * delete the selected log items.
     *
     * @return    boolean
     */
    function delete($cid)
    {
        $db        = $this->getDbo(); 
        $query     = $db->getQuery(true);
        
        $total = count( $cid );
        $logs = join(",", $cid);

        $query->from('#__jdownloads_logs');
        $query->delete();
        $query->where("id IN ($logs)");
        $db->setQuery((string)$query);
        if ($db->execute()){
            return true;
        } else {
            return false;
        }    
    } 
    
    /**
     * Add listed log IDs to the blick IP list
     *
     * @return    boolean
     */    
    public function blockip($cid){
        global $jlistConfig;
        
        $db        = $this->getDbo(); 
        $query     = $db->getQuery(true);
        $total = 0;
        $id = join(",", $cid);
        
        $db->setQuery("SELECT * FROM #__jdownloads_logs WHERE id IN ($id)");
        $logs = $db->loadObjectList();
        if ($logs){
            $blacklist = $jlistConfig['blocking.list'];
            for ($i=0; $i < count($logs); $i++) {
                if (!stristr($blacklist, $logs[$i]->log_ip)){
                    $blacklist = $blacklist.nl2br('\n'.$logs[$i]->log_ip); 
                    $total++;
                }    
            }
            if ($total){
                // update data
                $db->setQuery("UPDATE #__jdownloads_config SET setting_value = '".$blacklist."' WHERE setting_name = 'blocking.list'");
                $db->execute();
                return true;
            } else {
                return false;
            }   
        }
        return false;
    }   
}
?>