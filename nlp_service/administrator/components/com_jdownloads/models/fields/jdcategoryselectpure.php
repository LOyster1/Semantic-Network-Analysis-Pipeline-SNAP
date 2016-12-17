<?php
/**
 * @copyright    Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 */

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

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class
 *
 */
class JFormFieldjdCategorySelectPure extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'jdCategorySelectPure';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__jdownloads_categories AS a');
        $query->join('LEFT', '`#__jdownloads_categories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Prevent parenting to children of this item.
		/* if ($id = $this->form->getValue('id')) {
			$query->join('LEFT', '`#__jdownloads_categories` AS p ON p.id = '.(int) $id);
			$query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

			$rowQuery	= $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
			$rowQuery->from('#__jdownloads_categories AS a');
			$rowQuery->where('a.id = ' . (int) $id);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}  */

		$query->where('a.published IN (0,1)');
		$query->group('a.id');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
        
        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        // Pad the option text with spaces using depth level as a multiplier.
        for ($i = 0, $n = count($options); $i < $n; $i++)
        {
            // Translate ROOT
            if ($options[$i]->level == 0) {
                unset($options[$i]);
                continue;
            }

            $options[$i]->text = str_repeat('- ',$options[$i]->level - 1).$options[$i]->text;
        }

        // Initialise variables.
        $user = JFactory::getUser();

        if (empty($id)) {
            // New item, only have to check core.create.
            foreach ($options as $i => $option)
            {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise('core.create', 'com_jdownloads.category.'.$option->value)) {
                    unset($options[$i]);
                }
            }
        }
        else {
            // Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
            foreach ($options as $i => $option)
            {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise('core.edit', 'com_jdownloads.category.'.$option->value)) {
                    // As a backup, check core.edit.own
                    if (!$user->authorise('core.edit.own', 'com_jdownloads.category.'.$option->value)) {
                        // No core.edit nor core.edit.own - bounce this one
                        unset($options[$i]);
                    }
                    else {
                        // TODO I've got a funny feeling we need to check core.create here.
                        // Maybe you can only get the list of categories you are allowed to create in?
                        // Need to think about that. If so, this is the place to do the check.
                    }
                }
            }
        }
       
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}