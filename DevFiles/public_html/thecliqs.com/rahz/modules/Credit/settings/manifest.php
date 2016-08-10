<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 04.01.12 15:28 TeaJay $
 * @author     Taalay
 */

return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'credit',
    'version' => '4.2.7',
    'path' => 'application/modules/Credit',
    'repository' => '',
    'title' => 'Credit',
    'description' => 'Hire-Experts Credit Plugin',
    'author' => 'Hire-Experts LLC',
    'meta' =>
    array (
      'title' => 'Credits Plugin',
      'description' => 'Hire-Experts Credits Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'disable',
      4 => 'enable',
    ),
    'callback' => array(
      'path' => 'application/modules/Credit/settings/install.php',
      'class' => 'Credit_Installer',
    ),
    'directories' =>
    array (
      'application/modules/Credit',
    ),
    'files' => array(
      'application/languages/en/credit.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'credit_order',
    'credit_payment',
    'credit_balance'
  ),
  // Content -------------------------------------------------------------------
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onItemCreateAfter',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onUserLoginAfter',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onInviterSendInvite',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onInviterRefered',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onPageVisit',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Credit_Plugin_Core'
    ),
    array(
      'event' => 'onUserUpdateBefore',
      'resource' => 'Credit_Plugin_Core'
    ),
        array(
      'event' => 'onWallPostAction',
      'resource' => 'Credit_Plugin_Core'
    ),
        array(
      'event' => 'onWallPostStatus',
      'resource' => 'Credit_Plugin_Core'
    ),
  ),
  // Routes --------------------------------------------------------------------

  'routes' => array(
    'admin_members_credit' => array(
      'route' => 'admin/credit/manage/:action/:user_id/*',
      'defaults' => array(
        'module' => 'credit',
        'controller' => 'admin-members',
        'action' => 'edit',
        'user_id' => 0
      )
    ),

    'credit_general' => array(
      'route' => 'credits/:action/*',
      'defaults' => array(
        'module' => 'credit',
        'controller' => 'index',
        'action' => 'index'
      )
    ),

    'credit_payment' => array(
      'route' => 'credit-payment/:action',
      'defaults' => array(
        'module' => 'credit',
        'controller' => 'payment',
        'action' => 'gateway'
      )
    ),

    'credit_transaction' => array(
      'route' => 'credit-transaction/:action/*',
      'defaults' => array(
        'module' => 'credit',
        'controller' => 'transaction',
        'action' => 'index'
      )
    )
  )
);