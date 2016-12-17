<?php


defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * jDownloads groups list controller class.
 *
 */
class jdownloadsControllerGroups extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	//protected $text_prefix = 'COM_JDOWNLOADS_GROUPS';

	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'group', $prefix = 'jdownloadsModel', $config = array('ignore_request' => true))
	{
        $model = parent::getModel($name, $prefix, $config);
        return $model;	
	}

    /**
     * logic to reset all jD user group limits
     *
     */
    public function resetLimits() 
    {
        $jinput = JFactory::getApplication()->input;
        $session        = JFactory::getSession();
        $error          = '';
        $cid            = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        
        // run the model methode
        $model = $this->getModel('groups');
        if(!$model->resetLimits($cid)) {
            $msg = JText::_( 'COM_JDOWNLOADS_USERGROUPS_RESET_LIMITS_RESULT_ERROR' );
            $error = 'error';
        } else {                             
            $msg = JText::_( 'COM_JDOWNLOADS_USERGROUPS_RESET_LIMITS_RESULT' );
        }
        $this->setRedirect( 'index.php?option=com_jdownloads&view=groups', $msg, $error);
    } 
}
