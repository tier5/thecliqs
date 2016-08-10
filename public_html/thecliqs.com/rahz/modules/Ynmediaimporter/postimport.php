<?php

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$view = Zend_Registry::get('Zend_View');

ini_set('display_startup_errors',1);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$request = $_POST;

$json = $_POST['json'];
$items = (array)json_decode($json, 1);
$photo = 0;
$photos = array();
$albums = array();
$photosId = array();
$albumsId = array();
foreach ($items as $item)
{
    $data = json_decode($item['data'], 1);
    $media = $item['media'];
    $provider = $item['provider'];
    if ('photo' == $media)
    {
        $photos[] = $data;
    }
    else
    {
        $albums[] = $data;
        $rows = Ynmediaimporter::getProvider($provider) -> getAllPhoto(array(
            'media' => $media,
            'aid' => $data['aid'],
            'media_parent'=>$data['media_parent'],
            'photo_count' => $data['photo_count'],
        ));

        foreach ($rows as $item)
        {
            $photos[] = $item;
        }
    }
}

$table = Engine_Api::_() -> getDbTable('Nodes', 'Ynmediaimporter');
$schedulerId = 0;
$tableName = $table -> info('name');
$db = $table -> getAdapter();
$sql = "INSERT IGNORE INTO $tableName (
                nid, user_id, owner_id,owner_type, id, uid, aid, media, provider,
                photo_count,status,title, src_thumb, src_small,src_medium,src_big, description
        ) VALUES";

$pieces = array();
$status = 0;
$userId = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
$ownerId = $userId;
$ownerType = 'user';

$db -> delete($tableName, array(
    'user_id=?' => $userId,
    'scheduler_id=?' => 0
));

$flag = 0;

foreach ($photos as $item)
{
    $photosId[] = $item['nid'];
    $pieces[] = '(' . implode(',', array_map(array(
        $db,
        'quote'
    ), array(
        $item['nid'],
        $userId,
        $ownerId,
        $ownerType,
        $item['id'],
        $item['uid'],
        $item['aid'],
        $item['media'],
        $item['provider'],
        $item['photo_count'],
        $item['status'],
        $item['title'],
        $item['src_thumb'],
        $item['src_small'],
        $item['src_medium'],
        $item['src_big'],
        $item['description']
    ))) . ')';
}

foreach ($albums as $item)
{
    $albumsId[] = $item['nid'];
    $pieces[] = '(' . implode(',', array_map(array(
        $db,
        'quote'
    ), array(
        $item['nid'],
        $userId,
        $ownerId,
        $ownerType,
        $item['id'],
        $item['uid'],
        $item['aid'],
        $item['media'],
        $item['provider'],
        $item['photo_count'],
        $item['status'],
        urldecode($item['title']),
        $item['src_thumb'],
        $item['src_small'],
        $item['src_medium'],
        $item['src_big'],
        $item['description']
    ))) . ')';
}
$sql .= implode(',', $pieces);
Ynmediaimporter::log($sql, 'query insert');
$db -> query($sql);
$numphoto = intval($db -> fetchOne("select count(*) from $tableName where user_id=$userId and scheduler_id=0 and media='photo'"));

$count_photo = count($photosId);
$count_album = count($albumsId);
$message = '';

if ($numphoto == 0)
{
    $message = $view -> translate('It seem all selected photos is set to queue. So the current request will be canceled.');
}
else
if ($count_album && $count_photo)
{
    $message = $view -> translate('Import <strong>%s</strong> photos in <strong>%s</strong> album(s).', $count_photo, $count_album);
}
else
if ($count_photo)
{
    $message = $view -> translate('Import <strong>%s</strong> photo(s).', $count_photo);
}
else
{
    $message = $view -> tranlsate('Sorry, Your request can not be executed.');
}

echo json_encode(array(
    'scheduler' => $schedulerId,
    'photos' => $photosId,
    'numphoto'=>$numphoto,
    'albums' => $albumsId,
    'message' => $message,
    'photo_count' => $count_photo,
    'album_count' => $count_album,
));
