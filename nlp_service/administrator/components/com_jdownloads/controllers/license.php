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

defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.controllerform');

/**
 * License controller class.
 *
 * @package        Joomla.Administrator
 * @subpackage    com_weblinks
 * @since        1.6
 */
class jdownloadsControllerlicense extends JControllerForm
{
  
   /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct();

        // Register Extra task
        $this->registerTask( 'apply', 'save' );
        $this->registerTask( 'add',   'edit' );
                
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
     * @since    1.6
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
    
}
?>    