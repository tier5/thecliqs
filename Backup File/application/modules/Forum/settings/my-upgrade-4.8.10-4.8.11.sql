UPDATE `engine4_activity_notificationtypes` SET
`body`='{item:$subject} has {item:$postGuid:posted} on a {item:$object:forum topic} you created.'
WHERE `type`= 'forum_topic_response';


UPDATE `engine4_activity_notificationtypes` SET
`body`= '{item:$subject} has {item:$postGuid:posted} on a {item:$object:forum topic} posted on.'
WHERE `type`= 'forum_topic_reply';
