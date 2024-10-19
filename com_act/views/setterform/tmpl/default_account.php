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

//use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
//use \Joomla\CMS\Uri\Uri;
//use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

$user             = Factory::getUser();
$userIsOwnSetter  = $user->id == $this->item->user_id; // Will der User seinen eigene Account bearbeiten?
$setter           = Factory::getUser($this->item->user_id);
$setterIsAdmin    = in_array(7, $setter->groups);

// Status kann nicht verändert werden 
// Der Mitarbeiter Administrator ist 
// Oder man seinen eigenen Account bearbeiten will
if((1 == $setterIsAdmin) OR (1 == $userIsOwnSetter)) {
    $this->form->setFieldAttribute( 'state', 'readonly', 'true' );
}
?>

<div class="card h-100">
    <div class="card-header"><h3><i class="fas fa-user-alt"></i> <?php echo Text::_('COM_ACT_SETTERS_SETTERS_ACCOUNT');?></h3></div>
        <div class="card-body">
            <div class="form-group row"> 
                <div class="col-12 col-xl-8"><?php echo $this->form->renderField('state'); ?></div>
            </div>
            <div class="form-group row">  
                <?php if(empty($this->item->user_id)) : ?>
                    <div class="col-12 col-xl-8">
                        <?php echo ActHelpersAct::getPopoverByParams('COM_ACT_SETTERS_BENUTZERKONTO', 'COM_ACT_SETTERS_BENUTZERKONTO_INFO', 'float-left mr-2'); ?>
                        <?php echo $this->form->renderField('user_id'); ?>
                    </div>
                <?php else : ?>
                    <div class="col-12 col-xl-8">
                        <b><?php echo Text::_('COM_ACT_SETTERS_BENUTZERKONTO_CONNECTED_WITH');?></b><br />
                        <?php $userAccount = Factory::getUser($this->item->user_id); ?>
                        <?php echo Text::_('COM_ACT_SETTERS_SETTERS_NAME'). ': ' . $userAccount->name; ?><br />
                        <?php echo Text::_('COM_ACT_SETTERS_SETTERS_USERNAME') . ': ' .$userAccount->username; ?>
                        <input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id; ?>" />
                    </div>
                <?php endif; ?>
            </div>
    </div><?php // Card Body END ?>
</div><?php // Card END ?>
