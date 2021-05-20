<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$name  = $this->item->extend_name;
$txt   = $this->item->extend_txt;
$info1 = $this->item->info1_extend;
$info2 = $this->item->info2_extend;
$info3 = $this->item->info3_extend;
$info4 = $this->item->info4_extend;

?>
<?php if((!empty($name))   OR
         (!empty($txt))    OR
         (!empty($info1))  OR
         (!empty($info2))  OR
         (!empty($info3))  OR
         (!empty($info4))
         ) : ?>

    <?php // CARD START ?>
        <div class="card mt-5 mb-5">
            <div class="card-header">
                <h3 class="float-left"><i class="fas fa-puzzle-piece"></i> <?php echo Text::_('COM_ACT_ROUTE_SPEZIAL_INFO'); ?></h3>    
            </div>
            <div class="card-body">
                <?php if(!empty($name)) : ?>
                <dl class="row"><?php // Color ?>
                    <dt class="col-6"><?php echo Text::_('COM_ACT_FROM_LBL_ROUTE_EXTEND_SQL'); ?></dt>
                    <dd class="col-6">
                        <?php echo $name;?>
                </dl>
                <?php endif; ?>
                <?php if(!empty($txt)) : ?>
                <dl class="row"><?php // Griffset ?>
                    <dt class="col-6"><?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_EXTEND_TXT'); ?></dt>
                    <dd class="col-6">
                        <?php echo $txt;?>
                </dl>
                <?php endif; ?>
                <?php if(!empty($info1)) : ?>
                <dl class="row"><?php // Griffanzahl ?>
                    <dt class="col-6"><?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_INFO1_EXTEND'); ?></dt>
                    <dd class="col-6">
                        <?php echo $info1;?>
                </dl>
                <?php endif; ?>
                <?php if((!empty($info2)) OR (!empty($info3)) OR (!empty($info4))) : ?>
                <dl class="row"><?php // Sepzial z. B GFK, PU usw ?>
                    <dt class="col-6"><?php echo Text::_('Spezial'); ?></dt>
                    <dd class="col-6">
                    <?php if(!empty($info2)) : ?>
                        <?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_INFO2_EXTEND'); ?><br />
                    <?php endif; ?>
                    <?php if(!empty($info3)) : ?>
                        <?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_INFO3_EXTEND'); ?><br />
                    <?php endif; ?>
                    <?php if(!empty($info4)) : ?>
                        <?php echo Text::_('COM_ACT_FORM_LBL_ROUTE_INFO4_EXTEND'); ?><br />
                    <?php endif; ?>
                </dl>
                <?php endif; ?>
            </div>     
        </div>
    <?php // CARD END ?>
<?php endif; ?>


