<?php
/**
* @version $Id: mod_jdownloads_tree.php v3.2
* @package mod_jdownloads_tree
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jdownloads/models', 'jdownloadsModel');

class ModJDownloadsTreeHelper
{
    static function getList($params)
    {
        $db = JFactory::getDbo();

        // Get an instance of the generic downloads model
        $model = JModelLegacy::getInstance ('categories', 'jdownloadsModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams('com_jdownloads');
        $model->setState('params', $appParams);
       
        // Set the filters based on the module params
        $model->setState('list.start', 0);
        //$model->setState('list.limit', (int) $params->get('sum_view', 5));
        $model->setState('filter.published', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_jdownloads')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        
        // Category display decisions
        $catid        = $params->get('catid');
        $catoption    = intval( $params->get('catoption', 1 ) );
        if ($catid){
            $catid = implode(',', $catid);
            if ($catoption == 1){
                $catid = '1,'.$catid;
            }
            $cat_condition = 'c.id '.($catoption ? ' IN ':' NOT IN ') .'(' . $catid . ') ';
            $model->setState('filter.category_id', $cat_condition);
        } else {
            $model->setState('filter.category_id', '');
        }       
        
        $level = intval( $params->get('maxlevel', 0 ) );
        $model->setState('filter.level', $level);

        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());

        // Set sort ordering
        $ordering = 'c.lft';
        $dir = 'ASC';

        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);

        $items = $model->getItems(true);  // with childrens

        foreach ($items as &$item)
        {
            $item->catslug = $item->id . ':' . $item->alias;

            if ($access || in_array($item->access, $authorised))
            {
                // We know that user has the privilege to view the download
                $item->link = '-';
            } else {
                $item->link = JRoute::_('index.php?option=com_users&view=login');
            }
        }
        return $items;        
    }

}    	
?>