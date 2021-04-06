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


use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$canEdit = Factory::getUser()->authorise('core.edit', 'com_act');

// Routes-Planning Params
$app = Factory::getApplication();
$params = $app->getParams();
$params         = $app->getParams('com_routes_planning');
$berechnungsart = $params['berechnungsart'];

if ((1 == $this->record_should) && (2 == $this->record_sector_or_building)) { // Wenn Sollerfassung innerhalb Sektoren
    $doc = Factory::getDocument();
    $doc->addScript('node_modules/chart.js/dist/Chart.bundle.min.js');
    $doc->addScript('node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js');
};

?>

    <?php // Page-Header ?>
    <div class="page-header">
        <h1><?php echo  $this->item->sector; ?></h1> 
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">   
                    <h3>Info</h3>
                </div>
                <div class="card-body">
                <?php echo ActHelpersAct::getBuildingName($this->item->building); ?>
                    <?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_'.$this->item->inorout); ?>
                    <div>Intervall: <?php echo $this->item->maintenance_interval; ?> Woche(n)</div>
                    <div>Nächste Wartung: xxx</div> <?php // TODO ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">   
                    <h3>Linien im Sektor</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <?php foreach($this->lines AS $line) : ?>
                            <div class="pr-2"><?php echo $line->line; ?> | </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2">Gesamt <?php echo count($this->lines); ?> Linie</div>
                </div>
            </div>
        </div>
    </div>

    <?php if ((1 == $this->record_should) && (2 == $this->record_sector_or_building))  :  ?> <?php // Gebäude=1 / Sektor=2 ?>
         <?php  if(0 == $this->record_type) { // Einzelwert (0) oder Prozente (1)?
                   echo $this->loadTemplate('charts_einzel');
                    
                } else {
                    echo $this->loadTemplate('charts_prozent');
                }; ?>
    <?php endif; ?>

    <div class="mt-4">
        <?php if($canEdit): ?>
            <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_act&task=sector.edit&id='.$this->item->id); ?>">
                <?php echo Text::_("COM_ACT_SECTORS_EDIT_ITEM_TITLE"); ?>
            </a>
        <?php endif; ?>
        <a class="btn btn-warning" href="<?php echo Route::_('index.php?option=com_act&task=sectors') ?>">
            <?php echo Text::_("COM_ACT_SECTORS"); ?>
        </a>
    </div>

