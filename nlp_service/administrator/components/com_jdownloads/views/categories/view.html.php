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

defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * Categories view class
 *
 * @package    jDownloads
 */
class jdownloadsViewcategories extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;
    protected static $rows = array();
    protected $canDo;
    
    
    /**
	 * categories view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        
        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = jdownloadsHelper::getActions();        
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        
        // Preprocess the list of items to find ordering divisions.
        foreach ($this->items as &$item) {
            $this->ordering[$item->parent_id][] = $item->id;
        }        
        
        //$params     = JComponentHelper::getParams('com_jdownloads');

        // Levels filter.
        $options    = array();
        $options[]    = JHtml::_('select.option', '1', JText::_('J1'));
        $options[]    = JHtml::_('select.option', '2', JText::_('J2'));
        $options[]    = JHtml::_('select.option', '3', JText::_('J3'));
        $options[]    = JHtml::_('select.option', '4', JText::_('J4'));
        $options[]    = JHtml::_('select.option', '5', JText::_('J5'));

        $this->assign('levels', $options);
        
        // build categories list box for batch operations 
        $lists = array();
        $config = array('filter.published' => array(0, 1));
        $select[] = JHtml::_('select.option', 0, JText::_('COM_JDOWNLOADS_SELECT_CATEGORY'));
        $select[] = JHtml::_('select.option', 1, JText::_('COM_JDOWNLOADS_BATCH_ROOT_CAT'));
        
        // get the categories data
        $categories = $this->getCategoriesList($config);
        $this->categories = @array_merge($select, $categories);
        
        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();        
        parent::display($tpl);
	}
    
    /**
     * Add the page title and toolbar.
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';

        $state    = $this->get('State');
        $canDo    = JDownloadsHelper::getActions();
        $user     = JFactory::getUser();

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_jdownloads/assets/css/style.css');
        
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        
        JDownloadsHelper::addSubmenu('categories');
        
        JToolBarHelper::title(JText::_('COM_JDOWNLOADS').': '.JText::_('COM_JDOWNLOADS_CATEGORIES'), 'jdcategories');
        
        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('category.add');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('category.edit');
        }    
        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::divider();
            JToolBarHelper::publish('categories.publish', 'COM_JDOWNLOADS_PUBLISH', true);
            JToolBarHelper::unpublish('categories.unpublish', 'COM_JDOWNLOADS_UNPUBLISH', true);
            JToolBarHelper::divider();
            JToolBarHelper::checkin('categories.checkin');
        } 
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList(JText::_('COM_JDOWNLOADS_DELETE_LIST_ITEM_CONFIRMATION'), 'categories.delete', 'COM_JDOWNLOADS_TOOLBAR_REMOVE');
            JToolBarHelper::divider();
        }
        
        // Add a batch button
        if ($canDo->get('core.create') && $canDo->get('core.edit') && $canDo->get('core.edit.state'))
        {
            JHtml::_('bootstrap.modal', 'collapseModal');
            $title = JText::_('JTOOLBAR_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title));
            $bar->appendButton('Custom', $dhtml, 'batch');
        }          
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::custom('categories.rebuild', 'refresh.png', 'refresh_f2.png', 'COM_JDOWNLOADS_REBUILD', false);
            JToolBarHelper::divider();
        }        

        JToolBarHelper::help('help.categories', true);
    }  
    
    
    public static function getCategoriesList($config = array('filter.published' => array(0, 1)))
    {
        $hash = md5('com_jdownloads' . '.categories.' . serialize($config));

        if (!isset(self::$rows[$hash]))
        {
            $config = (array) $config;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('a.id, a.title, a.level');
            $query->from('#__jdownloads_categories AS a');
            $query->where('a.parent_id > 0');

            // Filter on the published state
            if (isset($config['filter.published']))
            {
                if (is_numeric($config['filter.published']))
                {
                    $query->where('a.published = ' . (int) $config['filter.published']);
                }
                elseif (is_array($config['filter.published']))
                {
                    JArrayHelper::toInteger($config['filter.published']);
                    $query->where('a.published IN (' . implode(',', $config['filter.published']) . ')');
                }
            }

            $query->order('a.lft');

            $db->setQuery($query);
            $rows = $db->loadObjectList();

            // Assemble the list options.
            self::$rows[$hash] = array();

            foreach ($rows as &$row)
            {
                $repeat = ($row->level - 1 >= 0) ? $row->level - 1 : 0;
                $row->title = str_repeat('- ', $repeat) . $row->title;
                self::$rows[$hash][] = JHtml::_('select.option', $row->id, $row->title);
            }
        }

        return self::$rows[$hash];       
    }
    
}
?>