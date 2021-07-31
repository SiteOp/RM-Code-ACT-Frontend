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

use \Joomla\CMS\Helper\TagsHelper;
/**
 * Methods supporting a list of Act records.
 *
 * @since  1.6
 */
class ActModelLines extends \Joomla\CMS\MVC\Model\ListModel
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
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'line', 'a.`line`',
				'sector', 'a.`sector`',
				'building', 'a.`building`',
				'inorout', 'a.`inorout`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'lineoption', 'a.`lineoption`',
				'interval', 'a.`interval`',
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
        parent::populateState('id', 'ASC');

        $context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $context);

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
		$query->from('`#__act_line` AS a');
                
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

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
		elseif (empty($published))
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
				
			}
		}
                

		// Filtering sector
		$filter_sector = $this->state->get("filter.sector");

		if ($filter_sector !== null && !empty($filter_sector))
		{
			$query->where("a.`sector` = '".$db->escape($filter_sector)."'");
		}

		// Filtering building
		$filter_building = $this->state->get("filter.building");

		if ($filter_building !== null && (is_numeric($filter_building) || !empty($filter_building)))
		{
			$query->where("a.`building` = '".$db->escape($filter_building)."'");
		}

		// Filtering inorout
		$filter_inorout = $this->state->get("filter.inorout");

		if ($filter_inorout !== null && (is_numeric($filter_inorout) || !empty($filter_inorout)))
		{
			$query->where("a.`inorout` = '".$db->escape($filter_inorout)."'");
		}

		// Filtering lineoption
		$filter_lineoption = $this->state->get("filter.lineoption");

		if ($filter_lineoption !== null && (is_numeric($filter_lineoption) || !empty($filter_lineoption)))
		{
			$query->where('FIND_IN_SET(' . $db->quote($filter_lineoption) . ', ' . $db->quoteName('a.lineoption') . ')');
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

			if (isset($oneItem->sector))
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select($db->quoteName('title'))
					->from($db->quoteName('#__categories'))
					->where('FIND_IN_SET(' . $db->quoteName('id') . ', ' . $db->quote($oneItem->sector) . ')');

				$db->setQuery($query);
				$result = $db->loadColumn();

				$oneItem->sector = !empty($result) ? implode(', ', $result) : '';
			}
					$oneItem->building = JText::_('COM_ACT_LINES_BUILDING_OPTION_' . strtoupper($oneItem->building));
					$oneItem->inorout = JText::_('COM_ACT_LINES_INOROUT_OPTION_' . strtoupper($oneItem->inorout));

			// Get the title of every option selected.

			$options      = explode(',', $oneItem->lineoption);

			$options_text = array();

			foreach ((array) $options as $option)
			{
				$options_text[] = JText::_('COM_ACT_LINES_LINEOPTION_OPTION_' . strtoupper($option));
			}

			$oneItem->lineoption = !empty($options_text) ? implode(', ', $options_text) : $oneItem->lineoption;
		}

		return $items;
	}
}
