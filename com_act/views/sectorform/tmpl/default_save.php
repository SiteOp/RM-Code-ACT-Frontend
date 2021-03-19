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

$doc = Factory::getDocument();
//$doc->addScript(Uri::base() . '/media/com_act/js/form.js');

// Add Script 
$doc->addScript('node_modules/chart.js/dist/Chart.bundle.min.js');
$doc->addScript('node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js');

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

$sum_routes = [];
for($i = 10; $i <= 36; $i++) {
    $soll = "soll$i";
    $varname = 'soll';
    ${$varname.$i} = $this->item->$soll;
    array_push($sum_routes,  $this->item->$soll);
  }

  $sum_routes = array_sum($sum_routes);




// Helper - Alle Linien in diesem Sektor
$lines = ActHelpersAct::getLinesFromSectorId($this->item->id);

// Max Routenanzahl gerechnet aus allen Linien in diesem Sektor
$total_max_routes = 0;
foreach($lines AS $line) {
    $total_max_routes += $line->maxroutes;
}

?>

<style>
.sw_soll  {text-align: center; font-weight: bold;}
.sw_soll .form-control {text-align: center; padding-left: 25px!important;}
#gradetable .form-control {padding: 0; min-width: 1.5rem; min-height: 1.5rem; text-align: center;}
#gradetable .table td {padding: 4px;}
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
            <div id="gradetable" class="table-responsive">
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

          <div class="mt-4">Gesamt Routenanzahl: <span class="total"></span></div>
           <div> Max Anzahl Routen:<?php echo $total_max_routes; ?>



           <div class="row mt-3">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                    <canvas id="sollBar" height="80"></canvas>
                    </div>
                </div>
            </div> 
        </div>
        <div class="button-container">
  <button id="update">click me</button>
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



<script>
    $(document).ready(function () {
    
        var sum = 0;
        $('.grade').each(function() {
            if((!(isNaN($(this).val()))) && $(this).val())
            {              
                sum +=  parseFloat($(this).val()); 
            }
        });
        $('.total').html(sum);

        $('#gradetable').change(function() {
            var sum = 0;
            $('.grade').each(function() {
            if((!(isNaN($(this).val()))) && $(this).val())
            {              
                sum +=  parseFloat($(this).val()); 
            }
            });
            
            $('.total').html(sum);
        });
    });
</script>


<script>
    (function( $ ){ 
        $.fn.sum=function () {
            var sum=0;
            $(this).each(function(index, element){
                if($(element).val()!="")
                    sum += parseFloat($(element).val());
            });
            return sum;
        }; 
    })( jQuery );


    $(document).ready(function show() {
        let grade3 =  $('#gradetable .grade3').sum();
        let grade4 =  $('#gradetable .grade4').sum();
        let grade5 =  $('#gradetable .grade5').sum();
        let grade6 =  $('#gradetable .grade6').sum();
        let grade7 =  $('#gradetable .grade7').sum();
        let grade8 =  $('#gradetable .grade8').sum();
        let grade9 =  $('#gradetable .grade9').sum();
        let grade10 = $('#gradetable .grade10').sum();
        let grade11 = $('#gradetable .grade11').sum();
        let grade12 = $('#gradetable .grade12').sum();
        let dataObj =  JSON.parse("["+grade3+','+grade4+','+grade5+','+grade6+','+grade7+','+grade8+','+grade9+','+grade10+','+grade11+','+grade12+"]");   
         
        $('#gradetable .grade').change(function() {
            let dataObj = [];
            show();
           
        });
        
        BuildChart(dataObj);

       
    });

   
 </script>

<script>
let myChart
Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
  align: 'end',
  anchor: 'end',
  color: '#555',
  offset: 0,
  font: {
    size: 16,
    weight: 'bold'
  },
  
});


function BuildChart(data) {
    // Wenn das Chart neu geladen wird (z.B nach .change) dann zerstöre das vorhandene
    // Ansonsten Hover-Effekt durch überlagernde Charst
    if(myChart){
          myChart.destroy();
      };
    var ctx = document.getElementById('sollBar').getContext('2d');
     myChart = new Chart(ctx, {
        type: 'bar',
    data: {
      labels: [3,4,5,6,7,8,9,10,11,12],
      datasets: [
        {
          backgroundColor: ["#a001f2", "#ffc600", "#a86301", "#fa3a07","#98c920","#019abc","#a001f2", "#2a82cd", "#ff00ff", "#ffc600", ],
          data: data
        }
      ]
    },
     // Abstand von Legend nach unten 3.Grade ...
  plugins: [{
    beforeInit: function(chart, options) {
      chart.legend.afterFit = function() {
        this.height = this.height + 20;
      };
    }
  }],
    options: {
        legend: { display: false },
        animation: {duration: 0 }, // general animation time
        hover: { animationDuration: 0 }, // duration of animations when hovering an item
        responsiveAnimationDuration: 0 , // animation duration after a resi
        scales: {
            yAxes: [{
                ticks: {display: false}
            }],
            xAxes: [{
        ticks: {
          callback: function(value, index, values) {
            return  value + '.Grad';
          }
        }
      }]
        }
    }

    });
    return myChart;
}




</script>