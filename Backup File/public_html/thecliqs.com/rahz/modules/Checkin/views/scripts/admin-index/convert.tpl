<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: convert.tpl  13.03.12 10:19 Ermek $
 * @author     Ermek
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
  <h3 style="font-size: 10pt"><?php echo $this->translate(array('CHECKIN_There are %s old records. Please wait ...'), array($this->rowCount)); ?></h3>
  <img src="application/modules/Checkin/externals/images/loader.gif" class="display_none" id="loader"/>
</div>