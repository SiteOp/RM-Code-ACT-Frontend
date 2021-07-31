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
class ActModelComments extends \Joomla\CMS\MVC\Model\ListModel
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
                'route', 'route_name',
                'comment', 'a.comment',
                'myroutegrade', 'a.myroutegrade',
                'settergrade','r.settergrade',
                'stars', 'a.stars',
                'created_by', 'a.created_by',
                'created', 'a.created',
                'input', 'a.input',
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
        parent::populateState('a.created', 'DESC');

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

        // Comment (a), Route (r), Color(c), Line (l), Setter (s), Categorie (cat)
        $query->select(array('a.id', 'a.route', 'a.comment', 'a.stars', 'a.created_by', 'a.created', 'a.myroutegrade', 'a.input', 'a.ascent', 'a.ticklist_yn',
                             'r.id AS route_id', 'r.state AS route_state', 'r.name AS route_name', 'r.settergrade',
                             'c.rgbcode',
                             'l.line',
                             's.settername',
                             'sc.sector AS lineSectorName',
                             't.calc_grade AS Calc_Grad',
                            )
                       );
        $query->from('#__act_comment AS a') 
                ->join('LEFT', '#__act_route        AS r   ON r.id   = a.route')
                ->join('LEFT', '#__act_setter       AS s   ON s.id   = r.setter')
                ->join('LEFT', '#__act_color        AS c   ON c.id   = r.color')
                ->join('LEFT', '#__act_line         AS l   ON l.id   = r.line')
                ->join('LEFT', '#__act_sector       AS sc  ON sc.id  = l.sector')
                ->join('LEFT', '#__act_trigger_calc AS t   ON t.id   = r.id')
                ->join('LEFT', '#__act_grade        AS g   ON g.id   = t.calc_grade')

                ->where('a.created  > DATE_SUB(NOW(),INTERVAL 11 MONTH)');
      

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (is_numeric($search))
            {
                $query->where($db->qn('r.id') . '=' . (int) $search);
            }
            else
            {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where($db->qn('r.name') . 'LIKE ' . $search);
            }
        }

        // Filtering stars
        $filter_stars = $this->state->get("filter.stars");
        if ($filter_stars != '')
        {
            $query->where($db->qn('a.stars') . '=' . (int) $filter_stars);
        }

        // Filtering Input
        $filter_input = $this->state->get("filter.input");
        if ($filter_input != '')
        {
            $query->where($db->qn('a.input') . '=' . (int) $filter_input);
        }

        // Filtering created Comment
        $filter_created_from = $this->state->get("filter.created.from");

        if ($filter_created_from !== null && !empty($filter_created_from))
        {
            $query->where($db->qn('a.created') . '>=' . $db->q($filter_created_from));
        }

        // Filtering Username
        $filter_user = $this->state->get("filter.user");
        // Hole die ID des User anhand des Username
		$user_id   = JUserHelper::getUserId($filter_user);

        if ($filter_user !== null && !empty($filter_user))
        {
           $query->where($db->qn('a.created_by') . '=' . (int) $user_id);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.created');
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
    * Method um eine Statistik für die Kommentare zu erhalten
    */
    public function getCommentsTotal()
    {
        // Get a db connection.
        $db = JFactory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);
        $query->select(array('COUNT(CASE WHEN MONTH(c.created) =  1 then 1 ELSE NULL END) as Jan',
                             'COUNT(CASE WHEN MONTH(c.created) =  2 then 1 ELSE NULL END) as Feb',
                             'COUNT(CASE WHEN MONTH(c.created) =  3 then 1 ELSE NULL END) as Maerz',
                             'COUNT(CASE WHEN MONTH(c.created) =  4 then 1 ELSE NULL END) as Apr',
                             'COUNT(CASE WHEN MONTH(c.created) =  5 then 1 ELSE NULL END) as Mai',
                             'COUNT(CASE WHEN MONTH(c.created) =  6 then 1 ELSE NULL END) as Jun',
                             'COUNT(CASE WHEN MONTH(c.created) =  7 then 1 ELSE NULL END) as Jul',
                             'COUNT(CASE WHEN MONTH(c.created) =  8 then 1 ELSE NULL END) as Aug',
                             'COUNT(CASE WHEN MONTH(c.created) =  9 then 1 ELSE NULL END) as Sept',
                             'COUNT(CASE WHEN MONTH(c.created) = 10 then 1 ELSE NULL END) as Okt',
                             'COUNT(CASE WHEN MONTH(c.created) = 11 then 1 ELSE NULL END) as Nov',
                             'COUNT(CASE WHEN MONTH(c.created) = 12 then 1 ELSE NULL END) as Dez',
                            )
                      )

              ->from('#__act_comment AS c')
              ->where('c.created  > DATE_SUB(NOW(),INTERVAL 11 MONTH)')
              ->where('c.state != -2');
              //->group('YEAR(c.created)')
              //->group('MONTH(c.created)');

       $db->setQuery($query);
       $result = $db->loadRowList();

       return $result; 
    }
    
    
    /**
    * Method um die Statistik der Kommentare mit Filter zu erweitern
    */
    public function getCommentsFilter()
    {
        // Get a db connection.
        $db = JFactory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);
        $query->select(array('COUNT(CASE WHEN MONTH(c.created) =  1 then 1 ELSE NULL END) as Jan',
                             'COUNT(CASE WHEN MONTH(c.created) =  2 then 1 ELSE NULL END) as Feb',
                             'COUNT(CASE WHEN MONTH(c.created) =  3 then 1 ELSE NULL END) as Maerz',
                             'COUNT(CASE WHEN MONTH(c.created) =  4 then 1 ELSE NULL END) as Apr',
                             'COUNT(CASE WHEN MONTH(c.created) =  5 then 1 ELSE NULL END) as Mai',
                             'COUNT(CASE WHEN MONTH(c.created) =  6 then 1 ELSE NULL END) as Jun',
                             'COUNT(CASE WHEN MONTH(c.created) =  7 then 1 ELSE NULL END) as Jul',
                             'COUNT(CASE WHEN MONTH(c.created) =  8 then 1 ELSE NULL END) as Aug',
                             'COUNT(CASE WHEN MONTH(c.created) =  9 then 1 ELSE NULL END) as Sept',
                             'COUNT(CASE WHEN MONTH(c.created) = 10 then 1 ELSE NULL END) as Okt',
                             'COUNT(CASE WHEN MONTH(c.created) = 11 then 1 ELSE NULL END) as Nov',
                             'COUNT(CASE WHEN MONTH(c.created) = 12 then 1 ELSE NULL END) as Dez',
                             )
                      )

              ->from('#__act_comment AS c')
             ->join('LEFT', '#__act_route AS r ON r.id = c.route')
              ->where('c.created  > DATE_SUB(NOW(),INTERVAL 11 MONTH)')
              ->where('c.state != -2');

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (is_numeric($search))
            {
                $query->where($db->qn('r.id') . '=' . (int) $search);
            }
            else
            {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where($db->qn('r.name') . 'LIKE ' . $search);
            }
        }

        // Filtering stars
        $filter_stars = $this->state->get("filter.stars");
        if ($filter_stars != '')
        {
            $query->where($db->qn('c.stars') . '=' . (int) $filter_stars);
        }

        // Filtering Username (Filter width ID)
        $filter_user = $this->state->get("filter.user");

        // Hole die ID des User anhand des Username
		$user_id   = JUserHelper::getUserId($filter_user);

        if ($filter_user !== null && !empty($filter_user))
        {
           $query->where($db->qn('c.created_by') . '=' . (int) $user_id);
        }
        
        // Filtering Input
        $filter_input = $this->state->get("filter.input");
        if ($filter_input != '')
        {
            $query->where($db->qn('c.input') . '=' . (int) $filter_input);
        }

       $db->setQuery($query);
       $result = $db->loadRowList();

       return $result; 
    }


}
