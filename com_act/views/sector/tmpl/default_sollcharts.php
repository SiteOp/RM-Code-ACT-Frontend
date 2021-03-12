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

?>

<canvas id="bar-chart-grouped" width="100%"></canvas>


<script>
new Chart(document.getElementById("bar-chart-grouped"), {
    type: 'horizontalBar',
    data: {
      labels: ["3", "3+", "4", "4-", "4", "4+", "5-", "5", "5", "5+", "6-", "6", "6+",],
      datasets: [
        {
          label: "Soll",
          backgroundColor: "#3e95cd",
          data: [0,2,3,2,2,1,0,1,2,1]
        }, {
          label: "Europe",
          backgroundColor: "#8e5ea2",
          data: [1,0,0,0,1,2,0,1,2,0]
        }
      ]
    },
    options: {
      title: {
        display: false,
        text: 'Population growth (millions)'
      }
    }
});



</script>