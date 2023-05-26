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
use Joomla\Utilities\ArrayHelper;


/**************************************************************
* Import der Helper mit folgende Beispiel möglich
* JLoader::import('helpers.config', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
* $gradeList   = ConfigHelpersConfig::getxxxxxx(); 
*****************************************************************/


/**
 * Class 
 *
 * @since  1.6
 */
class ConfigHelpersConfig
{
     /**
     * Routen Eigenschaften bzw. Charakter der Route als Liste mit Komma getrennt
     *
     * @param array 
     *
     * @return 
     */
    
    public static function getRoutePropertiesList($properties)
    {

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select('name AS property')
            ->from($db->qn('#__rm_config_routes_property'))
            ->where($db->qn('id') . 'IN ('.$properties.')');

        $db->setQuery($query);
        $list = $db->loadObjectList();
        
        // remove the last comma
        $properties = array();
        foreach ($list as $key => $value) {
            array_push($properties, $value->property); 
        }
        return implode(", ", $properties);

    }


    /**
     * Linien Eigenschaften bzw. Charakter der Linie als Liste mit Komma getrennt
     *
     * @param array 
     *
     * @return 
     */
    public static function getLinePropertiesList($properties)
    {

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select('name AS property')
            ->from($db->qn('#__rm_config_lines_property'))
            ->where($db->qn('id') . 'IN ('.$properties.')');

        $db->setQuery($query);
        $list = $db->loadObjectList();
        
        // remove the last comma
        $properties = array();
        foreach ($list as $key => $value) {
            array_push($properties, $value->property); 
        }
        return implode(", ", $properties);

    }


   
    


}

