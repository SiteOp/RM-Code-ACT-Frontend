<?php

/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Access\Access;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table as Table;

/**
 * route Table class
 *
 * @since  1.6
 */
class ActTableroute extends Table
{
	
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__act_route', 'id', $db);
		
		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_act.route'));
		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_act.route'));
		$this->setColumnAlias('published', 'state');
		
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  null|string  null is operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable:bind
	 * @since   1.5
     * @throws Exception
	 */
	public function bind($array, $ignore = '')
	{
	    $date = Factory::getDate();
		$task = Factory::getApplication()->input->get('task');
	    

		if ($array['id'] == 0 && empty($array['created_by']))
		{
			$array['created_by'] = JFactory::getUser()->id;
		}

		if ($array['id'] == 0 && empty($array['modified_by']))
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}
		$input = JFactory::getApplication()->input;
		$task = $input->getString('task', '');

		// Support for multiple field: settergrade
		if (isset($array['settergrade']))
		{
			if (is_array($array['settergrade']))
			{
				$array['settergrade'] = implode(',',$array['settergrade']);
			}
			elseif (strpos($array['settergrade'], ',') != false)
			{
				$array['settergrade'] = explode(',',$array['settergrade']);
			}
			elseif (strlen($array['settergrade']) == 0)
			{
				$array['settergrade'] = '';
			}
		}
		else
		{
			$array['settergrade'] = '';
		}

		// Support for multiple field: color
		if (isset($array['color']))
		{
			if (is_array($array['color']))
			{
				$array['color'] = implode(',',$array['color']);
			}
			elseif (strpos($array['color'], ',') != false)
			{
				$array['color'] = explode(',',$array['color']);
			}
			elseif (strlen($array['color']) == 0)
			{
				$array['color'] = '';
			}
		}
		else
		{
			$array['color'] = '';
		}

		// Support for multiple field: line
		if (isset($array['line']))
		{
			if (is_array($array['line']))
			{
				$array['line'] = implode(',',$array['line']);
			}
			elseif (strpos($array['line'], ',') != false)
			{
				$array['line'] = explode(',',$array['line']);
			}
			elseif (strlen($array['line']) == 0)
			{
				$array['line'] = '';
			}
		}
		else
		{
			$array['line'] = '';
		}

		// Support for multiple field: setter
		if (isset($array['setter']))
		{
			if (is_array($array['setter']))
			{
				$array['setter'] = implode(',',$array['setter']);
			}
			elseif (strpos($array['setter'], ',') != false)
			{
				$array['setter'] = explode(',',$array['setter']);
			}
			elseif (strlen($array['setter']) == 0)
			{
				$array['setter'] = '';
			}
		}
		else
		{
			$array['setter'] = '';
		}

		// Support for empty date field: createdate
		if($array['createdate'] == '0000-00-00' )
		{
			$array['createdate'] = '';
		}

		// Support for multiple field: sponsor
		if (isset($array['sponsor']))
		{
			if (is_array($array['sponsor']))
			{
				$array['sponsor'] = implode(',',$array['sponsor']);
			}
			elseif (strpos($array['sponsor'], ',') != false)
			{
				$array['sponsor'] = explode(',',$array['sponsor']);
			}
			elseif (strlen($array['sponsor']) == 0)
			{
				$array['sponsor'] = '';
			}
		}
		else
		{
			$array['sponsor'] = '';
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified'] = $date->toSql();
		}

		// Support for multiple field: routeoption
		if (isset($array['routeoption']))
		{
			if (is_array($array['routeoption']))
			{
				$array['routeoption'] = implode(',',$array['routeoption']);
			}
			elseif (strpos($array['routeoption'], ',') != false)
			{
				$array['routeoption'] = explode(',',$array['routeoption']);
			}
			elseif (strlen($array['routeoption']) == 0)
			{
				$array['routeoption'] = '';
			}
		}
		else
		{
			$array['routeoption'] = '';
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (!Factory::getUser()->authorise('core.admin', 'com_act.route.' . $array['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_act/access.xml',
				"/access/section[@name='route']/"
			);
			$default_actions = Access::getAssetRules('com_act.route.' . $array['id'])->getData();
			$array_jaccess   = array();

			foreach ($actions as $action)
			{
                if (key_exists($action->name, $default_actions))
                {
                    $array_jaccess[$action->name] = $default_actions[$action->name];
                }
			}

			$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * This function convert an array of JAccessRule objects into an rules array.
	 *
	 * @param   array  $jaccessrules  An array of JAccessRule objects.
	 *
	 * @return  array
	 */
	private function JAccessRulestoArray($jaccessrules)
	{
		$rules = array();

		foreach ($jaccessrules as $action => $jaccess)
		{
			$actions = array();

			if ($jaccess)
			{
				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool)$allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	/**
	 * Overloaded check function
	 *
	 * @return bool
	 */
	public function check()
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}
		
		

		return parent::check();
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @see Table::_getAssetName
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_act.route.' . (int) $this->$k;
	}

	/**
	 * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
	 *
	 * @param   JTable   $table  Table name
	 * @param   integer  $id     Id
	 *
	 * @see Table::_getAssetParentId
	 *
	 * @return mixed The id on success, false on failure.
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_act');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}

	
    /**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return bool
     */
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);
        
        return $result;
    }

	

	
}
