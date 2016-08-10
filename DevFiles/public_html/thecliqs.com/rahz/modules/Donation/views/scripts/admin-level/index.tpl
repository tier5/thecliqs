<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 24.08.12
 * Time: 16:46
 * To change this template use File | Settings | File Templates.
 */?>

<h2>
  <?php echo $this->translate('Donation Plugin') ?>
</h2>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/donation/level/index/id/'+level_id;
  }
</script>

<?php if( count($this->navigation) ): ?>
<div class='donation_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
      <?php echo $this->form->render($this) ?>
    </div>

</div>