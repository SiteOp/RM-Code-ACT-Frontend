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

<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
<?php endif; ?>

<div class="comment-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo JText::sprintf('COM_ACT_EDIT_ITEM_TITLE', $this->route->routename); ?></h1>
		<?php else: ?>
			<h1><?php echo JText::_('COM_ACT_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

	<form id="form-comment" action="<?php echo JRoute::_('index.php?option=com_act&task=comment.save'); ?>"
		  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		
    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<div class="control-group">
		<?php if(!$canState): ?>
			<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
			<div class="controls"><?php echo $state_string; ?></div>
			<input type="hidden" name="jform[state]" value="<?php echo $state_value; ?>" />
		<?php else: ?>
			<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
		<?php endif; ?>
	</div>

    <input type="hidden" name="jform[route]" value="<?php echo $this->item->route[0]; ?>" />

	<?php echo $this->form->renderField('review_yn'); ?>

	<?php echo $this->form->renderField('stars'); ?>
    
   <div class="control-group" >
        <div class="control-label">
            <label>My Routegrade </label>
        </div>
        <div class="controls">
            <select name="jform[myroutegrade]" > 
                <option value="0" selected="selected">Keine Wertung</option> 
            <?php // The level is less than 3 or larger 12-do not allow any other selection ?>
            <?php if((int)$this->route->settergrade  >= (int)12) : ?>
                <option value="<?php echo $this->route->settergrade -2; ?>" 
                    <?php echo ( ($this->item->myroutegrade[0]) == ($this->route->settergrade -2) ? 'selected="selected"' : '');  ?>>
                    <?php echo ActHelpersAct::uiaa($this->route->settergrade -2); ?>
                </option> 
            <?php endif; ?>
            <?php if((int)$this->route->settergrade  >= (int)11) : ?>
                <option value="<?php echo $this->route->settergrade -1; ?>" 
                    <?php echo ( ($this->item->myroutegrade[0]) == ($this->route->settergrade -1) ? 'selected="selected"' : '');  ?>>
                    <?php echo ActHelpersAct::uiaa($this->route->settergrade -1); ?>
                </option> 
             <?php endif; ?>
                <option value="<?php echo $this->route->settergrade; ?>" 
                    <?php echo ( ($this->item->myroutegrade[0]) == ($this->route->settergrade) ? 'selected="selected"' : '');  ?>>
                    <?php echo ActHelpersAct::uiaa($this->route->settergrade   ); ?>
                </option> 
             <?php if((int)$this->route->settergrade  <= (int)35) : ?>
                <option value="<?php echo $this->route->settergrade +1; ?>" 
                    <?php echo ( ($this->item->myroutegrade[0]) == ($this->route->settergrade +1) ? 'selected="selected"' : '');  ?>>
                    <?php echo ActHelpersAct::uiaa($this->route->settergrade +1); ?>
                </option>
            <?php endif; ?>
           <?php if((int)$this->route->settergrade  <= (int)34) : ?>
                <option value="<?php echo $this->route->settergrade +2; ?>" 
                    <?php echo ( ($this->item->myroutegrade[0]) == ($this->route->settergrade +2) ? 'selected="selected"' : '');  ?>>
                    <?php echo ActHelpersAct::uiaa($this->route->settergrade +2); ?>
                </option> 
             <?php endif; ?>
            </select> 
        </div>
    </div> 

	<?php echo $this->form->renderField('comment'); ?>

	<?php echo $this->form->renderField('ticklist_yn'); ?>
    
    <?php echo $this->form->renderField('tries'); ?>

	<?php echo $this->form->renderField('ascent'); ?>

	<?php echo $this->form->renderField('climbdate'); ?>

    <?php echo $this->form->renderField('tick_comment'); ?>

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<?php echo $this->form->renderField('modified'); ?>


	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

	<?php echo $this->form->renderField('created_by'); ?>

	<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo $this->form->renderField('created'); ?>

			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_act"/>
			<input type="hidden" name="task"
				   value="commentform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>

