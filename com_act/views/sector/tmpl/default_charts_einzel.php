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

// Helper Grade aus ACT
JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');

// Liste der Schwierigkeitsgrade aus der jeweiligen Tabelle UIAA/Franz usw
$gradeList     = GradeHelpersGrade::getGradeListPlanning(); // JSON String der Grade 
$jsonColorList = GradeHelpersGrade::getGradeColorList();  // JSON String der Farben
$jsonLabelList = GradeHelpersGrade::getGradeLabelList();  // JSON String der Farben

if (!empty($this->item->routessoll_ind)) {
    $jsonroutes = json_decode($this->item->routessoll_ind, true); // Hole die Werte aus DB um die Inputfelder vorab zu füllen


    // Array - Anzahl der Routen pro Grad
    $routes_array = [];
    foreach($gradeList AS $value) {
        $grade = "g$value->id_grade";
        array_push($routes_array, $jsonroutes[$grade] );
    }

    $total_routes = array_sum($routes_array);             // Gesamtzahl Routen
    $total_lines  = count($this->lines);                  // Gesamtzahl Linien
    $density      = round($total_routes/$total_lines, 2); // Routendichte
    $json_routes  = json_encode($routes_array);           // JSON-String für Charts

};

?>
<?php if (!empty($this->item->routessoll_ind)) : ?>
    <div class="row mt-5">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart" height="80"></canvas>
                    <div class="text-center mt-2">Routenanzahl <?php echo $total_routes; ?> -  Routendichte: <?php echo $density; ?></div>
                </div>
            </div>
        </div> 
    </div>
<?php endif; ?>

<script>
Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
  align: 'end',
  anchor: 'end',
  color: '#555',
  offset: 0,
  font: {
    size: 16,
    weight: 'bold'
  },
  
});

new Chart(document.getElementById("myChart"), {
    type: 'bar',
        data: {
            labels: <?php echo $jsonLabelList; ?>,
            datasets: [{
                data: <?php echo $json_routes; ?>,
                backgroundColor: <?php echo $jsonColorList; ?>,
            }]
        },
        // Abstand von Legend nach unten 3.Grade ...
        plugins: [{
            beforeInit: function(chart, options) {
             chart.legend.afterFit = function() {this.height = this.height + 20;};
            }
        }],
        options: {
            legend: { display: false },
            animation: {duration: 0 },
            hover: { animationDuration: 0 },
            responsiveAnimationDuration: 0 ,
            scales: {
                yAxes: [{
                    ticks: {display: false}
                }]
                
            }
        }
    });
</script>