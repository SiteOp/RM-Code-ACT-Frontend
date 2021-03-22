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

$doc = Factory::getDocument();
//$doc->addScript('node_modules/chartjs-plugin-labels/build/chartjs-plugin-labels.min.js'); // Für Prozentwerte 

?>

<div class="row mt-5">
    <div class="col-12">
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