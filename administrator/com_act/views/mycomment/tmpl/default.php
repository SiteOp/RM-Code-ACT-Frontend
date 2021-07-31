<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_act');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_act'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
<?php endif; ?>
<div class="item_fields">

	<table class="table">
		

	</table>

</div>

