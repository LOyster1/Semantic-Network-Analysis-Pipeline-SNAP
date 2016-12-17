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

setlocale(LC_ALL, 'C.UTF-8', 'C');

jimport('joomla.application.component.controllerform');

/**
*       
 */
class jdownloadsControllerDownload extends JControllerForm
{

    protected $items;
    protected $params;
    protected $state;
    protected $user;
    protected $user_rules;    
    
	protected $view_item = 'form';
	protected $view_list = 'categories';
    

	/**
	 * Method to add a new record.
	 *
	 * @return	boolean	True if the download can be added, false if not.
	 */
	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		//$jinput = JFactory::getApplication()->input;
        
        // Initialise variables.
		$user		= JFactory::getUser();
		$categoryId = JArrayHelper::getValue($data, 'cat_id', $this->input->getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_jdownloads.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else {
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$asset		= 'com_jdownloads.download.'.$recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset)) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_id']) ? $data['created_id'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_id;  // original : created_by
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	} 

	/**
	 * Method to cancel an edit.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 *
	 * @return	Boolean	True if access level checks pass, false otherwise.
	 */
	public function cancel($key = 'a_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if access level check and checkout passes, false otherwise.
	 */
	public function edit($key = 'id', $urlVar = 'a_id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 *
	 */
	public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
	{
		$jinput = JFactory::getApplication()->input;
        
        // Need to override the parent method completely.
		$tmpl		= $jinput->get('tmpl');
		$layout		= $jinput->get('layout', 'edit', 'CMD');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= $jinput->get('Itemid');
		$return	= $this->getReturnPage();
		$catId = $jinput->get->get('catid', null, 'int');

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if($catId) {
			$append .= '&catid='.$catId;
		}

		if ($return) {
			$append .= '&return='.base64_encode(urlencode($return));
		}

		return $append;
	}
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     * @return    JTable    A database object
     * @since    1.6
     */
    public function getTable($type = 'download', $prefix = 'jdownloadsTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }    

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 */
	protected function getReturnPage()
	{
		$jinput = JFactory::getApplication()->input;
                  
        $return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(urldecode(base64_decode($return)))) {
			return JUri::base();
		}
		else {
			return urldecode(base64_decode($return));
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param	JModel	$model		The data model object.
	 * @param	array	$validData	The validated data.
	 *
	 * @return	void
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$task = $this->getTask();

		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=category&id='.$validData['cat_id'], false));
		}
	}

	/**
	 * Method to save a record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = 'a_id')
	{
		// Load the backend helper for filtering.
		require_once JPATH_ADMINISTRATOR.'/components/com_jdownloads/helpers/jdownloadshelper.php';

		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result) {
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
    
    /**
     * Method to delete an assigned file from the download.
     *
     *
     * @return    Boolean    True if successful, false otherwise.
     */
    public function deletefile()
    {
        $jinput = JFactory::getApplication()->input;
        
        $type   = $jinput->get('type', '');
        $id     = $jinput->get('id', 0, 'int');        
        
        // load the download data
        $data = $this->getModel()->getItem($id);
        
        // check permissions
        if ($data->params->get('access-edit') == true){

            // Load the backend helper methodes
            require_once JPATH_ADMINISTRATOR.'/components/com_jdownloads/helpers/jdownloadshelper.php';

            if ($type == 'prev'){
                // delete the preview file
                $result = jdownloadshelper::deletePreviewFile($id);
            } else {
                // delete the main file
                $result = jdownloadshelper::deleteFile($id);
            }    
        } else {
            // no permissions - do nothing
            JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        } 
        
        // Add a message to the message queue
        $application = JFactory::getApplication();
        if ($result){
            $application->enqueueMessage( JText::_('COM_JDOWNLOADS_FILE_DELETED_MSG'), 'message'); 
        } else {
            $application->enqueueMessage( JText::_('COM_JDOWNLOADS_FILE_DELETED_MSG_ERROR'), 'error'); 
        }      
        
        // Redirect to the download view page.
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=download&id='.$id, false));

        return $result;
    } 
    
    /**
     * Method to delete an download.
     *
     *
     * @return    Boolean    True if successful, false otherwise.
     */
    public function delete()
    {

        // Load the backend download model
        require_once JPATH_ADMINISTRATOR.'/components/com_jdownloads/models/download.php';

        $jinput = JFactory::getApplication()->input;
        $id     = $jinput->get('a_id', 0, 'int');
        
        // load the download data
        $model_download = JModelAdmin::getInstance( 'Download', 'jdownloadsModel' );
        $data = $this->getModel()->getItem($id);
        $this->option = 'com_jdownloads';
         
        // check permissions
        if ($data->params->get('access-delete') == true){

            if ($id > 0){
                $result = $model_download->delete($id);
            }    
            
        } else {
            // no permissions - do nothing
            JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        } 
        
        // Add a message to the message queue
        $application = JFactory::getApplication();
        if ($result){
            $application->enqueueMessage( JText::_('COM_JDOWNLOADS_DOWNLOAD_DELETED_MSG'), 'message'); 
        } else {
            $application->enqueueMessage( JText::_('COM_JDOWNLOADS_DOWNLOAD_DELETED_MSG_ERROR'), 'error'); 
        }      
        
        // Redirect to the download view page.
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=downloads'));

        return $result;
    }
    
    /**
     * Method to submit the downloads file to the browser.
     *
     *
     * @return    null
     */
    public function send()
    {
        global $jlistConfig;
        
        jimport('joomla.environment.uri');
        $jinput = JFactory::getApplication()->input;
        
        $app     = JFactory::getApplication();
        $params  = $app->getParams();
        $user    = JFactory::getUser();
        $groups  = implode (',', $user->getAuthorisedViewLevels());        

        $db      = JFactory::getDBO();
        
        $config = array('ignore_request' => true);        
        $model = $this->getModel('send', 'jdownloadsModel', $config);
        
        $user_rules = JDHelper::getUserRules();            
        
        clearstatcache();
        
        $active = $app->getMenu()->getActive();
        if ($active) {
            $current_link = $active->link;
        } else {
            $current_link = JRoute::_(JUri::current().'?option=com_jdownloads');
        } 
        
        // abort when downloads are offline 
        if ($jlistConfig['offline']) {
            $msg = JDHelper::getOnlyLanguageSubstring($jlistConfig['offline.text']);
            $app->redirect(JRoute::_($current_link), $msg, 'notice');
        }    

        $allow        = false;
        $extern       = false;
        $extern_site  = false;
        $can_download = false;
        $aup_exist    = false;  
        $profile       = '';
        
        // Which file types shall be viewed in browser 
        $view_types = array();
        $view_types = explode(',', $jlistConfig['file.types.view']); 

        // get request data
        $cat_id     = $db->escape($jinput->get('catid', 0, 'int'));
        $file_id    = $db->escape($jinput->get('id', 0, 'int'));
        $mirror     = $db->escape($jinput->get('m', 0, 'int'));
        $files_list = $db->escape($jinput->get('list', '', 'string'));
        $zip_file   = $db->escape($jinput->get('user', 0, 'cmd'));
        
        // get session data
        $stored_random_id   = (int)JDHelper::getSessionDecoded('jd_random_id');
        $stored_file_id     = (int)JDHelper::getSessionDecoded('jd_fileid');
        $stored_cat_id      = (int)JDHelper::getSessionDecoded('jd_catid');
        $stored_files_list  = JDHelper::getSessionDecoded('jd_list');

        // compare and check it 
        if (($cat_id > 0 && $cat_id != $stored_cat_id) || ($file_id > 0 && $file_id != $stored_file_id) || ($zip_file > 0 && $zip_file != $stored_random_id) || ($files_list != '' && $files_list != $stored_files_list)){
            // perhaps use it a direct download option
            $this->items = $model->getItems($file_id);
            if ($this->items){
                $this->state = $model->getState();
                
                $sum_selected_files   = $this->state->get('sum_selected_files');
                $sum_selected_volume  = $this->state->get('sum_selected_volume');
                $sum_files_prices     = $this->state->get('sum_files_prices');
                $must_confirm_license = $this->state->get('must_confirm_license');
                $directlink           = $this->state->get('directlink_used');
                $marked_files_id      = $this->state->get('download.marked_files.id');
                
                // check the permission access for direct download option
                $within_the_user_limits = JDHelper::checkDirectDownloadLimits($cat_id, $file_id, $files_list, $user_rules, $sum_selected_files, $sum_selected_volume);
                
                if ($within_the_user_limits !== true){
                    // user has his limits reached or not enough points 
                    $msg = JText::_($within_the_user_limits);
                    $app->redirect(JRoute::_($current_link), $msg, 'notice');
                }
            } else {
                // invalid data found / url manipulations?
                $msg = JText::_('COM_JDOWNLOADS_INVALID_DOWNLOAD_DATA_MSG');
                $app->redirect(JRoute::_($current_link), $msg, 'notice');
            }            

        }
        
        // check leeching
        if ($is_leeching = JDHelper::useAntiLeeching()){
            // download stopped - view hint
            $msg = JText::_('COM_JDOWNLOADS_ANTILEECH_MSG').' '.JText::_('COM_JDOWNLOADS_ANTILEECH_MSG2');
            $app->redirect(JRoute::_($current_link), $msg, 'notice');
        }
                
        if ($zip_file){
            // user has selected more as a single file
            $zip_file = $jlistConfig['zipfile.prefix'].$zip_file.'.zip';
            $filename  = $jlistConfig['files.uploaddir'].'/'.$jlistConfig['tempzipfiles.folder.name'].'/'.$zip_file;
            
            if (!file_exists($filename)){
                // download stopped - zip file not found
                $msg = JText::_('COM_JDOWNLOADS_FILE_NOT_FOUND').': '.basename($zip_file);
                $app->redirect(JRoute::_($current_link), $msg, 'notice');
            }
        }

        //  download action check (not for uncategorized)
        if ($cat_id > 1) {
            // If the category has been passed in the data or URL check it.
            $allow = $user->authorise('download', 'com_jdownloads.category.'.$cat_id);
            if ($file_id && $allow){
                // If the category has been passed in the data or URL check it.
                $allow = $user->authorise('download', 'com_jdownloads.download.'.$file_id);
            }            
        } else {
            if ($file_id){
                // If the category has been passed in the data or URL check it.
                $allow = $user->authorise('download', 'com_jdownloads.download.'.$file_id);
            }            
        }
        
        if (!$allow){
            // download stopped - user has not the right to download it
            $msg = JText::_('COM_JDOWNLOADS_DOWNLOAD_NOT_ALLOWED_MSG');
            $app->redirect(JRoute::_($current_link), $msg, 'notice');
        }
        
        $transfer_speed = (int)$user_rules->transfer_speed_limit_kb;
        
        if ($jlistConfig['use.alphauserpoints']){
            // get AUP user info
            $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
            
            if (file_exists($api_AUP) && !$user->guest){
                require_once ($api_AUP);
                $aup_exist = true;
                // get user profile data from AUP
                $profile = AlphaUserPointsHelper::getUserInfo('', $user->id);

                // get standard points value from AUP
                $db->setQuery("SELECT points FROM #__alpha_userpoints_rules WHERE published = 1 AND plugin_function = 'plgaup_jdownloads_user_download'");
                $aup_fix_points = floatval($db->loadResult());
                //$aup_fix_points = JDHelper::strToNumber($aup_fix_points);
            }
        }    

        // build a array with IDs
        $files_arr = explode(',', $files_list);
        
        // get the files data for multi or single download
        $query = $db->getQuery(true);
        $query->select('a.*');
        $query->from('#__jdownloads_files AS a');
        
        // Join on category table.
        $query->select('c.title AS category_title, c.id AS category_id, c.cat_dir AS category_cat_dir, c.cat_dir_parent AS category_cat_dir_parent');
        $query->join('LEFT', '#__jdownloads_categories AS c on c.id = a.cat_id');
        
        // Join on license table.
        $query->select('l.title AS license_title');
        $query->join('LEFT', '#__jdownloads_licenses AS l on l.id = a.license');
        
        $query->where('(a.published = '.$db->Quote('1').')');
        if ($files_list){
            $query->where('a.file_id IN (' .$files_list.')');
        } else {
            $query->where('a.file_id = '.$db->Quote($file_id));
        }    

        // Filter by access level so when we get not a result this user has not the access to view it
        $query->where('a.access IN ('.$groups.')');
        $query->where('c.access IN ('.$groups.')');
        
        $db->setQuery($query);
        $files = $db->loadObjectList();

        if (!$files){
            // invalid data or user has not really the access 
            $msg = JText::_('COM_JDOWNLOADS_DATA_NOT_FOUND');
            $app->redirect(JRoute::_($current_link), $msg, 'error');
        }            

        // When we have a regged user, we must check whether he downloads the file in parts.
        // If so, we may only once write the download data in log and compute the AUP etc.
        $download_in_parts = JDHelper::getLastDownloadActivity($user->id, $files_list, $file_id, $user_rules->download_limit_after_this_time);
            
        if (count($files) > 1){
            // mass download

            if (!$download_in_parts){            
                // add AUP points
                if ($jlistConfig['use.alphauserpoints'] && $aup_exist){
                    if ($jlistConfig['use.alphauserpoints.with.price.field']){
                        $db->setQuery("SELECT SUM(price) FROM #__jdownloads_files WHERE file_id IN ($files_list)");
                        $sum_points = (int)$db->loadResult();
                        if ($profile->points >= $sum_points){
                            foreach($files as $aup_data){
                                $db->setQuery("SELECT price FROM #__jdownloads_files WHERE file_id = '$aup_data->file_id'");
                                if ($price = floatval($db->loadResult())){
                                    $can_download = JDHelper::setAUPPointsDownloads($user->id, $aup_data->file_title, $aup_data->file_id, $price, $profile);
                                }                                                   
                            }
                        }
                    } else {
                        // use fix points
                        $sum_points = $aup_fix_points * count($files_arr);
                        if ($profile->points >= $sum_points){
                            foreach($files as $aup_data){
                                $can_download = JDHelper::setAUPPointsDownloads($user->id, $aup_data->file_title, $aup_data->file_id, 0, $profile);
                            }
                        } else {
                            $can_download = false;
                        }    
                    }
                } else {
                    // no AUP active
                    $can_download = true;
                }
                if ($jlistConfig['user.can.download.file.when.zero.points'] && !$user->guest){
                    $can_download = true;
                }
            } else {
                $can_download = true;
            }        
        
        } else {

            // single download           

            // we must be ensure that the user cannot skiped special options or settings
            // check at first the password option
            if ($files[0]->password_md5 != ''){
                // captcha is activated for this user
                $session_result = (int)JDHelper::getSessionDecoded('jd_password_run');
                if ($session_result < 2){
                    // Abort !!!
                    $msg = JText::_('COM_JDOWNLOADS_ANTILEECH_MSG');
                    $app->redirect(JRoute::_($current_link), $msg, 'error');          
                } else {
                    JDHelper::writeSessionEncoded('0', 'jd_password_run');
                }
            } else {
                // when is not use a password,  we must check captcha
                if ($user_rules->view_captcha){
                    // captcha is activated for this user
                    $session_result = (int)JDHelper::getSessionDecoded('jd_captcha_run');
                    if ($session_result < 2){
                        // Abort !!!
                        $msg = JText::_('COM_JDOWNLOADS_ANTILEECH_MSG');
                        $app->redirect(JRoute::_($current_link), $msg, 'error');          
                    } else {
                        JDHelper::writeSessionEncoded('0', 'jd_captcha_run');
                    }
                }
            }              
            
           if (!$mirror){
               if ($files[0]->url_download){
                   // build the complete category path
                   if ($files[0]->cat_id > 1){
                       // Download has a category
                       if ($files[0]->category_cat_dir_parent != ''){
                           $cat_dir = $files[0]->category_cat_dir_parent.'/'.$files[0]->category_cat_dir;
                       } else {
                           $cat_dir = $files[0]->category_cat_dir;
                       }               
                       $filename        = $jlistConfig['files.uploaddir'].'/'.$cat_dir.'/'.$files[0]->url_download;
                       $filename_direct = $jlistConfig['files.uploaddir'].'/'.$cat_dir.'/'.$files[0]->url_download;        
                   } else {
                       // Download is 'uncategorized'
                       $filename = $jlistConfig['files.uploaddir'].'/'.$jlistConfig['uncategorised.files.folder.name'].'/'.$files[0]->url_download;
                   }    
               } elseif ($files[0]->other_file_id) {
                            // A file from another Download was assigned         
                            $query = $db->getQuery(true);
                            $query->select('a.*');
                            $query->from('#__jdownloads_files AS a');
                            // Join on category table.
                            $query->select('c.id AS category_id, c.cat_dir AS category_cat_dir, c.cat_dir_parent AS category_cat_dir_parent');
                            $query->join('LEFT', '#__jdownloads_categories AS c on c.id = a.cat_id');
                            $query->where('a.published = '.$db->Quote('1'));
                            $query->where('a.file_id = '.$db->Quote($files[0]->other_file_id));
                            $query->where('a.access IN ('.$groups.')');
                            $db->setQuery($query);
                            $other_file_data = $db->loadObject();
                            if ($other_file_data->cat_id > 1){
                                // the assigned Download has a category
                                if ($other_file_data->category_cat_dir_parent != ''){
                                    $cat_dir = $other_file_data->category_cat_dir_parent.'/'.$other_file_data->category_cat_dir;
                               } else {
                                    $cat_dir = $other_file_data->category_cat_dir;
                               }               
                               $filename        = $jlistConfig['files.uploaddir'].'/'.$cat_dir.'/'.$other_file_data->url_download;
                               $filename_direct = $jlistConfig['files.uploaddir'].'/'.$cat_dir.'/'.$other_file_data->url_download;
                            } else {
                               // Download is 'uncategorized'
                               $filename = $jlistConfig['files.uploaddir'].'/'.$jlistConfig['uncategorised.files.folder.name'].'/'.$other_file_data->url_download;
                            }                   
               } else {
                   $filename = $files[0]->extern_file; 
                   if ($files[0]->extern_site){
                       $extern_site = true;
                   }
                   $extern = true;
               }
           } else {
             // is mirror 
             if ($mirror == 1){
                 $filename = $files[0]->mirror_1; 
                 if ($files[0]->extern_site_mirror_1){
                     $extern_site = true;
                 }
             } else {
                 $filename = $files[0]->mirror_2; 
                 if ($files[0]->extern_site_mirror_2){
                     $extern_site = true;
                 }
             }
             $extern = true;    
           }      

           $price = '';
           
           // Is AUP rule or price option used - we need the price for it
           if ($aup_exist){
               if ($jlistConfig['use.alphauserpoints.with.price.field']){
                   $price = floatval($files[0]->price);
               } else {
                   $price = $aup_fix_points;
               }        
           }    
            
           if (!$download_in_parts){
               $can_download = JDHelper::setAUPPointsDownload($user->id, $files[0]->file_title, $files[0]->file_id, $price, $profile);
           
               if ($jlistConfig['user.can.download.file.when.zero.points'] && $user->id){
                   $can_download = true;
               }
           } else {
               $can_download = true;
           }        
        }    

        
        // plugin support 
        // load external plugins
        $dispatcher = JDispatcher::getInstance();
        JPluginHelper::importPlugin('jdownloads');
        $results = $dispatcher->trigger('onBeforeDownloadIsSendJD', array(&$files, &$can_download, $user_rules, $download_in_parts));    
        
        if (!$can_download){
            $msg = JText::_('COM_JDOWNLOADS_BACKEND_SET_AUP_FE_MESSAGE_NO_DOWNLOAD');
            $app->redirect(JRoute::_($current_link), $msg, 'notice');
        } else {
            // run download            
            
            if (!$download_in_parts){
                // send at first e-mail
                if ($jlistConfig['send.mailto.option'] == '1' && $files){
                    JDHelper::sendMailDownload($files);               
                }

                // give uploader AUP points when is set on
                if ($jlistConfig['use.alphauserpoints']){
                    if ($jlistConfig['use.alphauserpoints.with.price.field']){
                        JDHelper::setAUPPointsDownloaderToUploaderPrice($files);
                    } else {    
                        JDHelper::setAUPPointsDownloaderToUploader($files);
                    }
                      
                }

                // write data in log 
                if ($jlistConfig['activate.download.log']){
                    JDHelper::updateLog($type = 1, $files, '');  
                }                
            
                // update downloads hits
                if (count($files) > 1){
                    $db->setQuery('UPDATE #__jdownloads_files SET downloads=downloads+1 WHERE file_id IN ('.$files_list.')'); 
                    $db->execute();    
                } else {
                    $db->setQuery("UPDATE #__jdownloads_files SET downloads=downloads+1 WHERE file_id = '".$files[0]->file_id."'");
                    $db->execute();
                }
            }
                
            // get filesize
            if (!$extern){
                if (!file_exists($filename)) { 
                    $msg = JText::_('COM_JDOWNLOADS_FILE_NOT_FOUND').': '.basename($filename);
                    $app->redirect(JRoute::_($current_link), $msg, 'notice');
                } else {
                    $size = filesize($filename);
                }    
            } else {   
                 $size = JDHelper::getUrlFilesize($filename);
            }
            
            // if url go to other website - open it in a new browser window
            if ($extern_site){
                echo "<script>document.location.href='$filename';</script>\n";  
                exit;   
            }    
            
            // if set the option for direct link to the file
            if (!$jlistConfig['use.php.script.for.download']){
                
                $root = str_replace('\\', '/', $_SERVER["DOCUMENT_ROOT"]);
                $root = rtrim($root, "/");               
                $host = $_SERVER["HTTP_HOST"];                
                
                // alternate when symlink are used (like "Strato")
                $joomla_host = JURI::root();
                $joomla_root = JPATH_ROOT.'/';
                $joomla_root = str_replace('\\', '/', $joomla_root);
                
                if (strpos($filename_direct, $root) !== false ){
                    $filename_direct = str_replace($root, $host, $filename_direct);
                } else {
                    $filename_direct = str_replace($joomla_root, $joomla_host, $filename_direct);
                }   
                    
                if (strpos($filename_direct, 'http://') === false && strpos($filename_direct, 'https://') === false && strpos($filename_direct, 'ftp://') === false){
                    //$filename_direct = str_replace('//', '/', $filename_direct);
                    $filename_direct = 'http://'.$filename_direct;
                }
                
                $app->redirect($filename_direct);

            } else {    
                $only_filename = basename($filename);
                $extension = JDHelper::getFileExtension($only_filename);
                if ($extern){
                    $mime_type = JDHelper::getMimeTypeRemote($filename);
                } else {
                    $mime_type = JDHelper::getMimeTyp($extension);
                }
                
                // Check for protocol and set the appropriate headers
                $use_ssl  = false;
                $uri      = JUri::getInstance(JUri::current());
                $protocol = $uri->getScheme();
                if ($protocol == 'https'){ 
                    $use_ssl = true;
                }
                
                $open_in_browser = false;
                if (in_array($extension, $view_types)){
                    // view file in browser
                    $open_in_browser = true;
                }                    
                
                clearstatcache();                     
               
                if ($extern){                

                    ob_end_clean();
                    // needed for MS IE - otherwise content disposition is not used?
                    if (ini_get('zlib.output_compression')){
                        ini_set('zlib.output_compression', 'Off');
                    }
                    
                    header("Cache-Control: public, must-revalidate");
                    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
                    // header("Pragma: no-cache");  // Problems with MS IE
                    header("Expires: 0"); 
                    header("Content-Description: File Transfer");
                    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                    header("Content-Type: " . $mime_type);
                    header("Content-Length: ".(string)$size);
                    if (!$open_in_browser){
                        header('Content-Disposition: attachment; filename="'.$only_filename.'"');
                    } else {
                      // view file in browser
                      header('Content-Disposition: inline; filename="'.$only_filename.'"');
                    }   
                    header("Content-Transfer-Encoding: binary\n");
                    // redirect to category when it is set the time
                    if (intval($jlistConfig['redirect.after.download']) > 0){ 
                        header( "refresh:".$jlistConfig['redirect.after.download']."; url=".$current_link );
                    }    
                    
                    // set_time_limit doesn't work in safe mode
                    if (!ini_get('safe_mode')){ 
                        @set_time_limit(0);
                    }
                    @readfile($filename);
                    flush();
                    exit;
                    
                } else {    
                            
                    $download_class_file = JPATH_SITE.DS.'components'.DS.'com_jdownloads'.DS.'helpers'.DS.'downloader.php';
                    if (file_exists($download_class_file)){
                         require_once ($download_class_file);
                         $object = new downloader;
                         $object->set_byfile($filename);              // Type: Download from a file
                         $object->set_filename($only_filename);       // Set the file basename
                         $object->set_filesize($size);                // Set the file basename
                         $object->set_mime($mime_type);               // Set the mime type
                         $object->set_speed($transfer_speed);         // Set download speed 
                         $object->set_refresh($current_link, (int)$jlistConfig['redirect.after.download']); // // redirect to category when it is set the time in configuration
                         $object->use_resume      = true;             // Set the value for using Resume Mode
                         $object->use_ssl         = $use_ssl;         // Set support for SSL
                         $object->open_in_browser = $open_in_browser; // Set whether the file shall be opened in browser window
                         $object->use_autoexit    = true;             // Set the value for auto exit  ('false' worked not really with extern file?)
                         $object->download();                         // Run the download
                         flush();
                         exit;
                    } else {
                         $msg = JText::_('COM_JDOWNLOADS_FILE_NOT_FOUND').': '.$filename;
                         $app->redirect(JRoute::_($current_link), $msg, 'notice');                
                    }
               }    
           }
        }    
    }    
}
?>