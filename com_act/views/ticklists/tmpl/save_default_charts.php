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

// Erstelle CSV-Liste aus dem Array
$total  = implode(',', $this->routes_total[0]);
$ascent = implode(',', $this->routes_ascent[0]);

// Datumbereiche festlegen
$currentmonth = date("m");
$year = date("y");
$lastyear = $year -1; 
$monate = array(1=>"01/",2=>"02/",3=>"03/",4=>"04/",5=>"05/",6=>"06/",7=>"07/",8=>"08/",9=>"09/",10=>"10/",11=>"11/",12=>"12/");

// Filter - Charts Data
switch($this->state->get('filter.ascent'))
{
    case '':
        $var = '';
        break;
    case 1:
        $var = "{data: [".$ascent."],
                 label: 'Flash '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '#46ac34',
                 fill: false
                 },";
        break;
    case 2:
        $var = "{data: [".$ascent."],
                 label: 'Onsight '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '#019abc',
                 fill: false
                 },";
        break;    
    case 3:
        $var = "{data: [".$ascent."],
                 label: 'Lead ' + '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '#46ac34',
                 fill: false
                 },";
        break;
    case 4:
        $var = "{data: [".$ascent."],
                 label: 'Toprobe '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '#990000',
                 fill: false
                 },";
        break;
    case 5:
        $var = "{data: [".$ascent."],
                 label: 'Projekt '+ '" .array_sum($this->routes_ascent[0])."',
                 borderColor: '#3e95cd',
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
      labels: [<?php foreach ($monate AS $key=>$value) : ?>
                    <?php if ($key <= $currentmonth) : ?>
                        <?php echo '"'.$value . $year.'",'; ?>
                    <?php else : ?>
                        <?php echo '"'.$value . $lastyear.'",'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>],
       datasets: [{ 
        data: [<?php echo $total; ?>],
        label: 'Total ' + <?php echo array_sum($this->routes_total[0]); ?>,
        borderColor: "orange",
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

