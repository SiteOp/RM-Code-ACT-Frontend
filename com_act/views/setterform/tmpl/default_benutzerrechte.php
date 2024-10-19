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

$user = Factory::getUser();
$setter = Factory::getUser($this->item->user_id);

$userIsAdmin           = $user->authorise('group.as.admin.manage', 'com_act');         // Administratorn dürfen alle Rechte verwalten
$userIsRoutesManager   = $user->authorise('group.as.routesmanager.manage', 'com_act'); // Rechte welche man als RM setzen darf
$setterIsRoutesManager = in_array(14, $setter->groups);
$setterIsAdmin         = in_array(7, $setter->groups);
$userIsOwnSetter       = $user->id == $this->item->user_id; // Will der User seinen eigene Account bearbeiten?

//User Kein Admin darf Rechte RM usw. nicht setzten
if(!$userIsAdmin) {
   $this->form->removeField('is_rm');
   $this->form->removeField('is_bf');
   $this->form->setFieldAttribute( 'is_wa', 'showon', '' );
   $this->form->setFieldAttribute( 'is_jo', 'showon', '' );
   $this->form->setFieldAttribute( 'is_ak', 'showon', '' );
}
if($userIsRoutesManager) {
   $this->form->setFieldAttribute( 'is_me', 'showon', 'is_wa:0' );
}

?>
<div class="card h-100">
    <div class="card-header"><h3><i class="fas fa-pencil-ruler"></i> <?php echo Text::_('Benutzerrechte');?></h3></div>
    <div class="card-body">
      <?php  // Mitarbeiter ist Admin
             // Keine Felder 
             // Nur Anzeige ist Admin  
             ?>
      <?php if($setterIsAdmin) : ?>
        <i class="fas fa-user-shield"></i> <?php echo Text::_('JADMINISTRATOR');?>
      <?php endif;?>
      
      <?php   // User ist Admin
              // Nicht Eigener Account
              // Mitarbeite kein Admin
        ?>
      <?php if(($userIsAdmin) AND (!$userIsOwnSetter) AND (!$setterIsAdmin)) : ?>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_rm'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_bf'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_ak'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_jo'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_wa'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_me'); ?></div></div>
      <?php endif; ?>

      <?php  // User ist kein Admin
             // User ist RM
             // Nicht eigener Account
             // Mitarbeite kein RM
             // Mitarbeiter kein Admin
      ?>
      <?php if ((!$userIsAdmin) AND ($userIsRoutesManager) AND (!$userIsOwnSetter) AND (!$setterIsRoutesManager) AND (!$setterIsAdmin) ): ?>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_ak'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_jo'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_wa'); ?></div></div>
        <div class="row"><div class="col"><?php echo $this->form->renderField('is_me'); ?></div></div>
      <?php endif; ?>
      
      <?php  // Mitarbeiter ist RM
             // Kein Formular
             // Anzeige ist Routes-Manager
      ?>
      <?php if(($setterIsRoutesManager) AND (!$userIsAdmin)) : ?>
          <i class="fas fa-chalkboard-teacher"></i> <?php echo Text::_('COM_ACT_ACL_USERS_RM'); ?>
      <?php endif;?>
    </div><?php // Card Body END ?>
</div><?php // Card END ?>