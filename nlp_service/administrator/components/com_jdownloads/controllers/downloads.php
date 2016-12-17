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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin'); 

/**
 * jDownloads list downloads controller class.
 *
 */
class jdownloadsControllerdownloads extends JControllerAdmin
{
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();
        
        // Register Extra task
        $this->registerTask('delete',    'delete');
        $this->registerTask('unfeatured', 'featured');
            
	}

                                                
    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'download', $prefix = 'jdownloadsModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    } 
	
    
   /**
    * Removes an download item in db table.
    *
    * @return  void
    *
    */    
    public function delete()
    {
        $jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', 0, 'array');
		$error          = '';
        $message        = '';
        
        // run the model methode
        $model = $this->getModel();
        
        if(!$model->delete($cid))
        {
            $msg = JText::_( 'COM_JDOWNLOADS_ERROR_RESULT_MSG' );
            $error = 'error';
        } else {                             
            $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
        }
        $this->setRedirect( 'index.php?option=com_jdownloads&view=downloads', $msg, $error);       
    }
    
   /**
    * Method to publish a list of items
    *
    * @return  void
    *
    */    
    public function publish()
    {
        global $jlistConfig;
        
        require_once JPATH_COMPONENT_SITE.'/helpers/jdownloadshelper.php';
        
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
                
        // Get items to publish from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $data = array('publish' => 1, 'unpublish' => 0);
        $task = $this->getTask();
        $state = JArrayHelper::getValue($data, $task, 0, 'int');        
        
        if (empty($cid)){
            JLog::add(JText::_('JGLOBAL_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
            $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=downloads', false));
        } else {
            if ($state == 1 && $jlistConfig['use.alphauserpoints']){
                // load the model
                $model = $this->getModel();
                foreach ($cid as $id){
                    // load the items data
                    $item = $model->getItem($id);
                    // add the AUP points
                    JDHelper::setAUPPointsUploads($item->submitted_by, $item->file_title);
                }
            }
            parent::publish();
        }        
    } 
    
    /**
     * Method to toggle the featured setting of a list of Downloads.
     *
     * @return  void
     */
    public function featured()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $user   = JFactory::getUser();
        $ids    = $this->input->get('cid', array(), 'array');
        $values = array('featured' => 1, 'unfeatured' => 0);
        $task   = $this->getTask();
        $value  = JArrayHelper::getValue($values, $task, 0, 'int');

        // Access checks.
        foreach ($ids as $i => $id)
        {
            if (!$user->authorise('core.edit.state', 'com_jdownloads.download.' . (int) $id))
            {
                // Prune items that you can't change.
                unset($ids[$i]);
                JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
            }
        }

        if (empty($ids))
        {
            JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Publish the items.
            if (!$model->featured($ids, $value))
            {
                JError::raiseWarning(500, $model->getError());
            }

            if ($value == 1)
            {
                $message = JText::plural('COM_JDOWNLOADS_N_ITEMS_FEATURED', count($ids));
            }
            else
            {
                $message = JText::plural('COM_JDOWNLOADS_N_ITEMS_UNFEATURED', count($ids));
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=downloads', false), $message);
    }

}
?>