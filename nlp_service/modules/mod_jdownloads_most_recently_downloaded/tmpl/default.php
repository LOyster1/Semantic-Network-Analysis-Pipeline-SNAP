<?php
/**
* @version $Id: mod_jdownloads_most_recently_downloaded.php
* @package mod_jdownloads_most_recently_downloaded
* @copyright (C) 2008/2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*/

/** This Modul shows the Most Recently Downloaded from the component jDownloads. 
*   Support: www.jDownloads.com
*/

// this is a default layout and used tables - you can also create a alternate layout and select it afterwards in the module configuration

defined('_JEXEC') or die;

    JHTML::_('bootstrap.tooltip');
    
    $html = '';
    $html = '<table style="width:100%;" class="moduletable'.$moduleclass_sfx.'">';
    
    $sum_files = count($files);
    if ($sum_view > $sum_files) $sum_view = $sum_files;
    
    if ($files) {
        if ($text_before <> ''){
            $html .= '<tr><td class="td_jd_ldf_before">'.$text_before.'</td></tr>';   
        }
        for ($i=0; $i<$sum_view; $i++) {
            
            $has_no_file = false;
            
            if (!$files[$i]->url_download && !$files[$i]->other_file_id && !$files[$i]->extern_file){
               // only a document without file
               $has_no_file = true;           
            }
            
            $version = $short_version;
            if ($sum_char > 0){
                $gesamt = strlen($files[$i]->file_title) + strlen($files[$i]->release) + strlen($short_version) +1;
                if ($gesamt > $sum_char){
                   $files[$i]->file_title = JString::substr($files[$i]->file_title, 0, $sum_char).$short_char;
                   $files[$i]->release = '';
                }    
            }
            
			if ($cat_show && $files[$i]->cat_id > 1) {
				if ($cat_show_type == 'containing') {
					$cat_show_text2 = $cat_show_text.$files[$i]->cat_title;
				} else {
                    if ($files[$i]->cat_dir_parent){
                        $cat_show_text2 = $cat_show_text.$files[$i]->cat_dir_parent.'/'.$files[$i]->cat_dir;
                    } else {
                        $cat_show_text2 = $cat_show_text.$files[$i]->cat_dir;
                    }
				}
			} else {
                $cat_show_text2 = '';
            }    

            // create the link
            if ($files[$i]->link == '-'){
                // the user have the access to view this item
                if ($detail_view == '1'){
                    if ($detail_view_config == 0){                    
                        // the details view is deactivated in jD config so the
                        // link must start directly the download process
                        if ($direct_download_config == 1){
                            if (!$has_no_file){
                                $link = JRoute::_('index.php?option='.$option.'&amp;task=download.send&amp;id='.$files[$i]->slug.'&amp;catid='.$files[$i]->cat_id.'&amp;m=0');                    
                            } else {
                                // create a link to the Downloads category as this download has not a file
                                if ($files[$i]->menu_cat_itemid){
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menu_cat_itemid);
                                } else {
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                                }
                            }   
                        } else {
                            // link to the summary page
                            if (!$has_no_file){
                                $link = JRoute::_('index.php?option='.$option.'&amp;view=summary&amp;id='.$files[$i]->slug.'&amp;catid='.$files[$i]->cat_id);
                            } else {
                                // create a link to the Downloads category as this download has not a file
                                if ($files[$i]->menu_cat_itemid){
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menu_cat_itemid);
                                } else {
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                                }
                            }   
                        }    
                    } else {
                        // create a link to the details view
                        if ($files[$i]->menu_itemid){
                            $link = JRoute::_('index.php?option='.$option.'&amp;view=download&id='.$files[$i]->slug.'&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menu_itemid);                    
                        } else {
                            $link = JRoute::_('index.php?option='.$option.'&amp;view=download&id='.$files[$i]->slug.'&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);                    
                        }
                    }                       
                } else {    
                    // create a link to the Downloads category
                    if ($files[$i]->menu_cat_itemid){
                        $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menu_cat_itemid);
                    } else {
                        $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                    }
                }    
            } else {
                $link = $files[$i]->link;
            }            
            
            if (!$files[$i]->release) $version = '';
            
            // build icon
            $size = 0;
            $files_pic = '';
            $number = '';
            if ($view_pics){
                $size = (int)$view_pics_size;
                $files_pic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" style="text-align=top;border=0;" width="'.$size.'" height="'.$size.'" alt="" /> '; 
            }
            // build number list
            if ($view_numerical_list){
                $num = $i+1;
                $number = "$num. ";
            }
            
            if ($view_tooltip && $files[$i]->description){
                $sum_char_desc = strlen($files[$i]->description);
                if ($sum_char_desc > $view_tooltip_length){
                    $files[$i]->description = substr($files[$i]->description,0,$view_tooltip_length).$short_char;
                }    
                $link_text = '<a href="'.$link.'">'.JHTML::tooltip(strip_tags($files[$i]->description),JText::_('MOD_JDOWNLOADS_MOST_RECENTLY_DOWNLOADED_DESCRIPTION_TITLE'),$files[$i]->file_title.' '.$version.$files[$i]->release,$files[$i]->file_title.' '.$version.$files[$i]->release).'</a>';                
            } else {    
                $link_text = '<a href="'.$link.'">'.$files[$i]->file_title.' '.$version.$files[$i]->release.'</a>';
            }    
            $html .= '<tr style="vertical-align:top;"><td style="text-align='.$alignment.';">'.$number.$files_pic.$link_text.'</td>';
            
            if ($view_date) {
                    if ($view_date_text) $view_date_text .= '&nbsp;';
                    if ($view_date_same_line){
                        if ($view_user){
                            $html .= '<td style="text-align:'.$date_alignment.'" class="td_jd_ldf_date_row">'.$view_date_text.JHTML::Date($files[$i]->log_datetime,$date_format,false).$view_user_by.' '.$files[$i]->username.'</td>';
                        } else {
                            $html .= '<td style="text-align:'.$date_alignment.'" class="td_jd_ldf_date_row">'.$view_date_text.JHTML::Date($files[$i]->log_datetime,$date_format,false).'</td>';
                        }    
                    } else {
                        if ($view_user){
                            $html .= '</tr><tr><td style="text-align:'.$date_alignment.'" class="td_jd_ldf_date_row">'.$view_date_text.JHTML::Date($files[$i]->log_datetime,$date_format,false).$view_user_by.' '.$files[$i]->username.'</td>';
                        } else {
                            $html .= '</tr><tr><td style="text-align:'.$date_alignment.'" class="td_jd_ldf_date_row">'.$view_date_text.JHTML::Date($files[$i]->log_datetime,$date_format,false).'</td>';
                        }    
                    }    
            } else {
                if ($view_user){
                    $html .= '</tr><tr><td style="text-align:'.$date_alignment.'" class="td_jd_ldf_date_row">'.$view_user_by.' '.$files[$i]->username.'</td>';
                }
            }    
            $html .= '</tr>'; 
            
            // add category info 
            if ($cat_show_text2) {
                if ($cat_show_as_link){
                    if ($files[$i]->menu_cat_itemid){
                        $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menu_cat_itemid.'">'.$cat_show_text2.'</a></td></tr>';
                    } else {
                        $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid.'">'.$cat_show_text2.'</a></td></tr>';
                    }    
                } else {    
                    $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';">'.$cat_show_text2.'</td></tr>';
                }                
            }
        
        }
        if ($text_after <> ''){
            $html .= '<tr><td class="td_jd_ldf_after">'.$text_after.'</td></tr>';
        }
    }
    
    echo $html.'</table>';
?>		