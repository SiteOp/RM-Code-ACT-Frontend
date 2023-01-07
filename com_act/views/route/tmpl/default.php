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
$adminUser   = $user->authorise('core.admin', 'com_act');
$routeAdmin  = 	$user->authorise('route.edit', 'com_act');
$canEdit     = Factory::getUser()->authorise('core.edit', 'com_act');

// ACT Params 
$params      = JComponentHelper::getParams('com_act');
$indicator   = $params['admin_lines_indicator'];
$colorOne    = $params['colorOne'];
$colorTwo    = $params['colorTwo'];
$colorThree  = $params['colorThree'];
$colorFour   = $params['colorFour'];
$colorZeiger = $params['colorZeiger'];
$extendFormField   = $params['extendFormField'];  // Griffhersteller ja/nein

// Helper um die Tabelle der Schwierigkeitsgrade zu erhalten
JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
//  Tacho 
$tachoCGrade       = GradeHelpersGrade::getGrade(($this->item->id_grade));
$tachoCGradeBefore = GradeHelpersGrade::getGrade(($this->item->id_grade) -1);
$tachoCGradeAfter  = GradeHelpersGrade::getGrade(($this->item->id_grade) +1);
$tachoZeiger       = ((($this->item->id_grade - ($this->item->id_grade)+1)/2) *100);
?>


<?php // Nach dem Kommentieren wird die Seite Refreshed. Dadurch kein HTTP_REFERER. Der Backlink muss daher unterschiedlich sein ?>
<a href='<?php echo URI::base(); ?>' 
   <?php if(isset($_SERVER['HTTP_REFERER'])) {
        echo "onclick='window.history.go(-1); return false;'> ";
    }
    else
    {
        echo "onclick='window.history.go(-2); return false;'> ";
    }
    ?>
    <button id='refresh-back' type='button' class='btn  btn-info'>
     <i class='fas fa-chevron-left'></i>
    </button>
</a>


<?php // Page-Header ?> 
	<div class="page-header">
        <h1>
            <span class="header-präfix"><?php echo Text::_('COM_ACT_ROUTE'); ?>:</span> 
            <?php echo $this->item->name; ?>
            <?php if (0 == $this->item->exclude) : ?>
				<div class="Stars" style=" --rating: <?php echo ActHelpersAct::getStarsRound($this->item->avg_stars); ?>;"></div>
				<small class="stars_percent">(<?php echo number_format(round($this->item->avg_stars,2),2); ?>)</small>
            <?php endif; ?>
        </h1>
	</div>
	
<div class="row">
        <div class="col-12 col-md-12 col-xl-4"> <?php // Detail Route ?>
		

            <div class="card">
                <div class="card-header">
                    <h3><i class="<?php echo Text::_('COM_ACT_FA_ROUTE'); ?>"></i> 
					<?php echo Text::_('COM_ACT_ROUTE_Details'); ?> 
					<?php if($adminUser) : ?> <?php // Für Admin - Tachoanzeige und Grade-ID ?>
					    <span class="float-right"><?php echo $tachoZeiger; ?> / <?php echo $this->item->id_grade; ?></span>
					<?php endif; ?>
					
					</h3>
                </div>
                 <div class="card-body">
				 <?php if (0 == $this->item->exclude) : ?><?php // Tacho anzeigen wenn Route für Kommentare nicht gesperrt ?>
					<dl class="row mt-4">
                           <dt class="col-6">
							 <label data-toggle="popover" 
                                       data-trigger="focus, hover" 
                                       data-placement="right" 
                                       data-html="true"
                                       data-original-title="<?php echo Text::_('COM_ACT_POPOVER_HEAD_C_GRADE_TACHO'); ?>" 
                                       data-content="<?php echo Text::_('COM_ACT_POPOVER_TXT_C_GRADE_TACHO'); ?>">
                                      <i class="<?php echo Text::_('COM_ACT_FA_INFO'); ?>"></i>
                                       <?php echo Text::_('COM_ACT_POPOVER_HEAD_C_GRADE_TACHO'); ?> 
                                </label>
							</dt>
							
							   <dd class="col-6"> <?php // Tacho ?>
								    <div class="gauge tacho1"></div>       
							   </dd>
                        </dl>
                        <dl class="row">
                            <dt class="col-6"><?php // C-Grade & Popover ?>
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
                            <dd class="col-6">
                                <?php echo $this->item->c_grade != 0 ? $this->item->c_grade : '-'; ?>
                                <?php echo $this->item->c_grade != 0 ? ' | ' .$this->item->grade_convert : ''; ?>
                            </dd>
                        </dl>

                        <dl class="row">
                           <dt class="col-6"><?php // VR-Grade & Popover ?>
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
                           <dd class="col-6">
                                <?php echo $this->item->s_grade != 0 ? $this->item->s_grade : '-'; ?>
                            </dd>
                        </dl>
                    <?php endif; ?>
                        <dl class="row  mt-4"><?php // Color ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_COLOR'); ?></dt>
                           <dd class="col-6">
                                <span class="routecolor" style="background: <?php echo $this->item->rgbcode; ?>;"> </span>
                                <?php echo '&nbsp;' . $this->item->color; ?>
                            </dd>
                        </dl>
                        <dl class="row"><?php // Line - In-/Outdoor ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_LINE'); ?></dt>
                           <dd class="col-6"><?php echo $this->item->line; ?> / <?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_' . $this->item->inorout); ?></dd>
                          
                        </dl>
                        <dl class="row"><?php // Sector ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SECTOR'); ?></dt>
                           <dd class="col-6"><?php echo $this->item->lineSectorName; ?></dd>
                        </dl>
                        <?php if (!empty($this->item->height)) : ?>
                         <dl class="row"><?php // Wandhöhe ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_HEIGHT'); ?></dt>
                           <dd class="col-6"><?php echo $this->item->height; ?> <?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_UNIT'); ?></dd>
                        </dl>
                       <?php endif; ?>
                        <?php if (!empty($img->image)) : ?><?php // Standordbild vorhanden? ?>
                        <dl class="row"><?php // Standort ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_STANDORT'); ?></dt>
                           <dd class="col-6">
                                <a type="button"  data-toggle="modal" data-target="#exampleModal"> 
                                    <i class="<?php echo Text::_('COM_ACT_FA_IMAGE'); ?>"></i>
                                </a>
                           </dd>
                        </dl>
                        <?php endif; ?>
                        <dl class="row"><?php // Setter ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_SETTER'); ?></dt>
                           <dd class="col-6"><?php echo $this->item->settername; ?></dd>
                        </dl>
                        <dl class="row"><?php // Setterdate ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_SETTERDATE'); ?></dt>
                           <dd class="col-6"><?php echo HTMLHelper::_('date', $this->item->setterdate, 'd.m.Y'); ?></dd>
						   <?php $setterdate = HTMLHelper::_('date', $this->item->setterdate, 'Y-m-d'); ?>
                        </dl>
                        
                        <?php if (!empty($this->item->info)) : ?>
                        <dl class="row mt-4"><?php // Route Info ?>
                           <dt class="col-6"><?php echo Text::_('COM_ACT_TABLE_LBL_ROUTE_INFO'); ?></dt>
                           <dd class="col-12"><?php echo $this->item->info; ?></dd>
                        </dl>
                        <?php endif; ?>
                        
                        <?php if (!empty($this->item->sp_name)) : ?>
                        <dl class="row sponsor"><?php // Sponsor ?>
                           <dt class="col-6"><img class="img-fluid" src="<?php echo($this->item->sp_media); ?>" alt=""  ></dt>
                           <dd class="col-6"><?php echo($this->item->sp_txt); ?></dd>
                        </dl>
                        <?php endif; ?>
                    </div>

            </div><?php // CARD END ?>
        
            <?php if($extendFormField) { // Griffhersteller ja/nein
                echo $this->loadTemplate('extend_info');
            }; ?>


            <?php if($routeAdmin) : ?><?php // Admin Info - Route ?>
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
                            <dt class="col-6"><?php echo Text::_('COM_ACT_ID'); ?></i></dt>
                            <dd class="col-6"><?php echo $this->item->id; ?></dd>
                        </dl>
						<?php if (1 == $indicator) : ?>
							<dl class="row">
								<dt class="col-6"><?php echo Text::_('COM_ACT_INDICATOR'); ?></i></dt>
								<dd class="col-6"><?php echo $this->item->indicator; ?></dd>
							</dl>
						<?php endif; ?>
                        <dl class="row">
                           <dt class="col-6"><?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_INFOADMIN'); ?></dt>
                           <dd class="col-6"><?php echo nl2br($this->item->infoadmin); ?></dd>
                        </dl>
                    </div>
                
                </div><?php // CARD END ?>
            <?php endif; ?>
        </div>


        <?php // Kommentare - Template && Zusatzinfo z.B für Speedroute usw. ?>
        <div class="col-12 col-md-12 col-xl-8  route-comment">
        
            <?php // Info Extend als Card vor den Review  == Zusatzinfo z.B für Speedroute usw.?>
            <?php if (!empty($this->item->infoextend)) : ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Info</h3>
                     </div>
                    <div class="card-body">
                    <?php echo $this->item->infoextend; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (0 == $this->item->exclude) : ?> <?php // Ist die Route für Kommentare gesperrt? z.B Speedrouten usw ?>
               <?php echo $this->loadTemplate('review_list'); ?>
                
                <div id="ReviewForm" class="mt-4"> <?php // Formular - Route - Kommentar ?>
                <?php // Load Form if User Registered  ?>    
                <?php if ($user->get('guest')) : ?>
                <div class="card mt-5" id="notlogin">
                    <div class="card-header"><h3><i class="<?php echo Text::_('COM_ACT_FA_LOGIN'); ?>"></i> <?php echo Text::_('COM_ACT_ROUTE_NOT_LOGGIN'); ?></h3></div> 
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
            <?php endif; ?> 
        </div>
 
</div><?php // END div row ?>	

<?php // Tacho ?>
<script src="/media/com_act/js/jquery-gauge.min.js"></script>
<script>

    // xample
        $('.gauge').gauge({
            values: {
                0 :  '<?php echo $tachoCGradeBefore; ?>',
                50:  '<?php echo $tachoCGrade; ?>',
                100: '<?php echo $tachoCGradeAfter; ?>'
            },
            colors: {
                0 :  '<?php echo $colorOne; ?>',
				25:  '<?php echo $colorTwo; ?>',
                50:  '<?php echo $colorThree; ?>',
				75:  '<?php echo $colorFour; ?>'
            },
            angles: [
                180,
                360
            ],
            lineWidth: 12,
            arrowWidth: 8,
            arrowColor: '<?php echo $colorZeiger; ?>',
            inset:true,

            value: <?php echo $tachoZeiger; ?>
        });
</script>
<script>
$(function () {
  $('[data-toggle="popover"]').popover()
})
</script>