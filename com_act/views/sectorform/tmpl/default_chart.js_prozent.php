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

// Erstelle den JSON-String für die Farben
$colors = json_encode([$this->c3,$this->c4,$this->c5,$this->c6,$this->c7,$this->c8,$this->c9,$this->c10,$this->c11,$this->c12]);

// Erstelle JSON-String für Label [3,4,5, usw]
  $label = [];
  for($i = 3; $i <= 12; $i++) {
    array_push($label, $i);
  };
$json_label = json_encode($label);
?>


<script>
    // Beim ersten Laden Daten berechnen
    $(document).ready(function () {
        updateData();
    });
    // Nach einer Änderung Daten neu berechnen
    $('#gradetable, #jform_routestotalinsector').change(function() {  
        updateData();
    });

    // Defaults für Charts
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

    let config = {
        type: 'bar',
        data: {
            labels: <?php echo $json_label; ?>,
            datasets: [{
                data: [],
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
            layout: {
                padding: {left: 78}
            },
            legend: { display: false },
            animation: {duration: 0 },
            hover: { animationDuration: 0 },
            responsiveAnimationDuration: 0 ,
            scales: {
                yAxes: [{
                    ticks: {display: false},
                    scaleLabel:{
                            display: true,
                            labelString: 'Anzahl Routen',
                            fontSize: 18
                        }
                }]
               
                
            }
        }
    };

    let ctx = document.getElementById("myChart").getContext("2d");
    let myChart = new Chart(ctx, config);

    // Hole die Daten aus dem Feld welches die Anzahl Routen berechnet
    function updateData(){
        let soll3 = $('#totalsoll3').text();
        let soll4 = $('#totalsoll4').text();
        let soll5 = $('#totalsoll5').text();
        let soll6 = $('#totalsoll6').text();
        let soll7 = $('#totalsoll7').text();
        let soll8 = $('#totalsoll8').text();
        let soll9 = $('#totalsoll9').text();
        let soll10 = $('#totalsoll10').text();
        let soll11 = $('#totalsoll11').text();
        let soll12 = $('#totalsoll12').text();
        let dataObj =  JSON.parse("["+soll3+','+soll4+','+soll5+','+soll6+','+soll7+','+soll8+','+soll9+','+soll10+','+soll11+','+soll12+"]"); 
        let newData=[];
        newData = dataObj;
        newData.push()
        myChart.data.datasets[0].data =newData;
        myChart.update();
    };
</script>