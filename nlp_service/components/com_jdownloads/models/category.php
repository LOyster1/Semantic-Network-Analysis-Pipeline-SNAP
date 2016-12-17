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

defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_SITE.'/components/com_jdownloads/helpers/categories.php';
require_once JPATH_SITE.'/components/com_jdownloads/helpers/query.php';
jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving a category, the articles associated with the category,
 * sibling, child and parent categories.
 */
class JdownloadsModelCategory extends JModelList
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $_item = null;
	protected $_downloads = null;
	protected $_siblings = null;
	protected $_children = null;
	protected $_parent = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_jdownloads.category';

	/**
	 * The category that applies.
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $_category = null;

	/**
	 * The list of other newfeed categories.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_categories = null;

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'file_id', 'a.file_id',
				'file_title', 'a.file_title',
				'file_alias', 'a.file_alias',
                'description, a.description',
                'description_long', 'a.description_long',
                'file_pic', 'a.file_pic',
                'images', 'a.images',
                'price', 'a.price',
                'release', 'a.release',
                'file_language', 'a.file_language',
                'system', 'a.system',
                'license', 'a.license',
                'url_license', 'a.url_license',
                'size', 'a.size',
                'date_added', 'a.date_added',
                'file_date', 'a.file_date',
                'publish_from', 'a.publish_from',
                'publish_to', 'a.publish_to',                
                'use_timeframe', 'a.use_timeframe',
                'url_download', 'a.url_download',
                'preview_filename', 'a.preview_filename',
                'other_file_id', 'a.other_file_id',
                'md5_value', 'a.md5_value',
                'sha1_value', 'a.sha1_value',
                'extern_file', 'a.extern_file', 
                'extern_site', 'a.extern_site', 
                'mirror_1', 'a.mirror_1', 
                'mirror_2', 'a.mirror_2', 
                'extern_site_mirror-1', 'a.extern_site_mirror_1',
                'extern_site_mirror_2', 'a.extern_site_mirror_2',
                'url_home', 'a.url_home',
                'author', 'a.author',
                'url_author', 'a.url_author',
                'created_id', 'a.created_id',
                'created_mail', 'a.created_mail',
                'modified_id', 'a.modified_id',
                'modified_date', 'a.modified_date',
                'submitted_by', 'a.submitted_by',
                'set_aup_points', 'a.set_aup_points',
                'downloads', 'a.downloads',
                'cat_id', 'a.cat_id', 'category_title',
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
                'featured', 'a.featured',                                
                'published', 'a.published',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 * return	void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		global $jlistConfig;
        
        // Initiliase variables.
		$app	= JFactory::getApplication('site');
        $jinput = JFactory::getApplication()->input;
		$pk		= JRequest::getInt('catid');
        
		$this->setState('category.id', $pk);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$menuParams = new JRegistry;

		if ($menu = $app->getMenu()->getActive()) {
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);
		$user		= JFactory::getUser();
				
        // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$groups	= implode(',', $user->getAuthorisedViewLevels());
        $menu_params = $this->state->params;

		if ((!$user->authorise('core.edit.state', 'com_jdownloads')) &&  (!$user->authorise('core.edit', 'com_jdownloads'))){
			// limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);
			// Filter by start and end dates.
			$nullDate = $db->Quote($db->getNullDate());
			$nowDate = $db->Quote(JFactory::getDate()->toSql()); // True to return the date string in the local time zone, false to return it in GMT.

			$query->where('(a.publish_from = ' . $nullDate . ' OR a.publish_from <= ' . $nowDate . ')');
			$query->where('(a.publish_to = ' . $nullDate . ' OR a.publish_to >= ' . $nowDate . ')');
		}
		else {
			$this->setState('filter.published', array(0, 1, 2));
		}

		// process show_noauth parameter
		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		// Optional filter text
		$this->setState('list.filter', JRequest::getString('filter-search'));

		// filter.order
		$itemid = JRequest::getInt('catid', 0) . ':' . JRequest::getInt('Itemid', 0);
		$orderCol = $app->getUserStateFromRequest('com_jdownloads.category.' . $itemid . '.filter_order', 'filter_order', '', 'string');
        
        $listOrderNew = '';
        
        if (!in_array($orderCol, $this->filter_fields) || $orderCol == '') {
			// use default sort order or menu order settings
            if ($menu_params->get('orderby_sec') == ''){
                // use config settings
                switch ($jlistConfig['files.order']){
                    case '0':
                         // files ordering field
                         $orderCol = 'a.ordering';
                         $listOrderNew = 'ASC';
                         break;
                    case '1':
                         // files date_added desc 
                         $orderCol = 'a.date_added'; // desc
                         $listOrderNew = 'DESC';
                         break;
                    case '2':
                         // files date_added asc 
                         $orderCol = 'a.date_added'; // asc
                         $listOrderNew = 'ASC';
                         break;
                    case '3':
                         // files title field asc 
                         $orderCol = 'a.file_title';
                         $listOrderNew = 'ASC';
                         break;
                    case '4':
                         // files title field desc 
                         $orderCol = 'a.file_title';
                         $listOrderNew = 'DESC';
                         break;
                    case '5':
                         // files hits/downloads field desc
                         $orderCol = 'a.downloads';
                         $listOrderNew = 'DESC';
                         break;
                    case '6':
                         // files hits/downloads field asc
                         $orderCol = 'a.downloads';
                         $listOrderNew = 'ASC';
                         break;                         
                    case '7':
                         // author title field asc 
                         $orderCol = 'a.author';
                         $listOrderNew = 'ASC';
                         break;
                    case '8':
                         // author title field desc 
                         $orderCol = 'a.author';
                         $listOrderNew = 'DESC';
                         break;                         
                }
		    }  else {
                // use order from menu settings 
                $filesOrderby = $params->get('orderby_sec', 'order');
                $orderCol    = JDContentHelperQuery::orderbySecondary($filesOrderby) . ' ';
                $order_array  = explode(' ', $orderCol);
                if (count($order_array > 2)){
                    $orderCol       = $order_array[0];
                    $listOrderNew   = $order_array[1];
                }
            }    
        }
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_jdownloads.category.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		if (!$listOrderNew){
            $this->setState('list.direction', $listOrder);
        } else {
            $this->setState('list.direction', $listOrderNew);
        }    

		$this->setState('list.start', JRequest::getUInt('limitstart', 0));

        $limit = $app->getUserStateFromRequest('com_jdownloads.category.' . $itemid . '.limit', 'limit',  '', 'uint');
		if (!$limit){
            if ((int)$menu_params->get('display_num') > 0) {
                $limit = (int)$menu_params->get('display_num');
            } else {
                $limit = (int)$jlistConfig['files.per.side'];
            }
        }
        
        $this->setState('list.limit', $limit);

		$this->setState('filter.language', $app->getLanguageFilter());

		$this->setState('layout', $jinput->get('layout'));
        
        // Set the featured Downloads state
        $this->setState('filter.featured', $params->get('show_featured'));        

	}

	/**
	 * Get the downloads in the category
	 *
	 * @return	mixed	An array of downloads or false if an error occurs.
	 */
	function getItems()
	{
		$params = $this->getState()->get('params');
		$limit = $this->getState('list.limit');

		if ($this->_downloads === null && $category = $this->getCategory()) {
			
            $model = JModelLegacy::getInstance('downloads', 'JdownloadsModel', array('ignore_request' => true));
			
            $model->setState('params', JFactory::getApplication()->getParams());
			$model->setState('filter.category_id', $category->id);
			$model->setState('filter.published', $this->getState('filter.published'));
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('filter.language', $this->getState('filter.language'));
            $model->setState('list.ordering', $this->_buildContentOrderBy());  // $this->getState('list.ordering'));
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.limit', $limit);
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.filter', $this->getState('list.filter'));
			$model->setState('list.links', $this->getState('list.links'));

            if ($limit >= 0) {
				$this->_downloads = $model->getItems();

				if ($this->_downloads === false) {
					$this->setError($model->getError());
				}
			} else {
				$this->_downloads=array();
			}
            
            $this->_pagination = $model->getPagination();
		}

		return $this->_downloads;
	}

	/**
	 * Build the orderby for the query
	 *
	 * @return	string	$orderby portion of query
	 * @since	1.5
	 */
	protected function _buildContentOrderBy()
	{
        global $jlistConfig;
        $app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;
		$itemid		= JRequest::getInt('catid', 0) . ':' . JRequest::getInt('Itemid', 0);
		$orderCol	= $app->getUserStateFromRequest('com_jdownloads.category.' . $itemid . '.filter_order', 'filter_order', '', 'string');
        if ($orderCol == ''){
            $orderCol = $this->state->get('list.ordering');
        }
		$orderDirn	= $app->getUserStateFromRequest('com_jdownloads.category.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        if ($orderDirn == ''){
            $orderDirn = $this->state->get('list.direction');
        }
        
        $orderby	= ' ';

		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = null;
		}

		if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC', ''))) {
			$orderDirn = '';
		}

		/* if ($orderCol && $orderDirn) {
			$orderby .= $db->escape($orderCol) . ' ' . $db->escape($orderDirn) . ' ';
		} else {
            $orderby .= $db->escape($orderCol) .' ';
            
        } */
       
		$filesOrderby		= $params->get('orderby_sec', '');
        // we have uses in the jD configuration the old numerical values (to be compatible) so we must correct it here at first
        $config_cats_order  = JDHelper::getCorrectedOrderbyValues('primary', $jlistConfig['cats.order']);
		$categoryOrderby	= $params->def('orderby_pri', $config_cats_order);

        if ($filesOrderby){
		    $secondary			= JDContentHelperQuery::orderbySecondary($filesOrderby) . ' ';
        } else {
            $secondary          = JDContentHelperQuery::orderbySecondary($orderCol) . ' ';
        }    
	
        $primary			= JDContentHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby .= $db->escape($primary) . ' ' . $db->escape($secondary) . ' '; // a.created ';
        
		return $orderby;
	}

	public function getPagination()
	{
		if (empty($this->_pagination)) {
			return null;
		}
		return $this->_pagination;
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param	int		An optional ID
	 *
	 * @return	object
	 * @since	1.5
	 */
	public function getCategory()
	{
		global $jlistConfig;
        $options = '';
        
        if (!is_object($this->_item)) {

			$categories = JDCategories::getInstance('jdownloads', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions.
			if (is_object($this->_item)) {
				$user	= JFactory::getUser();
				$userId	= $user->get('id');
				$asset	= 'com_jdownloads.category.'.$this->_item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset)) {
					$this->_item->getParams()->set('access-create', true);
				}

				// TODO: Why aren't we lazy loading the children and siblings?
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;

				if ($this->_item->getParent()) {
					$this->_parent = $this->_item->getParent();
				}

				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else {
				$this->_children = false;
				$this->_parent = false;
			}
            
            if (count($this->_children)){
                for ($i = 0; $i < count($this->_children); $i++) { 
                    // Get the tags
                    $this->_children[$i]->tags = new JHelperTags;
                    $this->_children[$i]->tags->getItemTags('com_jdownloads.category',  $this->_children[$i]->id);
                }
            }
		}

		return $this->_item;
	}

	/**
	 * Get the parent category.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	public function getParent()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the left sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getLeftSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getRightSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getChildren()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		// Order subcategories
		if (sizeof($this->_children)) {
			$params = $this->getState()->get('params');
			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha') {
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->_children;
	}
}
