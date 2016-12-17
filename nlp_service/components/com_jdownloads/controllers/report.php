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

jimport('joomla.application.component.controllerform');

/**
*       
 */
class jdownloadsControllerReport extends JControllerForm
{


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
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 */
	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(urldecode(base64_decode($return)))) {
			return JURI::base();
		}
		else {
			return urldecode(base64_decode($return));
		}
	}


	/**
	 * Method to send the report form data to the defined e-mail addresses
	 *
	 */
	public function send()
	{
	
        // Check for request forgeries.
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('Report');
        if ($model->send()) {
            $type = 'message';
        } else {
            $type = 'error';
        }

        $msg = $model->getError();
        $this->setRedirect('index.php?option=com_jdownloads', $msg, $type);
    }

    /**
     * Method to cancel a report form.
     *
     */
     public function cancel()
     {
        // Check for request forgeries.
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
        $this->setRedirect('index.php?option=com_jdownloads');
     }

}