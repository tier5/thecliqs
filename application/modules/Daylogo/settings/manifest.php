<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2012-08-16 16:37 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */
 
// 02.03.2013 - TrioxX

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'daylogo',
    'version' => '4.2.1p1',
    'path' => 'application/modules/Daylogo',
    'title' => 'Day-logo',
    'description' => 'Day-logo',
    'author' => 'Hire-Experts LLC',
    'meta' =>
    array (
      'title' => 'Day-logo Plugin',
      'description' => 'Day-logo Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      ),
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'enable',
      'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Daylogo/settings/install.php',
      'class' => 'Daylogo_Installer',
    ),
    'directories' => array(
      'application/modules/Daylogo',
    ),
    'files' => array(
      'application/languages/en/daylogo.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------

  'items' => array(
    'logo'
  ),
  // Routes --------------------------------------------------------------------

  'routes' => array(
    'daylogo_admin_index' => array(
      'route' => 'admin/daylogo/:controller/:action/*',
      'defaults' => array(
        'module' => 'daylogo',
        'controller' => 'admin-index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|create|upload-photo|remove-photo)',
        'controller' => 'admin-index',
      )
    ),

    'daylogo_manage' => array(
      'route' => 'admin/daylogo/manage/:action/:logo_id',
      'defaults' => array(
        'module' => 'daylogo',
        'controller' => 'admin-manage',
        'action' => 'enable',
        'logo_id' => 0,
      ),
      'reqs' => array(
        'logo_id' => '\d+',
        'action' => '(enable|disable|edit|remove)',
      )
    ),

    'daylogo_default' => array(
      'route' => 'daylogo/:action/*',
      'defaults' => array(
        'module' => 'daylogo',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|preview)',
      )
    ),

  ),
);