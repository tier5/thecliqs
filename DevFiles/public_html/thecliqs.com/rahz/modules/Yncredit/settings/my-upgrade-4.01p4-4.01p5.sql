UPDATE `engine4_core_modules` SET `version` = '4.01p5' WHERE `name` = 'yncredit';

-- Ultimatevideo Video actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES
('ynultimatevideo', 'ynultimatevideo_video_new', 'earn', 'Posting new %s video', '5', '{"route":"ynultimatevideo_general","action":"create"}');

UPDATE `engine4_activity_notificationtypes` SET `body` = 'You have just received {var:$credits} credits from {item:$subject}.{item:$object}' WHERE `type` = 'yncredit_receive' ;
UPDATE `engine4_activity_notificationtypes` SET `body` = 'You have just been debited {var:$credits} credits by {var:$site_name}.{item:$object}' WHERE `type` = 'yncredit_debit' ;

