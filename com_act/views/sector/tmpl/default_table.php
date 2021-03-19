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

// Helpers 
$ist_grade_list = ActHelpersAct::getIstGrades($this->item->id)[0];


$sum_soll = (
        $this->item->soll10 + 
        $this->item->soll11 +
        $this->item->soll12 +
        $this->item->soll13 +
        $this->item->soll14 +
        $this->item->soll15 +
        $this->item->soll16 +
        $this->item->soll17 +
        $this->item->soll18 +
        $this->item->soll19 +
        $this->item->soll20 +
        $this->item->soll21 +
        $this->item->soll22 +
        $this->item->soll23 +
        $this->item->soll24 +
        $this->item->soll25 +
        $this->item->soll26 +
        $this->item->soll27 +
        $this->item->soll28 +
        $this->item->soll29 +
        $this->item->soll30 +
        $this->item->soll31 +
        $this->item->soll32 +
        $this->item->soll33 +
        $this->item->soll34 +
        $this->item->soll35 
    ); 
$sum_ist = array_sum($ist_grade_list);
$sum_diff = $sum_soll - $sum_ist;
?>
<p class="mt-5">Schwierigkeitsgrade Soll-Ist Vergleich</p>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm text-center">
        <thead>
            <tr>
                <th>Grad</th>
                <?php for($i = 10; $i < 35; $i++) : ?>
                    <th> <?php echo Text::_('COM_ACT_ROUTES_GRADE_OPTION_'.$i) ; ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><i class="fas fa-align-justify"></i> Soll</td>
                <?php for($i = 10; $i < 35; $i++) : ?>
                    <?php  $soll = "soll$i"; ?>
                    <td><?php echo $this->item->$soll; ?></td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td><i class="fas fa-align-left"></i>&nbsp; Ist</td>
                 <?php for($i = 10; $i < 35; $i++) : ?>
                    <td><?php echo $ist_grade_list['ist_grade_'.$i]; ?></td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td>Diff</td>
                <?php for($i = 10; $i < 35; $i++) : ?>
                    <?php  $soll = "soll$i"; ?>
                    <?php if(($ist_grade_list['ist_grade_'.$i] -$this->item->$soll) < 0) {
                        echo '<td style="color: red">';
                    } 
                    elseif (($ist_grade_list['ist_grade_'.$i] -$this->item->$soll) > 0) {
                        echo '<td style="color: green">';
                    }
                    else {
                        echo '<td>';
                    }; ?>
                    <?php echo ($ist_grade_list['ist_grade_'.$i] -$this->item->$soll); ?></td>
                <?php endfor; ?>
            </tr>
        </tbody>
    </table>
</div>