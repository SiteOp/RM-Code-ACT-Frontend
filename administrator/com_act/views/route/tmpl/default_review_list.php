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

$user        = JFactory::getUser();
$userId      = $user->get('id');
$adminUser   = $user->authorise('core.admin', 'com_actj');	

?>

<div class="card">
    <div class="card-header">
    <h3><i class="<?php echo Text::_('COM_ACT_FA_COMMENTS'); ?>"></i>  <?php echo JText::_('COM_ACT_COMMENTS'); ?></h3>
    </div>
    <div class="card-body">

     <?php if (!empty($this->comment)) : ?><?php // Kommentar vorhanden? ?>
            <?php foreach ($this->comment as $comment) : ?>
       
                    <?php if ($comment['state'] == 1  ) :?> <?php //Kommentar Status 1 ?>
                    <div class="row" >
                        <div class="route_comment_rating ">
                                <?php echo ActHelpersAct::getStarsByParams($comment['stars']); ?>
                        </div>
                        <div class="route_comment_usergrade ">
                            <?php echo (empty($comment['myroutegrade'])) ? '' : ActHelpersAct::uiaa($comment['myroutegrade']); ?><?php // Short if else ?>
                        </div>
                        <div class="route_comment_username">
                            <div> <?php echo $comment['user_name']; ?></div>
                        </div>
                        <div class="route_comment_comment">
                            <div> <?php echo $comment['comment']; ?></div>  
                        </div>
                        <div class="route_comment_edit" >
                            <?php if ($userId === $comment['created_by']) :?>
                                <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $comment['c_id'], false, 1); ?>"
                                         class="pull-right" type="button"> 
                                         <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                                 </a>
                            <?php endif; ?>
                        </div>
                    </div><?php // ROW END  ?><hr />
                    <?php endif; ?> <?php // IF Kommentar Status 1 END ?>
                    
            <?php endforeach; ?>
            
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

