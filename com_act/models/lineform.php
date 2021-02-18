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

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

use \Joomla\CMS\Factory;
use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table;

/**
 * Act model.
 *
 * @since  1.6
 */
class ActModelLineForm extends \Joomla\CMS\MVC\Model\FormModel
{
    private $item = null;

    

    

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return void
     *
     * @since  1.6
     *
     * @throws Exception
     */
    protected function populateState()
    {
        $app = Factory::getApplication('com_act');

        // Load state from the request userState on edit or from the passed variable on default
        if (Factory::getApplication()->input->get('layout') == 'edit')
        {
                $id = Factory::getApplication()->getUserState('com_act.edit.line.id');
        }
        else
        {
                $id = Factory::getApplication()->input->get('id');
                Factory::getApplication()->setUserState('com_act.edit.line.id', $id);
        }

        $this->setState('line.id', $id);

        // Load the parameters.
        $params       = $app->getParams();
        $params_array = $params->toArray();

        if (isset($params_array['item_id']))
        {
                $this->setState('line.id', $params_array['item_id']);
        }

        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param   integer $id The id of the object to get.
     *
     * @return Object|boolean Object on success, false on failure.
     *
     * @throws Exception
     */
    public function getItem($id = null)
    {
        if ($this->item === null)
        {
            $this->item = false;

            if (empty($id))
            {
                    $id = $this->getState('line.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            if ($table !== false && $table->load($id) && !empty($table->id))
            {
                $user = Factory::getUser();
                $id   = $table->id;
                

                $canEdit = $user->authorise('core.edit', 'com_act') || $user->authorise('core.create', 'com_act');

                if (!$canEdit && $user->authorise('core.edit.own', 'com_act'))
                {
                        $canEdit = $user->id == $table->created_by;
                }

                if (!$canEdit)
                {
                        throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
                }

                // Check published state.
                if ($published = $this->getState('filter.published'))
                {
                        if (isset($table->state) && $table->state != $published)
                        {
                                return $this->item;
                        }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->item = ArrayHelper::toObject($properties, 'JObject');
                
		if (isset($this->item->category) && is_object($this->item->category))
		{
			$this->item->category = ArrayHelper::fromObject($this->item->category);
		}
		if (isset($this->item->sector) && is_object($this->item->sector))
		{
			$this->item->sector = ArrayHelper::fromObject($this->item->sector);
		}

                
            }
        }

        return $this->item;
    }

    /**
     * Method to get the table
     *
     * @param   string $type   Name of the JTable class
     * @param   string $prefix Optional prefix for the table class name
     * @param   array  $config Optional configuration array for JTable object
     *
     * @return  JTable|boolean JTable if found, boolean false on failure
     */
    public function getTable($type = 'Line', $prefix = 'ActTable', $config = array())
    {
        $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_act/tables');

        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Get an item by alias
     *
     * @param   string $alias Alias string
     *
     * @return int Element id
     */
    public function getItemIdByAlias($alias)
    {
        $table      = $this->getTable();
        $properties = $table->getProperties();

        if (!in_array('alias', $properties))
        {
                return null;
        }

        $table->load(array('alias' => $alias));
        $id = $table->id;

        
            return $id;
        
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
        $id = (!empty($id)) ? $id : (int) $this->getState('line.id');
        
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
        $id = (!empty($id)) ? $id : (int) $this->getState('line.id');
        
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
     * Method to get the profile form.
     *
     * The base form is loaded from XML
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
     *
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_act.line', 'lineform', array(
                        'control'   => 'jform',
                        'load_data' => $loadData
                )
        );

        if (empty($form))
        {
                return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    array  The default data is an empty array.
     * @since    1.6
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_act.edit.line.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        if ($data)
        {
            
		// Support for multiple or not foreign key field: building
		$array = array();

		foreach ((array) $data->lineoption as $value)
		{
			if (!is_array($value))
			{
				$array[] = $value;
			}
		}
		if(!empty($array)){

		$data->lineoption = $array;
		}

            return $data;
        }

        return array();
    }

    /**
     * Method to save the form data.
     *
     * @param   array $data The form data
     *
     * @return bool
     *
     * @throws Exception
     * @since 1.6
     */
    public function save($data)
    {
        $id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('line.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user  = Factory::getUser();

        
        if ($id)
        {
            // Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_act') || $authorised = $user->authorise('core.edit.own', 'com_act');
        }
        else
        {
            // Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_act');
        }

        if ($authorised !== true)
        {
            throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $table = $this->getTable();

        if ($table->save($data) === true)
        {
            return $table->id;
        }
        else
        {
            return false;
        }
        
    }

    /**
     * Method to delete data
     *
     * @param   int $pk Item primary key
     *
     * @return  int  The id of the deleted item
     *
     * @throws Exception
     *
     * @since 1.6
     */
    public function delete($pk)
    {
        $user = Factory::getUser();

        
            if (empty($pk))
            {
                    $pk = (int) $this->getState('line.id');
            }

            if ($pk == 0 || $this->getItem($pk) == null)
            {
                    throw new Exception(Text::_('COM_ACT_ITEM_DOESNT_EXIST'), 404);
            }

            if ($user->authorise('core.delete', 'com_act') !== true)
            {
                    throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            }

            $table = $this->getTable();

            if ($table->delete($pk) !== true)
            {
                    throw new Exception(Text::_('JERROR_FAILED'), 501);
            }

            return $pk;
        
    }

    /**
     * Check if data can be saved
     *
     * @return bool
     */
    public function getCanSave()
    {
        $table = $this->getTable();

        return $table !== false;
    }
    public function getAliasFieldNameByView($view)
	{
		switch ($view)
		{
			case 'color':
			case 'colorform':
				return 'alias';
			break;
		}
	}
}
