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
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');


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


<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="get" name="adminForm" id="adminForm">

	<?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    
    <?php if (sizeof($this->items) !== 0 ) : ?> <?php // Prüfe ob es überhaubpt Ergebnisse gibt ?>

    <div class="table-responsive">
    <table class="table table-striped  table-sm" id="commentList">
		<thead>
			<tr>
				<th class="myc_route pl-2"><?php // Name Route ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MYCOMMENTS_ROUTENAME', 'a.route', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_comment"><?php // Kommentar ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MYCOMMENTS_COMMENT', 'a.comment', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_vrgrade text-center"><?php // C-Grade ?>
                   <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_C_GRADE', 'COM_ACT_ROUTE_POPOVER_TXT_C_GRADE'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_ROUTES_C_GRADE', 't.calc_grade', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_mygrade text-center"><?php // My-Grad ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_COMMENT_POPOVER_HEAD_MY_GRADE', 'COM_ACT_COMMENT_POPOVER_TXT_MY_GRADE'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MYCOMMENTS_MYGRADE', 'a.myroutegrade', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_avg"><?php // AVG ?>
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_STARS', 'COM_ACT_ROUTE_POPOVER_TXT_STARS'); ?><br />
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MYCOMMENTS_STARS', 'a.stars', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_created"><?php // Created-Date ?>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MYCOMMENTS_COMMENT_CREATED', 'a.created', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_ticklist d-none d-md-table-cell"><?php // Ticklist Y/N ?>
                    <div class="pl-3"><?php echo ActHelpersAct::getPopoverByParams('COM_ACT_TICKL_POPOVER_HEAD_TICKLIST', 'COM_ACT_TICKL_POPOVER_TXT_TICKLIST'); ?></div>
                    <?php echo HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_MYCOMMENTS_TICKLIST_YN', 'a.ticklist_yn', $listDirn, $listOrder); ?>
                </th>
				<th class="myc_edit text-center"><?php // Edit ?>
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
				<?php $canEdit = $user->authorise('core.edit', 'com_act'); ?>
					<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_act')): ?>
						<?php $canEdit = Factory::getUser()->id == $item->created_by; ?>
					<?php endif; ?>
						<td class="pl-2"><?php // Route Name - If Route State 1 - Then link else echo Name  ?>
							<?php if (!in_array($this->route->state, $routeStateOk)) : ?>	
								<a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $item->route_id); ?>"><?php echo $this->escape($item->route_name); ?></a>
							<?php else :?>
								<?php echo $this->escape($item->route_name); ?>
							<?php endif; ?>
						</td>
						<td><?php // Kommentar mit Popover ?>
                        <?php if (!empty($item->comment)) : ?>
                            <a rel="popover" 
                                data-placement="bottom" 
                                data-trigger="hover" 
                                data-content="<?php echo $this->escape($item->comment); ?>" 
                                >
								<?php echo HTMLHelper::_('string.truncate', $this->escape($item->comment), 30, false, false ); ?>
                            </a>
                        <?php endif; ?>
                        </td>
						<td class="text-center"><?php // VR-Grade  ?>
                            <?php echo ActHelpersAct::uiaa(round($item->cgrade_uiaa,0)); ?>
                        </td>
						<td class="text-center"><?php // My-Grade ?>
                             <?php echo ActHelpersAct::uiaa($item->my_uiaa); ?>
                        </td>
						<td class="d-none d-sm-table-cell" >  <?php // AVG  ?>
                           <div class="Stars" style=" --star-size: 150%; --rating: <?php echo ActHelpersAct::getStarsRound($item->stars); ?>;"></div>
                        </td>
                        <td class="d-sm-none" >  <?php // AVG nur Ausgabe Text für Smartphone  ?>
                            <?php echo $item->stars; ?>;
                        </td>
						<td>
                            <?php if ($item->created != 0): ?>
                                <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                            <?php endif; ?>
                          
                        </td>
						<td class="text-center d-none d-md-table-cell"><?php // Ticklist Y/N ?>
                            <?php if ($item->ticklist_yn == 1): ?>
								<i class="<?php echo Text::_('COM_ACT_FA_PUBLISHED'); ?>"></i>
                            <?php else: ?>
                                <i class="<?php echo Text::_('COM_ACT_FA_UNPUBLISHED'); ?>"></i>
							<?php endif; ?>
                        </td>
                        
						<td class="text-center"><?php // Edit ?>
							<?php if ((!$canEdit) OR (!in_array($this->route->state, $routeStateOk))) : ?> 
								<a href="<?php echo Route::_('index.php?option=com_act&task=commentform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button">
									<i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
								
							<?php endif; ?>
						</td>
					</tr></a>
			<?php endforeach; ?>
     <?php else: ?> <?php // Wenn Keine Ergebnisse bei den Suchfiltern ?>
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

