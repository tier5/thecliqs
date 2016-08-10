<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  04.01.12 13:11 TeaJay $
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
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMINASSIGNCREDITS_INDEX_DESCRIPTION") ?>
</p>
<br />

<div class="admin_table_form" id="admin_assign_credits">
  <form method="post">
    <table class='admin_table'>
      <thead>
        <tr>
          <th><?php echo $this->translate("Action Type") ?></th>
          <th style='width: 1%;'><?php echo $this->translate("Credit") ?></th>
          <th style='width: 1%;'><?php echo $this->translate("Max Credit") ?></th>
          <th style='width: 1%;'><?php echo $this->translate("Rollover Period") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $this->actionTypes as $key => $type ): ?>
          <?php if ($key): ?>
            <?php $is_module = ($type->action_module != $this->actionTypes[$key-1]->action_module); $type = $this->actionTypes[$key];?>
            <?php if ($is_module): ?>
              <tr><td colspan="4" class="admin_credit_module"><?php echo ucfirst($this->translate('_CREDIT_'.$type->action_module)); ?></td></tr>
            <?php endif; ?>
          <?php else : ?>
            <tr><td colspan="4" class="admin_credit_module"><?php echo ucfirst($this->translate('_CREDIT_'.$type->action_module)); ?></td></tr>
          <?php endif; ?>
          <tr>
            <td><?php echo $this->translate('_CREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->action_type), '_')))?></td>
            <td><input type="text" value="<?php echo $this->locale()->toNumber($type->credit) ?>" name="credit-<?php echo $type->action_id?>" size="7" /></td>
            <td><input type="text" value="<?php echo $this->locale()->toNumber($type->max_credit) ?>" name="max_credit-<?php echo $type->action_id?>" size="7" /></td>
            <td><input type="text" value="<?php echo $this->locale()->toNumber($type->rollover_period) ?>" name="rollover_period-<?php echo $type->action_id?>" size="7" />
              <?php echo $this->translate('day(s)')?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br />
    <div class='buttons' style="text-align: right">
      <button type='submit' name="save" value="save"><?php echo $this->translate("Save") ?></button>
    </div>
  </form>
</div>