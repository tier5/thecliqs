<?php 
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     YnAuction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: manifest.php
 * @author     Minh Nguyen
 */
return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynauction',
    'version' => '4.02p4',
    'path' => 'application/modules/Ynauction',
    'title' => 'YN - Auction',
    'description' => 'This is  Auction module',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'core',
         'minVersion' => '4.1.2',
      ),
    ),
    'callback' => 
    array (
        'path' => 'application/modules/Ynauction/settings/install.php',    
        'class' => 'Ynauction_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Ynauction',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/Ynauction.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Ynauction_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Ynauction_Plugin_Core'
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'ynauction_product',
    	'ynauction_category',
        'ynauction_param',
        'ynauction_bid',
        'ynauction_album',
        'ynauction_photo',
        'ynauction_location',
        'ynauction_become',
        'ynauction_confirm',
        'ynauction_transaction_tracking',
        'ynauction_proposal',
        'ynauction_order',
    ),
   // Routes --------------------------------------------------------------------
    'routes' => array(
        'ynauction_extended' => array(
          'route' => 'auction/:controller/:action/*',
          'defaults' => array(
            'module' => 'ynauction',
            'controller' => 'index',
            'action' => 'index',
          ),
          'reqs' => array(
                'action' => '(index)',
          ),
        ),
        'ynauction_general' => array(
            'route' => 'auction/:action/*',
            'defaults' => array(
                'module' => 'ynauction',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(update-order|browse|listing|confirm|become|check-confirm|create|success|edit|delete|detail|rate|display|publish|manageauction|subcategories|bid|update|win|stop|start|transaction|participate|history|user-auction|buynow|list-page|approve|deny|term-service)',
            ),
        ), 
        'ynauction_proposal' => array(
            'route' => 'auction/boughts/:action/*',
            'defaults' => array(
                'module' => 'ynauction',
                'controller' => 'proposal',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|proposal-price|proposal-seller|approve|deny|checkout|makebill)',
            ),
        ), 
        'ynauction_account' => array(
            'route' => 'auction/account/:action/*',
            'defaults' => array(
                'module' => 'ynauction',
                'controller' => 'account',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|create|edit|his-bids|approve|deny)',
            ),
        ),  
        'ynauction_winning' => array(
            'route' => 'auction/winning/:action/*',
            'defaults' => array(
                'module' => 'ynauction',
                'controller' => 'win',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|checkout|makebill)',
            ),
        ), 
         'ynauction_viewtransaction' => array(
         'route' => 'auction/viewtransaction/:id/:username',
          'defaults' => array(
            'module' => 'ynauction',
            'controller' => 'index',
            'action' => 'view-transaction',
          ),
          ), 
        'ynauction_admin_manage_create' => array(
            'route' => 'admin/ynauction/manage/create/*',
            'defaults' => array(
                'module' => 'ynauction',
                'controller' => 'admin-manage',
                'action' => 'create',
            ),
          ),
		  'ynauction_admin_manage_edit' => array(
            'route' => 'admin/ynauction/manage/edit/*',
            'defaults' => array(
                'module' => 'ynauction',
                'controller' => 'admin-manage',
                'action' => 'edit',
            ),
          ),
        'ynauction_help' => array(
           'route'    => 'auction/help/*',
           'defaults' => array(
               'module'     => 'ynauction',
               'controller' => 'help',
               'action'     => 'detail',
           ),
        ),
        'ynauction_transaction' => array(
	      'route' => 'auction/transaction-process/:action/*',
	      'defaults' => array(
	        'module' => 'ynauction',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
    	),
    ),
); ?>
