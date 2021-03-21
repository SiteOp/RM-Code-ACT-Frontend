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

    <h3 class="mt-5">Soll-Verteilung SW</h3>
        <div class="">Gesamt Routenanzahl: <span id="total"><?php //Summer wird per Javascript berechnet; ?></span></div>
        <div>Gesamtzahl Linie: <span id="total_lines"><?php echo $this->total_lines_in_sektor; ?></span></div>
        <div>Routendichte: <span  id="density"></span></div>
        
          
    <div id="gradetable" class="table-responsive mt-4">
        <table class="table table-bordered text-center" id="datatable">
            <thead>
                <tr>
                    <?php for($i = 10; $i <= 36; $i++) : ?>
                        <?php  $soll = "soll$i"; ?>
                        <td class="grade<?php echo $i; ?>"><?php echo $this->form->getLabel($soll); ?></td>
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

