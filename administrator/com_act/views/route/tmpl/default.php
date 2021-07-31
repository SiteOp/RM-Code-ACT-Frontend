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
$params     = Factory::getApplication()->getParams('com_act');
$sponsoring = $params->get('sponsoring');

// IMG aus Kategorie der Sektoren - Steht im Datenbankfeld Params - Muss über Json Decodiert werden
$img = json_decode($this->item->params);

// Falls das Object nicht vorhanden ist (Route nicht im View) dann erstelle das Object und setzte Default Wert 0 
if (empty((array) $this->view)) {
    $this->view = new stdClass(0);
    $this->view->avg_stars = 0 ;
    $this->view->C_Grade = 0;
}

?>


<?php if (!empty($img->image)) : ?>

 
<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo $this->item->lineSectorName; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <img class="img-fluid" src="<?php echo $img->image;  ?>" width="800" height="800"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php endif; ?>


<?php // Page-Header ?> 
	<div class="page-header">
        <h1>
            <span class="header-präfix"><?php echo Text::_('COM_ACT_ROUTE'); ?>:</span> <?php echo $this->item->name; ?>
            <?php echo ActHelpersAct::getStarsByParams(round($this->view->avg_stars,1)); ?>
        </h1>
	</div>
<div class="row">
        <div class="col-12 col-md-12 col-xl-4"> <?php // Detail Route ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="<?php echo Text::_('COM_ACT_FA_ROUTE'); ?>"></i> <?php echo Text::_('COM_ACT_ROUTE_Details'); ?></h3>
                </div>
                 <div class="card-body">
                        <dl class="row">
                            <dt class="col-5"><?php // C-Grade & Popover ?>
                                <label data-toggle="popover" 
                                       data-trigger="focus, hover" 
                                       data-placement="right" 
                                       data-html="true"
                                       data-original-title="<?php echo Text::_('COM_ACT_ROUTE_POPOVER_HEAD_C_GRADE'); ?>" 
                                       data-content="<?php echo Text::_('COM_ACT_ROUTE_POPOVER_TXT_C_GRADE'); ?>">
                                      <i class="<?php echo Text::_('COM_ACT_FA_INFO'); ?>"></i>
                                       <?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_C_GRADE'); ?> 
                                </label>
                            </dt>
                            <dd class="col-7">
                                <?php if ($this->view->C_Grade == 0) :?>
                                    <?php echo '-'; ?>
                                <?php else: ?>
                                    <?php echo ActHelpersAct::uiaa(round($this->view->C_Grade,0)); ?>
                            <?php endif; ?>
                            </dd>
                        </dl>
                        <dl class="row">
                           <dt class="col-5"><?php // VR-Grade & Popover ?>
                                <label data-toggle="popover"
                                       data-trigger="focus, hover"  
                                       data-placement="top" 
                                       data-html="true"
                                       data-original-title="<?php echo Text::_('COM_ACT_ROUTE_POPOVER_HEAD_VR_GRADE'); ?>" 
                                       data-content="<?php echo Text::_('COM_ACT_ROUTE_POPOVER_TXT_VR_GRADE'); ?>">
                                       <i class="<?php echo Text::_('COM_ACT_FA_INFO'); ?>"></i>
                                        <?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SETTER_GRADE'); ?>
                                </label>
                           </dt>
                           <dd class="col-7"><?php echo $this->item->uiaa; ?> || <?php echo $this->item->franzoesisch; ?></dd>
                        </dl>
                        <dl class="row"><?php // Color ?>
                           <dt class="col-5"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_COLOR'); ?></dt>
                           <dd class="col-7"><span class="routecolor" style="background: <?php echo $this->item->rgbcode; ?>;"></span></dd>
                        </dl>
                        <dl class="row"><?php // Line - In-/Outdoor ?>
                           <dt class="col-5"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_LINE'); ?></dt>
                           <dd class="col-7"><?php echo $this->item->line; ?> / <?php echo Text::_('COM_ACT_LINES_INOROUT_OPTION_' . $this->item->inorout); ?></dd>
                        </dl>
                        <dl class="row"><?php // Sector ?>
                           <dt class="col-5"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SECTOR'); ?></dt>
                           <dd class="col-7"><?php echo $this->item->lineSectorName; ?></dd>
                        </dl>
                       
                        <?php if (!empty($img->image)) : ?><?php // Standordbild vorhanden? ?>
                        <dl class="row"><?php // Standort ?>
                           <dt class="col-5"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_STANDORT'); ?></dt>
                           <dd class="col-7">
                                <a type="button"  data-toggle="modal" data-target="#exampleModal"> 
                                    <i class="<?php echo Text::_('COM_ACT_FA_IMAGE'); ?>"></i>
                                </a>
                           </dd>
                        </dl>
                        <?php endif; ?>
                        <dl class="row"><?php // Setter ?>
                           <dt class="col-5"><?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_SETTER'); ?></dt>
                           <dd class="col-7"><?php echo $this->item->settername; ?></dd>
                        </dl>
                        <dl class="row"><?php // Setterdate ?>
                           <dt class="col-5"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SETTERDATE'); ?></dt>
                           <dd class="col-7"><?php echo HTMLHelper::_('date', $this->item->setterdate, 'd.m.Y'); ?></dd>
                        </dl>
                        
                        <?php if (!empty($this->item->sp_name)) : ?>
                        <dl class="row sponsor"><?php // Sponsor ?>
                           <dt class="col-5"><img class="img-fluid" src="<?php echo($this->item->sp_media); ?>" alt=""  ></dt>
                           <dd class="col-7"><?php echo($this->item->sp_txt); ?></dd>
                        </dl>
                        <?php endif; ?>
                    </div>

            </div><?php // CARD END ?>
             
            <?php if($adminUser) : ?><?php // Admin Info - Route ?>
                <div class="card mt-5 mb-5">
                    <div class="card-header">
                        <h3 class="float-left"><i class="<?php echo Text::_('COM_ACT_FA_ADMIN'); ?>"></i> <?php echo Text::_('COM_ACT_ROUTE_ADMIN_INFO'); ?></h3>
                        <?php // Button to Edit Route ?>
                        <a class="btn btn-success float-right" href="<?php echo Route::_('index.php?option=com_act&task=route.edit&id='.$this->item->id); ?>">
                            <?php echo Text::_("COM_ACT_ROUTES_EDIT_ITEM"); ?> <i class="<?php echo Text::_('COM_ACT_FA_EDIT'); ?>"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-5"><?php echo JText::_('COM_ACT_ID'); ?></i></dt>
                            <dd class="col-7"><?php echo $this->item->id; ?></dd>
                        </dl>
                        <dl class="row">
                           <dt class="col-5"><?php echo JText::_('COM_ACT_TABLE_LBL_ROUTE_MODIFIED'); ?></dt>
                           <dd class="col-7"><?php echo HTMLHelper::_('date', $this->item->modified, 'd.m.Y - H.i');  ?> Uhr</dd>
                        </dl>
                        <dl class="row">
                           <dt class="col-5"><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_INFOADMIN'); ?></dt>
                           <dd class="col-7"><?php echo nl2br($this->item->info); ?></dd>
                        </dl>
                    </div>
                
                </div><?php // CARD END ?>
            <?php endif; ?>
            
           
        </div>

        <?php // Template Review laden ?> <?php // Kommentare - Template ?>
        <div class="col-12 col-md-12 col-xl-8">
           
           <?php echo $this->loadTemplate('review_list'); ?>
            
            <div id="ReviewForm" class="mt-4"> <?php // Formular - Route - Kommentar ?>
            <?php // Load Form if User Registered  ?>    
            <?php if ($user->get('guest')) : ?>
            <div class="card mt-5" id="notlogin">
                <div class="card-header"><h3><i class="<?php echo Text::_('COM_ACT_FA_LOGIN'); ?>"></i> <?php echo JText::_('COM_ACT_ROUTE_NOT_LOGGIN'); ?></h3></div> 
            </div>
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
   
            </div><?php // --- row #ReviewForm ?>
        </div>

</div><?php // END div row ?>	



<script>
$(function () {
  $('[data-toggle="popover"]').popover()
})
</script>