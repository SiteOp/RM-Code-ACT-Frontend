<?php
/**
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später
 */
// No direct access
defined('_JEXEC') or die;

/*
 * Zeitleiste über Helper             =  ActHelpersAct::Timeline()
 * Erstes Dataset (Kurve) über Helper = ActHelpersAct::linesDataset($this->routes_total[0])
 * Zweites Dataset(Kurve)             = $ascent auch über die Helper
*/
$ascent = ActHelpersAct::linesDataset($this->CommentsFilter[0]);


// Filter User
if ($this->state->get('filter.user') != '')
{
    $var = "{data: [".$ascent." ],
                 label: 'Filter ' + '" .array_sum($this->CommentsFilter[0])."',
                 borderColor: 'orange',
                 fill: false
                 },";
}
else {
    $var = ''; // Die Variable muss erst auf null gesetzt werden sonst lädt die Grafik beim erstenmal nicht (Zum testen Browser Cookies leeren!)
}

// Filter Stars 
if ($this->state->get('filter.stars') != '')
{
    $var = "{data: [".$ascent." ],
                 label: 'Filter ' + '" .array_sum($this->CommentsFilter[0])."',
                 borderColor: 'orange',
                 fill: false
                 },";
}

// Filter  Suchfeld 
 if ($this->state->get('filter.search') != '')
{
    $var = "{data: [".$ascent." ],
                 label: 'Filter ' + '" .array_sum($this->CommentsFilter[0])."',
                 borderColor: 'orange',
                 fill: false
                 },";
}

// Filter  Suchfeld 
 if ($this->state->get('filter.input') != '')
{
    $var = "{data: [".$ascent." ],
                 label: 'Filter ' + '" .array_sum($this->CommentsFilter[0])."',
                 borderColor: 'orange',
                 fill: false
                 },";
}
?>

<?php // Charts.js ?>
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
        data: [<?php echo ActHelpersAct::linesDataset($this->CommentsTotal[0]); ?>],
        label: 'Total ' + <?php echo array_sum($this->CommentsTotal[0]); ?>,
        borderColor: '#46ac34',
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