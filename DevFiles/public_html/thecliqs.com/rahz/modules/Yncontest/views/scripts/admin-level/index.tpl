<h2><?php echo $this->translate("Contest Plugin") ?></h2>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/yncontest/level/index/id/'+level_id;
  }
</script>

<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>
