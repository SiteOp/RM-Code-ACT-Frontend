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
$doc->addScript('node_modules/chart.js/dist/Chart.bundle.min.js');
$doc->addScript('node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js');

$sum_routes = [];
for($i = 10; $i <= 36; $i++) {
    $soll = "soll$i";
    $varname = 'soll';
    ${$varname.$i} = $this->item->$soll;
    array_push($sum_routes,  $this->item->$soll);
  }

// Erstelle Variablen für Gesamtgrad (3.Grad, 4.Grad usw)
$grade_3  = ($soll10 + $soll11);             // 3
$grade_4  = ($soll12 + $soll13 + $soll14);   // 4
$grade_5  = ($soll15 + $soll16 + $soll17 );	 // 5
$grade_6  = ($soll18 + $soll19 + $soll20 );	 // 6
$grade_7  = ($soll21 + $soll22 + $soll23 );	 // 7
$grade_8  = ($soll24 + $soll25 + $soll26 );	 // 8
$grade_9  = ($soll27 + $soll28 + $soll29 );	 // 9
$grade_10 = ($soll30 + $soll31 + $soll32 );	 // 10
$grade_11 = ($soll33 + $soll34 + $soll35 );	 // 11
$grade_12 = ($soll36);	                     // 12
?>

<script>
    // Berechne die gesamte Routenanzahl besser mit Javascript nicht PHP 
    // Beim erneuten Laden der Seite ist somit der Wert ebenfalls richtig
   $(document).ready(function () {

        let sum =  $('#gradetable .grade').sum(); // Gesamtzahl Routen
        $('#total').html(sum);

        let total_lines = $('#total_lines').text(); // Anzahl Linien
        let density = parseFloat((sum / total_lines).toFixed(2)); // Routendichte 
        $('#density').html(density);

       // let total_max_routes = $('#total_max_routes').text();
       // if(sum > total_max_routes) {
        //    $('#warning_to_much').html('Achtung max. Anzahl Routen wurde überschritten').addClass('alert alert-danger mt-4');
      //  };

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

    var config = {
        type: 'bar',
        data: {
            labels: ['3-',3,4,5,6,7,8,9,10,11,12],
            datasets: [{
            data: [<?php echo $soll10.','.$grade_3.','.$grade_4.','.$grade_5.','.$grade_6.','.$grade_7.','.$grade_8.','.$grade_9.','.$grade_10.','.$grade_11.','.$grade_12; ?>],
            backgroundColor: ["#a001f2", "#ffc600", "#a86301", "#fa3a07","#98c920","#019abc","#a001f2", "#2a82cd", "#ff00ff", "#ffc600"]
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
                }],
                xAxes: [{
                    ticks: {
                        callback: function(value, index, values) {
                            return  value + '.Grad';
                        }
                    }
                }]
            }
        }
    };

    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, config);

    // Funktion zum berechnen der Summe der einzelnen Grade
    (function( $ ){ 
        $.fn.sum=function () {
            let sum=0;
            $(this).each(function(index, element){
                if($(element).val()!="")
                    sum += parseFloat($(element).val());
            });
            return sum;
        }; 
    })( jQuery )

   
    // Die Daten (Angabe Summe der einzelnen Grade) muss nach der Eingabe neu erstellt werden
    // Nur diese Daten werden dann als Update in das Chart eingespielt
    function updateData(){
        let grade3 =  $('#gradetable .grade3').sum();
        let grade4 =  $('#gradetable .grade4').sum();
        let grade5 =  $('#gradetable .grade5').sum();
        let grade6 =  $('#gradetable .grade6').sum();
        let grade7 =  $('#gradetable .grade7').sum();
        let grade8 =  $('#gradetable .grade8').sum();
        let grade9 =  $('#gradetable .grade9').sum();
        let grade10 = $('#gradetable .grade10').sum();
        let grade11 = $('#gradetable .grade11').sum();
        let grade12 = $('#gradetable .grade12').sum();
        let dataObj =  JSON.parse("["+grade3+','+grade4+','+grade5+','+grade6+','+grade7+','+grade8+','+grade9+','+grade10+','+grade11+','+grade12+"]");   
        let newData=[];
        newData = dataObj;
        newData.push()
        myChart.data.datasets[0].data =newData;
        myChart.update();
    };

    // Wenn sich in der Tabelle etwas ändern dann
    $('#gradetable').change(function() {    

        let sum =  $('#gradetable .grade').sum(); // Gesamtzahl aller Routen berechnen
        $('#total').html(sum);  // Gesamtzahl in ausgeben

        let total_lines = $('#total_lines').text();
        let density = parseFloat((sum / total_lines).toFixed(2));
        $('#density').html(density);

        let total_max_routes = $('#total_max_routes').text();
    
        console.log(sum);
        
       // if ((sum > total_max_routes)) 
      //  {
       //     $('#warning_to_much').html('Achtung').addClass('alert alert-danger mt-4'); 
       // }
       // else
        //{
        //    $('#warning_to_much').empty().removeClass();
       // }
        
        
        
        updateData();   // Funktion Update der Grade durchführen
    });


    // Enter sendet das Formular nicht ab sondern geht in der Tabreihenfolge weiter 
    $(document).on("keypress", ":input:not(textarea):not([type=submit])", function(event) {
        if (event.keyCode == 13) {
            var fields = $(this).closest("#gradetable").find("input ");
            var index = fields.index(this) + 1;

            fields.eq(
                fields.length <= index
                    ? 0
                    : index
            ).focus();
            event.preventDefault();
        }
    });
</script>