<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

// ACT Params 
$params      	= JComponentHelper::getParams( 'com_act' );
$primaryline    = $params['primaryline'];
$secondaryline  = $params['secondaryline'];


/*
 * Zeitleiste über Helper             =  ActHelpersAct::Timeline()
 * Erstes Dataset (Kurve) über Helper = ActHelpersAct::linesDataset($this->routes_total[0])
 * Zweites Dataset(Kurve)             = $ascent auch über die Helper
*/
$ascent = ActHelpersAct::linesDataset($this->routes_ascent[0]);

// Filter - Charts Data
switch($this->state->get('filter.ascent'))
{
    case '':
        $var = '';
        break;
    case 1:
        $var = "{data: [".$ascent."],
                 label: 'Flash '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '" . $secondaryline . "',
                 fill: false
                 },";
        break;
    case 2:
        $var = "{data: [".$ascent."],
                 label: 'Onsight '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '" . $secondaryline . "',
                 fill: false
                 },";
        break;    
    case 3:
        $var = "{data: [".$ascent."],
                 label: 'Lead ' + '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '" . $secondaryline . "',
                 fill: false
                 },";
        break;
    case 4:
        $var = "{data: [".$ascent."],
                 label: 'Toprobe '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '" . $secondaryline . "',
                 fill: false
                 },";
        break;
    case 5:
        $var = "{data: [".$ascent."],
                 label: 'Projekt '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '" . $secondaryline . "',
                 fill: false
                 },";
        break;
    case 6:
        $var = "{data: [".$ascent."],
                 label: 'Automat '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '" . $secondaryline . "',
                 fill: false
                 },";
        break;
    default:
          echo '';  
          break;
}
?>

<script>
Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
  align: 'end',
  anchor: 'end',
  font: {
    size: 15,
    weight: 700
  },
});
</script>
<script>
new Chart(document.getElementById("chart"), {
    type: 'line',
    data: {
      labels: [<?php echo ActHelpersAct::Timeline(); ?>],
       datasets: [{ 
        data: [<?php echo ActHelpersAct::linesDataset($this->routes_total[0]); ?>],
        label: 'Total ' + <?php echo array_sum($this->routes_total[0]); ?>,
        borderColor:  '<?php echo $primaryline; ?>',
        fill: false
      },
      <?php echo $var; ?>
    ]
  },  
       options: {
            animation: {
                duration: 0 // general animation time
            },
            legend: {
                display: true,
                labels: {
                    fontSize: 15,
                }
            }
        },
});
</script>