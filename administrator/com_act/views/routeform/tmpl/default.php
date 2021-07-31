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

// ACT Params 
$params =  JComponentHelper::getParams( 'com_act' );
$sponsoring = $params['sponsoring']; 

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

<?php if (!$canEdit) : ?>
    <h3><?php throw new Exception(JText::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?></h3>
<?php else : ?>

    <?php // Pager-Header Start ?>
    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><?php echo JText::sprintf('COM_ACT_FORM_ROUTE_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"><?php echo JText::_('COM_ACT_FORM_ROUTE_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>
    <?php // Pager-Header END ?>
    

    <div id="form-edit" class="route-edit front-end-edit">

        <form id="form-route" action="<?php echo JRoute::_('index.php?option=com_act&task=route.save'); ?>"
                method="post" class="form-validate" enctype="multipart/form-data" >
              
            <?php echo $this->form->getInput('id'); ?>
            
            <div class="form-group row">  
                <div class="col-md-5"><?php echo $this->form->renderField('state'); ?></div>
              </div>
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('name'); ?></div>
                <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('setterdate'); ?></div>
            </div> 
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('setter'); ?></div>
                <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('settergrade'); ?></div>
            </div>
            
            <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('line'); ?></div>
                <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('color'); ?></div>
            </div>
            

            <div class="form-group row">
               <div class="col-md-5"><?php echo $this->form->renderField('info'); ?></div>
               <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('infoadmin'); ?></div>
            </div>
            <div class="form-group row">
                <div class="col-md-5"> 
                <?php echo $this->form->renderField('sponsor'); ?>

           </div>
            </div>


            <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
            <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

            <?php echo $this->form->getInput('created_by'); ?>
            <?php echo $this->form->renderField('modified'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>
            
            <div class="control-group">
                <div class="controls mt-1">
                    <?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-secondary mr-1">
                            <?php echo JText::_('COM_ACT_SUBMIT_SAVE'); ?>
                        </button>
                    <?php endif; ?>
                    <a class="btn btn-warning" href="<?php echo JRoute::_('index.php?option=com_act&task=routeform.cancel'); ?>"
                         title="<?php echo JText::_('JCANCEL'); ?>">
                        <?php echo JText::_('JCANCEL'); ?>
                    </a>
                </div>
            </div>

            <input type="hidden" name="option" value="com_act"/>
            <input type="hidden" name="task" value="routeform.save"/>
            
            <?php echo JHtml::_('form.token'); ?>

        </form>
<?php endif; ?>
</div>


