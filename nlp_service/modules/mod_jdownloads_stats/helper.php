<?php
/**
* @version $Id: mod_jdownloads_stats.php v3.2
* @package mod_jdownloads_stats
* @copyright (C) 2014 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

//require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';

//JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jdownloads/models', 'jdownloadsModel');

class modJdownloadsStatsHelper
{
	static function getData($params)
	{

        $db = JFactory::getDbo();
        $query    = $db->getQuery(true);

        $result = array();
        $use_all_items = $params->get('use_all_items', 0); 
        
        $app = JFactory::getApplication();
        $language = $app->getLanguageFilter();       
        
        // Access 
        $access = !JComponentHelper::getParams('com_jdownloads')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $groups = implode(',', $authorised);        
        
        // get at first the categories
        $query->select('id')->from('#__jdownloads_categories');
        $query->where('published = 1');
        if (!$use_all_items){
            $query->where('access IN ('.$groups.')');        
        }    
        $query->where("id > '1'"); // remove 'root' cat
        $db->setQuery($query);
        $cats = $db->loadColumn();
        $result['cats'] = (int)count($cats);
        
        $cats_id_list = implode(',', $cats);        
        
        // get the Downloads
        $query->clear();
        $query->select('COUNT(file_id)')->from('#__jdownloads_files');
        $query->where('published = 1');
        if (!$use_all_items){
            $query->where('access IN ('.$groups.')');        
        }    
        $query->where('cat_id = 1 OR cat_id IN ('.$cats_id_list.')');        
        $db->setQuery($query);
        $result['files'] = (int)$db->loadResult();
        
        // get the Downloads
        $query->clear();
        $query->select('SUM(downloads)')->from('#__jdownloads_files');
        $query->where('published = 1');
        if (!$use_all_items){
            $query->where('access IN ('.$groups.')');        
        }    
        $query->where('cat_id = 1 OR cat_id IN ('.$cats_id_list.')');        
        $db->setQuery($query);
        $result['hits'] = (int)$db->loadResult(); 
        
        $query->clear();
        $query->select('SUM(views)')->from('#__jdownloads_files');
        $query->where('published = 1');
        if (!$use_all_items){
            $query->where('access IN ('.$groups.')');        
        }    
        $query->where('cat_id = 1 OR cat_id IN ('.$cats_id_list.')');        
        $db->setQuery($query);
        $result['views'] = (int)$db->loadResult();                

        return $result;        
	}
    
    /**
    * remove the language tag from a given text and return only the text
    *    
    * @param string     $msg
    */
    public static function getOnlyLanguageSubstring($msg)
    {
        // Get the current locale language tag
        $lang       = JFactory::getLanguage();
        $lang_key   = $lang->getTag();        
        
        // remove the language tag from the text
        $startpos = strpos($msg, '{'.$lang_key.'}') +  strlen( $lang_key) + 2 ;
        $endpos   = strpos($msg, '{/'.$lang_key.'}') ;
        
        if ($startpos !== false && $endpos !== false){
            return substr($msg, $startpos, ($endpos - $startpos ));
        } else {    
            return $msg;
        }    
    }
    
    /**
    * Converts a string into Float while taking the given or locale number format into account
    * Used as default the defined separator characters from the Joomla main language ini file (as example: en-GB.ini)  
    * 
    * @param mixed $str
    * @param mixed $dec_point
    * @param mixed $thousands_sep
    * @param mixed $decimals
    * @return mixed
    */
    public static function strToNumber( $str, $dec_point=null, $thousands_sep=null, $decimals = 0 )
    {
        if( is_null($dec_point) || is_null($thousands_sep) ) {
            if( is_null($dec_point) ) {
                $dec_point = JText::_('DECIMALS_SEPARATOR');
            }
            if( is_null($thousands_sep) ) {
                $thousands_sep = JText::_('THOUSANDS_SEPARATOR');
            }
        }
        // in this case use we as default the en-GB format
        if (!$dec_point) $dec_point = '.'; 
        if (!$thousands_sep) $thousands_sep = ','; 

        $number = number_format($str, $decimals, $dec_point, $thousands_sep);
        return $number;
    }            
}	
?>