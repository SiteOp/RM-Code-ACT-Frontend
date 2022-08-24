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

$app = Factory::getApplication();
$user        = Factory::getUser();
$user_groups = $user->get('groups');
$userId      = $user->get('id');
$adminUser   = $user->authorise('core.admin', 'com_actj');	

$document    = Factory::getDocument();
$path        = JURI::base(true).'/templates/'.$app->getTemplate().'/';
$document->addStyleSheet('../media/system/css/fields/calendar.css');
$document->addStyleSheet($path.'/css/jui/chosen.css');

$now = Factory::getDate()->Format('d.m.Y'); 

$params      = JComponentHelper::getParams( 'com_act' );
$grade_offset_comment   = $params['grade_offset_comment'];
$stars_no_rating = $params['stars_no_rating'];


// Start und Ende der Grade unter Berücksichtigung der Anzahl wieviel rauf und runter bewertet werden darf ($grade_offset_comment)
$start_grade = $this->item->settergrade - $grade_offset_comment;
$end_grade =  $this->item->settergrade + $grade_offset_comment;

?>

    
<?php 
// Formular abgesendet
if (isset($_POST["submit"])) : ?>

    <?php   
    $jinput = Factory::getApplication()->input;
    
    // Datum umwandeln für die Datenbank (Y-m-d)
    $climbdate = $jinput->post->get('climbdate', '', 'STRING');
    $climbdate = $date = implode('-', array_reverse(explode('.', $climbdate)));
       
	// Create and populate an object.
	$comment = new stdClass();
	$comment->id = NULL; 
    $comment->state = 1;
	$comment->route = $this->item->id;
    $comment->comment = $jinput->post->get('comment', '', 'HTML');
	$comment->stars = $jinput->post->get('stars',  '0', 'UINT');
	$comment->myroutegrade = $jinput->post->get('myroutegrade', NULL, 'UINT');
    $comment->ticklist_yn = $jinput->post->get('ticklist_yn', '0', 'BOOL');
	$comment->ascent = $jinput->post->get('ascent', '1', 'STRING');
	$comment->tries = $jinput->post->get('tries', NULL, 'UINT');
	$comment->climbdate = $climbdate;
    $comment->tick_comment = $jinput->post->get('tick_comment', '', 'HTML');
    $comment->created_by = $userId;
	$comment->created = date('Y-m-d H:i:s');
	$comment->input = 1;
	
	// Insert the object into the user comment table.
	$result = Factory::getDbo()->insertObject('#__act_comment', $comment,  'id');
       $refresh_url = Route::_('index.php?option=com_act&view=route&id='  . $this->item->id, false, 1);
    ?>
    <meta http-equiv="refresh" content="1; URL=<?php echo $refresh_url; ?> ">
	 
<?php  else : ?>

    <form id="form-edit" action="<?php echo Route::_('index.php?option=com_act&view=route&id='  . $this->item->id, false, 1); ?>" 
        method="post" class="form-validate" enctype="multipart/form-data" onsubmit="return checkForm(this);">
   
        <div class="row">
            <div class="col-12">
                <div class="card" id="commentform"><?php //Kommentar abgebeben ?>
                    <div class="card-header">
                        <h3> 
                            <i class="<?php echo Text::_('COM_ACT_FA_COMMENT'); ?>"></i> 
                            <?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_COMMENT'); ?> 
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <fieldset>
                                    <select name="stars" required="required" class="form-control" >
                                        <option value=""> <?php echo Text::_('COM_ACT_SEARCH_FILTER_STARS'); ?></option>
                                        <?php if(1== $stars_no_rating) : ?>
                                        <option value="0"><?php echo Text::_('COM_ACT_NO_RATING'); ?></option>
                                        <?php endif; ?>
                                        <option value="1"><?php echo Text::_('COM_ACT_RATING_ONE'); ?></option>
                                        <option value="2"><?php echo Text::_('COM_ACT_RATING_TWO'); ?></option>
                                        <option value="3"><?php echo Text::_('COM_ACT_RATING_THREE'); ?></option>
                                        <option value="4"><?php echo Text::_('COM_ACT_RATING_FOUR'); ?></option>
                                        <option value="5"><?php echo Text::_('COM_ACT_RATING_FIVE'); ?></option>
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6">
                                <fieldset>
                                    <select name="myroutegrade" class="form-control" required="required" > 
                                        <option value=""><?php echo Text::_('COM_ACT_SELECT_GRADE'); ?></option> 
                                        <option value="0"><?php echo Text::_('COM_ACT_NO_RATING'); ?></option> 
                                        <?php if($this->item->settergrade !=0) : ?>
                                            <?php foreach (range($start_grade, $end_grade) as $i) : ?>
                                                <?php if($i >= 10 && $i <= 36) :?>
                                                    <option value="<?php echo $i; ?>"><?php echo ActHelpersAct::uiaa($i); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php // Wenn VR-Grade unbekannt dann erlaube alle Grade zum bewerten ?>
                                        <?php else : ?>
                                            <?php foreach (range(10, 36) as $i) : ?>
                                                <option value="<?php echo $i; ?>"><?php echo ActHelpersAct::uiaa($i); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select> 
                                </fieldset> 
                            </div>
                            </div>
                         <textarea id="comment" name="comment" class="form-control mt-3" placeholder="<?php echo Text::_('COM_ACT_COMMENTS_WRITE'); ?>" maxlength="600"></textarea>
                             <?php // Zeichen zählen ?>
                             <div id="the-count" class="float-right mt-1">
                                <span id="current">0</span>
                                <span id="maximum">/ 600</span>
                              </div>

                    </div><?php // Card-Body END ?>
                    
                </div><?php // Card END ?>

            </div><?php // Col END ?>

        
            <div class="col-12"> <?php // Ticklist ?> 
                <div class="card mt-2" id="ticklistform">
                    <div class="card-header">
                         <h3> <i class="<?php echo Text::_('COM_ACT_FA_TICKLIST'); ?>"></i> <?php echo JText::_('COM_ACT_TICKLIST'); ?> </h3>
                         <fieldset id="jform_ticklist_yn" name="jform_ticklist_yn" class="btn-group btn-group-yesno radio">
                            <input type="radio" id="jform_ticklist_yn1" name="ticklist_yn" value="1"  onclick="show1();" />
                            <label for="jform_ticklist_yn1" ><?php echo Text::_('JYES'); ?></label> 
                            <input type="radio" id="jform_ticklist_yn0" name="ticklist_yn" value="0" checked="checked"  onclick="show2();" />
                            <label for="jform_ticklist_yn0" ><?php echo Text::_('JNO'); ?></label>
                        </fieldset>
                        
                    </div><div id="div2" class="hide" style="display: none">   
                    <div class="card-body">
                        <div class="row">
                        
                        
                               
                    
                                <div class="col-12 col-sm-4">
                                    <fieldset>
                                            <select name="ascent" class="form-control">
                                                <option value="1"><?php echo Text::_('COM_ACT_ASCENT_OPTION_1'); ?></option> 
                                                <option value="2"><?php echo Text::_('COM_ACT_ASCENT_OPTION_2'); ?></option>
                                                <option value="3"><?php echo Text::_('COM_ACT_ASCENT_OPTION_3'); ?></option>
                                                <option value="4"><?php echo Text::_('COM_ACT_ASCENT_OPTION_4'); ?></option>
                                                <option value="5"><?php echo Text::_('COM_ACT_ASCENT_OPTION_5'); ?></option>
                                                <option value="6"><?php echo Text::_('COM_ACT_ASCENT_OPTION_6'); ?></option>
                                            </select> 
                                     </fieldset> 
                                </div>
                                
                                 <div class="col-12 col-sm-4">
                                    <fieldset>
                                        <input class="form-control" type="number" name="tries"  value="1" placeholder="Versuche" max="30" step="1" min="1">
                                    </fieldset> 
                                </div>
                            
                            <div class="col-12 col-sm-4">
                            <?php //Note BUG data-dayformat=" " - Do not use: data-dayformat="%d.%m.%Y" - it is not stored in the database ?>
                            <fieldset class="field-calendar" class="form-control" >
                                <div class="input-append">
                                    <input type="text"  name="climbdate" value="<?php echo $now; ?>" class="inputbox form-control"
                                           placeholder="<?php echo Text::_('COM_ACT_FORM_LBL_COMMENT_CLIMBDATE'); ?>" data-alt-value="<?php echo $now; ?>" autocomplete="off"/>
                                    <button type="button" 
                                            class="btn btn-secondary"
                                            id="climbdate"
                                            data-inputfield="climbdate"
                                            data-dayformat="%d.%m.%Y"
                                            data-button="climbdate_btn"
                                            data-firstday="1"
                                            data-weekend="0,6"
                                            data-today-btn="1"
                                            data-week-numbers="0"
                                            data-show-time="0"
                                            data-show-others="1"
                                            data-time-24="12"
                                            data-only-months-nav="1"
                                            title="<?php echo Text::_('JLIB_HTML_BEHAVIOR_OPEN_CALENDAR'); ?>">
                                        <i class="<?php echo Text::_('COM_ACT_FA_CALENDAR'); ?>"></i>
                                    </button>
                                </div>
                            </fieldset> 
                            </div>
                            </div>
                            <textarea id="tick_comment" name="tick_comment" class="form-control mt-3" placeholder="<?php echo Text::_('COM_ACT_COMMENTS_TICK_WRITE'); ?>" maxlength="255"></textarea> 

                        </div>
                    </div>
                    
                    </div>
                </div>
            </div>
           
        </div>
        <div class="row">
                        <div class="col">
                            <input type='submit' name='submit'  value='<?php echo Text::_('JSUBMIT'); ?>' class="float-right btn btn-submit btn-primary mt-3 ">
                        </div>
                         <?php echo JHtml::_('form.token'); ?>
    </form>
<?php //print_R($this->item->name); ?>
<?php endif; ?>
<script src="/media/com_act/js/form.js" type="text/javascript"></script>
<script src="/media/system/js/fields/calendar-locales/de.js" type="text/javascript"></script>
<script src="/media/system/js/fields/calendar-locales/date/gregorian/date-helper.min.js" type="text/javascript"></script>
<script src="/media/system/js/fields/calendar.min.js" type="text/javascript"></script>
<script src="/media/system/js/multiselect.js" type="text/javascript"></script>
<script src="/templates/act/js/jui/chosen.jquery.min.js" type="text/javascript"></script>
<script>
$('.form-control-chosen').chosen({
});
</script>

<script>

    function show1(){
      document.getElementById('div2').style.display ='block';
    }
    function show2(){
      document.getElementById('div2').style.display = 'none';
    }
</script>


<script>
 function checkForm(form)
  {
    // validation at least one field 
    if((form.stars.value == '0') && (form.myroutegrade.value == '0') && (form.comment.value == '')){
      alert("Es muss entweder ein Kommentar, eine Bewertung (Sterne) oder ein Routegrad gewählt werden!");
      return false;
    }
     if((form.ascent.value <= '2') && (form.tries.value > '1')){
      alert("Bitte Angabe des Durchstieg bzw. der Versuche prüfen");
      return false;
     }
    // validation was successful
    return true;
  }
</script>


<?php // Zeichen zählen // https://codepen.io/zabielski/pen/gPPywv ?>
<script>
$('#comment').keyup(function() {
    
  var characterCount = $(this).val().length,
      current = $('#current'),
      maximum = $('#maximum'),
      theCount = $('#the-count');
    
  current.text(characterCount);
   if (characterCount > 400) {
    current.css('color', '#6d5555');
  }

  if (characterCount >= 500) {
    maximum.css('color', '#8f0001');
    current.css('color', '#8f0001');
    theCount.css('font-weight','bold');
  } else {
    maximum.css('color','#6d5555');
    theCount.css('font-weight','normal');
  }
  
      
});
</script>

<?php  echo $this->loadTemplate('sendmail'); ?>
