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
                'orderMyGrade', 'orderMyGrade',
                'orderSGrade','orderSGrade',
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
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(array(// Comment
                             'a.id', 'a.route', 'a.comment', 'a.stars', 'a.created_by', 'a.created',
                             //'a.myroutegrade', 
                             'a.input', 
                             'a.ascent', 'a.ticklist_yn', // Stil benötigt Ticklist_yn
                             // Route
                             'r.id AS route_id', 'r.state AS route_state', 'r.name AS route_name',
                            // 'r.settergrade',
                             // Color
                             'c.rgbcode', 'c.rgbcode2', 'c.rgbcode3',
                             // Line
                             'l.line',
                             // Setter
                             's.settername',
                             // Sector
                             'sc.sector AS lineSectorName',
                             // Trigger
                            // 't.calc_grade_round AS Calc_Grad',
                             // S-Grade
                             'vg.grade AS s_grade', 
                             'vg.id_grade AS orderSGrade',
                              // My-Grade
                              'mg.grade AS my_grade', 
                              'mg.id_grade AS orderMyGrade',
                              // C-Grade
                              'cg.grade AS c_grade',
                            )
                       );
        $query->from('#__act_comment AS a') 
                ->join('LEFT', '#__act_route        AS r   ON r.id        = a.route')
                ->join('LEFT', '#__act_setter       AS s   ON s.id        = r.setter')
                ->join('LEFT', '#__act_color        AS c   ON c.id        = r.color')
                ->join('LEFT', '#__act_line         AS l   ON l.id        = r.line')
                ->join('LEFT', '#__act_sector       AS sc  ON sc.id       = l.sector')
                ->join('LEFT', '#__act_trigger_calc AS t   ON t.id        = r.id')
                ->join('LEFT', '#__'.$grade_table.' AS vg  ON vg.id_grade = r.settergrade')
                ->join('LEFT', '#__'.$grade_table.' AS mg  ON mg.id_grade = a.myroutegrade')
                ->join('LEFT', '#__'.$grade_table.' AS cg  ON cg.id_grade = t.calc_grade_round')

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
     * Hilfsmethode: Baut die chronologische Timeline der letzten 12 Monate auf.
     * Gibt ein Array von 'mm/yy'-Strings zurück, ältester Monat zuerst.
     *
     * @return array  z.B. ['03/25', '04/25', ..., '02/26']
     */
    protected function buildTimeline()
    {
        $timeline = [];
        for ($i = 11; $i >= 0; $i--)
        {
            $timeline[] = date('m/y', strtotime("-{$i} months"));
        }
        return $timeline;
    }


    /**
     * Statistik aller Kommentare der letzten 12 Monate.
     * Gibt die Monatszahlen bereits in chronologischer Reihenfolge zurück,
     * fehlende Monate werden mit 0 aufgefüllt.
     *
     * @return array  [[0 => int, 1 => int, ...]] (12 Einträge)
     */
    public function getCommentsTotal()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select([
                    'DATE_FORMAT(c.created, "%m/%y") AS monat',
                    'COUNT(*) AS anzahl',
                ])
              ->from('#__act_comment AS c')
              ->where('c.created > DATE_SUB(NOW(), INTERVAL 11 MONTH)')
              ->where('c.state != -2')
              ->group('YEAR(c.created), MONTH(c.created)')
              ->order('YEAR(c.created) ASC, MONTH(c.created) ASC');

        $db->setQuery($query);
        $rows = $db->loadAssocList('monat', 'anzahl');

        // Jeden Monat der Timeline befüllen (fehlende → 0)
        $result = [];
        foreach ($this->buildTimeline() as $key)
        {
            $result[] = isset($rows[$key]) ? (int) $rows[$key] : 0;
        }

        return [$result];
    }


    /**
     * Statistik der Kommentare mit aktiven Filtern.
     * Gleiche Rückgabestruktur wie getCommentsTotal().
     *
     * @return array  [[0 => int, 1 => int, ...]] (12 Einträge)
     */
    public function getCommentsFilter()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select([
                    'DATE_FORMAT(c.created, "%m/%y") AS monat',
                    'COUNT(*) AS anzahl',
                ])
              ->from('#__act_comment AS c')
              ->join('LEFT', '#__act_route AS r ON r.id = c.route')
              ->where('c.created > DATE_SUB(NOW(), INTERVAL 11 MONTH)')
              ->where('c.state != -2')
              ->group('YEAR(c.created), MONTH(c.created)')
              ->order('YEAR(c.created) ASC, MONTH(c.created) ASC');

        // Filter: Suche nach Route-Name oder Route-ID
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (is_numeric($search))
            {
                $query->where($db->qn('r.id') . ' = ' . (int) $search);
            }
            else
            {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where($db->qn('r.name') . ' LIKE ' . $search);
            }
        }

        // Filter: Sternebewertung
        $filter_stars = $this->getState('filter.stars');
        if ($filter_stars !== null && $filter_stars !== '')
        {
            $query->where($db->qn('c.stars') . ' = ' . (int) $filter_stars);
        }

        // Filter: Eingabegerät
        $filter_input = $this->getState('filter.input');
        if ($filter_input !== null && $filter_input !== '')
        {
            $query->where($db->qn('c.input') . ' = ' . (int) $filter_input);
        }

        // Filter: Benutzer (via Username → ID)
        $filter_user = $this->getState('filter.user');
        if (!empty($filter_user))
        {
            $user_id = JUserHelper::getUserId($filter_user);
            if ($user_id)
            {
                $query->where($db->qn('c.created_by') . ' = ' . (int) $user_id);
            }
        }

        $db->setQuery($query);
        $rows = $db->loadAssocList('monat', 'anzahl');

        // Jeden Monat der Timeline befüllen (fehlende → 0)
        $result = [];
        foreach ($this->buildTimeline() as $key)
        {
            $result[] = isset($rows[$key]) ? (int) $rows[$key] : 0;
        }

        return [$result];
    }


}