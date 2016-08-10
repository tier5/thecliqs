<?php

try
{
    $application = Engine_Api::getInstance()->getApplication();
    $application->getBootstrap()->bootstrap('layout');
    $application->getBootstrap()->bootstrap('translate');
    $application->getBootstrap()->bootstrap('locale');
    
    $api = Engine_Api::_() -> ynfbpp();
    $type = $_REQUEST['match_type'];
    $id = $_REQUEST['match_id'];

    echo $api -> getJsonDataAction($type, $id);

}
catch(Exception $e)
{
    echo Zend_Json::encode(array(
        'match_type'=>"",
        'match_id'=> "",
        'error'=>'',
        'message'=>$e->getMessage(),
    ));
}
