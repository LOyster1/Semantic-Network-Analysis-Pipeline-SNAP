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

require_once JPATH_SITE.'/components/com_jdownloads/helpers/categories.php';
require_once JPATH_SITE.'/components/com_jdownloads/helpers/query.php';
jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of download categories.
 *
 */
class JdownloadsModelCategories extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jdownloads.categories';

	private $_parent = null;

	private $_items = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		
        global $jlistConfig;
        
        $app = JFactory::getApplication();
        $jinput = JFactory::getApplication()->input;

        // Load the parameters. Merge Global and Menu Item params into new object
        $params = $app->getParams();
        $menuParams = new JRegistry;

        if ($menu = $app->getMenu()->getActive()) {
            $menuParams->loadString($menu->params);
        }

        $mergedParams = clone $menuParams;
        $mergedParams->merge($params);

        $this->setState('params', $mergedParams);
        $user = JFactory::getUser();
                
        // Create a new query object.
        $db           = $this->getDbo();
        $query        = $db->getQuery(true);
        $groups       = implode(',', $user->getAuthorisedViewLevels());
        $menu_params  = $this->state->params;
        $listOrderNew = '';

        $this->setState('filter.published', 1);
        $this->setState('filter.access', true);

        // filter.order
        $orderCol = $app->getUserStateFromRequest('com_jdownloads.categories.filter_order', 'filter_order', '', 'string');
        
        if ($orderCol == '') {
            // use default sort order or menu order settings
            if ($menu_params->get('orderby_pri') == ''){
                // use config settings
                switch ($jlistConfig['cats.order']){
                    case '1':
                         // files title field asc 
                         $orderCol = 'c.title';
                         $listOrderNew = 'ASC';
                         break;
                    case '2':
                         // files title field desc 
                         $orderCol = 'c.title';
                         $listOrderNew = 'DESC';
                         break;
                    default:
                         // files ordering field
                         $orderCol = 'c.ordering';
                         $listOrderNew = 'ASC';
                         break;                }
            }  else {
                // use order from menu settings 
                $categoryOrderby    = $params->def('orderby_pri', $jlistConfig['cats.order']);
                $orderCol           = str_replace(', ', '', JDContentHelperQuery::orderbyPrimary($categoryOrderby));
            }    
        }
        $this->setState('list.ordering', $orderCol);

        $listOrder = $app->getUserStateFromRequest('com_jdownloads.categories.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
            $listOrder = 'ASC';
        }
        if (!$listOrderNew){
            $this->setState('list.direction', $listOrder);
        } else {
            $this->setState('list.direction', $listOrderNew);
        }    

        $this->setState('list.start', JRequest::getUInt('limitstart', 0));

        $limit = $app->getUserStateFromRequest('com_jdownloads.categories.limit', 'limit',  '', 'uint');
        if (!$limit){
            if ((int)$menu_params->get('display_num') > 0) {
                $limit = (int)$menu_params->get('display_num');
            } else {
                $limit = (int)$jlistConfig['categories.per.side'];
            }
        }
        
        $this->setState('list.limit', $limit);
        $this->setState('filter.language', $app->getLanguageFilter());
        $this->setState('layout', $jinput->get('layout'));        
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
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');
        $id .= ':'.$this->getState('filter.category_id');
        $id .= ':'.$this->getState('filter.level');
        
		return parent::getStoreId($id);
	}

	/**
	 * Redefine the function an add some properties to make the styling more easy
	 *
	 * @param	bool	$recursive	True if you want to return children recursively.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 */
	public function getItems($recursive = false)
	{
		if (!count($this->_items)) {
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();

			if ($active) {
				$params->loadString($active->params);
			}

			$options = array();
			$options['countItems'] = 1;
            $option =  $this->getState('list.ordering');
            if ($option == 'c.ordering'){
                $options['ordering'] = 'c.lft';
            } else {
                $options['ordering'] = $this->getState('list.ordering');
            }    
			$options['direction'] = $this->getState('list.direction');
            $options['category_id'] = $this->getState('filter.category_id');
            $options['level'] = $this->getState('filter.level', 0);
            
			$categories = JDCategories::getInstance('jdownloads', $options);
            
            $this->_parent = $categories->get($this->getState('filter.parentId', 'root'));

            if (is_object($this->_parent)) {
				$this->_items = $this->_parent->getChildren($recursive);
			}
			else {
				$this->_items = false;
			}
		}
        
		return $this->_items;
	}

	public function getParent()
	{
		if (!is_object($this->_parent)) {
			$this->getItems();
		}

		return $this->_parent;
	}
    
    /**
   * Method to get the total number of categories
   *
   * @return  int     The total number of categories
   */
    public function getTotal()
    {
        // Let's load the categories if they doesn't already exist
        if(empty($this->_total)){
          //$query        = $this->_buildQuery();
          $this->_total = count($this->_items);
        }

        return $this->_total;
  }    
}
