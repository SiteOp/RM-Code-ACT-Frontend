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
use Joomla\CMS\MVC\Model\ListModel;

//jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Act records.
 *
 * @since  1.6
 */
class ActModelLines extends ListModel
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
                'line', 'a.line',
                'maker', 'a.maker',
                'sector', 'a.sector',
                'height', 'a.height',
                'state', 'a.state',
                'indicator', 'a.indicator',
                'maintenance_interval', 'a.maintenance_interval',
                'first_maintenace', 'a.first_maintenace',
                'next_maintenance', 'v.next_maintenance',
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
        parent::populateState('a.state', 'asc');

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

        // Line (a) Sector (sc)
        $query->select(array('a.id', 'a.state', 'a.line', 'a.sector',  'a.maker', 'a.height', 'a.indicator', 'a.maintenance_interval',
                             'sc.sector AS lineSectorName',
                             'v.next_maintenance'

                            )
                         );

        $query->from('`#__act_line` AS a')
              ->join('LEFT', '#__act_sector AS sc ON sc.id = a.sector')
              ->join('LEFT', '#__maintenance_last_maintenance_line_view AS v ON v.id = a.id');

        if (!Factory::getUser()->authorise('core.edit', 'com_act'))
        {
            $query->where('a.state = 1');
        }

            // Filter by search in title
            $search = $this->getState('filter.search');

            if (!empty($search))
            {
                if (stripos($search, 'id:') === 0)
                {
                    $query->where('a.line = ' . (int) substr($search, 3));
                }
                else
                {
                    $search = $db->Quote('%' . $db->escape($search, true) . '%');
                    $query->where('( a.line LIKE ' . $search . ' )');
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
            $query->where($db->qn('a.state') . 'IN (1,3)');
        }
 
         // Filtering sector
        $filter_sector = $this->state->get("filter.sector");
            if ($filter_sector)
            {
                $query->where($db->qn('sc.id') . '=' . (int) $filter_sector);
            }

        // Filtering building
        $filter_building = $this->state->get("filter.building");
            if ($filter_building != '') {
               $query->where($db->qn('sc.building') .'=' . (int) $filter_building);
            }

        // Filtering Indoor - Ourdoor
        $filter_inorout = $this->state->get("filter.inorout");
            if ($filter_inorout != '')
            {
                $query->where($db->qn('sc.inorout') .'=' . (int) $filter_inorout);
            }

            // Add the list ordering clause.
            $orderCol  = $this->state->get('list.ordering', 'state');
            $orderDirn = $this->state->get('list.direction', 'DESC');

            if ($orderCol && $orderDirn)
            {
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }

            return $query;
    }

}
