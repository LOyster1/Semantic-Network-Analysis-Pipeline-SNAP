<?php
/**
* @version $Id: mod_jdownloads_latest.php
* @package mod_jdownloads_latest
* @copyright (C) 2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*/

// this is an alternate layout without tables - it used only <div> tags

defined('_JEXEC') or die;

    JHTML::_('bootstrap.tooltip');

    $html = '';
        
    if ($files){
        $html = '<div class="moduletable'.$moduleclass_sfx.'" style="padding: 5px;">';

		if ($text_before <> ''){
			$html .= '<div style="padding-bottom: 5px;">'.$text_before.'</div>';   
		}
		
        for ($i=0; $i<count($files); $i++) {
            $has_no_file = false;
            if (!$files[$i]->url_download && !$files[$i]->other_file_id && !$files[$i]->extern_file){
               // only a document without file
               $has_no_file = true;           
            }
                         
            // get the first image as thumbnail when it exist           
            $thumbnail = ''; 
            $first_image = '';
            $images = explode("|",$files[$i]->images);
            if (isset($images[0])) $first_image = $images['0'];

            $version = $params->get('short_version', ''); 

            // short the file title?			
            if ($sum_char > 0){
				$gesamt = strlen($files[$i]->file_title) + strlen($files[$i]->release) + strlen($short_version) +1;
				if ($gesamt > $sum_char){
				   $files[$i]->file_title = JString::substr($files[$i]->file_title, 0, $sum_char).$short_char;
				   $files[$i]->release = '';
				}    
			} 
			
            // create the viewed category text   			
            if ($cat_show && $files[$i]->cat_id > 1) {
                if ($cat_show_type == 'containing') {
                    $cat_show_text2 = $cat_show_text.$files[$i]->category_title;
                } else {
                    if ($files[$i]->category_cat_dir_parent){
                        $cat_show_text2 = $cat_show_text.$files[$i]->category_cat_dir_parent.'/'.$files[$i]->category_cat_dir;
                    } else {
                        $cat_show_text2 = $cat_show_text.$files[$i]->category_cat_dir;
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
                                if ($files[$i]->menuc_cat_itemid){
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menuc_cat_itemid);
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
                                if ($files[$i]->menuc_cat_itemid){
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menuc_cat_itemid);
                                } else {
                                    $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                                }
                            }   
                        }    
                    } else {
                        // create a link to the details view
                        if ($files[$i]->menuf_itemid){
                            $link = JRoute::_('index.php?option='.$option.'&amp;view=download&id='.$files[$i]->slug.'&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menuf_itemid);                    
                        } else {
                            $link = JRoute::_('index.php?option='.$option.'&amp;view=download&id='.$files[$i]->slug.'&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);                    
                        }
                    }                       
                } else {    
                    // create a link to the Downloads category
                    if ($files[$i]->menuc_cat_itemid){
                        $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menuc_cat_itemid);
                    } else {
                        $link = JRoute::_('index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid);
                    }
                }    
            } else {
                $link = $files[$i]->link;
            }
            
            if (!$files[$i]->release) $version = '';
			
			// add mime file pic
            $size = 0;
			$files_pic = '';
			$number = '';
			if ($view_pics){
				$size = (int)$view_pics_size;
				$files_pic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" width="'.$size.'" height="'.$size.'" style="border: 0px; vertical-align: top;"'.' alt="" /> ';
 
			}
			if ($view_numerical_list){
				$num = $i+1;
				$number = "$num. ";
			}    
			
            // add description in tooltip
            if ($view_tooltip && $files[$i]->description != ''){
				$link_text = '<a href="'.$link.'">'.JHTML::tooltip(strip_tags(substr($files[$i]->description,0,$view_tooltip_length)).$short_char,JText::_('MOD_JDOWNLOADS_LATEST_DESCRIPTION_TITLE'),$files[$i]->file_title.' '.$version.$files[$i]->release,$files[$i]->file_title.' '.$version.$files[$i]->release).'</a>';                
			} else {    
				$link_text = '<a href="'.$link.'">'.$files[$i]->file_title.' '.$version.$files[$i]->release.'</a>';
			}    
			$html .= '<div style="padding-bottom: 3px; text-align: '.$alignment.';">'.$number.$files_pic.$link_text.'</div>';
            
            // add the creation date  
            if ($view_date) {
                if ($files[$i]->date_added){
                    if ($view_date_same_line){
                        $html .= '<div style="padding-bottom: 3px; float:'.$date_alignment.';"><small>'.JHTML::date($files[$i]->date_added, $date_format).'</small></div>'; 
                    } else {
                        $html .= '<div style="padding-bottom: 3px; text-align:'.$date_alignment.';"><small>'.JHTML::date($files[$i]->date_added, $date_format).'</small></div>';
                    }
                }    
            } 

            // add the first download screenshot when exists and activated in options
            if ($view_thumbnails){
                if ($first_image){
                    $thumbnail = '<img class="img" src="'.$thumbfolder.$first_image.'" style="padding:5px;border:'.$border.'" width="'.$view_thumbnails_size.'" height="'.$view_thumbnails_size.'" alt="'.$files[$i]->file_title.'" />';
                } else {
                    if ($view_thumbnails_dummy){
                        $thumbnail = '<img class="img" src="'.$thumbfolder.'no_pic.gif" style="padding:5px;border:'.$border.'" width="'.$view_thumbnails_size.'" height="'.$view_thumbnails_size.'" alt="" />';
                    }    
                }
                if ($thumbnail) $html .= '<div style="padding-bottom: 3px; text-align:'.$alignment.'">'.$thumbnail.'</div>';
            }
			
			
			// add category info
			if ($cat_show_text2) {
                if ($cat_show_as_link){
                    if ($files[$i]->menuc_cat_itemid){
                        $html .= '<div style="padding-bottom: 3px; text-align:'.$alignment.'; font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menuc_cat_itemid.'">'.$cat_show_text2.'</a></div>';
                    } else {
                        $html .= '<div style="padding-bottom: 3px; text-align:'.$alignment.'; font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid.'">'.$cat_show_text2.'</a></div>';
                    }
                } else {    
                    $html .= '<div style="padding-bottom: 3px; text-align:'.$alignment.'; font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';">'.$cat_show_text2.'</div>';
                }
            }    
		}
		if ($text_after <> ''){
			$html .= '<div style="padding-top: 5px;">'.$text_after.'</div>';
		}
        echo $html.'</div>';
    }
?>