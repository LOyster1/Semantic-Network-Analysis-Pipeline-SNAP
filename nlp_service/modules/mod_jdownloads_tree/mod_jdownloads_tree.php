<?php
/**
* @version		$Id: mod_jDMTree1.5.5
* @package		DOCMan jDMTree Module for Joomla 1.5
* @copyright	Copyright (C) 2008-2010 youthpole.com. All rights reserved.
* @author     Josh Prakash
* @license		GNU/GPL, see LICENSE.php
* This module is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* Dec-20-2010 Adapted and modified for jDownloads by Arno Betz
* Aug-20-2011 Adapted and modified for jDownloads 1.9 by Arno Betz
* Jun-16-2015 Adapted and modified for jDownloads 3.2 by Arno Betz
* Version 3.2.34
*
*/

// no direct access
    defined('_JEXEC') or die('Restricted access');

    require_once(JPATH_SITE.DS.'modules'.DS.'mod_jdownloads_tree'.DS.'jdtree'.DS.'jdownloadstree.php');
    require_once __DIR__ . '/helper.php';
    
    $user = JFactory::getUser();
	$db	  = JFactory::getDBO();
    
    $lang = JFactory::getLanguage();
    $lang->load('com_jdownloads');      
    
    $Itemid  = JRequest::getVar("Itemid");
    
    // get published root menu link
    $db->setQuery("SELECT id from #__menu WHERE link = 'index.php?option=com_jdownloads&view=categories' and published = 1");
    $root_itemid = $db->loadResult();
    if ($root_itemid){
        $Itemid = $root_itemid;
    }

    $home_url = JRoute::_('index.php?option=com_jdownloads&amp;view=categories&amp;Itemid='.$Itemid);
    $home_link = '<a href="'.$home_url.'">'.JText::_('COM_JDOWNLOADS_HOME_LINKTEXT').'</a>';

    $lengthc    = intval( $params->get( 'lengthc', 20 ) );	 // Max length of category before truncation
    $baseGif = "modules/mod_jdownloads_tree/jdtree/images/base.gif";
    $nodeId = 0;
    $counter = 0;
    $catlink = "";
    $curcat = 0;
    $precat = -1;

    $rows = modJdownloadsTreeHelper::getList($params);

    if (!count($rows)) {
        return;
    }

    $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

    require JModuleHelper::getLayoutPath('mod_jdownloads_tree',$params->get('layout', 'default'));    
?>