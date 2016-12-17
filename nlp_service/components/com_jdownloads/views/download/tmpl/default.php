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

    // get jD user limits and settings
    $jd_user_settings = JDHelper::getUserRules();
    
    $jdownloads_root_dir_name = basename($jlistConfig['files.uploaddir']);
    
    $file_path = '';
    if ($this->item->url_download){
        if ($this->item->cat_id > 1){
            if ($this->item->category_cat_dir_parent){
                $file_path = $jlistConfig['files.uploaddir'].'/'.$this->item->category_cat_dir_parent.'/'.$this->item->category_cat_dir.'/'.$this->item->url_download;
            } else {
                $file_path = $jlistConfig['files.uploaddir'].'/'.$this->item->category_cat_dir.'/'.$this->item->url_download;
            }
        } else {
           // Download is 'uncategorized'
           $file_path = $jlistConfig['files.uploaddir'].'/'.$jlistConfig['uncategorised.files.folder.name'].'/'.$this->item->url_download; 
        }    
    }
    
    if ($this->item->category_cat_dir_parent){
        $category_dir = $this->item->category_cat_dir_parent.'/'.$this->item->category_cat_dir;
    } elseif ($this->item->category_cat_dir) {
        $category_dir = $this->item->category_cat_dir;
    } else {
        // we have an uncategorised download so we must add the defined folder for this
        $category_dir = $jlistConfig['uncategorised.files.folder.name'];
    }   
    
    // for Tabs
    //jimport('joomla.html.pane');
    //jimport( 'joomla.html.html.tabs' );
    jimport ('joomla.html.html.bootstrap');
    // for Tooltip
    JHtml::_('behavior.tooltip');
    
    // Create shortcuts to some parameters.
    $params     = $this->item->params;
    $canEdit    = $this->item->params->get('access-edit');

    $html           = '';
    $body           = '';
    $footer_text    = '';
    $layout         = '';
    $is_admin   = false;
    
    $date_format = JDHelper::getDateFormat();

    if (JDHelper::checkGroup('8', true) || JDHelper::checkGroup('7', true)){
        $is_admin = true;
    }

    // Get the needed layout data - type = 5 for a 'download details' layout            
    $layout = JDHelper::getLayout(5, false);
    if ($layout){
        $layout_text = $layout->template_text;
        $header      = $layout->template_header_text;
        $subheader   = $layout->template_subheader_text;
        $footer      = $layout->template_footer_text;
    } else {
        // We have not a valid layout data
        echo '<big>No valid layout found!</big>';
    }
    
    $catid              = $this->item->cat_id;
    $is_detail          = true;
    $is_showcats        = false;
    $is_one_cat         = false;
    $has_no_file        = false;
    $extern_media       = false;
    $no_file_info       = '';
    
    // has this download a file or an extern file or is used a file from other download?
    if (!$this->item->url_download && !$this->item->other_file_id && !$this->item->extern_file){
        // only a document without file
        $no_file_info = JText::_('COM_JDOWNLOADS_FRONTEND_ONLY_DOCUMENT_USER_INFO');
        $has_no_file = true;
    }

    // get current category menu ID when exist and all needed menu IDs for the header links
    $menuItemids = JDHelper::getMenuItemids($catid);
    
    // get all other menu category IDs so we can use it when we needs it
    $cat_link_itemids = JDHelper::getAllJDCategoryMenuIDs();
    
    // "Home" menu link itemid
    $root_itemid =  $menuItemids['root'];

    // make sure, that we have a valid menu itemid for the here viewed base category
    // if (!$this->category->menu_itemid) $this->category->menu_itemid = $root_itemid;    
    
    $html = '<div class="jd-item-page'.$this->pageclass_sfx.'">';
    
    if ($this->params->get('show_page_heading')) {
        $html .= '<h1>'.$this->escape($this->params->get('page_heading')).'</h1>';
    }    
    
     
    // ==========================================
    // HEADER SECTION
    // ==========================================

    if ($header != ''){
        
        $menuItemids = JDHelper::getMenuItemids($catid);
        
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

        // build upper link
        if ($is_detail){
            if ($catid == 1){
                $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=downloads&amp;type=uncategorised&amp;Itemid='.$menuItemids['root']);
            } elseif ($catid == -1) {
                $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=downloads&amp;Itemid='.$menuItemids['root']);
            } else {    
                $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=category&amp;catid='.$catid.'&amp;Itemid='.$menuItemids['root']);
            }    
            $header = str_replace('{upper_link}', '<a href="'.$upper_link.'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upper.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'" /></a> <a href="'.$upper_link.'">'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'</a>', $header);
        } else { 
            // get parent category (access must be present then we are always in a sub category from it)
            $db->setQuery("SELECT parent_id FROM #__jdownloads_categories WHERE id = '$catid'");
            $parent_cat_id = $db->loadResult();
            if ($parent_cat_id){
                $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=category&amp;catid='.$parent_cat_id.'&amp;Itemid='.$menuItemids['root']);
                $header = str_replace('{upper_link}', '<a href="'.$upper_link.'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upper.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'" /></a> <a href="'.$upper_link.'">'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'</a>', $header);    
            } else {
                // we are in a sub category - so we link to the main
                if ($is_one_cat){
                    $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=categories&amp;Itemid='.$menuItemids['root']);
                    $header = str_replace('{upper_link}', '<a href="'.$upper_link.'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upper.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'" /></a> <a href="'.$upper_link.'">'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'</a>', $header);            
                } else {
                  $header = str_replace('{upper_link}', '', $header);
                }  
            }    
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
            
            // replace google adsense placeholder with script when active (also for subheader tab)
            if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                    $subheader = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $subheader);
            } else {
                    $subheader = str_replace( '{google_adsense}', '', $subheader);
            }         
            
            if ($is_detail){
                $subheader = str_replace('{detail_title}', JText::_('COM_JDOWNLOADS_FRONTEND_SUBTITLE_OVER_DETAIL'), $subheader); 
            } 
            $html .= $subheader;            
        }    
    }
    
    // ==========================================
    // BODY SECTION - VIEW THE DOWNLOAD DATA
    // ==========================================
    
    if ($layout_text != ''){
 
        $body = $layout_text;
        
        // build a little pic for extern links
        $extern_url_pic = '<img src="'.JURI::base().'components/com_jdownloads/assets/images/link_extern.gif" alt="external" />';        
        
        // create field labels
        $body = JDHelper::buildFieldTitles($body, $this->item);

        // tabs or sliders when the placeholders are used
        if ((int)$jlistConfig['use.tabs.type'] > 0){
           if ((int)$jlistConfig['use.tabs.type'] == 1){
                // use slides
               $body = str_replace('{tabs begin}', JHtml::_('bootstrap.startAccordion', 'jdpane', 'panel1'), $body);
               $body = str_replace('{tab description}', JHtml::_('bootstrap.addSlide', 'jdpane', JText::_('COM_JDOWNLOADS_FE_TAB_DESCRIPTION_TITLE'), 'panel1'), $body); 
               $body = str_replace('{tab description end}', JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab pics}', JHtml::_('bootstrap.addSlide', 'jdpane', JText::_('COM_JDOWNLOADS_FE_TAB_PICS_TITLE'), 'panel2'), $body); 
               $body = str_replace('{tab pics end}', JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab mp3}', JHtml::_('bootstrap.addSlide', 'jdpane', JText::_('COM_JDOWNLOADS_FE_TAB_AUDIO_TITLE'), 'panel3'), $body);
               $body = str_replace('{tab mp3 end}', JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab data}', JHtml::_('bootstrap.addSlide', 'jdpane', JText::_('COM_JDOWNLOADS_FE_TAB_DATA_TITLE'), 'panel4'), $body);
               $body = str_replace('{tab data end}', JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab download}', JHtml::_('bootstrap.addSlide', 'jdpane', JText::_('COM_JDOWNLOADS_FE_TAB_DOWNLOAD_TITLE'), 'panel5'), $body); 
               $body = str_replace('{tab download end}',JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab custom1}', JHtml::_('bootstrap.addSlide', 'jdpane', $jlistConfig['additional.tab.title.1'], 'panel6'), $body); 
               $body = str_replace('{tab custom1 end}', JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab custom2}', JHtml::_('bootstrap.addSlide', 'jdpane', $jlistConfig['additional.tab.title.2'], 'panel7'), $body); 
               $body = str_replace('{tab custom2 end}', JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tab custom3}', JHtml::_('bootstrap.addSlide', 'jdpane', $jlistConfig['additional.tab.title.3'], 'panel8'), $body); 
               $body = str_replace('{tab custom3 end}',JHtml::_('bootstrap.endSlide'), $body);
               $body = str_replace('{tabs end}', JHtml::_('bootstrap.endAccordion'), $body);            
           } else {
               // use tabs
               $body = str_replace('{tabs begin}', JHtml::_('bootstrap.startTabSet', 'jdpane', array('active' => 'panel1')), $body);
               $body = str_replace('{tab description}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel1', JText::_('COM_JDOWNLOADS_FE_TAB_DESCRIPTION_TITLE', true)), $body); 
               $body = str_replace('{tab description end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab pics}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel2', JText::_('COM_JDOWNLOADS_FE_TAB_PICS_TITLE', true)), $body); 
               $body = str_replace('{tab pics end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab mp3}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel3', JText::_('COM_JDOWNLOADS_FE_TAB_AUDIO_TITLE', true)), $body); 
               $body = str_replace('{tab mp3 end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab data}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel4', JText::_('COM_JDOWNLOADS_FE_TAB_DATA_TITLE', true)), $body); 
               $body = str_replace('{tab data end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab download}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel5', JText::_('COM_JDOWNLOADS_FE_TAB_DOWNLOAD_TITLE', true)), $body); 
               $body = str_replace('{tab download end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab custom1}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel6', $jlistConfig['additional.tab.title.1'], true), $body); 
               $body = str_replace('{tab custom1 end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab custom2}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel7', $jlistConfig['additional.tab.title.2'], true), $body); 
               $body = str_replace('{tab custom2 end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tab custom3}', JHtml::_('bootstrap.addTab', 'jdpane', 'panel8', $jlistConfig['additional.tab.title.3'], true), $body); 
               $body = str_replace('{tab custom3 end}', JHtml::_('bootstrap.endTab'), $body);
               $body = str_replace('{tabs end}', JHtml::_('bootstrap.endTabSet'), $body);      
           }
        } else {
           // delete the placeholders 
           $body = str_replace('{tabs begin}', '', $body);
           $body = str_replace('{tab description}', '', $body);
           $body = str_replace('{tab description end}', '', $body);
           $body = str_replace('{tab pics}', '', $body);
           $body = str_replace('{tab pics end}', '', $body);
           $body = str_replace('{tab mp3}', '', $body);
           $body = str_replace('{tab mp3 end}', '', $body);
           $body = str_replace('{tab data}', '', $body);
           $body = str_replace('{tab data end}', '', $body);
           $body = str_replace('{tab download}', '', $body);
           $body = str_replace('{tab download end}', '', $body);
           $body = str_replace('{tab custom1}', '', $body);
           $body = str_replace('{tab custom1 end}', '', $body);      
           $body = str_replace('{tab custom2}', '', $body);
           $body = str_replace('{tab custom2 end}', '', $body);
           $body = str_replace('{tab custom3}', '', $body);
           $body = str_replace('{tab custom3 end}', '', $body);
           $body = str_replace('{tabs end}', '', $body);      
        }    

        // check custom data fields
        $custom_fields_arr = JDHelper::existsCustomFieldsTitles();
        $row_custom_values = array('dummy',$this->item->custom_field_1, $this->item->custom_field_2, $this->item->custom_field_3, $this->item->custom_field_4, $this->item->custom_field_5,
                                   $this->item->custom_field_6, $this->item->custom_field_7, $this->item->custom_field_8, $this->item->custom_field_9, $this->item->custom_field_10, $this->item->custom_field_11, $this->item->custom_field_12, $this->item->custom_field_13, $this->item->custom_field_14);
        for ($x=1; $x<15; $x++){
            // replace placeholder with title and value
            if (in_array($x,$custom_fields_arr[0]) && $row_custom_values[$x] && $row_custom_values[$x] != '0000-00-00'){
                $body = str_replace("{custom_title_$x}", $custom_fields_arr[1][$x-1], $body);
                if ($x > 5){
                    $body = str_replace("{custom_value_$x}", stripslashes($row_custom_values[$x]), $body);
                } else {
                    $body = str_replace("{custom_value_$x}", $custom_fields_arr[2][$x-1][$row_custom_values[$x]], $body);
                }    
            } else {
                // remove placeholders
                if ($jlistConfig['remove.field.title.when.empty']){
                    $body = str_replace("{custom_title_$x}", '', $body);
                } else {
                    $body = str_replace("{custom_title_$x}", $custom_fields_arr[1][$x-1], $body);
                }    
                $body = str_replace("{custom_value_$x}", '', $body);
            }    
        }
        
        // get data to publish the edit icon and publish data as tooltip
        if ($canEdit){
            $editIcon = JDHelper::getEditIcon($this->item);
        } else {
            $editIcon = '';
        }   
        
        // replace 'featured' placeholders
        if ($this->item->featured){
            // add the css class
            $body = str_replace('{featured_class}', 'jd_featured', $body);
            $body = str_replace('{featured_detail_class}', 'jd_featured_detail', $body);            
            // add the pic
            if ($jlistConfig['featured.pic.filename']){
                $featured_pic = '<img class="jd_featured_star" src="'.JURI::base().'images/jdownloads/featuredimages/'.$jlistConfig['featured.pic.filename'].'" width="'.$jlistConfig['featured.pic.size'].'" height="'.$jlistConfig['featured.pic.size.height'].'" alt="'.$jlistConfig['featured.pic.filename'].'" />';
                $body = str_replace('{featured_pic}', $featured_pic, $body);
            } else {
                $body = str_replace('{featured_pic}', '', $body);
            }
        } else {
            $body = str_replace('{featured_class}', '', $body);
            $body = str_replace('{featured_detail_class}', '', $body);
            $body = str_replace('{featured_pic}', '', $body);
        }        
        
        $body = str_replace('{price_value}', $this->item->price, $body);
        $body = str_replace('{views_value}',JDHelper::strToNumber((int)$this->item->views), $body);
        $body = str_replace('{details_block_title}', JText::_('COM_JDOWNLOADS_FE_DETAILS_DATA_BLOCK_TITLE'), $body);
        if ($this->item->url_download){
            $body = str_replace('{file_name}', JDHelper::getShorterFilename($this->item->url_download), $body);
        } elseif (isset($this->item->filename_from_other_download) && $this->item->filename_from_other_download != ''){            
            $body = str_replace('{file_name}', JDHelper::getShorterFilename($this->item->filename_from_other_download), $body);
        } else {
            $body = str_replace('{file_name}', '', $body);
        }   

        $body = str_replace('{category_title}', JText::_('COM_JDOWNLOADS_CATEGORY_LABEL'), $body);
        $body = str_replace('{category_name}', $this->item->category_title, $body);
        
        $body = str_replace('{file_title}', $this->item->file_title.' '.$editIcon, $body);
        
        if ($this->item->size == '0 B'){
            $body = str_replace('{filesize_value}', '', $body);
        } else {
            $body = str_replace('{filesize_value}', $this->item->size, $body);
        } 
        
        $body = str_replace('{created_by_value}', $this->item->creator, $body);    
        $body = str_replace('{modified_by_value}', $this->item->modifier, $body);
        $body = str_replace('{hits_value}',JDHelper::strToNumber((int)$this->item->downloads), $body);         
        $body = str_replace('{md5_value}',$this->item->md5_value, $body);
        $body = str_replace('{sha1_value}',$this->item->sha1_value, $body);
        $body = str_replace('{changelog_value}', $this->item->changelog, $body);
        
        
        if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)){ 
            $this->item->tagLayout = new JLayoutFile('joomla.content.tags');
            $body = str_replace('{tags}', $this->item->tagLayout->render($this->item->tags->itemTags), $body);
            $body = str_replace('{tags_title}', JText::_('COM_JDOWNLOADS_TAGS_LABEL'), $body);
        } else {
            $body = str_replace('{tags}', '', $body);
            $body = str_replace('{tags_title}', '', $body);
        }
        
        
        $body = str_replace('{cat_title}', $this->item->category_title, $body);  
      //$body = str_replace('{pathway_text}', JText::_('COM_JDOWNLOADS_FE_DETAILS_PATHWAY_TEXT'), $body);

        // insert google adsense
        if ($jlistConfig['google.adsense.active']){
           $body = str_replace('{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $body);
        } else {
           $body = str_replace('{google_adsense}', '', $body);
        }

        // report download link
        if ($this->user_rules->view_report_form){
           $report_link = '<a href="'.JRoute::_("index.php?option=com_jdownloads&amp;view=report&amp;id=".$this->item->slug."&amp;catid=".$this->item->cat_id."&amp;Itemid=".$root_itemid).'" rel="nofollow">'.JText::_('COM_JDOWNLOADS_FRONTEND_REPORT_FILE_LINK_TEXT').'</a>';
           $body = str_replace('{report_link}', $report_link, $body);
        } else {
           $body = str_replace('{report_link}', '', $body);
        }

        // get icon file pic
        if ($this->item->file_pic != '' ) {
            $fpicsize = $jlistConfig['file.pic.size'];
            $fpicsize_height = $jlistConfig['file.pic.size.height'];
            $this->itempic = '<img src="'.JURI::base().'images/jdownloads/fileimages/'.$this->item->file_pic.'" style="text-align:top;border:0px;" width="'.$fpicsize.'" height="'.$fpicsize_height.'"  alt="'.$this->item->file_title.'" /> ';
        } else {
            $this->itempic = '';
        }
        $body = str_replace('{file_pic}',$this->itempic, $body);
        
        if ($this->item->release) {
            $body = str_replace('{release}', $this->item->release, $body);        
            //$body = str_replace('{release}',JText::_('COM_JDOWNLOADS_FRONTEND_VERSION_TITLE').$this->item->release, $body);        
        } else {
            $body = str_replace('{release}', '', $body);        
        }

        // description
        if (!$this->item->description_long){
            // support for content plugins
            if ($jlistConfig['activate.general.plugin.support'] && $jlistConfig['use.general.plugin.support.only.for.descriptions']) {  
               $this->item->description = JHtml::_('content.prepare', $this->item->description);    /*old 1.5 way */        
            }        
            $body = str_replace('{description_long}', $this->item->description, $body); 
        } else {
            // support for content plugins
            if ($jlistConfig['activate.general.plugin.support'] && $jlistConfig['use.general.plugin.support.only.for.descriptions']) {  
                $this->item->description_long = JHtml::_('content.prepare', $this->item->description_long);   /*old 1.5 way */             
            }
            $body = str_replace('{description_long}', $this->item->description_long, $body);
        }
        
        // place the images
        $body = JDHelper::placeThumbs($body, $this->item->images, 'detail');
        
        // pics for: new file / hot file /updated
        $hotpic = '<img src="'.JURI::base().'images/jdownloads/hotimages/'.$jlistConfig['picname.is.file.hot'].'" alt="hotpic" />';
        $newpic = '<img src="'.JURI::base().'images/jdownloads/newimages/'.$jlistConfig['picname.is.file.new'].'" alt="newpic" />';
        $updatepic = '<img src="'.JURI::base().'images/jdownloads/updimages/'.$jlistConfig['picname.is.file.updated'].'" alt="updatepic" />';
        
        // alternate CSS buttons when selected in configuration
        $status_color_hot       = $jlistConfig['css.button.color.hot'];
        $status_color_new       = $jlistConfig['css.button.color.new'];
        $status_color_updated   = $jlistConfig['css.button.color.updated'];
        $download_color         = $jlistConfig['css.button.color.download'];
        $download_size          = $jlistConfig['css.button.size.download'];
        $download_size_mirror   = $jlistConfig['css.button.size.download.mirror'];        
        $download_color_mirror1  = $jlistConfig['css.button.color.mirror1'];        
        $download_color_mirror2  = $jlistConfig['css.button.color.mirror2']; 
        
        // compute for HOT symbol
        if ($jlistConfig['loads.is.file.hot'] > 0 && $this->item->downloads >= $jlistConfig['loads.is.file.hot'] ){
            // is the old button pic used?
            if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                $body = str_replace('{pic_is_hot}', $hotpic, $body);
            } else {
                // CSS Button is selected
                $body = str_replace('{pic_is_hot}', '<span class="jdbutton '.$status_color_hot.' jstatus">'.JText::_('COM_JDOWNLOADS_HOT').'</span>', $body);
            }    
        } else {    
            $body = str_replace('{pic_is_hot}', '', $body);
        }
        
        // compute for NEW symbol
        $days_diff = JDHelper::computeDateDifference(date('Y-m-d H:i:s'), $this->item->date_added);
        if ($jlistConfig['days.is.file.new'] > 0 && $days_diff <= $jlistConfig['days.is.file.new']){
            // is the old button used?
            if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){            
                $body = str_replace('{pic_is_new}', $newpic, $body);
            } else {
                // CSS Button is selected
                $body = str_replace('{pic_is_new}', '<span class="jdbutton '.$status_color_new.' jstatus">'.JText::_('COM_JDOWNLOADS_NEW').'</span>', $body);
            }    
        } else {    
            $body = str_replace('{pic_is_new}', '', $body);
        }
        
        // compute for UPDATED symbol
        // view it only when in the download is activated the 'updated' option
        if ($this->item->update_active) {
            $days_diff = JDHelper::computeDateDifference(date('Y-m-d H:i:s'), $this->item->modified);
            if ($jlistConfig['days.is.file.updated'] > 0 && $days_diff >= 0 && $days_diff <= $jlistConfig['days.is.file.updated']){
                if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                    $body = str_replace('{pic_is_updated}', $updatepic, $body);
                } else {
                    // CSS Button is selected
                    $body = str_replace('{pic_is_updated}', '<span class="jdbutton '.$status_color_updated.' jstatus">'.JText::_('COM_JDOWNLOADS_UPDATED').'</span>', $body);
                }    
            } else {    
                $body = str_replace('{pic_is_updated}', '', $body);
            }
        } else {
           $body = str_replace('{pic_is_updated}', '', $body);
        }    
        
        // build the license info data and build link
        if ($this->item->license == '') $this->item->license = 0;
        $lic_data = '';

        if ($this->item->license_url != '') {
             $lic_data = '<a href="'.$this->item->license_url.'" target="_blank" rel="nofollow" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_MINI_ICON_ALT_LICENCE').'">'.$this->item->license_title.'</a> '.$extern_url_pic;
        } else {
            if ($this->item->license_title != '') {
                 if ($this->item->license_text != '') {
                      $lic_data = $this->item->license_title;
                      $lic_data .= JHtml::_('tooltip', $this->item->license_text, $this->item->license_title);
                 } else {
                      $lic_data = $this->item->license_title;
                 }
            } else {
                $lic_data = '';
            }
        }
        $body = str_replace('{license_text}', $lic_data, $body);
        
        if ($this->item->modified != '0000-00-00 00:00:00') {
            $modified_data = JHtml::_('date',$this->item->modified, $date_format['long']);
        } else {
            $modified_data = '';
        }
        $body = str_replace('{modified_date_value}',$modified_data, $body);
        
        // remove placeholder from a older version (not more used)
        $body = str_replace('{download_time}','', $body);    

        // file_date
        if ($this->item->file_date != '0000-00-00 00:00:00') {
             $this->itemdate_data = JHtml::_('date',$this->item->file_date, $date_format['long']);
        } else {
             if ($file_path && JFile::exists($file_path)){
                 $this->itemdate_data = JHtml::_('date',filemtime($file_path), $date_format['long']);
             } else {
                $this->itemdate_data = '';
             }
        }
        $body = str_replace('{file_date}',$this->itemdate_data, $body);

        // date_added    
        if ($this->item->date_added != '0000-00-00 00:00:00') {
            $date_data = JHtml::_('date',$this->item->date_added, $date_format['long']);
        } else {
            $date_data = '';
        }
        $body = str_replace('{created_date_value}',$date_data, $body);

        // when we have a simple document, view only the info not any buttons.
        if ($has_no_file){
            $body = str_replace('{url_download}', $no_file_info, $body);
            $body = str_replace('{mirror_1}', '', $body);
            $body = str_replace('{mirror_2}', '', $body);
        } else {
            // only view download link when user has correct access level
            if ($this->item->params->get('access-download') == true){     
                $blank_window = '';
                $blank_window1 = '';
                $blank_window2 = '';
                // get file extension
                $view_types = array();
                $view_types = explode(',', $jlistConfig['file.types.view']);
                $only_file_name = basename($this->item->url_download);
                $this->itemextension = JDHelper::getFileExtension($only_file_name);
                if (in_array($this->itemextension, $view_types)){
                    $blank_window = 'target="_blank"';
                }    
                // check is set link to a new window?
                if ($this->item->extern_file && $this->item->extern_site   ){
                    $blank_window = 'target="_blank"';
                }
                // is 'direct download' activated?
                if ($jlistConfig['direct.download'] == '0'){ 
                    // when not, we must link to the summary page
                    $url_task = 'summary';
                    $blank_window = '';
                    $download_link = JRoute::_(JDownloadsHelperRoute::getOtherRoute($this->item->slug, $this->item->cat_id, $this->item->language, $url_task));
                } else {
                    if ($this->item->license_agree || $this->item->password || $this->user_rules->view_captcha) {
                         // user must agree the license - fill out a password field - or fill out the captcha human check - so we must view the summary page!
                        $url_task = 'summary';
                        $download_link = JRoute::_(JDownloadsHelperRoute::getOtherRoute($this->item->slug, $this->item->cat_id, $this->item->language, $url_task));
                    } else {     
                        // start the download promptly
                        $url_task = 'download.send';
                        $download_link = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$this->item->file_id.'&amp;catid='.$this->item->cat_id.'&amp;m=0');
                    }
                } 
                
                if ($url_task == 'download.send'){
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){   
                        $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.details'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" /></a>';
                    } else {
                        // we use the new css button
                         $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" class="jdbutton '.$download_color.' '.$download_size.'">'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a>';
                    }    
                } else {
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){ 
                        $download_link_text = '<a href="'.$download_link.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.details'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" /></a>';
                    } else {
                        // we use the new css button                    
                        $download_link_text = '<a '.$blank_window.' href="'.$download_link.'" class="jdbutton '.$download_color.' '.$download_size.'">'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a>';
                    }    
                }
                $body = str_replace('{url_download}', $download_link_text, $body);
                
                // mirrors
                if ($this->item->mirror_1) {
                    if ($this->item->extern_site_mirror_1 && $url_task == 'download.send'){
                        $blank_window1 = 'target="_blank"';
                    }
                    $mirror1_link_dum = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$this->item->file_id.'&amp;catid='.$this->item->cat_id.'&amp;m=1');
                    //$mirror1_link_dum = JRoute::_(JDownloadsHelperRoute::getOtherRoute($this->item->slug, $this->item->cat_id, $this->item->language, $url_task, 1));
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){                
                        $mirror1_link = '<a '.$blank_window1.' href="'.$mirror1_link_dum.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.mirror_1'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_1').'" /></a>';
                    } else {
                        // we use the new css button 
                        $mirror1_link = '<a '.$blank_window1.' href="'.$mirror1_link_dum.'" class="jdbutton '.$download_color_mirror1.' '.$download_size_mirror.'">'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_1').'</a>'; 
                    }    
                    $body = str_replace('{mirror_1}', $mirror1_link, $body);
                } else {
                    $body = str_replace('{mirror_1}', '', $body);
                }
                if ($this->item->mirror_2) {
                    if ($this->item->extern_site_mirror_2 && $url_task == 'download.send'){
                        $blank_window2 = 'target="_blank"';
                    }            
                    $mirror2_link_dum = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$this->item->file_id.'&amp;catid='.$this->item->cat_id.'&amp;m=2');
                    //$mirror2_link_dum = JRoute::_(JDownloadsHelperRoute::getOtherRoute($this->item->slug, $this->item->cat_id, $this->item->language, $url_task, 2));
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){                
                        $mirror2_link = '<a '.$blank_window2.' href="'.$mirror2_link_dum.'" class="jd_download_url"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.mirror_2'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_2').'" /></a>';
                    } else {
                        // we use the new css button 
                        $mirror2_link = '<a '.$blank_window2.' href="'.$mirror2_link_dum.'" class="jdbutton '.$download_color_mirror2.' '.$download_size_mirror.'">'.JText::_('COM_JDOWNLOADS_FRONTEND_MIRROR_URL_TITLE_2').'</a>'; 
                    }                
                    $body = str_replace('{mirror_2}', $mirror2_link, $body);
                } else {
                    $body = str_replace('{mirror_2}', '', $body);
                }            
            } else {
                // visitor has not access to download this item - so we will inform him
                
                if (!$user->guest){
                    // user is always logged in but has no access - so add a special info that only special members has access
                    $regg = JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED2');
                } else {
                    $regg = JText::_('COM_JDOWNLOADS_FRONTEND_FILE_ACCESS_REGGED');
                }
                
                // when CSS3 buttons are activate, we use it also for the message
                if ($jlistConfig['use.css.buttons.instead.icons']){               
                    $regg = '<div class="'.$jlistConfig['css.button.color.download'].' '.$jlistConfig['css.button.size.download'].'">'.$regg.'</div>';
                }     
                $body = str_replace('{url_download}', $regg, $body);
                $body = str_replace('{mirror_1}', '', $body); 
                $body = str_replace('{mirror_2}', '', $body); 
            }    
        }
        // build website url
        if (!$this->item->url_home == '') {
             if (strpos($this->item->url_home, 'http://') !== false) {    
                 $body = str_replace('{author_url_text}', '<a href="'.$this->item->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$extern_url_pic, $body);
             } else {
                 $body = str_replace('{author_url_text}', '<a href="http://'.$this->item->url_home.'" target="_blank" title="'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'">'.JText::_('COM_JDOWNLOADS_FRONTEND_HOMEPAGE').'</a> '.$extern_url_pic, $body);
             }    
        } else {
            $body = str_replace('{author_url_text}', '', $body);
        }

        // encode is link a mail
        $link_author = '';
        if (strpos($this->item->url_author, '@') && $jlistConfig['mail.cloaking']){
            if (!$this->item->author) { 
                $mail_encode = JHtml::_('email.cloak',$this->item->url_author);
            } else {
                $mail_encode = JHtml::_('email.cloak',$this->item->url_author, true, $this->item->author, false);
            }        
        }
                        
        // build author link
        if ($this->item->author <> ''){
             if ($this->item->url_author <> '') {
                  if ($mail_encode) {
                      $link_author = $mail_encode;
                  } else {
                      if (strpos($this->item->url_author, 'http://') !== false) {
                         $link_author = '<a href="'.$this->item->url_author.'" target="_blank">'.$this->item->author.'</a> '.$extern_url_pic;
                      } else {
                         $link_author = '<a href="http://'.$this->item->url_author.'" target="_blank">'.$this->item->author.'</a>  '.$extern_url_pic;
                      }        
                  }
                  $body = str_replace('{author_text}',$link_author, $body);
                  $body = str_replace('{url_author}', '', $body);
             } else {
                  $link_author = $this->item->author;
                  $body = str_replace('{author_text}',$link_author, $body);
                  $body = str_replace('{url_author}', '', $body);
             }
        } else {
            $body = str_replace('{url_author}', $this->item->url_author, $body);
            $body = str_replace('{author_text}','', $body);
        }

        // set system value
        $this->item_sys_values = explode(',' , JDHelper::getOnlyLanguageSubstring($jlistConfig['system.list']));
        if ($this->item->system == 0 ) {
             $body = str_replace('{system_text}', '', $body);
        } else {
             $body = str_replace('{system_text}', $this->item_sys_values[$this->item->system], $body);
        }

        // set language value
        $this->item_lang_values = explode(',' , JDHelper::getOnlyLanguageSubstring($jlistConfig['language.list']));
        if ($this->item->file_language == 0 ) {
            $body = str_replace('{language_text}', '', $body);
        } else {
            $body = str_replace('{language_text}', $this->item_lang_values[$this->item->file_language], $body);
        }
        
        // media player
        if ($this->item->preview_filename){
            // we use the preview file when exist  
            $is_preview = true;
            $this->item->itemtype = JDHelper::getFileExtension($this->item->preview_filename);
            $is_playable    = JDHelper::isPlayable($this->item->preview_filename);
        } else {                  
            $is_preview = false;
            if ($this->item->extern_file){
                $extern_media = true;
                $this->item->itemtype = JDHelper::getFileExtension($this->item->extern_file);
                $is_playable    = JDHelper::isPlayable($this->item->extern_file);
            } else {    
                $this->item->itemtype = JDHelper::getFileExtension($this->item->url_download);
                $is_playable    = JDHelper::isPlayable($this->item->url_download);
                $extern_media = false;
            }  
        }
            
        if ( !$jlistConfig['flowplayer.use'] && !$jlistConfig['html5player.use'] && $this->item->itemtype == 'mp3' ){
            // we use only the 'OLD' mp3 player
            if ($extern_media){
                $mp3_path = $this->item->extern_file;
            } else {        
                if ($is_preview){
                    // we need the path to the "previews" folder
                    $mp3_path = JUri::base().$jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$this->item->preview_filename;
                } else {
                    // we use the normal download file for the player
                    $mp3_path = JUri::base().$jdownloads_root_dir_name.'/'.$category_dir.'/'.$this->item->url_download;
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
            
            if (strpos($body, '{mp3_player}')){
                $body = str_replace('{mp3_player}', $mp3_player, $body);
                $body = str_replace('{preview_player}', '', $body);
            } else {                
                $body = str_replace('{preview_player}', $mp3_player, $body);
            }            
        } 
        
        if ( $is_playable ){
            
               if ($jlistConfig['html5player.use']){
                    // we will use the new HTML5 player option
                    if ($extern_media){
                        $media_path = $this->item->extern_file;
                    } else {        
                        if ($is_preview){
                            // we need the relative path to the "previews" folder
                            $media_path = $jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$this->item->preview_filename;
                        } else {
                            // we use the normal download file for the player
                            $media_path = $jdownloads_root_dir_name.'/'.$category_dir.'/'.$this->item->url_download;
                        }   
                    }    
                            
                    // create the HTML5 player
                    $player = JDHelper::getHTML5Player($this->item, $media_path);
                    
                    if ($this->item->itemtype == 'mp4' || $this->item->itemtype == 'webm' || $this->item->itemtype == 'ogg' || $this->item->itemtype == 'ogv' || $this->item->itemtype == 'mp3' || $this->item->itemtype == 'wav' || $this->item->itemtype == 'oga'){
                        // We will replace at first the old placeholder when exist
                        if (strpos($body, '{mp3_player}')){
                            $body = str_replace('{mp3_player}', $player, $body);
                            $body = str_replace('{preview_player}', '', $body);
                        } else {                
                            $body = str_replace('{preview_player}', $player, $body);
                        }    
                    } else {
                        $body = str_replace('{mp3_player}', '', $body);        
                        $body = str_replace('{preview_player}', '', $body);       
                    }
                
                } else {
            
                    if ( $jlistConfig['flowplayer.use'] ){
                        // we will use the new flowplayer option
                        if ($extern_media){
                            $media_path = $this->item->extern_file;
                        } else {        
                            if ($is_preview){
                                // we need the relative path to the "previews" folder
                                $media_path = $jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$this->item->preview_filename;
                            } else {
                                // we use the normal download file for the player
                                $media_path = $jdownloads_root_dir_name.'/'.$category_dir.'/'.$this->item->url_download;
                            }   
                        }    

                        $ipadcode = '';
                        
                        if ($this->item->itemtype == 'mp3'){
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
                        
                        $player = '<a href="'.$media_path.'" style="display:block;width:'.$jlistConfig['flowplayer.playerwidth'].'px;height:'.$playerheight.'px;" id="player" class="player"></a>';
                        $player .= '<script language="JavaScript">
                        // install flowplayer into container
                                    flowplayer("player", "'.JURI::base().'components/com_jdownloads/assets/flowplayer/flowplayer-3.2.16.swf",
                                     {
             
                            plugins: {
                                controls: {
                                    // insert at first the config settings
                                    '.$jlistConfig['flowplayer.control.settings'].'
                                    // and now the basics
                                    fullscreen: '.$fullscreen.',
                                    height: '.(int)$jlistConfig['flowplayer.playerheight.audio'].',
                                    autoHide: '.$autohide.'
                                }
                                
                            },

                            clip: {
                                autoPlay: false,
                                // optional: when playback starts close the first audio playback
                                onBeforeBegin: function() {
                                    $f("player").close();
                                }
                            }
                        })'.$ipadcode.'; </script>'; // the 'ipad code' is only required for ipad/iphone users 

                        if ($this->item->itemtype == 'mp4' || $this->item->itemtype == 'flv' || $this->item->itemtype == 'mp3'){    
                            // We will replace at first the old placeholder when exist
                            if (strpos($body, '{mp3_player}')){
                                $body = str_replace('{mp3_player}', $player, $body);
                                $body = str_replace('{preview_player}', '', $body);
                            } else {
                                $body = str_replace('{preview_player}', $player, $body);
                            }                                
                        } else {
                            $body = str_replace('{mp3_player}', '', $body);
                            $body = str_replace('{preview_player}', '', $body);
                        }                        
                    
                    }
               }
        } 
            
        if ($jlistConfig['mp3.view.id3.info'] && $this->item->itemtype == 'mp3' && !$extern_media){
            // read mp3 infos
            if ($is_preview){
                // get the path to the preview file
                $mp3_path_abs = $jlistConfig['files.uploaddir'].DS.$jlistConfig['preview.files.folder.name'].DS.$this->item->preview_filename;
            } else {
                // get the path to the downloads file
                $mp3_path_abs = $jlistConfig['files.uploaddir'].DS.$category_dir.DS.$this->item->url_download;
            }
            $info = JDHelper::getID3v2Tags($mp3_path_abs);           
            if ($info){
                // add it
                $mp3_info = stripslashes($jlistConfig['mp3.info.layout']);
                $mp3_info = str_replace('{name_title}', JText::_('COM_JDOWNLOADS_FE_VIEW_ID3_TITLE'), $mp3_info);
                if ($is_preview){
                    $mp3_info = str_replace('{name}', $this->item->preview_filename, $mp3_info);
                } else {
                    $mp3_info = str_replace('{name}', $this->item->url_download, $mp3_info);
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
                $body = str_replace('{mp3_id3_tag}', $mp3_info, $body); 
            }     
        }        
    
        $body = str_replace('{mp3_player}', '', $body);
        $body = str_replace('{preview_player}', '', $body);
        $body = str_replace('{mp3_id3_tag}', '', $body);             

        // replace the {preview_url}
        if ($this->item->preview_filename){
            // we need the relative path to the "previews" folder
            $media_path = $jdownloads_root_dir_name.'/'.$jlistConfig['preview.files.folder.name'].'/'.$this->item->preview_filename;
            $body = str_replace('{preview_url}', $media_path, $body);
        } else {
            $body = str_replace('{preview_url}', '', $body);
        }         
        
        // insert rating system
        if ($jlistConfig['view.ratings']){
            $rating_system = JDHelper::getRatings($this->item->file_id, $this->item->rating_count, $this->item->rating_sum);
            $body = str_replace('{rating}', $rating_system, $body);
            $body = str_replace('{rating_title}', JText::_('COM_JDOWNLOADS_RATING_LABEL'), $body);
        } else {
            $body = str_replace('{rating}', '', $body);
            $body = str_replace('{rating_title}', '', $body);
        } 

        // remove empty html tags
        if ($jlistConfig['remove.empty.tags']){
            $body = JDHelper::removeEmptyTags($body);
        }
             
        // Option for JComments integration
        if ($jlistConfig['jcomments.active']){
            $jcomments = JPATH_BASE.'/components/com_jcomments/jcomments.php';
            if (file_exists($jcomments)) {
                require_once($jcomments);
                $obj_id = $this->item->file_id;
                $obj_title = $this->item->file_title;
                $body .= JComments::showComments($obj_id, 'com_jdownloads', $obj_title);
            }    
        }
        
        $html .= $body; 
    }    

    // ==========================================
    // FOOTER SECTION  
    // ==========================================

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
    $html   .= $footer; 
    
    $html .= '</div>';

    // support for global content plugins - used only when the usage is not limited to the description.
    if ($jlistConfig['activate.general.plugin.support'] && !$jlistConfig['use.general.plugin.support.only.for.descriptions']) {  
        $html = JHtml::_('content.prepare', $html);
    }

    // ==========================================
    // VIEW THE BUILDED OUTPUT
    // ==========================================

    if ( !$jlistConfig['offline'] ) {
            echo $this->item->event->beforeDisplayContent;
            echo $html;
            echo $this->item->event->afterDisplayContent;
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