<?php
/**
 * @package jDownloads
 * @version 2.5  
 * @copyright (C) 2007 - 2013 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */


 defined('_JEXEC') or die('Restricted access');

    global $jlistConfig;

    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

    $db         = JFactory::getDBO(); 
    $document   = JFactory::getDocument();
    $jinput     = JFactory::getApplication()->input;
    $app        = JFactory::getApplication();    
    $user       = JFactory::getUser();
    
    // get jD user limits and settings
    $jd_user_settings = JDHelper::getUserRules();
    
    // for Tabs
    jimport('joomla.html.pane');
    // for Tooltip
    JHtml::_('behavior.tooltip');
    
    // Create shortcuts to some parameters.
    $params    = $this->params;
    $cats      = $this->items;
    
    $html           = '';
    $body           = '';
    $footer_text    = '';
    $layout         = '';
    $is_admin       = false;

    if (JDHelper::checkGroup('8', true) || JDHelper::checkGroup('7', true)){
        $is_admin = true;
    }

    // Get the needed layout data - type = 1 for a 'categories' layout            
    $layout = JDHelper::getLayout(1, false);
    if ($layout){
        $layout_cat_text = $layout->template_text;
        $cats_before     = $layout->template_before_text;
        $cats_after      = $layout->template_after_text;
        $header          = $layout->template_header_text;
        $subheader       = $layout->template_subheader_text;
        $footer          = $layout->template_footer_text;
    } else {
        // We have not a valid layout data
        echo '<big>No valid layout found for Categories!</big>';
    }
    
    $total_cats  = count($cats);
    
    // get current category menu ID when exist and all needed menu IDs for the header links
    $menuItemids = JDHelper::getMenuItemids(0);
    
    // get all other menu category IDs so we can use it when we needs it
    $cat_link_itemids = JDHelper::getAllJDCategoryMenuIDs();
    
    // "Home" menu link itemid
    $root_itemid =  $menuItemids['root'];

       
    $html = '<div class="jd-item-page'.$this->pageclass_sfx.'">';
    
    if ($this->params->get('show_page_heading')) {
        $html .= '<h1>'.$this->escape($this->params->get('page_heading')).'</h1>';
    }    
     
    // ==========================================
    // HEADER SECTION
    // ==========================================

    if ($header != ''){
        
       
        // component title - not more used. So we must replace the placeholder from layout with spaces!
        $header = str_replace('{component_title}', '', $header);
        
        // replace google adsense placeholder with script when active (also for header tab)
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                $header = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $header);
        } else {
                $header = str_replace( '{google_adsense}', '', $header);
        }        
        
        // components description
        if ($jlistConfig['downloads.titletext'] != '') {
            $header_text = stripslashes(JDHelper::getOnlyLanguageSubstring($jlistConfig['downloads.titletext']));
            if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                $header_text = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $header_text);
            } else {
                $header_text = str_replace( '{google_adsense}', '', $header_text);
            }   
            $header .= $header_text;
        }
        
        // check $Itemid exist
        if (!isset($menuItemids['search'])) $menuItemids['search'] = $menuItemids['root'];
        if (!isset($menuItemids['upload'])) $menuItemids['upload'] = $menuItemids['root'];
        
        // build home link        
        $home_link = '<a href="'.JRoute::_('index.php?option=com_jdownloads&amp;Itemid='.$menuItemids['root']).'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/home_fe.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_HOME_LINKTEXT').'" /></a> <a href="'.JRoute::_('index.php?option=com_jdownloads&amp;Itemid='.$menuItemids['root']).'">'.JText::_('COM_JDOWNLOADS_HOME_LINKTEXT').'</a>';
        // build search link
        $search_link = '<a href="'.JRoute::_('index.php?option=com_jdownloads&amp;view=search&amp;Itemid='.$menuItemids['search']).'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/search.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_SEARCH_LINKTEXT').'" /></a> <a href="'.JRoute::_('index.php?option=com_jdownloads&amp;view=search&amp;Itemid='.$menuItemids['search'].'').'">'.JText::_('COM_JDOWNLOADS_SEARCH_LINKTEXT').'</a>';
        // build frontend upload link
        $upload_link = '<a href="'.JRoute::_('index.php?option=com_jdownloads&amp;view=form&amp;layout=edit&amp;Itemid='.$menuItemids['upload']).'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upload.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPLOAD_LINKTEXT').'" /></a> <a href="'.JRoute::_('index.php?option=com_jdownloads&amp;view=form&amp;layout=edit&amp;Itemid='.$menuItemids['upload'].'').'">'.JText::_('COM_JDOWNLOADS_UPLOAD_LINKTEXT').'</a>';

        $header = str_replace('{home_link}', $home_link, $header);
        $header = str_replace('{search_link}', $search_link, $header);
        
        if ($jd_user_settings->uploads_view_upload_icon){
            if ($this->view_upload_button){
                $header = str_replace('{upload_link}', $upload_link, $header);
            } else {
                $header = str_replace('{upload_link}', '', $header);
            }    
        } else {
            $header = str_replace('{upload_link}', '', $header);
        }    

        if ($menuItemids['upper'] > 1){   // 1 is 'root'
            // exists a single category menu link for the category a level up? 
            $level_up_cat_itemid = JDHelper::getSingleCategoryMenuID($cat_link_itemids, $menuItemids['upper'], $root_itemid);
            $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=category&amp;catid='.$menuItemids['upper'].'&amp;Itemid='.$level_up_cat_itemid);
            $header = str_replace('{upper_link}', '<a href="'.$upper_link.'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upper.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'" /></a> <a href="'.$upper_link.'">'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'</a>', $header);    
        } else {
            $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=categories&amp;Itemid='.$menuItemids['root']);
            $header = str_replace('{upper_link}', '<a href="'.$upper_link.'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upper.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'" /></a> <a href="'.$upper_link.'">'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'</a>', $header);            
        }
        
        // create category listbox and viewed it when it is activated in configuration
        if ($jlistConfig['show.header.catlist']){

            // get current selected cat id from listbox
            $catlistid = $jinput->get('catid', '0', 'integer');
            
            // get current sort order and direction
            $orderby_pri = $this->params->get('orderby_pri');
            // when empty get the state params
            $listordering = $this->state->get('list.ordering');
            if (!$orderby_pri && !empty($listordering)){            
                $state_ordering = $this->state->get('list.ordering');
                $state_direction = $this->state->get('list.direction');
                if ($state_ordering == 'c.title'){
                    if ($state_direction== 'DESC'){
                        $orderby_pri = 'ralpha';
                    } else {
                        $orderby_pri = 'alpha';
                    }  
                }    
            } 
            $data = JDHelper::buildCategorySelectBox($catlistid, $cat_link_itemids, $root_itemid, $jlistConfig['view.empty.categories'], $orderby_pri );            
            
            // build special selectable URLs for category listbox
            $root_url       = JRoute::_('index.php?option=com_jdownloads&Itemid='.$root_itemid);
            $uncat_url      = JRoute::_('index.php?option=com_jdownloads&view=downloads&type=uncategorised&Itemid='.$root_itemid);
            $allfiles_url   = JRoute::_('index.php?option=com_jdownloads&view=downloads&Itemid='.$root_itemid);
            $topfiles_url   = JRoute::_('index.php?option=com_jdownloads&view=downloads&type=top&Itemid='.$root_itemid);
            $newfiles_url   = JRoute::_('index.php?option=com_jdownloads&view=downloads&type=new&Itemid='.$root_itemid);
            
            
            $listbox = JHtml::_('select.genericlist', $data['options'], 'cat_list', 'class="inputbox" onchange="gocat(\''.$root_url.'\', \''.$uncat_url.'\', \''.$allfiles_url.'\', \''.$topfiles_url.'\',  \''.$newfiles_url.'\'  ,\''.$data['url'].'\')"', 'value', 'text', $data['selected'] ); 
            
            $header = str_replace('{category_listbox}', '<form name="go_cat" id="go_cat" method="post">'.$listbox.'</form>', $header);
        } else {                                                                        
            $header = str_replace('{category_listbox}', '', $header);         
        }
        
        $html .= $header;  

    }

    // ==========================================
    // SUB HEADER SECTION
    // ==========================================

    if ($subheader != ''){

        if ($jlistConfig['view.subheader']) {
            // display number of sub categories only when > 0 
            if ($total_cats == 0){
                $total_subcats_text = '';
            } else {
                $total_subcats_text = JText::_('COM_JDOWNLOADS_NUMBER_OF_CATEGORIES_LABEL').': '.$total_cats;
            }
            
            // display category title
            $subheader = str_replace('{subheader_title}', JText::_('COM_JDOWNLOADS_FRONTEND_SUBTITLE_OVER_CATLIST'), $subheader);            

            // display pagination            
            if ($jlistConfig['option.navigate.top'] && $this->pagination->get('pages.total') > 1 && $this->params->get('show_pagination') != '0' 
                || (!$jlistConfig['option.navigate.top'] && $this->pagination->get('pages.total') > 1 && $this->params->get('show_pagination') == '1') )
            {
                $page_navi_links = $this->pagination->getPagesLinks(); 
                if ($page_navi_links){
                    $page_navi_pages   = $this->pagination->getPagesCounter();
                    $page_navi_counter = $this->pagination->getResultsCounter(); 
                    $page_limit_box    = $this->pagination->getLimitBox();  
                }    
                $subheader = str_replace('{page_navigation}', $page_navi_links, $subheader);
                $subheader = str_replace('{page_navigation_results_counter}', $page_navi_counter, $subheader);
                if ($this->params->get('show_pagination_results') == null || $this->params->get('show_pagination_results') == '1'){
                    $subheader = str_replace('{page_navigation_pages_counter}', $page_navi_pages, $subheader); 
                } else {
                    $subheader = str_replace('{page_navigation_pages_counter}', '', $subheader);                
                }                                   
                $subheader = str_replace('{count_of_sub_categories}', $total_subcats_text, $subheader); 
            } else {
                $subheader = str_replace('{page_navigation}', '', $subheader);
                $subheader = str_replace('{page_navigation_results_counter}', '', $subheader);
                $subheader = str_replace('{page_navigation_pages_counter}', '', $subheader);                
                $subheader = str_replace('{count_of_sub_categories}', $total_subcats_text, $subheader);                
            }

            // remove sort order bar placeholder
            $subheader = str_replace('{sort_order}', '', $subheader);          
                
        }    
        // remove this placeholder when it is used not for this layout
        $subheader = str_replace('{sort_order}', '', $subheader); 
        
        // replace google adsense placeholder with script when active (also for subheader tab)
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                $subheader = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $subheader);
        } else {
                $subheader = str_replace( '{google_adsense}', '', $subheader);
        }         
 
        $html .= $subheader;            
    }
    
    // ==========================================
    // BODY SECTION - VIEW THE CATEGORIES
    // ==========================================

    $html_cat = '';
    $metakey  = '';
    
    if ($total_cats < $this->pagination->limit){
        $amount = $total_cats;
    } else {
        if (($this->pagination->limitstart + $this->pagination->limit) > $total_cats){
            $amount = $total_cats; 
        } else {
            $amount = $this->pagination->limitstart + $this->pagination->limit;
        }
    }    

    if ($layout_cat_text != ''){

        if(!empty($cats)){
            
            $html_cat = $cats_before;

            for ($i=$this->pagination->limitstart; $i<$amount; $i++) {
            
               $html_cat .= $layout_cat_text;
               
               // Exist a single category menu link for this, when we must use it here
               if (!$cats[$i]->menu_itemid){
                    $cats[$i]->menu_itemid = $root_itemid;    
               }     
               $catlink = JRoute::_("index.php?option=com_jdownloads&amp;view=category&amp;catid=".$cats[$i]->id."&amp;Itemid=".$cats[$i]->menu_itemid);

               //  display the categories icon with link
               if ($cats[$i]->pic != '' ) {
                   $catpic = '<a href="'.$catlink.'"><img src="'.JURI::base().'images/jdownloads/catimages/'.$cats[$i]->pic.'" style="text-align:top;border:0px;" width="'.$jlistConfig['cat.pic.size'].'" height="'.$jlistConfig['cat.pic.size.height'].'" alt="'.$cats[$i]->pic.'" /></a> ';
               } else {
                   $catpic = '';
               }
               
               // support for content plugins
               if ($jlistConfig['activate.general.plugin.support'] && $jlistConfig['use.general.plugin.support.only.for.descriptions']) {  
                   $cats[$i]->description = JHtml::_('content.prepare', $cats[$i]->description);
               } 
                                  
                 // more as one column   ********************************************************
                 if ($layout->cols > 1 && strpos($layout_cat_text, '{cat_title1}')){
                    $a = 0;     

                    for ($a=0; $a < $layout->cols; $a++){
                       
                         $x = $a + 1;
                         $x = (string)$x;

                         if ( $i < $amount ){
                            if ($a == 0){
                                $html_cat = str_replace("{cat_title$x}", '<a href="'.$catlink.'">'.$cats[$i]->title.'</a>', $html_cat);
                            } else {
                                $html_cat = str_replace("{cat_title$x}", '<a href="'.$catlink.'">'.$cats[$i]->title.'</a>', $html_cat);
                            }
                             
                            $html_cat = str_replace("{cat_pic$x}", $catpic, $html_cat);

                            if ($this->params->get('show_description')){
                                $html_cat = str_replace("{cat_description$x}", $cats[$i]->description, $html_cat);
                            } else {
                               $html_cat = str_replace("{cat_description$x}", '', $html_cat); 
                            }
                                
                            if (!$cats[$i]->subcatitems){
                                   $html_cat = str_replace("{sum_subcats$x}", '', $html_cat);
                            } else {
                                   $html_cat = str_replace("{sum_subcats$x}", JText::_('COM_JDOWNLOADS_FRONTEND_COUNT_SUBCATS').' '.$cats[$i]->subcatitems, $html_cat);
                            }
                               
                            $html_cat = str_replace("{sum_files_cat$x}", JText::_('COM_JDOWNLOADS_FRONTEND_COUNT_FILES').' '.(int)$cats[$i]->numitems, $html_cat);
                        
                         } else {
                            
                            $html_cat = str_replace("{cat_title$x}", '', $html_cat);
                            $html_cat = str_replace("{cat_pic$x}", '', $html_cat);
                            $html_cat = str_replace("{cat_description$x}", '', $html_cat);
                         }
                         if (($a + 1) < $layout->cols){
                            $i++;

                            if (isset($cats[$i])){
                                // exists a single category menu link for this subcat? 
                                if (!$cats[$i]->menu_itemid){
                                    $cats[$i]->menu_itemid = $root_itemid;    
                                } 
                                                             
                                $catlink = JRoute::_("index.php?option=com_jdownloads&amp;view=category&amp;catid=".$cats[$i]->id."&amp;Itemid=".$cats[$i]->menu_itemid);
                                
                                // Symbol anzeigen - auch als url                                                                                                                    
                               if ($cats[$i]->pic != '' ) {
                                   $catpic = '<a href="'.$catlink.'"><img src="'.JURI::base().'images/jdownloads/catimages/'.$cats[$i]->pic.'" style="text-align:top;border:0px;" width="'.$jlistConfig['cat.pic.size'].'" height="'.$jlistConfig['cat.pic.size.height'].'" alt="'.$cats[$i]->pic.'" /></a> ';
                               } else {
                                   $catpic = '';
                               }
                            }
                         }  
                    }
                    
                    for ($b=1; $b < 10; $b++){
                        $x = (string)$b;
                        $html_cat = str_replace("{cat_title$x}", '', $html_cat);
                        $html_cat = str_replace("{cat_pic$x}", '', $html_cat);
                        $html_cat = str_replace("{sum_files_cat$x}", '', $html_cat); 
                        $html_cat = str_replace("{sum_subcats$x}", '', $html_cat); 
                    }
                 
                 } else {
                     // only single column layout
                     $html_cat = str_replace('{cat_title}', '<a href="'.$catlink.'">'.$cats[$i]->title.'</a>', $html_cat);
                     
                     if (!$cats[$i]->subcatitems){
                        $html_cat = str_replace('{sum_subcats}','', $html_cat);
                     } else {
                        $html_cat = str_replace('{sum_subcats}', JText::_('COM_JDOWNLOADS_FRONTEND_COUNT_SUBCATS').' '.$cats[$i]->subcatitems, $html_cat);
                     }
                     $html_cat = str_replace('{sum_files_cat}', JText::_('COM_JDOWNLOADS_FRONTEND_COUNT_FILES').' '.(int)$cats[$i]->numitems, $html_cat);
                 }
                   
                 if ($this->params->get('show_description') && isset($cats[$i])){
                    $html_cat = str_replace('{cat_description}',$cats[$i]->description, $html_cat);
                 } else {
                    $html_cat = str_replace('{cat_description}', '', $html_cat); 
                 }    
                 
                 // tags creation
                 if ($this->params->get('show_cat_tags', 1) && !empty($cats[$i]->tags->itemTags)){
                    $cats[$i]->tagLayout = new JLayoutFile('joomla.content.tags'); 
                    $html_cat = str_replace('{tags}', $cats[$i]->tagLayout->render($cats[$i]->tags->itemTags), $html_cat); 
                    $html_cat = str_replace('{tags_title}', JText::_('COM_JDOWNLOADS_TAGS_LABEL'), $html_cat); 
                 } else {
                    $html_cat = str_replace('{tags}', '', $html_cat);
                    $html_cat = str_replace('{tags_title}', '', $html_cat); 
                 }                 
                 
                 $html_cat = str_replace('{cat_pic}', $catpic, $html_cat);
                 $html_cat = str_replace('{cat_info_begin}', '', $html_cat); 
                 $html_cat = str_replace('{cat_info_end}', '', $html_cat);

                 if ($pos_end = strpos($html_cat, '{cat_title_end}')){
                     $pos_beg = strpos($html_cat, '{cat_title_begin}');
                     $html_cat = substr_replace($html_cat, '', $pos_beg, ($pos_end - $pos_beg) + 15);
                 } 
                     
               $html_cat = str_replace('{files}', "", $html_cat);
               $html_cat = str_replace('{checkbox_top}', "", $html_cat);
               $html_cat = str_replace('{form_button}', "", $html_cat);
               $html_cat = str_replace('{form_hidden}', "", $html_cat);
               $html_cat = str_replace('{cat_info_end}', "", $html_cat);
               $html_cat = str_replace('{cat_info_begin}', "", $html_cat);
               
               // google adsense
               if ($jlistConfig['google.adsense.active']){
                  $html_cat = str_replace('{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $html_cat);
               } else {
                  $html_cat = str_replace('{google_adsense}', '', $html_cat);
               } 
               
               // add metakey infos
               if (isset($cats[$i]) && $cats[$i]->metakey){
                    $metakey = $metakey.' '.$cats[$i]->metakey; 
               }
            }
                        
            $jmeta = $document->getMetaData( 'keywords' );
            if (!$metakey){
                $document->setMetaData( 'keywords' , $jmeta);
            } else {
                $document->setMetaData( 'keywords' , strip_tags($metakey));
            }
            
        }
        
        $html .= $html_cat;   
    }

    // ==========================================
    // FOOTER SECTION  
    // ==========================================

    // display pagination            
    if ($jlistConfig['option.navigate.bottom'] && $this->pagination->get('pages.total') > 1 && $this->params->get('show_pagination') != '0' 
        || (!$jlistConfig['option.navigate.bottom'] && $this->pagination->get('pages.total') > 1 && $this->params->get('show_pagination') == '1') )
    {        
        $page_navi_links = $this->pagination->getPagesLinks(); 
        if ($page_navi_links){
            $page_navi_pages   = $this->pagination->getPagesCounter();
            $page_navi_counter = $this->pagination->getResultsCounter(); 
            $page_limit_box    = $this->pagination->getLimitBox();  
        }    
        $footer = str_replace('{page_navigation}', $page_navi_links, $footer);
        $footer = str_replace('{page_navigation_results_counter}', $page_navi_counter, $footer);
        
        if ($this->params->get('show_pagination_results') == null || $this->params->get('show_pagination_results') == '1'){
            $footer = str_replace('{page_navigation_pages_counter}', $page_navi_pages, $footer); 
        } else {
            $footer = str_replace('{page_navigation_pages_counter}', '', $footer);                
        }                                   
    } else {
        $footer = str_replace('{page_navigation}', '', $footer);
        $footer = str_replace('{page_navigation_results_counter}', '', $footer);
        $footer = str_replace('{page_navigation_pages_counter}', '', $footer);                
    }

    // components footer text
    if ($jlistConfig['downloads.footer.text'] != '') {
        $footer_text = stripslashes(JDHelper::getOnlyLanguageSubstring($jlistConfig['downloads.footer.text']));
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
            $footer_text = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $footer_text);
        } else {
            $footer_text = str_replace( '{google_adsense}', '', $footer_text);
        }   
        $html .= $footer_text;
    }
    
    // back button
    if ($jlistConfig['view.back.button']){
        $footer = str_replace('{back_link}', '<a href="javascript:history.go(-1)">'.JText::_('COM_JDOWNLOADS_FRONTEND_BACK_BUTTON').'</a>', $footer); 
    } else {
        $footer = str_replace('{back_link}', '', $footer);
    }    
    
    $footer .= JDHelper::checkCom();
   
    $html .= $footer; 
    
    $html .= '</div>';
    
    // remove empty html tags
    if ($jlistConfig['remove.empty.tags']){
        $html = JDHelper::removeEmptyTags($html);
    }    
    
    // ==========================================
    // VIEW THE BUILDED OUTPUT
    // ==========================================

    if ( !$jlistConfig['offline'] ) {
         if (isset($this->item->event->beforeDisplayContent)){   
            echo $this->item->event->beforeDisplayContent;
         }   
         echo $html;
         if (isset($this->item->event->afterDisplayContent)){   
            echo $this->item->event->afterDisplayContent;
         }         
    } else {
        // admins can view it always
        if ($is_admin) {
            echo $html;     
        } else {
            // build the offline message
            $html = '';
            // offline message
            if ($jlistConfig['offline.text'] != '') {
                $html .= JDHelper::getOnlyLanguageSubstring($jlistConfig['offline.text']);
            }
            echo $html;    
        }
    }     

?>