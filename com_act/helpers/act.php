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
    * Ist der Begehungstil sichtbar geschalten?
    * @param User_Id
    * @return Value 
    */
    public static function getUserProfilAscentShow($user) {
      
		$db	   = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('value')
            ->from('#__fields_values')
            ->where('item_id = ' . (int) $user)
            ->where('field_id = 17');

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
    public static function getPopoverByParams($header, $txt, $class='lblpopover') {
      
     echo ' <a class="'.$class.'" rel="popover" 
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
    * Get Popover $params
    * @params Text Var für  Body
    * @return Bootstrap Popover
    *  <?php echo ActHelpersAct::getPopover('SPRACHSTRING', 'Icon Class'); ?><br />
    * Einfache Ausgabe nur Icon und Erklärung
    */
    public static function getPopover($header, $icon_class) {
      echo'  <a class="popover2" rel="popover" 
			    data-container="body"
				data-placement="top" 
				data-trigger="hover" 
				data-content="'.Text::_($header).' ">
				<i class="'.$icon_class.'"></i>
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
     * 
     * Anzeigeart 0 == nur PopoverIcon
     * Anzeigeart 1 == Popover und daneben der Text
     */
    public static function getLineoptions($id, $anzeigeart = 0)
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
            if($anzeigeart == 1) {
                echo $option->name;
             }
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



    /**
     * Routeoption - Text und Icon 
     * z.B Automat, Toprpbe
     * @param   int     $pk     The item's id
     * @return  mixed
     */
    public static function getRouteoptions($id)
    {
        $db	   = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('icon', 'name', 'color'))
            ->from('#__routeoption_opt')
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




    /**
     * Anzahl an Routen pro Schwierigkeitsgrad innerhalb eines Sektors
     * @param   int    Sektor-ID
     * @return  mixed
     */
	public static function getIstGrades($sector)
    {
      $db = Factory::getDbo();

      $query = $db->getQuery(true);
      $query->select(array('COUNT(CASE WHEN t.calc_grade = 10 then 1 ELSE NULL END) as  ist_grade_10',
                           'COUNT(CASE WHEN t.calc_grade = 11 then 1 ELSE NULL END) as  ist_grade_11',
                           'COUNT(CASE WHEN t.calc_grade = 12 then 1 ELSE NULL END) as  ist_grade_12',
                           'COUNT(CASE WHEN t.calc_grade = 13 then 1 ELSE NULL END) as  ist_grade_13',
                           'COUNT(CASE WHEN t.calc_grade = 14 then 1 ELSE NULL END) as  ist_grade_14',
                           'COUNT(CASE WHEN t.calc_grade = 15 then 1 ELSE NULL END) as  ist_grade_15',
                           'COUNT(CASE WHEN t.calc_grade = 16 then 1 ELSE NULL END) as  ist_grade_16',
                           'COUNT(CASE WHEN t.calc_grade = 17 then 1 ELSE NULL END) as  ist_grade_17',
                           'COUNT(CASE WHEN t.calc_grade = 18 then 1 ELSE NULL END) as  ist_grade_18',
                           'COUNT(CASE WHEN t.calc_grade = 19 then 1 ELSE NULL END) as  ist_grade_19',
                           'COUNT(CASE WHEN t.calc_grade = 20 then 1 ELSE NULL END) as  ist_grade_20',
                           'COUNT(CASE WHEN t.calc_grade = 21 then 1 ELSE NULL END) as  ist_grade_21',
                           'COUNT(CASE WHEN t.calc_grade = 22 then 1 ELSE NULL END) as  ist_grade_22',
                           'COUNT(CASE WHEN t.calc_grade = 23 then 1 ELSE NULL END) as  ist_grade_23',
                           'COUNT(CASE WHEN t.calc_grade = 24 then 1 ELSE NULL END) as  ist_grade_24',
                           'COUNT(CASE WHEN t.calc_grade = 25 then 1 ELSE NULL END) as  ist_grade_25',
                           'COUNT(CASE WHEN t.calc_grade = 26 then 1 ELSE NULL END) as  ist_grade_26',
                           'COUNT(CASE WHEN t.calc_grade = 27 then 1 ELSE NULL END) as  ist_grade_27',
                           'COUNT(CASE WHEN t.calc_grade = 28 then 1 ELSE NULL END) as  ist_grade_28',
                           'COUNT(CASE WHEN t.calc_grade = 29 then 1 ELSE NULL END) as  ist_grade_29',
                           'COUNT(CASE WHEN t.calc_grade = 30 then 1 ELSE NULL END) as  ist_grade_30',
                           'COUNT(CASE WHEN t.calc_grade = 31 then 1 ELSE NULL END) as  ist_grade_31',
                           'COUNT(CASE WHEN t.calc_grade = 32 then 1 ELSE NULL END) as  ist_grade_32',
                           'COUNT(CASE WHEN t.calc_grade = 33 then 1 ELSE NULL END) as  ist_grade_33',
                           'COUNT(CASE WHEN t.calc_grade = 34 then 1 ELSE NULL END) as  ist_grade_34',
                           'COUNT(CASE WHEN t.calc_grade = 35 then 1 ELSE NULL END) as  ist_grade_35',
                           )
                    )
                    
            ->from('#__act_route AS a')
            ->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // VIEW TABLE
			->join('LEFT', '#__act_line AS l ON l.id = a.line')
			->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
            ->where('a.state IN(1,-1)')
            ->where('s.id =' .$sector);
            
       $db->setQuery($query);
       $result = $db->loadAssocList();
	   
       return $result; 
    }



    /**
    * Alle Linien in diesem Sektor - 
    * @param integer $sector_id
    * @return mixed 
    */
    public static function getLinesFromSectorId($sector_id) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('line, maxroutes, id',))
            ->from('#__act_line')
            ->where('state = 1')
            ->where('sector = ' . intval($sector_id));

        $db->setQuery($query);
        return $db->loadObjectList();
    }

     /**
    * Anzahl Routen innerhalb einer Linie
    * @param integer $line_id
    * @return mixed 
    */
    public static function getNumbersRoutesFromLineId($line_id) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('COUNT(*)'))
            ->from('#__act_route')
            ->where('state = 1')
            ->where('line = ' . intval($line_id));

        $db->setQuery($query);
        return $db->loadResult();
    }


    /**
    * Name des Gebäudes bzw. Mastersektor
    * @param integer $id
    * @return string
    */
    public static function getBuildingName($id) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('building'))
            ->from('#__act_building')
            ->where('id = ' . intval($id));

        $db->setQuery($query);
        return $db->loadResult();
    }
   




/**
    * Anzeige eines Icons für den Removestatus einer Route
    * Beispiel ActHelpersAct::getRemoveRouteIcon($item->lifetime, $removedate)
    * @param =  
    * @return echo Icons
    */
    public static function getRemoveRouteIcon($removestate, $popover = 0) {
      
		switch($removestate){
            case (0):
                $color = '#89c200';
                break;
            case (1):
                $color = '#fc8c26';
                break;
            case (2):
                $color = 'red';
                break;
            }
        // Mit Angabe des Removedate Popover mit Datumsangabe
        // Z. B. Routenliste Admin
        if(0 == $popover) {
            echo '<i class="'.Text::_('ACTGLOBAL_FA_REMOVE_STATE_'.$removestate).' fa-lg" style="color: '.$color.';"></i> </a>';
           
        } 
        // Ohne Angabe des Datums nur Icon anzeigen
        // Z, B für Routenliste User
        else {
            echo '<a class="d-inline-block" rel="popover" 
            data-container="body"
            data-placement="top" 
            data-trigger="hover" 
            data-content="'.Text::_('ACTGLOBAL_ROUTE_LIFETIME_POPOVER_CONTENT_USER').' ">
            <i class="'.Text::_('ACTGLOBAL_FA_REMOVE_STATE_1').'" style="color: red"></i> 
          </a>';
        }
              
        
    }

    

}
    
    