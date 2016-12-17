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

require_once JPATH_SITE.'/components/com_jdownloads/helpers/query.php';
jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of downloads.
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 */
class jdownloadsModelMyDownloads extends JModelList
{

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
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'cat_id', 'a.cat_id', 'category_title',
                'author', 'a.author',
                'featured', 'a.featured',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'date_added', 'a.date_added',
				'created_id', 'a.created_id',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'downloads', 'a.downloads',
				'publish_from', 'a.publish_from',
				'publish_to', 'a.publish_to',
				'images', 'a.images',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
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
        $user        = JFactory::getUser();
        
        $listOrderNew = false;
                
        // Create a new query object.
        $db        = $this->getDbo();
        $query    = $db->getQuery(true);
        $groups    = implode(',', $user->getAuthorisedViewLevels());
        $menu_params = $this->state->params;

        if ((!$user->authorise('core.edit.state', 'com_jdownloads')) &&  (!$user->authorise('core.edit.own', 'com_jdownloads'))){
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
        $orderCol = $app->getUserStateFromRequest('com_jdownloads.downloads.filter_order', 'filter_order', '', 'string');
        
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

        $listOrder = $app->getUserStateFromRequest('com_jdownloads.downloads.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
            $listOrder = 'ASC';
        }
        if (!$listOrderNew){
            $this->setState('list.direction', $listOrder);
        } else {
            $this->setState('list.direction', $listOrderNew);
        }    

        $this->setState('list.start', JRequest::getUInt('limitstart', 0));

        $limit= $app->input->get('limit', false, 'uint');
        
        if ($limit === false){
            if ((int)$menu_params->get('display_num') > 0) {
                $limit = (int)$menu_params->get('display_num');
            } else {
                $limit = (int)$jlistConfig['files.per.side'];
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
		$id .= ':' . serialize($this->getState('filter.published'));
		$id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.featured');        
		$id	.= ':' . serialize($this->getState('filter.category_id'));
		$id .= ':' . $this->getState('filter.category_id.include');
		$id	.= ':' . serialize($this->getState('filter.author_id'));
		$id .= ':' . $this->getState('filter.author_id.include');
		$id	.= ':' . serialize($this->getState('filter.author_alias'));
		$id .= ':' . $this->getState('filter.author_alias.include');
		$id .= ':' . $this->getState('filter.date_filtering');
		$id .= ':' . $this->getState('filter.date_field');
		$id .= ':' . $this->getState('filter.start_date_range');
		$id .= ':' . $this->getState('filter.end_date_range');
		$id .= ':' . $this->getState('filter.relative_date');

		return parent::getStoreId($id);
	}

	/**
	 * Get the master query for retrieving a list of downloads subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 */
	function getListQuery()
	{
		global $jlistConfig; 
        
        // Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
        $user    = JFactory::getUser();
        $groups  = implode (',', $user->getAuthorisedViewLevels());
        
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.file_id, a.file_title, a.file_alias, a.description, a.description_long, a.file_pic, a.images, a.price, a.release, a.file_language, a.system, '.
                'a.license, a.url_license, a.license_agree, a.size, a.date_added, a.file_date, a.publish_from, a.publish_to, a.use_timeframe, a.url_download, a.preview_filename, '.
                'a.other_file_id, a.md5_value, a.sha1_value, a.extern_file, a.extern_site, a.mirror_1, a.mirror_2, a.extern_site_mirror_1, a.extern_site_mirror_2, '.
                'a.url_home, a.author, a.url_author, a.created_id, a.created_mail, a.modified_id, a.modified_date, a.submitted_by, a.set_aup_points, a.downloads, '.
                'a.cat_id, a.changelog, a.password, a.password_md5, a.views, a.metakey, a.metadesc, a.robots, a.update_active, a.custom_field_1, '.
                'a.custom_field_2, a.custom_field_3, a.custom_field_4, a.custom_field_5, a.custom_field_6, a.custom_field_7, a.custom_field_8, a.custom_field_9, '.
                'a.custom_field_10, a.custom_field_11, a.custom_field_12, a.custom_field_13, a.custom_field_14, a.access, a.language, a.ordering, a.featured, '.                
                'a.published, a.checked_out, a.checked_out_time, ' .
				// use date_added if modified_date is 0
                // 'CASE WHEN a.modified_date = 0 THEN a.date_added ELSE a.modified_date END as modified, ' .
                'a.modified_date as modified, ' .
   				'a.modified_id,' .
				// use date_added if publish_from is 0
				'CASE WHEN a.publish_from = 0 THEN a.date_added ELSE a.publish_from END as publish_from,' .
					'a.publish_to, a.images, a.metakey, a.metadesc, a.access, ' .
					'a.downloads,'.' '.$query->length('a.description_long').' AS readmore'
			)
		);

		// Process an Archived Download layout
		if ($this->getState('filter.published') == 2) {
			// If badcats is not null, this means that the download is inside an archived category
			// In this case, the state is set to 2 to indicate Archived (even if the download state is Published)
			$query->select($this->getState('list.select', 'CASE WHEN badcats.id is null THEN a.published ELSE 2 END AS state'));
		}
		else {
			// Process non-archived layout
			// If badcats is not null, this means that the download is inside an unpublished category
			// In this case, the state is set to 0 to indicate Unpublished (even if the download state is Published)
			$query->select($this->getState('list.select', 'CASE WHEN badcats.id is not null THEN 0 ELSE a.published END AS state'));
		}

		$query->from('#__jdownloads_files AS a');
        
        // Join on files table.
        $query->select('aa.url_download AS filename_from_other_download');
        $query->join('LEFT', '#__jdownloads_files AS aa on aa.file_id = a.other_file_id');        

		// Join over the categories.
		$query->select('c.title AS category_title, c.access AS category_access, c.alias AS category_alias, c.cat_dir AS category_cat_dir, c.cat_dir_parent AS category_cat_dir_parent');
		$query->join('LEFT', '#__jdownloads_categories AS c ON c.id = a.cat_id');
        
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_id');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_id');

        // Join on user table.
        if ($jlistConfig['use.real.user.name.in.frontend']){
            $query->select('u.name AS creator');
        } else {
            $query->select('u.username AS creator');
        }    
        $query->join('LEFT', '#__users AS u on u.id = a.created_id');
        
        if ($jlistConfig['use.real.user.name.in.frontend']){
            $query->select('u2.name AS modifier');
        } else {
            $query->select('u2.username AS modifier');
        } 
        $query->select('u2.name AS modifier');
        $query->join('LEFT', '#__users AS u2 on u2.id = a.modified_id');        
        
        // Join on license table.
        $query->select('l.title AS license_title, l.url AS license_url, l.description AS license_text, l.id as lid');
        $query->join('LEFT', '#__jdownloads_licenses AS l on l.id = a.license');
        
        // Join on ratings table.
        $query->select('ROUND(r.rating_sum / r.rating_count, 0) AS rating, r.rating_count as rating_count, r.rating_sum as rating_sum');
        $query->join('LEFT', '#__jdownloads_ratings AS r on r.file_id = a.file_id'); 
        
		// Join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.alias as parent_alias');
		$query->join('LEFT', '#__jdownloads_categories as parent ON parent.id = c.parent_id');
        
        // Join on menu table. We need the single download menu itemid when exist                                                                                                  
        $query->select('menuf.id AS menuf_itemid');
        $query->join('LEFT', '(SELECT id, link, access, published from #__menu GROUP BY link) AS menuf on menuf.link LIKE CONCAT(\'index.php?option=com_jdownloads&view=download&id=\',a.file_id) AND menuf.published = 1 AND menuf.access IN ('.$groups.')') ;

		// Join to check for category published state in parent categories up the tree
		$query->select('c.published, CASE WHEN badcats.id is null THEN c.published ELSE 0 END AS parents_published');
		$subquery = 'SELECT cat.id as id FROM #__jdownloads_categories AS cat JOIN #__jdownloads_categories AS parent ';
		$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
		// Find any up-path categories that are not published
		// If all categories are published, badcats.id will be null, and we just use the download state
		$subquery .= ' AND parent.published != 1 GROUP BY cat.id ';
		// Select state to unpublished if up-path category is unpublished
		$publishedWhere = 'CASE WHEN badcats.id is null THEN a.published ELSE 0 END';
        
		$query->join('LEFT OUTER', '(' . $subquery . ') AS badcats ON badcats.id = c.id');

		// Filter by user id
        $query->where('a.created_id = '.$db->Quote($user->id)); 
        
        // Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access IN ('.$groups.')');
			$query->where('c.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published)) {
			// Use download state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' = ' . (int) $published);
		}
		elseif (is_array($published)) {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			// Use download state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' IN ('.$published.')');
		}
        
        // Filter by a single category
        $categoryId = $this->getState('filter.category_id');

        if (is_numeric($categoryId)) {
            $type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

            $categoryEquals = 'a.cat_id '.$type.(int) $categoryId;
            $query->where($categoryEquals);
        } else { 
            if (is_array($categoryId) && (count($categoryId) > 0)) {
                JArrayHelper::toInteger($categoryId);
                $categoryId = implode(',', $categoryId);
                if (!empty($categoryId)) {
                    $type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
                    $query->where('a.cat_id '.$type.' ('.$categoryId.')');
                }
            }    
        }        

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		$authorWhere = '';

		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
			$authorWhere = 'a.created_id '.$type.(int) $authorId;
		}
		elseif (is_array($authorId)) {
			JArrayHelper::toInteger($authorId);
			$authorId = implode(',', $authorId);

			if ($authorId) {
				$type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
				$authorWhere = 'a.created_id '.$type.' ('.$authorId.')';
			}
		}
       
		if (!empty($authorWhere)) {
			$query->where('('.$authorWhere.')');
		}
		
		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toSql()); // True to return the date string in the local time zone, false to return it in GMT.

		$query->where('(a.publish_from = '.$nullDate.' OR a.publish_from <= '.$nowDate.')');
		$query->where('(a.publish_to = '.$nullDate.' OR a.publish_to >= '.$nowDate.')');

		// Filter by Date Range or Relative Date
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField = $this->getState('filter.date_field', 'a.date_added');

		switch ($dateFiltering)
		{
			case 'range':
				$startDateRange = $db->Quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->Quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField .
					' <= '.$endDateRange.')');
				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' .
					$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		// process the filter for list views with user-entered filters
		$params = $this->getState('params');

		if ((is_object($params)) && ($params->get('filter_field') != 'hide') && ($filter = $this->getState('list.filter'))) {
			// clean filter variable
			$filter = JString::strtolower($filter);
			$hitsFilter = intval($filter);
			$filter = $db->Quote('%'.$db->escape($filter, true).'%', false);

			switch ($params->get('filter_field'))
			{
				case 'author':
					$query->where(
						'LOWER(ua.name) LIKE '.$filter.' '
					);
					break;

				case 'hits':
					$query->where('a.downloads >= '.$hitsFilter.' ');
					break;

				case 'title':
				default: // default to 'title' if parameter is not valid
					$query->where('LOWER( a.file_title ) LIKE '.$filter);
					break;
			}
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
        
        // Filter by uncategorised (cat_id = 1 for 'root')
        if ($this->getState('only_uncategorised')) {
            $query->where('a.cat_id = 1');
        }

        // Filter by featured state
        $featured = $this->getState('filter.featured');

        switch ($featured)
        {
            case 'hide':
                $query->where('a.featured = 0');
                break;

            case 'only':
                $query->where('a.featured = 1');
                break;

            case 'show':
            default:
                break;
        }        
        
		// Add the list ordering clause.
        $order = $this->getState('list.ordering', 'a.ordering').' '.$this->getState('list.direction', 'ASC');
        $order = str_replace('DESC   DESC','DESC', $order);
        $query->order($order);
		
		return $query;
	}

	/**
	 * Method to get a list of downloads.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_jdownloads', true);

		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			$downloadParams = new JRegistry;
			if (isset($item->attribs)){
                $downloadParams->loadString($item->attribs);
            }    

			$item->layout = $downloadParams->get('layout');

			$item->params = clone $this->getState('params');

			// Compute the asset access permissions.

            $userId   = $user->get('id');
            $asset    = 'com_jdownloads.download.'.$item->file_id;

            // Check at first the 'download' permission.
            if ($user->authorise('download', $asset)) {
                $item->params->set('access-download', true);
            }                

            // Technically guest could edit a download, but lets not check that to improve performance a little.
            if (!$guest) {
                
				// Check general edit permission first.
				if ($user->authorise('core.edit', $asset)) {
					$item->params->set('access-edit', true);
				}
				// Now check if edit.own is available.
				elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_id) {
						$item->params->set('access-edit', true);
					}
				}
                
                // Check general delete permission
                if ($user->authorise('core.delete', $asset)) {
                    $item->params->set('access-delete', true);
                }                
			}

			$access = $this->getState('filter.access');

			if ($access) {
				// If the access filter has been set, we already have only the downloads this user can view.
				$item->params->set('access-view', true);
			}
			else {
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null) {
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else {
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
            
            // Get the tags
            $item->tags = new JHelperTags;
            $item->tags->getItemTags('com_jdownloads.download', $item->file_id);            
		}

		return $items;
	}

	public function getStart()
	{
		return $this->getState('list.start');
	}
}
