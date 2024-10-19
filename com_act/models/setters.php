<?php

/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Act records.
 *
 * @since  1.6
 */
class ActModelSetters extends JModelList
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
				'id', 'a.id',
				'category', 'category',
				'lastname', 'a.lastname',
				'firstname', 'a.firstname',
				'settername', 'a.settername',
				'state', 'a.state',
				'user_group', 'uv.user_group'
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
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
            $app  = Factory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;

		$list['limit']     = 10; //(int) Factory::getConfig()->get('list_limit', 10);
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

        // List state information.
        parent::populateState('a.id', 'desc');

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
            $query->select(array('a.id', 'a.state',  'category',  'a.settername',  'a.lastname', 'a.firstname', 'a.user_id',
								 '.uv.user_group'
                                 )
                           )

                    ->from('#__act_setter AS a');
            
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
		$query->join('LEFT', '#__act_user_connect_list_view AS uv ON uv.setterId =a.id');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
            
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
				$query->where('( a.settername LIKE ' . $search . '  OR  a.lastname LIKE ' . $search . ' OR  a.firstname LIKE ' . $search . '  )');
                }
            }
            


        // Filter by state 
        $filter_state = $this->getState('filter.state');
        
        if ($filter_state != '')
        {
            $query->where($db->qn('a.state') . '=' .  $filter_state);
        }
        else 
        {
            $query->where($db->qn('a.state') . '= 1');
        }
		
		// Filter by linked 
		$filter_linked = $this->getState('filter.linked');
		if($filter_linked != '')
		switch ($filter_linked) {
			case 1:
				$query->where($db->qn('a.user_id') . '!= 0');
				break;
			case 0:
				$query->where($db->qn('a.user_id') . '= 0');
				break;
			}
		
		// Filtering Benutzerrechte
		$filter_benutzerrechte = $this->state->get("filter.benutzerrechte");

		if ($filter_benutzerrechte)
		{		
		  switch($filter_benutzerrechte) {
			case 'wa': // Wartung
				$query->where($db->qn('uv.user_group') . 'IN (7,14,11)');
				break;
			case 'bf': // Benutzer
				$query->where($db->qn('uv.user_group') . 'IN (7,12)');
				break;
			case 'ak': //Admin Kommentar
				$query->where($db->qn('uv.user_group') . 'IN (7,13)');
				break;
			case 'rm': //Routes-Manager
				$query->where($db->qn('uv.user_group') . 'IN (7,14)');
				break;
			case 'jo': //Jobs
				$query->where($db->qn('uv.user_group') . 'IN (7,14,16)');
				break;
			case 'me': //Jobs
				$query->where($db->qn('uv.user_group') . 'IN (7,14,17)');
				break;
		  }
			//$filter = $filter_benutzerrechte;
			//$query->where($db->qn('a.ugroup') . 'IN ('.$filter.')');
		    //echo $query->dump(); exit;
		}	

		// Filter by username
		//$filter_user_id = $this->getState('filter.user_id');
        
		//	if ($filter_user_id != '')
		//	{
		//		$query->where($db->qn('a.user_id') . '=' .  $filter_user_id);
		//	}
	

		// Filtering category
		$filter_category = $this->state->get("filter.category");

		if ($filter_category)
		{
			$query->where("FIND_IN_SET('" . $db->escape($filter_category) . "',category)");
		}

            // Add the list ordering clause.
            $orderCol  = $this->state->get('list.ordering', 'a.id');
            $orderDirn = $this->state->get('list.direction', 'DESC');

            if ($orderCol && $orderDirn)
            {
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }

            return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{

		if (isset($item->category) && $item->category != '')
		{

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select($db->quoteName('title'))
				->from($db->quoteName('#__rm_config_setter_category'))
				->where('FIND_IN_SET(' . $db->quoteName('id') . ', ' . $db->quote($item->category) . ')');

			$db->setQuery($query);

			$result = $db->loadColumn();

			$item->category = !empty($result) ? implode(', ', $result) : '';
		}
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_ACT_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
