DROP TABLE IF EXISTS `#__act_route`;
DROP TABLE IF EXISTS `#__act_comment`;
DROP TABLE IF EXISTS `#__act_setter`;
DROP TABLE IF EXISTS `#__act_color`;
DROP TABLE IF EXISTS `#__act_line`;
DROP TABLE IF EXISTS `#__act_grade`;
DROP TABLE IF EXISTS `#__act_sponsor`;
DROP TABLE IF EXISTS `#__act_sector`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_act.%');