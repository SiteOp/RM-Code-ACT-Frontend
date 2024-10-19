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
<div class="card">
     <div class="card-header"><h3><i class="fas fa-user-tag"></i> <?php echo Text::_('COM_ACT_SETTERS_SETTERS_DATA');?></h3></div>
     <div class="card-body">
          <div class="form-group row">  
               <div class="col-md-5"><?php echo $this->form->renderField('category'); ?></div>
          </div>
          <div class="form-group row">  
               <div class="col-md-5">
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_FORM_LBL_SETTER_FIRST_AND_LASTNAME', 'COM_ACT_FORM_LBL_SETTER_FIRSTNAME_INFO', 'float-left mr-2'); ?>
                    <?php echo $this->form->renderField('firstname'); ?>
               </div>
               <div class="col-md-5"><?php echo $this->form->renderField('lastname'); ?>
               </div>
          </div>
          <div class="form-group row">  
               <div class="col-md-5">
                    <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_FORM_LBL_SETTER_SETTERNAME', 'COM_ACT_FORM_LBL_SETTER_SETTERNAME_INFO', 'float-left mr-2'); ?>
                    <?php echo $this->form->renderField('settername'); ?>
               </div>
          </div>
          <div class="form-group row">  
               <div class="col-md-5"><?php echo $this->form->renderField('email'); ?></div>
               <div class="col-md-5"><?php echo $this->form->renderField('phone'); ?></div>
          </div>
          <div class="form-group row">  
               <div class="col-md-5"><?php echo $this->form->renderField('info'); ?></div>
          </div>
     </div><?php // Card Body END ?>
</div><?php // Card END ?>
