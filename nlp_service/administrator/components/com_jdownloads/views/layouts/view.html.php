<?php
/**
 * @package jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2012 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */


defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * 
 *
 * 
 */
class jdownloadsViewlayouts extends JViewLegacy
{
    protected $canDo;
    
    /**
	 * templates view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
        $option      = 'com_jdownloads'; 
		
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
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $state    = $this->get('State');
        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        JDownloadsHelper::addSubmenu('layouts');
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_BACKEND_CPANEL_TEMPLATES_NAME'), 'jdlogo');
        
        if ($canDo->get('core.edit')) {
            JToolBarHelper::custom( 'layouts.install', 'upload.png', 'upload.png', JText::_('COM_JDOWNLOADS_LAYOUTS_IMPORT_LABEL'), false, false); 
        }
        JToolBarHelper::divider();
        JToolBarHelper::help('help.layouts', true);
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
                    <a href="<?php echo $link.'&amp;tmpl=component'; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: 650, y: 400}}" />
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