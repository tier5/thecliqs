<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: select-send.tpl  29.02.12 15:53 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/scripts/core.js')
  ;
?>

<script type="text/javascript">
  gift_manager.send_url = '<?php echo $this->url(array('action' => 'send'), 'hegift_general', true) ?>';
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Virtual Gifts');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<?php echo $this->render('_breadcrumbs.tpl');?>

<div class="select-and-send">
  <div style="margin: 3px; float: left">
    <img src="<?php echo $this->gift->getPhotoUrl('thumb.normal');?>" style="max-width: 80px; max-height: 90px;"/>
  </div>
  <div>
    <ul class="form-notices">
      <li>
        <?php
          echo $this->translate('HEGIFT_Your %s gift successfully created, here you can select '.
          'friends witch want to send this gift, then Send, for this click button below. If you decide not to send a'.
          ' gift and closed the page, do not worry your gift to be kept in %s', $this->gift->getTypeName(), $this->htmlLink($this->url(array(), 'hegift_temp', true), $this->translate('HEGIFT_temporary storage'), array('target' => '_blank')));
        ?>
      </li>
    </ul>
  </div>
  <button onclick="gift_manager.open_form('<?php echo $this->gift->getIdentity()?>')"><?php echo $this->translate('HEGIFT_Select Friends and Send Gift')?></button>
</div>
