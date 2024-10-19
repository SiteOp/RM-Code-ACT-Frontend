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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$user        = Factory::getUser();
$userId      = $user->get('id');
$adminUser   = $user->authorise('core.admin', 'com_act');	
$userAdminComment   = $user->authorise('core.edit', 'com_act_admin_comment');	


// Erhalte den Itemid des Menüpunktes Admin Commens Liste 
// Nötig um beim Editieren direkt in das richtige Formular zu kommen	
$app = JFactory::getApplication(); 
$menu = $app->getMenu();
$menuItem = $menu->getItems( 'link', 'index.php?option=com_act_admin_comment&view=admincomments', true );

// Komponente Admin Kommentare 
$component_params  = JComponentHelper::getParams('com_act_admin_comment');
$adminAlias        = $component_params['name']; // Was für ein Name soll für den Admin Alias verwendet werden?
// Einbinden der Helper-Datei der Komponente Admin Kommentare
JLoader::register('Act_admin_commentHelpersAct_admin_comment', JPATH_ROOT . '/components/com_act_admin_comment/helpers/act_admin_comment.php'); 

?>

<div class="card">
    <div class="card-header">
    <h3>
		<i class="<?php echo Text::_('COM_ACT_FA_COMMENTS'); ?>"></i>  
		<?php echo JText::_('COM_ACT_COMMENTS'); ?> 
		<small>(<?php echo count($this->comment); ?>)</small>
	</h3> 
    </div>
    <div class="card-body">
     <?php if (!empty($this->comment)) : ?><?php // Kommentar vorhanden? ?>
            <?php foreach ($this->comment as $comment) : ?>
       
                    <?php if ($comment['state'] == 1 || $comment['state'] == 2  ) :?> <?php //Kommentar Status 1 ?>
                    <div class="row" >
					
						<div class="route_comment_rating Stars" style=" --star-size: 140%; --rating: <?php echo ActHelpersAct::getStarsRound($comment['stars']); ?>;"></div>

                        <div class="route_comment_usergrade ">
                            <?php echo (empty($comment['grade'])) ? '' : $comment['grade']; ?><?php // Short if else ?>
						</div>
						<div class="route_comment_ascent">
						<?php // Im Profil ascent_show auf 1 gesetzt? ;?>
							<?php if(ActHelpersAct::getUserProfilAscentShow($comment['created_by']) AND $comment['ticklist_yn'] == 1) : ?> 
								<?php echo ActHelpersAct::getUserAscentIcon($comment['ascent']); ?> <?php // Anzeige der Begehung als Icon ; ?>
							<?php endif; ?>
						</div>
                        <div class="route_comment_username">
                            <div> <?php echo $comment['user_name']; ?></div>
                        </div>
                        <div class="route_comment_comment">
							<div> <?php echo $comment['comment']; ?>
							
						<?php // ADMIN Kommentar Start ?>
						<?php $adminComment   = Act_admin_commentHelpersAct_admin_comment::getAdminComment($comment['c_id']);	// Helper Datei um den Admin Kommentar zu erhalten ?>
								<div class="row mt-4 mb-2 ml-1 ">
								<?php if (!empty($adminComment->admincommentId)) : ?>
									<div class="col-11 admin_answer">
										<?php echo $adminComment->comment; ?><br />
										<small><?php echo $adminAlias; ?></small><?php // aus Config der Komponente Admin Comments ?>
									</div>
								<?php endif; ?>
									
								<?php if($userAdminComment) : ?> <?php // nur für Admin ?>
										<?php if (empty($adminComment->admincommentId)) : ?>
											<div class="col-1 offset-11">
												<a href="<?php echo Route::_('index.php?option=com_act_admin_comment&view=admincommentform&layout=edit&id=0&commentid='.$comment['c_id'].'&route_id='.$this->item->id, false, 0); ?>">
													<i class="fas fa-reply"></i>
												</a>
											</div>
										<?php else : ?>
											<div class="col-1">
												<a href="<?php echo Route::_('index.php?option=com_act_admin_comment&task=admincomment.edit&id='.$adminComment->admincommentId.'&route_id='.$this->item->id.'&Itemid='.$menuItem->id, false, 0); ?>">
													<i class="fas fa-pencil-alt"></i>
												</a>
											</div>
										<?php endif; ?>
								<?php endif; ?>
								</div><?php // ADMIN Kommentar END ?>	
							</div>  
						</div>

                        <div class="route_comment_edit" >
                            <?php if ($userId === $comment['created_by']) :?>
                                <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $comment['c_id'], false, 1); ?>"
                                         class="pull-right" type="button"> 
                                         <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                                 </a>
                            <?php endif; ?>
                        </div>
                    </div>
					
					
					
					<?php // ROW END  ?><hr />
                    <?php endif; ?> <?php // IF Kommentar Status 1 OR 2 END ?>
                    
            <?php endforeach; ?>
			 <div>
			
			 <?php if(!ActHelpersAct::getUserProfilAscentShow($userId)  AND $userId != 0) : ?> <?php // Eingeloggt und Begehungsstil nicht freigeschalten - dann Link ; ?>
				<p class="mt-4">
					<i class="fas fa-eye" style="font-size: 20px; padding-right: 3px"></i>
					<b><a href="index.php?option=com_users&view=profile">
					<?php echo Text::_('COM_ACT_ASCENT_SHOW_IN_COMMENT'); ?>
					<i class="fas fa-chevron-circle-right"></i></b></a>  
				</p>
			<?php endif; ?>

	
	</div>
            
    <?php else: ?> <?php // Kein Kommentar ?>
        <div class="row" >
            <div class="col-12">
               <?php echo JText::_('COM_ACT_ROUTE_NO_COMMENT'); ?>
            </div>
         </div>
            
    <?php endif; ?>
   
    
    </div><?php // div class = card-body END ?> 
</div><?php // CARD END ?>

<?php // If Comment State nicht veröffenlicht (State 1) ?>
    <?php foreach ($this->comment as $comment) : ?>
         <?php if ($userId === $comment['created_by'] && $comment['state'] != 1  ) :?>
        
         <div class="card mt-5">
            <div class="card-header">
                <h3><i class="<?php echo Text::_('COM_ACT_FA_ONLYYOU'); ?>"></i>  <?php echo Text::_('HELLO'); ?> <?php echo $comment['user_name'] ; ?></h3>
            </div>
            <div class="card-body">
                <?php echo Text::_('COM_ACT_COMMENT_HIDDEN'); ?>
                <?php switch ($comment['state'])
                        {
                            case 0:  echo JText::_('COM_ACT_UNPUBLISHED');
                                     break;
                            case 2:  echo JText::_('COM_ACT_ARCHIVED');
                                     break;
                        }
                 ?>
                <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $comment['c_id'], false, 2); ?>" class="pull-right" type="button">
                    <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                </a>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?> 
	


