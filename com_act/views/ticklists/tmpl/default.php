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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'commentform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'commentform.xml');
$canCheckin = $user->authorise('core.manage', 'com_act');
$canChange  = $user->authorise('core.edit.state', 'com_act');
$canDelete  = $user->authorise('core.delete', 'com_act');

// Routenstatus 1 = Freigegeben, -1 Vorgemerkt
$routeStateOk = array(1,-1);


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

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="get" name="adminForm" id="adminForm" class="mt-5">

    <?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>


<?php // Statistik Getickte Routen ?>
<div class="row mb-5">
 <div class=" col-12">
 <div class="card">
        <div class="card-header">
        <h3> <i class="fas fa-sort-amount-up-alt"></i><?php echo Text::_('COM_ACT_TICKLIST_PERFORMANCE'); ?></h3>
        </div>
            <div class="card-body">
                 <canvas id="chart" style="height:25vh; width:100vw"></canvas>
            </div>
       </div>
    </div>
</div>

    <?php if (sizeof($this->items) !== 0 ) : ?> <?php // Prüfe ob es überhaubpt Ergebnisse gibt ?>

    <div class="table-responsive">
    <table class="table table-striped table-sm" id="commentList">
        <thead>
            <tr>
                <th class="tl_route pl-2"><?php // STATE ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_ROUTENAME', 'a.route', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_vrgrade text-center"><?php // C-Grade ?>
                    <div class=""> <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_C_GRADE', 'COM_ACT_ROUTE_POPOVER_TXT_C_GRADE'); ?></div>
                    <?php echo JHtml::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_C_GRADE', 'orderCGrade', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_mygrade text-center"><?php // My Grade ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_MYGRADE', 'orderMyGrade', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_avg d-none d-lg-table-cell"><?php // AVG ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_STARS', 'COM_ACT_ROUTE_POPOVER_TXT_STARS'); ?><br />
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_STARS', 'a.stars', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_ascent"><?php // Durchstieg ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_ASCENT', 'a.ascent', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_tries text-center d-none d-md-table-cell"><?php // Versuche ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_TRIES', 'a.tries', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_climbdate d-none d-md-table-cell"><?php // Climbdate ?>
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_CLIMBDATE', 'a.climbdate', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_tcomment"><?php // Tick Comment ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_TICKL_POPOVER_HEAD_TICKCOMMENT', 'COM_ACT_TICKL_POPOVER_TXT_TICKCOMMENT'); ?><br />
                    <?php echo JHtml::_('grid.sort',  'COM_ACT_TABLE_HEADER_TICKLIST_TICK_COMMENT', 'a.tick_comment', $listDirn, $listOrder); ?>
                </th>
                <th class="tl_edit text-center"><?php // Edit ?>
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
                        <td class="pl-2"><?php // Route Name  ## If Route State 1 - Then link else echo Name?>
                            <?php if (!in_array($this->route->state, $routeStateOk)) : ?>	
                                <a href="<?php echo JRoute::_('index.php?option=com_act&view=route&id='.(int) $item->route_id); ?>"><?php echo $this->escape($item->route_name); ?></a>
                            <?php else :?>
                                <?php echo $this->escape($item->route_name); ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php // Settergrade ?>
                            <?php echo (0 == (int)$item->c_grade) ? '-' : $item->c_grade; ?>
                        </td>
                        <td class="text-center"><?php // MY-Grade ?>
                            <?php echo (0 == (int)$item->my_grade) ? '-' : $item->my_grade; ?>
                        </td>
                        <td class="d-none d-lg-table-cell"><?php // AVG ?>
                            <div class="Stars" style=" --star-size: 150%; --rating: <?php echo ActHelpersAct::getStarsRound($item->stars); ?>;"></div>
                        </td>
                        <td class="text-center"><?php // Durchstieg ?>
							 <?php echo ActHelpersAct::getUserAscentIcon($item->ascent); ?> <?php // Anzeige der Begehung als Icon ; ?>
                        </td>
                        <td class="text-center d-none d-md-table-cell"><?php // Versuche ?>
                             <?php echo $item->tries; ?>
                        </td>
                        
                        <td class="d-none d-md-table-cell"><?php // Climbdate ?>
                            <?php echo ($item->climbdate == 0) ? '' : HTMLHelper::_('date', $item->climbdate, Text::_('DATE_FORMAT_LC4')); ?><?php // Short if else ?>
                        </td>
                        <td><?php // Comment Ticklist ## Truncate ?>
                            <?php if (!empty($item->tick_comment)) : ?>
                                <a rel="popover" 
                                    data-placement="right" 
                                    data-trigger="hover" 
                                    data-content="<?php echo $item->tick_comment; ?>" 
                                    >
                                    <?php echo HTMLHelper::_('string.truncate', $item->tick_comment, 20, false, false ); ?>
                                </a>
                            <?php endif; ?>
                        </td>

                        <td class="text-center"><?php // Edit ?>
                        <?php if ((!$canEdit) OR (!in_array($this->route->state, $routeStateOk))) : ?> 
                                <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $item->id, false, 2); ?>" class="pr-3" type="button"><i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i></a>
                            <?php endif; ?>
                        </td>
                    
                    </tr>
            <?php endforeach; ?>
    <?php else: ?> <?php // Wenn Keine Ergebnisse bei den Suchfiltern ?>
        <span class="alert alert-info inline-flex" role="alert">
            <i class="<?php echo Text::_('COM_ACT_FA_NO_VALUE'); ?>"></i> <?php echo Text::_('COM_ACT_SEARCH_FILTER_NO_VALUE'); ?>
        </span>
    <?php endif; ?>
    </tbody>
    </table>


    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
    </div>
</form>


<?php // Load Template Charts.js ?>
<?php echo $this->loadTemplate('charts'); ?>