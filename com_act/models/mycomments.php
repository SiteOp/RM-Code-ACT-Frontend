<?php

/**
 * @version    CVS: 1.1.0
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
class ActModelMycomments extends JModelList
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
                'route', 'a.route',
                'comment', 'a.comment',
                'calc_grade', 't.calc_grade',
                'myroutegrade', 'a.myroutegrade',
                'stars', 'a.stars',
                'ascent', 'a.ascent',
                'tries', 'a.tries',
                'created', 'a.created',
                'state', 'a.state',
                'ticklist_yn', 'a.ticklist_yn',
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

        $list['limit']     = (int) Factory::getConfig()->get('list_limit', 20);
        $list['start']     = $app->input->getInt('start', 0);
        $list['ordering']  = $ordering;
        $list['direction'] = $direction;

        $app->setUserState($this->context . '.list', $list);
        $app->input->set('list', null);

        // List state information.
        parent::populateState('a.created', 'desc');

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
        $user = JFactory::getUser();
        $user = $user->get('id');
        
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select record from Comment (a), Route (route), Grade (g/gr)
        // Where Comment User ID setState
        // Where is publish or unpublish / not Trash or Archiv
        // NOTE - ID is required for delet
        $query->select(array('a.id, a.comment, a.stars, a.created, a.state', 'a.ticklist_yn', 'a.myroutegrade as my_uiaa', 
                             'route.id AS route_id', 'route.state AS route_state', 'route.name AS route_name',
                             ' t.calc_grade as cgrade_uiaa'
                             )
                      )
              ->from('#__act_comment AS a')
              
              ->join('LEFT', '#__act_route AS route     ON route.id  = a.route')
              ->join('LEFT', '#__act_trigger_calc AS t ON t.id     = route.id')
              ->join('LEFT', '#__act_grade        AS g  ON g.id     = t.calc_grade_round') // GRADE CONVERSIONN TABLE
              ->where('a.created_by ='. $user);
        
            
        

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
                $query->where($db->qn('route.name') . 'LIKE ' . $search);
            }
        }

        
         // Filtering c-grade - Value from Multiple List - Array
        $filter_sgrade = $this->state->get("filter.sgrade");

            if ($filter_sgrade != '')
             {
                JArrayHelper::toInteger($filter_sgrade);
                $query->where($db->qn('g.filter_uiaa') . 'IN (' . implode(',', $filter_sgrade).')');
             }

        // Filtering stars
        $filter_stars = $this->state->get("filter.stars");
            if ($filter_stars != '')
            {
                $query->where($db->qn('a.stars') . '= ' . (int) $filter_stars);
            }

        // Filtering Ticklist Yes/NO
        $filter_ticklist = $this->state->get("filter.ticklist");
            if ($filter_ticklist != '') 
            {
                $query->where($db->qn('a.ticklist_yn') . '=' . (int) $filter_ticklist);
            }

        // Filtering created Comment
        $filter_created_from = $this->state->get("filter.created.from");

            if ($filter_created_from !== null && !empty($filter_created_from))
            {
                $query->where($db->qn('a.created') . '>=' . $db->q($filter_created_from));
            }

        // Filtering State 
        $filter_state = $this->getState('filter.state');
        
            if ($filter_state == '' OR $filter_state == 1)
            {
                $query->where('a.state = 1');       // Veröffentlich
            }
            elseif ($filter_state == '2')          // Archiv (2)
            {
                $query->where('a.state = 2');
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
