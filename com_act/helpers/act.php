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

JLoader::register('ActHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_act' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'act.php');

/**
 * Class ActFrontendHelper
 *
 * @since  1.6
 */
class ActHelpersAct
{
    /**
    * Get category name using category ID
    * @param integer $category_id Category ID
    * @return mixed category name if the category was found, null otherwise
    */
    public static function getCategoryNameByCategoryId($category_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('title')
            ->from('#__categories')
            ->where('id = ' . intval($category_id));

        $db->setQuery($query);
        return $db->loadResult();
    }
   
   
    
    /**
    * Get Route name using route ID
    * @param integer $route_id 
    * @return mixed route name
    */
    public static function getRouteByID($params) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('name')
            ->from('#__act_route')
            ->where('id = ' . intval($params));

        $db->setQuery($query);
        return $db->loadResult();
    }
	
    
 	 /**
    * Ausgabe eines einzelnen Inhalt des erweiterten Benutzerprofiles
    * @param User_Id, profile_key
    * @return Value 
    */
    public static function getUserProfil($user, $profile_key) {
      
		$db	   = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('profile_value')
            ->from('#__user_profiles')
            ->where('user_id = ' . (int) $user)
            ->where('profile_key = "'.$profile_key.'"');

        $db->setQuery($query);
        return $db->loadResult();
		

    }   
    /**
    * Get Stars using $params
    * @param Variable 
    * @return Html Stars
    */
    public static function getStarsByParams($params) {
     
        echo ' <span class="rm-rating">';
            for ($x = 1; $x <= $params; $x++)
            {
                echo '<span class="rm-star on"></span>';
            } 
             if (strpos($params, '.'))  
             {
                echo '<span class="rm-star half"></span>';
                $x++; 
             }
            while ($x <= 5)
           {
                echo '<span class="rm-star"></span>';
                 $x++;
            }
         echo '</span> ';
    }
	
	
	
	/**
    * Zahl der Sterne als gerundeter Wert - Rundung in .5 Schritten
    * @param Stars als Kommawert 4.24 z.B. 
    * @return Wert z.B 4.5
    */
    public static function getStarsRound($params) {
     
		$interval = .5; // Gerunden soll immer in .5 Schritten
		$wert = round($params,1);
		$faktor = 5/($interval*5);
		//$ergebnis  = round($wert*$faktor)/$faktor;
		$ergebnis  = round($wert*$faktor)/$faktor;
		return $ergebnis;
    }
	
	

	
	
	 /**
    * Anzeige des Begehungsstils als Icon 
    * @param = Begehungsstil 
    * @return echo Icons
    */
    public static function getUserAscentIcon($params) {
      
		switch($params)
			{
			  case (1): // Onsight
			  echo '<a class="lblpopover" rel="popover" 
					   data-container="body"
					   data-placement="top" 
					   data-trigger="hover" 
					   data-content="'.Text::_('COM_ACT_ASCENT_OPTION_1').' ">
					    <i class="fas fa-bolt"></i>
					</a>';
			  break;
			 
			  case (2): // Flash
			  echo '<a class="lblpopover" rel="popover" 
					   data-container="body"
					   data-placement="top" 
					   data-trigger="hover" 
					   data-content="'.Text::_('COM_ACT_ASCENT_OPTION_2').' ">
					    <i class="fas fa-eye"></i>
					</a>';
			  break;
			  
			  case (3): // Lead
			  echo '<a class="lblpopover" rel="popover" 
					   data-container="body"
					   data-placement="top" 
					   data-trigger="hover" 
					   data-content="'.Text::_('COM_ACT_ASCENT_OPTION_3').' ">
					    <i class="fas fa-circle"></i>
					</a>';
			  break;
			  
			  case (4): // Toprobe
			  echo '<a class="lblpopover" rel="popover" 
					   data-container="body"
					   data-placement="top" 
					   data-trigger="hover" 
					   data-content="'.Text::_('COM_ACT_ASCENT_OPTION_4').' ">
					    <i class="fas fa-level-down-alt"></i>
					</a>';
			  break;
			  
			  case (5):	// Projekt
			  echo '<a class="lblpopover" rel="popover" 
					   data-container="body"
					   data-placement="top" 
					   data-trigger="hover" 
					   data-content="'.Text::_('COM_ACT_ASCENT_OPTION_5').' ">
					    <i class="fas fa-hourglass-end"></i>
					</a>';
			  break;
			  
			  case (6):	// Automat
			  echo '<a class="lblpopover" rel="popover" 
					   data-container="body"
					   data-placement="top" 
					   data-trigger="hover" 
					   data-content="'.Text::_('COM_ACT_ASCENT_OPTION_6').' ">
					    <i class="far fa-dot-circle"></i>
					</a>';
			  break;
			}
       
    }
    
    /**
    * Get Popover $params
    * @params JText Var für Header und Body
    * @return Bootstrap Popover
    *  <?php echo ActHelpersAct::getPopoverByParams('SPRACHSTRING', 'SPRACHSTRING'); ?><br />
    */
    public static function getPopoverByParams($header, $txt) {
      
     echo ' <a class="lblpopover" rel="popover" 
                data-container="body"
                data-placement="right" 
                data-trigger="hover" 
                data-html="true"
                data-title="<header> '.Text::_($header).'</header> " 
                data-content=" '.Text::_($txt).' ">
                <i class=" '.Text::_('COM_ACT_FA_INFO').' "></i>
            </a>';
    }
    
    
    
    
    
    /**
     * Get an instance of the named model
     *
     * @param   string  $name  Model name
     *
     * @return null|object
     */
    public static function getModel($name)
    {
        $model = null;

        // If the file exists, let's
        if (file_exists(JPATH_SITE . '/components/com_act/models/' . strtolower($name) . '.php'))
        {
            require_once JPATH_SITE . '/components/com_act/models/' . strtolower($name) . '.php';
            $model = JModelLegacy::getInstance($name, 'ActModel');
        }

        return $model;
    }

    /**
     * Gets the files attached to an item
     *
     * @param   int     $pk     The item's id
     *
     * @param   string  $table  The table's name
     *
     * @param   string  $field  The field's name
     *
     * @return  array  The files
     */
    public static function getFiles($pk, $table, $field)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select($field)
            ->from($table)
            ->where('id = ' . (int) $pk);

        $db->setQuery($query);

        return explode(',', $db->loadResult());
    }

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = JFactory::getUser();

        if ($user->authorise('core.edit', 'com_act'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_act') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
    
    
    /**
     * Gets the Grade as Uiaa
     *
     * @param integer $params The Grad z.b Settergrade 
     *
     * @return  string
     */
    
    public static function uiaa($params)
    {
    // Reihenfolge normal
    $params = str_replace(array('10', '11', '12', '13', '14', '15', '16','17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','40',),    
                          array('3', '3+', '4-', '4', '4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+','9-','9','9+','10-','10','10+','11-','11','11+','12-','?'), 
                          $params);
    return $params;
    }


     /**
     * Erstellung einer chronologischen Zeitleiste
     * Wird für Charts.js benötigt
     *
     * Zeitraum 1 Jahr
     * Aktueller Monat am Ende
     *
     * Ausgabe "12/18","01/19","02/19","03/19", usw.
     */
    
    public static function Timeline()
    {
        $monate = array(1=>"01/",2=>"02/",3=>"03/",4=>"04/",5=>"05/",6=>"06/",7=>"07/",8=>"08/",9=>"09/",10=>"10/",11=>"11/",12=>"12/"); 
        $year = date("y");
        $lastyear = $year -1;
        $currentmonth = date("m");
    
        for ($i = ($currentmonth +1); $i <= 12; $i++)
        {
            echo '"'.$monate[$i] .$lastyear . '",';
        } 
        for ($i = 1; $i <= $currentmonth;)
        {
            echo '"'.$monate[$i] .$year . '",';
            $i++;
        }; 
    }
    
    
     /*
      * Erstellung der Kurven bzw. Datasets für Charts.js
      * Daten werden chronolgische Sortiert
      * Ausgabe als CSV-Liste
     */

    public static function LinesDataset($params)
    {

        $offset = date("m");                                               // Start ($offest) ist der aktuelle Monat ($currentmonth)
        $totalCurrentYear = array_slice($params, 0, $offset, true);        // Alle Daten des Array ab diesen Monat
        $totalLastYear    = array_slice($params, $offset, null, true);     // Daten des Array bis diesen Monat
        $linesDataset = array_merge($totalLastYear,$totalCurrentYear);     // Beide Array werden wieder verbunden
        $linesDataset  = implode(',', $linesDataset);                      // Erstelle CSV-Liste 

        return $linesDataset;
    }
	
	
	/**
     * Lineoptions - Text und Icon 
     * z.B Automat, Toprpbe
     * @param   int     $pk     The item's id
     * @return  mixed
     */
    public static function getLineoptions($id)
    {
        $db	   = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('icon', 'name'))
            ->from('#__lineoption_opt')
            ->where('id = ' . (int) $id);

        $db->setQuery($query);
        $options = $db->loadObjectList();
		
		foreach ($options as $option) {
			echo '<span class="icon ml-1"  
				 rel="popover" 
				 data-html="true"
				 data-trigger="hover" 
				 data-placement="top"
				 data-container="body"
				 data-content=" '.$option->name.' ">
				 ' .$option->icon. '
				 </span>';
		}
    }

}
