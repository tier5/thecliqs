<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  18.01.12 13:31 TeaJay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate('Credits Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMINPAYMENTS_INDEX_DESCRIPTION") ?>
</p>
<br />

<?php echo $this->form->render($this)?>

<div style="clear: both;"></div>
<br />
<?php if( count($this->prices) ): ?>
  <div class="admin_table_form">
    <form>
      <table class='admin_table'>
        <thead>
          <tr>
            <th><?php echo $this->translate("Caption") ?></th>
            <th class='admin_table_options'><?php echo $this->translate("Option") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach( $this->prices as $price ): ?>
            <?php $bonus = ((100*$price->credit)/($price->price*$this->credits_for_one_unit) - 100); $bonus = ($bonus) ? '<span class="credit_payment_bonus">'.round($bonus, 2) . $this->translate('% bonus').'</span>' : ''?>
            <?php $caption = '<span style="font-weight:bold">'.$this->translate(array("%s Credit for ", "%s Credits for ", $price->credit), $this->locale()->toNumber((int)$price->credit) ).'</span>' . $this->locale()->toCurrency($price->price, $this->currency) . ' ' . $bonus?>
            <tr>
              <td><?php echo $caption; ?></td>
              <td class='admin_table_options'>
                <?php echo $this->htmlLink(
                  $this->url(
                    array(
                      'module' => 'credit',
                      'controller' => 'payments',
                      'action' => 'delete',
                      'payment_id' => $price->payment_id
                    ), 'admin_default', true
                  ), $this->translate('delete')) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  </div>
<?php endif; ?>