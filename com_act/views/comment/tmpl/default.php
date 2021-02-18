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

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_ROUTE'); ?></th>
			<td><?php echo $this->item->route; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_COMMENT'); ?></th>
			<td><?php echo nl2br($this->item->comment); ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_MYROUTEGRADE'); ?></th>
			<td><?php echo $this->item->myroutegrade; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_STARS'); ?></th>
			<td><?php echo $this->item->stars; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_ASCENT'); ?></th>
			<td><?php echo $this->item->ascent; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_TRIES'); ?></th>
			<td><?php echo $this->item->tries; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_CLIMBDATE'); ?></th>
			<td><?php echo $this->item->climbdate; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_CREATED'); ?></th>
			<td><?php echo $this->item->created; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_act&task=comment.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ACT_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_act.comment.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
		<?php echo JText::_("COM_ACT_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_ACT_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_ACT_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_act&task=comment.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_ACT_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>