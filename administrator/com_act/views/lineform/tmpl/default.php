<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_act', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_act/js/form.js');

$user    = JFactory::getUser();
$canEdit = ActHelpersAct::canUserEdit($this->item, $user);


if($this->item->state == 1){
	$state_string = 'Publish';
	$state_value = 1;
} else {
	$state_string = 'Unpublish';
	$state_value = 0;
}
$canState = JFactory::getUser()->authorise('core.edit.state','com_act');
?>

<?Php // Pager-Header ?>
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>

    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><?php echo JText::sprintf('COM_ACT_FORM_LINE_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"><?php echo JText::_('COM_ACT_FORM_LINE_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>
    

<div id="form-edit" class="line-edit front-end-edit">

		<form id="form-line"
			  action="<?php echo JRoute::_('index.php?option=com_act&task=line.save'); ?>"
			  method="post" class="form-validate" enctype="multipart/form-data">
			
	<?php echo $this->form->getInput('id'); ?>
    
    <div class="form-group row">  
            <div class="col-md-5"><?php echo $this->form->renderField('state'); ?></div>
    </div>
    
    <div class="form-group row">
        <div class="col-md-5"><?php echo $this->form->renderField('sector'); ?></div>
        <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('line'); ?></div>
    </div> 

    <div class="form-group row">
        <div class="col-md-5"><?php echo $this->form->renderField('inorout'); ?></div>
        <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('building'); ?></div>
    </div> 



	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-success">
							<?php echo JText::_('COM_ACT_SUBMIT_SAVE'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-warning"
					   href="<?php echo JRoute::_('index.php?option=com_act&task=lineform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_act"/>
			<input type="hidden" name="task"
				   value="lineform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
