<?php
defined('_ENGINE') or die('Access Denied');
return array (
  'default_backend' => 'File',
  'frontend' => 
  array (
    'core' => 
    array (
      'automatic_serialization' => true,
      'cache_id_prefix' => 'Engine4_',
      'lifetime' => '60',
      'caching' => true,
      'gzip' => true,
    ),
  ),
  'backend' => 
  array (
    'File' => 
    array (
      'file_locking' => true,
      'cache_dir' => '/home/thecliqs/public_html/temporary/cache',
    ),
  ),
  'default_file_path' => '/home/thecliqs/public_html/temporary/cache',
); ?>