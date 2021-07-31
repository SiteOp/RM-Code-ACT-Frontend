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

$doc = Factory::getDocument();
$doc->addScript(Uri::base() . '/media/com_act/js/form.js');



$user        = Factory::getUser();
$userId      = $user->get('id');
$adminUser   = $user->authorise('core.admin', 'com_actj');	
$canEdit     = Factory::getUser()->authorise('core.edit', 'com_act');

// Params from Component
// TODO NEW Params Sponsoring with method params->get...
$params     = Factory::getApplication()->getParams('com_act');
$sponsoring = $params->get('sponsoring');


$comment = '';
$stars = array();
$myroutegrade = array();
$Avg_Stars = '';
$Calc_Grade = array();

// Get the Calulated Grad and AVG Stars
if (!empty($this->comment))
{
    foreach ($this->comment as $comment)
    {
        if ($comment['stars'] != 0)
        {
            $stars[] = $comment['stars'];
        }
        
        if ($comment['myroutegrade'] >=1 AND $comment['state'] ==1  )
        {
            $myroutegrade[] = $comment['myroutegrade'];
        }
    }

     if ($comment['stars'] != 0 AND $comment['state'] ==1 )
     {
         $Avg_Stars = round(array_sum($stars) / count($stars),0);
     }
}

$Calc_Grade = round(($this->item->settergrade * 2 + (array_sum($myroutegrade))) / (count($myroutegrade) + 2),0);

?>
 <h1>
    <?php echo $this->item->name; ?> 
    <?php if ($Avg_Stars > 0) : ?>
        Stars <?php echo $Avg_Stars; ?>
    <?php endif; ?>
</h1>



<div class="row">
        <div class="col-xs-12 col-md-5"> 
            <div class="">

                <table class="table">

                <?php // Is there a calculated value of grade? ?>
                <?php if (!empty($myroutegrade)) : ?>
                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_C_GRADE'); ?></th>
                        <td><?php echo ActHelpersAct::uiaa($Calc_Grade); ?></td>
                    </tr>
                <?php endif; ?>

                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SETTER_GRADE'); ?></th>
                        <td><?php echo $this->item->uiaa; ?> || <?php echo $this->item->franzoesisch; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_COLOR'); ?></th>
                        <td><?php echo $this->item->color . $this->item->rgbcode; ?></td> 
                    </tr>

                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_LINE'); ?></th>
                        <td><?php echo $this->item->line; ?> / <?php echo $this->item->inorout; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SECTOR'); ?></th>
                        <td><?php echo $this->item->lineSectorName; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_SETTER'); ?></th>
                        <td><?php echo $this->item->settername; ?></td>
                    </tr>
                    
                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SETTERDATE'); ?></th>
                        <td><?php echo HTMLHelper::_('date', $this->item->setterdate, 'd.m.Y'); ?></td>
                    </tr>

                <?php if (!empty($this->item->info)) :?>
                    <tr>
                        <th><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_INFO'); ?></th>
                        <td><?php echo nl2br($this->item->info); ?></td>
                    </tr>
            	<?php endif; ?>

                <?php if($adminUser) : ?>
                	<tr>
                        <th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_INFOADMIN'); ?></th>
                        <td><?php echo nl2br($this->item->infoadmin); ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Text::_('COM_ACT_STATUS'); ?></th>
                        <td><i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
                    </tr>

                    <tr>
                        <th><?php echo JText::_('COM_ACT_ID'); ?></th>
                        <td><?php echo $this->item->id; ?></td>
                    </tr>

                	<tr>
                        <th><?php echo JText::_('COM_ACT_TABLE_LBL_ROUTE_MODIFIED'); ?></th>
                        <?php // TODO modified ?>
                        <td><?php echo HTMLHelper::_('date', $this->item->modified, 'd.m.Y - H.i');  ?> Uhr</span> 
                    </tr>
                <?php endif; ?>

                <?php if (!empty($this->item->sp_name) AND ($sponsoring > 0)) : ?>
                    <tr>
                        <th><?php echo JText::_('Sponsor'); ?></th>
                        <td>
                            <?php echo($this->item->sp_txt); ?><br /> 
                            <img src="<?php echo($this->item->sp_media); ?>" alt="" height="150" width="150" >
                        </td>
                    </tr>
                <?php endif; ?>

                </table>
            </div>

            <?php // Button to Edit Route ?>
            <?php if($canEdit): ?>
                <a class="btn btn-success" href="<?php echo Route::_('index.php?option=com_act&task=route.edit&id='.$this->item->id); ?>">
                    <?php echo Text::_("COM_ACT_ROUTES_EDIT_ITEM"); ?>
                </a>
            <?php endif; ?>
        </div>
	
        <?php // If any Comments then load the Template  ?>
        <div class="col-12 col-md-7">
            <?php if (!empty($this->comment)) : ?>
                <?php echo $this->loadTemplate('review_list'); ?>
             <?php endif; ?>
        </div>
    
</div><?php // END div row ?>	

<hr />

<div id="ReviewForm" class="row"> <?php // +++ ROW ?>
    <div class="col"><?php // +++ COL ?>

        <?php // Load Form if User Registered  ?>    
        <?php if ($user->get('guest')) : ?>
            <p><?php echo JText::_('COM_ACT_ROUTE_NO_COMMENT'); ?></p>
        <?php else: ?>

        <?php // If comments then check user list ?>
            <?php
            switch (!empty($this->comment))
            {
                case 0:
                    echo $this->loadTemplate('review_form');
                    break;
                case 1: 
                    // Get an Array of User ID, which has a comment
                    foreach ($this->comment as $comment)
                    {
                        $userList[] = $comment['created_by']; 
                    }

                    // Check the array if the logged-in user is included
                    if (!in_array($userId, $userList )) 
                    {
                        echo $this->loadTemplate('review_form');
                    }
                    break;           
            }
            ?>
        <?php endif; ?>  
    </div><?php // --- col ?>
</div><?php // --- row #ReviewForm ?>
