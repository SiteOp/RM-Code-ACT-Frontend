<?php

/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder spÃ¤ter; siehe LICENSE.txt
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
				'settergrade', 'a.settergrade',
				'routegrade', 'a.routegrade',
				'color', 'c.color',
				'Calc_Grad', 'Calc_Grad',
				'AvgStars', 'AvgStars',
				'line', 'l.line',
				'sector', 'l.sector',
				'inorout', 'l.inorout',
				'lineSectorName', 'lineSectorName',
				'settername', 's.settername',
				'setterdate', 'a.setterdate',
				'routeCommentUpdate', 'routeCommentUpdate',
				'info', 'a.info',
				'state', 'a.state',
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

		$list['limit']     = (int) Factory::getConfig()->get('list_limit', 5); // List Limit ist in der  Joomla Konfiguration (Config - Site- Listenlänge )festgelegt
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

        // List state information - Ordering = Setterdate
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
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Route (a), Setter (s), Color (c), Categorie (cat), Comment (r),
        $query->select(array('a.id', 'a.state', 'a.name', 'a.setterdate', 'a.info', 'a.created_by', 's.settername', 'a.settergrade',
                             'c.color', 'c.rgbcode',
                             'l.line', 'l.sector', 'l.building', 'l.inorout',
                             'cat.title AS lineSectorName',

                             // VIEW TABLE
                             'view.count_stars', 'view.avg_stars AS AvgStars', 
                             //'CASE WHEN count_user_myroutegrade != 0 THEN ((a.settergrade * 2 + view.sum_grade) / (count_user_myroutegrade + 2 )) ELSE a.settergrade END AS Calc_Grad',
                             'view2.calc_grad AS Calc_Grad',
                             // Last Update from Route modified/created OR Comment modified/created
                             'CASE WHEN  view.comment_modified > a.modified  THEN view.comment_modified ELSE a.modified END AS routeCommentUpdate',
                            )
                         )

              ->from('#__act_route AS a')  


              ->join('LEFT', '#__act_line AS l ON l.id = a.line')
              ->join('LEFT', '#__categories AS cat ON cat.id = l.sector')
              ->join('LEFT', '#__act_setter AS s ON a.setter = s.id')
              ->join('LEFT', '#__act_color AS c ON c.id = a.color')
              //->join('LEFT', '#__act_grade AS g ON g.id = a.settergrade')
              ->join('LEFT', '#__act_view AS view ON view.route = a.id') // VIEW TABLE
              ->join('LEFT', '#__act_grade_view AS view2 ON view2.routeId = a.id') // VIEW TABLE
              ->join('LEFT', '#__act_grade AS g2 ON g2.id = view2.calc_grad')
              ->group('a.id');

		//if (Factory::getUser()->authorise('core.edit', 'com_actj'))
		//{
		//	$query->select(array('max(r.created) AS comment_modified'), 'a.modified')
		//		  ->join('LEFT', '#__act_route AS created_route ON created_route.id=r.route')
		//		  ->select(('CASE WHEN r.created IS NULL THEN a.modified ELSE r.created END AS comment_modified'));
			
				
			//Join over the users for the checked out user.
			//$query->select('uc.name AS uEditor')
			//	 ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

			//Join over the created by field 'created_by'
			//$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

			// Join over the created by field 'modified_by'
			//$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
		//}
		
		
		
		// ########################### Filter ##################
	    // Filter by state 
        $filter_state = $this->getState('filter.state');

        // If Admin allow Select State
        if (Factory::getUser()->authorise('core.edit', 'com_actj')) 
        {
            switch (true)
            {
                //Default State ist NULL
                case ($filter_state == '');
                    $query->where('a.state  = 1 ');
                    break;
                // Select All *
                case ($filter_state == '*'):
                    break;
                // Select 1 = Published, 0 = Unpublished, 2 = Archiv, -2 = Trashed ,
                case ($filter_state !== NULL):
                    $query->where('a.state = '. (int) $filter_state);
                    break;
            }
        }
        // Else not Admin - Default Query->where 1
        else 
        {
        $query->where('a.state  = 1 ');
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
                $query->where($db->qn('g2.filter_uiaa') . 'IN (' . implode(',', $filter_cgrade).')');
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
                $query->where($db->qn('l.sector') . '=' . (int) $filter_sector);
            }

        // Filtering Indoor - Ourdoor
        $filter_inorout = $this->state->get("filter.inorout");

            if ($filter_inorout != '')
            {
                $query->where($db->qn('l.inorout') .'=' . (int) $filter_inorout);
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
                $query->where($db->qn('view.avg_stars') . '=' . (int) $filter_stars);
            }

        // Filtering color
        $filter_color= $db->escape($this->getState('filter.color'));

            if (!empty($filter_color))
            {
                $query->where($db->qn('c.id') . '=' . (int) $filter_color);
            }


        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', "a.setterdate");
        $orderDirn = $this->state->get('list.direction', "DESC");

            if ($orderCol && $orderDirn)
            {
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }

         return $query;
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
	 * Checks if a given date is valid and in a specified format 
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("d.m.Y") : null;
	}
	
	
	
}
