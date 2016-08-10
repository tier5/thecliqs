UPDATE `engine4_core_modules` SET `version` = '4.1.1'  WHERE `name` = 'suggest';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` ,`module` ,`body` ,`is_request` ,`handler`) VALUES
('suggest_page',  'page',  '{item:$subject} has suggested to you a {item:$object:page}.',  '1',  'suggest.handler.request'),
('suggest_user',  'user',  '{item:$subject} has suggested to you a {item:$object:user}.',  '1',  'suggest.handler.request');