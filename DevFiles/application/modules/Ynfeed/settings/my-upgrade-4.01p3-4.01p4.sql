UPDATE `engine4_core_modules` SET `version` = '4.01p4' WHERE `name` = 'ynfeed';

INSERT IGNORE INTO `engine4_ynfeed_contents` ( `module_name`, `filter_type`, `resource_title`, `photo_id`, `order`, `show`, `default`, `content_tab`) VALUES
('ynvideochannel', 'ynvideochannel', 'Video Channel', '0', '18', '1', '0', '1'),
('ynmusic', 'ynmusic', 'Social Music', '0', '19', '1', '0', '1'),
('ynultimatevideo', 'ynultimatevideo', 'Ultimate Video', '0', '20', '1', '0', '1'),
('ynlisting', 'ynlisting', 'Listings', '0', '21', '1', '0', '1'),
('ynmultilisting', 'ynmultilisting', 'Multiple Listings', '0', '22', '1', '0', '1'),
('ynfeedback', 'ynfeedback', 'Feedback', '0', '23', '1', '0', '1'),
('ynbusinesspages', 'ynbusinesspages', 'Business Pages', '0', '24', '1', '0', '1'),
('ynwiki', 'ynwiki', 'Wiki', '0', '25', '1', '0', '1'),
('ynidea', 'ynidea', 'Ideas', '0', '26', '1', '0', '1'),
('socialstore', 'socialstore', 'Store', '0', '27', '1', '0', '1'),
('groupbuy', 'groupbuy', 'Group Buy', '0', '28', '1', '0', '1'),
('ynfundraising', 'ynfundraising', 'Fundraising', '0', '29', '1', '0', '1'),
('ynauction', 'ynauction', 'Auction', '0', '30', '1', '0', '1');
