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

jimport('joomla.application.component.controller');

/**
 * Jdownloads Component Jdownloads Controller
 *
 * @package Joomla
 * @subpackage Jdownloads
 */
class jdownloadsControllerFiles extends jdownloadsController
{
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();                            

	}
    
    public function getModel($name = 'files', $prefix = 'jdownloadsModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function delete()
    {
         
         global $jlistConfig;
         jimport('joomla.filesystem.file');
         
         $msg = '';
         $deleted = 0;
         $cid = array();
         $cid = JArrayHelper::getValue($_POST, 'cid', 0); 

         if (count($cid)){
             foreach ($cid as $file){
                 // delete the file
                 if (!JFile::delete($jlistConfig['files.uploaddir'].DS.$file)){
                     // can not delete!
                     $this->setRedirect( 'index.php?option=com_jdownloads&view=files', JText::_('COM_JDOWNLOADS_MANAGE_FILES_DELETE_ERROR'), 'error');
                } else {    
                    $deleted++;
                } 
             }
             // successful!
             $msg = sprintf(JText::_('COM_JDOWNLOADS_MANAGE_FILES_DELETE_SUCCESS'),$deleted);    
         }    
         // set redirect
         $this->setRedirect( 'index.php?option=com_jdownloads&view=files', $msg );
    }
    
    public function uploads() 
    {
         // set redirect
         $this->setRedirect( 'index.php?option=com_jdownloads&view=uploads');        
    }  
    
                  
    public function downloads() 
    {
         // set redirect
         $this->setRedirect( 'index.php?option=com_jdownloads&view=downloads');        
    }  
    
}
?>