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

// PARAMS Routes-Planning
$grade_start = $this->grade_start_percent; 
$grade_end = $this->grade_end_percent; 


if(!empty($this->item->id)) {                            // Nur wenn es die ID bzw. das Gebäude schon gibt
    $json = json_decode($this->item->percentsoll, true); // Hole die Werte aus DB um die Inputfelder vorab zu füllen
	$total_lines = count($this->lines);
} else {
	$total_lines = 0;
}
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
                <?php for ($i = $grade_start; $i <= $grade_end; $i++) : ?>
                     <?php $color = "c$i";
                           $varname = 'color';
                           ${$varname.$i} = $this->$color; 
                     ?>
					<td class="grade">
						<label id="gradelbl<?php echo $i; ?>" class="lblg grade<?php echo $i; ?>" style="border-color:<?php echo $this->$color; ?>"><?php echo $i; ?></label>
					</td>
					<?php endfor; ?>
                </tr>
            </thead>
            <tbody>
			<tr id="percent_val"> 
                    <td><?php echo Text::_('COM_ACT_PERCENT'); ?></td>   
					<?php for ($i = $grade_start; $i <=$grade_end; $i++) : ?>
						<td class="grade">
							<input type="number" id="percent<?php echo $i; ?>" class="form-control" min="0" max="100" step="1" value="<?php echo $json[$i]; ?>">
						</td>
					<?php endfor; ?>
                </tr>
				<tr id="allroutes">
                    <td  width="110px"><?php echo Text::_('COM_ACT_NUMBER_ROUTES'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <td>
						<input type="text" id="routes_grade<?php echo $i; ?>" class="form-control" name="routes_grade<?php echo $i; ?>" value="" readonly>
						</td>
                    <?php endfor; ?>
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
