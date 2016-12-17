<?php
/**
* @version $Id: mod_jdownloads_most_recently_downloaded.php
* @package mod_jdownloads_most_recently_downloaded
* @copyright (C) 2014 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*/

/** This Modul shows the Most Recently Downloaded from the component jDownloads. 
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jdownloads/models', 'jdownloadsModel');

class modJdownloadsMostRecentlyDownloadedHelper
{
	static function getList(&$params)
	{

        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        
        $app = JFactory::getApplication();
        $appParams = $app->getParams('com_jdownloads');

        $sum_view = (int) $params->get('sum_view');
        $sum_view_total = $sum_view + 50;
        
        // Get an instance of the generic downloads model
        $logs_model = JModelLegacy::getInstance ('logs', 'jdownloadsModel', array('ignore_request' => true));
        // Set application parameters in model
        $logs_model->setState('params', $appParams);         
        
        // Set the filters based on the module params
        $logs_model->setState('list.start', 0);
        $logs_model->setState('list.limit', $sum_view_total);
        
        $logs_model->setState('filter.type', 1);  // 1=download 2=upload
        
        $logs_model->setState('list.ordering', 'a.log_datetime');
        $logs_model->setState('list.direction', 'DESC');        
        
        $logs = $logs_model->getItems();
        
        if (!$logs){
            return;
        }
        
        $logs_ids = '';
        foreach ($logs as $log) {
            $logs_ids .= $log->log_file_id . ",";
        }
        $logs_ids = trim( $logs_ids, ',' );  
        
        // Get an instance of the generic downloads model
        $model = JModelLegacy::getInstance ('downloads', 'jdownloadsModel', array('ignore_request' => true));
        // Set application parameters in model
        $model->setState('params', $appParams); 

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', $sum_view_total);
        $model->setState('filter.published', 1);
        
        // Access filter
        $access = !JComponentHelper::getParams('com_jdownloads')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels($user->id);
        $model->setState('filter.access', $access);

        // Category filter
        $catid = $params->get('cat_id', array()); 
        if (empty($catid)){
            $model->setState('filter.category_id', '');
        } else {
            $model->setState('filter.category_id', $catid);
        }
        
        // Logs filter
        $model->setState('filter.log_id', $logs_ids);
            
        // Filter by language
        //$model->setState('filter.language', $app->getLanguageFilter());

        // Set sort ordering
        $ordering = 'file_id';
        $dir = 'ASC';

        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);

        $items = $model->getItems();

        foreach ($items as $item)
        {
            $item->slug = $item->file_id . ':' . $item->file_alias;
            $item->catslug = $item->cat_id . ':' . $item->category_alias;

            if ($access || in_array($item->access, $authorised))
            {
                // We know that user has the privilege to view the download
                $item->link = '-'; 
            }
            else
            {
                $item->link = JRoute::_('index.php?option=com_users&view=login');
            }
        }
        
	    if ($items){
            $count = count($logs);
            for ($x=0; $x < count($logs); $x++) {
                for ($i=0; $i < count($items); $i++) {
                    if ($items[$i]->file_id == $logs[$x]->log_file_id){
                        $logs[$x]->file_title    = $items[$i]->file_title;
                        $logs[$x]->cat_id        = $items[$i]->cat_id;
                        $logs[$x]->file_id       = $items[$i]->file_id;
                        $logs[$x]->file_pic      = $items[$i]->file_pic;
                        $logs[$x]->release       = $items[$i]->release;
                        $logs[$x]->description   = $items[$i]->description;
                        $logs[$x]->menu_itemid     = $items[$i]->menuf_itemid;
                        $logs[$x]->menu_cat_itemid = $items[$i]->menuc_cat_itemid;
                        $logs[$x]->cat_title     = $items[$i]->category_title;
                        $logs[$x]->cat_dir       = $items[$i]->category_cat_dir;
                        $logs[$x]->cat_dir_parent = $items[$i]->category_cat_dir_parent;
                        $logs[$x]->catslug       = $items[$i]->catslug;
                        $logs[$x]->slug          = $items[$i]->slug;
                        $logs[$x]->link          = $items[$i]->link;
                        $logs[$x]->url_download  = $items[$i]->url_download;
                        $logs[$x]->other_file_id = $items[$i]->other_file_id;
                        $logs[$x]->extern_file   = $items[$i]->extern_file;
                        continue 2;
                    } 
                }  
                
                if (!isset($logs[$x]->file_id)){
                    // download not found or user is not allowed to see it
                    $unset_logs[] = $logs[$x]->id;
                }
            } 
            if (isset($unset_logs)){
                $newlogs = array();
                $sum = 0;
                foreach ($logs as $newlog){
                    if (!in_array($newlog->id, $unset_logs)){
                        $newlogs[] = $newlog;
                        $sum ++;
                        if ($sum == $sum_view) continue;
                    }
                }
                return $newlogs;
            }                       
            return $logs;  
        }		
		return;    
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
    
}
?>