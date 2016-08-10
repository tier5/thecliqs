<?php return array (
  'package' => 
  array (
    'name' => 'yncontainer-advanced',
    'type' => 'widget',
    'version' => '4.01',
    'path' => 'application/widgets/yncontainer-advanced',
    'title' => 'Advanced Widget Container',
    'author' => 'YouNet Company',
    'canHaveChildren' => true,
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'directories' => 
    array (
      0 => 'application/widgets/yncontainer-advanced',
      
    ),
    'files'=>array(
        0 => 'application/modules/Core/views/scripts/admin-content/index.tpl',
    )
  ),
  'canHaveChildren' => true,
  'type' => 'widget',
  'category' => 'Core',
  'special' => 1,
  'name' => 'yncontainer-advanced',  
  'childAreaDescription' => 'Any other blocks you drop inside it will become in a row.',
  'version' => '4.01',
  'title' => 'Advanced Widget Container',
  'autoEdit' => true,
  'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Text',
          'separate_width',
          array(
            'label' => 'Sepecify width of each block from left to right, separate by ";". example: 100px;200px',
          )
        ),
        array(
          'Text',
          'padding_width',
          array(
            'label' => 'Padding each widget',
            'value'=>'10px',
          )
        ),
      )
    ),
  
); ?>