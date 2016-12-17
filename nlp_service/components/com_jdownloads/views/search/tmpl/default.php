<?php
/**
 * @package     com_jdownloads
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @modified    by Arno Betz for jDownloads
 */

// no direct access
defined('_JEXEC') or die;

    global $jlistConfig;
    
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>

    <div class="search<?php echo $this->pageclass_sfx; ?>">

    <?php if ($this->params->get('show_page_heading')) : ?>
    <h1>
	    <?php if ($this->escape($this->params->get('page_heading'))) :?>
		    <?php echo $this->escape($this->params->get('page_heading')); ?>
	    <?php else : ?>
		    <?php echo $this->escape($this->params->get('page_title')); ?>
	    <?php endif; ?>
    </h1>
    <?php endif; ?>

    <?php
    
    $is_admin   = false;

    if (JDHelper::checkGroup('8', true) || JDHelper::checkGroup('7', true)){
        $is_admin = true;
    }    

    // view offline message - but admins can view it always    
    if ($jlistConfig['offline'] && !$is_admin){
        if ($jlistConfig['offline.text'] != '') {
            echo JDHelper::getOnlyLanguageSubstring($jlistConfig['offline.text']);
        }
    } else { 

        echo $this->loadTemplate('form');
        if ($this->error==null && count($this->results) > 0){
	        echo $this->loadTemplate('results');
        } else {
	        echo $this->loadTemplate('error');
        }
    }
    
    // ==========================================
    // FOOTER SECTION  
    // ==========================================

    $footer = '';    
    
    $layout = JDHelper::getLayout(7, false);
    if ($layout){
        $footer      = $layout->template_footer_text;
    }
    
    // components footer text
    if ($jlistConfig['downloads.footer.text'] != '') {
        $footer_text = stripslashes(JDHelper::getOnlyLanguageSubstring($jlistConfig['downloads.footer.text']));
        if ($jlistConfig['google.adsense.active'] && $jlistConfig['google.adsense.code'] != ''){
            $footer_text = str_replace( '{google_adsense}', stripslashes($jlistConfig['google.adsense.code']), $footer_text);
        } else {
            $footer_text = str_replace( '{google_adsense}', '', $footer_text);
        }   
        $footer .= $footer_text;    
    }
    
    // we need here not a back button
    $footer = str_replace('{back_link}', '', $footer);
    $footer .= JDHelper::checkCom();
    
    // remove empty html tags
    if ($jlistConfig['remove.empty.tags']){
        $footer = JDHelper::removeEmptyTags($footer);
    }
            
    echo $footer.'</div>'; 

?>