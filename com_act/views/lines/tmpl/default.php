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
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'lineform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'lineform.xml');
$canCheckin = $user->authorise('core.manage', 'com_act');
$canChange  = $user->authorise('core.edit.state', 'com_act');
$canDelete  = $user->authorise('core.delete', 'com_act');


// ACT Params 
$params    = JComponentHelper::getParams( 'com_act' );
$pdf = $params['admin_lines_pdf'];
$indicator = $params['admin_lines_indicator'];

// Lade Globale Sprachdateien
$lang = Factory::getLanguage();
$extension = 'com_act_global';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
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

    <?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    <div class="table-responsive ">    
        <table class="table table-striped table-sm" id="lineList">
            <thead>
                <tr>
                    <th class="text-center" width="5%"><?php echo HTMLHelper::_('grid.sort',  'ACTGLOBAL_STATUS', 'a.locked', $listDirn, $listOrder); ?></th>
                    <th class="text-center"><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_LINE', 'a.line', $listDirn, $listOrder); ?></th>
                    <?php if (1 == $indicator) : ?>
                        <th width="5%"><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_INDICATOR', 'a.indicator', $listDirn, $listOrder); ?></th>
                    <?php endif; ?>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_SECTOR', 'a.sector', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_LBL_ROUTE_HEIGHT', 'a.height', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MAKER', 'a.maker', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_NEXT_MAINTENACE_SHORT', 'a.next_maintenance', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort',  'COM_ACT_INTERVAL', 'a.maintenance_interval', $listDirn, $listOrder); ?></th>
                    <?php if ($canEdit || $canDelete): ?>
                        <th class="center">
                            <?php echo Text::_('COM_ACT_ACTIONS'); ?>
                        </th>
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
                  
                   
                    <tr class="row<?php echo $i % 2; ?> <?php if(3==$item->state) {echo "table-danger";} ?>">
                    <td class="text-center">
                    <?php if ($item->state == 1): ?><?php // Freigegeben  ?>
                        <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=line.publish&id=' . $item->id . '&state= 3') : '#'; ?>">
                            <i class="<?php echo Text::_('ACTGLOBAL_FA_CHECK'); ?>"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($item->state == 3): ?><?php // Freigegeben  ?>
                        <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=line.publish&id=' . $item->id . '&state= 1') : '#'; ?>">
                            <i class="<?php echo Text::_('ACTGLOBAL_FA_LOCK'); ?>"></i>
                        </a>
                    <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $item->line; ?>
                        <?php if(1 == $pdf) : ?><?php // ist in der Config PDF auf Ja? ?>
                            <a class="ml-3" href="/createPDF/jdisplay.php?line=<?php echo (int)$item->line; ?>">  <i style="color: red;" class="far fa-file-pdf"></i></a>
                            <?php // (int) wichtig damit die Linie 001 gleich 1 ist ?>
                        <?php endif; ?>
                    </td>
                    <?php if (1 == $indicator) : ?>
                        <td width="5%" class="text-center"><?php echo $item->indicator; ?></td>
                    <?php endif; ?>
                    <td><?php echo $item->lineSectorName; ?></td>
                    <td><?php echo (empty($item->height)) ? '' : $item->height . ' ' . Text::_('COM_ACT_TABLE_LBL_ROUTE_UNIT'); ?> </td><?php // Short if else ?>
                    <td><?php echo Text::_('COM_ACT_FORM_LBL_LINE_MAKER_' . $item->maker); ?> </td>
                    <td><?php if ($item->next_maintenance > 0 ) : ?>
                        <?php echo HTMLHelper::_('date', $item->next_maintenance, Text::_('DATE_FORMAT_LC4')); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo sprintf("%02d", $item->maintenance_interval) . ' ' . Text::_('COM_ACT_INTERVAL_WEEKS'); ?> </td>
                    <?php if ($canEdit): ?>
                        <td class="center">
                            <?php if ($canEdit): ?>
                                <a href="<?php echo Route::_('index.php?option=com_act&task=lineform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button">
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
        <a href="<?php echo Route::_('index.php?option=com_act&task=lineform.edit&id=0', false, 0); ?>"
        class="btn btn-secondary btn-small mt-4">
            <i class="<?php echo Text::_('COM_ACT_FA_ADD_ITEM'); ?>"></i>
            <?php echo Text::_('COM_ACT_LINE_ADD_ITEM'); ?></a>
    <?php endif; ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

