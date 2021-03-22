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

?>
<style>
.progress {height: 1.3rem; font-size: 120%; font-weight: bold; margin: .3rem 0;}
</style>
    <h3 class="mt-5">Soll-Verteilung SW</h3>
      
        <div>Gesamtzahl Linie: <span id="total_lines"><?php echo $this->total_lines_in_sektor; ?></span></div>
        <div>Routendichte: <span id="density"></span></div>
        <div class="row">
            <div class="col-sm-6"><?php echo $this->form->renderField('routestotalinsector'); ?></div>
        </div>
        

    <div id="gradetable" class="table-responsive mt-4">
        <table class="table table-bordered text-center" id="datatable">
            <thead>
                <tr>
                    <td>Grad</td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll3'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll4'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll5'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll6'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll7'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll8'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll9'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll10'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll11'); ?></td>
                    <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel('percentsoll12'); ?></td>
                </tr>
            </thead>
            <tbody>
                <tr id="percentsoll"> 
                    <td>Prozent</td>   
                    <td id="percentsoll3" ><?php echo $this->form->getInput('percentsoll3'); ?> </td>
                    <td id="percentsoll4"><?php echo $this->form->getInput('percentsoll4'); ?></td>
                    <td id="percentsoll5"><?php echo $this->form->getInput('percentsoll5'); ?></td>
                    <td id="percentsoll6"><?php echo $this->form->getInput('percentsoll6'); ?></td>
                    <td id="percentsoll7"><?php echo $this->form->getInput('percentsoll7'); ?></td>
                    <td id="percentsoll8"><?php echo $this->form->getInput('percentsoll8'); ?></td>
                    <td id="percentsoll9"><?php echo $this->form->getInput('percentsoll9'); ?></td>
                    <td id="percentsoll10"><?php echo $this->form->getInput('percentsoll10'); ?></td>
                    <td id="percentsoll11"><?php echo $this->form->getInput('percentsoll11'); ?></td>
                    <td id="percentsoll12"><?php echo $this->form->getInput('percentsoll12'); ?></td>
                </tr>
                <tr>
                    <td width="110px">Anzahl Routen</td>
                    <td><?php echo $this->form->getInput('totalsoll3'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll4'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll5'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll6'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll7'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll8'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll9'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll10'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll11'); ?></td>
                    <td><?php echo $this->form->getInput('totalsoll12'); ?></td>
                </tr>
                <tr>
                    <td>Erfüllung</td>
                    <td colspan="11">
                        <div class="progress">
                            <span class="sr-only"><?php echo $this->form->getInput('percent'); ?></span>
                            <div id="progress" class="" role="progressbar" style=""></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

<script>
    // Funktion zum berechnen der Summe 
    (function( $ ){ 
        $.fn.sum=function () {
            let sum=0;
            $(this).each(function(index, element){
                if($(element).val()!="")
                    sum += parseFloat($(element).val());
            });
         return sum;
        }; 
    })( jQuery );

    // Progressbar erstellen
    function makeProgress(percent_sum){
        $("#progress").removeClass();
        $("#progress").css("width", percent_sum + "%").text(percent_sum + " %");
        if (percent_sum > 100){
            $("#progress").addClass('progress-bar progress-bar-striped bg-danger');
        }
        else if (percent_sum == 100){
            $("#progress").addClass('progress-bar progress-bar-striped bg-success');
        }
        else if(percent_sum > 70) {
            $("#progress").addClass('progress-bar progress-bar-striped bg-info');
        }
        else {
            $("#progress").addClass('progress-bar progress-bar-striped'); 
        }
    }

    function loadData() {

        let routestotalinsector = $('#jform_routestotalinsector').val(); // geplante Routenanzahl Total
        let totallines = <?php echo $this->total_lines_in_sektor; ?>;
        let density = parseFloat((routestotalinsector/totallines)).toFixed(1);
        $('#density').html(density);
       // let density = ()
      
        // Hole die Prozentwerte aus dem Inputfeld 
        let percent3  = $('#jform_percentsoll3').val(); 
        let percent4  = $('#jform_percentsoll4').val();
        let percent5  = $('#jform_percentsoll5').val();
        let percent6  = $('#jform_percentsoll6').val();
        let percent7  = $('#jform_percentsoll7').val();
        let percent8  = $('#jform_percentsoll8').val();
        let percent9  = $('#jform_percentsoll9').val();
        let percent10 = $('#jform_percentsoll10').val();
        let percent11 = $('#jform_percentsoll11').val();
        let percent12 = $('#jform_percentsoll12').val();
      
        // Berechnung der Routenanzahl ((Gesamt Routenanzahl / 100) X Prozentwert)
        let total3  = ((routestotalinsector/100) * percent3);
        let total4  = ((routestotalinsector/100) * percent4);
        let total5  = ((routestotalinsector/100) * percent5);
        let total6  = ((routestotalinsector/100) * percent6);
        let total7  = ((routestotalinsector/100) * percent7);
        let total8  = ((routestotalinsector/100) * percent8);
        let total9  = ((routestotalinsector/100) * percent9);
        let total10 = ((routestotalinsector/100) * percent10);
        let total11 = ((routestotalinsector/100) * percent11);
        let total12 = ((routestotalinsector/100) * percent12);

        // Trage die berechnete Routenanzahl in das Readonly-Feld
        $('#jform_totalsoll3').val(parseFloat(total3).toFixed(1));
        $('#jform_totalsoll4').val(parseFloat(total4).toFixed(1));
        $('#jform_totalsoll5').val(parseFloat(total5).toFixed(1));
        $('#jform_totalsoll6').val(parseFloat(total6).toFixed(1));
        $('#jform_totalsoll7').val(parseFloat(total7).toFixed(1));
        $('#jform_totalsoll8').val(parseFloat(total8).toFixed(1));
        $('#jform_totalsoll9').val(parseFloat(total9).toFixed(1));
        $('#jform_totalsoll10').val(parseFloat(total10).toFixed(1));
        $('#jform_totalsoll11').val(parseFloat(total11).toFixed(1));
        $('#jform_totalsoll12').val(parseFloat(total12).toFixed(1));

        let sum_percent =  $('#percentsoll input').sum(); // Summe der Prozentwerte
        $('#jform_percent').val(sum_percent);
        makeProgress(sum_percent); // Funktion der Progressbar aufrufen
    };

    // Beim Laden die Daten holen und eintragen
    $(document).ready(function () { 
        loadData();
    });

    // Nach Veränderung der Daten nochmals die Daten holen
    $('#gradetable, #jform_routestotalinsector').change(function() {  
        loadData();
    });
</script>
