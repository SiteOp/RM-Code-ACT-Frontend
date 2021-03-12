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
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user       = Factory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'sectorform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'sectorform.xml');
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

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

    <?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    
    <div class="table-responsive">
        <table class="table table-striped table-sm"  id="sectorList">
            <thead>
                <tr>
                    <th class="pl-3"><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_SECTORS_SECTOR', 'a.sector', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_SECTORS_BUILDING', 'a.building', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_SECTORS_INOROUT', 'a.inorout', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_NEXT_MAINTENACE_SHORT', 'a.next_maintenance', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_INTERVAL', 'a.maintenance_interval', $listDirn, $listOrder); ?></th>
                    <th class="text-center"><?php echo Text::_('COM_ACT_ACTIONS'); ?></th>
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
                    <td class="pl-3">
                        <a href="<?php echo JRoute::_('index.php?option=com_act&view=sector&id='.(int) $item->id); ?>">
				         <?php echo $this->escape($item->sector); ?>
                        </a>
                    </td>
                    <td><?php echo Text::_('COM_ACT_SECTORS_BUILDING_OPTION_' . $item->building); ?> </td>
                    <td><?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_' . $item->inorout); ?> </td>
                    <td><?php if ($item->next_maintenance > 0 ) : ?>
                        <?php echo HTMLHelper::_('date', $item->next_maintenance, Text::_('DATE_FORMAT_LC4')); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo sprintf("%02d", $item->maintenance_interval) . ' ' . Text::_('COM_ACT_INTERVAL_WEEKS'); ?> </td>
                    <td class="text-center">                
                        <a href="<?php echo Route::_('index.php?option=com_act&task=sectorform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button">
                            <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($canCreate) : ?>
        <a href="<?php echo Route::_('index.php?option=com_act&task=sectorform.edit&id=0', false, 0); ?>"
           class="btn btn-secondary btn-small mt-4">
           <i class="<?php echo Text::_('COM_ACT_FA_ADD_ITEM'); ?>"></i>
            <?php echo Text::_('COM_ACT_SECTORS_ADD_ITEM'); ?>
        </a>
    <?php endif; ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
