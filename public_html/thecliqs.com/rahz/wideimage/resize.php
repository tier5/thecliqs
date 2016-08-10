<?php

require_once 'lib/WideImage.php';

$src =  $_REQUEST['src'];
$w =  $_REQUEST['w'];
$h = $_REQUEST['h'];

$src =  urldecode($src);

$img = WideImage::load($src);

@ob_start('ob_gzhandler');

$resize =  $img->resize($w, $h, 'outside','any')->crop('center','middle', $w, $h);

header('content-type: image/jpeg');

echo $resize;