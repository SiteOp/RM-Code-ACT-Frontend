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

/**
 * Setters list controller class.
 *
 * @since  1.6
 */
class ActControllerSetters extends ActController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Setters', $prefix = 'ActModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
