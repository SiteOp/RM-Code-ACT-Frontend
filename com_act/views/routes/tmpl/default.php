<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder sp�ter; siehe LICENSE.txt
 */


// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// ACT Params 
$params    = JComponentHelper::getParams('com_act');
$calcgrade = $params['calculatedyn']; // Ausgabe des Kalkulierten Grades ?   | if ($calcgrade == 1 )
$line      = $params['lineyn'];

$newRouteDateRange  = $params['newroutedaterange']; // Config Anzahl Tage wann Route Label Neu
$newRouteDateRange = 24*60*60*$newRouteDateRange;
$unix_date = strtotime(Factory::getDate());

$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');



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

<form action="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>" method="get" name="adminForm" id="adminForm">

<?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

<?php if (sizeof($this->items) !== 0 ) : ?> <?php // Pr�fe ob es �berhaubpt Ergebnisse gibt ?>

    <div class="table-responsive ">
        <table class="table table-striped table-sm" id="routeList">
            <thead>
            <tr>
                <th class="r_info text-center"><?php // Info Popover ?>
                    <?php echo Text::_('COM_ACT_LBL_INFO'); ?>
                </th>
                <?php if ($line == 1 ) : ?><?php // Line ?>
                <th class="r_line d-none d-xxl-table-cell">
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_LINE', 'l.line', $listDirn, $listOrder); ?> 
                </th>
                <?php endif ;?>
                <th class="r_color text-center d-none d-md-table-cell"><?php // Color ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_COLOR', 'c.color', $listDirn, $listOrder); ?>
                </th>
                <th class="r_name"><?php // Name Route ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_NAME', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <?php if ($calcgrade == 1 ) : ?>
                <th class="r_cgrade text-center"> <?php // C-Grade ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_C_GRADE', 'COM_ACT_ROUTE_POPOVER_TXT_C_GRADE'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_C_GRADE', 'Calc_Grad', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>   
                <th class="r_vrgrade text-center"><?php // VR_Grade ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_VR_GRADE', 'COM_ACT_ROUTE_POPOVER_TXT_VR_GRADE'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTER_GRADE', 'a.settergrade', $listDirn, $listOrder); ?>
                </th>    
               
                <th class="r_avg"><?php // AVG ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_STARS', 'COM_ACT_ROUTE_POPOVER_TXT_STARS'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_REVIEW', 'AvgStars', $listDirn, $listOrder); ?>
                </th>
                <th class="r_count"><?php // Count ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_REVIEW_COUNT', 'COM_ACT_ROUTE_POPOVER_TXT_REVIEW_COUNT'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_REVIEW_COUNT', 't.count_stars', $listDirn, $listOrder); ?>
                </th>
                <th class="r_setter d-none d-lg-table-cell"><?php // Setter ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTERNAME', 's.settername', $listDirn, $listOrder); ?>
                </th>
                <th class="r_setterdate d-none d-md-table-cell"><?php // Setterdate ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTERDATE', 'a.setterdate', $listDirn, $listOrder); ?>
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

                    <td class="r_info text-center"><?php // Info Popover ?>
                         <a class="" rel="popover" 
                                data-placement="right" 
                                data-html="true" 
                                data-trigger="hover" 
                                title=" 
                                    <?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_' . $item->inorout); ?> |
                                    <?php echo $item->building = Text::_('COM_ACT_SECTORS_BUILDING_OPTION_' . $item->building); ?>
                                      "
                                data-content=" 
                                    <div class='pop_line'>Linie: <?php echo $item->line; ?></div>
                                    <div class=''><?php echo $item->lineSectorName; ?> </div>
                                    <div class='pop_setter d-lg-none'><?php echo $item->settername; ?></div>
                                    <div class='pop_setterdate d-lg-none'> <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?></div>
                                    
                                    ">
                            <?php if (1 == $item->inorout ) : ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_INDOOR'); ?>"></i>
                            <?php else : ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_OUTDOOR'); ?>"></i>
                            <?php endif; ?>
                        </a>
                    </td>
                    <?php  if ($line == 1 ) : ?><?php // Line Number ?>
                    <td class="d-none d-xxl-table-cell">
                         <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[line]=<?php echo $item->lineId; ?>">
                            <?php echo $item->line; ?>
                        </a>
                    </td>
                    <?php endif; ?>
                    <td class="text-center d-none d-md-table-cell"><?php // Color ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[color]=<?php echo $item->colorId; ?>">
                            <span class="routecolor" style="background: <?php echo $item->rgbcode; ?>;"></span>
                        </a>
                    </td>
					
                    <td class=""> <?php // Setterdate older then 14 Day ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $item->id); ?>"><?php echo $this->escape($item->name); ?></a>
                          <?php if (strtotime($item->setterdate) > ($unix_date - $newRouteDateRange) ): ?><span class="new_route"><?php echo Text::_('COM_ACT_ROUTE_NEW_ROUTE'); ?></span> <?php endif; ?>
						  <?php $options = explode(",", $item->lineoption); ?>
						<?php foreach ($options as $option) : ?>
						  <?php echo ActHelpersAct::getLineoptions($option); ?>
						<?php endforeach ; ?>
						 
                    </td>
                    <?php if ($calcgrade == 1 ) : ?><?php // C-Grade - UIAA by helper ?>
                    <td  class="text-center">
                        <?php if ($item->Calc_Grad == 0) :?>
                            <?php echo '-'; ?>
                        <?php else: ?>
                              <?php echo ActHelpersAct::uiaa(round($item->Calc_Grad,0)); ?>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td class="text-center"><?php // VR_Grade Wenn Kommentare gesperrt dann - Wichtig f�r Speedroute ?>
                     <?php echo (1 == $item->exclude) ? '-' : ActHelpersAct::uiaa($item->settergrade); ?><?php // Short if else ?>
                    </td> 
                    <td class="d-none d-sm-table-cell" >  <?php // AVG  ?>
                        <?php if (0 == $item->exclude) : ?>
							<div class="Stars" style="--star-size: 160%; --rating: <?php echo ActHelpersAct::getStarsRound($item->AvgStars); ?>;"></div>

                        <?php endif; ?>
                    </td>
                    <td class="d-sm-none text-center" >  <?php // AVG nur Ausgabe Text f�r Smartphone  ?>
                        <?php echo round($item->AvgStars,0); ?>
                    </td>
                    <td  class=""><?php // Count User ?>
                        <?php echo (empty($item->count_stars)) ? '(0)' : '(' .$item->count_stars. ')'; ?><?php // Short if else ?>
                    </td>

                    <td class="d-none d-lg-table-cell">
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[settername]=<?php echo $item->setterId; ?>">
                             <?php echo $item->settername; ?>
                        </a>
                    </td>
                    <td class="d-none d-md-table-cell"><?php // Setterdate ?>
                        <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?>
                    </td>

                </tr>
            <?php endforeach; ?>
<?php else: ?><?php // Wenn keine Ergebnisse dann Text ausgeben Sorry.... ?>
    <span class="alert alert-info inline-flex" role="alert">
        <i class="<?php echo Text::_('COM_ACT_FA_NO_VALUE'); ?>"></i> <?php echo Text::_('COM_ACT_SEARCH_FILTER_NO_VALUE'); ?>
    </span>
<?php endif; ?>
        </tbody>
    </table>
</div>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
