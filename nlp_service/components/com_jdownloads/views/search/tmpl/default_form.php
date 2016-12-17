<?php
/**
 * @package		com_jdownloads
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @modified    by Arno Betz for jDownloads
 */

// no direct access
defined('_JEXEC') or die;

    global $jlistConfig; 

    $db         = JFactory::getDBO(); 
    $document   = JFactory::getDocument();
    $jinput     = JFactory::getApplication()->input;
    $app        = JFactory::getApplication();    
    $user       = JFactory::getUser();
    $lang = JFactory::getLanguage();
    $upper_limit = $lang->getUpperLimitSearchWord();
    
    // get jD user limits and settings
    $jd_user_settings = JDHelper::getUserRules();    

    $html       = '';
    $layout     = '';

    // Get the needed layout data - type = 7 for a 'search form' layout
    // Please note that he are stored only header data not the layout self - not the best solutions. So perhaps will we change it in later releases.            
    $layout = JDHelper::getLayout(7, false);
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
    $menuItemids = JDHelper::getMenuItemids(0);
    
    // get all other menu category IDs so we can use it when we needs it
    $cat_link_itemids = JDHelper::getAllJDCategoryMenuIDs();
    
    // "Home" menu link itemid
    $root_itemid =  $menuItemids['root'];
    
    // ==========================================
    // HEADER SECTION
    // ==========================================

    if ($header != ''){
        
        $menuItemids = JDHelper::getMenuItemids(0);
        
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
        // build level up link
        $upper_link = JRoute::_('index.php?option=com_jdownloads&amp;view=categories&amp;Itemid='.$menuItemids['root']);
        
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
        $header = str_replace('{upper_link}', '<a href="'.$upper_link.'">'.'<img src="'.JURI::base().'components/com_jdownloads/assets/images/upper.png" width="32" height="32" style="border:0px;" alt="'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'" /></a> <a href="'.$upper_link.'">'.JText::_('COM_JDOWNLOADS_UPPER_LINKTEXT').'</a>', $header);                    
        
        // create category listbox and viewed it when it is activated in configuration
        if ($jlistConfig['show.header.catlist']){
            
            // get current selected cat id from listbox
            $catlistid = 0;
            
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
            
            // replace google adsense placeholder with script when active (also for subheader tab)
            if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                    $subheader = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $subheader);
            } else {
                    $subheader = str_replace( '{google_adsense}', '', $subheader);
            }         
            $html .= $subheader;            
        }    
    }

    // ==========================================
    // MAIN SECTION
    // ==========================================    
    if ($layout_text) {
        // replace google adsense placeholder with script when active (also for subheader tab)
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
                $layout_text = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $layout_text);
        } else {
                $layout_text = str_replace( '{google_adsense}', '', $layout_text);
        }         
    } 

    $html .= $layout_text;
    
    // remove empty html tags
    if ($jlistConfig['remove.empty.tags']){
        $html = JDHelper::removeEmptyTags($html);
    }
    
    echo $html;    
    
?>

<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=search');?>" method="post" accept-charset="utf-8">

	<fieldset class="word">
		<label for="search-searchword">
			<?php echo JText::_('COM_JDOWNLOADS_SEARCH_KEYWORD'); ?>
		</label>
		<input type="text" name="searchword" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
		<button name="Search"  onclick="this.form.submit()" class="button"><?php echo JText::_('COM_JDOWNLOADS_SEARCH');?></button>
        <button name="Resetit" onclick="this.form.reset.value = 1;this.form.submit();" class="button"><?php echo JText::_('COM_JDOWNLOADS_SEARCH_RESET');?></button>
        
        <input type="hidden" name="task" value="search" />
        <input type="hidden" name="reset" value="" />
	</fieldset>

	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)):?>
		<p><?php echo JText::plural('COM_JDOWNLOADS_SEARCH_KEYWORD_N_RESULTS', $this->total);?></p>
		<?php endif;?>
	</div>

	<fieldset class="phrases">
		<legend><?php echo JText::_('COM_JDOWNLOADS_SEARCH_FOR');?>
		</legend>
			<div class="phrases-box">
			<?php echo $this->lists['searchphrase']; ?>
			</div>
			<div class="ordering-box">
			<label for="ordering" class="ordering">
				<?php echo JText::_('COM_JDOWNLOADS_SEARCH_ORDERING');?>
			</label>
			<?php echo $this->lists['ordering'];?>
			</div>
	</fieldset>

	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only">
		<legend><?php echo JText::_('COM_JDOWNLOADS_SEARCH_ONLY_IN');?></legend>
		<?php foreach ($this->searchareas['search'] as $val => $txt) :
			$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
		?>
		<label for="area-<?php echo $val;?>" class="checkbox">
        <input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> />
				<?php echo JText::_($txt); ?>
		</label>
		<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

<?php if ($this->total > 0) : ?>

	<div class="form-limit">
		<label for="limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
<p class="counter">
        <?php echo $this->pagination->getPagesCounter(); ?>
	</p>

<?php endif; ?>

</form>