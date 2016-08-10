<?php

return array(
    array(
        'title' => 'Menu Browse',
        'description' => 'Menu browse.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.browse-menu',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
    array(
        'title' => 'Facebook Quick Links',
        'description' => 'Facebook Quick Links.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.facebook-menu',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
    array(
        'title' => 'Flickr Quick Links',
        'description' => 'Flickr Quick Links.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.flickr-menu',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
    
    array(
        'title' => 'Picasa Quick Links',
        'description' => 'Picasa Quick Links.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.picasa-menu',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
    array(
        'title' => 'Instagram Quick Links',
        'description' => 'Instagram Quick Links.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.instagram-menu',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
	/*
    array(
        'title' => 'YFrog Quick Links',
        'description' => 'YFrog Quick Links.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.yfrog-menu',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
    */
    array(
        'title' => 'Media Browse',
        'description' => 'Media browse.',
        'category' => 'Media Importer',
        'type' => 'widget',
        'name' => 'ynmediaimporter.media-browse',
        'isPaginated' => false,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array('elements' => array( array(
                    'Hidden',
                    'title',
                    array('label' => 'Title','decorators'=>array('ViewHelper'))
                ), )),
    ),
);
?>