<?php


defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * 
 *
 * 
 */
class jdownloadsViewjdownloads extends JViewLegacy
{
    protected $canDo;

	/**
	 * view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		global $jlistConfig;
        
        //Load pane behavior in joomla 3
        jimport( 'joomla.html.html.tabs' );

		//initialise variables
		$document	= JFactory::getDocument();
        
		$user 		= JFactory::getUser();
        $this->jdVersion = JDownloadsHelper::getjDownloadsVersion();

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();        
		parent::display($tpl);
	}
    
    /**
     * Add the page title and toolbar.
     *
     * 
     */
    protected function addToolbar()
    {
        global $jlistConfig;
        
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $state    = $this->get('State');
        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        $document->addScriptDeclaration('function openWindow (url) {
        fenster = window.open(url, "_blank", "width=550, height=480, STATUS=YES, DIRECTORIES=NO, MENUBAR=NO, SCROLLBARS=YES, RESIZABLE=NO");
        fenster.focus();
        }');
        
        // view menu only when we have not to work with an update 
        if (!$jlistConfig['old.jd.release.found']){
            JDownloadsHelper::addSubmenu('jdownloads');
        }    
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_CPANEL'), 'jdlogo');
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_jdownloads');
            JToolBarHelper::divider();
        }

        JToolBarHelper::help('help.jdownloads', true);
    }        
    
	
    /**
	 * Creates the buttons view
	 *
	 * @param string $link targeturl
	 * @param string $image path to image
	 * @param string $text image description
	 * @param boolean $modal 1 for loading in modal
	 */
	function quickiconButton( $link, $image, $text, $modal = 0 )
	{
		//initialise variables
		$lang = JFactory::getLanguage();
  		?>

		
			<div class="thumbnails jd-icon">
				<?php
				if ($modal == 1) {
					JHtml::_('behavior.modal');
				?>
					<a href="<?php echo $link.'&amp;tmpl=component'; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: 650, y: 400}}">
				<?php
				} else {
				?>
					<a class="thumbnail jd-icon-inside" href="<?php echo $link; ?>">
				<?php
				}

					echo JHtml::_('image', 'administrator/components/com_jdownloads/assets/images/'.$image, $text );
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		
		<?php
	}	
}
