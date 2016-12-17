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

jimport('joomla.application.component.controller');

/**
 * Jdownloads Component Jdownloads Controller
 *
 * @package Joomla
 * @subpackage Jdownloads
 */
class jdownloadsControllerlayouts extends jdownloadsController
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

	}
    
    public function install() 
    {
         // set redirect
         $this->setRedirect( 'index.php?option=com_jdownloads&view=layoutinstall');        
    }

}
?>