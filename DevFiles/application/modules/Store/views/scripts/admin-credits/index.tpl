<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  01.06.12 16:48 TeaJay $
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

<?php if ($this->error): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->translate('Update Credit Plugin'); ?>
    </li>
  </ul>
<?php endif; ?>

<div class="store_credit" style="margin: 5px;">
  <div class="admin_home_environment_buttons">
    <button onclick="switchCreditSystem(0, this);this.blur();"
      <?php if ($this->credit_enabled): ?>class="button_disabled"<?php endif; ?>><?php echo $this->translate('Only Store') ?></button>
    <button onclick="switchCreditSystem(1, this);this.blur();"
      <?php if (!$this->credit_enabled): ?>class="button_disabled"<?php endif; ?>><?php echo $this->translate('With Credits') ?></button>
  </div>

  <br />

  <div class="admin_home_environment_description">
    <?php echo $this->translate('Your store system is currently working %s credit.', ($this->credit_enabled) ? 'with' : 'without'); ?>
    <?php if ($this->credit_enabled): ?>
      <?php echo $this->translate('STORE_WITH_CREDIT') ?>
    <?php else: ?>
      <?php echo $this->translate('STORE_WITHOUT_CREDIT') ?>
    <?php endif; ?>
  </div>

  <script type="text/javascript">
    //<![CDATA[
    var switchCreditSystem = function(switcher, btn) {
      $$('div.store_credit button').set('class', 'button_disabled');
      btn.set('class', '');
      $$('div.admin_home_environment_description').set('text', ((switcher) ? 'Enabling Credits System' : 'Disabling Credits System')+' - please wait...');
      new Request.JSON({
        url: '<?php echo $this->url(array('module' => 'store', 'controller' => 'credits', 'action'=>'enable'), 'admin_default', true) ?>',
        method: 'post',
        onSuccess: function(responseJSON){
          if ($type(responseJSON) == 'object') {
            if (responseJSON.success || !$type(responseJSON.error))
              window.location.href = window.location.href;
            else
              alert(responseJSON.error);
          } else
            alert('An unknown error occurred; changes have not been saved.');
        }
      }).send('format=json&switcher='+switcher);
    }
    //]]>
  </script>
</div>
<br />
<?php if ($this->credit_enabled && $this->page_enabled) : ?>
  <div class="settings">
    <?php echo $this->form->render($this)?>
  </div>
<?php endif; ?>