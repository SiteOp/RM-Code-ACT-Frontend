<?php
/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Add Script 
$doc = Factory::getDocument();
$doc->addScript('media/com_routes_planning/js/einzel.js', true, true); // Wichtig um die Datenreihen JSON für den Import in die Datenbank zu erstellen

// Helper Grade aus ACT Grade.php
JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');

$gradeList   = GradeHelpersGrade::getGradeListPlanning(); // JSON String der Grade
$color_array = json_decode(GradeHelpersGrade::getGradeColorList());  // JSON String der Farben

$json = json_decode($this->item->routessoll_ind, true); 

?>

    <h3 class="mt-5"><?php echo Text::_('COM_ACT_SHOULD_DISTRIBUTED'); ?></h3>
    <div class=""><?php echo Text::_('COM_ACT_TOTAL_ROUTES'); ?>: <span id="total"><?php //Summer wird per Javascript berechnet; ?></span></div>
    <div><?php echo Text::_('COM_ACT_TOTAL_LINES'); ?>: <span id="total_lines"><?php echo $this->total_lines_in_sektor; ?></span></div>
    <div><?php echo Text::_('COM_ACT_ROUTES_DENSITY'); ?>: <span  id="density"></span></div>
        
    <div id="gradetable" class="table-responsive mt-4">
        <table class="table table-bordered text-center" id="datatable">
            <thead>
                <tr id="gradelabel">
                <?php $i = 0; ?> <?php // Zähler für Farben ?>
                <?php foreach($gradeList AS $value) : ?>
                    <td class="grade">
                        <label id="gradelbl<?php echo $value->id_grade; ?>" class="lblg grade<?php echo $value->id_grade; ?>" style="border-color:<?php echo $color_array[$i]; ?>">
                            <?php echo $value->grade; ?>
                        </label>
                    </td>
                <?php $i++; ?>
				<?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr id="soll_value"> 
                    <?php foreach($gradeList AS $value) : ?>
                        <td class="grade">
                            <?php  $grade = "g$value->id_grade"; ?>
                            <input type="number" id="routesoll<?php echo $value->id_grade; ?>" class="form-control" min="0" max="" step="1" value="<?php echo $json[$grade]; ?>">
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
	<input type="hidden" id="routessoll_ind" name="jform[routessoll_ind]" value="" />
