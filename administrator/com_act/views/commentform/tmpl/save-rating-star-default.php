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

use Joomla\CMS\Language\Text;

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

<?php if (!$canEdit) : ?>
    <h3><?php throw new Exception(JText::_('COM_ACT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?></h3>
<?php else : ?>

    <?php // Pager-Header ?>
    <?php if (!empty($this->item->id)): ?>
        <div class="page-header">
            <h1><?php echo JText::sprintf('COM_ACT_FORM_COMMENT_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        </div>
	<?php else: ?>
        <div class="page-header">
            <h1 itemprop="headline"><?php echo JText::_('COM_ACT_COMMENT_COLOR_APPLY_ITEM_TITLE'); ?></h1>
        </div>
    <?php endif; ?>
    

    <div id="form-edit" class="comment-edit front-end-edit">

        <form id="form-comment" action="<?php echo JRoute::_('index.php?option=com_act&task=comment.save'); ?>"
              method="post" class="form-validate" enctype="multipart/form-data">
            
            <?php echo $this->form->getInput('id'); ?>
            
            <div class="form-group row">  
                <div class="col-md-5"><?php echo $this->form->renderField('state'); ?></div>
            </div>
            
            <input type="hidden" name="jform[route]" value="<?php echo $this->item->route[0]; ?>" />


            <div class="form-group row">
                <div class="col-md-5">
                    <div class="starrating risingstar d-flex justify-content-end flex-row-reverse">
                        <input type="radio" id="star5" name="jform[stars]" value="5" />
                        <label id="a5" for="star5" title="5 star">
                            <span id="some-element"><?php echo Text::_('COM_ACT_RATING_FIVE'); ?></span>
                        </label>
                        <input type="radio" id="star4" name="jform[stars]" value="4" />
                        <label id="a4" for="star4" title="4 star">
                            <span id="some-element"><?php echo Text::_('COM_ACT_RATING_FOUR'); ?></span>
                        </label>
                        <input type="radio" id="star3" name="jform[stars]" value="3" />
                        <label id="a3" for="star3" title="3 star">
                            <span id="some-element"><?php echo Text::_('COM_ACT_RATING_THREE'); ?></span>
                        </label>
                        <input type="radio" id="star2" name="jform[stars]" value="2" />
                        <label id="a2" for="star2" title=" star">
                            <span id="some-element"><?php echo Text::_('COM_ACT_RATING_TWO'); ?></span>
                        </label>
                        <input type="radio" id="star1" name="jform[stars]" value="1" />
                        <label id="a1" for="star1" title="1 star">
                            <span id="some-element"><?php echo Text::_('COM_ACT_RATING_ONE'); ?></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">  
                <div class="col-md-5"><?php echo $this->form->renderField('stars'); ?></div>
            </div>

        
            <div class="form-group row">
                <div class="col-md-5">
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
                </div> 
            </div> 
            
             <div class="form-group row">
                <div class="col-md-5"><?php echo $this->form->renderField('comment'); ?></div>
            </div> 
        

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
                            <button type="submit" class="validate btn btn-success">
                                <?php echo JText::_('COM_ACT_SUBMIT_SAVE'); ?>
                            </button>
                        <?php endif; ?>
                        <a class="btn btn-warning"
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

