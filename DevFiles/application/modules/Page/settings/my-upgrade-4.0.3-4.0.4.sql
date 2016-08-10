UPDATE `engine4_core_modules` SET `version` = '4.0.4'  WHERE `name` = 'page';

INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) VALUES
('pagediscussion', 'pagediscussion.profile-discussion', 15, '{"title":"PAGEDISCUSSION_TABITEM", "titleCount":true}');