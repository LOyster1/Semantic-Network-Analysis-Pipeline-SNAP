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

if (!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
} 

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jdownloads')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'jdownloadshelper.php';

global $jlistConfig;
$GLOBALS['jlistConfig'] = JDownloadsHelper::buildjlistConfig(); 

jimport('joomla.application.component.controller');

// Get an instance of the controller
$controller = JControllerLegacy::getInstance('jdownloads');
 
// Perform the Request task
$jinput = JFactory::getApplication()->input;
$controller->execute($jinput->get('task'));
 
// Redirect if set by the controller
$controller->redirect();
                   

?>