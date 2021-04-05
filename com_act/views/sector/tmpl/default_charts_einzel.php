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

$grade_start = $this->grade_start_individually;
$grade_end = $this->grade_end_individually;


if (!empty($this->item->routessoll_ind)) {
    $jsonroutes = json_decode($this->item->routessoll_ind, true); // Hole die Werte aus DB um die Inputfelder vorab zu füllen

    $routes_array = [];
    for($i = $grade_start; $i <= $grade_end; $i++) {
        $grade = "g$i";
        array_push($routes_array, $jsonroutes[$grade] );
    }


    $total_routes = array_sum($routes_array);         // Gesamtzahl Routen
    $total_lines = count($this->lines);               // Gesamtzahl Linien
    $density = round($total_routes/$total_lines, 2);  // Routendichte
    $json_routes = json_encode($routes_array);        // JSON-String für Charts


    // Erstelle den JSON-String für die Farben
    $colors = json_encode([$this->c3,$this->c3,$this->c4,$this->c4,$this->c4,$this->c5,$this->c5,$this->c5,$this->c6,$this->c6,$this->c6,
                        $this->c7,$this->c7,$this->c7,$this->c8,$this->c8,$this->c8,$this->c9,$this->c9,$this->c9,$this->c10,$this->c10,
                        $this->c10,$this->c11,$this->c11,$this->c11,$this->c12
                        ]);


    // Label JSON [3,3-,3,3+ usw]
    $label = [];
    for($i = $grade_start; $i <= $grade_end; $i++) {
    array_push($label, ActHelpersAct::uiaa($i));
    };
    $json_label = json_encode($label);

    $json = json_decode($this->item->routessoll_ind, true);
    for($i = $grade_start; $i <= $grade_end; $i++) {
    };
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
            labels: <?php echo $json_label; ?>,
            datasets: [{
                data: <?php echo $json_routes; ?>,
                backgroundColor: <?php echo $colors; ?>,
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