<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  11.04.12 15:35 TeaJay $
 * @author     Taalay
 */
?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate('STORE_ADMIN_TAXES_DESCRIPTION'); ?>
</p>
<br />

<?php echo $this->form->render($this)?>

<div style="clear: both;"></div>
<br />
<?php if ($this->taxes->count() > 0) : ?>
  <div class="admin_table_form">
    <table class='admin_table'>
      <thead>
        <tr>
          <th style="width: 120px"><?php echo $this->translate("Name") ?></th>
          <th style="width: 120px"><?php echo $this->translate("Percent") ?></th>
          <th style="width: 120px" class='admin_table_options'><?php echo $this->translate("Options")?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->taxes as $tax) : ?>
          <tr>
            <td><?php echo $tax->title?></td>
            <td><?php echo number_format($tax->percent, 2, '.', '');?>%</td>
            <td class='admin_table_options'>
              <?php echo $this->htmlLink(
                $this->url(
                  array(
                    'module' => 'store',
                    'controller' => 'taxes',
                    'action' => 'edit',
                    'tax_id' => $tax->tax_id
                  ), 'admin_default', true
                ), $this->translate('edit'), array('class' => 'smoothbox')) ?>
              <span>&nbsp;|&nbsp;</span>
              <?php echo $this->htmlLink(
                $this->url(
                  array(
                    'module' => 'store',
                    'controller' => 'taxes',
                    'action' => 'delete',
                    'tax_id' => $tax->tax_id
                  ), 'admin_default', true
                ), $this->translate('delete'), array('class' => 'smoothbox')) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('STORE_There are no taxes.');?>
    </span>
  </div>
<?php endif; ?>