<h2>
  <?php echo $this->translate('Auction Plugin') ?>
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

<style>
#global_page_ynaffiliate-admin-terms-index .settings .form-label {
   float: none !important;
}
#ynaffiliate_terms #static_content-wrapper {
    border-bottom: medium none;
    border-top: medium none;
    padding-bottom: 0;
    padding-top: 0;
}
</style>


<div class='settings'>
  <?php echo $this->form->render($this); ?>
</div>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
</style>