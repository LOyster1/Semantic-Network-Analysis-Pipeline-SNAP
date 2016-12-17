<?php
/**
* @version $Id: mod_jdownloads_last_updated.php
* @package mod_jdownloads_updated
* @copyright (C) 2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*
* This Modul shows the last updated files from the component jDownloads. 
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

    require_once __DIR__ . '/helper.php';
    
    $db = JFactory::getDBO();
    
    $Itemid  = JRequest::getVar("Itemid");
    
    // get published root menu link
    $db->setQuery("SELECT id from #__menu WHERE link = 'index.php?option=com_jdownloads&view=categories' and published = 1");
    $root_itemid = $db->loadResult();
    
    if ($root_itemid){
        $Itemid = $root_itemid;
    }
    
    // get this option from configuration to see whether the links shall run the download without summary page
    $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'direct.download'");
    $direct_download_config = $db->loadResult();
    
    // get this option from configuration to see whether the links may going to the details page
    $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'view.detailsite'");
    $detail_view_config = $db->loadResult();

    $before                 = trim($params->get( 'text_before' ) );
    $text_before            = modJdownloadsLastUpdatedHelper::getOnlyLanguageSubstring($before);
    $after                  = trim($params->get( 'text_after' ) );
    $text_after             = modJdownloadsLastUpdatedHelper::getOnlyLanguageSubstring($after);    
	$cat_id                 = $params->get('cat_id', array());
	$sum_view               = intval(($params->get( 'sum_view' ) ));
	$sum_char               = intval(($params->get( 'sum_char' ) ));
	$short_char             = ($params->get( 'short_char' ) ); 
	$short_version          = ($params->get( 'short_version' ) );
	$detail_view            = ($params->get( 'detail_view' ) ); 
	$view_date              = ($params->get( 'view_date' ) );
	$view_date_same_line    = ($params->get( 'view_date_same_line' ) );
	$view_date_text         = ($params->get( 'view_date_text' ) );
    $view_date_text         = modJdownloadsLastUpdatedHelper::getOnlyLanguageSubstring($view_date_text);        
	// We use the standard short date format from the activated language when here is not a format defined 
    $date_format            = $params->get( 'date_format', JText::_('DATE_FORMAT_LC4') ); 
	$date_alignment         = ($params->get( 'date_alignment' ) );
	$view_pics              = ($params->get( 'view_pics' ) );
	$view_pics_size         = ($params->get( 'view_pics_size' ) );
	$view_numerical_list    = ($params->get( 'view_numerical_list' ) );
    $view_thumbnails        = $params->get( 'view_thumbnails' );
    $view_thumbnails_size   = $params->get( 'view_thumbnails_size' );
    $view_thumbnails_dummy  = $params->get( 'view_thumbnails_dummy' ); 
	$cat_show    	        = ($params->get( 'cat_show' ) );
	$cat_show_type	        = ($params->get( 'cat_show_type' ) );
	$cat_show_text          = ($params->get( 'cat_show_text' ) );
    $cat_show_text          = modJdownloadsLastUpdatedHelper::getOnlyLanguageSubstring($cat_show_text);    
	$cat_show_text_color    = ($params->get( 'cat_show_text_color' ) );
	$cat_show_text_size     = ($params->get( 'cat_show_text_size' ) );
	$cat_show_as_link       = ($params->get( 'cat_show_as_link' ) ); 
	$view_tooltip           = ($params->get( 'view_tooltip' ) ); 
	$view_tooltip_length    = intval(($params->get( 'view_tooltip_length' ) ));
	$alignment              = ($params->get( 'alignment' ) ); 
	$moduleclass_sfx        = ($params->get( 'moduleclass_sfx' ) );
    
    $thumbfolder = JURI::base().'images/jdownloads/screenshots/thumbnails/';
    $thumbnail = '';
    $border = ''; 

    $cat_show_text = trim($cat_show_text);
    if ($cat_show_text) $cat_show_text = ' '.$cat_show_text.' ';

    if ($sum_view == 0) $sum_view = 5;
    $option = 'com_jdownloads';
    
    $files = modJdownloadsLastUpdatedHelper::getList($params);

    if (!count($files)) {
	    return;
    }

    $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

    require JModuleHelper::getLayoutPath('mod_jdownloads_last_updated',$params->get('layout', 'default'));
?>