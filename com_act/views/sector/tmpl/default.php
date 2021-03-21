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

// Helper - Alle Linien in diesem Sektor
$lines = ActHelpersAct::getLinesFromSectorId($this->item->id);

// Totale von max Routenanzahl und tatsächliche Anzahl innerhalb der Linie
$total_max_routes = 0;
$total_ist_routes = 0;
foreach($lines AS $line) {
    $total_max_routes += $line->maxroutes;
}
                      



?>

<?php // Page-Header ?>
<div class="page-header">
    <h1><?php echo  $this->item->sector; ?></h1> 
</div>

<div class="row" id="info">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-body">
                    <?php echo Text::_('COM_ACT_SECTORS_BUILDING_OPTION_'.$this->item->building); ?>
                    <?php echo Text::_('COM_ACT_SECTORS_INOROUT_OPTION_'.$this->item->inorout); ?>
                    <div>Intervall: <?php echo $this->item->maintenance_interval; ?> Woche(n)</div>
                    <div>Nächste Wartung: xxx</div> <?php // TODO ?>
                </div>
            </div>
        </div>
</div>

<div class="row mt-5">
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Linien im Sektor</h3>
            </div>
            <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th>Linie</th>
                    <th>Routen max.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lines AS $line) : ?>
                        <tr>
                            <th><?php echo $line->line; ?></th>
                            <td><?php echo $line->maxroutes; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><?php echo count($lines); ?></td>
                        <td><?php echo $total_max_routes; ?> </td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </div>   
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-align-justify"></i> Soll-Werte Schwierigkeitsgrade</h3> <?php // TODO ?>
            </div>
            <div class="card-body">
                <?php echo $this->loadTemplate('charts'); ?>
            </div>
        </div>
    </div> 
 </div>


<?php // echo $this->loadTemplate('table'); ?>


    <?php if($canEdit): ?>
        <a class="btn btn-secondary mt-4" href="<?php echo Route::_('index.php?option=com_act&task=sector.edit&id='.$this->item->id); ?>">
            <?php echo Text::_("COM_ACT_SECTORS_EDIT_ITEM_TITLE"); ?>
        </a>
    <?php endif; ?>

