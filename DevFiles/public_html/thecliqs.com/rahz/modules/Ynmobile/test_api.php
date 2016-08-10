<?php

$api = Engine_Api::_('comment','ynmobile');

$data = $api->listallcomments(array('sItemType'=>'activity_action','iItemId'=>'3868'));

return $data;

