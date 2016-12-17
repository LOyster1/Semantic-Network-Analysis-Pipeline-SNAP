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
jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.modeladmin');

/**
 * jDownloads Component Download Model
 *
 */
class jdownloadsModelDownload extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_jdownloads.download';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');
        $jinput = JFactory::getApplication()->input;
        
		// Load state from the request.
		$pk = $jinput->get('id');
		$this->setState('download.id', $pk);

		$offset = $jinput->get('limitstart', 0, 'uint');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_jdownloads')) &&  (!$user->authorise('core.edit', 'com_jdownloads')) && (!$user->authorise('core.edit.own', 'com_jdownloads'))){
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	/**
	 * Method to get a download data.
	 *
	 * @param	integer	The id of the download.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($pk = null, $plugin = false)
	{
		global $jlistConfig;
        
        // Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('download.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select($this->getState(
					'item.select', 'a.file_id, a.asset_id, a.file_title, a.file_alias, a.description, a.description_long, a.file_pic, a.images, a.price, a.release, ' .
					'a.file_language, a.system, a.license, a.url_license, a.license_agree, a.size, a.url_download, a.preview_filename, a.other_file_id, a.md5_value, a.sha1_value, ' .
                    'a.extern_file, a.extern_site, a.mirror_1, a.mirror_2, a.extern_site_mirror_1, a.extern_site_mirror_2, a.url_home, a.author, a.url_author, a.created_mail, a.submitted_by, ' .
                    'a.changelog, a.password, a.password_md5, a.views, a.update_active, a.custom_field_1, a.custom_field_2, a.custom_field_3, a.custom_field_4, a.custom_field_5, a.custom_field_6, ' .
                    'a.custom_field_7, a.custom_field_8, a.custom_field_9, a.custom_field_10, a.custom_field_11, a.custom_field_12, a.custom_field_13, a.custom_field_14, a.featured, a.published, ' .
                    // If badcats is not null, this means that the download is inside an unpublished category
					// In this case, the state is set to 0 to indicate Unpublished (even if the download state is Published)
					'CASE WHEN badcats.id is null THEN a.published ELSE 0 END AS state, ' .
					'a.cat_id, a.date_added, a.created_id, a.file_date, ' .
				    // use created if modified is 0
				    // 'CASE WHEN a.modified_date = 0 THEN a.date_added ELSE a.modified_date END as modified, ' .
                    'a.modified_date as modified, ' .					
                    'a.publish_from, a.publish_to, a.modified_id, a.checked_out, a.checked_out_time,  ' .
					'a.ordering, a.metakey, a.metadesc, a.robots, a.access, a.downloads, a.language'
					)
				);
				$query->from('#__jdownloads_files AS a');

                // Join on files table.
                $query->select('aa.url_download AS filename_from_other_download');
                $query->join('LEFT', '#__jdownloads_files AS aa on aa.file_id = a.other_file_id');
				
                // Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access, c.cat_dir AS category_cat_dir, c.cat_dir_parent AS category_cat_dir_parent');
				$query->join('LEFT', '#__jdownloads_categories AS c on c.id = a.cat_id');

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

				// Join on contact table
				$subQuery = $db->getQuery(true);
				$subQuery->select('contact.user_id, MAX(contact.id) AS id, contact.language');
				$subQuery->from('#__contact_details AS contact');
				$subQuery->where('contact.published = 1');
				$subQuery->group('contact.user_id, contact.language');
				$query->select('contact.id as contactid' );
				$query->join('LEFT', '(' . $subQuery . ') AS contact ON contact.user_id = a.created_id');

                // Join on license table.
                $query->select('l.title AS license_title, l.url AS license_url, l.description AS license_text, l.id as lid');
                $query->join('LEFT', '#__jdownloads_licenses AS l on l.id = a.license');
                
                // Join on ratings table.
                $query->select('ROUND(r.rating_sum / r.rating_count, 0) AS rating, r.rating_count as rating_count, r.rating_sum as rating_sum');
                $query->join('LEFT', '#__jdownloads_ratings AS r on r.file_id = a.file_id');

				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where('a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
				}

				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.alias as parent_alias');
				$query->join('LEFT', '#__jdownloads_categories as parent ON parent.id = c.parent_id');

				$query->where('a.file_id = ' . (int) $pk);

				// Filter by start and end dates.
				$nullDate = $db->Quote($db->getNullDate());
				$date = JFactory::getDate(); 

				$nowDate = $db->Quote($date->toSql());

				$query->where('(a.publish_from = ' . $nullDate . ' OR a.publish_from <= ' . $nowDate . ')');
				$query->where('(a.publish_to = ' . $nullDate . ' OR a.publish_to >= ' . $nowDate . ')');

				// Join to check for category published state in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the download state
				$subquery = ' (SELECT cat.id as id FROM #__jdownloads_categories AS cat JOIN #__jdownloads_categories AS parent ';
				$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
				$subquery .= 'WHERE parent.published <= 0 GROUP BY cat.id)';
				$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

				// Filter by published state.
				$published = $this->getState('filter.published');

				if (is_numeric($published)) {
					$query->where('(a.published = ' . (int) $published.')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

                if (!$data && $plugin === true){
                    return $data;
                }
                
				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($data)) {
					return JError::raiseError(404, JText::_('COM_JDOWNLOADS_DOWNLOAD_NOT_FOUND'));
				}

				// Check for published state if filter set.
				if ((is_numeric($published)) && ($data->published != $published)) {
					return JError::raiseError(404, JText::_('COM_JDOWNLOADS_DOWNLOAD_NOT_FOUND'));
				}

				$data->params = clone $this->getState('params');

				// Compute selected asset permissions.
				$user	= JFactory::getUser();

				$userId	= $user->get('id');
				$asset	= 'com_jdownloads.download.'.$data->file_id;

                // Check at first the 'download' permission.
                if ($user->authorise('download', $asset)) {
                    $data->params->set('access-download', true);
                }

                // Technically guest could edit a download, but lets not check that to improve performance a little.
                if (!$user->get('guest')) {
                                        
					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) {
						$data->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_id) {
							$data->params->set('access-edit', true);
						}
					}
                    
                    // Check general delete permission
                    if ($user->authorise('core.delete', $asset)) {
                        $data->params->set('access-delete', true);
                    }                
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($data->cat_id == 0 || $data->category_access === null) {
						$data->params->set('access-view', in_array($data->access, $groups));
					}
					else {
						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Increment the views counter for the download
	 *
	 * @param	int		Optional primary key of the download to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function view($pk = 0)
	{
        $jinput = JFactory::getApplication()->input;    
        $viewcount = $jinput->get('viewcount', 1, 'int');

        if ($viewcount)
        {
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) $this->getState('download.id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__jdownloads_files' .
                    ' SET views = views + 1' .
                    ' WHERE file_id = '.(int) $pk
            );

            if (!$db->execute()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }
        }
        return true;
	}

    public function storeVote($pk = 0, $rate = 0)
    {
        if ( $rate >= 1 && $rate <= 5 && $pk > 0 )
        {
            $userIP = $_SERVER['REMOTE_ADDR'];
            $db = $this->getDbo();

            $db->setQuery(
                    'SELECT *' .
                    ' FROM #__jdownloads_ratings' .
                    ' WHERE file_id = '.(int) $pk
            );

            $rating = $db->loadObject();

            if (!$rating)
            {
                // There are no ratings yet, so lets insert our rating
                $db->setQuery(
                        'INSERT INTO #__jdownloads_ratings ( file_id, lastip, rating_sum, rating_count )' .
                        ' VALUES ( '.(int) $pk.', '.$db->Quote($userIP).', '.(int) $rate.', 1 )'
                );

                if (!$db->execute()) {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
            } else {
                if ($userIP != ($rating->lastip))
                {
                    $db->setQuery(
                            'UPDATE #__jdownloads_ratings' .
                            ' SET rating_count = rating_count + 1, rating_sum = rating_sum + '.(int) $rate.', lastip = '.$db->Quote($userIP) .
                            ' WHERE file_id = '.(int) $pk
                    );
                    if (!$db->execute()) {
                            $this->setError($db->getErrorMsg());
                            return false;
                    }
                } else {
                    return false;
                }
            }
            return true;
        }
        JError::raiseWarning( '100', JText::sprintf('COM_JDOWNLOADS_INVALID_RATING', $rate), "JModelDownload::storeVote($rate)");
        return false;
    }
    
    
}
