<?php

/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use \Joomla\CMS\Factory;
use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Date\Date;

/**
 * Act model.
 *
 * @since  1.6
 */
class ActModelRoute extends \Joomla\CMS\MVC\Model\ItemModel
{
    public $_item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since    1.6
	 *
     * @throws Exception
	 */
	protected function populateState()
	{
		$app  = Factory::getApplication('com_act');
		$user = Factory::getUser();

		// Wenn der Benutzer nicht Zugriff auf das Archiv hat dann setzte den Status 2 
		// Der Status 2 kann dann mit dem Status der Route (Status 2 = Archiv verglichen werden)
		if (!$user->authorise('route.archive.show', 'com_act')) 
		{
			$this->setState('filter.route.archive', 2);
		}
		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = Factory::getApplication()->getUserState('com_act.edit.route.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_act.edit.route.id', $id);
		}

		$this->setState('route.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('route.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}
    
    
     public function getComment($id = null)
    
    { 
        $id = $this->getState('route.id');

		$params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
         
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select(array('c.id as c_id', 'c.state', 'c.stars', 'c.comment', 
		                     'username AS user_name', 'c.created_by', 'c.ascent', 'c.ticklist_yn',
							 'a.settergrade',
							 'g.grade'
							 ))
              ->from('#__act_comment AS c')
              ->join('LEFT', '#__users AS user ON c.created_by = user.id')
              ->join('LEFT', '#__act_route AS a ON c.route = a.id')
			  ->join('LEFT', '#__'.$grade_table.' AS g ON g.id_grade = c.myroutegrade') // Convertierter Grad cg = C-Grade
              ->where($db->qn('c.route').'='. (int) $id)
              ->order($db->qn('c.id') . 'DESC');
              
        $db->setQuery($query);

        // Load the results as AssocList returns an indexed array of associated arrays from the table records
        $result = $db->loadAssocList();
        
        return $result;
    }

	/**
	 * Method to get an object.
	 *
	 * @param   integer $id The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
     *
     * @throws Exception
	 */
	public function getItem($id = null)
	{
            if ($this->_item === null)
            {
                $this->_item = false;

                if (empty($id))
                {
                    $id = $this->getState('route.id');
                }

                // Get a level row instance.
                $table = $this->getTable();

                // Attempt to load the row.
                if ($table->load($id))
                {
					if ($archive = $this->getState('filter.route.archive'))
						{ 
							if (isset($table->state) && ($table->state == $archive))
							{
							  throw new Exception(Text::_('COM_ACT_ITEM_NOT_LOADED'), 403);
							}
                    }

                    // Convert the JTable to a clean JObject.
                    $properties  = $table->getProperties(1);
                    $this->_item = ArrayHelper::toObject($properties, 'JObject');
                    
                }

                if (empty($this->_item))
				{
					throw new Exception(Text::_('COM_ACT_ITEM_NOT_LOADED'), 404);
				}
				
            }

		$params      = JComponentHelper::getParams('com_act');
		$grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

		$use_route_lifetime =  $params['use_route_lifetime'];
        $route_lifetime_range =  $params['route_lifetime_range'];

        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Where Route ID setState
        $query->select(array(
				// Route
				'a.id', 'a.state', 'a.name', 'a.setterdate', 'a.info', 'a.infoadmin', 'a.settergrade',
				'a.modified', 'a.checked_out', 'a.exclude', 'a.infoextend', 'a.properties AS route_properties',
				'a.routetype', 'a.extend_txt', 'a.extend_check1', 'a.extend_check2', 
				'a.info1_extend', 'a.info2_extend', 'a.info3_extend', 'a.info4_extend', 
				// Color
                'c.rgbcode', 'c.color', 'c.rgbcode2', 'c.rgbcode3',
				// Setter
                's.settername', 
				// Line
                'l.line',  'l.height', 'l.indicator', 'l.properties AS line_properties',
				// Sector
                'sc.sector AS lineSectorName', 'sc.building', 'sc.inorout',
				// Trigger
                't.calc_grade AS calc_grade', // Für Berechnung des Tacho 
				't.avg_stars',
				// Holds Manufacturer
				'h.name AS extend_name',
				// VR-Grade
				'vr.grade AS s_grade', 
				// C-Grade
				'cg.grade AS c_grade',
				'cg.id_grade'
                )
             )
                       
              ->from('#__act_route AS a')
			  ->join('LEFT', '#__act_trigger_calc       AS t  ON t.id        = a.id')               // Trigger
			  ->join('LEFT', '#__'.$grade_table.'       AS cg ON cg.id_grade = t.calc_grade_round') // Convertierter Grad cg = C-Grade
              ->join('LEFT', '#__'.$grade_table.'       AS vr ON vr.id_grade = a.settergrade')      // Convertierter Grad vr = VR-Grade
              ->join('LEFT', '#__act_color              AS c  ON a.color     = c.id')               // Color
              ->join('LEFT', '#__act_setter             AS s  ON a.setter    = s.id')               // Setter
              ->join('LEFT', '#__act_line               AS l  ON a.line      = l.id')               // Line
              ->join('LEFT', '#__act_sector             AS sc ON sc.id       = l.sector')           // Sector
			  ->join('LEFT', '#__act_holds_manufacturer AS h  ON h.id        = a.extend_sql' )      // Holds
              ->where($db->qn('a.id') . '='. (int) $id);
       
		// Removedate / Lifetime
        if(1 == $use_route_lifetime) {
            $query->select('NOW() >  DATE_SUB(a.removedate, INTERVAL '.$route_lifetime_range.' DAY) AS lifetime' );
        }
        
        $db->setQuery($query);
        $query = $db->loadAssoc();
		
        //$this->_item = $query;
        $this->_item = ArrayHelper::toObject($query);    

            return $this->_item;
        }

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string $type   Name of the JTable class to get an instance of.
	 * @param   string $prefix Prefix for the table class name. Optional.
	 * @param   array  $config Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = 'Route', $prefix = 'ActTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_act/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string $alias Item alias
	 *
	 * @return  mixed
	 */
	public function getItemIdByAlias($alias)
	{
            $table      = $this->getTable();
            $properties = $table->getProperties();
            $result     = null;
            $aliasKey   = null;
            if (method_exists($this, 'getAliasFieldNameByView'))
            {
            	$aliasKey   = $this->getAliasFieldNameByView('route');
            }
            

            if (key_exists('alias', $properties))
            {
                $table->load(array('alias' => $alias));
                $result = $table->id;
            }
            elseif (isset($aliasKey) && key_exists($aliasKey, $properties))
            {
                $table->load(array($aliasKey => $alias));
                $result = $table->id;
            }
            
                return $result;
            
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('route.id');
                
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
                
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('route.id');

                
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = Factory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
                
	}

	/**
	 * Publish the element
	 *
	 * @param   int $id    Item id
	 * @param   int $state Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{


		$table = $this->getTable();
                
		$table->load($id);
		$table->state = $state;
		$table->modified = new Date('now');

		return $table->store();
                
	}

	/**
	 * Method to delete an item
	 *
	 * @param   int $id Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();
     
        return $table->delete($id);
                
	}


}
