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
		
	js('input:hidden.color').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('colorhidden')){
			js('#jform_color option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_color").trigger("liszt:updated");
	js('input:hidden.line').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('linehidden')){
			js('#jform_line option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_line").trigger("liszt:updated");
	js('input:hidden.setter').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('setterhidden')){
			js('#jform_setter option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_setter").trigger("liszt:updated");
	js('input:hidden.sponsor').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('sponsorhidden')){
			js('#jform_sponsor option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_sponsor").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'route.cancel') {
			Joomla.submitform(task, document.getElementById('route-form'));
		}
		else {
			
			if (task != 'route.cancel' && document.formvalidator.isValid(document.id('route-form'))) {
				
	if(js('#jform_setter option:selected').length == 0){
		js("#jform_setter option[value=0]").attr('selected','selected');
	}
				Joomla.submitform(task, document.getElementById('route-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_act&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="route-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ACT_TITLE_ROUTE', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<?php echo $this->form->renderField('id'); ?>
				<?php echo $this->form->renderField('name'); ?>
				<?php echo $this->form->renderField('state'); ?>
				<?php echo $this->form->renderField('settergrade'); ?>
				<?php echo $this->form->renderField('color'); ?>

			<?php
				foreach((array)$this->item->color as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="color" name="jform[colorhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('line'); ?>

			<?php
				foreach((array)$this->item->line as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="line" name="jform[linehidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('setter'); ?>

			<?php
				foreach((array)$this->item->setter as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="setter" name="jform[setterhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('createdate'); ?>
				<?php echo $this->form->renderField('info'); ?>
				<?php echo $this->form->renderField('infoadmin'); ?>
				<?php echo $this->form->renderField('sponsor'); ?>

			<?php
				foreach((array)$this->item->sponsor as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="sponsor" name="jform[sponsorhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->renderField('created_by'); ?>				<?php echo $this->form->renderField('modified'); ?>

				<?php echo $this->form->renderField('modified_by'); ?>				<input type="hidden" name="jform[hit]" value="<?php echo $this->item->hit; ?>" />


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
