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
 * comment Table class
 *
 * @since  1.6
 */
class ActTablecomment extends Table
{
	
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__act_comment', 'id', $db);
		
		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_act.comment'));
		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_act.comment'));
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

		// Support for multiple field: route
		if (isset($array['route']))
		{
			if (is_array($array['route']))
			{
				$array['route'] = implode(',',$array['route']);
			}
			elseif (strpos($array['route'], ',') != false)
			{
				$array['route'] = explode(',',$array['route']);
			}
			elseif (strlen($array['route']) == 0)
			{
				$array['route'] = '';
			}
		}
		else
		{
			$array['route'] = '';
		}

		// Support for multiple field: stars
		if (isset($array['stars']))
		{
			if (is_array($array['stars']))
			{
				$array['stars'] = implode(',',$array['stars']);
			}
			elseif (strpos($array['stars'], ',') != false)
			{
				$array['stars'] = explode(',',$array['stars']);
			}
			elseif (strlen($array['stars']) == 0)
			{
				$array['stars'] = '';
			}
		}
		else
		{
			$array['stars'] = '';
		}

		// Support for multiple field: myroutegrade
		if (isset($array['myroutegrade']))
		{
			if (is_array($array['myroutegrade']))
			{
				$array['myroutegrade'] = implode(',',$array['myroutegrade']);
			}
			elseif (strpos($array['myroutegrade'], ',') != false)
			{
				$array['myroutegrade'] = explode(',',$array['myroutegrade']);
			}
			elseif (strlen($array['myroutegrade']) == 0)
			{
				$array['myroutegrade'] = '';
			}
		}
		else
		{
			$array['myroutegrade'] = '';
		}

		// Support for multiple field: ascent
		if (isset($array['ascent']))
		{
			if (is_array($array['ascent']))
			{
				$array['ascent'] = implode(',',$array['ascent']);
			}
			elseif (strpos($array['ascent'], ',') != false)
			{
				$array['ascent'] = explode(',',$array['ascent']);
			}
			elseif (strlen($array['ascent']) == 0)
			{
				$array['ascent'] = '';
			}
		}
		else
		{
			$array['ascent'] = '';
		}

		// Support for empty date field: climbdate
		if($array['climbdate'] == '0000-00-00' )
		{
			$array['climbdate'] = '';
		}

		if ($array['id'] == 0 && empty($array['created_by']))
		{
			$array['created_by'] = JFactory::getUser()->id;
		}

		if ($array['id'] == 0)
		{
			$array['created'] = $date->toSql();
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified'] = $date->toSql();
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

		if (!Factory::getUser()->authorise('core.admin', 'com_act.comment.' . $array['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_act/access.xml',
				"/access/section[@name='comment']/"
			);
			$default_actions = Access::getAssetRules('com_act.comment.' . $array['id'])->getData();
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

		return 'com_act.comment.' . (int) $this->$k;
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
