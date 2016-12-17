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

jimport( 'joomla.application.component.view' );

/**
 * View downloads list
  * @package    jDownloads
 */
class jdownloadsViewList extends JViewLegacy
{
	
    protected $state;
    protected $items;
    protected $pagination;
    protected static $rows = array();
    protected $canDo;
    
    
    /**
	 * Downloads list view method
	 * @return void
	 **/
	function display($tpl = null)
	{

        // set a switch so we can build later a valid: db query
        $app = JFactory::getApplication();
        if ($this->getLayout() == 'modal' || $this->getLayout() == 'modallist'){
            $app->setUserState( 'jd_modal', true );
            // Load the backend helper
            require_once JPATH_ADMINISTRATOR.'/components/com_jdownloads/helpers/jdownloadshelper.php';
            // we must load the admin language here explicit
            $lang = JFactory::getLanguage();
            $locale = JDownloadsHelper::getLangKey();
            $lang->load( 'com_jdownloads', JPATH_ADMINISTRATOR, $locale, true);
        }  else {
            $app->setUserState( 'jd_modal', false );
        }
         
        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
   
		// create 'delete also file' option
		$filters = array();
        $filters[] = JHtml::_('select.option', '0', JText::_( 'NO' ) );
		$filters[] = JHtml::_('select.option', '1', JText::_( 'YES' ) );
        $this->delete_file_option = $filters;
        
        // build categories list box 
        $lists = array();
        $config = array('filter.published' => array(0, 1));
        $select[] = JHtml::_('select.option', 0, JText::_('COM_JDOWNLOADS_SELECT_CATEGORY'));
        $select[] = JHtml::_('select.option', 1, JText::_('COM_JDOWNLOADS_SELECT_UNCATEGORISED'));
        // get the categories data
        $categories = $this->getCategoriesList($config);
        $this->categories = @array_merge($select, $categories);
        
        parent::display($tpl);
	}
    
    
    /**
     * Returns an array of the categories 
     *
     * @param   array   $config     An array of configuration options. By default, only
     *                              published and unpublished categories are returned.
     *
     * @return  array
     *
     */
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
