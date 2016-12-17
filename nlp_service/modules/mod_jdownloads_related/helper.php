<?php
/**
* @version $Id: mod_jdownloads_related.php
* @package mod_jdownloads_related
* @copyright (C) 2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*
* This modul shows you some related downloads from the jDownloads component. 
* It is only for jDownloads 3.x and later (Support: www.jDownloads.com)
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jdownloads/models', 'jdownloadsModel');

class modJdownloadsRelatedHelper
{
	static function getList($params, $catids, $id)
	{
        $db = JFactory::getDbo();

        // Get an instance of the generic downloads model
        $downloads = JModelLegacy::getInstance ('downloads', 'jdownloadsModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams('com_jdownloads');
        $downloads->setState('params', $appParams);
       
        // Set the filters based on the module params
        $downloads->setState('list.start', 0);
        $downloads->setState('list.limit', (int) $params->get('sum_view', 5) + 1);
        $downloads->setState('filter.published', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_jdownloads')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $downloads->setState('filter.access', $access);

        // Category filter
        if ($params->get('show_child_category_downloads', 0) && (int) $params->get('levels', 0) > 0){
            // Get an instance of the generic categories model
            $categories = JModelLegacy::getInstance('Categories', 'jdownloadsModel', array('ignore_request' => true));
            $categories->setState('params', $appParams);
            $levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
            $categories->setState('filter.get_children', $levels);
            $categories->setState('filter.published', 1);
            $categories->setState('filter.access', $access);
            $additional_catids = array();            
        
            $categories->setState('filter.parentId', $catids[0]);
            $recursive = true;
            $cats = $categories->getItems($recursive);
    
            if ($cats){
                foreach($cats as $category){
                    $condition = (($category->level - $categories->getParent()->level) <= $levels);
                    if ($condition){
                        $additional_catids[] = $category->id;
                    }
                }
            }
            $catids = array_unique(array_merge($catids, $additional_catids));
        }
        $downloads->setState('filter.category_id', $catids);
        
        // User filter
        $userId = JFactory::getUser()->get('id');

        // Filter by language
        $downloads->setState('filter.language', $app->getLanguageFilter());

        // Set sort ordering
        $downloads->setState('list.ordering', $params->get('download_ordering', 'a.ordering'));
        $downloads->setState('list.direction', $params->get('download_ordering_direction', 'ASC'));        

        $items = $downloads->getItems();

        foreach ($items as &$item)
        {
            $item->slug = $item->file_id . ':' . $item->file_alias;
            $item->catslug = $item->cat_id . ':' . $item->category_alias;

            if ($access || in_array($item->access, $authorised))
            {
                // We know that user has the privilege to view the download
                $item->link = '-';
            } else {
                $item->link = JRoute::_('index.php?option=com_users&view=login');
            }
        }
        return $items;        
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