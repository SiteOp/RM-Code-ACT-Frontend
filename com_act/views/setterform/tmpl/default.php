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

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_act', JPATH_SITE);
$doc = Factory::getDocument();
$doc->addScript(JUri::base() . '/media/com_act/js/form.js');

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

<?php if (!$canEdit) : ?>
	<h3><?php throw new Exception(Text::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?></h3>
<?php else : ?>

    <?php // Pager-Header ?>
    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><?php echo Text::sprintf('COM_ACT_FORM_SETTER_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"><?php echo Text::_('COM_ACT_FORM_SETTER_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>
    

    <div id="form-edit" class="setter-edit front-end-edit">
            <form id="form-setter"
                  action="<?php echo Route::_('index.php?option=com_act&task=setter.save'); ?>"
                  method="post" class="form-validate" enctype="multipart/form-data">
                
        <?php echo $this->form->getInput('id'); ?>
        
        <div class="form-group row">  
             <div class="col-md-5"><?php echo $this->form->renderField('state'); ?></div>
             <div class="col-md-5"><?php echo $this->form->renderField('category'); ?></div>
        </div>
        <div class="form-group row">  
             <div class="col-md-5"><?php echo $this->form->renderField('firstname'); ?></div>
             <div class="col-md-5"><?php echo $this->form->renderField('lastname'); ?></div>
        </div>
        <div class="form-group row">  
             <div class="col-md-5"><?php echo $this->form->renderField('settername'); ?></div>
        </div>
        <div class="form-group row">  
             <div class="col-md-5"><?php echo $this->form->renderField('email'); ?></div>
             <div class="col-md-5"><?php echo $this->form->renderField('phone'); ?></div>
        </div>
        <div class="form-group row">  
             <div class="col-md-5"><?php echo $this->form->renderField('info'); ?></div>
             <div class="col-md-5"><?php echo $this->form->renderField('image'); ?></div>
        </div>

        <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
        <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
                    <?php echo $this->form->getInput('created_by'); ?>
                    <?php echo $this->form->getInput('modified_by'); ?>
        <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

                <div class="control-group">
                    <div class="controls">

                        <?php if ($this->canSave): ?>
                            <button type="submit" class="validate btn btn-success">
                                <?php echo Text::_('COM_ACT_SUBMIT_SAVE'); ?>
                            </button>
                        <?php endif; ?>
                        <a class="btn btn-warning"
                           href="<?php echo Route::_('index.php?option=com_act&task=setterform.cancel'); ?>"
                           title="<?php echo Text::_('JCANCEL'); ?>">
                            <?php echo Text::_('JCANCEL'); ?>
                        </a>
                    </div>
                </div>

                <input type="hidden" name="option" value="com_act"/>
                <input type="hidden" name="task"
                       value="setterform.save"/>
                <?php echo JHtml::_('form.token'); ?>
            </form>
	<?php endif; ?>
</div>
