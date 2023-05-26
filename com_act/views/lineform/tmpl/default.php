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

// ACT Params 
$params      = JComponentHelper::getParams('com_act');
$use_line_properties = $params['use_line_properties'];

$doc = Factory::getDocument();
$doc->addScript(Uri::base() . '/media/com_act/js/form.js');

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
?>

    <?Php // Pager-Header ?>
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(Text::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>

    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><?php echo Text::sprintf('COM_ACT_FORM_LINE_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"><?php echo Text::_('COM_ACT_FORM_LINE_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>

	<div id="form-edit" class="line-edit front-end-edit">

		<form id="form-line"
			  action="<?php echo Route::_('index.php?option=com_act&task=line.save'); ?>"
			  method="post" class="form-validate" enctype="multipart/form-data">
			
			<div class="form-group row">  
					<div class="col-md-5"><?php echo $this->form->renderField('state'); ?></div>
			</div>
			<div class="form-group row">
				<div class="col-md-5"><?php echo $this->form->renderField('sector'); ?></div>
				<div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('line'); ?></div>
			</div> 
			<div class="form-group row">
				<div class="col-md-5"><?php echo $this->form->renderField('height'); ?></div>
				<div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('maker'); ?></div>
			</div> 
			<div class="form-group row">
				<div class="col-md-5"><?php echo $this->form->renderField('lineoption'); ?></div>
				<?php if(1==$use_line_properties) : ?>
				<div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('properties'); ?></div>
				<?php endif; ?>
			</div>
			<div class="form-group row">
				<div class="col-md-5"><?php echo $this->form->renderField('maxroutes'); ?></div>
			</div>
			<div class="form-group row">
				<div class="col-md-5"><?php echo $this->form->renderField('maintenance_interval'); ?></div>
				<div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('first_maintenace'); ?></div>
			</div> 
	
			<?php echo $this->form->getInput('id'); ?>
			<?php echo $this->form->getInput('created_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?>
			<div class="control-group">
				<div class="controls mt-1">
					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-secondary mr-1">
							<?php echo Text::_('COM_ACT_SUBMIT_SAVE'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-warning"
					   href="<?php echo Route::_('index.php?option=com_act&task=lineform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>
			<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
			<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
			<input type="hidden" name="option" value="com_act"/>
			<input type="hidden" name="task"
				   value="lineform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
