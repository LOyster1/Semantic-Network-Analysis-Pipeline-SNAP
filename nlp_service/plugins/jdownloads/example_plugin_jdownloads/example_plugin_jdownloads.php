<?php
/**
* @package jDownloads
* @copyright (C) 2014 www.jdownloads.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Example Plugin to handle events triggered by jDownloads and used by other extensions.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgJdownloadsExample extends JPlugin
{
	
	public function onBeforeDownloadButtonViewedJD ( &$userinfo, $rules, $download_url )
	{	
			//
			
			return true;
			
	}
	
	
	public function onAfterDownloadButtonViewedJD ( &$userinfo, $rules, $download_url )
	{	
			//
			
			return true;
			
	}
	
	public function onBeforeDownloadIsSendJD ( &$files, &$can_download, $user_rules, $download_in_parts) 
	{	
		// $files 			    : is an array, each file in files is a download which will be downloaded by user
		// $can_download 	    : boolean, when true, user may download the file then he complied all preconditions
		// $user_rules 		    : is an array
        // $download_in_parts   : boolean, is true when the user has started the same download (or parts from it) in the last hour(s). So we compute it not again (for AUP, log file, limits) 
		
		return true;
	
	}

	public function onAfterDownloadIsSendJD ( &$userinfo, $cid )
	{	
		// $cid is an array : each id in cid is a download which is downloaded by user
		
		return true;
	
	}
	

}
?>