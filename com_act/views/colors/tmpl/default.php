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

use Joomla\CMS\Language\Text;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$params  = $this->params;
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'colorform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'colorform.xml');
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

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
<div class="table-responsive ">    
	<table class="table table-striped table-sm" id="colorList">
		<thead>
		<tr>
			<?php if (isset($this->items[0]->state)): ?>
				<th width="5%">
                    <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                </th>
			<?php endif; ?>
				<th class=''><?php // ID ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
				<th class=''><?php // Color ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_COLOR', 'a.color', $listDirn, $listOrder); ?>
				</th>
				<th class=''><?php // RGB-Code ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_COLOR_RGB', 'a.rgbcode', $listDirn, $listOrder); ?>
				</th>
				<th class="center"><?php // Edit ?>
                    <?php echo JText::_('COM_ACT_ACTIONS'); ?>
				</th>
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
					<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
				<?php endif; ?>

			<tr class="row<?php echo $i % 2; ?>">
				<?php if (isset($this->items[0]->state)) : ?>
					<?php $class = ($canChange) ? 'active' : 'disabled'; ?>
					<td class="center">
                        <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? JRoute::_('index.php?option=com_act&task=color.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
                            <?php if ($item->state == 1): ?>
                               <i class="<?php echo Text::_('COM_ACT_FA_PUBLISHED'); ?>"></i>
                            <?php else: ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_UNPUBLISHED'); ?>"></i>
                            <?php endif; ?>
                        </a>
                    </td>
				<?php endif; ?>

				<td><?php // ID ?>
                    <?php echo $item->id; ?>
                </td>
				<td><?php // Color ?>
                    <span class="routecolor" style="background: <?php echo $item->rgbcode; ?>;"></span>
                    <?php echo $item->color; ?>
                </td>
				<td><?php // RGB-Code ?>
                    <?php echo $item->rgbcode; ?>
                </td>
				<td class="center">
					<a href="<?php echo JRoute::_('index.php?option=com_act&task=colorform.edit&id=' . $item->id, false, 2); ?>" class="" type="button">
                        <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                    </a>
                </td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
	<?php if ($canCreate) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_act&task=colorform.edit&id=0', false, 0); ?>"
		    class="btn btn-secondary btn-small mt-4">
            <i class="icon-plus"></i>
			<?php echo JText::_('COM_ACT_COLORS_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>


