<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: upgrade.tpl  23.12.11 16:19 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
  <?php if ($this->rowCount) : ?>
    window.setTimeout(function() {
      window.document.getElementById('loader').removeClass('display_none');
      window.location.reload();
    }, 3000);
    <?php else : ?>
    parent.location.href = parent.location.href;
    <?php endif; ?>
  });
</script>

<div style="padding: 15px; text-align: center;">
  <h3 style="font-size: 10pt"><?php echo $this->translate(array('PAGE_There are %s old records. Please wait ...'), array($this->rowCount)); ?></h3>
  <img src="application/modules/Core/externals/images/loading.gif" class="display_none" id="loader"/>
</div>