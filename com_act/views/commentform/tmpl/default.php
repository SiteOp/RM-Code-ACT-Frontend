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

use Joomla\CMS\Language\Text;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_act', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_act/js/form.js');

$user    = JFactory::getUser();
$canEdit = ActHelpersAct::canUserEdit($this->item, $user);
$canState = JFactory::getUser()->authorise('core.edit.state','com_act');
$canChange  = $user->authorise('core.edit.state', 'com_act');
$canDelete  = $user->authorise('core.delete', 'com_act');

$params      = JComponentHelper::getParams( 'com_act' );
$grade_offset_comment   = $params['grade_offset_comment'];
$stars_no_rating = $params['stars_no_rating'];


// Start und Ende der Grade unter Berücksichtigung der Anzahl wieviel rauf und runter bewertet werden darf ($grade_offset_comment)
$start_grade = $this->route->settergrade - $grade_offset_comment;
$end_grade =  $this->route->settergrade + $grade_offset_comment;

if($this->item->state == 1){
	$state_string = 'Publish';
	$state_value = 1;
} else {
	$state_string = 'Unpublish';
	$state_value = 0;
}
$routeStateOk = array(1,-1);
?>
<?php // Wenn die Route im Archiv ist darf keine Bearbeitung von Kommentaren möglich sein ?>
<?php if ((!$canEdit) OR (!in_array($this->route->state, $routeStateOk))) : ?> 
    <h3><?php throw new Exception(JText::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?></h3>
<?php else : ?>

    <?php // Pager-Header Start ?>
    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><?php echo JText::sprintf('COM_ACT_FORM_COMMENT_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"><?php echo JText::_('COM_ACT_COMMENT_COLOR_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>
    <?php // Pager-Header END ?>
    

    <div id="form-edit" class=" comment-edit front-end-edit">

        <form id="form-comment" action="<?php echo JRoute::_('index.php?option=com_act&task=comment.save'); ?>"
              method="post" class="form-validate" enctype="multipart/form-data" onsubmit="return checkForm(this);">
            
            

            <div class="row">
                <div class="col-12 col-md-8 order-1">
                    <div class="card" id=""><?php //Kommentar abgebeben ?>
                        <div class="card-header">
                            <h3>
                                <i class="<?php echo Text::_('COM_ACT_FA_COMMENT'); ?>"></i> 
                                <?php echo JText::_('COM_ACT_FORM_LBL_COMMENT_COMMENT'); ?> 
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                <fieldset>
                                    <select id="jform_stars" name="jform[stars]" required="required" class="form-control" >
                                        <option value=""> <?php echo Text::_('COM_ACT_SEARCH_FILTER_STARS'); ?></option>
                                        <?php if(1== $stars_no_rating) : ?>
                                        <option value="0"  
                                            <?php echo (0 === ((int)$this->item->stars[0]) ? 'selected="selected"' : '');  ?>>
                                            <?php echo Text::_('COM_ACT_NO_RATING'); ?>
                                        </option>
                                        <?php endif; ?>
                                        <option value="1" 
                                            <?php echo (1 === ((int)$this->item->stars[0]) ? 'selected="selected"' : '');  ?>>
                                            <?php echo Text::_('COM_ACT_RATING_ONE'); ?>
                                        </option>
                                        <option value="2" 
                                            <?php echo (2 === ((int)$this->item->stars[0]) ? 'selected="selected"' : '');  ?>>
                                            <?php echo Text::_('COM_ACT_RATING_TWO'); ?>
                                        </option>
                                        <option value="3" 
                                            <?php echo (3 === ((int)$this->item->stars[0]) ? 'selected="selected"' : '');  ?>>    
                                            <?php echo Text::_('COM_ACT_RATING_THREE'); ?>
                                        </option>
                                        <option value="4" 
                                            <?php echo (4 === ((int)$this->item->stars[0]) ? 'selected="selected"' : '');  ?>>
                                            <?php echo Text::_('COM_ACT_RATING_FOUR'); ?>
                                        </option>
                                        <option value="5" 
                                            <?php echo (5 === ((int)$this->item->stars[0]) ? 'selected="selected"' : '');  ?>>
                                            <?php echo Text::_('COM_ACT_RATING_FIVE'); ?>
                                        </option>
                                    </select> 
                                </fieldset>
                                </div>
                                <div class="col-12 col-md-6 ">
                                    <div class="controls">
                                        <select id="jform_myroutegrade" name="jform[myroutegrade]" > 
                                            <option value="0" selected="selected"><?php echo JText::_('COM_ACT_FORM_LBL_HINT_NO_GRAD'); ?></option> 
                                             <?php if($this->route->settergrade !=0) : ?>
                                            <?php foreach (range($start_grade, $end_grade) as $i) : ?>
                                                <?php if($i >= 10 && $i <= 36) :?>
                                                    <option value="<?php echo $i; ?>"
                                                        <?php echo (($i == $this->item->myroutegrade[0]) ? 'selected="selected"' : '');  ?>>
                                                        <?php echo ActHelpersAct::uiaa($i); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php // Wenn VR-Grade unbekannt dann erlaube alle Grade zum bewerten ?>
                                        <?php else : ?>
                                            <?php foreach (range(10, 36) as $i) : ?>
                                                <option value="<?php echo $i; ?>"
                                                <?php echo (($i == $this->item->myroutegrade[0]) ? 'selected="selected"' : '');  ?>>
                                                <?php echo ActHelpersAct::uiaa($i); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                         </select> 
                                    </div> 
                                </div> 
                                 <div class="col-12 mt-3">
                                    <?php echo $this->form->getInput('comment'); ?>
                                </div>
                               
                            </div><?php // row END ?>
                        </div> <?php // Card-Body END ?>
                    </div> <?php // Card END ?>   
                </div><?php // Col End ?>

                <div class="col-12 col-md-4 order-3 order-md-2">
                    <div class="card">
                        <div class="card-header">
                             <h3><i class="<?php echo Text::_('COM_ACT_FA_ROUTE'); ?>"></i> <?php echo Text::_('COM_ACT_ROUTE_Details'); ?></h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">                    
                                <dt class="col-5">Route</dt>
                                <dd class="col-7"><?php echo ActHelpersAct::getRouteByID($this->item->route[0]); ?></dd>
                            </dl>
                            <dl class="row">                    
                                <dt class="col-5">Kommentar von:</dt>
                                <dd class="col-7"> <?php echo $this->form->renderField('created'); ?></dd>
                            </dl>
                            <dl class="row">                    
                                <dt class="col-5">Kommentar geändert</dt>
                                <dd class="col-7"><?php echo $this->form->renderField('modified'); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="col-12 col-md-8 order-2 ">
                    <div class="card" id="mycomments_ticklistform">
                        <div class="card-header">
                            <h3> <i class="<?php echo Text::_('COM_ACT_FA_TICKLIST'); ?>"></i>
                            <?php echo JText::_('COM_ACT_TICKLIST'); ?> </h3>
                            <?php echo $this->form->renderField('ticklist_yn'); ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <?php echo $this->form->renderField('ascent'); ?>
                                </div>
                                <div class="col-12 col-md-4">
                                    <?php echo $this->form->renderField('tries'); ?>
                                </div>
                                <div class="col-12 col-md-4">
                                    <?php echo $this->form->renderField('climbdate'); ?>
                                </div>
                                <div class="col-12 mt-3">
                                    <?php echo $this->form->renderField('tick_comment'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><?php // Col END ?>
                 
               
            </div><?php // row END ?>
            




            
            <div class="row">
                <div class="col-12 col-md-8 mt-3">
                    <div class="control-group">
                        <div class="controls float-right mt-1">

                            <?php if ($this->canSave): ?>
                                <button type="submit" class="validate btn btn-secondary mr-1">
                                    <?php echo JText::_('COM_ACT_SUBMIT_SAVE'); ?>
                                </button>
                            <?php endif; ?>
                            <a class="btn btn-warning mr-1"
                               href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.cancel'); ?>"
                               title="<?php echo JText::_('JCANCEL'); ?>">
                                <?php echo JText::_('JCANCEL'); ?>
                            </a>
                            <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.remove&id=' .$this->item->id, false, 2); ?>"
                            class="btn btn-danger delete-button" type="button"><?php echo JText::_('JACTION_DELETE'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
                    <?php echo $this->form->renderField('created_by'); ?>
                    <?php echo $this->form->getInput('modified_by'); ?>
                    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
                    <input type="hidden" name="jform[route]" value="<?php echo $this->item->route[0]; ?>" />
                    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
                    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
                    <input type="hidden" name="jform[input]" value="1" />
                    <?php echo $this->form->getInput('id'); ?>      
                    <input type="hidden" name="option" value="com_act"/>
                    <input type="hidden" name="task"
                           value="commentform.save"/>
                    <?php echo JHtml::_('form.token'); ?>
                </form>
<?php endif; ?>
</div><?php // DIV Form END ?>


<script>
    function show1(){
      document.getElementById('div2').style.display ='block';
    }
    function show2(){
      document.getElementById('div2').style.display = 'none';
    }
</script>
<?php // Script Bootstrap Tooltip ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('[rel=popover]').popover();
    });
</script>


<script>
 function checkForm(form)
  {
    // validation at least one field 
     if((form.jform_stars.value == '0') && (form.jform_comment.value == '') && (form.jform_myroutegrade.value == '0')){
      alert("Es muss entweder ein Kommentar, eine Bewertung (Sterne) oder ein Routegrad gewählt werden!");
      return false;
    }
     if((form.jform_ascent.value <= '2') && (form.jform_tries.value > '1') ){
      alert("Bitte Angabe des Durchstieg bzw. der Versuche prüfen");
      return false;
    }
    // validation was successful
    return true;
  }
</script>


<?php if($canDelete) : ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo Text::_('COM_ACT_MYCOMMENT_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>
<?php endif; ?>