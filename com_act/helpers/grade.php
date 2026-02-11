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
* JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
* $gradeList   = GradeHelpersGrade::getGradeListPlanning(); 
*****************************************************************/


/**
 * Class ActFrontendHelper
 *
 * @since  1.6
 */
class GradeHelpersGrade
{
     /**
     * Ausgabe des eigentlichen Schwierigkeitsgrades 
     * je nach gültiger Tablle (UIAA oder Franz usw)
     *
     * @param integer $grade z.B 10
     *
     * @return  string z.B 3+ oder 6c
     */
    
    public static function getGrade($grade)
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select('grade')
            ->from('#__'.$grade_table.'')
            ->where('id_grade = ' .(int)$grade);

        $db->setQuery($query);
        return $db->loadResult();
    }


    /**
     * C-Grade einer Route anhand Route-ID 
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param integer 
     *
     * @return string 
     */
    
    public static function getCGradeFromRouteID($routeId)
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select(array('cg.grade AS c_grade'))
            ->from('#__act_trigger_calc AS t')
            ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round') 
            ->where($db->qn('t.id') .' = ' .(int)$routeId);

        $db->setQuery($query);
        return $db->loadResult();
    }


    /**
     * VR-Grad einer Route anhand Route-ID 
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param integer 
     *
     * @return string 
     */
    
    public static function getVrGradeFromRouteID($routeId)
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query 
            ->select(array('sg.grade AS s_grade'))
            ->from('#__act_route AS r')
            ->join('LEFT', '#__'.$grade_table.' AS sg ON sg.id_grade = r.settergrade') 
            ->where($db->qn('r.id') .' = ' .(int)$routeId);

        $db->setQuery($query);
        return $db->loadResult();
    }


    /**
     * Liste der Routengrade
     * Z. B für Selectfeld von Routengrden 
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param  
     *
     * @return  
     */

    public static function getSettergradeList()
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('grade', 'id_grade'))
            ->from('#__'.$grade_table.'')
			//->where($db->qn('routes_planning') . ' IN (' . implode(',', ArrayHelper::toInteger($planning)) . ')')
            ->where($db->qn('id_grade').' >=1');

        $db->setQuery($query);

        return $db->loadObjectList();
    }



    /**
     * Liste der Routengrade ohne Zwischengrade 
     * Z. B Übesicht Charts
     * Wird auch im Routeplanning verwendet!!!!!!!!!!!!!!!!!!!!!!!
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param  
     *
     * @return  
     */

    public static function getGradeListPlanning()
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
        
        $planning = array(1);

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('grade', 'id_grade', 'filter'))
            ->from('#__'.$grade_table.'')
			//->where($db->qn('routes_planning') . ' IN (' . implode(',', ArrayHelper::toInteger($planning)) . ')')
            ->where($db->qn('routes_planning'). ' = 1')
            ->where($db->qn('id_grade').' >=1');

        $db->setQuery($query);

        return $db->loadObjectList();
    }


    
    /**
     * Liste der Routengrade ohne Zwischengrade 
     * Z. B Übesicht Charts
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param  
     *
     * @return  
     */

    public static function getGradeListPlanningPercent()
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

        $planning = array(1);

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('grade', 'id_grade', 'filter'))
            ->from('#__'.$grade_table.'')
            ->where($db->qn('routes_planning_percent'). ' = 1')
            ->where($db->qn('id_grade').' >=1');

        $db->setQuery($query);

        return $db->loadObjectList();
    }


    /**
     * JSON-String der Farben.
     * Z. B Übesicht Charts
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param  
     *
     * @return  
     */
    public static function getGradeColorList()
    {
        $params      = JComponentHelper::getParams('com_act');

        self::getGradeListPlanning();
        $gradeList = GradeHelpersGrade::getGradeListPlanning();
    
        $color_array = [];
        foreach($gradeList AS $value) {
            $color = $params['color'.$value->filter.'grad'];
            array_push($color_array, $color);
        }

        return json_encode($color_array);
    
    }



    /**
     * JSON-String der Label
     * Z. B Übesicht Charts
     * je nach gültiger Tabelle (UIAA oder Franz usw)
     *
     * @param  
     *
     * @return  
     */

    public static function getGradeLabelList()
    {
        self::getGradeListPlanning();
        $gradeList = GradeHelpersGrade::getGradeListPlanning();
    
        $label = [];
        foreach($gradeList AS $value) {
            $grade = "g$value->id_grade";
            array_push($label, $value->grade);
        }
    
        return json_encode($label);

    }


    
     /**
     * Höchster und niedrigster Grade innheralb der Schwierigkeitstabelle
     * 
     * @param 
     *
     * @return  
     * Z.B- Array ( [0] => stdClass Object ( [min_id_grade] => 10 [max_id_grade] => 40 ) ) 
     */
    
    public static function getSettergradeListRange()
    {
        $params      = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('MIN(id_grade) AS min_id_grade', 'MAX(id_grade) AS max_id_grade'))
            ->from($db->qn('#__'.$grade_table.''))
            ->where($db->qn('id_grade') .'!=0');

        $db->setQuery($query);

        return $db->loadObjectList();
    }


    
     /**
     * Anzahl der Routen nach Routengrad - ganze Grade !!!!!!!!!!!!
     * WICHTIG nur ganze Grade 3,4,5,6 usw 
     * Keine 6- oder 6+ 
     */

    public static function getNumberOfRoutesByCompleteGrade()
    {
      $params    = JComponentHelper::getParams('com_act');
      $grade_table = $params['grade_table'];  // Tabelle der Schwierigkeitsgrade zu erhalten aus den Params

      $db = Factory::getDbo();
      $query = $db->getQuery(true);
      $query->select(array('COUNT(CASE WHEN cg.filter = 0  then 1 ELSE NULL END) as  GradeFilter0', // Grade ist_undefined
                           'COUNT(CASE WHEN cg.filter = 3  then 1 ELSE NULL END) as  GradeFilter3',
                           'COUNT(CASE WHEN cg.filter = 4  then 1 ELSE NULL END) as  GradeFilter4',
                           'COUNT(CASE WHEN cg.filter = 5  then 1 ELSE NULL END) as  GradeFilter5',
                           'COUNT(CASE WHEN cg.filter = 6  then 1 ELSE NULL END) as  GradeFilter6',
                           'COUNT(CASE WHEN cg.filter = 7  then 1 ELSE NULL END) as  GradeFilter7',
                           'COUNT(CASE WHEN cg.filter = 8  then 1 ELSE NULL END) as  GradeFilter8',
                           'COUNT(CASE WHEN cg.filter = 9  then 1 ELSE NULL END) as  GradeFilter9',
                           'COUNT(CASE WHEN cg.filter = 10 then 1 ELSE NULL END) as  GradeFilter10',
                           'COUNT(CASE WHEN cg.filter = 11 then 1 ELSE NULL END) as  GradeFilter11',
                           'COUNT(CASE WHEN cg.filter = 12 then 1 ELSE NULL END) as  GradeFilter12',
                           ) 
                    )
                    
            ->from('#__act_route AS a')
            ->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // Trigger TABLE
            ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round') // Convertierter Grad cg = C-Grade
            ->where('state IN (1,-1)');
            
       $db->setQuery($query);
       $result = $db->loadObject();
	   
       return $result; 
    }



        
     /**
     * Anzahl der Routen nach Routengrad - ganze Grade !!!!!!!!!!!!
     * WICHTIG nur ganze Grade 3,4,5,6 usw 
     * Keine 6- oder 6+ 
     */

     public static function getRoutesgradeFilter()
     {
       $params    = JComponentHelper::getParams('com_act');
       $grade_table = $params['grade_table'];  // Tabelle der Schwierigkeitsgrade zu erhalten aus den Params
 
       $db = Factory::getDbo();
       $query = $db->getQuery(true);
       $query->select(array('DISTINCT(filter)'))
                     
             ->from('#__'.$grade_table)
             ->where('routes_planning = 1');
             
        $db->setQuery($query);
        $result = $db->loadRowList();
        
        return $result; 
     }
    

    /**
     * Generiert das Merge-Schema aus der Datenbank
     * 
     * Diese Funktion liest aus der konfigurierten Grade-Tabelle alle Schwierigkeitsgrade aus,
     * die für die Routenplanung verwendet werden (routes_planning = 1) und gruppiert sie
     * nach ihrem merge_group Wert.
     * 
     * Funktionsweise:
     * 1. Lädt die Grade-Tabelle aus den Komponenten-Parametern
     * 2. Führt eine Datenbank-Abfrage aus mit GROUP_CONCAT um alle Grades zu sammeln
     * 3. Filtert nur Datensätze mit routes_planning = 1 (aktiv für Routenplanung)
     * 4. Gruppiert nach merge_group und sortiert nach id_grade (aufsteigende Schwierigkeit)
     * 5. Wandelt das Ergebnis in ein assoziatives Array um
     * 
     * Beispiel aus der Datenbank:
     * - Grade "5b" (id_grade=15) und "5b+" (id_grade=16) haben beide merge_group "5b/5b+"
     * - Diese werden zu einem Array-Eintrag: "5b/5b+" => ["5b", "5b+"]
     * - Grade "5a" (id_grade=14) hat merge_group "5a" 
     * - Wird zu: "5a" => ["5a"]
     * 
     * GROUP_CONCAT sammelt alle grade-Werte einer Gruppe kommasepariert
     * und sortiert sie dabei nach id_grade, damit die Reihenfolge stimmt.
     * 
     * @return array Das Merge-Schema im Format:
     *               [
     *                 "merge_group" => ["grade1", "grade2", ...],
     *                 ...
     *               ]
     *               
     *               Konkrete Ausgabe:
     *               [
     *                 "3" => ["3"],
     *                 "4a" => ["4a"],
     *                 "4b" => ["4b"],
     *                 "4c" => ["4c"],
     *                 "5a" => ["5a"],
     *                 "5b/5b+" => ["5b", "5b+"],
     *                 "5c/5c+" => ["5c", "5c+"],
     *                 "6a/6a+" => ["6a", "6a+"],
     *                 "6b/6b+" => ["6b", "6b+"],
     *                 "6c/6c+" => ["6c", "6c+"],
     *                 "7a/7a+" => ["7a", "7a+"],
     *                 "7b/7b+" => ["7b", "7b+"],
     *                 "7c/7c+" => ["7c", "7c+"],
     *                 "8a/8a+" => ["8a", "8a+"],
     *                 "8b/8b+" => ["8b", "8b+"],
     *                 ...
     *               ]
     */
    public static function getMergeScheme(): array
    {
        // Lade die Komponenten-Parameter um die konfigurierte Grade-Tabelle zu ermitteln
        $params = JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];

        $db = Factory::getDbo();
        
        // SQL-Query:
        // SELECT merge_group, GROUP_CONCAT(DISTINCT grade ORDER BY id_grade) AS grades
        // FROM #__grade_table
        // WHERE routes_planning = 1 AND merge_group IS NOT NULL
        // GROUP BY merge_group
        // ORDER BY id_grade ASC
        //
        // GROUP_CONCAT: Fasst alle grade-Werte einer merge_group zu einem String zusammen (kommasepariert)
        // DISTINCT: Verhindert doppelte Einträge (sollte nicht vorkommen, aber zur Sicherheit)
        // ORDER BY id_grade: Sortiert die Grades innerhalb der Gruppe nach Schwierigkeit
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('merge_group'),
                'GROUP_CONCAT(DISTINCT ' . $db->quoteName('grade') . ' ORDER BY ' . $db->quoteName('id_grade') . ') AS grades'
            ])
            ->from($db->quoteName('#__' . $grade_table))
            ->where($db->quoteName('routes_planning') . ' = 1')        // Nur Grades für Routenplanung
            ->where($db->quoteName('merge_group') . ' IS NOT NULL')    // Nur Grades mit merge_group
            ->group($db->quoteName('merge_group'))                      // Gruppiere nach merge_group
            ->order($db->quoteName('id_grade') . ' ASC');               // Sortiere Ergebnis nach Schwierigkeit
        
        $db->setQuery($query);
        $results = $db->loadObjectList();
        
        // Konvertiere die Datenbank-Resultate in das gewünschte Array-Format
        // Eingabe: [{"merge_group": "5b/5b+", "grades": "5b,5b+"}, ...]
        // Ausgabe: ["5b/5b+" => ["5b", "5b+"], ...]
        $mergeScheme = [];
        foreach ($results as $row) {
            // Wandle die kommaseparierte Grade-Liste in ein Array um
            // z.B. "5b,5b+" wird zu ["5b", "5b+"]
            // z.B. "5a" wird zu ["5a"]
            $mergeScheme[$row->merge_group] = explode(',', $row->grades);
        }
        
        return $mergeScheme;
    }


}

