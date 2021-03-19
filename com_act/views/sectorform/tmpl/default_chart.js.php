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
  $json_routes = json_encode($sum_routes);


  $label = [];
  for($i = 10; $i <= 36; $i++) {
    array_push($label, ActHelpersAct::uiaa($i));
  };
$json_label = json_encode($label);

         
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
            labels: <?php echo $json_label; ?>,
            datasets: [{
            data: <?php echo $json_routes; ?>,
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
        let grade10 =  $('#jform_grade10').val();
        let grade11 =  $('#jform_grade11').val();
        let grade12 =  $('#jform_grade12').val();
        let grade13 =  $('#jform_grade13').val();
        let grade14 =  $('#jform_grade14').val();
        let grade15 =  $('#jform_grade15').val();
        let grade16 =  $('#jform_grade16').val();
        let grade17 =  $('#jform_grade17').val();
        let grade18 =  $('#jform_grade18').val();
        let grade19 =  $('#jform_grade19').val();
        let grade20 =  $('#jform_grade20').val();
        let grade21 =  $('#jform_grade21').val();
        let grade22 =  $('#jform_grade22').val();
        let grade23 =  $('#jform_grade23').val();
        let grade24 =  $('#jform_grade24').val();
        let grade25 =  $('#jform_grade25').val();
        let grade26 =  $('#jform_grade26').val();
        let grade27 =  $('#jform_grade27').val();
        let grade28 =  $('#jform_grade28').val();
        let grade29 =  $('#jform_grade29').val();
        let grade30 =  $('#jform_grade30').val();
        let grade31 =  $('#jform_grade31').val();
        let grade32 =  $('#jform_grade32').val();
        let grade33 =  $('#jform_grade33').val();
        let grade34 =  $('#jform_grade34').val();
        let grade35 =  $('#jform_grade35').val();
        let grade36 =  $('#jform_grade36').val();
        let dataObj =  JSON.parse("["+grade10+','+grade11+','+grade12+','+grade13+','+grade14+','+grade15+','+grade16+','+grade17+','+grade18+','+grade19+','+grade20+','+grade21+','+grade22+','+grade23+','+grade24+','+grade25+','+grade26+','+grade27+','+grade28+','+grade29+','+grade30+','+grade31+','+grade32+','+grade33+','+grade34+','+grade35+','+grade36+"]"); 
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