<?php

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$view = Zend_Registry::get('Zend_View');
$view -> addScriptPath(APPLICATION_PATH . '/application/modules/Ynmediaimporter/views/scripts');

ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL);

$request = $_REQUEST;
$format = isset($_REQUEST['format']) ? $_REQUEST['format'] : null;
$service = isset($_REQUEST['service']) ? $_REQUEST['service'] : 'facebook';
$cache = isset($_REQUEST['cache']) ? $_REQUEST['cache'] : 1;

if (isset($_GET['remove-cache']) && $_GET['remove-cache'])
{
    Ynmediaimporter::clearCache();
    unset($_GET['remove-cache']);
}
$provider = Ynmediaimporter::getProvider($service);

$data = array(
    'html' => '',
    'message' => ''
);

try
{
    list($items, $params, $media) = $provider -> getData($_GET, $cache);
    $arrItems = array();

    foreach ($items as $item) {
    	$t = $item;
    	$t['title'] = urlencode($t['title']);
    	array_push($arrItems, $t);
    }
    $view -> items = $arrItems;
    $view -> params = $params;
    $view -> item_count = count($items);
    $view -> userId = intval(Engine_Api::_() -> user() -> getViewer() -> getIdentity());

    if ('photo' == $media)
    {
        $script = 'index/__photos.tpl';
    }
    else
    {
        $script = 'index/__albums.tpl';
    }
    $data['html'] = $view -> render($script);

}
catch(Exception $e)
{
    $data['message'] = $e -> getMessage();
}
echo json_encode($data);
exit(0);
