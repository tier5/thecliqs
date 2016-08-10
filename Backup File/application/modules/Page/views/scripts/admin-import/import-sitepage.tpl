<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: import-sitepage.tpl  23.12.11 16:19 Ulan T $
 * @author     Ulan T
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
  <?php if ($this->pageCount > 0 ) : ?>
    window.setTimeout(function() {
      window.document.getElementById('loader').removeClass('display_none');
      window.location.reload();
    }, 2000);
    <?php else : ?>
    parent.location.href = parent.location.href;
    <?php endif; ?>
  });
</script>

<?php if( $this->pageCount > 0 ) :?>
<div style="padding: 15px; text-align: center;">
  <h3 style="font-size: 10pt"><?php echo $this->translate(array('PAGE_There are %s pages. Please wait ...'), array($this->pageCount)); ?></h3>
  <img src="application/modules/Core/externals/images/loading.gif" class="display_none" id="loader"/>
</div>
<?php elseif($this->pageCount == 0) : ?>
<div style="padding: 15px; text-align: center;">
  <h3 style="font-size: 10pt"><?php echo $this->translate('PAGE_All Pages have been imported'); ?></h3>
</div>
<?php else : ?>
<div style="padding: 15px; text-align: center;">
  <h3 style="font-size: 10pt"><?php echo $this->translate('PAGE_There is no pages or Sitepage module doesn\'t exsists'); ?></h3>
</div>
<?php endif;?>
