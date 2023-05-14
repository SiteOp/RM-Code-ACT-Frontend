<?php
/**
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder sp�ter
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


$user       = Factory::getUser();
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'commentform.xml');
$canEdit    = $user->authorise('core.edit', 'com_act') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'commentform.xml');
$canCheckin = $user->authorise('core.manage', 'com_act');
$canChange  = $user->authorise('core.edit.state', 'com_act');
$canDelete  = $user->authorise('core.delete', 'com_act');
$adminCommentEdit = $user->authorise('admin.comment.edit', 'com_act');

// Helper Colors
JLoader::import('helpers.colors', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
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
	
	<?php if($adminCommentEdit) : ?>
    <?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
	<?php endif; ?>

    <?php // Statistik Kommentare Charts.js - Script = Template default_charts ?>
    <div class="row mb-5 d-none d-md-block">
        <div class=" col-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="chart" style="position: relative; height:27vh; width:100vw"></canvas>
                </div>
           </div>
        </div>
    </div>

    <div class="table-responsive ">
    <table class="table table-striped" id="commentList">
        <thead>
            <tr>
				<th class="ac_avg"><?php // AVG ?>
                <?php echo ($adminCommentEdit)
					? HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_COMMENTS_USER_STARS', 'a.stars', $listDirn, $listOrder)
					: Text::_('COM_ACT_TABLE_HEADER_COMMENTS_USER_STARS');
				?>
                </th>
                <th class="ac_comment"><?php // Comment ?>
				<?php echo ($adminCommentEdit) 
					? HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_COMMENTS_COMMENT', 'a.comment', $listDirn, $listOrder)
					: Text::_('COM_ACT_TABLE_HEADER_COMMENTS_COMMENT');
				?>
                </th>
                <th class="ac_route"><?php // Route ?>
                <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_ROUTE_POPOVER_HEAD_ADMIN_ROUTE', 'COM_ACT_ROUTE_POPOVER_TXT_ADMIN_ROUTE'); ?><br />
				<?php echo ($adminCommentEdit) 
					? HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_COMMENTS_ROUTENAME', 'route_name', $listDirn, $listOrder)
					: Text::_('COM_ACT_TABLE_HEADER_COMMENTS_ROUTENAME');
				?>
                </th>
                <th class="ac_vrgrade"><?php // VR Grade ?>
                <?php echo ($adminCommentEdit) 
					? HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_COMMENTS_SETTER_GRADE', 'orderSGrade', $listDirn, $listOrder)
					:  Text::_('COM_ACT_TABLE_HEADER_COMMENTS_SETTER_GRADE');
				?>
                </th>
                <th class="ac_mygrade"><?php // MY Grade ?>
				<?php echo ($adminCommentEdit)
					? HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_COMMENTS_USER_GRADE', 'orderMyGrade', $listDirn, $listOrder) 
					: Text::_('COM_ACT_TABLE_HEADER_COMMENTS_USER_GRADE');
				?>
                    
                </th>
				<th>
				<?php echo Text::_('Stil'); ?>
				</th>
                <th class="ac_createdname"><?php // Created Name ?>
				<?php echo ($adminCommentEdit)
					? HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_COMMENTS_USER_NAME', 'a.created_by', $listDirn, $listOrder)
					: Text::_('COM_ACT_TABLE_HEADER_COMMENTS_USER_NAME');
				?>
                </th>
                <th class="ac_createddate"><?php // Created Date ?>
				<?php echo ($adminCommentEdit)
					? HTMLHelper::_('grid.sort',  'COM_ACT_TABLE_HEADER_COMMENTS_COMMENT_CREATED', 'a.created', $listDirn, $listOrder)
					: Text::_('COM_ACT_TABLE_HEADER_COMMENTS_COMMENT_CREATED'); 
				?>
                </th>
				<?php if ($adminCommentEdit) : ?>
                <th class='ac_input'><?php // Input API oder Website ?>
                    <?php echo HTMLHelper::_('grid.sort', 'COM_ACT_TABLE_HEADER_COMMENTS_INPUT', 'a.input', $listDirn, $listOrder); ?>
                </th>
				<?php endif; ?>
                <?php if ($adminCommentEdit) : ?>
                <th class='ac_edit text-center'><?php // Edit ?>
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
                <?php if (!$canEdit && $user->authorise('core.edit.own', 'com_act')): ?>
                    <?php $canEdit = Factory::getUser()->id == $item->created_by; ?>
                    
                <?php endif; ?>
                
            <tr class="row<?php echo $i % 2; ?>">
            
				 <td><?php // AVG  ?>
                    <div class="Stars" style=" --star-size: 150%; --rating: <?php echo ActHelpersAct::getStarsRound($item->stars); ?>;"></div>
                </td>
                <td><?php // Comment ?>
                    <a rel="popover" 
                        data-placement="right" 
                        data-trigger="hover" 
                        data-content="<?php echo $this->escape($item->comment); ?>" 
                        data-original-title="User: <?php echo Factory::getUser($item->created_by)->username; ?>" 
                        >
                        <p><?php echo HTMLHelper::_('string.truncate', $item->comment, 60, false, false ); ?></p>
                    </a>
                </td>
            
                <td><?php // Route ?>
                    <a rel="popover" 
                        data-placement="right" 
                        data-trigger="hover" 
                        data-html="true" 
                        data-original-title="<?php echo ColorsHelpersColors::getColor($item->rgbcode, $item->rgbcode2, $item->rgbcode3); ?>
                                             <?php echo $this->escape($item->route_name); ?>
                                            " 
                        data-content="<div class='pop_line'>Linie: <?php echo $item->line; ?></div>
                                      <div><?php echo $item->lineSectorName; ?></div>
                                      <div class='pop_setter'><?php echo $item->settername; ?></div>
                                      <div class='pop_cgrade'>C-Grade:
                                          <?php if ($item->c_grade == 0) :?><?php // Wenn Communitiy Grade vorhanden (berechnet) dann ausgeben. ## View Table ?>
                                            <?php echo '-'; ?>
                                          <?php else: ?>
                                            <?php echo $item->c_grade; ?>
                                          <?php endif; ?>
                                       </div>
                                     " 
                        ><?php // Wenn die Route Status 1 dann route_active sonst route_archiv ?>
                        <i class="<?php echo Text::_('COM_ACT_FA_INFO'); ?> pr-2 <?php echo ($item->route_state == 1 OR  $item->route_state == -1) ? 'route_active"' : 'route_archiv"'; ?>"></i>
                    </a>
					<?php if (($item->route_state == 1) OR ($item->route_state == -1) OR ($adminCommentEdit)) : ?>
						<a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $item->route_id); ?>"><?php echo $this->escape($item->route_name); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->route_name); ?>
					<?php endif; ?>
                </td>
                <td><?php // VR Grade ?>
                    <?php echo (0 == (int)$item->s_grade) ? '-' : $item->s_grade; ?>
                </td>
                <td><?php // MY Grade ?>
                    <?php echo (0 == (int)$item->my_grade) ? '-' : $item->my_grade; ?>
                </td>
               
				<td>
					<?php if(ActHelpersAct::getUserProfilAscentShow($item->created_by) AND $item->ticklist_yn == 1) : ?> <?php // Im Profil ascent_show auf 1 gesetzt? ;?>
						<?php echo ActHelpersAct::getUserAscentIcon($item->ascent); ?> <?php // Anzeige der Begehung als Icon ; ?>
					<?php endif; ?>
				</td>
                <td><?php // Created Name ?>
				<?php if ($adminCommentEdit) : ?>
                    <a href="<?php echo Route::_('index.php?option=com_act&view=comments'); ?>?filter[user]=<?php print_R(Factory::getUser($item->created_by)->username); ?>">
                        <?php echo Factory::getUser($item->created_by)->username; ?>
                    </a>
				<?php else : ?>
					<?php echo Factory::getUser($item->created_by)->username; ?>
				<?php endif; ?>
                </td>

                <td><?php // Created Comment Date ?>
				<?php if ($adminCommentEdit) : ?>
                    <?php echo HTMLHelper::_('date', $item->created, JText::_('DATE_FORMAT_LC5')); ?>
				<?php else : ?>
					<?php echo HTMLHelper::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
				<?php endif; ?>
                </td>
                
				<?php if ($adminCommentEdit) : ?>
                 <td class="text-center"><?php // Input - Welches Ger�t Website, API ... ?>
                    <?php switch ($item->input) {
                        case 1:
                             echo '<a class="" rel="popover" 
									  data-container="body"
								      data-placement="top" 
								      data-trigger="hover" 
								      data-content="Website">
										<i class="fas fa-laptop" style="color: #0393ad"></i>
									</a>';
                             break;
                        case 2:
                             echo '<a class="" rel="popover" 
									  data-container="body"
								      data-placement="top" 
								      data-trigger="hover" 
								      data-content="Android">
										<i class="fab fa-android fa-lg" style="color: #78C257"></i>
									</a>';
                             break;
						case 3:
                             echo '<a class="" rel="popover" 
									  data-container="body"
								      data-placement="top" 
								      data-trigger="hover" 
								      data-content="iOS">
										<i class="fab fa-apple fa-lg" style="color: #777"></i>
									</a>';
                             break;
						default:
							 echo '<a class="" rel="popover" 
									  data-container="body"
								      data-placement="top" 
								      data-trigger="hover" 
								      data-content="Unknown">
										<i class="fas fa-question" style="color: #fab903"></i>
									</a>';
                             break;
                    }?>
                </td>
				<?php endif; ?>
                <?php if ($adminCommentEdit) : ?>
                <td class="text-center"> <?php // Edit ?>
                    <a href="<?php echo Route::_('index.php?option=com_act&task=commentform.edit&id=' . $item->id, false, 2); ?>" class="" type="button">
                        <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                    </a>
                </td>
                <?php endif; ?>
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

<?php // Load Template Charts.js ?>
<?php echo $this->loadTemplate('charts'); ?>
