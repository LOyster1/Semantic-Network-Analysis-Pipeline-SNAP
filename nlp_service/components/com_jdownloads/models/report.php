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
class jdownloadsModelReport extends jdownloadsModeldownload
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
		$pk = $app->input->get('id', 0, 'int');
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
    
    
    public function getForm($data = array(), $loadData = true) 
    {
        
        // Initialise variables.
        $app    = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm('com_jdownloads.report', 'report',
                                array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) 
        {
            return false;
        }
        return $form;
    }    
    
    public function send()
    {
        global $jlistConfig;
        
        // Initialise variables.
        $data    = JRequest::getVar('jform', array(), 'post', 'array');
        $app     = JFactory::getApplication();

        $name       = array_key_exists('name', $data)       ? $data['name'] : '';
        $email      = array_key_exists('email', $data)      ? $data['email'] : '';
        $file_id    = array_key_exists('file_id', $data)    ? intval($data['file_id']) : 0;
        $file_title = array_key_exists('file_title', $data) ? $data['file_title'] : '';
        $file_name  = array_key_exists('url_download', $data)  ? $data['url_download'] : '';
        $cat_id     = array_key_exists('cat_id', $data)     ? $data['cat_id'] : 0;
        $cat_title  = array_key_exists('cat_title', $data)  ? $data['cat_title'] : '';
        $reason     = array_key_exists('reason', $data)     ? intval($data['reason']) : 0;
        $text       = array_key_exists('text', $data)       ? $data['text'] : '';
        
        switch ($reason) {
            case 0:
                $reason_text = ''; 
                break;
            case 1:
                $reason_text = JText::_('COM_JDOWNLOADS_REPORT_REASON_MISSING'); 
                break;
            case 2:
                $reason_text = JText::_('COM_JDOWNLOADS_REPORT_REASON_BAD_FILE'); 
                break;
            case 3:
                $reason_text = JText::_('COM_JDOWNLOADS_REPORT_REASON_OTHERS'); 
                break;
        }        
        
        // get the users IP
        $ip = JDHelper::getRealIp();

        // automatically removes html formatting
        $text = JFilterInput::getInstance()->clean($text, 'string');
        $name = JFilterInput::getInstance()->clean($name, 'string');
        $email = JFilterInput::getInstance()->clean($email, 'string');
        
        // Get all users email addresses in an array
        $recipients = explode( ';', $jlistConfig['send.mailto.report']);

        // Check to see if there are any users in this group before we continue
        if (!count($recipients)) {
            $this->setError(JText::_('COM_JDOWNLOADS_NO_EMAIL_RECIPIENT_FOUND'));
            return false;
        }

        // Get the Mailer
        $mailer = JFactory::getMailer();

        // Build email message format.
        $mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
        $mailer->setSubject('jDownloads - '.stripslashes(JDHelper::getOnlyLanguageSubstring($jlistConfig['report.mail.subject'])));
        
        $message = JDHelper::getOnlyLanguageSubstring($jlistConfig['report.mail.layout']);
        $message = str_replace('{category}', $cat_title, $message);
        $message = str_replace('{cat_id}', $cat_id, $message);
        $message = str_replace('{file_id}', $file_id, $message);
        $message = str_replace('{file_title}', $file_title, $message);
        $message = str_replace('{file_name}', $file_name, $message);
        $message = str_replace('{name}', $name, $message);
        $message = str_replace('{mail}', $email, $message);
        $message = str_replace('{ip}', $ip, $message);
        $date_format = JDHelper::getDateFormat();
        $message = str_replace('{date_time}', JHtml::date($input = 'now', $date_format['long'], true), $message);
        $message = str_replace('{reason}', $reason_text, $message);
        $message = str_replace('{message}', $text, $message);
        
        $mailer->setBody($message);
        
        // Example: Optional file attached
        // $mailer->addAttachment(JPATH_COMPONENT.'/assets/document.pdf');
        // Example: Optionally add embedded image 
        // $mailer->AddEmbeddedImage( JPATH_COMPONENT.'/assets/logo128.jpg', 'logo_id', 'logo.jpg', 'base64', 'image/jpeg' );
        
        // Needed for use HTML 
        $mailer->IsHTML(true);
        $mailer->Encoding = 'base64';

        // Add recipients
        $mailer->addRecipient($recipients);

        // Send the Mail
        $result    = $mailer->Send();

        if ( $result !== true ) {
            $this->setError($result->getError());
            return false;
        } else {
            JError::raiseNotice( 100, JText::_('COM_JDOWNLOADS_EMAIL_SUCCESSFUL_SENDED'));
            return true;
        }        
    }    
    
}
