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

/**************************************************************
* Import der Helper mit folgende Beispiel möglich
* JLoader::import('helpers.map', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
* $mapImage   = MapHelpersMap::getMapImage($this->item->mapimage);
*****************************************************************/


/**
 * Class ActFrontendHelper
 *
 * @since  1.6
 */
class MapHelpersMap
{
     /**
     * 
     * 
     *
     * @param integer 
     *
     * @return  string 
     */
    
    public static function getMapImage($mapImageId, $raster)
    {

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select(array('name, image, width, height, raster'))
            ->from('#__mapimage')
           // ->join('LEFT', '#__mapimage AS cg ON cg.id_grade = t.calc_grade_round') 
            ->where($db->qn('raster') .' = ' .(int)$raster)
            ->where($db->qn('mapid') .' = ' .(int)$mapImageId);

        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * @param integer 
     *
     * @return  string 
     */

    public static function getMapInfoFromLine($lineId)
    {

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select(array('mapid, cx, cy'))
            ->from('#__act_line')
            ->where($db->qn('line') .' = ' .(int)$lineId);

        $db->setQuery($query);
        return $db->loadObject();
    }

        /**
     * @param integer 
     *
     * @return  string 
     */

     public static function getMapPointsfromMapId($mapId)
     {
 
         $db    = Factory::getDbo();
         $query = $db->getQuery(true);
         $query 
             ->select(array('line, cx, cy'))
             ->from('#__act_line')
             ->where($db->qn('state') .' = 1')
             ->where($db->qn('cx') .' != 0')
             ->where($db->qn('cy') .' != 0')
             ->where($db->qn('mapid') .' = ' .(int)$mapId)
             ->order($db->qn('line'));
 
         $db->setQuery($query);
         return $db->loadObjectList();
     }

}

