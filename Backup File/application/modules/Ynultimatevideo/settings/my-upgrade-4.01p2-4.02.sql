UPDATE `engine4_core_modules` SET `version` = '4.02' where 'name' = 'ynultimatevideo';

INSERT IGNORE INTO `engine4_ynultimatevideo_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, 1, NULL, 34, 0, 'All Categories','1'),
(2, 0, 1, 32, 33, 1, 'Animation','2'),
(3, 0, 1, 30, 31, 1, 'Arts & Design','3'),
(4, 0, 1, 28, 29, 1, 'Cameras & Techniques','4'),
(5, 0, 1, 26, 27, 1, 'Comedy','5'),
(6, 0, 1, 24, 25, 1, 'Documentary','6'),
(7, 0, 1, 22, 23, 1, 'Experimental','7'),
(8, 0, 1, 20, 21, 1, 'Fashion','8'),
(9, 0, 1, 18, 19, 1, 'Food','9'),
(10, 0, 1, 16, 17, 1, 'Instructionals','10'),
(11, 0, 1, 14, 15, 1, 'Music','11'),
(12, 0, 1, 12, 13, 1, 'Narrative','12'),
(13, 0, 1, 10, 11, 1, 'Personal','13'),
(14, 0, 1, 8, 9, 1, 'Reporting & Journalism','14'),
(15, 0, 1, 6, 7, 1, 'Sports','15'),
(16, 0, 1, 4, 5, 1, 'Talks','16'),
(17, 0, 1, 2, 3, 1, 'Travel','17');

INSERT IGNORE INTO `engine4_ynultimatevideo_video_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1, 1, 'Default Type', 0),
(2, 1, 'Animation', 999),
(3, 1, 'Arts & Design', 999),
(4, 1, 'Cameras & Techniques', 999),
(5, 1, 'Comedy', 999),
(6, 1, 'Documentary', 999),
(7, 1, 'Experimental', 999),
(8, 1, 'Fashion', 999),
(9, 1, 'Food', 999),
(10, 1, 'Instructionals', 999),
(11, 1, 'Music', 999),
(12, 1, 'Narrative', 999),
(13, 1, 'Personal', 999),
(14, 1, 'Reporting & Journalism', 999),
(15, 1, 'Sports', 999),
(16, 1, 'Talks', 999),
(17, 1, 'Travel', 999);