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

use Joomla\CMS\Language\Text;


JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'sponsorform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'sponsorform.xml');
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
	
<div class="table-responsive">
    <table class="table table-striped table-sm" id="sponsorList">
		<thead>
		<tr>
			<th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_ID', 'a.id', $listDirn, $listOrder); ?></th>
			<th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SPONSORS_NAME', 'a.name', $listDirn, $listOrder); ?></th>
			<th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SPONSORS_MEDIA', 'a.media', $listDirn, $listOrder); ?></th>
			<th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SPONSORS_URL', 'a.url', $listDirn, $listOrder); ?></th>
			<th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SPONSORS_TXT', 'a.txt', $listDirn, $listOrder); ?></th>
			<th class=''><?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_SPONSORS_CONTACT', 'a.contact', $listDirn, $listOrder); ?></th>
            <th class="center">
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
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php // ID ?>
                    <?php echo $item->id; ?>
                </td>
				<td><?php // Sponsor Name ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_act&view=sponsor&id='.(int) $item->id); ?>">
                        <?php echo $this->escape($item->name); ?>
                    </a>
				</td>
				<td><?php // Sponsor Logo ?>
                   <?php echo JHtml::_('image', $item->media, $this->escape($item->name), array('width' => 120, 'height' => ''), false, -1); ?>
                </td>
				<td><?php // Sponsor URL ?>
                    <?php echo JHTML::link($item->url, $this->escape($item->name), array('target' => '_blank')); ?>

                </td>
				<td><?php // Sponsor Description ?>
                    <?php echo $item->txt; ?>
                </td>
				<td><?php // Sponsor Contact ?>
                    <?php echo $item->contact; ?>
                </td>
                <td class="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_act&task=sponsorform.edit&id=' . $item->id, false, 2); ?>" class="" type="button">
                        <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                    </a>
                </td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

	<?php if ($canCreate) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_act&task=sponsorform.edit&id=0', false, 0); ?>"
		    class="btn btn-secondary btn-small mt-4">
            <i class="icon-plus"></i>
			<?php echo JText::_('COM_ACT_SPONSOR_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
