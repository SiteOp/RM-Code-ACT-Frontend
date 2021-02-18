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

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = Factory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'setterform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'setterform.xml');
$canCheckin = $user->authorise('core.manage', 'com_act');
$canChange  = $user->authorise('core.edit.state', 'com_act');
$canDelete  = $user->authorise('core.delete', 'com_act');
?>

<?php // Page-Header ?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
<?php else : ?>
    <div class="page-header">
		<h1 itemprop="headline">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
    </div>
<?php endif; ?>


<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">

<?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

<div class="table-responsive ">    
	<table class="table table-striped table-sm" id="setterList">
		<thead>
            <tr>
                <?php if (isset($this->items[0]->state)): ?>
                    <th width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_ACT_STATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>

                    <th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_ID', 'a.id', $listDirn, $listOrder); ?></th>
                    <th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_CATEGORY', 'a.category', $listDirn, $listOrder); ?></th>
                    <th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_LASTNAME', 'a.lastname', $listDirn, $listOrder); ?></th>
                    <th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_FIRSTNAME', 'a.firstname', $listDirn, $listOrder); ?></th>
                    <th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_SETTERNAME', 'a.settername', $listDirn, $listOrder); ?></th>

                <?php if ($canEdit || $canDelete): ?>
                    <th class="center"><?php echo Text::_('COM_ACT_ACTIONS'); ?></th>
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
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_act'); ?>

							<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_act')): ?>
					<?php $canEdit = Factory::getUser()->id == $item->created_by; ?>
				<?php endif; ?>

			<tr class="row<?php echo $i % 2; ?>">

				<?php if (isset($this->items[0]->state)) : ?>
					<?php $class = ($canChange) ? 'active' : 'disabled'; ?>
					<td class="center">
	<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=setter.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
	<?php if ($item->state == 1): ?>
		<i class="<?php echo Text::_('COM_ACT_FA_PUBLISHED'); ?>"></i>
	<?php else: ?>
		<i class="<?php echo Text::_('COM_ACT_FA_UNPUBLISHED'); ?>"></i>
	<?php endif; ?>
	</a>
</td>
				<?php endif; ?>

				<td><?php echo $item->id; ?></td>
				<td><?php echo $item->category; ?></td>
                <td><?php echo $item->lastname; ?></td>
				<td><?php echo $item->firstname; ?></td>
				<td>
                    <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'setters.', $canCheckin); ?>
                    <?php endif; ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=setter&id='.(int) $item->id); ?>">
                            <?php echo $this->escape($item->settername); ?>
                        </a>
				</td>

				<?php if ($canEdit || $canDelete): ?>
					<td class="center">
						<?php if ($canEdit): ?>
							<a href="<?php echo Route::_('index.php?option=com_act&task=setterform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button">
                                <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                            </a>
						<?php endif; ?>
					</td>
				<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

	<?php if ($canCreate) : ?>
		<a href="<?php echo Route::_('index.php?option=com_act&task=setterform.edit&id=0', false, 0); ?>"
		   class="btn btn-secondary btn-small mt-4">
           <i class="icon-plus"></i>
			<?php echo Text::_('COM_ACT_SETTER_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>



