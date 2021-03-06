<?php

/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Act model.
 *
 * @since  1.6
 */
class ActModelRoute extends ItemModel
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
     */
    protected function populateState()
    {
        $app  = Factory::getApplication('com_act');
        $user = Factory::getUser();

        // Check published state
        if ((!$user->authorise('core.edit.state', 'com_act')) && (!$user->authorise('core.edit', 'com_act')))
        {
            $this->setState('filter.published', 1);
            $this->setState('filter.replace', -1);
            $this->setState('filter.archived', 2);
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
    
    
    /**
     * Method to get List Comment.
     *
     * @param   integer $id The id of the route.
     *
     * @return  mixed    
     *
     */

    public function getComment($id = null)
    
    { 
        $id = $this->getState('route.id');
         
        // Get a db connection.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Select record from - Comment (c), User (user), Route (a)
        // Where Route ID setState
        $query->select(array('c.id as c_id', 'c.state', 'c.stars', 'c.comment', 'c.myroutegrade', 'username AS user_name', 'a.settergrade', 'c.created_by', 'c.ascent', 'c.ticklist_yn'))
              ->from('#__act_comment AS c')
              ->join('LEFT', '#__users AS user ON c.created_by = user.id')
              ->join('LEFT', '#__act_route AS a ON c.route = a.id')
              ->where('c.route ='. $id)
              ->order('c.id DESC');
              
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
                    // Öffentlich entspricht Status 1 (Freigegebn) und -1 (Kommt raus) 
                    // Erstelle ein Array [1,-1]
                    $published_replace = [$this->getState('filter.published'),$this->getState('filter.replace')];

                    // Check published state.
                    if (($published = $this->getState('filter.published') && $replace = $this->getState('filter.replace')))
                    {
                        if (isset($table->state) && $table->state != in_array($table->state, $published_replace))
                        {
                            throw new Exception(JText::_('COM_ACT_ITEM_NOT_LOADED'), 403);
                        }
                    }
                    
                } 
            }
        
        // Get a db connection
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Select record from - Route (a), Color (c), Setter (s), Line (l), Line Kategorie (cat), Sponsoring (sp)
        // Where Route ID setState
        $query->select(array
                        ('a.id', 'a.state', 'a.name', 'a.setterdate', 'a.info', 'a.infoadmin', 'a.modified', 'a.settergrade', 'a.checked_out', 'a.exclude', 'a.infoextend',
                         'c.rgbcode', 'c.color',
                         's.settername', 
                         'g.uiaa', 'g.franzoesisch',
                         'l.line',  'l.height', 'l.indicator',
                         'sc.sector AS lineSectorName', 'sc.building', 'sc.inorout',
                         'sp.name AS sp_name', 'sp.media AS sp_media', 'sp.txt AS sp_txt',
                         't.calc_grade', 't.avg_stars'
                         )
                       )
                       
              ->from('#__act_route AS a')
              ->join('LEFT', '#__act_color    AS c  ON a.color       = c.id')
              ->join('LEFT', '#__act_setter   AS s  ON a.setter      = s.id')
              ->join('LEFT', '#__act_line     AS l  ON a.line        = l.id')
              ->join('LEFT', '#__act_grade    AS g  ON a.settergrade = g.id')
              ->join('LEFT', '#__act_sponsor  AS sp ON a.sponsor     = sp.id')
              ->join('LEFT', '#__act_sector   AS sc ON sc.id         = l.sector')
              ->join('LEFT', '#__act_trigger_calc AS t ON t.id       = a.id')
              ->where('a.id ='. $id);
              //->where('a.hidden != 1');
              
    
        $db->setQuery($query);

        // Load the results returns an associated array from a single record in the table
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

        return JTable::getInstance($type, $prefix, $config);
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

            if (key_exists('alias', $properties))
            {
                $table->load(array('alias' => $alias));
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
