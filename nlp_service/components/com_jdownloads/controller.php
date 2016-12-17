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
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * jDownloads Component Controller
 */
class jdownloadsController extends JControllerLegacy
{
    
    function __construct($config = array())
    {
        $this->input = JFactory::getApplication()->input;
        
        // Frontpage Editor 'select download' proxying:
        if ($this->input->get('view') === 'downloads' || $this->input->get('view') === 'list'){ 
            if ($this->input->get('layout') === 'modal' || $this->input->get('layout') === 'modallist'){
                JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
                $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
            }
        }        
        
        parent::__construct($config);    
    }    
    
    
    /**
     * Method to display a view.
     *
     * @param    boolean          If true, the view output will be cached
     * @param    array            An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return    JController     This object to support chaining.
     */
    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;

        // Get the document object.
        $document = JFactory::getDocument();
        $jinput = JFactory::getApplication()->input;
        
        // Set the default view name and format from the Request.
        // Note we are using a_id to avoid collisions with the router and the return page.
        $id     = JRequest::getInt('a_id');
        $vName  = $jinput->get('view', 'categories');
        
        JRequest::setVar('view', $vName);

        $user = JFactory::getUser();

        $safeurlparams = array('catid'=>'INT', 'id'=>'INT', 'cid'=>'ARRAY', 'list'=>'STRING', 'user'=>'UINT', 'type'=>'STRING', 'm'=>'UINT', 'year'=>'INT', 'month'=>'INT', 'limit'=>'UINT', 'limitstart'=>'UINT',
            'showall'=>'INT', 'return'=>'BASE64', 'filter'=>'STRING', 'order'=>'CMD', 'filter_order'=>'CMD', 'dir'=>'CMD', 'filter_order_Dir'=>'CMD', 'filter-search'=>'STRING', 'print'=>'BOOLEAN', 'lang'=>'CMD', 'Itemid' => 'INT');

        // Check for edit form.
        if ($vName == 'form' && !$this->checkEditId('com_jdownloads.edit.download', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
        }
        
        parent::display($cachable, $safeurlparams);

        return $this;
    }    
    
}
?>
