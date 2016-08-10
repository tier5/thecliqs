<?php

$url  = $_REQUEST['link'];

file_get_contents($url);


echo $url;