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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_act/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	js('input:hidden.route').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('routehidden')){
			js('#jform_route option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_route").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'comment.cancel') {
			Joomla.submitform(task, document.getElementById('comment-form'));
		}
		else {
			
			if (task != 'comment.cancel' && document.formvalidator.isValid(document.id('comment-form'))) {
				
				Joomla.submitform(task, document.getElementById('comment-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_act&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="comment-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ACT_TITLE_COMMENT', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<?php echo $this->form->renderField('id'); ?>
				<?php echo $this->form->renderField('state'); ?>
				<?php echo $this->form->renderField('route'); ?>

			<?php
				foreach((array)$this->item->route as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="route" name="jform[routehidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('review_yn'); ?>
				<?php echo $this->form->renderField('stars'); ?>
				<?php echo $this->form->renderField('myroutegrade'); ?>
				<?php echo $this->form->renderField('comment'); ?>
				<?php echo $this->form->renderField('ticklist_yn'); ?>
				<?php echo $this->form->renderField('ascent'); ?>
				<?php echo $this->form->renderField('tries'); ?>
				<?php echo $this->form->renderField('climbdate'); ?>
				<?php echo $this->form->renderField('tick_comment'); ?>
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
				<?php echo $this->form->renderField('created_by'); ?>

				<?php echo $this->form->renderField('modified_by'); ?>				<?php echo $this->form->renderField('created'); ?>
				<?php echo $this->form->renderField('modified'); ?>


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
