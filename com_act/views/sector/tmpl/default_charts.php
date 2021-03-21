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

$doc = Factory::getDocument();
$doc->addScript('node_modules/chart.js/dist/Chart.bundle.min.js');
$doc->addScript('node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js');
$doc->addScript('node_modules/chartjs-plugin-labels/build/chartjs-plugin-labels.min.js');


// Erstelle Variablen soll10 usw => Sollwert (Alle einzeln 3, 3+, 4- usw)
for($i = 10; $i <= 36; $i++) {
    $soll = "soll$i";
    $varname = 'soll';
    ${$varname.$i} = $this->item->$soll;
  }

// Erstelle Variablen für Gesamtgrad (3.Grad, 4.Grad usw)
$soll_grade_3  = ($soll10 + $soll11);              // 3
$soll_grade_4  = ($soll12 + $soll13 + $soll14);    // 4
$soll_grade_5  = ($soll15 + $soll16 + $soll17 );	 // 5
$soll_grade_6  = ($soll18 + $soll19 + $soll20 );	 // 6
$soll_grade_7  = ($soll21 + $soll22 + $soll23 );	 // 7
$soll_grade_8  = ($soll24 + $soll25 + $soll26 );	 // 8
$soll_grade_9  = ($soll27 + $soll28 + $soll29 );	 // 9
$soll_grade_10 = ($soll30 + $soll31 + $soll32 );	 // 10
$soll_grade_11 = ($soll33 + $soll34 + $soll35 );	 // 11
$soll_grade_12 = ($soll36);	                       // 12

// Farben
$color_3  = '#a001f2'; // TODO CONFIG
$color_4  = '#ffc600';
$color_5  = '#a86301';
$color_6  = '#fa3a07';
$color_7  = '#98c920';
$color_8  = '#019abc';
$color_9  = '#a001f2';
$color_10 = '#2a82cd';
$color_11 = '#ff00ff';
$color_12 = '#ffc600';

// Label für Grad
$label_3 = '3.Grad';  // TODO Sprache
$label_4 = '4.Grad';
$label_5 = '5.Grad';
$label_6 = '6.Grad';
$label_7 = '7.Grad';
$label_8 = '8.Grad';
$label_9 = '9.Grad';
$label_10 = '10.Grad';
$label_11 = '11.Grad';
$label_12 = '12.Grad';

// Ist Werte für Chart
$soll_label = '';
$soll_color = '';
$soll_data = ''; 

for ($i = 3; $i <= 12; $i++) {
 
  $soll_grade = "soll_grade_$i";
  $color = "color_$i";
  $label = "label_$i";

  if ($$soll_grade != 0) {
    $soll_data .= $$soll_grade . ',';
    $soll_color .= '"'.$$color.'",';
    $soll_label .= '"'.$$label.'",';
   } 

}

?>

<?php if ($soll_data != '') : ?>
  <canvas id="sollChart" width="" height="130"></canvas>

  <script>
  Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
    align: 'end',
    anchor: 'end',
    color: '#555',
    offset: 0,
    margin: 30,
    font: {
      size: 14,
      weight: 'bold'
    },
  });


  var canvas = document.getElementById('sollChart');
  new Chart(canvas, {
    type: 'doughnut',    
    data: {
      labels: [<?php echo $soll_label; ?>],
      datasets: [{
        data: [<?php echo $soll_data; ?>],
        backgroundColor: [<?php echo $soll_color; ?> ]
      }]
    },

    // Abstand von Legend nach unten 3.Grade ...
    plugins: [{
      beforeInit: function(chart, options) {
        chart.legend.afterFit = function() {
          this.height = this.height + 30;
        };
      }
    }],

    options: {
      legend: {
        display: true,
        position: 'top',
        padding: 0,
        labels: {
        fontSize: 14,
        }
      },
      // Option animation time
      animation: {
        duration: 0 
      },
      // Semi
      rotation: -Math.PI,
      cutoutPercentage: 35,
      circumference: Math.PI,
      responsive: true,
      maintainAspectRatio: true,
      
      plugins: {
        labels: {
          fontColor: 'black',
          fontSize: 15,
          precision: 2
        },
      },
    }

  });
  </script>

<?php else : ?>
  <p>Keine Sollwerte erfasst.</p>
<?php endif; ?>