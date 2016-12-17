<?php
/**
 * @package jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2012 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

/**
 * Template controller class.
 *
 */
class jdownloadsControllerdownload extends JControllerForm
{
    var $tmpl_type = 0;
    
   /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct();

        // Register Extra task
        $this->registerTask( 'apply',           'save' );
        $this->registerTask( 'add',             'edit' );
        $this->registerTask( 'download',        'download' );
        $this->registerTask( 'delete',          'delete' );
        $this->registerTask( 'deletepreview',   'deletepreview' );        
        $this->registerTask( 'create',          'add' );
        
        // store filename in session when is selected in files list
        $jinput = JFactory::getApplication()->input;
        $filename = ($jinput->get('file', '', 'string'));
        $filename = JFilterOutput::cleanText($filename);
        $session = JFactory::getSession();
        if ($filename != ''){
            $session->set('jd_filename',$filename);
        } else {
            $session->set('jd_filename','');            
        }      
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param    array    $data    An array of input data.
     * @return    boolean
     * @since    1.6
     */
    protected function allowAdd($data = array()) 
    {
        // Initialise variables. 
        $user        = JFactory::getUser();
        $allow        = null;
        $allow    = $user->authorise('core.create', 'com_jdownloads');
        
        if ($allow === null) {
            return parent::allowAdd($data);
        } else {
            return $allow;
        }
    }
    
    /**
     * Method to check if you can edit a record.
     *
     * @param    array    $data    An array of input data.
     * @param    string    $key    The name of the key for the primary key.
     *
     * @return    boolean
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        // Initialise variables. 
        $user        = JFactory::getUser();
        $allow        = null;
        $allow    = $user->authorise('core.edit', 'com_jdownloads');
        if ($allow === null) {
            return parent::allowEdit($data, $key);
        } else {
            return $allow;
        }
    }
    
    public function download()
    {
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id', 0, 'integer');
        $type = $jinput->get('type', '', 'string');
        
        if ($id){
            JDownloadsHelper::downloadFile($id, $type);
        }        
        // set redirect
        $this->setRedirect( 'index.php?option=com_jdownloads&view=files', $msg );
    }
    
    // delete the assigned file from a download
    public function delete()
    {
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id', 0, 'integer');
        $type = $jinput->get('type', '', 'string');
        
        $result = false;
        $msg    = '';
        
        if ($id){
            if ($type == 'prev'){
                $result = JDownloadsHelper::deletePreviewFile($id);
            } else {
                $result = JDownloadsHelper::deleteFile($id);
            }
            if ($result){
                $msg = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_REMOVE_OK');
            } else {
                $msg = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_REMOVE_ERROR');
            }
        }        
        // set redirect
        $this->setRedirect( 'index.php?option=com_jdownloads&task=download.edit&file_id='.$id, $msg );
    }
    
    public function batch() 
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model    = $this->getModel('download', '', array());
        
        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_jdownloads&view=downloads'.$this->getRedirectToListAppend(), false));
        
        return parent::batch($model);
    } 
    
    public function create(){ 
          $x = 99;
    
        //return parent::edit($model);
    }
    
}
?>    