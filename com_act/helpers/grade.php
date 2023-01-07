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
    


}

