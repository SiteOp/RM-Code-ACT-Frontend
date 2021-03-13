DELIMITER $$
CREATE  PROCEDURE `SetCalcGradeNew`()
    COMMENT 'Neuberechnung des C-Grade.'
BEGIN
 DECLARE count INT DEFAULT 0;
 WHILE count < 10000 DO
  UPDATE g9p5k_act_trigger_calc as t
	SET t.calc_grade = 
		( SELECT (CASE WHEN t.sum_cgrade IS NULL THEN r.settergrade ELSE (round((((r.settergrade * 1.99)  + t.sum_cgrade) / (1.99 +  t.count_cgrade)),3)) END)
			FROM g9p5k_act_trigger_calc as t
			RIGHT OUTER JOIN g9p5k_act_route AS r on r.id = t.id
			WHERE t.id = count
		),
		t.calc_grade_round = 
		( SELECT (CASE WHEN t.sum_cgrade IS NULL THEN r.settergrade ELSE (round((((r.settergrade * 1.99)  + t.sum_cgrade) / (1.99 +  t.count_cgrade)),0)) END)
			FROM g9p5k_act_trigger_calc as t
			RIGHT OUTER JOIN g9p5k_act_route AS r on r.id = t.id
			WHERE t.id = count
		)

    WHERE t.id = count;
   SET count = count + 1;
 END WHILE;
END$$
DELIMITER ;