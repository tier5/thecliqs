<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2012-08-16 16:38 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

return array(
    array(
        'title' => 'Logo For A Day',
        'description' => 'Displays a logo in defined days.',
        'category' => 'Daylogo',
        'type' => 'widget',
        'name' => 'daylogo.day-logo',
        'adminForm' => 'Daylogo_Form_Admin_Widget_Sitelogo',
        'requirements' => array(
            'no-subject',
        ),
    ),
);