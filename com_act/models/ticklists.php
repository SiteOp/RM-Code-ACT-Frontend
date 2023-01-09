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
                'orderCGrade', 'orderCGrade',
                'orderMyGrade', 'orderMyGrade',
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
        $user = Factory::getUser();
        $user = $user->get('id');

        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        
        $query->select(array(// Comment
                             'a.id', // NOTE - ID is required for delet
                             'a.comment', 'a.stars', 'a.ascent', 'a.tries', 'a.climbdate', 
                             'a.state', 'ticklist_yn',  'a.tick_comment',
                             // Route
                             'r.id AS route_id', 'r.state AS route_state', 'r.name AS route_name',
                             // My-Grade
                             'mg.grade AS my_grade', 
                             'mg.id_grade AS orderMyGrade',
                             // C-Grade
                             'cg.grade AS c_grade', 
                             'cg.id_grade AS orderCGrade', 
                             )
                      )
              ->from('#__act_comment AS a')

              ->join('LEFT', '#__act_route        AS r     ON r .id             = a.route')
              ->join('LEFT', '#__act_trigger_calc AS t     ON t.id              = r.id')
              ->join('LEFT', '#__'.$grade_table.' AS cg    ON cg.id_grade  = t.calc_grade_round') // Convertierter Grad cg = C-Grade
              ->join('LEFT', '#__'.$grade_table.' AS mg    ON mg.id_grade  = t.calc_grade_round') // Convertierter Grad cg = My-Grade
              ->where($db->qn('a.created_by') . '='. (int) $user)
              ->where($db->qn('ticklist_yn') . '= 1')
              ->where('cg.id_grade IS NOT NULL')
              ->where($db->qn('a.climbdate') . ' > DATE_SUB(NOW(),INTERVAL 11 MONTH)');

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
                $query->where($db->qn('r.name') . ' LIKE ' . $search );
            }
        }
            
        // Filtering myroutegrade
        $filter_myroutegrade = $this->state->get("filter.myroutegrade");
        if ($filter_myroutegrade != '')
        {
            $query->where($db->qn('mg.filter') . 'IN (' . implode(',', $filter_myroutegrade).')');
        }

        // Filtering stars
        $filter_stars = $this->state->get("filter.stars");
        if ($filter_stars != '')
        {
            $query->where($db->qn('a.stars') . '=' . $db->q($filter_stars));
        }

        // Filtering ascent
        $filter_ascent = $this->state->get("filter.ascent");
        if ($filter_ascent != '') 
        {
           $query->where($db->qn('a.ascent') . '=' . $db->q($filter_ascent));
        }

         // Filtering climbdate
        $filter_climbdate_from = $this->state->get("filter.climbdate.from");

        if ($filter_climbdate_from !== null && !empty($filter_climbdate_from))
        {
            $query->where($db->qn('a.climbdate') . '>='.$db->q($filter_climbdate_from));
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
      $db   = Factory::getDbo();
      $user = Factory::getuser()->id;

      $params      = JComponentHelper::getParams('com_act');
      $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

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
            //->join('LEFT', '#__act_grade AS g ON g.id  = c.myroutegrade')
            ->join('LEFT', '#__'.$grade_table.' AS mg    ON mg.id_grade  = c.myroutegrade') // Convertierter Grad  My Grade
            ->where($db->qn('c.created_by') . '=' . (int) $user)
            ->where($db->qn('ticklist_yn') . '= 1')
            ->where($db->qn('c.climbdate') . ' > DATE_SUB(NOW(),INTERVAL 11 MONTH)');
            
        // Filtering myroutegrade
        $filter_myroutegrade = $this->state->get("filter.myroutegrade");
        if ($filter_myroutegrade != '')
        {
            $query->where($db->qn('mg.filter') . 'IN (' . implode(',', $filter_myroutegrade).')');
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
      $db   = Factory::getDbo();
      $user = Factory::getuser()->id;

      $params      = JComponentHelper::getParams('com_act');
      $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

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
            ->join('LEFT', '#__'.$grade_table.' AS mg    ON mg.id_grade  = c.myroutegrade') // Convertierter Grad  My Grade
            ->where($db->qn('c.created_by') . '=' . (int) $user)
            ->where($db->qn('ticklist_yn') . '= 1')
            ->where($db->qn('c.climbdate') . ' > DATE_SUB(NOW(),INTERVAL 12 MONTH)');

        // Filtering myroutegrade
        $filter_myroutegrade = $this->state->get("filter.myroutegrade");
        if ($filter_myroutegrade != '')
        {
            $query->where($db->qn('mg.filter') . 'IN (' . implode(',', $filter_myroutegrade).')');
        }

         // Filtering ascent
        $filter_ascent = $this->state->get("filter.ascent");
        if ($filter_ascent != '') 
        {
            $query->where($db->qn('c.ascent') . '='.$db->escape($filter_ascent));
        }

       $db->setQuery($query);
       $result = $db->loadAssocList();

       return $result; 
    }
}
