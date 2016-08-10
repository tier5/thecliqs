<?php
// YouNet Responsive Clean
return array(
 array(
    'title' => 'Grids Container',
    'description' => 'Grids Container',
    'category' => 'YouNet Responsive Clean',
    'type' => 'widget',
    'name' => 'ynresponsiveclean.grid',
    'requirements' => array(
      
    ),
    'adminForm'=>'Ynresponsiveclean_Form_Admin_Grid',
  ), 
  
  array(
    'title' => 'Grids Slide Container',
    'description' => 'Grids Slide Container',
    'category' => 'YouNet Responsive Clean',
    'type' => 'widget',
    'name' => 'ynresponsiveclean.gridslide',
    'requirements' => array(
    ),
    'autoEdit' => true,
    'adminForm'=>'Ynresponsiveclean_Form_Admin_GridsSlider',
  ), 
  
  array(
    'title' => 'Join Now',
    'description' => 'YouNet Responsive Clean Join Now',
    'category' => 'YouNet Responsive Clean',
    'type' => 'widget',
    'name' => 'ynresponsiveclean.join-now',
    'requirements' => array(
      
    ),
  ),
  array(
    'title' => 'Latest Shots',
    'description' => 'YouNet Responsive Clean Latest Shots',
    'category' => 'YouNet Responsive Clean',
    'type' => 'widget',
    'name' => 'ynresponsiveclean.latest-shots',
    'isPaginated' => true,
    'requirements' => array(
      
    ),
  ),
  array(
    'title' => 'Menu Main',
    'description' => 'YouNet Responsive Clean Menu Main',
    'category' => 'YouNet Responsive Clean',
    'type' => 'widget',
    'name' => 'ynresponsiveclean.menu-main',
    'requirements' => array(
       'header-footer',
    ),'adminForm' => array(
      'elements' => array(
        array(
          'select',
          'logo',
          array(
            'label' => 'Show Logo',
            'multiOptions' => array(
              '0' => 'No',
              '1' => 'Yes',
            ),
            'value' => '0',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Mini Menu',
    'description' => 'YouNet Responsive Clean Mini Menu',
    'category' => 'YouNet Responsive Clean',
    'type' => 'widget',
    'name' => 'ynresponsiveclean.mini-menu',
    'requirements' => array(
       'header-footer',
    ),
  ),
  array(
        'title' => 'Slider',
        'description' => 'Slider',
        'category' => 'YouNet Responsive Clean',
        'type' => 'widget',
        'name' => 'ynresponsiveclean.slider',
        'isPaginated' => true,
        'requirements' => array(),
        'adminForm' => 'Ynresponsiveclean_Form_Admin_Slider',
        'autoEdit' => true
    ),

    array(
        'title' => 'Slider Full',
        'description' => 'Slider Full (only for width 100% screen landing page)',
        'category' => 'YouNet Responsive Clean',
        'type' => 'widget',
        'name' => 'ynresponsiveclean.sliderfull',
        'isPaginated' => true,
        'requirements' => array(),
        'adminForm' => 'Ynresponsiveclean_Form_Admin_SliderFull',
        'autoEdit' => true
    ),
    array(
        'title' => 'Footer Menu',
        'description' => 'YouNet Responsive Clean Footer Menu',
        'category' => 'YouNet Responsive Clean',
        'type' => 'widget',
        'name' => 'ynresponsiveclean.menu-footer',
        'requirements' => array()
    )
 );