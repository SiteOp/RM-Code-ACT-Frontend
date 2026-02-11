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
use \Joomla\CMS\Language\Text;
?>
<style>
.table > tbody > tr > td {
     vertical-align: middle;
}
.table {margin-bottom: 0;}
.modal-body {padding: 0;}
.table i {font-size: 1.5rem; }
.table i.fa-check {font-size: .9rem; color: green}
</style>

<div class="modal fade" id="benutzerrechteModal" tabindex="-1" aria-labelledby="benutzerrechteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h3 class="modal-title w-100" id="benutzerrechteModalLabel"><i class="fas fa-pencil-ruler"></i> Benutzerrechte</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo JHTML::_('content.prepare', '{loadposition benutzerrechte}');  ?>
      </div> <?php // modal-body End ?>
    </div> <?php // modal-content End ?>
  </div> <?php // modal-dialog End ?>
</div> <?php // modal End ?>
