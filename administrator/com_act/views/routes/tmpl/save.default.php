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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user        = JFactory::getUser();
$user_groups = $user->get('groups');
$userId      = $user->get('id');
$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');
$canCreate   = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'routeform.xml');
$canEdit     = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'routeform.xml');
$canCheckin  = $user->authorise('core.manage', 'com_act');
$canChange   = $user->authorise('core.edit.state', 'com_act');
$canDelete   = $user->authorise('core.delete', 'com_act');
$adminUser   = $user->authorise('core.admin', 'com_actj');													 
?>
<?php foreach ($user_groups as $group)
{
    echo '<p>Group = ' . $group . '</p>';
} ?>
<?php if($adminUser) : ?>Hallo Admin<?php endif; ?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post"name="adminForm" id="adminForm">

	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

	<table class="table table-striped" id="routeList">
		<thead>
		<tr>
		
			<?php if($adminUser) : ?>
				<?php if (isset($this->items[0]->state)): ?>
					<th width="5%"><?php echo JHtml::_('grid.sort', 'Status', 'a.state', $listDirn, $listOrder); ?></th>
				<?php endif; ?>
			<?php endif; ?>
				
				<th class=''><?php echo JHtml::_('grid.sort',  'Linie', 'a.line', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Farbe', 'a.color', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Routenname', 'a.name', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Grad', 'Calc_Grad', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Franz.', 'a.calc_franz', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Vorschlag <br /> Routenbauer', 'a.settergrade', $listDirn, $listOrder); ?></th>
				
			<?php if($adminUser) : ?>
				<th class=''><?php echo JHtml::_('grid.sort',  'Info', 'a.info', $listDirn, $listOrder); ?></th>
			<?php endif; ?>
			
				<th class=''><?php echo JHtml::_('grid.sort',  'Bewertung', 'AvgStars', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'In-/Outdoor', 'inorout', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Sektor', 'lineSectorName', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Routenbauer', 'a.setter', $listDirn, $listOrder); ?></th>
				<th class=''><?php echo JHtml::_('grid.sort',  'Datum', 'a.createdate', $listDirn, $listOrder); ?></th>
				
			<?php if($adminUser) : ?>
				<th class=''><?php echo JHtml::_('grid.sort',  'Letzte Änderung', 'a.modified', $listDirn, $listOrder); ?></th>
				<th class="center"><?php echo JText::_('Bearbeiten'); ?></th>
			<?php endif; ?>
			
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		
<?php // Routelist Start ?>
		<?php foreach ($this->items as $i => $item) : ?>
	
			<?php //$canEdit = $user->authorise('core.edit', 'com_act'); ?>
				<?php //if (!$canEdit && $user->authorise('core.edit.own', 'com_act')): ?>
					<?php //$canEdit = JFactory::getUser()->id == $item->created_by; ?>
				<?php //endif; ?>

			<tr class="row<?php echo $i % 2; ?>">
				<?php if (isset($this->items[0]->state)) : ?>
				<?php if($adminUser) : ?>
				<?php $class = ($canChange) ? 'active' : 'disabled'; ?>
				<td class="center">
					<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canChange) ? JRoute::_('index.php?option=com_act&task=route.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
						<?php if ($item->state == 1): ?>
							<i class="icon-publish"></i>
						<?php else: ?>
							<i class="icon-unpublish"></i>
						<?php endif; ?>
					</a>
				</td>
				<?php endif; ?>
				<?php endif; ?>
				
				<td><?php echo $item->line; ?></td>
				<td><?php echo $item->color; ?></td>
				<td>
					<?php if (isset($item->checked_out) && $item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'routes.', $canCheckin); ?>
					<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_act&view=route&id='.(int) $item->id); ?>"><?php echo $this->escape($item->name); ?></a>
				</td>
	
				
				<td><?php $Calc_Grad2 = round($item->Calc_Grad, 0); ?>   
				<?php echo $item->Calc_Grad; ?><br />
					<?php echo $item->$Calc_Grad2 = str_replace
								(array('10', '11', '12', '13', '14', '15', '16','17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36',),	
								 array('3', '3+', '4-', '4', '4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+','9-','9','9+','10-','10','10+','11-','11','11+','12-',), 
									   $Calc_Grad2); ?>
					<br />
					<?php echo $item->CountMyGrade; ?> / 
					<?php echo $item->AvgMyGrade =  str_replace(
								array('10', '11', '12', '13', '14', '15', '16','17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36',),	
					    		array('3', '3+', '4-', '4', '4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+','9-','9','9+','10-','10','10+','11-','11','11+','12-',), 
								      $item->AvgMyGrade); ?>
				</td>
				
				<td><?php //########################### Ausgabe Convert Franz ?></td>
				
			
				<td>
					<?php echo $item->settergrade = str_replace(array('10', '11', '12', '13', '14', '15', '16','17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36',),	
																array('3','3+','4-','4','4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+','9-','9','9+','10-','10','10+','11-','11','11+','12-',),
																$item->settergrade); ?>
				</td>
			<?php if($adminUser) : ?>
				<td><?php echo $item->info; ?></td>
			<?php endif; ?>
			
				<td><?php echo $item->CountStars; ?>/<?php echo $item->AvgStars; ?></td>
				<td><?php echo $item->inorout; ?></td>
				<td><?php echo $item->lineSectorName; ?></td>		
				<td><?php echo $item->setter; ?></td>
				<td><?php echo JHtml::_('date', $item->createdate, 'd.m.Y'); ?></td>
				
			<?php if($adminUser) : ?>
				<td><?php echo $item->comment_modified; ?></td>
			<?php endif; ?>
			
			<?php if ($canEdit || $canDelete): ?>
				<td class="center">
					<?php if ($canEdit): ?>
						<a href="<?php echo JRoute::_('index.php?option=com_act&task=routeform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
					<?php endif; ?>
					<?php if ($canDelete): ?>
						<a href="<?php echo JRoute::_('index.php?option=com_act&task=routeform.remove&id=' . $item->id, false, 2); ?>" class="btn btn-mini delete-button" type="button"><i class="icon-trash" ></i></a>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($canCreate) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_act&task=routeform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo JText::_('COM_ACT_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php if($canDelete) : ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo JText::_('COM_ACT_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>
<?php endif; ?>
<?php print_R($this->items); ?>