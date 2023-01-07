<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Act_building
 * @author     Birgit Gebhard <info@routes-manager.de>
 * @copyright  2021 Birgit Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Add Script 
$doc = Factory::getDocument();
$doc->addScript('media/com_routes_planning/js/prozent.js', true, true); 

$user    = Factory::getUser();

// Helper Grade aus ACT Grade.php
JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');

$gradeList   = GradeHelpersGrade::getGradeListPlanningPercent(); // JSON String der Grade
$color_array = json_decode(GradeHelpersGrade::getGradeColorList());  // JSON String der Farben
$listRange   = GradeHelpersGrade::getSettergradeListRange(); // JSON String der Grade

// ACT Params wichtig um die Farbe der Grade zu erhalten
$params      = JComponentHelper::getParams('com_act');


if(!empty($this->item->id)) {                            // Nur wenn es die ID bzw. das Gebäude schon gibt
    $json = json_decode($this->item->percentsoll, true); // Hole die Werte aus DB um die Inputfelder vorab zu füllen
	$total_lines = count($this->lines);
} else {
	$total_lines = 0;

}
print_R($json);

?>

             
		
	<h3 class="mt-5"><?php echo Text::_('COM_ACT_SHOULD_DISTRIBUTE_GRADES'); ?></h3>
	<div><?php echo Text::_('COM_ACT_TOTAL_LINES'); ?>: <span id="total_lines"><?php echo $total_lines ; ?></span></div>
	<div><?php echo Text::_('COM_ACT_ROUTES_DENSITY'); ?>: <span id="density"> </span> </div>

	<div class="row mt-2">
       <div class="col-sm-5"><?php echo $this->form->renderField('routestotal'); ?></div>
    </div>

    <div id="gradetable" class="table-responsive mt-4">
        <table class="table table-bordered text-center" id="datatable">
            <thead>
                <tr id="gradelabel">
				<td><?php echo Text::_('COM_ACT_GRADE'); ?></td>
                <?php foreach($gradeList AS $value) : ?>
                    <td class="grade">
                        <label id="gradelbl<?php echo $value->id_grade; ?>" class="lblg grade<?php echo $value->id_grade; ?>" 
                               style="border-color:<?php echo  $params['color'.$value->filter.'grad']; ?>">
                            <?php echo $value->grade; ?>
                        </label>
                    </td>

                <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
			<tr id="percent_val"> 
                    <td><?php echo Text::_('COM_ACT_PERCENT'); ?></td>
         
                    <?php foreach($gradeList AS $value) : ?>
                        <td class="grade">
							<input type="number" id="percent<?php echo $value->filter; ?>" class="form-control" min="0" max="100" step="1" value="<?php echo $json[$value->filter]; ?>">
						</td>
       
                    <?php endforeach; ?>
                </tr>
				<tr id="allroutes">
                    <td  width="110px"><?php echo Text::_('COM_ACT_NUMBER_ROUTES'); ?></td> 
                    <?php foreach($gradeList AS $value) : ?>
                        <td>
						<input type="text" id="routes_grade<?php echo $value->filter; ?>" class="form-control" name="routes_grade<?php echo $value->grade; ?>" value="" readonly>
						</td>
                    <?php endforeach; ?>

                </tr>
				<tr>
                    <td><?php echo Text::_('COM_ACT_FULFILLMENT'); ?></td>
                    <td colspan="11">
                        <div class="progress">
                            <span class="sr-only"><input type="number" name="jform[percent]" id="jform_percent" value="" max="100" step="1"></span>
                            <div id="progress" class="progress-bar progress-bar-striped" role="progressbar" style=""></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
	<input type="hidden" id="percentsoll" name="jform[percentsoll]" value="" />
	<input type="hidden" id="routessoll" name="jform[routessoll]" value="" />
