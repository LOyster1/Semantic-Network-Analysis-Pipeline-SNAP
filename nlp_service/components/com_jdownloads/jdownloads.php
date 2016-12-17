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

global $jlistConfig;

if (!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

jimport('joomla.application.component.controller');

// Include helpers
require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';
require_once JPATH_COMPONENT.'/helpers/route.php';

$GLOBALS['jlistConfig'] = JDHelper::buildjlistConfig(); 

$controller = JControllerLegacy::getInstance('jdownloads');

// Perform the Request task
$jinput = JFactory::getApplication()->input;
$controller->execute($jinput->get('task'));
$controller->redirect();

?>