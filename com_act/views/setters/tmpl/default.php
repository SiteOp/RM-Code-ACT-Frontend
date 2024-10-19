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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
//use Joomla\CMS\HTML\Helpers\StringHelper;

//HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
//HTMLHelper::_('bootstrap.tooltip');
//HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user       = Factory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'setterform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'setterform.xml');


$authorised = $user->authorise('group.as.admin.manage', 'com_act') || $authorised = $user->authorise('group.as.routesmanager.manage', 'com_act');
if ($authorised !== true)
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}
?>

<?php // Page-Header ?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><i class="fas fa-pencil-ruler"></i> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
<?php else : ?>
    <div class="page-header">
		<h1 itemprop="headline">
		<i class="fas fa-pencil-ruler"></i> <?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
    </div>
<?php endif; ?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

	<div class="table-responsive ">    
		<table class="table table-striped table-sm" id="setterList">
			<thead>
				<tr>
					<?php if (isset($this->items[0]->state)): ?>
						<th class="text-center" width="5%"><?php echo Text::_('COM_ACT_STATUS'); ?></th>
					<?php endif; ?>
					<th class=''><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_SETTERNAME', 'a.settername', $listDirn, $listOrder); ?></th>
					<th class=''><?php echo Text::_('COM_ACT_SETTERS_BENUTZERRECHTE'); ?></th>
					<th class=''><?php echo Text::_('COM_ACT_SETTERS_BENUTZERKONTO'); ?></th>
					<th class=''><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_LASTNAME', 'a.lastname', $listDirn, $listOrder); ?></th>
					<th class=''><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_FIRSTNAME', 'a.firstname', $listDirn, $listOrder); ?></th>
					<th class=''><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_SETTERS_CATEGORY', 'cat.id', $listDirn, $listOrder); ?></th>
					<?php if ($canEdit): ?>
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
						<td class="text-center">
							<?php if ($item->state == 1): ?>
								<i class="<?php echo Text::_('COM_ACT_FA_PUBLISHED'); ?>"></i>
							<?php else: ?>
								<i class="<?php echo Text::_('COM_ACT_FA_UNPUBLISHED'); ?>"></i>
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<td><?php echo $this->escape($item->settername); ?></td>
					<td>
						<?php $setterUserGroups =  Factory::getUser($item->user_id)->groups; ?>
						<?php if(in_array(7, $setterUserGroups))  {echo ActHelpersAct::getPopover('JADMINISTRATOR', 'fas fa-user-shield fa-lg pr-2'); }?>
						<?php if(in_array(14, $setterUserGroups)) {echo ActHelpersAct::getPopover('COM_ACT_ACL_USERS_RM', 'fas fa-chalkboard-teacher fa-lg pr-2'); }?>
						<?php if(in_array(12, $setterUserGroups)) {echo ActHelpersAct::getPopover('COM_ACT_ACL_USERS_BF', 'fas fa-users fa-lg pr-2'); }?>
						<?php if(in_array(13, $setterUserGroups)) {echo ActHelpersAct::getPopover('COM_ACT_ACL_USERS_AK', 'fas fa-comments fa-lg pr-2'); }?>
						<?php if(in_array(16, $setterUserGroups)) {echo ActHelpersAct::getPopover('COM_ACT_ACL_USERS_JO', 'fas fa-tasks fa-lg pr-2'); }?>
						<?php if(in_array(11, $setterUserGroups)) {echo ActHelpersAct::getPopover('COM_ACT_ACL_USERS_WA', 'fas fa-inbox fa-lg pr-2'); }?>	
						<?php if(in_array(17, $setterUserGroups)) {echo ActHelpersAct::getPopover('COM_ACT_ACL_USERS_ME', 'fas fa-paperclip fa-lg pr-2'); }?>	
					</td>
					<td>
						<?php $username = Factory::getUser($item->user_id)->username; ?>
						<?php if(!empty($username)) : ?>
							<i class="fas fa-link" style="color: #89c200;"></i>  <?php echo Factory::getUser($item->user_id)->username; ?>
						<?php else : ?>
							<i class="fas fa-unlink"></i>
						<?php endif; ?>
					</td>
					<td><?php echo $this->escape($item->lastname); ?></td>
					<td><?php echo $this->escape($item->firstname); ?></td>
					<td>
					 	<a class="popover2" rel="popover" data-container="body" data-placement="top" data-trigger="hover" data-content="<?php echo Text::_($item->category);?> ">
							<?php echo HTMLHelper::_('string.truncate', $item->category, 20, true, false); ?>
						</a>
					</td>
					<?php if ($canEdit): ?>
						<td class="center">
							<a href="<?php echo Route::_('index.php?option=com_act&task=setterform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button">
								<i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
							</a>
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
	<?php echo HTMLHelper::_('form.token'); ?>
</form>



