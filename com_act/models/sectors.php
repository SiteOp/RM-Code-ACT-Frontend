<?php

/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Act records.
 *
 * @since  1.6
 */
class ActModelSectors extends \Joomla\CMS\MVC\Model\ListModel
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
                'state', 'a.state',
                'sector', 'a.sector',
                'building', 'a.building',
                'inorout', 'a.inorout',
                'maintenance_interval', 'a.maintenance_interval',
                'first_maintenace', 'a.first_maintenace',
                'next_maintenance', 'v.next_maintenance'
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
            $app  = JFactory::getApplication();
        $list = $app->getUserState($this->context . '.list');

        $ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
        $direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;

        $list['limit']     = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->get('list_limit'), 'uint');
        $list['start']     = $app->input->getInt('start', 0);
        $list['ordering']  = $ordering;
        $list['direction'] = $direction;

        $app->setUserState($this->context . '.list', $list);
        $app->input->set('list', null);
           
        // List state information.
        parent::populateState("a.id", "ASC");

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
     * Build an SQL query to load the list data.
     *
     * @return   JDatabaseQuery
     *
     * @since    1.6
     */
    protected function getListQuery()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select(array('a.id', 'a.sector', 'a.building', 'a.inorout',  'a.state', 'a.maintenance_interval',
                             'v.next_maintenance'
                            )
                        );

        $query->from('`#__act_sector` AS a')
              ->join('LEFT', '#__maintenance_last_maintenance_sector_view AS v ON v.id = a.id');
        
        // ########################### Filter ##################
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
                $query->where('( a.sector LIKE ' . $search . ' )');
             }
        }
            
        // Filtering building
        $filter_building = $this->state->get("filter.building");
            if ($filter_building != '') {
               $query->where($db->qn('a.building') .'=' . (int) $filter_building);
            }

        // Filtering Indoor - Ourdoor
        $filter_inorout = $this->state->get("filter.inorout");
            if ($filter_inorout != '')
            {
                $query->where($db->qn('a.inorout') .'=' . (int) $filter_inorout);
            }



            // Add the list ordering clause.
            $orderCol  = $this->state->get('list.ordering', "a.id");
            $orderDirn = $this->state->get('list.direction', "ASC");

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
        

        return $items;
    }
}
