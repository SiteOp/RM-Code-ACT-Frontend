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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Lade Globale Sprachdateien
$lang = Factory::getLanguage();
$extension = 'com_act_global';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

$map = $this->item->mapid;



if (!empty($map)) {
    // Map Helper
    JLoader::import('helpers.map', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
    $mapImage   = MapHelpersMap::getMapImage($this->item->mapid,1);
    // CSS
    $document = Factory::getDocument();
    $style = "#sop_map svg {background-image: url('/$mapImage->image');}";
    $document->addStyleDeclaration($style); 
    // Add Script 
    $doc = Factory::getDocument();
    $doc->addScript('/components/com_act/js/lineform/koordinate.js', true, true); // Koordinaten auslesen und in das Input-Feld eintragen
}

?>
 <?php if (!empty($map)): ?>
 
<div id="sop_map" class="mt-5">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 <?php echo $mapImage->width . ' ' . $mapImage->height; ?>">
    <text fill="#000" x="<?php echo $this->item->cx; ?>" y="<?php echo $this->item->cy; ?>" class="fas fa-2x">&#xf276;</text>
</svg>
</div>
<?php endif; ?>

