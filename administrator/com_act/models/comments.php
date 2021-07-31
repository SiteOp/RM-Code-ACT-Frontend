<?php

/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Act records.
 *
 * @since  1.6
 */
class ActModelComments extends JModelList
{
    
        
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'state', 'a.`state`',
				'route', 'a.`route`',
				'review_yn', 'a.`review_yn`',
				'stars', 'a.`stars`',
				'myroutegrade', 'a.`myroutegrade`',
				'comment', 'a.`comment`',
				'ticklist_yn', 'a.`ticklist_yn`',
				'ascent', 'a.`ascent`',
				'tries', 'a.`tries`',
				'climbdate', 'a.`climbdate`',
				'tick_comment', 'a.`tick_comment`',
				'ordering', 'a.`ordering`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'created', 'a.`created`',
				'modified', 'a.`modified`',
			);
		}

		parent::__construct($config);
	}

    
        
    
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
        // List state information.
        parent::populateState('a.ordering', 'asc');

        $context = $this->getUserStateFromRequest($this->context . '.context', 'context', 'com_content.article', 'CMD');
        $this->setState('filter.context', $context);

        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

                
                    return parent::getStoreId($id);
                
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__act_comment` AS a');
                

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
                

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.comment LIKE ' . $search . ' )');
			}
		}
                

		// Filtering review_yn
		$filter_review_yn = $this->state->get("filter.review_yn");

		if ($filter_review_yn !== null && (is_numeric($filter_review_yn) || !empty($filter_review_yn)))
		{
			$query->where("a.`review_yn` = '".$db->escape($filter_review_yn)."'");
		}

		// Filtering stars
		$filter_stars = $this->state->get("filter.stars");

		if ($filter_stars !== null && (is_numeric($filter_stars) || !empty($filter_stars)))
		{
			$query->where("a.`stars` = '".$db->escape($filter_stars)."'");
		}

		// Filtering myroutegrade
		$filter_myroutegrade = $this->state->get("filter.myroutegrade");

		if ($filter_myroutegrade !== null && (is_numeric($filter_myroutegrade) || !empty($filter_myroutegrade)))
		{
			$query->where("a.`myroutegrade` = '".$db->escape($filter_myroutegrade)."'");
		}

		// Filtering ticklist_yn
		$filter_ticklist_yn = $this->state->get("filter.ticklist_yn");

		if ($filter_ticklist_yn !== null && (is_numeric($filter_ticklist_yn) || !empty($filter_ticklist_yn)))
		{
			$query->where("a.`ticklist_yn` = '".$db->escape($filter_ticklist_yn)."'");
		}

		// Filtering ascent
		$filter_ascent = $this->state->get("filter.ascent");

		if ($filter_ascent !== null && (is_numeric($filter_ascent) || !empty($filter_ascent)))
		{
			$query->where("a.`ascent` = '".$db->escape($filter_ascent)."'");
		}

		// Filtering climbdate
		$filter_climbdate_from = $this->state->get("filter.climbdate.from");

		if ($filter_climbdate_from !== null && !empty($filter_climbdate_from))
		{
			$query->where("a.`climbdate` >= '".$db->escape($filter_climbdate_from)."'");
		}
		$filter_climbdate_to = $this->state->get("filter.climbdate.to");

		if ($filter_climbdate_to !== null  && !empty($filter_climbdate_to))
		{
			$query->where("a.`climbdate` <= '".$db->escape($filter_climbdate_to)."'");
		}

		// Filtering created
		$filter_created_from = $this->state->get("filter.created.from");

		if ($filter_created_from !== null && !empty($filter_created_from))
		{
			$query->where("a.`created` >= '".$db->escape($filter_created_from)."'");
		}
		$filter_created_to = $this->state->get("filter.created.to");

		if ($filter_created_to !== null  && !empty($filter_created_to))
		{
			$query->where("a.`created` <= '".$db->escape($filter_created_to)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'climbdate');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
                
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->route))
			{
				$values    = explode(',', $oneItem->route);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = JFactory::getDbo();
						$query = "SELECT id, name FROM  #__act_route WHERE state != 0 AND #__act_route.id LIKE '" . $value . "' ORDER BY name ASC";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->name;
						}
					}
				}

				$oneItem->route = !empty($textValue) ? implode(', ', $textValue) : $oneItem->route;
			}
					$oneItem->review_yn = ($oneItem->review_yn == '') ? '' : JText::_('COM_ACT_COMMENTS_REVIEW_YN_OPTION_' . strtoupper($oneItem->review_yn));
					$oneItem->stars = JText::_('COM_ACT_COMMENTS_STARS_OPTION_' . strtoupper($oneItem->stars));
					$oneItem->myroutegrade = JText::_('COM_ACT_COMMENTS_MYROUTEGRADE_OPTION_' . strtoupper($oneItem->myroutegrade));
					$oneItem->ticklist_yn = ($oneItem->ticklist_yn == '') ? '' : JText::_('COM_ACT_COMMENTS_TICKLIST_YN_OPTION_' . strtoupper($oneItem->ticklist_yn));
					$oneItem->ascent = JText::_('COM_ACT_COMMENTS_ASCENT_OPTION_' . strtoupper($oneItem->ascent));
		}

		return $items;
	}
}
