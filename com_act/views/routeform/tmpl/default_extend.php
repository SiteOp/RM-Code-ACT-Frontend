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

// ACT Params 
$params        = JComponentHelper::getParams('com_act');
$extendHeader  = $params['extendHeader'];
$extend_sql    = $params['extend_sql'];
$extend_sql2   = $params['extend_sql2'];
$extend_txt    = $params['extend_txt'];
$info1_extend  = $params['info1_extend'];
$info2_extend  = $params['info2_extend'];
$info3_extend  = $params['info3_extend'];
$info4_extend  = $params['info4_extend'];
$extend_check1 = $params['extend_check1'];
$extend_check2 = $params['extend_check2'];
$extend_check2 = $params['extend_check2'];

$routetypeRequired    = $params['routetypeRequired'];
$extend_sqlRequired   = $params['extend_sqlRequired'];
$info1_extendRequired = $params['info1_extendRequired'];
$info2_extendRequired = $params['info2_extendRequired'];
$info3_extendRequired = $params['info3_extendRequired'];
$info4_extendRequired = $params['info4_extendRequired'];

?>

<?php if(!empty($extendHeader) ) : ?>
    <h3 class="mt-5"><?php echo $extendHeader; ?></h3>
<?php endif;?>

<?php if (1 == $extend_check1) : ?>
   <div class="form-check form-check-inline mb-2">
        <input type="hidden" name="jform[extend_check1]" value="0">
        <?php echo $this->form->getInput('extend_check1'); ?>
         <?php echo $this->form->getLabel('extend_check1'); ?>
    </div>
<?php endif; ?>

<?php if (1 == $extend_check2) : ?>
    <div class="form-check form-check-inline">
        <input type="hidden" name="jform[extend_check2]" value="0">
        <?php echo $this->form->getInput('extend_check2'); ?>
        <?php echo $this->form->getLabel('extend_check2'); ?>
     </div>
<?php endif; ?>

<?php if (1 == $extend_sql OR 1 == $extend_txt OR 1==$extend_sql2) : ?>
    <div class="form-group row">   
        <?php if (1 == $extend_sql): ?>
            <div class="col-md-5"><?php echo $this->form->renderField('extend_sql'); ?></div>
        <?php endif; ?>
        <?php if(1== $extend_sql2) : ?>
            <div class="col-md-5 mb-2"><?php echo $this->form->renderField('extend_sql2'); ?></div>
        <?php endif; ?>
        <?php if(1 == $extend_txt) : ?>
            <div class="col-md-5 col-md-offset-1"><?php echo $this->form->renderField('extend_txt'); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
    
<?php if(1==$info1_extend) : ?>
    <div class="form-group row">
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-3"><?php echo $this->form->renderField('info1_extend'); ?></div>
                 <?php if (1== $info2_extend) : ?>
                    <div class="col-md-3"><?php echo $this->form->renderField('info2_extend'); ?></div>
                <?php endif; ?>
                <?php if (1== $info3_extend) : ?>
                  <div class="col-md-3"><?php echo $this->form->renderField('info3_extend'); ?></div>
                <?php endif; ?>
                <?php if (1== $info4_extend) : ?>
                    <div class="col-md-3"><?php echo $this->form->renderField('info4_extend'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    <?php // In der Config von ACT nachsehen welche Felder als Pflichtfelder definiert sind. ?>
    <?php if (1 == $routetypeRequired) : ?>
        $("[name='jform[routetype]']").prop("required", true);
        $('#jform_routetype-lbl').addClass('required2');
    <?php endif; ?>

    <?php if (1 == $extend_sqlRequired) : ?>
        $("[name='jform[extend_sql]']").prop("required", true);
        $('#jform_extend_sql-lbl').addClass('required2');
    <?php endif; ?>


    <?php if (1 == $info1_extendRequired) : ?>
        $("[name='jform[info1_extend]']").prop("required", true);
     $('#jform_info1_extend-lbl').addClass('required2');
    <?php endif; ?>

    <?php if (1 == $info2_extendRequired) : ?>
        $("[name='jform[info2_extend]']").prop("required", true);
        $('#jform_info2_extend-lbl').addClass('required2');
    <?php endif; ?>

    <?php if (1 == $info3_extendRequired) : ?>
        $("[name='jform[info3_extend]']").prop("required", true);
        $('#jform_info3_extend-lbl').addClass('required2');
    <?php endif; ?>

    <?php if (1 == $info4_extendRequired) : ?>
        $("[name='jform[info4_extend]']").prop("required", true);
        $('#jform_info4_extend-lbl').addClass('required2');
    <?php endif; ?>

    <?php // Wenn Required extra definiert (extra heißt nicht im form.xml) dann erstelle den * über die die Css clas .required2 ; ?>
        $(".required2").each(function(){
        $(this).append("<span class='star'> *</span>");
    });
</script>