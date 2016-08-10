--
-- Update module Weather
--

UPDATE `engine4_core_modules` SET `version` = '4.1.1'  WHERE `name` = 'weather';

UPDATE `engine4_core_settings` SET `value` = IF(`value` <> 'en_gb', 'us', 'si') WHERE `name` = 'weather.unit.system';