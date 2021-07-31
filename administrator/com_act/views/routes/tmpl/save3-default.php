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

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// ACT Params 
$params    = JComponentHelper::getParams('com_act');
$calcgrade = $params['calculatedyn']; // Ausgabe des Kalkulierten Grades ?   | if ($calcgrade == 1 )
$setter    = $params['setteryn'];
$sector    = $params['sectoryn'];
$inorout   = $params['inoroutyn'];
$hits      = $params['hitsyn'];
$route_ID  = $params['routeidyn'];
$line      = $params['lineyn'];

$user        = Factory::getUser();

$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');
$canEdit     = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'routeform.xml');
$canCheckin  = $user->authorise('core.manage', 'com_act');
$canChange   = $user->authorise('core.edit.state', 'com_act');
$adminUser   = $user->authorise('core.manage', 'com_actj');

$unix_date = strtotime(Factory::getDate());


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

<?php // Filter Published - State only Admins ?>
<?php if($adminUser) : ?>
    <fieldset id="filter-bar">  
            <select id="filter_state" name="filter[state]" class="admin"  onchange="this.form.submit()">
                <option value=""><?php echo Text::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo  HTMLHelper::_('select.options',  HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->getState('filter.state'), true);?>
            </select>
    </fieldset>
<?php endif; ?>

<?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

<div class="table-responsive ">
	<table class="table table-striped table-sm" id="routeList">
		<thead>
		<tr>
            <?php if ($adminUser): ?>
            <th class="r_state text-center d-none d-xl-table-cell "><?php // STATE ?>
                <?php echo  HTMLHelper::_('grid.sort', 'COM_ACT_STATUS', 'a.state', $listDirn, $listOrder); ?> 
            </th>
            <?php endif; ?>
            <th class="r_info text-center"><?php // Info Popover ?>
                <?php echo Text::_('Info'); ?>
            </th>
            <?php if ($line == 1 ) : ?><?php // Line ?>
			<th class="r_line d-none d-xxl-table-cell">
                <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_LINE', 'a.line', $listDirn, $listOrder); ?> 
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
			<?php if($adminUser) : ?>
			<th class="r_admininfo d-none d-xl-table-cell" ><?php // Route Info ? Admin ?>
                <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTES_INFO', 'a.info', $listDirn, $listOrder); ?>
            </th>
			<?php endif; ?>
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
            
			<?php if($adminUser) : ?><?php // IF Admin Start ?>
			<th class="r_modified d-none d-md-table-cell"><?php // Modified ?>
                <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_ROUTE_MODIFIED', 'routeCommentUpdate', $listDirn, $listOrder); ?>
            </th>
             <?php if($route_ID) : ?>
            <th class="r_id d-none d-xxl-table-cell"><?php // ID ?>
                <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
            <?php endif; ?>
             <?php if($hits) : ?>
			<th class="r_hits text-center d-none d-xxl-table-cell "><?php // Hits ?>
                <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_HITS', 'a.hits', $listDirn, $listOrder); ?>
            </th>
            <?php endif; ?><?php // EDIT ?>
            <th class="r_edit text-center">
                <?php echo Text::_('COM_ACT_ACTIONS'); ?>
            </th>
			<?php endif; ?><?php // IF Admin END ?>
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
            

                <?php if ($adminUser) : ?><?php // IF Admin Start ?>
                 <?php if (isset($this->items[0]->state)) : ?>
                    <?php $class = ($canChange) ? 'active' : 'disabled'; ?>
                    
                <td class="text-center d-none d-xl-table-cell"><?php // State ?>
                    <a class=" <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_act&task=route.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
                        <?php if ($item->state == 1): ?>
                            <i class="<?php echo Text::_('COM_ACT_FA_PUBLISHED'); ?>"></i>
                        <?php else: ?>
                            <i class="<?php echo Text::_('COM_ACT_FA_UNPUBLISHED'); ?>"></i>
                        <?php endif; ?>
                    </a>
                </td>
                 <?php endif; ?>
                <?php endif; ?><?php // IF Admin END ?>

                <td class="r_info text-center"><?php // Info Popover ?>
                     <a class="" rel="popover" 
							data-placement="right" 
                            data-html="true" 
							data-trigger="hover" 
                            title=" 
                                <?php echo Text::_('COM_ACT_LINES_INOROUT_OPTION_' . $item->inorout); ?> |
                                <?php echo $item->building = Text::_('COM_ACT_LINES_BUILDING_OPTION_' . $item->building); ?>
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
                    <?php echo $item->line; ?>
                </td>
                <?php endif; ?>
				<td class="text-center d-none d-md-block"><?php // Color ?>
                    <span class="routecolor" style="background: <?php echo $item->rgbcode; ?>;"></span>
                </td>
				<td class=""> <?php // Setterdate older then 14 Day ?>
					<a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $item->id); ?>"><?php echo $this->escape($item->name); ?></a>
                      <?php if (strtotime($item->setterdate) > ($unix_date - 1209600) ): ?><span class="new_route"><?php echo Text::_('COM_ACT_ROUTE_NEW_ROUTE'); ?></span> <?php endif; ?>
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
				<td class="text-center"><?php // VR_Grade ?>
                    <?php echo ActHelpersAct::uiaa($item->settergrade); ?>
                </td> 
                
                <?php if ($adminUser) : ?> <?php // IF Admin Start ?>
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
                <?php endif; ?><?php // IF Admin End ?>
                
				<td class="d-none d-sm-table-cell" >  <?php // AVG  ?>
                    <?php echo ActHelpersAct::getStarsByParams(round($item->AvgStars,1)); ?>
                    <?php echo (empty($item->count_stars)) ? '(0)' : '(' .$item->count_stars. ')'; ?><?php // Short if else ?>
                </td>
                <td class="d-sm-none text-center" >  <?php // AVG nur Ausgabe Text für Smartphone  ?>
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
                    <?php echo $item->settername; ?>
                </td>
                <?php endif; ?>
				<td class="d-none d-md-table-cell"><?php // Setterdate ?>
                    <?php echo HTMLHelper::_('date', $item->setterdate, Text::_('DATE_FORMAT_LC4')); ?>
                </td>
                
                <?php if($adminUser) : ?><?php // IF Admin Start ?>
                 <?php // Last Updatetime from Route or Comment NOTE FIELD IN TABLE MUST BE TIMESTAMP ?>
				<td class="d-none d-md-table-cell">
                    <?php echo	HTMLHelper::_('date', $item->routeCommentUpdate, Text::_('DATE_FORMAT_LC5')); ?>
                </td>
                <?php if($route_ID) : ?><?php // ID ?>
                <td class="d-none d-xxl-table-cell">
                    <?php echo $item->id; ?>
                </td>
                <?php endif; ?>
                <?php if($hits) : ?><?php // Hits ?>
                <td class="text-center d-none d-xxl-table-cell">
                    <?php echo $item->hits; ?>
                </td>
                <?php endif; ?>
                <td class="text-center" ><?php // Edit ?>
					<?php if ($canEdit): ?>
						<a href="<?php echo Route::_('index.php?option=com_act&task=routeform.edit&id=' . $item->id, false, 2); ?>" class="" type="button">
                            <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                        </a>
					<?php endif; ?>
				</td>
                <?php endif; ?><?php // IF Admin End ?>
                
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>


<?php // Script Bootstrap Tooltip ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('[rel=popover]').popover();
    });
</script>