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

// Add Script 
$doc = Factory::getDocument();
$doc->addScript('media/com_routes_planning/js/einzel.js', true, true); 

$grade_start = $this->grade_start_individually;
$grade_end = $this->grade_end_individually;

$json = json_decode($this->item->routessoll_ind, true); 

// Erstelle den JSON-String für die Farben
$this->c10 = $this->c3;
$this->c11 = $this->c3;
$this->c12 = $this->c4;
$this->c13 = $this->c4;
$this->c14 = $this->c4;
$this->c15 = $this->c5;
$this->c16 = $this->c5;
$this->c17 = $this->c5;
$this->c18 = $this->c6;
$this->c19 = $this->c6;
$this->c20 = $this->c6;
$this->c21 = $this->c7;
$this->c22 = $this->c7;
$this->c23 = $this->c7;
$this->c24 = $this->c8;
$this->c25 = $this->c8;
$this->c26 = $this->c8;
$this->c27 = $this->c9;
$this->c28 = $this->c9;
$this->c29 = $this->c9;
$this->c30 = $this->c10;
$this->c31 = $this->c10;
$this->c32 = $this->c10;
$this->c33 = $this->c11;
$this->c34 = $this->c11;
$this->c35 = $this->c11;
$this->c36 = $this->c12;

?>

    <h3 class="mt-5">Soll-Verteilung SW</h3>
        <div class="">Gesamt Routenanzahl: <span id="total"><?php //Summer wird per Javascript berechnet; ?></span></div>
        <div>Gesamtzahl Linie: <span id="total_lines"><?php echo $this->total_lines_in_sektor; ?></span></div>
        <div>Routendichte: <span  id="density"></span></div>
        
          
    <div id="gradetable" class="table-responsive mt-4">
        <table class="table table-bordered text-center" id="datatable">
            <thead>
                <tr id="gradelabel">
                <?php for ($i = $grade_start; $i <= $grade_end; $i++) : ?>
                     <?php $color = "c$i";
                           $varname = 'color';
                           ${$varname.$i} = $color; 
                     ?>
                     
					<td class="grade">
						<label id="gradelbl<?php echo $i; ?>" class="lblg grade<?php echo $i; ?>" style="border-color:<?php echo $this->$color; ?>"><?php echo ActHelpersAct::uiaa($i); ?>
                        </label>
					</td>
					<?php endfor; ?>
                </tr>
            </thead>
            <tbody>
            <tr id="soll_value"> 
                <?php for ($i = $grade_start; $i <=$grade_end; $i++) : ?>
						<td class="grade">
                            <?php  $grade = "g$i"; ?>
						    <input type="number" id="routesoll<?php echo $i; ?>" class="form-control" min="0" max="" step="1" value="<?php echo $json[$grade]; ?>">
						</td>
					<?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>
	<input type="hidden" id="routessoll_ind" name="jform[routessoll_ind]" value="" />
