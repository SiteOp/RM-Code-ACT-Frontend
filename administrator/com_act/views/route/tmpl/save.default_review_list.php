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

$user        = JFactory::getUser();
$userId      = $user->get('id');
$adminUser   = $user->authorise('core.admin', 'com_actj');	


?>

	<h2><?php echo JText::_('COM_ACT_COMMENTS'); ?></h2>
		<table class="table">
			  <thead>
				  <tr>
				  <?php if($adminUser) : ?>
					<th><?php echo JText::_('COM_ACT_ID'); ?></th>
				  <?php endif; ?>
					<th><?php echo JText::_('COM_ACT_TABLE_LBL_ROUTE_COMMENTS_STARS'); ?></th>
					<th><?php echo JText::_('COM_ACT_TABLE_LBL_ROUTE_COMMENTS_COMMENT'); ?></th>
					<th><?php echo JText::_('COM_ACT_TABLE_LBL_ROUTE_COMMENTS_MYGRADE'); ?></th>
					<th><?php echo JText::_('COM_ACT_TABLE_LBL_ROUTE_COMMENTS_USERNAME'); ?></th>
				  </tr>
				</thead>
				<tbody>
                
                       <?php // List of Comments ?>
					<?php foreach ($this->comment as $comment) : ?>
					<tr> 
                    <?php if($comment['state'] == 1 ) :?>
                        <?php if($adminUser) : ?>	
                            <td>
                                <?php echo $comment['c_id']; ?>
                            </td>
                        <?php endif; ?>
						<td>
                            <?php if ($comment['stars'] != 0) : ?>
                                <?php echo $comment['stars']; ?>
                            <?php endif; ?>
                        </td>
						<td><?php echo $comment['comment']; ?></td>
						<td><?php echo $comment['uiaa']; ?></td>
						<td>
                            <?php echo $comment['user_name']; ?>
                            <?php if ($userId === $comment['created_by']) :?>
                                <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $comment['c_id'], false, 2); ?>"
                                   class="btn btn-mini pull-right" type="button"> <i class="icon-edit"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                     <?php endif; ?> 
					</tr>
					<?php endforeach; ?>
		</table>

  


    <?php // If Comment State nicht veröffenlicht (State 1) ?>
    <?php foreach ($this->comment as $comment) : ?>
         <?php if ($userId === $comment['created_by'] && $comment['state'] != 1  ) :?>
            <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $comment['c_id'], false, 2); ?>" 
              class="btn btn-mini pull-right" type="button"> <i class="icon-edit"></i>
            </a>
                    
            <?php echo $comment['user_name'] ; ?> Status = 
                <?php
                  switch ($comment['state'])
                  {

                    case 2:  echo 'Archiviert';
                             break;
                    case 0:  echo 'Versteckt';
                             break;
                    case -2:  echo 'Papierkorb';
                             break;
                  }
                ?>
        <?php endif; ?>
    <?php endforeach; ?> 
