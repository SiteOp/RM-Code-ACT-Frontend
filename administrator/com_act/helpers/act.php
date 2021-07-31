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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * Act helper.
 *
 * @since  1.6
 */
class ActHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_ROUTES'),
			'index.php?option=com_act&view=routes',
			$vName == 'routes'
		);

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_COMMENTS'),
			'index.php?option=com_act&view=comments',
			$vName == 'comments'
		);

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_SETTERS'),
			'index.php?option=com_act&view=setters',
			$vName == 'setters'
		);

		JHtmlSidebar::addEntry(
			Text::_('JCATEGORIES') . ' (' . Text::_('COM_ACT_TITLE_SETTERS') . ')',
			"index.php?option=com_categories&extension=com_act.setters",
			$vName == 'categories.setters'
		);
		if ($vName=='categories') {
			JToolBarHelper::title('ACT: JCATEGORIES (COM_ACT_TITLE_SETTERS)');
		}

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_COLORS'),
			'index.php?option=com_act&view=colors',
			$vName == 'colors'
		);

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_LINES'),
			'index.php?option=com_act&view=lines',
			$vName == 'lines'
		);

		JHtmlSidebar::addEntry(
			Text::_('JCATEGORIES') . ' (' . Text::_('COM_ACT_TITLE_LINES') . ')',
			"index.php?option=com_categories&extension=com_act.lines",
			$vName == 'categories.lines'
		);
		if ($vName=='categories') {
			JToolBarHelper::title('ACT: JCATEGORIES (COM_ACT_TITLE_LINES)');
		}

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_GRADES'),
			'index.php?option=com_act&view=grades',
			$vName == 'grades'
		);

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_SPONSORS'),
			'index.php?option=com_act&view=sponsors',
			$vName == 'sponsors'
		);

JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_SECTORS'),
			'index.php?option=com_act&view=sectors',
			$vName == 'sectors'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_TICKLISTS'),
			'index.php?option=com_act&view=ticklists',
			$vName == 'ticklists'
		);
		JHtmlSidebar::addEntry(
			Text::_('COM_ACT_TITLE_MYCOMMENTS'),
			'index.php?option=com_act&view=mycomments',
			$vName == 'mycomments'
		);
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = Factory::getUser();
		$result = new JObject;

		$assetName = 'com_act';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

