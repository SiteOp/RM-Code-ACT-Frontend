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
$setter                 = $params['setteryn'];
$sector                 = $params['sectoryn'];
$inorout                = $params['inoroutyn'];
$line                   = $params['lineyn'];
$use_route_lifetime     = $params['use_route_lifetime']; // Removedate Lifetime einer Route

$user        = Factory::getUser();

$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');
$canEdit     = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'routeform.xml');
$canCheckin  = $user->authorise('core.manage', 'com_act');
$canChange   = $user->authorise('core.edit.state', 'com_act');
$adminUser   = $user->authorise('core.manage', 'com_actj');

$unix_date = strtotime(Factory::getDate());

// Lade Globale Sprachdateien
$lang = Factory::getLanguage();
$extension = 'com_act_global';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

// Add styles
$document = Factory::getDocument();
$style .=  1==$use_route_lifetime ? '' : '#filter_lifetime, #filter_lifetime_chosen {display: none;}'; // Filter ausblenden
$document->addStyleDeclaration($style);

?>

<?php // Page-Header ?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <i class="fas fa-shield-alt"></i> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1> 
	</div>
<?php else : ?>
    <div class="page-header">
		<h1 itemprop="headline">
			<i class="fas fa-shield-alt"></i> <?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
    </div>
<?php endif; ?>

<form action="<?php echo Route::_('index.php?option=com_act&view=routesadmin'); ?>" method="get" name="adminForm" id="adminForm">



<?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

<?php if (sizeof($this->items) !== 0 ) : ?> <?php // Prüfe ob es überhaubpt Ergebnisse gibt ?>

    <div class="table-responsive ">
        <table class="table table-striped table-sm" id="routeList">
            <thead>
            <tr>
                <th class="r_state text-center" ><?php // STATE ?>
                    <?php echo  HTMLHelper::_('grid.sort', 'COM_ACT_STATUS', 'a.state', $listDirn, $listOrder); ?> 
                </th>
                <?php if(1==$use_route_lifetime) : ?>
                    <th class="r_edit text-center"> <?php // Removedate ?>
                        <?php echo ActHelpersAct::getPopoverByParams('ACTGLOBAL_ROUTE_LIFETIME', 'ACTGLOBAL_ROUTE_LIFETIME_POPOVER_TXT'); ?><br />
                        <?php echo HTMLHelper::_('grid.sort', 'ACTGLOBAL_LIFETIME', 'removedate', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>
                <th class="r_info text-center d-xl-table-cell"><?php // Info Popover ?>
                    <?php echo Text::_('COM_ACT_LBL_INFO'); ?>
                </th>
                <th class="r_line d-none d-xxl-table-cell">
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_LINE', 'l.line', $listDirn, $listOrder); ?> 
                </th>
                <th class="r_color text-center"><?php // Color ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_COLOR', 'c.color', $listDirn, $listOrder); ?>
                </th>
                <th class="r_name"><?php // Name Route ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_NAME', 'a.name', $listDirn, $listOrder); ?>
                </th>
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
                <th class="r_admininfo d-none d-xl-table-cell" ><?php // Route Info ? Admin ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_INFO', 'a.info', $listDirn, $listOrder); ?>
                </th>
                <th class="r_avg"><?php // AVG ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_STARS', 'COM_ACT_ROUTE_POPOVER_TXT_STARS'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_REVIEW', 'AvgStars', $listDirn, $listOrder); ?>
                </th>
                <?php if ($inorout == 1 ) : ?>
                <th class="r_inorout d-none "><?php // In-/Outdoor ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_INOROUT', 'inorout', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>
                <?php if ($sector == 1 ) : ?>
                <th class="r_sector d-none d-xl-table-cell pr-1"><?php // Sektoren ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SECTOR', 'lineSectorName', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>
                <?php if ($setter == 1 ) : ?>
                <th class="r_setter d-none d-lg-table-cell"><?php // Setter ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTERNAME', 's.settername', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>
                <th class="r_setterdate d-none d-md-table-cell"><?php // Setterdate ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_SETTERDATE', 'a.setterdate', $listDirn, $listOrder); ?>
                </th>
                <th class="r_edit text-center">
                    <?php echo Text::_('COM_ACT_ACTIONS'); ?>
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

                     <?php if (isset($this->items[0]->state)) : ?>
                        <?php $class = ($canChange) ? 'active' : 'disabled'; ?>    
                         
                    <td class="text-center"><?php // State ############################ TODO Statt if elseif - Switch einsetzten ?>
                    <?php if ($item->state == -4): ?><?php // Planung  ?>
                            <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=route.publish&id=' . $item->id . '&state= -3') : '#'; ?>">
                                <i class="fas fa-solar-panel"></i>
                            </a>
                       <?php elseif ($item->state == -3): ?><?php // Zur Freigabe ist Geschraubt  ?>
                            <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=route.publish&id=' . $item->id . '&state= 1') : '#'; ?>">
                             <i class="fas fa-tools"></i> 
                            </a>
                        <?php elseif ($item->state == 1): ?><?php // Öffentlich ?>
                            <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=route.publish&id=' . $item->id . '&state= -1') : '#'; ?>">
                                <i class="<?php echo Text::_('COM_ACT_FA_PUBLISHED'); ?>"></i>
                            </a>
                        <?php elseif ($item->state == -1): ?><?php // Kommt raus ?>
                            <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=route.publish&id=' . $item->id . '&state= 2') : '#'; ?>">
                                <i class="fas fa-highlighter"></i>
                            </a>
                           
                        <?php elseif ($item->state == 2): ?>
                            <i class="<?php echo Text::_('COM_ACT_FA_ARCHIV'); ?>"></i>
                        <?php else : ?>
                            <?php echo '<i class=" '. Text::_('COM_ACT_FA_TRASHED') . '"></i>'; ?>
                        <?php endif; ?>
                    </td>
                     <?php endif; ?>

                     <?php if(1==$use_route_lifetime) : ?>
                        <td class="text-center">
                            <?php $removedate = HTMLHelper::_('date', $item->removedate, Text::_('DATE_FORMAT_LC4')); ?>
                            <?php echo ActHelpersAct::getRemoveRouteIcon($item->lifetime, $removedate); ?>
                        </td>
                    <?php endif; ?> 

                    <td class="r_info text-center"><?php // Info Popover ?>
                         <a class="" rel="popover" 
                                data-placement="right" 
                                data-html="true" 
                                data-trigger="hover" 
                                title=" 
                                    <?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_' . $item->inorout); ?> |
                                    <?php echo ActHelpersAct::getBuildingName($item->building); ?>
                                      "
                                data-content=" 
                                    <div class='pop_line'>Linie: <?php echo $item->line; ?></div>
                                    <div class=''><?php echo $item->lineSectorName; ?> </div>
                                    <div class='pop_setter d-lg-none'><?php echo $item->settername; ?></div>
                                    <div class='pop_setterdate d-lg-none'> <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?></div>
                                    <div><?php echo $item->settername; ?></div>
                                    
                                    ">
                            <?php if (1 == $item->inorout ) : ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_INDOOR'); ?>"></i>
                            <?php else : ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_OUTDOOR'); ?>"></i>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td class="d-none d-xxl-table-cell">
                         <a href="<?php echo Route::_('index.php?option=com_act&view=routesadmin'); ?>?filter[line]=<?php echo $item->lineId; ?>">
                            <?php echo $item->line; ?>
                        </a>
                    </td>
                    <td class="text-center"><?php // Color ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routesadmin'); ?>?filter[color]=<?php echo $item->colorId; ?>">
                        <?php echo ColorsHelpersColors::getColor($item->rgbcode, $item->rgbcode2, $item->rgbcode3); ?>
                        </a>
                    </td>
                    <td class=""> 
						<?php // soll die Route in der Datenbank erscheinen? wenn nicht - hidden ?>
						<?php if ($item->hidden != 0) : ?>
							<i class="fas fa-eye-slash mr-1"></i> 
						<?php endif; ?>
						<?php // Setterdate older then 14 Day ?>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
                          <?php if (strtotime($item->setterdate) > ($unix_date - 1209600) ): ?><span class="new_route"><?php echo Text::_('COM_ACT_ROUTE_NEW_ROUTE'); ?></span> <?php endif; ?>
                    </td>
                    <?php if ($calcgrade == 1 ) : ?><?php // C-Grade ?>
                    <td  class="text-center">
                    <?php echo (0 == (int)$item->c_grade) ? '-' : $item->c_grade; ?>
                    </td>
                    <?php endif; ?>
                    <td class="text-center"><?php // VR_Grade Wenn Null dann - Wichtig f�r Speedroute ?>
                    <?php echo (1 == $item->exclude OR 0 == (int)$item->s_grade) ? '-' : $item->s_grade; ?><?php // Short if else ?>
                    </td> 
                    <td class="d-none d-xl-table-cell"><?php // Info Route Admin? ?>
                        <?php if (!empty($item->info)) : ?>
                            <a rel="popover" 
                                data-placement="bottom" 
                                data-trigger="hover" 
                                data-content="<?php echo $item->info; ?>" 
                                >
                                    <?php echo HTMLHelper::_('string.truncate', $item->info, 8, false, false ); ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td class="d-none d-sm-table-cell" >  <?php // AVG  ?>
                        <?php if (0 == $item->exclude) : ?>
                            <div class="Stars" style=" --star-size: 140%; --rating: <?php echo ActHelpersAct::getStarsRound($item->AvgStars); ?>;"></div>
                            <?php echo (empty($item->count_stars)) ? '(0)' : '(' .$item->count_stars. ')'; ?><?php // Short if else ?>
                        <?php endif; ?>
                    </td>
                    <td class="d-sm-none text-center" >  <?php // AVG nur Ausgabe Text f�r Smartphone  ?>
                        <?php echo round($item->AvgStars,0); ?> /
                        <?php echo (empty($item->count_stars)) ? '0' :  $item->count_stars; ?><?php // Short if else ?>
                    </td>
                    <?php if ($inorout == 1 ) : ?>   <?php // In-/Outdoor ?>
                    <td class="d-none" >
                        <?php echo Text::_('COM_ACT_LINES_INOROUT_OPTION_' . $item->inorout); ?>
                    </td>
                    <?php endif; ?>
                     <?php if ($sector == 1 ) : ?><?php // Sektoren ?>
                    <td class="d-none d-xl-table-cell">
                        <?php echo $item->lineSectorName; ?>
                    </td>
                    <?php endif; ?>
                     <?php if ($setter == 1 ) : ?><?php // Setter ?>
                    <td class="d-none d-lg-table-cell">
                        <a href="<?php echo Route::_('index.php?option=com_act&view=routesadmin'); ?>?filter[settername]=<?php echo $item->setterId; ?>">
                             <?php echo $item->settername; ?>
                        </a>
                    </td>
                    <?php endif; ?>
                    <td class="d-none d-md-table-cell"><?php // Setterdate ?>
                        <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?>
                    </td>
                    <td class="text-center" ><?php // Edit ?>
                        <?php if ($canEdit): ?>
                            <a href="<?php echo Route::_('index.php?option=com_act&task=routeform.edit&id=' . $item->id, false, 2); ?>" class="" type="button">
                                <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                            </a>
                        <?php endif; ?>
                    </td>
 
                </tr>
            <?php endforeach; ?>
<?php else: ?>
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
