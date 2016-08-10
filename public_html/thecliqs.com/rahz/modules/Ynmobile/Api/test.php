<?php

$test = '<img src="/projects/se483/application/modules/Blog/externals/images/nophoto_blog_thumb_icon.png" alt="" class="thumb_icon item_photo_blog item_nophoto " />';

$result = preg_match("#src=\"([^\"]+)\"#", $test, $match);

var_dump($match);
