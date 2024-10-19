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

// Colors
JLoader::import('helpers.colors', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');

// ACT Params 
$params    = JComponentHelper::getParams('com_act');
$calcgrade              = $params['calculatedyn']; // Ausgabe des Kalkulierten Grades ?   | if ($calcgrade == 1 )
$line                   = $params['lineyn'];
$use_routesetter        = $params['use_routesetter'];
$use_setterdate         = $params['use_setterdate'];
$use_route_properties   = $params['use_route_properties'];
$use_line_properties    = $params['use_line_properties'];
$use_route_lifetime     = $params['use_route_lifetime']; // Removedate Lifetime einer Route
$use_info_icon          = $params['use_info_icon'];
$use_row_line           = $params['use_row_line'];
$use_review_count       = $params['use_review_count'];
$use_row_sector         = $params['use_row_sector'];


$newRouteDateRange  = $params['newroutedaterange']; // Config Anzahl Tage wann Route Label Neu
$newRouteDateRange = 24*60*60*$newRouteDateRange;
$unix_date = strtotime(Factory::getDate());

$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');

// Lade Globale Sprachdateien
$lang = Factory::getLanguage();
$extension = 'com_act_global';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

// Add styles
$document = Factory::getDocument();
$style .=  1==$use_routesetter ? '' : '#filter_settername, #filter_settername_chosen {display: none;}';
$document->addStyleDeclaration($style);

// Search Field C-Grade und VR-Grade Text anpassen
$document->addScript("/templates/b4/js/changeSelectOptionsValue.js", array(), array('async'=>'async'));

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

    <div class="table-responsive">
        <table class="table table-striped table-sm" id="routeList">
            <thead>
            <tr>
                <?php if(1 == $use_info_icon) : ?>
                <th class="r_info text-center"><?php // Info Popover ?>
                    <?php echo Text::_('COM_ACT_LBL_INFO'); ?>
                </th>
                <?php endif; ?>
                <th class="r_color text-center"><?php // Color ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_COLOR', 'c.color', $listDirn, $listOrder); ?>
                </th>
                <th class="r_name"><?php // Name Route ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_NAME', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <?php if (1 == $use_row_sector  ) : ?><?php // Sektor ?>
                <th class="">
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_SECTORS_SECTOR', 'sectorID', $listDirn, $listOrder); ?> 
                </th>
                <?php endif ;?>
                <?php if (1 == $use_row_line ) : ?><?php // Line ?>
                <th class="r_line">
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_LINE', 'l.line', $listDirn, $listOrder); ?> 
                </th>
                <?php endif ;?>
                <?php if ($calcgrade == 1 ) : ?>
                <th class="r_cgrade text-center"> <?php // C-Grade ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_C_GRADE', 'COM_ACT_ROUTE_POPOVER_TXT_C_GRADE'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_C_GRADE', 'orderCGrade', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>   
                <th class="r_vrgrade text-center"><?php // VR_Grade ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_VR_GRADE', 'COM_ACT_ROUTE_POPOVER_TXT_VR_GRADE'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTER_GRADE', 'orderVrGrade', $listDirn, $listOrder); ?>
                </th>    
               
                <th class="r_avg"><?php // AVG ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_STARS', 'COM_ACT_ROUTE_POPOVER_TXT_STARS'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_REVIEW', 'AvgStars', $listDirn, $listOrder); ?>
                </th>
                <?php if(1==$use_review_count) : ?>
                <th class="r_count  d-none d-md-table-cell"><?php // Count ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_REVIEW_COUNT', 'COM_ACT_ROUTE_POPOVER_TXT_REVIEW_COUNT'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_REVIEW_COUNT', 't.count_stars', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>
                <?php if(1==$use_routesetter) : ?>
                <th class="r_setter d-none d-lg-table-cell"><?php // Setter ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTERNAME', 's.settername', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>
                <?php if(1==$use_setterdate) : ?>
                <th class="r_setterdate d-none d-md-table-cell"><?php // Setterdate ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTERDATE', 'a.setterdate', $listDirn, $listOrder); ?>
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
                <tr class="row<?php echo $i % 2; ?>">
                    <?php if(1 == $use_info_icon) : ?>
                    <td class="r_info text-center"><?php // Info Popover ?>
                         <a class="" rel="popover" 
                                data-placement="right" 
                                data-html="true" 
                                data-trigger="hover" 
                                title=" 
                                    <?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_' . $item->inorout); ?> |
                                    <?php echo $item->building =  ActHelpersAct::getBuildingName($item->building); ?>
                                      "
                                data-content=" 
                                    <div class='pop_line'>Linie: <?php echo $item->line; ?></div>
                                    <div class=''><?php echo $item->lineSectorName; ?> </div>
                                    <?php if(1==$use_routesetter) : ?>
                                    <div class='pop_setter d-lg-none'><?php echo $item->settername; ?></div>
                                    <?php endif;?>
                                    <?php if(1==$use_setterdate) : ?>
                                    <div class='pop_setterdate d-lg-none'> <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?></div>
                                    <?php endif;?>
                                    
                                    ">
                            <?php if (1 == $item->inorout ) : ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_INDOOR'); ?>"></i>
                            <?php else : ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_OUTDOOR'); ?>"></i>
                            <?php endif; ?>
                        </a>
                    </td>
                    <?php endif; ?>
                    <td class="text-center"><?php // Color ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[color]=<?php echo $item->colorId; ?>">
                        <?php echo ColorsHelpersColors::getColor($item->rgbcode, $item->rgbcode2, $item->rgbcode3); ?>
                        </a>
                    </td>

                    <td class=""> <?php // Setterdate older then 14 Day ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $item->id); ?>"><?php echo $this->escape($item->name); ?></a>
                        <?php // Removedate Lifetime der Route ?>
                        <?php if((1==$use_route_lifetime) AND (1==$item->lifetime)) : ?> 
                         <?php echo ActHelpersAct::getRemoveRouteIcon($item->lifetime, 1); ?>
                        <?php endif; ?>
                        <?php // Route NEWS ?>
                        <?php if (strtotime($item->setterdate) > ($unix_date - $newRouteDateRange) ): ?><span class="new_route"><?php echo Text::_('COM_ACT_ROUTE_NEW_ROUTE'); ?></span> <?php endif; ?>
                        <?php // Linienoptionen ?>
                        <?php $options = explode(",", $item->lineoption); ?>
						<?php foreach ($options as $option) : ?>
						  <?php echo ActHelpersAct::getLineoptions($option); ?>
						<?php endforeach ; ?>
                    </td>
                    <?php if(1==$use_row_sector) : ?><?php // Sektor ?>
                    <td class="r_setter">
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[sector]=<?php echo $item->sectorID; ?>">
                            <?php echo $item->lineSectorName; ?>
                        </a>
                    </td>
                    <?php endif; ?>
                    <?php  if (1 == $use_row_line ) : ?><?php // Line Number ?>
                    <td class="">
                         <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[line]=<?php echo $item->lineId; ?>">
                            <?php echo $item->line; ?>
                        </a>
                    </td>
                    <?php endif; ?>

                    <?php if ($calcgrade == 1 ) : ?><?php // C-Grade ?>
                    <td  class="text-center">
                        <?php echo (0 == (int)$item->c_grade) ? '-' : $item->c_grade; ?>
                    </td>
                    <?php endif; ?>
                    <td class="text-center"><?php // VR_Grade Wenn Kommentare gesperrt dann - Wichtig für Speedroute ?>
                     <?php echo (1 == $item->exclude OR 0 == (int)$item->s_grade) ? '-' : $item->s_grade; ?><?php // Short if else ?>
                    </td> 
                    <td class="d-none d-sm-table-cell" >  <?php // AVG  ?>
                        <?php if (0 == $item->exclude) : ?>
							<div class="Stars" style="--star-size: 160%; --rating: <?php echo ActHelpersAct::getStarsRound($item->AvgStars); ?>;"></div>
                        <?php endif; ?>
                    </td>
                    <td class="d-sm-none text-center" >  <?php // AVG nur Ausgabe Text für Smartphone  ?>
                        <?php echo round($item->AvgStars,0); ?>
                    </td>
                    <?php if(1==$use_review_count) : ?>
                    <td  class=" d-none d-md-table-cell"><?php // Count User ?>
                        <?php echo (empty($item->count_stars)) ? '(0)' : '(' .$item->count_stars. ')'; ?><?php // Short if else ?>
                    </td>
                    <?php endif; ?>
                    <?php if(1==$use_routesetter) : ?>
                    <td class="d-none d-lg-table-cell">
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routes'); ?>?filter[settername]=<?php echo $item->setterId; ?>">
                             <?php echo $item->settername; ?>
                        </a>
                    </td>
                    <?php endif; ?>
                    <?php if(1==$use_setterdate) : ?>
                    <td class="d-none d-md-table-cell"><?php // Setterdate ?>
                        <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?>
                    </td>
                    <?php endif;?>

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

