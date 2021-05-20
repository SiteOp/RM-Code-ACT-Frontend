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

if (!empty($this->item->routessoll)) {
  $json = json_decode($this->item->routessoll, true);
  // Erstelle Variablen für Gesamtgrad (3.Grad, 4.Grad usw)
  $soll_grade_3  = $json['g3'];  // 3
  $soll_grade_4  = $json['g4'];  // 4
  $soll_grade_5  = $json['g5'];	 // 5
  $soll_grade_6  = $json['g6'];	 // 6
  $soll_grade_7  = $json['g7'];	 // 7
  $soll_grade_8  = $json['g8'];	 // 8
  $soll_grade_9  = $json['g9'];	 // 9
  $soll_grade_10 = $json['g10']; // 10
  $soll_grade_11 = $json['g11']; // 11
  $soll_grade_12 = $json['g12']; // 12
  $soll_grade_13 = $json['g13']; // undefiniert
  $soll_grade_array = [$soll_grade_3,$soll_grade_4,$soll_grade_5,$soll_grade_6,$soll_grade_7,$soll_grade_8,$soll_grade_9,
                       $soll_grade_10,$soll_grade_11,$soll_grade_12];
  // Farben
  $color_3  = $this->c3; 
  $color_4  = $this->c4;
  $color_5  = $this->c5;
  $color_6  = $this->c6;
  $color_7  = $this->c7;
  $color_8  = $this->c8;
  $color_9  = $this->c9;
  $color_10 = $this->c10;
  $color_11 = $this->c11;
  $color_12 = $this->c12;
  $color_13 = $this->c13;
  // Label für Grad
  $label_13 = 'undefiniert';
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

  for ($i = 3; $i <= 13; $i++) {
  
    $soll_grade = "soll_grade_$i";
    $color = "color_$i";
    $label = "label_$i";

    if ($$soll_grade != 0) {
      $soll_data .= $$soll_grade . ',';
      $soll_color .= '"'.$$color.'",';
      $soll_label .= '"'.$$label.'",';
    } 
  }
} else {
  $soll_data = '';
}

?>

<?php if ($soll_data != '') : ?>


  <div class="row mt-5">
        <div class="col">
            <div class="card">
                <div class="card-body">
                <canvas id="sollChart" width="" height="90"></canvas>
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
          precision: 0
        },
      },
    }

  });
  </script>

<?php else : ?>
  <p><?php echo Text::_('COM_ACT_BUILDING_NO_SHOULD_VALUES'); ?></p>
<?php endif; ?>