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
 * Jdownloads categories Controller
 *
 */
class jdownloadsControllercategories extends JControllerAdmin
{
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();
  
	}

    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'category', $prefix = 'jdownloadsModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    /**
     * Save the manual order inputs from the categories list page.
     *
     * @return    void
    */
    public function saveorder()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the arrays from the Request
        //$order    = JRequest::getVar('order',    null, 'post', 'array');
        //$originalOrder = explode(',', JRequest::getString('original_order_values'));
        $order = $this->input->post->get('order', null, 'array');
        $originalOrder = explode(',', $this->input->getString('original_order_values'));

        // Make sure something has changed
        if (!($order === $originalOrder)) {
            parent::saveorder();
        } else {
            // Nothing to reorder
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
            return true;
        }
    } 
    
    /**
     * Rebuild the nested set tree.
     *
     * @return    bool    False on failure or error, true on success.
     */
    public function rebuild()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=categories', false));

        // Initialise variables.
        $model = $this->getModel();

        if ($model->rebuild()) {
            // Rebuild succeeded.
            $this->setMessage(JText::_('COM_JDOWNLOADS_REBUILD_CATS_SUCCESS'));
            return true;
        } else {
            // Rebuild failed.
            $this->setMessage(JText::_('COM_JDOWNLOADS_REBUILD_CATS_FAILURE'));
            return false;
        }
    }       
    
    
    /**
     * Method to publish a list of items
     *
     * @return  void
     */
    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $data = array('publish' => 1, 'unpublish' => 0);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            JError::raiseWarning(500, JText::_(COM_JDOWNLOADS_NO_ITEM_SELECTED));
        }
        else
        {
            // Get the model.
            //$model = $this->getModel();
            $model = $this->getModel('category', '', array());

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);

            // Publish the items.
            if (!$model->publish($cid, $value))
            {
                JError::raiseWarning(500, $model->getError());
            }
            else
            {
                if ($value == 1)
                {
                    $ntext = 'COM_JDOWNLOADS_N_ITEMS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = 'COM_JDOWNLOADS_N_ITEMS_UNPUBLISHED';
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
        }
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=categories', false));
    }        
 	
}
?>
