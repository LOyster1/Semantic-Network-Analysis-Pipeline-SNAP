<?php
/**
* @version 3.2
* @package JDownloads
* @copyright (C) 2007/2014 www.jdownloads.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Editor button for jDownloads content plugin 3.2 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

jimport( 'joomla.plugin.plugin' );

class plgButtonJdownloads extends JPlugin {
     
    protected $autoloadLanguage = true;
    
    function plgButtonJdownloads(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }    

	public function onDisplay($name, $asset, $author){
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$allowed_in_frontend = $this->params->get('frontend', 0);

        $document->addStyleSheet( JURI::root().'plugins/editors-xtd/jdownloads/assets/css/jdownloads.css', 'text/css', null, array() ); 


        /*
         * Javascript to insert the link
         * View element calls jSelectDownloadContent when an download is clicked
         * jSelectDownload creates the content tag, sends it to the editor,
         * and closes the select frame.
         */
        $js = "
        function jSelectDownload(id, title, catid, object, link, lang)
        {
            var tag = '{jd_file file=='+ id + '}';
            jInsertEditorText(tag, '" . $name . "');
            SqueezeBox.close();
        }";
        $document->addScriptDeclaration($js);

        $link = 'index.php?option=com_jdownloads&amp;view=list&amp;layout=modallist&amp;tmpl=component&amp;e_name='.$name.'&amp;' . JSession::getFormToken() . '=1';

        JHtml::_('behavior.modal');
        $button = new JObject;
        $button->modal = true;
        $button->class = 'btn';
        $button->link = $link;
        $button->text = JText::_('PLG_EDITORS-XTD_JDOWNLOADS_CAT_BUTTON_TEXT');                  
        $button->name = 'file-add';
        $button->options = "{handler: 'iframe', size: {x: 950, y: 500}}";		
        
		if ($allowed_in_frontend == 0 && !$app->isAdmin()) $button = null;
		        
		return $button;
	}
}
?>