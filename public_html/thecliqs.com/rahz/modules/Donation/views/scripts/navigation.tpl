<div class="tabs" id="page_donation_options">
  <?php
  echo $this
    ->navigation()
    ->menu()
    ->setContainer($this->navigation)
    ->setPartial(array('_contentNavIcons.tpl', 'page'))
    ->render();
  ?>
  <div class="donation_loader hidden" id="donation_loader">
    <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Donation/externals/images/loader.gif'); ?>
  </div>
  <div class="clr"></div>
</div>

