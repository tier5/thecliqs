<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Responsive Spectacular Theme - Footer Text',
        'description' => 'You can place this widget in the footer and can set text accordingly from the ‘Language Manager’ under ‘Layout’ section available in the admin panel of your site. For more detail, please read FAQ section from Admin Panel => Responsive Spectacular Theme => FAQs',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.homepage-footertext',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_signup_popup_footer',
                    array(
                        'label' => "Do you want Signup popup when create account button is clicked on this widget?",
                        'multiOptions' => array(
                            1 => 'Yes, show Signup popup',
                            0 => 'No, do not show Signup popup'
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Footer Menu',
        'description' => 'Shows the site-wide footer menu. You can edit its contents in your menu editor.',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.menu-footer',
        'requirements' => array(
            'header-footer',
        ),
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Landing Search',
        'description' => 'Displays the Advanced Search Box on the landing page. [Dependent on Advanced Search Plugin, if you are not having this plugin global search box will be displayed.]',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.landing-search',
        'adminForm' => 'Spectacular_Form_Admin_Widget_Search',
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Banner Images',
        'description' => 'Displays the Banner Images uploaded by you. This widget can be placed on any widgetized page.',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.banner-images',
        'adminForm' => 'Spectacular_Form_Admin_Widget_BannerContent',
        'autoEdit' => 'true'
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Navigation Tabs',
        'description' => "Displays the site wide navigation menus of your website. This widget should be placed in header.",
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.navigation',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
        ),
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Main Menu',
        'description' => 'Shows the site-wide main menu. You can edit its contents in your menu editor.',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'seaocore.browse-menu-main',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'max',
                    array(
                        'description' => "How many menus do you want to show under the 'Browse' drop-down?",
                        'value' => 20
                    )
                )))
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Landing Page Images',
        'description' => 'Displays multiple images uploaded by you on the Landing Page.',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.images',
        'adminForm' => 'Spectacular_Form_Admin_Widget_Content',
        'autoEdit' => 'true'
    ),
    array(
        'title' => 'Responsive Spectacular Theme - Search Box',
        'description' => 'Displays the Advanced Search box in the header. [Dependent on Advanced Search Plugin, if your are not having this plugin global search box will be displayed.]',
        'category' => 'SEAO - Responsive Spectacular Theme',
        'type' => 'widget',
        'name' => 'spectacular.search-box',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'spectacular_search_width',
                    array(
                        'label' => 'Enter width for searchbox.',
                        'value' => 275,
                    )
                ),
            ),
        ),
    ),
);
?>