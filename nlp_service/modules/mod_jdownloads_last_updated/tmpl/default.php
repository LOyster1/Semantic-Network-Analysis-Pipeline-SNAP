<?php
/**
* @version $Id: mod_jdownloads_top.php
* @package mod_jdownloads_top
* @copyright (C) 2015 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*/

defined('_JEXEC') or die;

    JHTML::_('bootstrap.tooltip');

    $html = '';
    
    if ($files) {
		$html = '<table style="width:100%;" class="moduletable'.$moduleclass_sfx.'">';
		
        if ($text_before <> ''){
			$html .= '<tr><td>'.$text_before.'</td></tr>';   
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
                $files_pic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" style="text-align:top:border:0px;" width="'.$size.'" height="'.$size.'" alt="" /> '; 
            }
            
			// build number list
            if ($view_numerical_list){
                $num = $i+1;
                $number = "$num. ";
            }
            
            // add description in tooltip 
			if ($view_tooltip && $files[$i]->description != ''){
                $link_text = '<a href="'.$link.'">'.JHTML::tooltip(strip_tags(substr($files[$i]->description,0,$view_tooltip_length)).$short_char,JText::_('MOD_JDOWNLOADS_LAST_UPDATED_DESCRIPTION_TITLE'),$files[$i]->file_title.' '.$version.$files[$i]->release,$files[$i]->file_title.' '.$version.$files[$i]->release).'</a>';                
            } else {    
                $link_text = '<a href="'.$link.'">'.$files[$i]->file_title.' '.$version.$files[$i]->release.'</a>';
            }    
            $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';">'.$number.$files_pic.$link_text.'</td>';
            
            // add the modified date
			if ($view_date) {
                if ($files[$i]->modified_date){
                    if ($view_date_same_line){
                        $html .= '<td style="text-align:'.$date_alignment.';"><small>'.$view_date_text.'&nbsp;'.substr(JHTML::Date($files[$i]->modified_date,$date_format),0,10).'</small></td>';
                    } else {
                        $html .= '</tr><tr><td style="text-align:'.$date_alignment.';"><small>'.$view_date_text.'&nbsp;'.substr(JHTML::Date($files[$i]->modified_date,$date_format),0,10).'</small></td>';
                    }    
                }
            }
            $html .= '</tr>';
            
            // add the first download screenshot when exists and activated in options
            if ($view_thumbnails){
                if ($first_image){
                    $thumbnail = '<img class="img" src="'.$thumbfolder.$first_image.'" style="text-align:top;padding:5px;border:'.$border.';" width="'.$view_thumbnails_size.'" height="'.$view_thumbnails_size.'" alt="'.$files[$i]->file_title.'" />';
                } else {
                    // use placeholder
                    if ($view_thumbnails_dummy){
                        $thumbnail = '<img class="img" src="'.$thumbfolder.'no_pic.gif" style="text-align:top;padding:5px;border:'.$border.';" width="'.$view_thumbnails_size.'" height="'.$view_thumbnails_size.'" alt="" />';    
                    }
                }
                if ($thumbnail) $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';">'.$thumbnail.'</td></tr>';
            } 
			
			// add category info 
            if ($cat_show_text2) {
				if ($cat_show_as_link){
                    if ($files[$i]->menuc_cat_itemid){
                        $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$files[$i]->menuc_cat_itemid.'">'.$cat_show_text2.'</a></td></tr>';
				    } else {
                        $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';"><a href="index.php?option='.$option.'&amp;view=category&catid='.$files[$i]->cat_id.'&amp;Itemid='.$Itemid.'">'.$cat_show_text2.'</a></td></tr>';
                    }    
                } else {    
				    $html .= '<tr style="vertical-align:top;"><td style="text-align:'.$alignment.';font-size:'.$cat_show_text_size.'; color:'.$cat_show_text_color.';">'.$cat_show_text2.'</td></tr>';
                }
			}    
		}
		if ($text_after <> ''){
			$html .= '<tr><td>'.$text_after.'</td></tr>';
		}
        echo $html.'</table>';
    }
    
?>