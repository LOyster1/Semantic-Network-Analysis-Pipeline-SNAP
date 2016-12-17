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

 setlocale(LC_ALL, 'C.UTF-8', 'C');
 
    global $jlistConfig;

    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

    $db         = JFactory::getDBO(); 
    $document   = JFactory::getDocument();
    $jinput     = JFactory::getApplication()->input;
    $app        = JFactory::getApplication();    
    $user       = JFactory::getUser();
    
    $jdownloads_root_dir_name = basename($jlistConfig['files.uploaddir']);
    $checkbox_top_always_added = false;
    
    // get jD user limits and settings
    $jd_user_settings = JDHelper::getUserRules();
    
    // for Tabs
    jimport('joomla.html.pane');
    // for Tooltip
    JHtml::_('behavior.tooltip');
    
    $listOrder = str_replace('a.', '', $this->escape($this->state->get('list.ordering')));    
    $listDirn  = $this->escape($this->state->get('list.direction'));    
    
    // Create shortcuts to some parameters.
    $params     = $this->params;
    $files      = $this->items;

    $html           = '';
    $body           = '';
    $footer_text    = '';
    $layout         = '';
    $is_admin       = false;
    
    $date_format = JDHelper::getDateFormat();

    $layout_has_checkbox = false;
    $layout_has_download = false;

    if (JDHelper::checkGroup('8', true) || JDHelper::checkGroup('7', true)){
        $is_admin = true;
    }
    
    // Get the needed layout data - type = 2 for a 'files' layout            
    $layout_files = JDHelper::getLayout(2, false);
    if ($layout_files){
        $layout_files_text        = $layout_files->template_text;
        $header                   = $layout_files->template_header_text;
        $subheader                = $layout_files->template_subheader_text;
        $footer                   = $layout_files->template_footer_text;
    } else {
        // We have not a valid layout data
        echo '<big>No valid layout found for files!</big>';
    }    
    
    
    if ($layout_files->symbol_off == 0 ) {
        $use_mini_icons = true;
    } else {
        $use_mini_icons = false; 
    }
    
    // we may not use in this listing checkboxes for mass downloads, since we have not a category layout with the required checkbox placeholders
    // so will view this listing always with download links
    // deactivate at first the setting when it is used - it is not used, we does nothing
    if ($layout_files->checkbox_off == 0){
        $layout_files->checkbox_off = 1;
        $layout_has_checkbox = true;
        // find out whether we have checkboxes AND download placeholders
        if (strpos($layout_files->template_text, '{url_download}')){
            // we have a layout also with download olaceholder 
            $layout_has_download = true;
        }       
    } else {
        if (strpos($layout_files->template_text, '{url_download}')){
            // we have a layout also with download olaceholder 
            $layout_has_download = true;
        }  
    }              
    
    // alternate CSS buttons when selected in configuration
    $status_color_hot       = $jlistConfig['css.button.color.hot'];
    $status_color_new       = $jlistConfig['css.button.color.new'];
    $status_color_updated   = $jlistConfig['css.button.color.updated'];
    $download_color         = $jlistConfig['css.button.color.download'];
    $download_size          = $jlistConfig['css.button.size.download'];
    $download_size_mirror   = $jlistConfig['css.button.size.download.mirror'];        
    $download_color_mirror1 = $jlistConfig['css.button.color.mirror1'];        
    $download_color_mirror2 = $jlistConfig['css.button.color.mirror2'];
    $download_size_listings = $jlistConfig['css.button.size.download.small'];     
    
    $total_downloads  = $this->pagination->get('total');
    
    // get current category menu ID when exist and all needed menu IDs for the header links
    $menuItemids = JDHelper::getMenuItemids();
    
    // get all other menu category IDs so we can use it when we needs it
    $cat_link_itemids = JDHelper::getAllJDCategoryMenuIDs();
    
    // "Home" menu link itemid
    $root_itemid =  $menuItemids['root'];

    // make sure, that we have a valid menu itemid (we have not a category here)
    $category_menu_itemid = $root_itemid;
        
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

        if ($menuItemids['upper'] > 1 && $menuItemids['upper'] != $menuItemids['base']){   // 1 is 'root'
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
            if ($this->state->get('only_uncategorised')){
                $catlistid = 1;
            } else {
                $catlistid = -1;
            }
           
            // get current sort order and direction
            $orderby_pri = $this->params->get('orderby_pri');
            if (!$orderby_pri){
                $orderby_pri = $this->state->get('parameters.menu[orderby_pri]');
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
            if ($total_downloads == 0){
                $total_files_text = '';
            } else {
                $total_files_text = JText::_('COM_JDOWNLOADS_NUMBER_OF_DOWNLOADS_LABEL').': '.$total_downloads;
            }
            
            // display category title
            if ($this->state->get('only_uncategorised')){
                $subheader = str_replace('{subheader_title}', JText::_('COM_JDOWNLOADS_FRONTEND_SUBTITLE_OVER_UNCATEGORISED'), $subheader);
            } else {    
                $subheader = str_replace('{subheader_title}', JText::_('COM_JDOWNLOADS_FRONTEND_SUBTITLE_OVER_ALL_DOWNLOADS'), $subheader);
            }    
            
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
                $subheader = str_replace('{count_of_sub_categories}', $total_files_text, $subheader); 
            } else {
                $subheader = str_replace('{page_navigation}', '', $subheader);
                $subheader = str_replace('{page_navigation_results_counter}', '', $subheader);
                $subheader = str_replace('{page_navigation_pages_counter}', '', $subheader);                
                $subheader = str_replace('{count_of_sub_categories}', $total_files_text, $subheader);                
            }

            // display sort order bar
            if ($jlistConfig['view.sort.order'] && $total_downloads > 1 && $this->params->get('show_sort_order_bar') != '0'
            || (!$jlistConfig['view.sort.order'] && $this->pagination->get('pages.total') > 1 && $this->params->get('show_sort_order_bar') == '1') )
            {
               $limitstart = $this->pagination->limitstart;
               
               // create form
               $sort_form = '<form action="'.htmlspecialchars(JFactory::getURI()->toString()).'" method="post" name="adminForm" id="adminForm">';
               $sort_form_hidden = '<input type="hidden" name="filter_order" value="" />
                                   <input type="hidden" name="filter_order_Dir" value="" />
                                   <input type="hidden" name="limitstart" value="" /></form>';
                              
               $ordering = '<span class="jd-list-ordering" id="ordering1">'.JHtml::_('grid.sort', JText::_('COM_JDOWNLOADS_FE_SORT_ORDER_DEFAULT'), 'ordering', $listDirn, $listOrder).' | </span>';
               $title    = '<span class="jd-list-title" id="ordering2">'.JHtml::_('grid.sort', JText::_('COM_JDOWNLOADS_FE_SORT_ORDER_NAME'), 'file_title', $listDirn, $listOrder).' | </span>';
               $author   = '<span class="jd-list-author" id="ordering3">'.JHtml::_('grid.sort', JText::_('COM_JDOWNLOADS_FE_SORT_ORDER_AUTHOR'), 'author', $listDirn, $listOrder).' | </span>';               
               $date     = '<span class="jd-list-date" id="ordering4">'.JHtml::_('grid.sort', JText::_('COM_JDOWNLOADS_FE_SORT_ORDER_DATE'), 'date_added', $listDirn, $listOrder).' | </span>';
               $hits     = '<span class="jd-list-hits" id="ordering5">'.JHtml::_('grid.sort', JText::_('COM_JDOWNLOADS_FE_SORT_ORDER_HITS'), 'downloads', $listDirn, $listOrder).' </span>';               
                
               $listOrder_bar = $sort_form
                                .JText::_('COM_JDOWNLOADS_FE_SORT_ORDER_TITLE').' '
                                .'<br />'
                                .$ordering
                                .$title
                                .$author
                                .$date
                                .$hits
                                .$sort_form_hidden;
                                
               $subheader = str_replace('{sort_order}', $listOrder_bar, $subheader);
            } else {   
               $subheader = str_replace('{sort_order}', '', $subheader);          
            }    
        }    
        // remove this placeholder when it is used not for files layout
        $subheader = str_replace('{sort_order}', '', $subheader); 
        
        // replace google adsense placeholder with script when active (also for subheader tab)
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                $subheader = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $subheader);
        } else {
                $subheader = str_replace( '{google_adsense}', '', $subheader);
        }         
 
        $html .= $subheader;            
    }
    
    $formid = $total_downloads + 1;
    
    // ==========================================
    // BODY SECTION - VIEW THE DOWNLOADS DATA
    // ==========================================
    
    $html_files = '';

    if ($layout_files_text != ''){
        
        // build the mini image symbols when used in layout ( 0 = activated !!! )
        if ($use_mini_icons) {
            $msize =  $jlistConfig['info.icons.size'];
            $pic_date = '<img src="'.JURI::base().'images/jdownloads/miniimages/date.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DATE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DATE').'" />&nbsp;';
            $pic_license = '<img src="'.JURI::base().'images/jdownloads/miniimages/license.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'" />&nbsp;';
            $pic_author = '<img src="'.JURI::base().'images/jdownloads/miniimages/contact.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_AUTHOR').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_AUTHOR').'" />&nbsp;';
            $pic_website = '<img src="'.JURI::base().'images/jdownloads/miniimages/weblink.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_WEBSITE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_WEBSITE').'" />&nbsp;';
            $pic_system = '<img src="'.JURI::base().'images/jdownloads/miniimages/system.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_SYSTEM').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_SYSTEM').'" />&nbsp;';
            $pic_language = '<img src="'.JURI::base().'images/jdownloads/miniimages/language.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LANGUAGE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LANGUAGE').'" />&nbsp;';
            $pic_downloads = '<img src="'.JURI::base().'images/jdownloads/miniimages/download.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD_HITS').'" />&nbsp;';
            $pic_price = '<img src="'.JURI::base().'images/jdownloads/miniimages/currency.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_PRICE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_PRICE').'" />&nbsp;';
            $pic_size = '<img src="'.JURI::base().'images/jdownloads/miniimages/stuff.png" style="text-align:middle;border:0px;" width="'.$msize.'" height="'.$msize.'"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_FILESIZE').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_FILESIZE').'" />&nbsp;';
        } else {
            $pic_date = '';
            $pic_license = '';
            $pic_author = '';
            $pic_website = '';
            $pic_system = '';
            $pic_language = '';
            $pic_downloads = '';
            $pic_price = '';
            $pic_size = '';
        }
        
        // create the pics for: NEW file / HOT file / file is UPDATED
        $hotpic = '<img src="'.JURI::base().'images/jdownloads/hotimages/'.$jlistConfig['picname.is.file.hot'].'" alt="hotpic" />';
        $newpic = '<img src="'.JURI::base().'images/jdownloads/newimages/'.$jlistConfig['picname.is.file.new'].'" alt="newpic" />';
        $updatepic = '<img src="'.JURI::base().'images/jdownloads/updimages/'.$jlistConfig['picname.is.file.updated'].'" alt="updatepic" />';        
        
        // build a little pic for extern links
        $extern_url_pic = '<img src="'.JURI::base().'components/com_jdownloads/assets/images/link_extern.gif" alt="external" />';        

        // ===========================================
        // display now the categories files (downloads)
        for ($i = 0; $i < count($files); $i++) {
            
            // build the categories path for the file
            if ($files[$i]->category_cat_dir_parent){
                $category_dir = $files[$i]->category_cat_dir_parent.'/'.$files[$i]->category_cat_dir;
            } elseif ($files[$i]->category_cat_dir) {
                $category_dir = $files[$i]->category_cat_dir;
            } else {
                // we have an uncategorised download so we must add the defined folder for this
                $category_dir = $jlistConfig['uncategorised.files.folder.name'];
            }            
            
            // When user has access: get data to publish the edit icon and publish data as tooltip
            if ($files[$i]->params->get('access-edit')){
                $editIcon = JDHelper::getEditIcon($files[$i]);
            } else {
                $editIcon = '';
            }            
            
            $has_no_file = false;
            $file_id = $files[$i]->file_id;

            // when we have not a menu item to the singel download, we need a menu item from the assigned category, or at lates the root itemid
            if ($files[$i]->menuf_itemid){
                $file_itemid =  (int)$files[$i]->menuf_itemid;
            } else {
                $file_itemid = $category_menu_itemid;
            }             
            
            if (!$files[$i]->url_download && !$files[$i]->other_file_id && !$files[$i]->extern_file){
               // only a document without file
               $userinfo = JText::_('COM_JDOWNLOADS_FRONTEND_ONLY_DOCUMENT_USER_INFO');
               $has_no_file = true;           
           }
            
            // add the content plugin event
            $event = $files[$i]->event->beforeDisplayContent;
            
            // get the activated/selected "files" layout text to build the output for every download
            $html_file = str_replace('{file_id}',$files[$i]->file_id, $event.$layout_files_text);
            
            // replace 'featured' placeholders
            if ($files[$i]->featured){
                // add the css class
                $html_file = str_replace('{featured_class}', 'jd_featured', $html_file);
                $html_file = str_replace('{featured_detail_class}', 'jd_featured_detail', $html_file);
                // add the pic
                if ($jlistConfig['featured.pic.filename']){
                    $featured_pic = '<img class="jd_featured_star" src="'.JURI::base().'images/jdownloads/featuredimages/'.$jlistConfig['featured.pic.filename'].'" width="'.$jlistConfig['featured.pic.size'].'" height="'.$jlistConfig['featured.pic.size.height'].'" alt="'.$jlistConfig['featured.pic.filename'].'" />';
                    $html_file = str_replace('{featured_pic}', $featured_pic, $html_file);
                } else {
                    $html_file = str_replace('{featured_pic}', '', $html_file);
                }
            } else {
                $html_file = str_replace('{featured_class}', '', $html_file);
                $html_file = str_replace('{featured_detail_class}', '', $html_file);
                $html_file = str_replace('{featured_pic}', '', $html_file);
            }           
            
            // render the tags
            if ($params->get('show_tags', 1) && !empty($files[$i]->tags->itemTags)){ 
                $files[$i]->tagLayout = new JLayoutFile('joomla.content.tags');
                $html_file = str_replace('{tags}', $files[$i]->tagLayout->render($files[$i]->tags->itemTags), $html_file);
                $html_file = str_replace('{tags_title}', JText::_('COM_JDOWNLOADS_TAGS_LABEL'), $html_file);
            } else {
                $html_file = str_replace('{tags}', '', $html_file);
                $html_file = str_replace('{tags_title}', '', $html_file);
            }            
            
            // files title row info only view when it is the first file
            if ($i > 0){
                // remove all html tags in top cat output
                if ($pos_end = strpos($html_file, '{files_title_end}')){
                    $pos_beg = strpos($html_file, '{files_title_begin}');
                    $html_file = substr_replace($html_file, '', $pos_beg, ($pos_end - $pos_beg) + 17);
                }
            } else {
                $html_file = str_replace('{files_title_text}', JText::_('COM_JDOWNLOADS_FE_FILELIST_TITLE_OVER_FILES_LIST'), $html_file);
                $html_file = str_replace('{files_title_end}', '', $html_file);
                $html_file = str_replace('{files_title_begin}', '', $html_file);
            } 
     
            // create file titles
            $html_file = JDHelper::buildFieldTitles($html_file, $files[$i]);
            
            // create category title
            $html_file = str_replace('{category_title}', JText::_('COM_JDOWNLOADS_CATEGORY_LABEL'), $html_file);
            $html_file = str_replace('{category_name}', $files[$i]->category_title, $html_file);            
            
            // create filename
            if ($files[$i]->url_download){
                $html_file = str_replace('{file_name}', JDHelper::getShorterFilename($files[$i]->url_download), $html_file);
            } elseif (isset($files[$i]->filename_from_other_download) && $files[$i]->filename_from_other_download != ''){
                $html_file = str_replace('{file_name}', JDHelper::getShorterFilename($files[$i]->filename_from_other_download), $html_file);
            } else {
                $html_file = str_replace('{file_name}', '', $html_file);
            }             
             
             // google adsense
             if ($jlistConfig['google.adsense.active']){
                 $html_file = str_replace('{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $html_file);
             } else {
                 $html_file = str_replace('{google_adsense}', '', $html_file);
             } 

             // report download link
             if ($jd_user_settings->view_report_form){
                $report_link = '<a href="'.JRoute::_("index.php?option=com_jdownloads&amp;view=report&amp;id=".$files[$i]->slug."&amp;catid=".$files[$i]->cat_id."&amp;Itemid=".$root_itemid).'" rel="nofollow">'.JText::_('COM_JDOWNLOADS_FRONTEND_REPORT_FILE_LINK_TEXT').'</a>';                
                $html_file = str_replace('{report_link}', $report_link, $html_file);
             } else {
                $html_file = str_replace('{report_link}', '', $html_file);
             }
            
             // view sum comments 
             if ($jlistConfig['view.sum.jcomments'] && $jlistConfig['jcomments.active']){
                 // check that comments table exist - get DB prefix string
                 $prefix = $db->getPrefix();
                 // sometimes wrong uppercase prefix result string - so we fix it
                 $prefix2 = strtolower($prefix);
                 $tablelist = $db->getTableList();
                 if (in_array($prefix.'jcomments', $tablelist ) || in_array($prefix2.'jcomments', $tablelist )){
                     $db->setQuery('SELECT COUNT(*) from #__jcomments WHERE object_group = \'com_jdownloads\' AND object_id = '.$files[$i]->file_id);
                     $sum_comments = $db->loadResult();
                     if ($sum_comments >= 0){
                         $comments = sprintf(JText::_('COM_JDOWNLOADS_FRONTEND_JCOMMENTS_VIEW_SUM_TEXT'), $sum_comments); 
                         $html_file = str_replace('{sum_jcomments}', $comments, $html_file);
                     } else {
                        $html_file = str_replace('{sum_jcomments}', '', $html_file);
                     }
                 } else {
                     $html_file = str_replace('{sum_jcomments}', '', $html_file);
                 }    
             } else {   
                 $html_file = str_replace('{sum_jcomments}', '', $html_file);
             }    

            if ($files[$i]->release == '' ) {
                $html_file = str_replace('{release}', '', $html_file);
            } else {
                $html_file = str_replace('{release}', $files[$i]->release, $html_file);
            }

            // display the thumbnails
            $html_file = JDHelper::placeThumbs($html_file, $files[$i]->images, 'list');                                                    

            // support for content plugins in description / here in the files list layout is only used the short description
            if ($jlistConfig['activate.general.plugin.support'] && $jlistConfig['use.general.plugin.support.only.for.descriptions']) {  
                $files[$i]->description = JHtml::_('content.prepare', $files[$i]->description);
            }                

            if ($jlistConfig['auto.file.short.description'] && $jlistConfig['auto.file.short.description.value'] > 0){
                 if (strlen($files[$i]->description) > $jlistConfig['auto.file.short.description.value']){ 
                     $shorted_text=preg_replace("/[^ ]*$/", '..', substr($files[$i]->description, 0, $jlistConfig['auto.file.short.description.value']));
                     $html_file = str_replace('{description}', $shorted_text, $html_file);
                 } else {
                     $html_file = str_replace('{description}', $files[$i]->description, $html_file);
                 }    
            } else {
                 $html_file = str_replace('{description}', $files[$i]->description, $html_file);
            }   

            // compute for HOT symbol            
            if ($jlistConfig['loads.is.file.hot'] > 0 && $files[$i]->downloads >= $jlistConfig['loads.is.file.hot'] ){
                // is the old button pic used?
                if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                    $html_file = str_replace('{pic_is_hot}', $hotpic, $html_file);
                } else {
                    // CSS Button is selected
                    $html_file = str_replace('{pic_is_hot}', '<span class="jdbutton '.$status_color_hot.' jstatus">'.JText::_('COM_JDOWNLOADS_HOT').'</span>', $html_file);
                }    
            } else {    
                $html_file = str_replace('{pic_is_hot}', '', $html_file);
            }

            // compute for NEW symbol
            $days_diff = JDHelper::computeDateDifference(date('Y-m-d H:i:s'), $files[$i]->date_added);
            if ($jlistConfig['days.is.file.new'] > 0 && $days_diff <= $jlistConfig['days.is.file.new']){
                // is the old button used?
                if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){            
                    $html_file = str_replace('{pic_is_new}', $newpic, $html_file);
                } else {
                    // CSS Button is selected
                    $html_file = str_replace('{pic_is_new}', '<span class="jdbutton '.$status_color_new.' jstatus">'.JText::_('COM_JDOWNLOADS_NEW').'</span>', $html_file);
                }    
            } else {    
                $html_file = str_replace('{pic_is_new}', '', $html_file);
            }
            
            // compute for UPDATED symbol
            // view it only when in the download is activated the 'updated' option
            if ($files[$i]->update_active) {
                $days_diff = JDHelper::computeDateDifference(date('Y-m-d H:i:s'), $files[$i]->modified);
                if ($jlistConfig['days.is.file.updated'] > 0 && $days_diff >= 0 && $days_diff <= $jlistConfig['days.is.file.updated']){
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                        $html_file = str_replace('{pic_is_updated}', $updatepic, $html_file);
                    } else {
                        // CSS Button is selected
                        $html_file = str_replace('{pic_is_updated}', '<span class="jdbutton '.$status_color_updated.' jstatus">'.JText::_('COM_JDOWNLOADS_UPDATED').'</span>', $html_file);
                    }    
                } else {    
                    $html_file = str_replace('{pic_is_updated}', '', $html_file);
                }
            } else {
               $html_file = str_replace('{pic_is_updated}', '', $html_file);
            }    
                
            // media player
            if ($files[$i]->preview_filename){
                // we use the preview file when exist  
                $is_preview = true;
                $files[$i]->itemtype = JDHelper::getFileExtension($files[$i]->preview_filename);
                $is_playable    = JDHelper::isPlayable($files[$i]->preview_filename);
                $extern_media = false;
            } else {                  
                $is_preview = false;
                if ($files[$i]->extern_file){
                    $extern_media = true;
                    $files[$i]->itemtype = JDHelper::getFileExtension($files[$i]->extern_file);
                    $is_playable    = JDHelper::isPlayable($files[$i]->extern_file);
                } else {    
                    $files[$i]->itemtype = JDHelper::getFileExtension($files[$i]->url_download);
                    $is_playable    = JDHelper::isPlayable($files[$i]->url_download);
                    $extern_media = false;
                }  
            }            
            
            if ( !$jlistConfig['flowplayer.use'] && !$jlistConfig['html5player.use'] && $files[$i]->itemtype == 'mp3' ){
                // we use only the 'OLD' mp3 player
                if ($extern_media){
                    $mp3_path = $files[$i]->extern_file;
                } else {        
                    if ($is_preview){
                        // we need the path to the "previews" folder
                        $mp3_path = JUri::base().$jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$files[$i]->preview_filename;
                    } else {
                        // we use the normal download file for the player
                        $mp3_path = JUri::base().$jdownloads_root_dir_name.'/'.$category_dir.'/'.$files[$i]->url_download;
                    }   
                }    
                $mp3_config = trim($jlistConfig['mp3.player.config']);
                $mp3_config = str_replace(';', '&amp;', $mp3_config);
                
                $mp3_player =  
                '<object type="application/x-shockwave-flash" data="components/com_jdownloads/assets/mp3_player_maxi.swf" width="200" height="20">
                <param name="movie" value="components/com_jdownloads/assets/mp3_player_maxi.swf" />
                <param name="wmode" value="transparent"/>
                <param name="FlashVars" value="mp3='.$mp3_path.'&amp;'.$mp3_config.'" />
                </object>';   
                
                if (strpos($html_file, '{mp3_player}')){
                    $html_file = str_replace('{mp3_player}', $mp3_player, $html_file);
                    $html_file = str_replace('{preview_player}', '', $html_file);
                } else {                
                    $html_file = str_replace('{preview_player}', $mp3_player, $html_file);
                }            
                
            } 
            
            if ( $is_playable ){
                
               if ($jlistConfig['html5player.use']){
                    // we will use the new HTML5 player option
                    if ($extern_media){
                        $media_path = $files[$i]->extern_file;
                    } else {        
                        if ($is_preview){
                            // we need the relative path to the "previews" folder
                            $media_path = $jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$files[$i]->preview_filename;
                        } else {
                            // we use the normal download file for the player
                            $media_path = $jdownloads_root_dir_name.'/'.$category_dir.'/'.$files[$i]->url_download;
                        }   
                    }    
                            
                    // create the HTML5 player
                    $player = JDHelper::getHTML5Player($files[$i], $media_path);
                    
                    // we use the player for video files only in listings, when the option allowed this
                    if ($jlistConfig['html5player.view.video.only.in.details'] && $files[$i]->itemtype != 'mp3' && $files[$i]->itemtype != 'wav' && $files[$i]->itemtype != 'oga'){
                        $html_file = str_replace('{mp3_player}', '', $html_file);
                        $html_file = str_replace('{preview_player}', '', $html_file);
                    } else {                            
                        if ($files[$i]->itemtype == 'mp4' || $files[$i]->itemtype == 'webm' || $files[$i]->itemtype == 'ogg' || $files[$i]->itemtype == 'ogv' || $files[$i]->itemtype == 'mp3' || $files[$i]->itemtype == 'wav' || $files[$i]->itemtype == 'oga'){
                            // We will replace at first the old placeholder when exist
                            if (strpos($html_file, '{mp3_player}')){
                                $html_file = str_replace('{mp3_player}', $player, $html_file);
                                $html_file = str_replace('{preview_player}', '', $html_file);
                            } else {                
                                $html_file = str_replace('{preview_player}', $player, $html_file);
                            }    
                        } else {
                            $html_file = str_replace('{mp3_player}', '', $html_file);
                            $html_file = str_replace('{preview_player}', '', $html_file);
                        }    
                    } 

               } else {
               
                    if ($jlistConfig['flowplayer.use']){
                        // we will use the new flowplayer option
                        if ($extern_media){
                            $media_path = $files[$i]->extern_file;
                        } else {        
                            if ($is_preview){
                                // we need the relative path to the "previews" folder
                                $media_path = $jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$files[$i]->preview_filename;
                            } else {
                                // we use the normal download file for the player
                                $media_path = $jdownloads_root_dir_name.'/'.$category_dir.'/'.$files[$i]->url_download;
                            }   
                        }    

                        $ipadcode = '';

                        if ($files[$i]->itemtype == 'mp3'){
                            $fullscreen = 'false';
                            $autohide = 'false';
                            $playerheight = (int)$jlistConfig['flowplayer.playerheight.audio'];
                            // we must use also the ipad plugin identifier when required
                            // see http://flowplayer.blacktrash.org/test/ipad-audio.html and http://flash.flowplayer.org/plugins/javascript/ipad.html
                            if ($this->ipad_user){
                               $ipadcode = '.ipad();'; 
                            }                  
                        } else {
                            $fullscreen = 'true';
                            $autohide = 'true';
                            $playerheight = (int)$jlistConfig['flowplayer.playerheight'];
                        }
                        
                        $player = '<a href="'.$media_path.'" style="display:block;width:'.$jlistConfig['flowplayer.playerwidth'].'px;height:'.$playerheight.'px;" class="player" id="player'.$files[$i]->file_id.'"></a>';
                        $player .= '<script language="JavaScript">
                        // install flowplayer into container
                                    flowplayer("player'.$files[$i]->file_id.'", "'.JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer-3.2.16.swf",  
                                     {  
                            plugins: {
                                controls: {
                                    // insert at first the config settings
                                    '.$jlistConfig['flowplayer.control.settings'].'
                                    // and now the basics
                                    fullscreen: '.$fullscreen.',
                                    height: '.(int)$jlistConfig['flowplayer.playerheight.audio'].',
                                    autoHide: '.$autohide.',
                                }
                                
                            },
                            clip: {
                                autoPlay: false,
                                // optional: when playback starts close the first audio playback
                                 onBeforeBegin: function() {
                                    $f("player'.$files[$i]->file_id.'").close();
                                }
                            }
                        })'.$ipadcode.'; </script>';
                        // the 'ipad code' above is only required for ipad/iphone users
                        
                        // we use the player for video files only in listings, when the option allowed this
                        if ($jlistConfig['flowplayer.view.video.only.in.details'] && $files[$i]->itemtype != 'mp3'){ 
                            $html_file = str_replace('{mp3_player}', '', $html_file);
                            $html_file = str_replace('{preview_player}', '', $html_file);            
                        } else {    
                            if ($files[$i]->itemtype == 'mp4' || $files[$i]->itemtype == 'flv' || $files[$i]->itemtype == 'mp3'){    
                                // We will replace at first the old placeholder when exist
                                if (strpos($html_file, '{mp3_player}')){
                                    $html_file = str_replace('{mp3_player}', $player, $html_file);
                                    $html_file = str_replace('{preview_player}', '', $html_file);
                                } else {
                                    $html_file = str_replace('{preview_player}', $player, $html_file);
                                }                                
                            } else {
                                $html_file = str_replace('{mp3_player}', '', $html_file);
                                $html_file = str_replace('{preview_player}', '', $html_file);
                            }
                        }
                    }
                }
            } 
                
            if ($jlistConfig['mp3.view.id3.info'] && $files[$i]->itemtype == 'mp3' && !$extern_media){
               // read mp3 infos
                if ($is_preview){
                    // get the path to the preview file
                    $mp3_path_abs = $jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'].DS.$files[$i]->preview_filename;
                } else {
                    // get the path to the downloads file
                    $mp3_path_abs = $jlistConfig['files.uploaddir'].DS.$category_dir.DS.$files[$i]->url_download;
                }
                $info = JDHelper::getID3v2Tags($mp3_path_abs);
                if ($info){
                    // add it
                    $mp3_info = stripslashes($jlistConfig['mp3.info.layout']);
                    $mp3_info = str_replace('{name_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_TITLE'), $mp3_info);
                    if ($is_preview){
                        $mp3_info = str_replace('{name}', $files[$i]->preview_filename, $mp3_info);
                    } else {
                        $mp3_info = str_replace('{name}', $files[$i]->url_download, $mp3_info);
                    } 
                    $mp3_info = str_replace('{album_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_ALBUM'), $mp3_info);
                    $mp3_info = str_replace('{album}', $info['TALB'], $mp3_info);
                    $mp3_info = str_replace('{artist_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_ARTIST'), $mp3_info);
                    $mp3_info = str_replace('{artist}', $info['TPE1'], $mp3_info);
                    $mp3_info = str_replace('{genre_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_GENRE'), $mp3_info);
                    $mp3_info = str_replace('{genre}', $info['TCON'], $mp3_info);
                    $mp3_info = str_replace('{year_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_YEAR'), $mp3_info);
                    $mp3_info = str_replace('{year}', $info['TYER'], $mp3_info);
                    $mp3_info = str_replace('{length_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_LENGTH'), $mp3_info);
                    $mp3_info = str_replace('{length}', $info['TLEN'].' '.JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_MINS'), $mp3_info);
                    
                    $html_file = str_replace('{mp3_id3_tag}', $mp3_info, $html_file); 
                }     
            }        
        
            $html_file = str_replace('{mp3_player}', '', $html_file);
            $html_file = str_replace('{preview_player}', '', $html_file);
            $html_file = str_replace('{mp3_id3_tag}', '', $html_file);             

            // replace the {preview_url}
            if ($files[$i]->preview_filename){
                // we need the relative path to the "previews" folder
                $media_path = $jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$files[$i]->preview_filename;
                $html_file = str_replace('{preview_url}', $media_path, $html_file);
            } else {
                $html_file = str_replace('{preview_url}', '', $html_file);
            }   
                         
            // build the license info data and build link
            if ($files[$i]->license == '') $files[$i]->license = 0;
            $lic_data = '';

            if ($files[$i]->license_url != '') {
                 $lic_data = $pic_license.'<a href="'.$files[$i]->license_url.'" target="_blank" rel="nofollow" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'">'.$files[$i]->license_title.'</a> '.$extern_url_pic;
            } else {
                if ($files[$i]->license_title != '') {
                     if ($files[$i]->license_text != '') {
                          $lic_data = $pic_license.$files[$i]->license_title;
                          $lic_data .= JHtml::_('tooltip', $files[$i]->license_text, $files[$i]->license_title);
                     } else {
                          $lic_data = $pic_license.$files[$i]->license_title;
                     }
                } else {
                    $lic_data = '';
                }
            }
            $html_file = str_replace('{license_text}', $lic_data, $html_file);
            
            // display checkboxes only, when the user have the correct access permissions and it is activated in layout ( = 0 !! )
            if ( $layout_files->checkbox_off == 0 ) {
                 $html_file = str_replace('{checkbox_list}',$checkbox_list, $html_file);
            } else {
                 //$html_file = str_replace('{checkbox_list}','', $html_file);
            }

            $html_file = str_replace('{cat_id}', $files[$i]->cat_id, $html_file);
            $html_file = str_replace('{cat_title}', $files[$i]->category_title, $html_file);
            
            // file size
            if ($files[$i]->size == '' || $files[$i]->size == '0 B') {
                $html_file = str_replace('{size}', '', $html_file);
                $html_file = str_replace('{filesize_value}', '', $html_file);
            } else {
                $html_file = str_replace('{size}', $pic_size.$files[$i]->size, $html_file);
                $html_file = str_replace('{filesize_value}', $pic_size.$files[$i]->size, $html_file);
            }            
            
            // price
            if ($files[$i]->price != '') {
                $html_file = str_replace('{price_value}', $pic_price.$files[$i]->price, $html_file);
            } else {
                $html_file = str_replace('{price_value}', '', $html_file);
            }

            // file_date
            if ($files[$i]->file_date != '0000-00-00 00:00:00') {
                 if ($this->params->get('show_date') == 0){ 
                     $filedate_data = $pic_date.JHtml::_('date',$files[$i]->file_date, $date_format['long']);
                 } else {
                     $filedate_data = $pic_date.JHtml::_('date',$files[$i]->file_date, $date_format['short']);
                 }    
            } else {
                 $filedate_data = '';
            }
            $html_file = str_replace('{file_date}',$filedate_data, $html_file);
            
            // date_added
            if ($files[$i]->date_added != '0000-00-00 00:00:00') {
                if ($this->params->get('show_date') == 0){ 
                    // use 'normal' date-time format field
                    $date_data = $pic_date.JHtml::_('date',$files[$i]->date_added, $date_format['long']);
                } else {
                    // use 'short' date-time format field
                    $date_data = $pic_date.JHtml::_('date',$files[$i]->date_added, $date_format['short']);
                }    
            } else {
                 $date_data = '';
            }
            $html_file = str_replace('{date_added}',$date_data, $html_file);
            $html_file = str_replace('{created_date_value}',$date_data, $html_file);
            
            if ($files[$i]->creator){
                $html_file = str_replace('{created_by_value}', $files[$i]->creator, $html_file);
            } else {
                $html_file = str_replace('{created_by_value}', '', $html_file);
            }                
            if ($files[$i]->modifier){
                $html_file = str_replace('{modified_by_value}', $files[$i]->modifier, $html_file);
            } else {                              
                $html_file = str_replace('{modified_by_value}', '', $html_file);
            }
            
            // modified_date
            if ($files[$i]->modified_date != '0000-00-00 00:00:00') {
                if ($this->params->get('show_date') == 0){ 
                    $modified_data = $pic_date.JHtml::_('date',$files[$i]->modified_date, $date_format['long']);
                } else {
                    $modified_data = $pic_date.JHtml::_('date',$files[$i]->modified_date, $date_format['short']);
                }    
            } else {
                $modified_data = '';
            }
            $html_file = str_replace('{modified_date_value}',$modified_data, $html_file);

            $user_can_see_download_url = 0;
           
            // only view download-url when user has correct access level
            if ($files[$i]->params->get('access-download') == true){ 
                $user_can_see_download_url++;
                $blank_window = '';
                $blank_window1 = '';
                $blank_window2 = '';
                // get file extension
                $view_types = array();
                $view_types = explode(',', $jlistConfig['file.types.view']);
                $only_file_name = basename($files[$i]->url_download);
                $fileextension = JDHelper::getFileExtension($only_file_name);
                if (in_array($fileextension, $view_types)){
                    $blank_window = 'target="_blank"';
                }    
                // check is set link to a new window?
                if ($files[$i]->extern_file && $files[$i]->extern_site   ){
                    $blank_window = 'target="_blank"';
                }

                 // direct download without summary page?
                 if ($jlistConfig['direct.download'] == '0'){
                     $url_task = 'summary';
                     $download_link = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files[$i]->slug, $files[$i]->cat_id, $files[$i]->language, $url_task));
                 } else {
                     if ($files[$i]->license_agree || $files[$i]->password || $jd_user_settings->view_captcha) {
                         // user must agree the license - fill out a password field - or fill out the captcha human check - so we must view the summary page!
                         $url_task = 'summary';
                         $download_link = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files[$i]->slug, $files[$i]->cat_id, $files[$i]->language, $url_task));
                     } else {     
                         $url_task = 'download.send';
                         $download_link = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$files[$i]->file_id.'&amp;catid='.$files[$i]->cat_id.'&amp;m=0');
                     }    
                 }                    
                
                 // when we have not a menu item to the single download, we need a menu item from the assigned category, or at lates the root itemid
                 if ($files[$i]->menuf_itemid){
                     $file_itemid =  (int)$files[$i]->menuf_itemid;
                 } else {
                     $file_itemid = $category_menu_itemid;
                 }                      
                 
                 if ($url_task == 'download.send'){ 
                     // is the old button used?
                     if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){                     
                         $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jd_download_url">';
                    } else {
                         $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color.' '.$download_size_listings.'">';
                    }                        
                 } else {
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){ 
                        $download_link_text = '<a href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'">';
                    } else {
                        $download_link_text = '<a href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color.' '.$download_size_listings.'">';
                    }                      
                 }    
                 
                 if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                     $pic_download = '<img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.files'].'" style="text-align:middle;border:0px;"  alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD').'" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_DOWNLOAD').'" />';
                 } else {
                    $pic_download = '';
                 } 
                 
                // view not any download link, when we have not really a file
                if ($has_no_file || !$files[$i]->state){
                    // remove download placeholder
                    $html_file = str_replace('{url_download}', '', $html_file);
                    $html_file = str_replace('{checkbox_list}', '', $html_file);
                } else {
                     // insert here the complete download link                 
                     if ($layout_has_download){
                         $placeholder = '{url_download}';
                     } else {
                         $placeholder = '{checkbox_list}';
                     }    
                     if ($jlistConfig['view.also.download.link.text'] && $jlistConfig['use.css.buttons.instead.icons'] == '0'){
                         $html_file = str_replace($placeholder, $download_link_text.$pic_download.'<br />'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a>', $html_file);
                     } elseif ($jlistConfig['use.css.buttons.instead.icons'] == '1') {
                        $html_file = str_replace($placeholder, $download_link_text.$pic_download.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a>', $html_file);  
                     } else {
                        $html_file = str_replace($placeholder, $download_link_text.$pic_download.'</a>', $html_file);  
                     }
                }    
                
                 // mirrors
                 if ($files[$i]->mirror_1 && $files[$i]->state) {
                    if ($files[$i]->extern_site_mirror_1 && $url_task == 'download.send'){
                        $blank_window1 = 'target="_blank"';
                    }
                    $mirror1_link_dum = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$files[$i]->file_id.'&amp;catid='.$files[$i]->cat_id.'&amp;m=1');
                    //$mirror1_link_dum = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files[$i]->slug, $files[$i]->cat_id, $files[$i]->language, $url_task, 1));
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){                
                        $mirror1_link = '<a '.$blank_window1.' href="'.$mirror1_link_dum.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.mirror_1'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_1').'" /></a>';
                    } else {
                        // we use the new css button 
                        $mirror1_link = '<a '.$blank_window1.' href="'.$mirror1_link_dum.'" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color_mirror1.' '.$download_size_mirror.'">'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_1').'</a>'; 
                    }                     
                    $html_file = str_replace('{mirror_1}', $mirror1_link, $html_file);
                 } else {
                    $html_file = str_replace('{mirror_1}', '', $html_file);
                 }
                 
                 if ($files[$i]->mirror_2 && $files[$i]->state) {
                    if ($files[$i]->extern_site_mirror_2 && $url_task == 'download.send'){
                        $blank_window2 = 'target="_blank"';
                    }
                    $mirror2_link_dum = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$files[$i]->file_id.'&amp;catid='.$files[$i]->cat_id.'&amp;m=2');
                    //$mirror2_link_dum = JRoute::_(JDownloadsHelperRoute::getOtherRoute($files[$i]->slug, $files[$i]->cat_id, $files[$i]->language, $url_task, 2));
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){                
                        $mirror2_link = '<a '.$blank_window2.' href="'.$mirror2_link_dum.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.mirror_2'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_2').'" /></a>';
                    } else {
                        // we use the new css button 
                        $mirror2_link = '<a '.$blank_window2.' href="'.$mirror2_link_dum.'" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jdbutton '.$download_color_mirror2.' '.$download_size_mirror.'">'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_2').'</a>'; 
                    }                     
                    $html_file = str_replace('{mirror_2}', $mirror2_link, $html_file);
                 } else {
                    $html_file = str_replace('{mirror_2}', '', $html_file);
                 }            
            } else {
                 $html_file = str_replace('{url_download}', '', $html_file);
                 $html_file = str_replace('{mirror_1}', '', $html_file); 
                 $html_file = str_replace('{mirror_2}', '', $html_file); 
            }
            
            if ($jlistConfig['view.detailsite']){
                $title_link = JRoute::_(JDownloadsHelperRoute::getDownloadRoute($files[$i]->slug, $files[$i]->cat_id, $files[$i]->language));
                $title_link_text = '<a href="'.$title_link.'">'.$this->escape($files[$i]->file_title).'</a>';
                $detail_link_text = '<a href="'.$title_link.'">'.JText::_('COM_JDOWNLOADS_FE_DETAILS_LINK_TEXT_TO_DETAILS').'</a>';
                // Symbol anzeigen - auch als url
                if ($files[$i]->file_pic != '' ) {
                    $filepic = '<a href="'.$title_link.'">'.'<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" style="text-align:top;border:0px;" width="'.$jlistConfig['file.pic.size'].'" height="'.$jlistConfig['file.pic.size.height'].'" alt="'.$files[$i]->file_pic.'" /></a> ';
                } else {
                    $filepic = '';
                }
                $html_file = str_replace('{file_pic}',$filepic, $html_file);
                // link to details view at the end
                $html_file = str_replace('{link_to_details}', $detail_link_text, $html_file);
                $html_file = str_replace('{file_title}', $title_link_text.' '.$editIcon, $html_file);
                
            } elseif ($jlistConfig['use.download.title.as.download.link']){
                
                if ($user_can_see_download_url && !$has_no_file){
                    // build title link as download link
                   if ($url_task == 'download.send'){ 
                      $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" class="jd_download_url">'.$files[$i]->file_title.'</a>';
                   } else {
                      $download_link_text = '<a href="'.$download_link.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'">'.$files[$i]->file_title.'</a>';                  
                   }
                   // View file icon also with link
                   if ($files[$i]->file_pic != '' ) {
                        $filepic = '<a href="'.$download_link.'"><img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" style="text-align:top;border:0px;" width="'.$jlistConfig['file.pic.size'].'" height="'.$jlistConfig['file.pic.size.height'].'" alt="'.$files[$i]->file_pic.'" /></a>';
                   } else {
                        $filepic = '';
                   }
                   $html_file = str_replace('{file_pic}',$filepic, $html_file);
                   $html_file = str_replace('{link_to_details}', '', $html_file);
                   $html_file = str_replace('{file_title}', $download_link_text.' '.$editIcon, $html_file);
                } else {
                    // user may not use download link
                    $html_file = str_replace('{file_title}', $files[$i]->file_title, $html_file);
                    if ($files[$i]->file_pic != '' ) {
                        $filepic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" style="text-align:top;border:0px;" width="'.$jlistConfig['file.pic.size'].'" height="'.$jlistConfig['file.pic.size.height'].'" alt="'.$files[$i]->file_pic.'" />';
                    } else {
                        $filepic = '';
                    }
                    $html_file = str_replace('{file_pic}',$filepic, $html_file);
                }    
            } else {
                // no links
                if ($files[$i]->file_pic != '' ) {
                    $filepic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$files[$i]->file_pic.'" style="text-align:top;border:0px;" width="'.$jlistConfig['file.pic.size'].'" height="'.$jlistConfig['file.pic.size.height'].'" alt="'.$files[$i]->file_pic.'" />';
                } else {
                    $filepic = '';
                }
                $html_file = str_replace('{file_pic}',$filepic, $html_file);
                // remove link to details view at the end
                $html_file = str_replace('{link_to_details}', '', $html_file);
                $html_file = str_replace('{file_title}', $files[$i]->file_title.' '.$editIcon, $html_file);
            }             
            
            
            // build website url
            if (!$files[$i]->url_home == '') {
                 if (strpos($files[$i]->url_home, 'http://') !== false) {    
                     $html_file = str_replace('{url_home}',$pic_website.'<a href="'.$files[$i]->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$extern_url_pic, $html_file);
                     $html_file = str_replace('{author_url_text} ',$pic_website.'<a href="'.$files[$i]->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$extern_url_pic, $html_file);
                 } else {
                     $html_file = str_replace('{url_home}',$pic_website.'<a href="http://'.$files[$i]->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$extern_url_pic, $html_file);
                     $html_file = str_replace('{author_url_text}',$pic_website.'<a href="http://'.$files[$i]->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$extern_url_pic, $html_file);
                 }    
            } else {
                $html_file = str_replace('{url_home}', '', $html_file);
                $html_file = str_replace('{author_url_text}', '', $html_file);
            }

            // encode is link a mail
            if (strpos($files[$i]->url_author, '@') && $jlistConfig['mail.cloaking']){
                if (!$files[$i]->author) { 
                    $mail_encode = JHtml::_('email.cloak', $files[$i]->url_author);
                } else {
                    $mail_encode = JHtml::_('email.cloak',$files[$i]->url_author, true, $files[$i]->author, false);
                }        
            }
                    
            // build author link
            if ($files[$i]->author <> ''){
                if ($files[$i]->url_author <> '') {
                    if ($mail_encode) {
                        $link_author = $pic_author.$mail_encode;
                    } else {
                        if (strpos($files[$i]->url_author, 'http://') !== false) {    
                            $link_author = $pic_author.'<a href="'.$files[$i]->url_author.'" target="_blank">'.$files[$i]->author.'</a> '.$extern_url_pic;
                        } else {
                            $link_author = $pic_author.'<a href="http://'.$files[$i]->url_author.'" target="_blank">'.$files[$i]->author.'</a> '.$extern_url_pic;
                        }        
                    }
                    $html_file = str_replace('{author}',$link_author, $html_file);
                    $html_file = str_replace('{author_text}',$link_author, $html_file);
                    $html_file = str_replace('{url_author}', '', $html_file);
                } else {
                    $link_author = $pic_author.$files[$i]->author;
                    $html_file = str_replace('{author}',$link_author, $html_file);
                    $html_file = str_replace('{author_text}',$link_author, $html_file);
                    $html_file = str_replace('{url_author}', '', $html_file);
                }
            } else {
                    $html_file = str_replace('{url_author}', $pic_author.$files[$i]->url_author, $html_file);
                    $html_file = str_replace('{author}','', $html_file);
                    $html_file = str_replace('{author_text}','', $html_file); 
            }

            // set system value
            $file_sys_values = explode(',' , JDHelper::getOnlyLanguageSubstring($jlistConfig['system.list']));
            if ($files[$i]->system == 0 ) {
                $html_file = str_replace('{system}', '', $html_file);
                 $html_file = str_replace('{system_text}', '', $html_file); 
            } else {
                $html_file = str_replace('{system}', $pic_system.$file_sys_values[$files[$i]->system], $html_file);
                $html_file = str_replace('{system_text}', $pic_system.$file_sys_values[$files[$i]->system], $html_file);
            }

            // set language value
            $file_lang_values = explode(',' , JDHelper::getOnlyLanguageSubstring($jlistConfig['language.list']));
            if ($files[$i]->file_language == 0 ) {
                $html_file = str_replace('{language}', '', $html_file);
                $html_file = str_replace('{language_text}', '', $html_file);
            } else {
                $html_file = str_replace('{language}', $pic_language.$file_lang_values[$files[$i]->file_language], $html_file);
                $html_file = str_replace('{language_text}', $pic_language.$file_lang_values[$files[$i]->file_language], $html_file);
            }

            // insert rating system
            if ($jlistConfig['view.ratings']){
                $rating_system = JDHelper::getRatings($files[$i]->file_id, $files[$i]->rating_count, $files[$i]->rating_sum);
                $html_file = str_replace('{rating}', $rating_system, $html_file);
                $html_file = str_replace('{rating_title}', JText::_('COM_JDOWNLOADS_RATING_LABEL'), $html_file);
            } else {
                $html_file = str_replace('{rating}', '', $html_file);
                $html_file = str_replace('{rating_title}', '', $html_file);
            }
            
            // custom fields
            $custom_fields_arr = JDHelper::existsCustomFieldsTitles();
            $row_custom_values = array('dummy',$files[$i]->custom_field_1, $files[$i]->custom_field_2, $files[$i]->custom_field_3, $files[$i]->custom_field_4, $files[$i]->custom_field_5,
                               $files[$i]->custom_field_6, $files[$i]->custom_field_7, $files[$i]->custom_field_8, $files[$i]->custom_field_9, $files[$i]->custom_field_10, $files[$i]->custom_field_11, $files[$i]->custom_field_12, $files[$i]->custom_field_13, $files[$i]->custom_field_14);
            for ($x=1; $x<15; $x++){
                // replace placeholder with title and value
                if (in_array($x,$custom_fields_arr[0]) && $row_custom_values[$x] && $row_custom_values[$x] != '0000-00-00'){
                    $html_file = str_replace("{custom_title_$x}", $custom_fields_arr[1][$x-1], $html_file);
                    if ($x > 5){
                        $html_file = str_replace("{custom_value_$x}", stripslashes($row_custom_values[$x]), $html_file);
                    } else {
                        $html_file = str_replace("{custom_value_$x}", $custom_fields_arr[2][$x-1][$row_custom_values[$x]], $html_file);
                    }    
                } else {
                    // remove placeholder
                    if ($jlistConfig['remove.field.title.when.empty']){
                        $html_file = str_replace("{custom_title_$x}", '', $html_file);
                    } else {
                        $html_file = str_replace("{custom_title_$x}", $custom_fields_arr[1][$x-1], $html_file);
                    }    
                    $html_file = str_replace("{custom_value_$x}", '', $html_file);
                }    
            }
            
            $html_file = str_replace('{downloads}',$pic_downloads.JDHelper::strToNumber((int)$files[$i]->downloads), $html_file);
            $html_file = str_replace('{hits_value}',$pic_downloads.JDHelper::strToNumber((int)$files[$i]->downloads), $html_file);            
            $html_file = str_replace('{ordering}',$files[$i]->ordering, $html_file);
            $html_file = str_replace('{published}',$files[$i]->published, $html_file);
            
            // support for content plugins 
            if ($jlistConfig['activate.general.plugin.support'] && !$jlistConfig['use.general.plugin.support.only.for.descriptions']) {  
                $html_file = JHtml::_('content.prepare', $html_file);

            }

            $html_files .= $html_file;
            $html_files .= $files[$i]->event->afterDisplayContent;
            
        }

        // display only downloads area when it exist data here
        if ($total_downloads > 0){
            $body = $html_files;
        } else {
            $no_files_msg = '';
            if ($jlistConfig['view.no.file.message.in.empty.category']){
                $no_files_msg = '<br />'.JText::_('COM_JDOWNLOADS_FRONTEND_NOFILES').'<br /><br />';            
            } 
            $body = $no_files_msg;
        }    

        
        // display top checkbox only when the user can download any files here - right access permissions
        if (isset($user_can_see_download_url)){ 
            $checkbox_top = '<tr><form name="down'.$formid.'" action="'.JRoute::_('index.php?option=com_jdownloads&amp;view=summary&amp;Itemid='.$file_itemid).'"
                    onsubmit="return pruefen('.$formid.',\''.JText::_('COM_JDOWNLOADS_JAVASCRIPT_TEXT_1').' '.JText::_('COM_JDOWNLOADS_JAVASCRIPT_TEXT_2').'\');" method="post">
                    <td width="89%" style="text-align:right;">'.JDHelper::getOnlyLanguageSubstring($jlistConfig['checkbox.top.text']).'</td>
                    <td width="11%" style="text-align:center;"><input type="checkbox" name="toggle"
                    value="" onclick="checkAlle('.$i.','.$formid.');" /></td></tr>';
            
            // view top checkbox only when activated in layout
            if ($layout_files->checkbox_off == 0 && !empty($files) && !$checkbox_top_always_added) {
               $body = str_replace('{checkbox_top}', $checkbox_top, $body);
               $checkbox_top_always_added = true;
            } else {
               $body = str_replace('{checkbox_top}', '', $body);
            }   
        } else {
            // view message for missing access permissions
            if ($user->guest){
                $regg = str_replace('<br />', '', JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED'));
            } else {
                $regg = str_replace('<br />', '', JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED2'));
            }    

            if ($total_downloads > 0){
                $body = str_replace('{checkbox_top}', '<div style="text-align:center; padding:8px;"><img src="'.JURI::base().'components/com_jdownloads/assets/images/info32.png" style="text-align:middle;border:0px;" width="32" height="32" alt="info" /> '.$regg.'</div>', $body);                    
            } else {
                $body = str_replace('{checkbox_top}', '', $body);                    
            }    
        } 
        
                
        $form_hidden = '<input type="hidden" name="boxchecked" value=""/> ';
        $body = str_replace('{form_hidden}', $form_hidden, $body);
        // $body .= '<input type="hidden" name="catid" value="'.$catid.'"/>';
        $body .= JHtml::_( 'form.token' ).'</form>';

        // view submit button only when checkboxes are activated
        $button = '<input class="button" type="submit" name="weiter" value="'.JText::_('COM_JDOWNLOADS_FORM_BUTTON_TEXT').'"/>';
        
        // view only submit button when user has correct access level and checkboxes are used in layout
        if ($layout_files->checkbox_off == 0 && !empty($files)) {
            $body = str_replace('{form_button}', $button, $body);
        } else {
            $body = str_replace('{form_button}', '', $body);
        }        
        
        $html .= $body;   
        
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
            echo $html;
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