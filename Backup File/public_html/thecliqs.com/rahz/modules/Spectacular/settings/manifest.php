<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'spectacular',
        'version' => '4.8.9p4',
        'path' => 'application/modules/Spectacular',
        'title' => 'Responsive Spectacular Theme',
        'description' => 'Responsive Spectacular Theme',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Spectacular/settings/install.php',
            'class' => 'Spectacular_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Spectacular',
            1 => 'application/themes/spectacular',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/spectacular.csv',
        ),
    ),
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Spectacular_Plugin_Core'
        ),
        array(
            'event' => 'onRenderLayoutDefaultSimple',
            'resource' => 'Spectacular_Plugin_Core',
        ),
    ),
    //Items ---------------------------------------------------------------------
    'items' => array(
        'spectacular_image',
        'spectacular_banner'
    )
);
?>