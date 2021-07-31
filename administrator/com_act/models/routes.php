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
class ActModelRoutes extends JModelList
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
				'name', 'a.`name`',
				'state', 'a.`state`',
				'settergrade', 'a.`settergrade`',
				'color', 'a.`color`',
				'line', 'a.`line`',
				'settername', 'a.settername',
				'createdate', 'a.`createdate`',
				'info', 'a.`info`',
				'infoadmin', 'a.`infoadmin`',
				'sponsor', 'a.`sponsor`',
				'ordering', 'a.`ordering`',
				'created_by', 'a.`created_by`',
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
		$query->from('`#__act_route` AS a');
                

		//Join over the users for the checked out user.
		$query->select('s.settername')
			 ->join('LEFT', '#__act_setter AS s ON s.id=a.setter');

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');


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
				$query->where('a.name LIKE ' . $search);
			}
		}
                

		// Filtering settergrade
		$filter_settergrade = $this->state->get("filter.settergrade");

		if ($filter_settergrade !== null && (is_numeric($filter_settergrade) || !empty($filter_settergrade)))
		{
			$query->where("a.`settergrade` = '".$db->escape($filter_settergrade)."'");
		}

		// Filtering setter

		// Filtering created_by
		$filter_created_by = $this->state->get("filter.created_by");

		if ($filter_created_by !== null && !empty($filter_created_by))
		{
			$query->where("a.`created_by` = '".$db->escape($filter_created_by)."'");
		}

		// Filtering modified
		$filter_modified_from = $this->state->get("filter.modified.from");

		if ($filter_modified_from !== null && !empty($filter_modified_from))
		{
			$query->where("a.`modified` >= '".$db->escape($filter_modified_from)."'");
		}
		$filter_modified_to = $this->state->get("filter.modified.to");

		if ($filter_modified_to !== null  && !empty($filter_modified_to))
		{
			$query->where("a.`modified` <= '".$db->escape($filter_modified_to)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

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
					$oneItem->settergrade = JText::_('COM_ACT_ROUTES_SETTERGRADE_OPTION_' . strtoupper($oneItem->settergrade));

			if (isset($oneItem->color))
			{
				$values    = explode(',', $oneItem->color);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = JFactory::getDbo();
						$query = "SELECT color, rgbcode, state, id FROM #__act_color WHERE state != 0 AND #__act_color.id LIKE '" . $value . "' ORDER BY color ASC";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->color;
						}
					}
				}

				$oneItem->color = !empty($textValue) ? implode(', ', $textValue) : $oneItem->color;
			}

			if (isset($oneItem->line))
			{
				$values    = explode(',', $oneItem->line);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = JFactory::getDbo();
						$query = "SELECT line, state, id FROM #__act_line WHERE state != 0 AND #__act_line.id LIKE '" . $value . "' ORDER BY line ASC";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->line;
						}
					}
				}

				$oneItem->line = !empty($textValue) ? implode(', ', $textValue) : $oneItem->line;
			}

			if (isset($oneItem->settername))
			{
				$values    = explode(',', $oneItem->settername);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = JFactory::getDbo();
						$query = "SELECT settername, state, id FROM #__act_setter WHERE state != 0 AND #__act_setter.id LIKE '" . $value . "' ORDER BY settername ASC";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->settername;
						}
					}
				}

				$oneItem->settername = !empty($textValue) ? implode(', ', $textValue) : $oneItem->settername;
			}

			if (isset($oneItem->sponsor))
			{
				$values    = explode(',', $oneItem->sponsor);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = JFactory::getDbo();
						$query = "SELECT id, state,  name FROM #__act_sponsor WHERE state != 0 AND #__act_sponsor.id LIKE '" . $value . "'";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->name;
						}
					}
				}

				$oneItem->sponsor = !empty($textValue) ? implode(', ', $textValue) : $oneItem->sponsor;
			}
		}

		return $items;
	}
}
