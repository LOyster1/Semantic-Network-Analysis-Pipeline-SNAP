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

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_jdownloads/models/download.php';

/**
 * jDownloads Component Download Model
 *
 */
class jdownloadsModelForm extends jdownloadsModeldownload
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
        $jinput = JFactory::getApplication()->input;

		// Load state from the request.
		$pk = $app->input->get('a_id', 0, 'int');
		$this->setState('download.id', $pk);

		$this->setState('download.catid', $app->input->get('catid', 0, 'int'));

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', urldecode(base64_decode($return)));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $jinput->get('layout'));
	}

	/**
	 * Method to get download data.
	 *
	 * @param	integer	The id of the download.
	 *
	 * @return	mixed	Content item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('download.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// Convert attrib field to Registry.
		$value->params = new JRegistry;
		//$value->params->loadString($value->attribs);

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_jdownloads.download.'.$value->file_id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_id) {
				$value->params->set('access-edit', true);
			}
		}
        
        // Check general delete permission
        if ($user->authorise('core.delete', $asset)) {
            $value->params->set('access-delete', true);
        }                
   
		// Check edit state permission.
		if ($itemId) {
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else {
			// New item.
			$catId = (int) $this->getState('download.catid');

			if ($catId) {
				$value->params->set('access-create', $user->authorise('core.create', 'com_jdownloads.category.'.$catId));
                $value->params->set('access-change', $user->authorise('core.edit.state', 'com_jdownloads.category.'.$catId));
				$value->catid = $catId;
			} else {
				$value->params->set('access-create', $user->authorise('core.create', 'com_jdownloads'));
                $value->params->set('access-change', $user->authorise('core.edit.state', 'com_jdownloads'));
			}
		}
        
        if ($itemId){
            $value->tags = new JHelperTags;
            $value->tags->getTagIds($value->file_id, 'com_jdownloads.download');
            //$value->metadata['tags'] = $value->tags;
        }        

		return $value;
	}

	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 */
	public function getReturnPage()
	{
		return base64_encode(urlencode($this->getState('return_page')));
	}
}
