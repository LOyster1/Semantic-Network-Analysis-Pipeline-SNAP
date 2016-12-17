<?php


defined('_JEXEC') or die();

jimport('joomla.application.component.modellist'); 

class jdownloadsModeltemplates extends JModelList
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
                'template_name', 'a.template_name',
                'template_typ', 'a.template_typ',
                'template_header_text', 'a.template_header_text',
                'template_subheader_text', 'a.template_subheader_text',
                'template_footer_text', 'a.template_footer_text',
                'template_before_text', 'a.template_before_text',
                'template_text', 'a.template_text',
                'template_after_text', 'a.template_after_text',
                'template_active', 'a.template_active',
                'locked', 'a.locked',
                'note', 'a.note',
                'cols', 'a.cols',
                'checkbox_off', 'a.checkbos_off',
                'symbol_off', 'a.symbol_off',
                'use_to_view_subcats', 'a.use_to_view_subcats',
                'language', 'a.language',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
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

        // Load the language state.
        $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);
        
        // Load the parameters.
        $params = JComponentHelper::getParams('com_jdownloads');
        $this->setState('params', $params);

        // List state information.
        $limit = 0;

        $default_ordering = 'a.template_name';
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
     * @param    string        $id    A prefix for the store id.
     * @return    string        A store id.
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
        
        $jinput    = JFactory::getApplication()->input;
        $jd_tmpl_type = $jinput->get('type', '0', 'integer');
        
        if (!$jd_tmpl_type){
            $session = JFactory::getSession();
            $jd_tmpl_type  = (int) $session->get( 'jd_tmpl_type', '' );  
        } else {
            $session = JFactory::getSession();
            $session->set( 'jd_tmpl_type', $jd_tmpl_type );  
        }
        
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.template_name, a.template_typ, a.template_header_text, a.template_subheader_text, a.template_footer_text, a.template_before_text, a.template_text, a.template_after_text, a.template_active, '  .
                'a.locked, a.note, a.cols, a.checkbox_off, a.symbol_off, a.use_to_view_subcats, a.language, a.checked_out, a.checked_out_time'
            )
        );
        $query->from('`#__jdownloads_templates` AS a');
        
        // Join over the language
        $query->select('l.title AS language_title');
        $query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');
       
        $query->where('(a.template_typ = '.$jd_tmpl_type.')');
        
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('(a.template_name LIKE '.$search.' OR a.note LIKE '.$search.')');
            }
        }                                                   

        // Filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where('a.language = ' . $db->quote($language));
        } 

        // Add the list ordering clause.
        $orderCol    = $this->state->get('list.ordering', 'a.template_name');
        $orderDirn    = $this->state->get('list.direction', 'asc');
        
        //sqlsrv change
        if($orderCol == 'language')
            $orderCol = 'l.title';
        
        $query->order($db->escape($orderCol.' '.$orderDirn));
        return $query;
    }
    
    /* Method to checkin a layout
     *
     * @access    public
     * @return    boolean    True on success
     */
    public function checkin($id)
    {
        $app       = JFactory::getApplication();
        $db        = $this->getDbo();
        $query     = $db->getQuery(true);
        $nullDate  = $db->getNullDate();
        $id = join(",", $id);
           
        $query = $db->getQuery(true)
                ->update($db->quoteName('#__jdownloads_templates'))
                ->set('checked_out = 0')
                ->set('checked_out_time = '.$db->Quote($nullDate))
                ->where('id IN ('.$id.')');

        $db->setQuery($query);
        if ($db->execute())
        {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Method to remove a layout
     *
     * @access    public
     * @return    boolean    True on success
     */
    public function delete($cid = array())
    {
        $app       = JFactory::getApplication();
        $db        = $this->getDbo();
        $total     = count($cid);
        $query     = '';
        $error_msg = '';
        
        if ($total > 0)
        {
            // default template can not erase!
            for( $i=0; $i < $total; $i++ ) {
                $db->setQuery("SELECT locked FROM #__jdownloads_templates WHERE id = ($cid[$i])");
                if ($db->loadResult() == 1 ) {
                    $error_msg = JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_ERROR_IS_LOCKED');
                    break;
                }
            }
            // active template can not erase!
            for( $i=0; $i < $total; $i++ ) {
                $db->setQuery("SELECT template_active FROM #__jdownloads_templates WHERE id = ($cid[$i])");
                if ($db->loadResult() == 1 ) {
                    $error_msg = JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_ERROR_IS_ACTIVE');
                    break;
                }
            }
            // cancel action when error msg
            if ($error_msg != '') {
                echo "<script> alert('".$error_msg."'); window.history.go(-1); </script>\n";
                exit();
            }
                                
            $cids = implode( ',', $cid );
            $query = 'DELETE FROM #__jdownloads_templates'
                    . ' WHERE id IN ('. $cids .')'
                    ;

            $this->_db->setQuery( $query );

            if(!$this->_db->execute()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
        }

        return true;
    } 

    /**
     * Method to activate a layout
     *
     * @access    public
     * @return    boolean    True on success
     */
    public function activate($jd_tmpl_type = 0)
    {
        $app       = JFactory::getApplication();
        $jinput    = JFactory::getApplication()->input;
        $db        = $this->getDbo();
        $query     = $db->getQuery();
        
        $cid       = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $total     = count($cid);
        
        if ($total > 1) {
            echo "<script> alert('".JText::_('COM_JDOWNLOADS_BACKEND_TEMPLATE_ACTIVE_ERROR')."'); window.history.go(-1); </script>\n";
            exit();
        }
        
        if (count( $cid )){
            // first, deactivate the old layout
            $query = 'UPDATE #__jdownloads_templates'
                    . ' SET template_active = 0'
                    . ' WHERE template_typ = '.$jd_tmpl_type
                    . ' AND template_active = 1'
                    ;
            $this->_db->setQuery( $query );
            if(!$this->_db->execute()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }

            // activate the selected            
            $query = 'UPDATE #__jdownloads_templates'
                    . ' SET template_active = 1'
                    . ' WHERE id = '.$cid[0]
                    ;
            $this->_db->setQuery( $query );
            if(!$this->_db->execute()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
        }
        return true;
    }
}
?>