DELIMITER ;;
CREATE TRIGGER `trigger_A_into` AFTER INSERT ON `g9p5k_act_comment` FOR EACH ROW
BEGIN
-- Trigger berechnet beim anlegen eines Kommentares:
-- Anzahl Sterne
-- AVG Sterne
-- Anzahl User mit C-Grade
-- Summe C-Grade

    DECLARE sum_cgrade   INTEGER DEFAULT 0;
    DECLARE count_cgrade INTEGER DEFAULT 0;
    DECLARE count_stars  INTEGER DEFAULT 0;
    DECLARE avg_stars    FLOAT   DEFAULT 0;

    SELECT COUNT(CASE WHEN c.myroutegrade = 0 THEN NULL ELSE 1 END),
           SUM(c.myroutegrade),
           COUNT(CASE WHEN c.stars = 0 THEN NULL ELSE 1 END),
           AVG(CASE WHEN c.stars = 0 THEN NULL ELSE c.stars END)

    FROM   g9p5k_act_comment as c
    WHERE  c.route = new.route
    INTO   count_cgrade, sum_cgrade, count_stars, avg_stars;

    UPDATE g9p5k_act_trigger_calc as t
    SET    t.sum_cgrade = sum_cgrade, t.count_cgrade = count_cgrade, t.avg_stars = avg_stars, t.count_stars = count_stars
    WHERE  t.id = new.route;
END;;


CREATE TRIGGER `trigger_B_into` AFTER INSERT ON `g9p5k_act_comment` FOR EACH ROW
/* 
 * Trigger berechnet beim anlegen eines Kommentares:
 * den C-Grade anhand der Werte des 1. Triggers
 * und des Settergrade aus der Tabelle #___act_route
*/
BEGIN
    DECLARE calc_grade FLOAT DEFAULT 0;

    SELECT (
        CASE WHEN r.settergrade = 0 
        THEN (round(((t.sum_cgrade) / t.count_cgrade),3)) 
        ELSE
            CASE WHEN t.sum_cgrade = 0 THEN r.settergrade 
            ELSE (round((((r.settergrade * 1.99)  + t.sum_cgrade) / (1.99 +  t.count_cgrade)),3)) 
            END
        END
        )
    FROM g9p5k_act_trigger_calc as t
    RIGHT OUTER JOIN g9p5k_act_route AS r on r.id = t.id
    
    WHERE t.id = new.route 
    INTO calc_grade;
    
    UPDATE g9p5k_act_trigger_calc as t
    SET  t.calc_grade = calc_grade, 
         t.calc_grade_round = round(calc_grade,0)
    WHERE t.id = new.route;
END;;


CREATE TRIGGER trigger_C_update AFTER UPDATE ON g9p5k_act_comment FOR EACH ROW 
/* Update 1 */
/*
 * Trigger berechnet beim ändern eines Kommentares:
 * Anzahl Sterne
 * AVG Sterne
 * Anzahl User mit C-Grade
 * Summe C-Grade
*/
BEGIN
    DECLARE sum_cgrade   INTEGER DEFAULT 0;
    DECLARE count_cgrade INTEGER DEFAULT 0;
    DECLARE count_stars  INTEGER DEFAULT 0;
    DECLARE avg_stars    FLOAT   DEFAULT 0;

    SELECT COUNT(CASE WHEN c.myroutegrade = 0 THEN NULL ELSE 1 END),
           SUM(c.myroutegrade),
           COUNT(CASE WHEN c.stars = 0 THEN NULL ELSE 1 END),
           AVG(CASE WHEN c.stars = 0 THEN NULL ELSE c.stars END)

    FROM   g9p5k_act_comment as c
    WHERE  c.route = new.route
    INTO   count_cgrade, sum_cgrade, count_stars, avg_stars;

    UPDATE g9p5k_act_trigger_calc as t
    SET    t.sum_cgrade = sum_cgrade, t.count_cgrade = count_cgrade, t.avg_stars = avg_stars, t.count_stars = count_stars
    WHERE  t.id = new.route;
END;;


CREATE TRIGGER `trigger_D_update` AFTER UPDATE ON `g9p5k_act_comment` FOR EACH ROW
/*  Update 2 */
/*
 * Trigger berechnet beim ändern eines Kommentares:
 * den C-Grade anhand der Werte des 1. Triggers
 * und des Settergrade aus der Tabelle #___act_route
*/
BEGIN
    DECLARE calc_grade FLOAT DEFAULT 0;

    SELECT (
        CASE WHEN r.settergrade = 0 
        THEN (round(((t.sum_cgrade) / t.count_cgrade),3)) 
        ELSE
            CASE WHEN t.sum_cgrade = 0 THEN r.settergrade 
            ELSE (round((((r.settergrade * 1.99)  + t.sum_cgrade) / (1.99 +  t.count_cgrade)),3)) 
            END
        END
        )
    FROM g9p5k_act_trigger_calc as t
    RIGHT OUTER JOIN g9p5k_act_route AS r on r.id = t.id
    
    WHERE t.id = new.route 
    INTO calc_grade;
    
    UPDATE g9p5k_act_trigger_calc as t
    SET  t.calc_grade = calc_grade, 
         t.calc_grade_round = round(calc_grade,0)
    WHERE t.id = new.route;
END;;



/*  DELETE COMMENT */

CREATE TRIGGER `trigger_E_delete` AFTER DELETE ON `g9p5k_act_comment` FOR EACH ROW 
/*  Delete 1 */
/*
 * Trigger berechnet beim löschen eines Kommentares:
 * Anzahl Sterne
 * AVG Sterne
 * Anzahl User mit C-Grade
 * Summe C-Grade
*/
BEGIN
    DECLARE sum_cgrade   INTEGER DEFAULT 0;
    DECLARE count_cgrade INTEGER DEFAULT 0;
    DECLARE count_stars  INTEGER DEFAULT 0;
    DECLARE avg_stars    FLOAT   DEFAULT 0;

    SELECT SUM(c.myroutegrade),
           COUNT(CASE WHEN c.myroutegrade = 0 THEN NULL ELSE 1 END),
           COUNT(CASE WHEN c.stars = 0 THEN NULL ELSE 1 END),
           AVG(CASE WHEN c.stars = 0 THEN NULL ELSE c.stars END)

    FROM   g9p5k_act_comment as c 
    WHERE  c.route = old.route
    INTO   sum_cgrade, count_cgrade, count_stars, avg_stars;

    UPDATE g9p5k_act_trigger_calc as t
    SET    t.sum_cgrade = sum_cgrade, t.count_cgrade = count_cgrade, t.avg_stars = avg_stars, t.count_stars = count_stars
    WHERE  t.id = old.route;
END;;


CREATE TRIGGER `trigger_F_delete` AFTER DELETE ON `g9p5k_act_comment` FOR EACH ROW 
/* Delete 2 */
/*
 * Trigger berechnet beim löschen eines Kommentares:
 * den C-Grade anhand der Werte des 1. Triggers
 * und des Settergrade aus der Tabelle #___act_route
*/
BEGIN
    DECLARE calc_grade FLOAT DEFAULT 0;

    SELECT (
        CASE WHEN r.settergrade = 0 
        THEN (round(((t.sum_cgrade) / t.count_cgrade),3))
        ELSE
            CASE WHEN t.sum_cgrade = 0 
            THEN r.settergrade 
            ELSE (round((((r.settergrade * 1.99)  + t.sum_cgrade) / (1.99 +  t.count_cgrade)),0))
            END
        END
    )
    FROM g9p5k_act_trigger_calc as t
    RIGHT OUTER JOIN g9p5k_act_route AS r on r.id = t.id
    WHERE t.id = old.route INTO calc_grade;

    UPDATE g9p5k_act_trigger_calc as t
    SET  t.calc_grade = calc_grade, t.calc_grade_round = round(calc_grade,0)
    WHERE t.id = old.route;
END;;



CREATE TRIGGER trigger_A_insert_trigger_calc AFTER INSERT ON g9p5k_act_route FOR EACH ROW 
/*
 * Dieser Trigger legt innerhalb der Tabelle #__act_trigger_calc 
 * die neue ID (Route) an
 * und setzt als Default den Settergrade
*/
BEGIN
    REPLACE INTO g9p5k_act_trigger_calc (id,calc_grade)
    VALUES(new.id, new.settergrade);
END;;



CREATE TRIGGER trigger_B_update_grade AFTER UPDATE ON `g9p5k_act_route` FOR EACH ROW 
/*
 * Dieser Trigger korrigiert den C-Grade 
 * wenn bei der Route der Grad geändert werden sollte
 * Vermutlich eher selten aber Sicherheitshalber!
*/ 
BEGIN
    DECLARE calc_grade FLOAT DEFAULT 0;

    SELECT (
    CASE WHEN r.settergrade = 0 
        THEN (round(((t.sum_cgrade) / t.count_cgrade),3))
        ELSE
            CASE WHEN t.sum_cgrade IS NULL 
            THEN r.settergrade 
            ELSE (round((((r.settergrade * 1.99)  + t.sum_cgrade) / (1.99 +  t.count_cgrade)),0)) 
            END
        END
    )
    FROM g9p5k_act_trigger_calc as t
    RIGHT OUTER JOIN g9p5k_act_route AS r on r.id = t.id
    WHERE t.id = new.id INTO calc_grade;

    UPDATE g9p5k_act_trigger_calc as t
    SET   t.calc_grade = calc_grade, 
          t.calc_grade_round = round(calc_grade,0)
    WHERE t.id = new.id;
END;;


CREATE TRIGGER trigger_Comment_update BEFORE UPDATE ON g9p5k_act_route FOR EACH ROW BEGIN
/*
 * Dieser Trigger wird beim ändern des Status einer Route ausgelöst 
 * Alle Kommentare auf diese Route erhalten den selben Status wie die Route
 * Als Ausnahme gilt der Routenstatus -1 (Kommt raus) hier wird Status 1 (Öffentlich) gesetzt
 * Route State 2 = Kommentar State 2
 * Route State 1 = Kommentar State 1
*/
 DECLARE state INTEGER DEFAULT 2;   
 
    IF new.state = -1 THEN 
        UPDATE g9p5k_act_comment as c
        SET c.state = 1
        WHERE c.route = old.id;
    ELSE 
         UPDATE g9p5k_act_comment as c
        SET c.state = new.state
        WHERE c.route = old.id;
    END IF;
END;;

DELIMITER ;
