<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: create.tpl  27.02.12 15:23 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var upload_element = document.getElementById("upload-wrapper");
    upload_element.style.display = "none";
  });
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

<?php echo $this->form->render($this)?>