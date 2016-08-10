<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: start.tpl  01.11.12 16:19 Ulan T$
 * @author     Ulan T
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
    <?php if ($this->import->status != 2) : ?>
        window.setTimeout(function() {
            window.document.getElementById('loader').removeClass('display_none');
            window.location.reload();
        }, 2000);
      <?php else : ?>
        parent.location.href = parent.location.href;
      <?php endif; ?>
    });
</script>

<div style="padding: 15px; text-align: center;">
    <h3 style="font-size: 10pt"><?php echo $this->translate(array('%s Pages have been imported'), array($this->import->import_count)); ?></h3>
    <img src="application/modules/Core/externals/images/loading.gif" class="display_none" id="loader"/>
</div>
