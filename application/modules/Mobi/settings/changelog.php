<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: changelog.php 10267 2014-06-10 00:55:28Z lucas $
 * @author     Charlotte
 */
return array(
  '4.8.5' => array(
    'layouts/scripts/default-simple.tpl' => 'Reduced the dependence on _ENGINE_SSL',
    'layouts/scripts/default.tpl' => 'Reduced the dependence on _ENGINE_SSL',
    'Plugin/Menus.php' => 'Added all mobile profile options',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.8.3-4.8.4.sql' => 'Added all mobile profile options',
    'settings/my.sql' => 'Incremented version',
    'settings/changelog.php' => 'Incremented version',
  ),
  '4.8.0' => array(
    'externals/styles/mobile.css' => 'Added styles for link image previews',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.7.0' => array(
    'Bootstrap.php' => 'Fixed a warning',
    'settings/my.sql' => 'Incremented version',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
  ),
  '4.5.0' => array(
    'layouts/scripts/default-simple.tpl' => 'Fixed incorrect stylesheet issue; added javascript property',
    'layouts/scripts/default.tpl' => 'Added javascript property',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.2.2' => array(
    'layouts/scripts/default-simple.tpl' => 'Upgrading to MooTools 1.4',
    'layouts/scripts/default.tpl' => 'Upgrading to MooTools 1.4',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.2.0' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.8' => array(
    'controllers/IndexController.php' => 'Removed deprecated method calls',
    'externals/.htaccess' => 'Updated with far-future expires headers for static resources',
    'Plugin/Menus.php' => 'Removed deprecated routes',
    'settings/changelog.php' => 'Incremented version',
    'settings/install.php' => 'Reformatted code',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'widgets/mobi-footer/Controller.php' => 'Added optional built-in affiliate banner',
    'widgets/mobi-footer/index.tpl' => 'Added optional built-in affiliate banner',
  ),
  '4.1.7' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'widgets/mobi-menu-main/index.tpl' => 'Fixed issue with active class',
  ),
  '4.1.6' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.5p1' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'widgets/mobi-profile-options/index.tpl' => 'Fixed issue with profile page not rendering',
  ),
  '4.1.5' => array(
    'Api/Core.php' => 'Fixed notices',
    'controllers/IndexController.php' => 'Fixed issues with member home page being accessible by the public',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/browse/browse.tpl' => 'Fixed notices',
    'widgets/mobi-menu-main/index.tpl' => 'Removed short php tags; Fixed notices being logged',
    'widgets/mobi-profile-options/index.tpl' => 'Fixed notices being logged',
    'widgets/mobi-switch/index.tpl' => 'Removed short php tags',
  ),
) ?>
