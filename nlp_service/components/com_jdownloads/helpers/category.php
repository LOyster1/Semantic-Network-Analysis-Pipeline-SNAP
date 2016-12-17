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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * jDownloads Component Category Tree
 *
 * @static
 */
class JdownloadsCategories extends JDCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__jdownloads_files';
		parent::__construct($options);
	}
}
