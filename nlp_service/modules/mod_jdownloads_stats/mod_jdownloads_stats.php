<?php
/**
* @version $Id: mod_jdownloads_stats.php
* @package mod_jdownloads_stats
* @copyright (C) 2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jdownloads.com
*
* This modul shows the statistic values from the jDownloads component.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once __DIR__ . '/helper.php';

	$db = JFactory::getDBO(); 
	$user   = JFactory::getUser(); 

    $text            = $params->get( 'text' );
    $text            = ModJDownloadsStatsHelper::getOnlyLanguageSubstring($text);	

    $text_admin      = $params->get( 'text_admin' );
    $text_admin      = ModJDownloadsStatsHelper::getOnlyLanguageSubstring($text_admin);    

	$color           = trim($params->get( 'value_color' ) );
	$alignment       = ($params->get( 'alignment' ) ); 
	
    $result = modJdownloadsStatsHelper::getData($params);    

    $sumcats        = modJdownloadsStatsHelper::strToNumber($result['cats']);
    $sumfiles       = modJdownloadsStatsHelper::strToNumber($result['files']);
    $sumdownloads   = modJdownloadsStatsHelper::strToNumber($result['hits']);
    $sumviews       = modJdownloadsStatsHelper::strToNumber($result['views']);
    
    $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

    require JModuleHelper::getLayoutPath('mod_jdownloads_stats',$params->get('layout', 'default'));    
?>