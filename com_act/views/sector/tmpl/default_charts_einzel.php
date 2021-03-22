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

$json_routes = json_encode([$this->item->soll10,$this->item->soll11,$this->item->soll12,$this->item->soll13,$this->item->soll14,$this->item->soll15,$this->item->soll16,$this->item->soll17,
                            $this->item->soll18,$this->item->soll19,$this->item->soll20,$this->item->soll21,$this->item->soll22,$this->item->soll23,$this->item->soll24,$this->item->soll25,
                            $this->item->soll26,$this->item->soll27,$this->item->soll28,$this->item->soll29,$this->item->soll30,$this->item->soll31,$this->item->soll32,$this->item->soll33,
                            $this->item->soll34,$this->item->soll35 
                            ]);


// Erstelle den JSON-String für die Farben
$colors = json_encode([$this->c3,$this->c3,$this->c4,$this->c4,$this->c4,$this->c5,$this->c5,$this->c5,$this->c6,$this->c6,$this->c6,
                      $this->c7,$this->c7,$this->c7,$this->c8,$this->c8,$this->c8,$this->c9,$this->c9,$this->c9,$this->c10,$this->c10,
                      $this->c10,$this->c11,$this->c11,$this->c11,$this->c12
                      ]);


// Label JSON [3,3-,3,3+ usw]
$label = [];
for($i = 10; $i <= 36; $i++) {
  array_push($label, ActHelpersAct::uiaa($i));
};
$json_label = json_encode($label);

?>

    <div class="row mt-5">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart" height="80"></canvas>
                    <div class="text-center mt-2">Routenanzahl xxx -  Routendichte: 2.5</div>
                </div>
            </div>
        </div> 
    </div>


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