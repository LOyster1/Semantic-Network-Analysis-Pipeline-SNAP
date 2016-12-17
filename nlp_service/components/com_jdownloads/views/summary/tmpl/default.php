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
    
    $password_used  = false;
    $password_valid = false;
    $password_invalid_msg = '';
    
    $captcha_valid  = false;
    $captcha_invalid_msg = '';
    
    // Check at first whether we have a single download and it is used the files password option
    // If so, then can we not use Captcha
    if ($this->state->get('download.id') && $this->items[0]->password_md5 != ''){
        $password_used = true;
        JDHelper::writeSessionEncoded('1', 'jd_password_run');
        $password_input = $jinput->getString('password_input', '');
        if ($password_input != ''){
            if (hash('sha256', $password_input) == $this->items[0]->password_md5){
                $password_valid = true;
                JDHelper::writeSessionEncoded('2', 'jd_password_run');
            } else {
                $password_invalid_msg = JText::_('COM_JDOWNLOADS_PASSWORD_INVALID');
            }    
        }
        // we need this switch to handle the data output 
        $captcha_valid = true;
        JDHelper::writeSessionEncoded('0', 'jd_captcha_run');
    } else {
        // captcha check
        if ($this->user_rules->view_captcha){
            // get captcha plugin
            JPluginHelper::importPlugin('captcha');
            $plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');

            // Get plugin param
            $pluginParams = new JRegistry($plugin->params);
            $captcha_version = $pluginParams->get('version');
            
            $dispatcher = JDispatcher::getInstance();
            $dummy = $jinput->getString('g-recaptcha-response');
            if (!$dummy) $dummy = $jinput->getString('recaptcha_response_field');
               
            // check now whether user has used the captcha already
            if (isset($dummy)){
                $captcha_res = $dispatcher->trigger('onCheckAnswer', $dummy);
                if (!$captcha_res[0]){
                    JDHelper::writeSessionEncoded('1', 'jd_captcha_run');
                    // init again for next try
                    $dispatcher->trigger('onInit','dynamic_recaptcha_1');
                    $captcha_invalid_msg = JText::_('COM_JDOWNLOADS_FIELD_CAPTCHA_INCORRECT_HINT');
                } else {
                    JDHelper::writeSessionEncoded('2', 'jd_captcha_run');
                    $captcha_valid = true;
                }
            } else {
                // init for first try
                JDHelper::writeSessionEncoded('1', 'jd_captcha_run');
                $exist_event = $dispatcher->trigger('onInit','dynamic_recaptcha_1');
                
                // When plugin event not exist, we must do the work without it. But give NOT a public info about this problem.
                if (!$exist_event){
                    $captcha_valid = true;
                    JDHelper::writeSessionEncoded('2', 'jd_captcha_run');
                }
            }
        } else {
            // we need this switch to handle the data output 
            $captcha_valid = true;
        }
        // not used - so must set it to true
        $password_valid = true;
    }    
          
    
    // for Tabs
    jimport('joomla.html.pane');
    // for Tooltip
    JHtml::_('behavior.tooltip');
    // required for captcha
    JHtml::_('behavior.keepalive');
    
    // required for captcha
    $form_uri = JFactory::getURI();
    $form_uri = $form_uri->toString();
    $form_uri = $this->escape($form_uri);
    
    // Create shortcuts to some parameters.
    $params               = $this->items[0]->params;
    $files                = $this->items;
    $user_rules           = $this->user_rules;
    $is_mirror            = $this->state->get('download.mirror.id');
    $fileid               = $this->state->get('download.id');
    $catid                = $this->state->get('download.catid');
    $sum_selected_files   = $this->state->get('sum_selected_files');
    $sum_selected_volume  = $this->state->get('sum_selected_volume');
    $sum_files_prices     = $this->state->get('sum_files_prices');
    $must_confirm_license = $this->state->get('must_confirm_license');
    $directlink           = $this->state->get('directlink_used');
    $marked_files_id      = $this->state->get('download.marked_files.id');
        
    $html               = '';
    $footer_text        = '';
    $layout             = '';
    $license_text       = '';
    $countdown          = '';
    $zip_file_info      = '';
    $sum_aup_points     = $sum_files_prices;
    $aup_valid          = true;
    $user_random_id     = 0;
    $is_admin           = false;
    $has_licenses       = false;
    $must_confirm       = false;
    $extern_site        = false;
    $open_in_blank_page = false;
    $directlink         = false;
    $total_consumed     = false;
    $may_download       = false;
    $zip_files_array    = array();
    
    // alternate CSS buttons when selected in configuration
    $status_color_hot       = $jlistConfig['css.button.color.hot'];
    $status_color_new       = $jlistConfig['css.button.color.new'];
    $status_color_updated   = $jlistConfig['css.button.color.updated'];
    $download_color         = $jlistConfig['css.button.color.download'];
    $download_size          = $jlistConfig['css.button.size.download'];
    $download_size_mirror   = $jlistConfig['css.button.size.download.mirror'];        
    $download_color_mirror1 = $jlistConfig['css.button.color.mirror1'];        
    $download_color_mirror2 = $jlistConfig['css.button.color.mirror2'];     

    if (JDHelper::checkGroup('8', true) || JDHelper::checkGroup('7', true)){
        $is_admin = true;
    }
    
    // build random value for zip filename
    if (count($files) > 1) {
        $user_random_id = JDHelper::buildRandomNumber();
    }        
    
    // we need the filed id when not used checkboxes
    if (!$marked_files_id){
        $marked_files_id = array($fileid);
    }
    $marked_files_id_string = implode(',', $marked_files_id);
    
    // We must compute up to this point, what this user has downloaded before and compare it then later with the defined user limitations 
    // Important: Please note, that we can check it only for registered users. By visitors it is not really useful, then we have here only a changeable IP.  

    $total_consumed = JDHelper::getUserLimits($user_rules, $marked_files_id);
    
    // When $total_consumed['limits_info'] has a value, we must check whether this user may download the selected files
    // If so, then the result is: TRUE - otherwise: the limitations message
    // Has $total_consumed['limits_info'] not any value, it exists not any limitations for this user  

    if ($total_consumed['limits_info']){ 
        $may_download = JDHelper::checkUserDownloadLimits($user_rules, $total_consumed, $sum_selected_files, $sum_selected_volume, $marked_files_id);
    } else {
        $may_download = true;
    }
    
    // check whether user has enough points from alphauserpoints (when used and installed)                
    if ($may_download === true && $jlistConfig['use.alphauserpoints']){
        $aup_result = JDHelper::checkUserPoints($sum_aup_points, $marked_files_id);
        if ($aup_result['may_download'] === true){
            $may_download = true;
        } else {
            $may_download = $aup_result['points_info']; 
        }    
    }    
    
    
    // write data in session
    if ($may_download === true){
        if ($user_random_id){    
            JDHelper::writeSessionEncoded($user_random_id, 'jd_random_id');
            JDHelper::writeSessionEncoded($marked_files_id_string, 'jd_list');
            JDHelper::writeSessionClear('jd_fileid');
        } else {
            // single file download
            if ($fileid){
                JDHelper::writeSessionEncoded($fileid, 'jd_fileid');    
            } else {
                JDHelper::writeSessionEncoded($marked_files_id[0], 'jd_fileid');    
            }
            JDHelper::writeSessionClear('jd_random_id');
            JDHelper::writeSessionClear('jd_list');                        
        }
        JDHelper::writeSessionEncoded($catid, 'jd_catid');
    }                    
    
    // Get the needed layout data - type = 3 for a 'summary' layout            
    $layout = JDHelper::getLayout(3, false);
    if ($layout){
        $layout_text = $layout->template_text;
        $header      = $layout->template_header_text;
        $subheader   = $layout->template_subheader_text;
        $footer      = $layout->template_footer_text;
    } else {
        // We have not a valid layout data
        echo '<big>No valid layout found!</big>';
    }
    
    // get current category menu ID when exist and all needed menu IDs for the header links
    $menuItemids = JDHelper::getMenuItemids($catid);
    
    // get all other menu category IDs so we can use it when we need it
    $cat_link_itemids = JDHelper::getAllJDCategoryMenuIDs();
    
    // "Home" menu link itemid
    $root_itemid =  $menuItemids['root'];

    $Itemid = JDHelper::getSingleCategoryMenuID($cat_link_itemids, $catid, $root_itemid);
    
    $html = '<div class="jd-item-page'.$this->pageclass_sfx.'">';
        
    if ($this->params->get('show_page_heading')) {
        $html .= '<h1>'.$this->escape($this->params->get('page_heading')).'</h1>';
    }            
    
    // ==========================================
    // HEADER SECTION
    // ==========================================

    if ($header != ''){
        
        // component title
        $header = str_replace('{component_title}', $document->getTitle('title'), $header);
        
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
            
            $orderby_pri = '';
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
            $subheader = str_replace('{summary_title}', JText::_('COM_JDOWNLOADS_FRONTEND_HEADER_SUMMARY_TITLE'), $subheader);
        }    

        // remove this placeholder when it is used not for files layout
        $subheader = str_replace('{summary_title}', '', $subheader); 
        
        // replace google adsense placeholder with script when active (also for subheader tab)
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                $subheader = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $subheader);
        } else {
                $subheader = str_replace( '{google_adsense}', '', $subheader);
        }         
 
        $html .= $subheader;            
    }
    
    // ==========================================
    // BODY SECTION - VIEW THE DOWNLOADS DATA
    // ==========================================
    
    $html_files = '';
    $id_text = '';

    if ($layout_text != ''){
    
        $event = $this->event->beforeDisplayContent;        
        
        $html_sum = $event.$layout_text;

        // summary pic
        $sumpic = '<img src="'.JURI::base().'components/com_jdownloads/assets/images/summary.png" width="'.$jlistConfig['cat.pic.size'].'" height="'.$jlistConfig['cat.pic.size.height'].'" style="border:0px;" alt="summary" /> ';
        $html_sum = str_replace('{summary_pic}', $sumpic, $html_sum);    
                
        // info text
        $html_sum = str_replace('{title_text}', JText::_('COM_JDOWNLOADS_FE_SUMMARY_PAGE_TITLE_TEXT'), $html_sum);
        
        // ==============================================================================
        // User may not download this files - limits reached. So we view only the message
        // ==============================================================================
        if ($may_download !== true){
           $html_sum = str_replace('{download_link}', $may_download, $html_sum);
           
            // google adsense
            if ($jlistConfig['google.adsense.active']){
                $html_sum = str_replace('{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $html_sum);
            } else {
                $html_sum = str_replace('{google_adsense}', '', $html_sum);
            }            
           
            // remove all other (not used) place holders
            $html_sum = str_replace('{info_zip_file_size}', '', $html_sum);
            $html_sum = str_replace('{license_text}', '', $html_sum);
            $html_sum = str_replace('{license_title}', '', $html_sum);
            $html_sum = str_replace('{license_checkbox}', '', $html_sum);
            $html_sum = str_replace('{download_liste}', '', $html_sum);
            $html_sum = str_replace('{external_download_info}', '', $html_sum);
            $html_sum = str_replace('{aup_points_info}', '', $html_sum);
            $html_sum = str_replace('{captcha}', '', $html_sum);
            $html_sum = str_replace('{password}', '', $html_sum);
           
        } else {
            // ============================
            // user may download this files            
            // ============================
            $mail_files = '<div class="jd_summary_list"><ul>';

            // when exists - no checkbox was used  
            if ($fileid){
                $directlink = true;
                $id_text = $fileid;        
                $filename = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&amp;id='.$fileid.'&amp;catid='.$catid.'&amp;m='.$is_mirror.'&amp;Itemid='.$Itemid);
                if ($files[0]->license && $files[0]->license_agree) $must_confirm = true;
                $download_link = $filename;
                $file_title = ' - '.$files[0]->file_title;       
            }
            
            // move in text for view the files list
            $anz = 0;
            if (!$id_text){
                $anz = count($marked_files_id);
                if ( $anz > 1 ){
                   $id_text = implode(',', $marked_files_id);
                } else {
                   $id_text = $marked_files_id[0];
                }
            }                 
            
            // add password protection when used but then is not possible to use the captcha
            if ($password_used){
                if ($password_valid === false){
                        $password = '<div id="jd_container" class="jd_password">';
                        if ($password_invalid_msg == ''){
                            $password .= JText::_('COM_JDOWNLOADS_PASSWORD_DESC');
                        } else {
                            $password .= $password_invalid_msg;
                        }  
                        $password .= '<form action="'.$form_uri.'" method="post" id="summary" class="form-validate" enctype="multipart/form-data" accept-charset="utf-8">';
                        $password .= '<p>'.JText::_('COM_JDOWNLOADS_PASSWORD_LABEL').':&nbsp;<input type="text" name="password_input" size="20" value=""></p>';
                        $password .= '<input type="hidden" name="f_file_id" value="'.$fileid.'">';
                        $password .= '<input type="hidden" name="f_cat_id" value="'.$catid.'">';
                        $password .= '<input type="hidden" name="f_marked_files_id" value="'.implode(',',$marked_files_id).'">';
                        $password .= '<br /><input type="submit" name="submit" id="jd_password" class="button" value="'.JText::_('COM_JDOWNLOADS_FORM_BUTTON_TEXT').'" />';
                        $password .= JHtml::_('form.token').'</form></div>';
                        $html_sum = str_replace('{password}', $password, $html_sum);
                    
                } else {
                    $html_sum = str_replace('{password}', '', $html_sum);
                }
                $html_sum = str_replace('{captcha}', '', $html_sum);
            } else {
                // add captcha option when required
                if ($this->user_rules->view_captcha){
                    if ($captcha_valid === false){
                        // html code inside form tag
                        // we use for the form action the request_uri
                        $captcha = '<div id="jd_container" class="jd_recaptcha">';
                        if ($captcha_invalid_msg == ''){
                            if ($captcha_version == '1.0'){
                                $captcha .= JText::_('COM_JDOWNLOADS_FIELD_CAPTCHA_HINT');
                            } elseif ($captcha_version == '2.0'){
                                $captcha .= JText::_('COM_JDOWNLOADS_FIELD_CAPTCHA_HINT_VERSION_2');
                            }    
                        }    
                        $captcha .= '<form action="'.$form_uri.'" method="post" id="summary" class="form-validate" enctype="multipart/form-data" accept-charset="utf-8">';
                        $captcha .= '<div id="dynamic_recaptcha_1">&#160;</div>';
                        $captcha .= '<input type="hidden" name="f_file_id" value="'.$fileid.'">';
                        $captcha .= '<input type="hidden" name="f_cat_id" value="'.$catid.'">';
                        $captcha .= '<input type="hidden" name="f_marked_files_id" value="'.implode(',',$marked_files_id).'">';
                        $captcha .= '<br /><input type="submit" name="submit" id="jd_captcha" class="button" value="'.JText::_('COM_JDOWNLOADS_FORM_BUTTON_TEXT').'" />';
                        if ($captcha_invalid_msg != ''){
                            $captcha .= $captcha_invalid_msg;
                        } 
                        $captcha .= JHtml::_('form.token').'</form></div>';
                        $html_sum = str_replace('{captcha}', $captcha, $html_sum);
                    } else {
                        $html_sum = str_replace('{captcha}', '', $html_sum);
                    }   
                } else {
                    $html_sum = str_replace('{captcha}', '', $html_sum);
                }
                $html_sum = str_replace('{password}', '', $html_sum);   
            }
            
            // build the information list about the selected files
            for ($i=0; $i<count($files); $i++){
               // get license name
               if ($files[$i]->license > 0){  
                   $has_licenses = true;
                   if ($files[$i]->license_agree){
                       $must_confirm = true;
                       $license_text = stripslashes($files[$i]->license_text);
                   } 
                   
                   if ($files[$i]->license_url){
                       // add license link
                       $mail_files .= "<div><li><b>".$files[$i]->file_title.' '.$files[$i]->release.'&nbsp;&nbsp;&nbsp;</b>'.JText::_('COM_JDOWNLOADS_FE_DETAILS_LICENSE_TITLE').': <b><a href="'.$files[$i]->license_url.'" target="_blank">'.$files[$i]->license_title.'</a></b>&nbsp;&nbsp;&nbsp;'.JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE').': <b>'.$files[$i]->size.'</b></li></div>';
                   } else {
                       $mail_files .= "<div><li><b>".$files[$i]->file_title.' '.$files[$i]->release.'&nbsp;&nbsp;&nbsp;</b>'.JText::_('COM_JDOWNLOADS_FE_DETAILS_LICENSE_TITLE').': <b>'.$files[$i]->license_title.'&nbsp;&nbsp;&nbsp;</b>'.JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE').': <b>'.$files[$i]->size.'</b></li></div>';
                   }   
               } else {
                   $mail_files .= "<div><li><b>".$files[$i]->file_title.' '.$files[$i]->release.'&nbsp;&nbsp;&nbsp;</b>'.JText::_('COM_JDOWNLOADS_FE_DETAILS_FILESIZE_TITLE').': <b>'.$files[$i]->size.'</b></li></div>';
               }     
            }
            
            $mail_files .= "</ul></div>";         
            $html_sum = str_replace('{download_liste}', $mail_files, $html_sum);
            
            // set flag when link must opened in a new browser window 
            if (!$is_mirror && $i == 1 && $files[0]->extern_site){
                $extern_site = true;    
            }
            if ($is_mirror == 1 && $i == 1 && $files[0]->extern_site_mirror_1){
                $extern_site = true;    
            }
            if ($is_mirror == 2 && $i == 1 && $files[0]->extern_site_mirror_2){
                $extern_site = true;    
            }
            // get file extension  when only one file selected - set flag when link must opened in a new browser window 
            if (count($files) == 1 && $files[0]->url_download) {
                $view_types = array();
                $view_types = explode(',', $jlistConfig['file.types.view']);
                $fileextension = strtolower(substr(strrchr($files[0]->url_download,"."),1));
                if (in_array($fileextension, $view_types)){
                    $open_in_blank_page = true;
                }
            }
            
            // when mass download with checkboxes
            if (!$directlink){ 
                // more as one file is selected - zip it in a temp file
                $download_dir = $jlistConfig['files.uploaddir'].'/';
                $zip_dir = $jlistConfig['files.uploaddir'].'/'.$jlistConfig['tempzipfiles.folder.name'].'/';
                
                if (count($files) > 1) {
                    
                    for ($i=0; $i<count($files); $i++) {
                        // get file url
                        $filename = $files[$i]->url_download;
                        if ($files[$i]->category_cat_dir_parent){
                            $cat_dir = $files[$i]->category_cat_dir_parent.'/'.$files[$i]->category_cat_dir.'/';
                        } else {
                            $cat_dir = $files[$i]->category_cat_dir.'/';
                        }     
                        if ($files[$i]->url_download != ''){
                            $zip_files_array[] = $download_dir.$cat_dir.$filename;
                        }
                    }
                    $zip_destination = $zip_dir.$jlistConfig['zipfile.prefix'].$user_random_id.'.zip';
                    
                    // create the temp zip file
                    $success  = JDHelper::createZipFile($zip_files_array, $zip_destination, true);
                    // if not success display error
                    if (!$success){
                        $html_sum = str_replace('{info_zip_file_size}', JText::_('COM_JDOWNLOADS_FRONTEND_SUMMARY_ZIP_ERROR'), $html_sum); 
                        $html_sum = str_replace('{download_link}', '', $html_sum); 
                    } else {
                        // success 
                        $zip_size = JDHelper::getFileSize($zip_destination);
                        $zip_file_info = JText::_('COM_JDOWNLOADS_FRONTEND_SUMMARY_ZIP_FILESIZE').': <b>'.$zip_size.'</b>';
                        
                        // delete before older temporary zip files
                        $del_ok = JDHelper::deleteOldZipFiles($zip_dir);
                        
                        $download_link = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&catid='.$catid.'&list='.$id_text.'&amp;user='.$user_random_id.'&amp;Itemid='.$Itemid); 
                    }
                } else {
                    // only one file selected
                    $download_link = JRoute::_('index.php?option=com_jdownloads&amp;task=download.send&id='.(int)$files[0]->file_id.'&catid='.$files[0]->cat_id.'&amp;Itemid='.$Itemid);
                    $file_title = ' - '.$files[0]->file_title;
                }
            }
            
            // info about temp zip file size (when used)
            $html_sum = str_replace('{info_zip_file_size}', $zip_file_info, $html_sum);        
                
            // google adsense
            if ($jlistConfig['google.adsense.active']){
                $html_sum = str_replace('{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $html_sum);
            } else {
                $html_sum = str_replace('{google_adsense}', '', $html_sum);
            }    
            
            // build countdown timer
            if ($user_rules->countdown_timer_duration > 0 && $user_rules->countdown_timer_msg != ''){
                $countdown_msg = JDHelper::getOnlyLanguageSubstring($user_rules->countdown_timer_msg);
                $countdown = '<script type="text/javascript"> counter='.(int)$user_rules->countdown_timer_duration.'; active=setInterval("countdown2()",1000);
                               function countdown2(){
                                  if (counter >0){
                                      counter-=1;
                                      document.getElementById("countdown").innerHTML=sprintf(\''.$countdown_msg.'\',counter);
                                  } else {
                                      document.getElementById("countdown").innerHTML=\''.'{link}'.'\'
                                      window.clearInterval(active);
                                  }    
                                } </script>';
            }

            // view AlphaUserPoints result
            if ($jlistConfig['use.alphauserpoints']){
                $html_sum = str_replace('{aup_points_info}', $aup_result['points_info'], $html_sum); 
            } else {
                $html_sum = str_replace('{aup_points_info}', '', $html_sum); 
            }    
            
           // we may view all other data only when this switches are true
           if ($captcha_valid && $password_valid){        
                 if (count($files) > 1) {
                    // mass download
                     if ($must_confirm){
                        $html_sum = str_replace('{license_title}','', $html_sum);
                        $html_sum = str_replace('{license_text}', '', $html_sum);
                        $agree_form = '<form action="'.$download_link.'" method="post" name="jd_agreeForm" id="jd_agreeForm" >';
                        $agree_form .= '<input type="checkbox" name="license_agree" onclick="enableDownloadButton(this)" /> '.JText::_('COM_JDOWNLOADS_FRONTEND_VIEW_AGREE_TEXT').'<br /><br />';
                        if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                            $agree_form .= '<input type="submit" name="submit" id="jd_license_submit" class="button" value="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" disabled="disabled" />';
                        } else {
                            $agree_form .= '<input type="submit" name="submit" id="jd_license_submit" class="jdbutton '.$download_color.' '.$download_size.'" value="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" disabled="disabled" />';
                        }    
                        $agree_form .= JHtml::_( 'form.token' )."</form>";
                    } else {
                        $html_sum = str_replace('{license_text}', '', $html_sum);
                        $html_sum = str_replace('{license_title}', '', $html_sum);
                        $html_sum = str_replace('{license_checkbox}', '', $html_sum);
                    }
                    
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){ 
                        $link = '<div id="countdown" style="text-align:center"><a href="'.$download_link.'" target="_self" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_ZIP').'"><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.details'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_ZIP').'" /></a></div>';
                    } else {
                        // we use the new css button                    
                        $link = '<div id="countdown" style="text-align:center"><a href="'.$download_link.'" target="_self" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_ZIP').'" class="jdbutton '.$download_color.' '.$download_size.'">'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a></div>'; 
                    }
                        
                    if ($countdown){
                       if ($must_confirm){
                           $countdown = str_replace('{link}', $agree_form, $countdown);
                           $html_sum = str_replace('{license_checkbox}', '<div id="countdown">'.$countdown.'</div>', $html_sum);
                           $html_sum = str_replace('{download_link}', '', $html_sum);
                       } else {
                             $countdown = str_replace('{link}', $link, $countdown);
                             $html_sum = str_replace('{download_link}', '<div id="countdown">'.$countdown.'</div>', $html_sum);
                       }    
                    } else {    
                       if ($must_confirm){
                           $html_sum = str_replace('{license_checkbox}', $agree_form, $html_sum);
                           $html_sum = str_replace('{download_link}', '', $html_sum);
                       } else {   
                           $html_sum = str_replace('{download_link}', $link, $html_sum);
                       }
                    }    
                    $html_sum = str_replace('{external_download_info}', '', $html_sum);
                } else {
                    // single download          
                    if ($must_confirm){
                        if ($license_text != ''){
                            $html_sum = str_replace('{license_title}', JText::_('COM_JDOWNLOADS_FE_SUMMARY_LICENSE_VIEW_TITLE'), $html_sum);
                            $html_sum = str_replace('{license_text}', '<div id="jd_license_text">'.$license_text.'</div>', $html_sum);
                        } else {
                            $html_sum = str_replace('{license_title}', '', $html_sum);
                            $html_sum = str_replace('{license_text}', '', $html_sum);
                        }    
                        $agree_form = '<form action="'.$download_link.'" method="post" name="jd_agreeForm" id="jd_agreeForm" >';
                        $agree_form .= '<input type="checkbox" name="license_agree" onclick="enableDownloadButton(this)" /> '.JText::_('COM_JDOWNLOADS_FRONTEND_VIEW_AGREE_TEXT').'<br /><br />';
                        if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){
                            $agree_form .= '<input type="submit" name="submit" id="jd_license_submit" class="button" value="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" disabled="disabled" />';
                        } else {
                            $agree_form .= '<input type="submit" name="submit" id="jd_license_submit" class="jdbutton '.$download_color.' '.$download_size.'" value="'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'" disabled="disabled" />';
                        }                        
                        $agree_form .= JHtml::_( 'form.token' )."</form>";
                    } else {
                        $html_sum = str_replace('{license_text}', '', $html_sum);
                        $html_sum = str_replace('{license_title}', '', $html_sum);
                        $html_sum = str_replace('{license_checkbox}', '', $html_sum);
                    }            
                     
                    if ($open_in_blank_page || $extern_site){
                        $targed = '_blank';
                        if ($extern_site){
                            $html_sum = str_replace('{external_download_info}', JText::_('COM_JDOWNLOADS_FRONTEND_DOWNLOAD_GO_TO_OTHER_SITE_INFO'), $html_sum);
                        } else {
                            $html_sum = str_replace('{external_download_info}', '', $html_sum);
                        }    
                    } else {
                        $targed = '_self';
                        $html_sum = str_replace('{external_download_info}', '', $html_sum);
                    }                    
                
                    // is the old button used?
                    if ($jlistConfig['use.css.buttons.instead.icons'] == '0'){ 
                        $link = '<div id="countdown" style="text-align:center"><a href="'.$download_link.'" target="'.$targed.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_ZIP').'" ><img src="'.JURI::base().'images/jdownloads/downloadimages/'.$jlistConfig['download.pic.details'].'" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_LINKTEXT_ZIP').'" /></a></div>'; 
                    } else {
                        // we use the new css button                    
                        $link = '<div id="countdown" style="text-align:center"><a href="'.$download_link.'" target="'.$targed.'" title="'.JText::_('COM_JDOWNLOADS_LINKTEXT_ZIP').'" class="jdbutton '.$download_color.' '.$download_size.'">'.JText::_('COM_JDOWNLOADS_LINKTEXT_DOWNLOAD_URL').'</a></div>'; 
                    }    

                    if ($countdown){
                         if ($must_confirm){
                             $countdown = str_replace('{link}', $agree_form, $countdown);
                             $html_sum = str_replace('{license_checkbox}', '<div id="countdown">'.$countdown.'</div>', $html_sum);
                             $html_sum = str_replace('{download_link}', '', $html_sum);
                         } else {
                             $countdown = str_replace('{link}', $link, $countdown);
                             $html_sum = str_replace('{download_link}', '<div id="countdown">'.$countdown.'</div>', $html_sum); 
                         }
                    } else {    
                         if ($must_confirm){
                             $html_sum = str_replace('{license_checkbox}', $agree_form, $html_sum);
                             $html_sum = str_replace('{download_link}', '', $html_sum);
                         } else {   
                             $html_sum = str_replace('{download_link}', $link, $html_sum);
                         }    
                            
                    }
                }
           } else {
                // remove all other (not used) place holders
                $html_sum = str_replace('{info_zip_file_size}', '', $html_sum);
                $html_sum = str_replace('{license_text}', '', $html_sum);
                $html_sum = str_replace('{license_title}', '', $html_sum);
                $html_sum = str_replace('{license_checkbox}', '', $html_sum);
                $html_sum = str_replace('{download_liste}', '', $html_sum);
                $html_sum = str_replace('{external_download_info}', '', $html_sum);
                $html_sum = str_replace('{aup_points_info}', '', $html_sum);
                $html_sum = str_replace('{download_link}', '', $html_sum);
           }    
        }
        
        // view the plugins event data
        $html_sum .= $this->event->afterDisplayContent;        
        
        // view user his limits when activated
        if ($user_rules->view_user_his_limits && $user_rules->view_user_his_limits_msg != '' && $total_consumed['limits_info'] != '' && !$user->guest){
            $html_sum = str_replace('{user_limitations}', $total_consumed['limits_info'], $html_sum);
        } else {
            $html_sum = str_replace('{user_limitations}', '', $html_sum);
        }
        
         // report download link
         if ($jd_user_settings->view_report_form && count($files) == 1){
             // create also link for report link when only one file selected
             $report_link = '<a href="'.JRoute::_("index.php?option=com_jdownloads&amp;view=report&amp;id=".(int)$files[0]->file_id."&amp;catid=".$files[0]->cat_id."&amp;Itemid=".$root_itemid).'" rel="nofollow">'.JText::_('COM_JDOWNLOADS_FRONTEND_REPORT_FILE_LINK_TEXT').'</a>';                
             $html_sum = str_replace('{report_link}', $report_link, $html_sum);
         } else {
            $html_sum = str_replace('{report_link}', '', $html_sum);
         }         
    
        $html .= $html_sum;
        
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