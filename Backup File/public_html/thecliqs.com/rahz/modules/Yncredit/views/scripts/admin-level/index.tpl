<h2>
  <?php echo $this->translate('User Credits Plugin') ?>
</h2>

<script type="text/javascript">
  var fetchLevelSettings = function(level_id){    
    window.location.href = en4.core.baseUrl +'admin/yncredit/level/index/id/'+level_id;
  };
  function checkIt(evt) {
      evt = (evt) ? evt : window.event;
      var status = "";
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
          status = "<?php echo $this->translate("This field accepts numbers only.")?>";
          return false;
      }
      return true;
  }
</script>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      /*---- Render the menu ----*/
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div style="margin-bottom: 15px;">
	<?php echo $this->translate("YNCREDIT_FORM_ADMIN_LEVEL_DESCRIPTION");?>
</div>


<div class='clear' style="float: right; width: 83%;">
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php echo $this->render('_adminLevelMenu.tpl'); ?>