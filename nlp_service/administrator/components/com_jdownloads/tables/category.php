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

defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
jimport('joomla.filesystem.file');
jimport('joomla.database.tablenested'); 
 
/**
 * Categories Table class
 */
class jdownloadsTablecategory extends JTable
{
	
/**
     * Object property holding the primary key of the parent node.  Provides
     * adjacency list data for nodes.
     *
     * @var    integer
     * @since  11.1
     */
    public $parent_id;

    /**
     * Object property holding the depth level of the node in the tree.
     *
     * @var    integer
     * @since  11.1
     */
    public $level;

    /**
     * Object property holding the left value of the node for managing its
     * placement in the nested sets tree.
     *
     * @var    integer
     * @since  11.1
     */
    public $lft;

    /**
     * Object property holding the right value of the node for managing its
     * placement in the nested sets tree.
     *
     * @var    integer
     * @since  11.1
     */
    public $rgt;

    /**
     * Object property holding the alias of this node used to constuct the
     * full text path, forward-slash delimited.
     *
     * @var    string
     * @since  11.1
     */
    public $alias;

    /**
     * Object property to hold the location type to use when storing the row.
     * Possible values are: ['before', 'after', 'first-child', 'last-child'].
     *
     * @var    string
     * @since  11.1
     */
    protected $_location;

    /**
     * Object property to hold the primary key of the location reference node to
     * use when storing the row.  A combination of location type and reference
     * node describes where to store the current node in the tree.
     *
     * @var integer
     * @since  11.1
     */
    protected $_location_id;

    /**
     * An array to cache values in recursive processes.
     *
     * @var   array
     * @since  11.1
     */
    protected $_cache = array();

    /**
     * Debug level
     *
     * @var    integer
     * @since  11.1
     */
    protected $_debug = 0;

    /**
     * Sets the debug level on or off
     *
     * @param   integer  $level  0 = off, 1 = on
     */
    public function debug($level)
    {
        $this->_debug = intval($level);
    }    
    
    
    /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__jdownloads_categories', 'id', $db);
        // we need also the 'tags' functionality
        JTableObserverTags::createObserver($this, array('typeAlias' => 'com_jdownloads.category'));        
	}
    
    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return    boolean    True on success.
     */
    public function checkData($isNew)
    {
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );
                
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        
        $jinput = JFactory::getApplication()->input;
        
        /**
        * @desc  check icon upload field
        *           if pic selected for upload:
        *           - check image typ
        *           - check whether filename exists. If so, rename the new file. 
        *           - move new file to catimages
        */          
        $file = JArrayHelper::getValue($_FILES,'picnew',array('tmp_name'=>'')); 
        if ($file['tmp_name'] != '' && JDownloadsHelper::fileIsPicture($file['name'])){
            $upload_dir = JPATH_SITE.'/images/jdownloads/catimages/'; 
            $file['name'] = JFile::makeSafe($file['name']);
            if (!JFile::upload($file['tmp_name'], $upload_dir.$file['name'])){
                // move error
                return false;
            } else {
                // move ok - set new file name as selected
                $this->pic = $file['name'];
            }        
        } else {
                // check selected pic
                $picname = $jinput->get('pic');
                if (isset($picname)){
                    $this->pic = $jinput->get('pic');    
                }
        }       
        
        // set new level and access value
        if ($this->parent_id > 1){
            if ($isNew){
                $query = "SELECT * FROM #__jdownloads_categories WHERE id = '$this->parent_id'";
                $db->setQuery( $query );
                $parent_cat = $db->loadObject();
                $this->level = $parent_cat->level + 1;
                $this->access = $parent_cat->access;
            }
        } else {
            // has no parents so we must delete the cat_dir_parent
            $this->cat_dir_parent = '';
        }       
        // check date and user id fields
        if (!$isNew){
            // set user id in modified field
            $this->modified_user_id = $user->id; 
            // fill out modified date field
            $this->modified_time = JHtml::_('date', '','Y-m-d H:i:s');
        } else {
             // fill out created date field 
            $this->created_time = JHtml::_('date', '','Y-m-d H:i:s');
            if (!$this->created_user_id){
                $this->created_user_id = $user->id;
            }    
        }    
        return true;
    }
    
     /**
     * Method to set the location of a node in the tree object.  This method does not
     * save the new location to the database, but will set it in the object so
     * that when the node is stored it will be stored in the new location.
     *
     * @param   integer  $referenceId  The primary key of the node to reference new location by.
     * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/setLocation
     * @since   11.1
     */
    public function setLocation($referenceId, $position = 'after')
    {
        // Make sure the location is valid.
        if (($position != 'before') && ($position != 'after') &&
            ($position != 'first-child') && ($position != 'last-child')) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_LOCATION', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Set the location properties.
        $this->_location = $position;
        $this->_location_id = $referenceId;

        return true;
    }
    
   
    /**
     * Method to rebuild the node's path field from the alias values of the
     * nodes from the current node to the root node of the tree.
     *
     * @param   integer  $pk  Primary key of the node for which to get the path.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/rebuildPath
     * @since   11.1
     */
    public function rebuildPath($pk = null)
    {
        // If there is no alias or path field, just return true.
        /*if (!property_exists($this, 'alias') || !property_exists($this, 'path')) {
            return true;
        } */

        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Get the aliases for the path from the node to the root node.
        $query = $this->_db->getQuery(true);
        $query->select('p.cat_dir');
        $query->from($this->_tbl.' AS n, '.$this->_tbl.' AS p');
        $query->where('n.lft BETWEEN p.lft AND p.rgt');
        $query->where('n.'.$this->_tbl_key.' = '. (int) $pk);
        $query->order('p.lft');
        $this->_db->setQuery($query);

        $segments = $this->_db->loadColumn();

        // Make sure to remove the root path if it exists in the list.
        if ($segments[0] == '') {
            array_shift($segments);
        }
        
        // remove the cat dir from current sub cat in the list. 
        array_pop($segments);

        // Build the path.
        $path = trim(implode('/', $segments), ' /\\');

        // Update the path field for the node.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('cat_dir_parent = '.$this->_db->quote($path));
        $query->where($this->_tbl_key.' = '.(int) $pk);
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILDPATH_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Update the current record's path to the new one:
        $this->cat_dir_parent = $path;

        return true;
    }    
    
    public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '')
    {
        // If no parent is provided, try to find it.
        if ($parentId === null)
        {
            // Get the root item.
            $parentId = $this->getRootId();
            if ($parentId === false) return false;

        }

        // Build the structure of the recursive query.
        if (!isset($this->_cache['rebuild.sql']))
        {
            $query    = $this->_db->getQuery(true);
            $query->select($this->_tbl_key.', cat_dir, cat_dir_parent');
            //$query->select($this->_tbl_key.', alias');
            $query->from($this->_tbl);
            $query->where('parent_id = %d');
            $query->order('parent_id, lft');

            // If the table has an ordering field, use that for ordering.
            /*if (property_exists($this, 'ordering')) {
                $query->order('parent_id, ordering, lft');
            } else { 
                $query->order('parent_id, lft');
            } */

            $this->_cache['rebuild.sql'] = (string) $query;            
        }

        // Make a shortcut to database object.

        // Assemble the query to find all children of this node.
        $this->_db->setQuery(sprintf($this->_cache['rebuild.sql'], (int) $parentId));
        $children = $this->_db->loadObjectList();

        // The right value of this node is the left value + 1
        $rightId = $leftId + 1;

        // execute this function recursively over all children
        foreach ($children as $node)
        {
            // $rightId is the current right value, which is incremented on recursion return.
            // Increment the level for the children.
            // Add this item's alias to the path (but avoid a leading /)
            $rightId = $this->rebuild($node->{$this->_tbl_key}, $rightId, $level + 1, $path.(empty($path) ? '' : '/').$node->cat_dir);

            // If there is an update failure, return false to break out of the recursion.
            if ($rightId === false) return false;
        }

        // We've got the left value, and now that we've processed
        // the children of this node we also know the right value.
        $ds = DS;
        $path = substr($path, 0, strrpos($path, '/') );
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = '. (int) $leftId);
        $query->set('rgt = '. (int) $rightId);
        $query->set('level = '.(int) $level);
        $query->set('cat_dir_parent = '.$this->_db->quote($path));
        $query->where($this->_tbl_key.' = '. (int)$parentId);
        $this->_db->setQuery($query);
        
        // If there is an update failure, return false to break out of the recursion.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }
        
        // Return the right value of this node + 1.
        return $rightId + 1;
    }  
    
        /**
     * Method to store a node in the database table.
     *
     * @param   boolean  True to update null values as well.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/store
     * @since   11.1
     */
    public function store($updateNulls = false)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        if ($this->_debug) {
            echo "\n".get_class($this)."::store\n";
            $this->_logtable(true, false);
        }
        /*
         * If the primary key is empty, then we assume we are inserting a new node into the
         * tree.  From this point we would need to determine where in the tree to insert it.
         */
        if (empty($this->$k))
        {
            /*
             * We are inserting a node somewhere in the tree with a known reference
             * node.  We have to make room for the new node and set the left and right
             * values before we insert the row.
             */
            if ($this->_location_id >= 0)
            {
                // Lock the table for writing.
                if (!$this->_lock()) {
                    // Error message set in lock method.
                    return false;
                }

                // We are inserting a node relative to the last root node.
                if ($this->_location_id == 0)
                {
                    // Get the last root node as the reference node.
                    $query = $this->_db->getQuery(true);
                    $query->select($this->_tbl_key.', parent_id, level, lft, rgt');
                    $query->from($this->_tbl);
                    $query->where('parent_id = 0');
                    $query->order('lft DESC');
                    $this->_db->setQuery($query, 0, 1);
                    $reference = $this->_db->loadObject();

                    // Check for a database error.
                    if ($this->_db->getErrorNum())
                    {
                        $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
                        $this->setError($e);
                        $this->_unlock();
                        return false;
                    }
                    if ($this->_debug) {
                        $this->_logtable(false);
                    }
                }

                // We have a real node set as a location reference.
                else
                {
                    // Get the reference node by primary key.
                    if (!$reference = $this->_getNode($this->_location_id))
                    {
                        // Error message set in getNode method.
                        $this->_unlock();
                        return false;
                    }
                }

                // Get the reposition data for shifting the tree and re-inserting the node.
                if (!($repositionData = $this->_getTreeRepositionData($reference, 2, $this->_location)))
                {
                    // Error message set in getNode method.
                    $this->_unlock();
                    return false;
                }

                // Create space in the tree at the new location for the new node in left ids.
                $query = $this->_db->getQuery(true);
                $query->update($this->_tbl);
                $query->set('lft = lft + 2');
                $query->where($repositionData->left_where);
                $this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

                // Create space in the tree at the new location for the new node in right ids.
                $query = $this->_db->getQuery(true);
                $query->update($this->_tbl);
                $query->set('rgt = rgt + 2');
                $query->where($repositionData->right_where);
                $this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

                // Set the object values.
                $this->parent_id    = $repositionData->new_parent_id;
                $this->level        = $repositionData->new_level;
                $this->lft          = $repositionData->new_lft;
                $this->rgt          = $repositionData->new_rgt;
            }
            else
            {
                // Negative parent ids are invalid
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_INVALID_PARENT_ID'));
                $this->setError($e);
                return false;
            }
        }

        /*
         * If we have a given primary key then we assume we are simply updating this
         * node in the tree.  We should assess whether or not we are moving the node
         * or just updating its data fields.
         */
        else
        {
            // If the location has been set, move the node to its new location.
            if ($this->_location_id > 0)
            {
                if (!$this->moveByReference($this->_location_id, $this->_location, $this->$k)) {
                    // Error message set in move method.
                    return false;
                }
            }

            // Lock the table for writing.
          /*  if (!$this->_lock()) {
                // Error message set in lock method.
                return false;
            } */
        }

        // Store the row to the database.
        if (!parent::store($updateNulls))
        {
            $this->_unlock();
            return false;
        }
        if ($this->_debug) {
            $this->_logtable();
        }

        // Unlock the table for writing.
        $this->_unlock();

        return true;
    } 
    
    /**
     * Method to get nested set properties for a node in the tree.
     *
     * @param   integer  $id   Value to look up the node by.
     * @param   string   $key  Key to look up the node by.
     *
     * @return  mixed    Boolean false on failure or node object on success.
     *
     * @since   11.1
     */
    protected function _getNode($id, $key = null)
    {
        // Determine which key to get the node base on.
        switch ($key)
        {
            case 'parent':
                $k = 'parent_id';
                break;
            case 'left':
                $k = 'lft';
                break;
            case 'right':
                $k = 'rgt';
                break;
            default:
                $k = $this->_tbl_key;
                break;
        }

        // Get the node data.
        $query = $this->_db->getQuery(true);
        $query->select($this->_tbl_key.', parent_id, level, lft, rgt');
        $query->from($this->_tbl);
        $query->where($k.' = '.(int) $id);
        $this->_db->setQuery($query, 0, 1);

        $row = $this->_db->loadObject();

        // Check for a database error or no $row returned
        if ((!$row) || ($this->_db->getErrorNum()))
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETNODE_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Do some simple calculations.
        $row->numChildren = (int) ($row->rgt - $row->lft - 1) / 2;
        $row->width = (int) $row->rgt - $row->lft + 1;

        return $row;
    } 
    
    /**
     * Method to get various data necessary to make room in the tree at a location
     * for a node and its children.  The returned data object includes conditions
     * for SQL WHERE clauses for updating left and right id values to make room for
     * the node as well as the new left and right ids for the node.
     *
     * @param   object   $referenceNode  A node object with at least a 'lft' and 'rgt' with
     *                                   which to make room in the tree around for a new node.
     * @param   integer  $nodeWidth      The width of the node for which to make room in the tree.
     * @param   string   $position       The position relative to the reference node where the room
     *                                     should be made.
     *
     * @return  mixed    Boolean false on failure or data object on success.
     *
     * @since   11.1
     */
    protected function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before')
    {
        // Make sure the reference an object with a left and right id.
        if (!is_object($referenceNode) && isset($referenceNode->lft) && isset($referenceNode->rgt)) {
            return false;
        }

        // A valid node cannot have a width less than 2.
        if ($nodeWidth < 2) return false;

        // Initialise variables.
        $k = $this->_tbl_key;
        $data = new stdClass;

        // Run the calculations and build the data object by reference position.
        switch ($position)
        {
            case 'first-child':
                $data->left_where        = 'lft > '.$referenceNode->lft;
                $data->right_where        = 'rgt >= '.$referenceNode->lft;

                $data->new_lft            = $referenceNode->lft + 1;
                $data->new_rgt            = $referenceNode->lft + $nodeWidth;
                $data->new_parent_id    = $referenceNode->$k;
                $data->new_level        = $referenceNode->level + 1;
                break;

            case 'last-child':
                $data->left_where        = 'lft > '.($referenceNode->rgt);
                $data->right_where        = 'rgt >= '.($referenceNode->rgt);

                $data->new_lft            = $referenceNode->rgt;
                $data->new_rgt            = $referenceNode->rgt + $nodeWidth - 1;
                $data->new_parent_id    = $referenceNode->$k;
                $data->new_level        = $referenceNode->level + 1;
                break;

            case 'before':
                $data->left_where        = 'lft >= '.$referenceNode->lft;
                $data->right_where        = 'rgt >= '.$referenceNode->lft;

                $data->new_lft            = $referenceNode->lft;
                $data->new_rgt            = $referenceNode->lft + $nodeWidth - 1;
                $data->new_parent_id    = $referenceNode->parent_id;
                $data->new_level        = $referenceNode->level;
                break;

            default:
            case 'after':
                $data->left_where        = 'lft > '.$referenceNode->rgt;
                $data->right_where        = 'rgt > '.$referenceNode->rgt;

                $data->new_lft            = $referenceNode->rgt + 1;
                $data->new_rgt            = $referenceNode->rgt + $nodeWidth;
                $data->new_parent_id    = $referenceNode->parent_id;
                $data->new_level        = $referenceNode->level;
                break;
        }

        if ($this->_debug)
        {
            echo "\nRepositioning Data for $position" .
                    "\n-----------------------------------" .
                    "\nLeft Where:    $data->left_where" .
                    "\nRight Where:   $data->right_where" .
                    "\nNew Lft:       $data->new_lft" .
                    "\nNew Rgt:       $data->new_rgt".
                    "\nNew Parent ID: $data->new_parent_id".
                    "\nNew Level:     $data->new_level" .
                    "\n";
        }

        return $data;
    }
    
/**
     * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
     * Negative numbers move the row up in the sequence and positive numbers move it down.
     *
     * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
     * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the
     *                           ordering values.
     *
     * @return  mixed    Boolean true on success.
     *
     * @link    http://docs.joomla.org/JTable/move
     * @since   11.1
     */
    public function move($delta, $where = '')
    {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = $this->$k;

        $query = $this->_db->getQuery(true);
        $query->select($k);
        $query->from($this->_tbl);
        $query->where('parent_id = '.$this->parent_id);
        if ($where) {
            $query->where($where);
        }
        $position = 'after';
        if($delta > 0)
        {
            $query->where('rgt > '.$this->rgt);
            $query->order('rgt ASC');
            $position = 'after';
        } else {
            $query->where('lft < '.$this->lft);
            $query->order('lft DESC');
            $position = 'before';
        }

        $this->_db->setQuery($query);
        $referenceId = $this->_db->loadResult();

        if ($referenceId) {
            return $this->moveByReference($referenceId, $position, $pk);
        }
        else {
            return false;
        }
    }

    /**
     * Method to move a node and its children to a new location in the tree.
     *
     * @param   integer  $referenceId  The primary key of the node to reference new location by.
     * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
     * @param   integer  $pk           The primary key of the node to move.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/moveByReference
     * @since   11.1
     */

    public function moveByReference($referenceId, $position = 'after', $pk = null)
    {
        if ($this->_debug) {
            echo "\nMoving ReferenceId:$referenceId, Position:$position, PK:$pk";
        }

        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Get the node by id.
        if (!$node = $this->_getNode($pk)) {
            // Error message set in getNode method.
            return false;
        }

        // Get the ids of child nodes.
        $query = $this->_db->getQuery(true);
        $query->select($k);
        $query->from($this->_tbl);
        $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
        $this->_db->setQuery($query);
        $children = $this->_db->loadColumn();

        // Check for a database error.
        if ($this->_db->getErrorNum())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }
        if ($this->_debug) {
            $this->_logtable(false);
        }

        // Cannot move the node to be a child of itself.
        if (in_array($referenceId, $children))
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_NODE_RECURSION', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Lock the table for writing.
        if (!$this->_lock()) {
            return false;
        }

        /*
         * Move the sub-tree out of the nested sets by negating its left and right values.
        */
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft * (-1), rgt = rgt * (-1)');
        $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
        $this->_db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        /*
         * Close the hole in the tree that was opened by removing the sub-tree from the nested sets.
         */
        // Compress the left values.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft - '.(int) $node->width);
        $query->where('lft > '.(int) $node->rgt);
        $this->_db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // Compress the right values.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('rgt = rgt - '.(int) $node->width);
        $query->where('rgt > '.(int) $node->rgt);
        $this->_db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // We are moving the tree relative to a reference node.
        if ($referenceId)
        {
            // Get the reference node by primary key.
            if (!$reference = $this->_getNode($referenceId))
            {
                // Error message set in getNode method.
                $this->_unlock();
                return false;
            }

            // Get the reposition data for shifting the tree and re-inserting the node.
            if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, $position))
            {
                // Error message set in getNode method.
                $this->_unlock();
                return false;
            }
        }

        // We are moving the tree to be the last child of the root node
        else
        {
            // Get the last root node as the reference node.
            $query = $this->_db->getQuery(true);
            $query->select($this->_tbl_key.', parent_id, level, lft, rgt');
            $query->from($this->_tbl);
            $query->where('parent_id = 0');
            $query->order('lft DESC');
            $this->_db->setQuery($query, 0, 1);
            $reference = $this->_db->loadObject();

            // Check for a database error.
            if ($this->_db->getErrorNum())
            {
                $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
                $this->setError($e);
                $this->_unlock();
                return false;
            }
            if ($this->_debug) {
                $this->_logtable(false);
            }

            // Get the reposition data for re-inserting the node after the found root.
            if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, 'last-child'))
            {
                // Error message set in getNode method.
                $this->_unlock();
                return false;
            }
        }

        /*
         * Create space in the nested sets at the new location for the moved sub-tree.
         */
        // Shift left values.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft + '.(int) $node->width);
        $query->where($repositionData->left_where);
        $this->_db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // Shift right values.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('rgt = rgt + '.(int) $node->width);
        $query->where($repositionData->right_where);
        $this->_db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        /*
         * Calculate the offset between where the node used to be in the tree and
         * where it needs to be in the tree for left ids (also works for right ids).
         */
        $offset = $repositionData->new_lft - $node->lft;
        $levelOffset = $repositionData->new_level - $node->level;

        // Move the nodes back into position in the tree using the calculated offsets.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('rgt = '.(int) $offset.' - rgt');
        $query->set('lft = '.(int) $offset.' - lft');
        $query->set('level = level + '.(int) $levelOffset);
        $query->where('lft < 0');
        $this->_db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // Set the correct parent id for the moved node if required.
        if ($node->parent_id != $repositionData->new_parent_id)
        {
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);

            // Update the title and alias fields if they exist for the table.
            if (property_exists($this, 'title') && $this->title !== null) {
                $query->set('title = '.$this->_db->Quote($this->title));
            }
            if (property_exists($this, 'alias') && $this->alias !== null) {
                $query->set('alias = '.$this->_db->Quote($this->alias));
            }

            $query->set('parent_id = '.(int) $repositionData->new_parent_id);
            $query->where($this->_tbl_key.' = '.(int) $node->$k);
            $this->_db->setQuery($query);

            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');
        }

        // Unlock the table for writing.
        $this->_unlock();

        // Set the object values.
        $this->parent_id = $repositionData->new_parent_id;
        $this->level = $repositionData->new_level;
        $this->lft = $repositionData->new_lft;
        $this->rgt = $repositionData->new_rgt;

        return true;
    }

    /**
     * Method to delete a node and, optionally, its child nodes from the table.
     *
     * @param   integer  $pk        The primary key of the node to delete.
     * @param   boolean  $children  True to delete child nodes, false to move them up a level.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/delete
     */
    public function delete($pk = null, $children = true)
    {
       
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
        
        if ($this->hasChildren($pk)){
           // category has children so we can not delete it 
           JError::raiseNotice(100, JText::_('COM_JDOWNLOADS_BE_NO_DEL_SUBCATS_EXISTS'));
           return false; 
        }
        
        if ($this->hasDownloads($pk)){
           // category has downloads so we can not delete it 
           JError::raiseNotice(100, JText::_('COM_JDOWNLOADS_BE_NO_DEL_FILES_EXISTS'));
           return false; 
        }        
        

        // Lock the table for writing.
        if (!$this->_lock()) {
            // Error message set in lock method.
            return false;
        }

        // If tracking assets, remove the asset first.
        if ($this->_trackAssets)
        {
            $name        = $this->_getAssetName();
            $asset        = JTable::getInstance('Asset');

            // Lock the table for writing.
            if (!$asset->_lock()) {
                // Error message set in lock method.
                return false;
            }

            if ($asset->loadByName($name)) {
                // Delete the node in assets table.
                if (!$asset->delete(null, $children)) {
                    $this->setError($asset->getError());
                    $asset->_unlock();
                    return false;
                }
                $asset->_unlock();
            }
            else {
                $this->setError($asset->getError());
                $asset->_unlock();
                return false;
            }
        }

        // Get the node by id.
        if (!$node = $this->_getNode($pk))
        {
            // Error message set in getNode method.
            $this->_unlock();
            return false;
        }
        
        // get first the folder name, so we can later delete this folder
        $query = $this->_db->getQuery(true);
        $query->select('cat_dir, cat_dir_parent');
        $query->from($this->_tbl);
        $query->where('id = '.(int)$pk);
        $this->_db->setQuery($query);
        $cat_dirs = $this->_db->loadObject();

        // Should we delete all children along with the node?
        if ($children)
        {
            // Delete the node and all of its children.
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from($this->_tbl);
            $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Compress the left values.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set('lft = lft - '.(int) $node->width);
            $query->where('lft > '.(int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Compress the right values.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set('rgt = rgt - '.(int) $node->width);
            $query->where('rgt > '.(int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
        }

        // Leave the children and move them up a level.
        else
        {
            // Delete the node.
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from($this->_tbl);
            $query->where('lft = '.(int) $node->lft);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Shift all node's children up a level.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set('lft = lft - 1');
            $query->set('rgt = rgt - 1');
            $query->set('level = level - 1');
            $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Adjust all the parent values for direct children of the deleted node.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set('parent_id = '.(int) $node->parent_id);
            $query->where('parent_id = '.(int) $node->$k);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Shift all of the left values that are right of the node.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set('lft = lft - 2');
            $query->where('lft > '.(int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Shift all of the right values that are right of the node.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set('rgt = rgt - 2');
            $query->where('rgt > '.(int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
        }


        // delete now the folder
        if ($cat_dirs){
            if ($cat_dirs->cat_dir_parent != ''){
                $cat_dir = $cat_dirs->cat_dir_parent.'/'.$cat_dirs->cat_dir;
            } else {
                $cat_dir = $cat_dirs->cat_dir;
            }    
            JDownloadsHelper::deleteCategoryFolder($cat_dir);
        }        
        
        // Unlock the table for writing.
        $this->_unlock();

        return true;
    } 
    
    /**
     * Method to move a node one position to the left in the same level.
     *
     * @param   integer  $pk  Primary key of the node to move.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/orderUp
     */
    public function orderUp($pk)
    {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Lock the table for writing.
        if (!$this->_lock()) {
            // Error message set in lock method.
            return false;
        }

        // Get the node by primary key.
        if (!$node = $this->_getNode($pk))
        {
            // Error message set in getNode method.
            $this->_unlock();
            return false;
        }

        // Get the left sibling node.
        if (!$sibling = $this->_getNode($node->lft - 1, 'right'))
        {
            // Error message set in getNode method.
            $this->_unlock();
            return false;
        }

        // Get the primary keys of child nodes.
        $query = $this->_db->getQuery(true);
        $query->select($this->_tbl_key);
        $query->from($this->_tbl);
        $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
        $this->_db->setQuery($query);
        $children = $this->_db->loadColumn();

        // Check for a database error.
        if ($this->_db->getErrorNum())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        // Shift left and right values for the node and it's children.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft - '.(int) $sibling->width);
        $query->set('rgt = rgt - '.(int) $sibling->width);
        $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        // Shift left and right values for the sibling and it's children.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft + '.(int) $node->width);
        $query->set('rgt = rgt + '.(int) $node->width);
        $query->where('lft BETWEEN '.(int) $sibling->lft.' AND '.(int) $sibling->rgt);
        $query->where($this->_tbl_key.' NOT IN ('.implode(',', $children).')');
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERUP_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        // Unlock the table for writing.
        $this->_unlock();

        return true;
    }

    /**
     * Method to move a node one position to the right in the same level.
     *
     * @param   integer  $pk  Primary key of the node to move.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/orderDown
     */
    public function orderDown($pk)
    {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Lock the table for writing.
        if (!$this->_lock()) {
            // Error message set in lock method.
            return false;
        }

        // Get the node by primary key.
        if (!$node = $this->_getNode($pk))
        {
            // Error message set in getNode method.
            $this->_unlock();
            return false;
        }

        // Get the right sibling node.
        if (!$sibling = $this->_getNode($node->rgt + 1, 'left'))
        {
            // Error message set in getNode method.
            $query->unlock($this->_db);
            $this->_locked=false;
            return false;
        }

        // Get the primary keys of child nodes.
        $query = $this->_db->getQuery(true);
        $query->select($this->_tbl_key);
        $query->from($this->_tbl);
        $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
        $this->_db->setQuery($query);
        $children = $this->_db->loadColumn();

        // Check for a database error.
        if ($this->_db->getErrorNum())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERDOWN_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        // Shift left and right values for the node and it's children.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft + '.(int) $sibling->width);
        $query->set('rgt = rgt + '.(int) $sibling->width);
        $query->where('lft BETWEEN '.(int) $node->lft.' AND '.(int) $node->rgt);
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERDOWN_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        // Shift left and right values for the sibling and it's children.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft - '.(int) $node->width);
        $query->set('rgt = rgt - '.(int) $node->width);
        $query->where('lft BETWEEN '.(int) $sibling->lft.' AND '.(int) $sibling->rgt);
        $query->where($this->_tbl_key.' NOT IN ('.implode(',', $children).')');
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_ORDERDOWN_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        // Unlock the table for writing.
        $this->_unlock();

        return true;
    }
    
    /**
     * Method to update order of table rows
     *
     * @param   array    $idArray    id numbers of rows to be reordered
     * @param   array    $lft_array  lft values of rows to be reordered
     *
     * @return  integer  1 + value of root rgt on success, false on failure
     */
    public function saveorder($idArray = null, $lft_array = null)
    {
        // Validate arguments
        if (is_array($idArray) && is_array($lft_array) && count($idArray) == count($lft_array))
        {
            for ($i = 0, $count = count($idArray); $i < $count; $i++)
            {
                // Do an update to change the lft values in the table for each id
                $query = $this->_db->getQuery(true);
                $query->update($this->_tbl);
                $query->where($this->_tbl_key . ' = ' . (int) $idArray[$i]);
                $query->set('lft = ' . (int) $lft_array[$i]);
                $this->_db->setQuery($query);

                // Check for a database error.
                if (!$this->_db->execute())
                {
                    $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REORDER_FAILED', get_class($this), $this->_db->getErrorMsg()));
                    $this->setError($e);
                    $this->_unlock();
                    return false;
                }

                if ($this->_debug) {
                    $this->_logtable();
                }

            }

            return $this->rebuild();
        }
        else {
            return false;
        }
    }
    

    /**
     * Gets the ID of the root item in the tree
     *
     * @return  mixed    The ID of the root row, or false and the internal error is set.
     *
     */
    public function getRootId()
    {
        // Get the root item.
        $k = $this->_tbl_key;

        // Test for a unique record with parent_id = 0
        $query = $this->_db->getQuery(true);
        $query->select($k);
        $query->from($this->_tbl);
        $query->where('parent_id = 0');
        $this->_db->setQuery($query);

        $result = $this->_db->loadColumn();

        if ($this->_db->getErrorNum())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        if (count($result) == 1) {
            $parentId = $result[0];
        }
        else
        {
            // Test for a unique record with lft = 0
            $query = $this->_db->getQuery(true);
            $query->select($k);
            $query->from($this->_tbl);
            $query->where('lft = 0');
            $this->_db->setQuery($query);

            $result = $this->_db->loadColumn();
            if ($this->_db->getErrorNum())
            {
                $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $this->_db->getErrorMsg()));
                $this->setError($e);
                return false;
            }

            if (count($result) == 1) {
                $parentId = $result[0];
            }
            elseif (property_exists($this, 'alias'))
            {
                // Test for a unique record alias = root
                $query = $this->_db->getQuery(true);
                $query->select($k);
                $query->from($this->_tbl);
                $query->where('alias = '.$this->_db->quote('root'));
                $this->_db->setQuery($query);

                $result = $this->_db->loadColumn();
                if ($this->_db->getErrorNum())
                {
                    $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $this->_db->getErrorMsg()));
                    $this->setError($e);
                    return false;
                }

                if (count($result) == 1) {
                    $parentId = $result[0];
                }
                else
                {
                    $e = new JException(JText::_('JLIB_DATABASE_ERROR_ROOT_NODE_NOT_FOUND'));
                    $this->setError($e);
                    return false;
                }
            }
            else
            {
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_ROOT_NODE_NOT_FOUND'));
                $this->setError($e);
                return false;
            }
        }

        return $parentId;
    }
       
    
    /**
     * Method to create a log table in the buffer optionally showing the query and/or data.
     *
     * @param   boolean  $showData   True to show data
     * @param   boolean  $showQuery  True to show query
     *
     * @return  void
     *
     * @since   11.1
     */
    protected function _logtable($showData = true, $showQuery = true)
    {
        $sep    = "\n".str_pad('', 40, '-');
        $buffer    = '';
        if ($showQuery) {
            $buffer .= "\n".$this->_db->getQuery().$sep;
        }

        if ($showData)
        {
            $query = $this->_db->getQuery(true);
            $query->select($this->_tbl_key.', parent_id, lft, rgt, level');
            $query->from($this->_tbl);
            $query->order($this->_tbl_key);
            $this->_db->setQuery($query);

            $rows = $this->_db->loadRowList();
            $buffer .= sprintf("\n| %4s | %4s | %4s | %4s |", $this->_tbl_key, 'par', 'lft', 'rgt');
            $buffer .= $sep;

            foreach ($rows as $row) {
                $buffer .= sprintf("\n| %4s | %4s | %4s | %4s |", $row[0], $row[1], $row[2], $row[3]);
            }
            $buffer .= $sep;
        }
        echo $buffer;
    }
    
/**
     * Method to determine if a node is a leaf node in the tree (has no children).
     *
     * @param   integer  $pk  Primary key of the node to check.
     *
     * @return  boolean  True if a leaf node.
     *
     * @link    http://docs.joomla.org/JTableNested/isLeaf
     * @since   11.1
     */
    public function isLeaf($pk = null)
    {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Get the node by primary key.
        if (!$node = $this->_getNode($pk)) {
            // Error message set in getNode method.
            return false;
        }

        // The node is a leaf node.
        return (($node->rgt - $node->lft) == 1);
    }    
    
    /**
     * Method to run an update query and check for a database error
     *
     * @params  string   $query
     * @param   string   $errorMessage
     *
     * @return  boolean  False on exception
     *
     */
    protected function _runQuery($query, $errorMessage)
    {
        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('$errorMessage', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }
        if ($this->_debug) {
            $this->_logtable();
        }
    }
    
    /**
     * Method to handle the category folder actions (create/move/rename)
     *
     * @param   string   $isNew                 true when used
     * @param   string   $catChanged            true when used
     * @param   string   $titleChanged          true when used 
     * @param   string   $checked_cat_title     the new/changed and checked category folder name  
     *
     * @return  boolean  $result True on success
     */
    public function checkCategoryFolder($isNew, $catChanged, $titleChanged, $checked_cat_title, $cat_dir_changed_manually)
    {
       global $jlistConfig;
       
       jimport( 'joomla.filesystem.folder' );
       jimport( 'joomla.filesystem.file' );       
       
       $jinput = JFactory::getApplication()->input;
               
       $root_dir_path = $jlistConfig['files.uploaddir'];
       
       if (!$isNew && !$catChanged && !$titleChanged && !$cat_dir_changed_manually){
           // nothing to do
           return true;
       }
       
       if ($isNew){
          // get parent dir when selected
          if ($this->parent_id > 1){
              $this->cat_dir_parent = $this->getParentCategoryPath($this->parent_id);
          }
          if ($this->cat_dir_parent != ''){
              $cat_dir_path = $root_dir_path.DS.$this->cat_dir_parent.DS.$this->cat_dir;
          } else {
              $cat_dir_path = $root_dir_path.DS.$this->cat_dir;
          }

          // create the new folder when he not exists
          if (!JFolder::exists($cat_dir_path)){
              $result = JFolder::create($cat_dir_path);
              // copy also a empty index.html
              JFile::copy(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jdownloads'.DS.'index.html', $cat_dir_path.DS.'index.html');
          } else {
              // new category but the given cat_dir exists always... 
              // TODO: problem, we have stored the new category in DB but can not create the new folder - so we have now two categories with the same folder path?   
              $result = false;
          }
           
       } else {
           // build the new folder path to move or rename the folder when needed
           if ($this->parent_id > 1){
               $this->cat_dir_parent = $this->getParentCategoryPath($this->parent_id);
           } else {
               $this->cat_dir_parent = '';
           }
           if ($this->cat_dir_parent != ''){
               $new_cat_dir_path = $root_dir_path.DS.$this->cat_dir_parent.DS.$this->cat_dir;
           } else {
               $new_cat_dir_path = $root_dir_path.DS.$this->cat_dir;
           }
           
           // we need also the old folder path  
           $old_parent = $jinput->get('cat_dir_parent_org', '', 'string');
           $old_dir    = $jinput->get('cat_dir_org', '', 'string');  
           if ($old_parent != ''){
               $old_cat_dir_path = $root_dir_path.DS.$old_parent.DS.$old_dir;
           } else {
               $old_cat_dir_path = $root_dir_path.DS.$old_dir;
           }           
       
           // category is not new - so we must at first check, whether the title is changed.
           if ($titleChanged || $cat_dir_changed_manually){
               // get the old and new cat dir and rename it
               if (JFolder::exists($old_cat_dir_path)){
                   $result = JFolder::move($old_cat_dir_path, $new_cat_dir_path);
               } else {
                   JError::raiseWarning( 100, JText::sprintf('COM_JDOWNLOADS_CATSEDIT_ERROR_CHECK_FOLDER', $old_cat_dir_path ));
                   $result = false; 
               }
           }    
           
           // we must only check this, when the user have not changed the category title
           // if so, we must move the category folder complete to the new position
           if ($catChanged && !$titleChanged){
               // move it to the new location when exists
               if (JFolder::exists($old_cat_dir_path)){
                   $result = jdownloadsHelper::moveDirs($old_cat_dir_path.DS,$new_cat_dir_path.'/', true, $msg, true, false, false);
                   if ($result !== true) {
                       // $result has a error message from file/folder operations
                       JError::raiseWarning( 100, $result );
                       $result = false;                        
                   }  
               } else {
                   $result = false;
               }   
           } 
       }
       return $result;    
    }
    
    // check whether a category has children
    // @return    boolean    True on success.
    public function hasChildren($pk)
    {
        $query = $this->_db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__jdownloads_categories');
        $query->where('parent_id = '.(int)$pk);
        $this->_db->setQuery($query);
        if ($this->_db->loadResult() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    // check whether a category has downloads
    // @return    boolean    True on success.
    public function hasDownloads($pk)
    {
        $query = $this->_db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__jdownloads_files');
        $query->where('cat_id = '.(int)$pk);
        $this->_db->setQuery($query);
        if ($this->_db->loadResult() > 0){
            return true;
        } else {
            return false;
        }
    } 

    // get the path from a given parent_id
    // @return    path    The folder path from the parent category
    public function getParentCategoryPath($parent_id)
    {
        $catpath = '';
        $query = $this->_db->getQuery(true);
        $query->select('cat_dir, cat_dir_parent');
        $query->from('#__jdownloads_categories');
        $query->where('id = '.(int)$parent_id);
        $this->_db->setQuery($query);
        $path = $this->_db->loadObject();
        if ($path->cat_dir_parent != ''){
            $catpath = $path->cat_dir_parent.'/'.$path->cat_dir;
        } else {
            $catpath = $path->cat_dir;            
        }
        return $catpath;
    }
    
    /**
     * Overloaded bind function.
     *
     * @param   array   $array   named array
     * @param   string  $ignore  An optional array or space separated list of properties
     * to ignore while binding.
     *
     * @return  mixed   Null if operation was satisfactory, otherwise returns an error
     *
     * @see     JTable::bind
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params']))
        {
            $registry = new JRegistry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $rules = new JAccessRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }
    
    
    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return    string
     * @since    1.6
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jdownloads.category.'.(int) $this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @return    string
     * @since    1.6
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }

    /**
     * Get the parent asset id for the current category
     * @param   JTable   $table  A JTable object for the asset parent.
     * @param   integer  $id     Id to look up
     * 
     * @return  int      The parent asset id for the category
     */
    protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
    {
        $assetParent = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
        
        // Find the parent-asset
        if ($this->parent_id > 1){
            // The item has a parent category
            $assetParent->loadByName('com_jdownloads.category.' . (int) $this->parent_id);
        } else {
            // The item has the component as asset-parent
            $assetParent->loadByName('com_jdownloads');
        }

        // Return the found asset-id's
        if ($assetParent->id){
            return $assetParent->id;        
        } else {
            return 0;
        }    
    }
    
    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return  boolean  True on success.
     *
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('published = ' . (int) $state);

        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time'))
        {
            $query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
            $checkin = true;
        }
        else
        {
            $checkin = false;
        }

        // Build the WHERE clause for the primary keys.
        $query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->_db->setQuery($query);

        // Check for a database error.
        if (!$this->_db->execute())
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);

            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks))
        {
            $this->published = $state;
        }

        $this->setError('');
        return true;
    }

}
?>