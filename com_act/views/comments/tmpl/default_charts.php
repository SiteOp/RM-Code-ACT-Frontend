<?php
/**
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri as JUri;

// ACT Params 
$params        = JComponentHelper::getParams('com_act');
$primaryline   = $params->get('primaryline', '#0393ad');
$secondaryline = $params->get('secondaryline', '#fab903');

// Daten aus dem Model - kommen bereits chronologisch sortiert
$totalDataArray  = array_values(array_map('intval', $this->CommentsTotal[0]));
$filterDataArray = array_values(array_map('intval', $this->CommentsFilter[0]));
$totalSum        = array_sum($totalDataArray);
$filterSum       = array_sum($filterDataArray);

// Timeline-Labels direkt aus PHP generieren (korrekt sortiert, ohne ob_start)
$timelineArray = [];
for ($i = 11; $i >= 0; $i--)
{
    $timelineArray[] = date('m/y', strtotime("-{$i} months"));
}

// Prüfe aktive Filter
$activeFilter = '';
$hasFilter    = false;

$filterStates = [
    'filter.user'   => 'Benutzer',
    'filter.stars'  => 'Bewertung',
    'filter.search' => 'Suche',
    'filter.input'  => 'Eingabegerät',
];

foreach ($filterStates as $filterKey => $filterName)
{
    if ($this->state->get($filterKey) != '')
    {
        $activeFilter = $filterName;
        $hasFilter    = true;
        break;
    }
}

$showFilter = $hasFilter && ($totalDataArray !== $filterDataArray);

// Chart-Daten für JavaScript
$chartData = [
    'labels'         => $timelineArray,
    'totalData'      => $totalDataArray,
    'totalSum'       => $totalSum,
    'filterData'     => $filterDataArray,
    'filterSum'      => $filterSum,
    'filterLabel'    => $activeFilter,
    'showFilter'     => $showFilter,
    'primaryColor'   => $primaryline,
    'secondaryColor' => $secondaryline,
];

// Daten als globale JavaScript-Variable ausgeben
$doc = Factory::getDocument();
$doc->addScriptDeclaration('var actCommentsChartData = ' . json_encode($chartData, JSON_UNESCAPED_SLASHES) . ';');

// Externes JavaScript laden
$doc->addScript(JUri::root() . 'components/com_act/js/comments/comments-chart.js');
?>