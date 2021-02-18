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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_act');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_act'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php // Page-Header ?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> 
            <?php echo $this->escape($this->params->get('page_heading')); ?> 
            <small> <?php echo $this->item->lastname; ?></small>
        </h1>
	</div>
<?php else : ?>
    <div class="page-header">
		<h1 itemprop="headline">
			<?php echo $this->escape($this->params->get('page_title')); ?>
            <small> <?php echo $this->item->firstname . ' ' . $this->item->lastname; ?></small>
		</h1>
    </div>
<?php endif; ?>


<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_CATEGORY'); ?></th>
			<td><?php echo $this->item->category; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_LASTNAME'); ?></th>
			<td><?php echo $this->item->lastname; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_FIRSTNAME'); ?></th>
			<td><?php echo $this->item->firstname; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_SETTERNAME'); ?></th>
			<td><?php echo $this->item->settername; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_EMAIL'); ?></th>
			<td><?php echo $this->item->email; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_PHONE'); ?></th>
			<td><?php echo $this->item->phone; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_IMAGE'); ?></th>
			<td><?php echo $this->item->image; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_INFO'); ?></th>
			<td><?php echo nl2br($this->item->info); ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ACT_FORM_LBL_SETTER_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_act&task=setter.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ACT_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_act.setter.'.$this->item->id)) : ?>

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
			<a href="<?php echo JRoute::_('index.php?option=com_act&task=setter.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_ACT_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>