<?php

/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

//namespace Joomla\Component\Act\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

//jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Act records.
 *
 * @since  1.6
 */
class ActModelRoutes extends ListModel
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
				'name', 'a.name',
				'color', 'c.color',
                'count_stars', 't.count_stars',
				'AvgStars', 'AvgStars',
				'line', 'l.line',
				'inorout', 'sc.inorout',
				'lineSectorName', 'lineSectorName',
				'settername', 's.settername',
				'setterdate', 'a.setterdate',
				'stars', 'stars',
				'orderCGrade', 'orderCGrade',
				'orderVrGrade', 'orderVrGrade',
                'sector', 'sector',
                'lineoption', 'l.lineoption',
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

		$list['limit']     = (int) Factory::getConfig()->get('list_limit', 5); // List Limit ist in der  Joomla Konfiguration (Config - Site- Listenl�nge )festgelegt
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

       // List state information 
        parent::populateState('a.setterdate', 'DESC');

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

        $use_route_lifetime =  $params['use_route_lifetime'];
        $route_lifetime_range =  $params['route_lifetime_range'];

        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(array(// Route
                             'a.id', 'a.name', 'a.setterdate', 'a.exclude',
                             // Setter
                             's.id AS setterId', 's.settername', 
                             // Color
                             'c.rgbcode', 'c.id AS colorId', 'c.rgbcode2', 'c.rgbcode3',
                             // Line
                             'l.line', 'l.id AS lineId', 'l.lineoption',
                             // Sector
                             'sc.inorout',  'sc.building', 'sc.id AS sectorID', 'sc.sector AS lineSectorName',
                             // Trigger
                             't.count_stars', 't.avg_stars AS AvgStars',
                             // VR-Grade
                             'vr.grade AS s_grade', 
                             'vr.id_grade AS orderVrGrade',
                             // C-Grade
                             'cg.grade AS c_grade', 
                             'cg.id_grade AS orderCGrade',
                            )
                       )

              ->from('#__act_route AS a')
              ->join('LEFT', '#__act_trigger_calc AS t  ON t.id        = a.id')               // TRIGGER TABLE
              ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round') // Convertierter Grad cg = C-Grade
              ->join('LEFT', '#__'.$grade_table.' AS vr ON vr.id_grade = a.settergrade')      // Convertierter Grad vr = VR-Grade
              ->join('LEFT', '#__act_line         AS l  ON l.id        = a.line')             // Line
              ->join('LEFT', '#__act_sector       AS sc ON sc.id       = l.sector')           // Sector
              ->join('LEFT', '#__act_setter       AS s  ON a.setter    = s.id')               // Setter  
              ->join('LEFT', '#__act_color        AS c  ON c.id        = a.color')            // Color
              ->where($db->qn('a.state') . 'IN (1,-1)')                                       // Freigegeben und Vorgemerkt
              ->where('cg.id_grade IS NOT NULL')
              ->where($db->qn('a.hidden') . ' != 1');
        
        // Removedate / Lifetime
        if(1 == $use_route_lifetime) {
            $query->select('NOW() >  DATE_SUB(a.removedate, INTERVAL '.$route_lifetime_range.' DAY) AS lifetime' );
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
                {
                    $query->where($db->qn('a.id') . '=' . (int) substr($search, 3));
                }
                else
                {
                    $search = $db->Quote('%' . $db->escape($search, true) . '%');
                    $query->where($db->qn('a.name') . 'LIKE ' . $search);
                }
            }
            

        // Filtering c-grade - Value from Multiple List - Array
        $filter_cgrade = $this->state->get("filter.cgrade");
            if ($filter_cgrade != '')
             {
                JArrayHelper::toInteger($filter_cgrade);
                $query->where($db->qn('cg.filter_merged') . 'IN (' . implode(',', $filter_cgrade).')');
             }
			 
		// Filtering Lineoptions - Value from Multiple List - Array (Automat, Toprobe usw)
        $filter_lineoption = $this->state->get("filter.lineoption");
            if ($filter_lineoption != '')
             {
                $query->where('FIND_IN_SET('.$filter_lineoption.', l.lineoption)');
             }
			 

        // Filtering Line
        $filter_line = $this->state->get("filter.line");
            if ($filter_line != '')
            {   
                $query->where(($db->qn('a.line') . '=' . (int) $filter_line));
            }
 

        // Filtering sector
        $filter_sector = $this->state->get("filter.sector");
            if ($filter_sector)
            {
                $query->where($db->qn('sc.id') . '=' . (int) $filter_sector);
            }

        // Filtering Indoor - Ourdoor
        $filter_inorout = $this->state->get("filter.inorout");
            if ($filter_inorout != '')
            {
                $query->where($db->qn('sc.inorout') .'=' . (int) $filter_inorout);
            }

        // Filtering setter
        $filter_settername = $this->state->get("filter.settername");
            if ($filter_settername)
            {
                $query->where($db->qn('s.id') . '=' . (int) $filter_settername);
            }

        // Filtering Stars
        $filter_stars = $this->state->get("filter.stars");
            if ($filter_stars)
            {
                $query->where($db->qn('t.avg_stars') . '=' . (int) $filter_stars);
            }

        // Filtering color
        $filter_color= $db->escape($this->getState('filter.color'));
            if (!empty($filter_color))
            {
                $query->where($db->qn('c.id') . '=' . (int) $filter_color);
            }
			
			 

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.setterdate');
        $orderDirn = $this->state->get('list.direction', 'DESC');

            if ($orderCol && $orderDirn)
            {
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }

        return $query;
    }

}
