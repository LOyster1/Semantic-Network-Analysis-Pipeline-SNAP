<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 *
 * @component jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2011 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin'); 

/**
 * jDownloads logs controller class.
 * @package jDownloads
 */
class jdownloadsControllerlogs extends JControllerAdmin
{
                                                                                    
    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'logs', $prefix = 'jdownloadsModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    // add marked log IDs to the block IP list 
    public function blockip(){
        
        $jinput = JFactory::getApplication()->input;
        
        $cid    = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        
        $model    = $this->getModel( 'logs' );
        
        if ($model->blockip($cid)) {
            $msg = JText::_( 'COM_JDOWNLOADS_BACKEND_LOG_LIST_BLOCK_IP_ADDED' );
        } else {
            $msg = JText::_( 'COM_JDOWNLOADS_BACKEND_LOG_LIST_BLOCK_IP_NOT_ADDED' );
        }
        $link = 'index.php?option=com_jdownloads&view=logs';
        $this->setRedirect($link, $msg);
    }
}
?>