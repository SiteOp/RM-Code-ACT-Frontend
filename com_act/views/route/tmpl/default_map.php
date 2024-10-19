<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;


JLoader::import('helpers.map', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
$mapInfo    = MapHelpersMap::getMapInfoFromLine($this->item->line);
$mapImage   = MapHelpersMap::getMapImage($mapInfo->mapid,0);

// CSS
$document = Factory::getDocument();
$style = "#sop_map svg {background-image: url('/$mapImage->image');background-size: cover;}";
$document->addStyleDeclaration($style); 
?>

<?php if($mapInfo->cx !=0 AND $mapInfo->cy != 0) : ?>
    <?php // CARD START ?>
        <div class="card mt-5 mb-5">
            <div class="card-header">
                <h3 class="float-left"><i class="fas fa-puzzle-piece"></i> <?php echo Text::_('MAP'); ?></h3>    
            </div>
                <div id="sop_map" class="">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 <?php echo $mapImage->width . ' ' . $mapImage->height; ?>">
                        <circle id="circle" cx="<?php echo $mapInfo->cx; ?>" cy="<?php echo $mapInfo->cy; ?>"  r="1rem" fill="#fc2c0a"/>
                    </svg>
                </div>    
        </div>
    <?php // CARD END ?>
<?php endif; ?>



