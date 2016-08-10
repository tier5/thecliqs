<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: gateway.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>
<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('Manage');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<p>
  <?php echo $this->translate('STORE_GATEWAY_ACCOUNT_SETTINGS');?>
</p>
<br />
<table class='store-product-list' style='width: 40%;'>
  <thead>
    <tr>
      <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
      <th><?php echo $this->translate("Title") ?></th>
      <th style='width: 1%;' class="center"><?php echo $this->translate("Enabled") ?></th>
      <th style='width: 1%;' class="center"><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if( count($this->paginator) ): ?>
      <?php foreach( $this->paginator as $item ): ?>
        <tr>
          <td>
            <?php echo $item->gateway_id ?>
          </td>
          <td class='admin_table_bold'>
            <?php echo $item->title ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ( $this->page_api->isGatewayEnabled($this->page->getIdentity(), $item->gateway_id) ? $this->translate('Yes') : $this->translate('No') ) ?>
          </td>
          <td class='admin_table_options'>
            <a href='<?php echo $this->url(array('action' => 'gateway-edit', 'gateway_id' => $item->gateway_id));?>'>
              <?php echo $this->translate("edit") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>