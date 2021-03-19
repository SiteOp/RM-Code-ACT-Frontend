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
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');


$user    = Factory::getUser();
$canEdit = ActHelpersAct::canUserEdit($this->item, $user);

if($this->item->state == 1){
    $state_string = 'Publish';
    $state_value = 1;
} else {
    $state_string = 'Unpublish';
    $state_value = 0;
}
$canState = Factory::getUser()->authorise('core.edit.state','com_act');

// Helper - Alle Linien in diesem Sektor (Array)
$lines = ActHelpersAct::getLinesFromSectorId($this->item->id);

$total_max_routes = 0;
foreach($lines AS $line) {
    $total_max_routes += $line->maxroutes;// Max Routenanzahl gerechnet aus allen Linien in diesem Sektor
}

$total_lines_in_sektor =  count($lines); // Anzahl Linien im Sektor
?>

<style>
.sw_soll  {text-align: center; font-weight: bold;}
.sw_soll .form-control {text-align: center; padding-left: 25px!important;}
#gradetable .form-control {padding: 0; min-width: 1.5rem; min-height: 1.5rem; text-align: center;}
#gradetable .table td {padding: 4px;}
.card-body {padding: 0;}
</style>

<?Php // Pager-Header ?>
<?php if (!$canEdit) : ?>
    <h3><?php throw new Exception(Text::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?></h3>
    <?php else : ?>
        <?php if (!empty($this->item->id)): ?>
            <div class="page-header">
                <h1><?php echo Text::sprintf('COM_ACT_SECTORS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
            </div>
        <?php else: ?>
            <div class="page-header">
                <h1><?php echo Text::_('COM_ACT_SECTORS_APPLY_ITEM_TITLE'); ?></h1>
            </div>
        <?php endif; ?>
        
    <div  id="form-edit" class="sector-edit front-end-edit">
        <form id="form-sector"
              action="<?php echo Route::_('index.php?option=com_act&task=sector.save'); ?>"
              method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

            <?php // Status mit Access ob Status bearbeitet werden darf ?>
            <div class="form-group row">
                <div class="col-md-5">
                    <?php if($canState): ?>
                       <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                       <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
                    <?php else: ?>
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?> : <?php echo $state_string; ?></div>
                        <input type="hidden" name="jform[state]" value="<?php echo $state_value; ?>" />
                    <?php endif; ?>
                </div>
            </div>
            <?php // Sector ?>
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('sector'); ?></div>
            </div>
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('inorout'); ?></div>
                <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('building'); ?></div>
            </div>

            <h3 class="mt-5">Soll-Verteilung SW</h3>
            <div class="">Gesamt Routenanzahl: <span id="total"><?php //Summer wird per Javascript berechnet; ?></span></div>
            <div> Max Anzahl Routen: <span id="total_max_routes"><?php echo $total_max_routes; ?></span></div>
            <div>Gesamtzahl Linie: <span id="total_lines"><?php echo $total_lines_in_sektor; ?></span></div>
            <div>Routendichte: <span  id="density"></span></div>
          



            <div id="gradetable" class="table-responsive mt-4">
                <table class="table table-bordered text-center" id="datatable">
                    <thead>
                        <tr>
                            <?php for($i = 10; $i <= 36; $i++) : ?>
                                <?php  $soll = "soll$i"; ?>
                                <td><?php echo $this->form->getLabel($soll); ?></td>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php for($i = 10; $i <= 36; $i++) : ?>
                                <?php  $soll = "soll$i"; ?>
                                <td><?php echo $this->form->getInput($soll); ?></td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="warning_to_much" class=""></div>

           <div class="row mt-5">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="myChart" height="80"></canvas>
                        </div>
                    </div>
                </div> 
            </div>

            <h3 class="mt-5">Wartung</h3>
            <div class="form-group row">
				<div class="col-md-5"><?php echo $this->form->renderField('maintenance_interval'); ?></div>
				<div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('first_maintenace'); ?></div>
			</div> 
           
                <div class="controls mt-1">
                    <?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-secondary mr-1">
                            <?php echo Text::_('COM_ACT_SUBMIT_SAVE'); ?>
                        </button>
                    <?php endif; ?>
                    <a class="btn btn-warning"
                        href="<?php echo Route::_('index.php?option=com_act&task=sectorform.cancel'); ?>"
                        title="<?php echo Text::_('JCANCEL'); ?>">
                            <?php echo Text::_('JCANCEL'); ?>
                    </a>
                </div>
    
            <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
            <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
            <?php echo $this->form->getInput('created_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>
            <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
            <input type="hidden" name="option" value="com_act"/>
            <input type="hidden" name="task" value="sectorform.save"/>
            
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
<?php endif; ?>

<?php echo $this->loadTemplate('chart.js'); ?>
