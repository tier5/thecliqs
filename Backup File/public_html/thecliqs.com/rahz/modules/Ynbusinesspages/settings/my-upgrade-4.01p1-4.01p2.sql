INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynbusinesspages_business_never_expire', 'ynbusinesspages', 'The business {item:$object} has just been set to never expire.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynbusinesspages_business_add_review', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_expired', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_never_expire', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_unclaimed', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_noticeexpired', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[time]');

UPDATE `engine4_core_menuitems` SET  `label` =  'YN - Business ' WHERE `engine4_core_menuitems`.`name` = 'core_admin_main_plugins_ynbusinesspages';
UPDATE  `engine4_core_menuitems` SET  `label` =  'Business' WHERE `engine4_core_menuitems`.`name` = 'core_main_ynbusinesspages';
DELETE FROM  `engine4_core_content` WHERE  `type` =  'widget' AND  `name` LIKE 'ynbusinesspages.business-profile-options';
