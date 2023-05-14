<?php

/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
defined('_JEXEC') or die;


/**************************************************************
* Import der Helper mit folgende Beispiel möglich
* JLoader::import('helpers.colors', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
* $gradeList   = ColorsHelpersColors::getColor($item->rgbcode, $item->rgbcode2, $item->rgbcode3);
*****************************************************************/


/**
 * Class ActFrontendHelper
 *
 * @since  1.6
 */
class ColorsHelpersColors
{
     /**
     */
    
    public static function getColor($rgbcolor1, $rgbcolor2, $rgbcolor3 )
    {
        if ((!empty($rgbcolor1)) AND (!empty($rgbcolor2)) AND (!empty($rgbcolor3))) 
            {
                return "<span class='routecolor' style='background: linear-gradient(to right, $rgbcolor1 33%, $rgbcolor2 33%,  66%, $rgbcolor3 66%);' ></span>";
            } 
        elseif ((!empty($rgbcolor1)) AND (!empty($rgbcolor2)) AND (empty($rgbcolor3))) 
            {
                return "<span class='routecolor'  style='background-image: linear-gradient(to bottom, $rgbcolor1 50%, $rgbcolor2 50%);' ></span>";
            } 
        else 
            {
                return "<span class='routecolor' style='background: $rgbcolor1'></span>";
            }
    }

}

