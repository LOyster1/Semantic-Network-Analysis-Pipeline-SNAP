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

defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.application.component.modeladmin');
 
/**
 * Logs Table class
 */
class jdownloadsTableconfig extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__jdownloads_config', 'id', $db);
	}
    
    
}
?>