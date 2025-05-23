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
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper; 

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');


$user    = Factory::getUser();
$canEdit = ActHelpersAct::canUserEdit($this->item, $user);

// Lade die Globale Sprachdatei für ACT
$lang = Factory::getLanguage();
$extension = 'com_act_global';
$base_dir = JPATH_SITE;
$language_tag = '';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

// ACT Params 
$params          = JComponentHelper::getParams('com_act');
//$holds           = $params['holds']; // Werden die Angaben Hersteller - Griffe benötigt?
$grade_table = $params['grade_table'];             // Welche Tabelle für Schwierigkeitsgrade
$routetype       = $params['routetype']; 
$extendFormField = $params['extendFormField'];
$route_properties = $params['use_route_properties'];
$use_route_lifetime = $params['use_route_lifetime'];


if($this->item->state == 1){
	$state_string = 'Publish';
	$state_value = 1;
 } else {
	$state_string = 'Unpublish';
	$state_value = 0;
 }
$canState = Factory::getUser()->authorise('core.edit.state','com_act');

JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
// Liste der Schwierigkeitsgrade aus der jeweiligen Tabelle UIAA/Franz usw
$gradeList = GradeHelpersGrade::getSettergradeList($grade_table);

?>


<?php if (!$canEdit) : ?>
    <h3><?php throw new Exception(Text::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?></h3>
<?php else : ?>

    <?php // Pager-Header Start ?>
    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><i class="fas fa-cube"></i> <?php echo Text::sprintf('COM_ACT_FORM_ROUTE_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"> <i class="fas fa-cube"></i> <?php echo Text::_('COM_ACT_FORM_ROUTE_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>
    <?php // Pager-Header END ?>
    

    <div id="form-edit" class="route-edit front-end-edit">

        <form id="form-route" action="<?php echo Route::_('index.php?option=com_act&task=route.save'); ?>"
                method="post" class="form-validate" enctype="multipart/form-data" >
              
            <?php echo $this->form->getInput('id'); ?>
            
            <div class="form-group row">  
                <div class="col-md-5"><?php echo $this->form->renderField('state'); ?></div>
              </div>
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('name'); ?></div>
                <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('setterdate'); ?></div>
            </div> 
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('setter'); ?></div>
                <div class="col-md-5 col-md-offset-1">
                    <?php echo $this->form->getLabel('settergrade'); ?>
                    <div class="controls">
                        <select id="jform_settergrade" name="jform[settergrade]" class="required" required aria-required="true">
                            <option value="" ><?php echo Text::_('COM_ACT_ROUTES_GRADE_OPTION_0'); ?></option>
                            <option value="0" ><?php echo Text::_('COM_ACT_ROUTES_GRADE_OPTION_100');?></option>
                            <?php foreach($gradeList AS $value) : ?>
                                <option value="<?php echo $value->id_grade; ?>" 
                                <?php echo ($value->id_grade === ($this->item->settergrade[0]) ? 'selected="selected"' : '');  ?>>
                                 <?php echo $value->grade; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                 </div>
            </div>
            
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('line'); ?></div>
                <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('color'); ?></div>
            </div>

            <div class="form-group row">
               <div class="col-md-5"><?php echo $this->form->renderField('info'); ?></div>
               <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('infoadmin'); ?></div>
            </div>
			
			 
			<div class="form-group row">
            <?php if(1==$routetype) : ?>
			   <div class="col-md-5"><?php echo $this->form->renderField('routetype'); ?></div>
            <?php endif; ?>
            <?php if(1==$route_properties) : ?>
			   <div class="col-md-5"><?php echo $this->form->renderField('properties'); ?></div>
            <?php endif; ?>
			</div>

 			<?php if(1==$use_route_lifetime) : ?>
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('route_lifetime'); ?></div>
            </div> 
			<?php endif; ?>
			

            <?php // Wird das Template für die erweiterten Felder (z. B. Hersteller) benötigt?>
            <?php if(1 == $extendFormField) : ?>
                <?php echo $this->loadTemplate('extend'); ?>
			<?php endif; ?>

			<h3 class="mt-5"><?php echo Text::_('COM_ACT_FORM_LBL_FORM_EXTEND'); ?></h3>
			
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('formextend'); ?></div>
            </div> 
			
			<div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('hidden'); ?></div>
                <div class="col-md-5 col-md-offset-1"> <?php echo $this->form->renderField('fixed'); ?></div>
            </div>

            

            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('exclude'); ?></div>
                <div class="col-md-5 col-md-offset-1"> </div>
            </div>
            
             <div id="infoextend" class="form-group row">
                <div class="col-md-5"> <?php echo $this->form->renderField('infoextend'); ?></div>
                <div class="col-md-5 col-md-offset-1"></div>
            </div>
            
            <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
            <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
            

            <div class="control-group">
                <div class="controls mt-1">
                    <?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-secondary mr-1">
                            <?php echo Text::_('COM_ACT_SUBMIT_SAVE'); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <input type="hidden" name="option" value="com_act"/>
            <input type="hidden" name="task" value="routeform.save"/>
            
            <?php echo HTMLHelper::_('form.token'); ?>

        </form>
<?php endif; ?>
</div>


<script>
// Required hinzufügen wenn -----
$("[name='jform[txt_holds]']").prop("required", true);
// Stern zum Label hinzufügen 
$("#jform_holds-lbl, #jform_txt_holds-lbl").each(function(){
   $(this).append("<span class='star'> *</span>");
  });
</script>
