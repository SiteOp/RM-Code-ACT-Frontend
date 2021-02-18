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
class ActModelTicklists extends JModelList
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
                'route', 'a.route',
                'comment', 'a.comment',
                'calc_grade', 't.calc_grade',
                'myroutegrade', 'a.myroutegrade',
                'stars', 'a.stars',
                'ascent', 'a.ascent',
                'tries', 'a.tries',
                'climbdate', 'a.climbdate',
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
        parent::populateState('a.climbdate', 'desc');

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
        $query->select(array('a.id', 'a.comment', 'a.stars', 'a.ascent', 'a.tries', 'a.climbdate', 'a.state', 'ticklist_yn',  'a.tick_comment', 'a.myroutegrade as my_uiaa',
                             'route.id AS route_id', 'route.state AS route_state', 'route.name AS route_name',
							 't.calc_grade as cgrade_uiaa',
                             )
                      )
              ->from('#__act_comment AS a')

              ->join('LEFT', '#__act_route AS route ON route.id = a.route')
              //->join('LEFT', '#__act_grade AS g     ON g.id     = a.myroutegrade')
              ->join('LEFT', '#__act_trigger_calc AS t ON t.id     = route.id')
             // ->join('LEFT', '#__act_grade AS gt    ON gt.id     = t.calc_grade')
              ->where('a.created_by ='. $user)
              ->where('ticklist_yn = 1')
              ->where('a.climbdate  > DATE_SUB(NOW(),INTERVAL 12 MONTH)');

        // Filter by search in title - Filter Name of Route
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
                $query->where('(route.name LIKE ' . $search . ' )');
            }
        }
            
        // Filtering myroutegrade
        $filter_myroutegrade = $this->state->get("filter.myroutegrade");
        if ($filter_myroutegrade != '')
        {

            $query->where($db->qn('g.filter_uiaa') . 'IN (' . implode(',', $filter_myroutegrade).')');
        }

        // Filtering stars
        $filter_stars = $this->state->get("filter.stars");
        if ($filter_stars != '')
        {
            $query->where("a.stars = '".$db->escape($filter_stars)."'");
        }

        // Filtering ascent
        $filter_ascent = $this->state->get("filter.ascent");
        if ($filter_ascent != '') 
        {
            $query->where("a.ascent = '".$db->escape($filter_ascent)."'");
        }

         // Filtering climbdate
        $filter_climbdate_from = $this->state->get("filter.climbdate.from");

        if ($filter_climbdate_from !== null && !empty($filter_climbdate_from))
        {
            $query->where("a.climbdate >= '".$db->escape($filter_climbdate_from)."'");
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

    /**
     * Method um die Liste aller gekletterten Routen zu erhalten
     */
      public function getRoutesTotal()
    {
      // Get a db connection.
      $db = JFactory::getDbo();
      $user    = JFactory::getuser()->id;

      // Create a new query object.
      $query = $db->getQuery(true);
      $query->select(array('COUNT(CASE WHEN MONTH(c.climbdate) =  1 then 1 ELSE NULL END) as Jan',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  2 then 1 ELSE NULL END) as Feb',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  3 then 1 ELSE NULL END) as Maerz',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  4 then 1 ELSE NULL END) as Apr',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  5 then 1 ELSE NULL END) as Mai',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  6 then 1 ELSE NULL END) as Jun',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  7 then 1 ELSE NULL END) as Jul',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  8 then 1 ELSE NULL END) as Aug',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  9 then 1 ELSE NULL END) as Sept',
                           'COUNT(CASE WHEN MONTH(c.climbdate) = 10 then 1 ELSE NULL END) as Okt',
                           'COUNT(CASE WHEN MONTH(c.climbdate) = 11 then 1 ELSE NULL END) as Nov',
                           'COUNT(CASE WHEN MONTH(c.climbdate) = 12 then 1 ELSE NULL END) as Dez',
                           )
                    )
                    
            ->from('#__act_comment AS c')
            ->join('LEFT', '#__act_grade AS g ON g.id  = c.myroutegrade')
            ->where('c.created_by =' .$user)
            ->where('ticklist_yn = 1')
            ->where('c.climbdate  > DATE_SUB(NOW(),INTERVAL 12 MONTH)');
            
             // Filtering myroutegrade
        $filter_myroutegrade = $this->state->get("filter.myroutegrade");
        if ($filter_myroutegrade != '')
        {
            $query->where($db->qn('g.filter_uiaa') . 'IN (' . implode(',', $filter_myroutegrade).')');
        }
        
       $db->setQuery($query);
       $result = $db->loadRowList();

       return $result; 
    }
    
    
    
    /**
     * Method um die Liste der Durchstiege zu erhalten z.B Flash, Onsight...
     */
      public function getRoutesAscent()
    {
      // Get a db connection.
      $db = JFactory::getDbo();
      $user    = JFactory::getuser()->id;

      // Create a new query object.
      $query = $db->getQuery(true);
      $query->select(array('COUNT(CASE WHEN MONTH(c.climbdate) =  1 then 1 ELSE NULL END) as Jan',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  2 then 1 ELSE NULL END) as Feb',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  3 then 1 ELSE NULL END) as Maerz',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  4 then 1 ELSE NULL END) as Apr',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  5 then 1 ELSE NULL END) as Mai',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  6 then 1 ELSE NULL END) as Jun',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  7 then 1 ELSE NULL END) as Jul',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  8 then 1 ELSE NULL END) as Aug',
                           'COUNT(CASE WHEN MONTH(c.climbdate) =  9 then 1 ELSE NULL END) as Sept',
                           'COUNT(CASE WHEN MONTH(c.climbdate) = 10 then 1 ELSE NULL END) as Okt',
                           'COUNT(CASE WHEN MONTH(c.climbdate) = 11 then 1 ELSE NULL END) as Nov',
                           'COUNT(CASE WHEN MONTH(c.climbdate) = 12 then 1 ELSE NULL END) as Dez',
                           )
                    )
                    
            ->from('#__act_comment AS c')
            ->join('LEFT', '#__act_grade AS g ON g.id  = c.myroutegrade')
            ->where('c.created_by =' .$user)
            ->where('ticklist_yn = 1')
            ->where('c.climbdate  > DATE_SUB(NOW(),INTERVAL 12 MONTH)');

        // Filtering myroutegrade
        $filter_myroutegrade = $this->state->get("filter.myroutegrade");
        if ($filter_myroutegrade != '')
        {
            $query->where($db->qn('g.filter_uiaa') . 'IN (' . implode(',', $filter_myroutegrade).')');
        }

         // Filtering ascent
        $filter_ascent = $this->state->get("filter.ascent");
        if ($filter_ascent != '') 
        {
            $query->where("c.ascent = '".$db->escape($filter_ascent)."'");
        }

       $db->setQuery($query);
       $result = $db->loadAssocList();

       return $result; 
    }
}
