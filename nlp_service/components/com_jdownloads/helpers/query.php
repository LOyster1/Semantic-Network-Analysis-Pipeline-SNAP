<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_jdownloads
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 /* Modified for jDownloads 2.5 */

// no direct access
defined('_JEXEC') or die;

/**
 * Content Component Query Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since		1.5
 */
class JDContentHelperQuery
{
	/**
	 * Translate an order code to a field for primary category ordering.
     *
	 * @param	string	$orderby	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 */
	public static function orderbyPrimary($orderby)
	{
		switch ($orderby)
		{
			case 'alpha' :
            case '1' :
				$orderby = 'c.title, ';
				break;

			case 'ralpha' :
            case '2' :
				$orderby = 'c.title DESC, ';
				break;

			case 'order' :
			case 0 :
            	$orderby = 'c.lft, ';
				break;

			default :
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for secondary downloads ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 * @param	string	$orderDate	The ordering code for the date.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.5
	 */
	public static function orderbySecondary($orderby)
	{
		$orderby = str_replace('a.', '', $orderby);
        
        switch ($orderby)
		{
			case 'date' :
            case 'date_added' :
				$orderby = 'a.date_added';
				break;

			case 'rdate' :
				$orderby = 'a.date_added DESC';
				break;

			case 'alpha' :
            case 'file_title' :
				$orderby = 'a.file_title';
				break;

			case 'ralpha' :
				$orderby = 'a.file_title DESC';
				break;

			case 'hits' :
				$orderby = 'a.downloads DESC';
				break;

            case 'downloads' :
                $orderby = 'a.downloads';
                break;                
                
			case 'rhits' :
				$orderby = 'a.downloads';
				break;

            case 'author' :
                $orderby = 'a.author';
                break;

            case 'rauthor' :
                $orderby = 'a.author DESC';
                break;                

			case 'order' :
            case 'ordering' :
				$orderby = 'a.ordering';
				break;

			default :
				$orderby = 'a.ordering';
				break;
		}

		return $orderby;
	}

	/**
	 * Get join information for the voting query.
	 *
	 * @param	JRegistry	$param	An options object for the download.
	 * @return	array		A named array with "select" and "join" keys.
	 */
	public static function buildVotingQuery($params=null)
	{
		if (!$params) {
			$params = JComponentHelper::getParams('com_jdownloads');
		}

		$voting = $params->get('show_vote');

		if ($voting) {
			// calculate voting count
			$select = ' , ROUND(v.rating_sum / v.rating_count) AS rating, v.rating_count';
			$join = ' LEFT JOIN #__jdownloads_ratings AS v ON a.id = v.file_id';
		}
		else {
			$select = '';
			$join = '';
		}

		$results = array ('select' => $select, 'join' => $join);

		return $results;
	}

}
