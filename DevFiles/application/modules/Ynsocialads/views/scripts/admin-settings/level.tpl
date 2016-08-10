<h2><?php echo $this->translate("YouNet Social Ads Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>

      <?php echo $this->form->render($this); ?>

    </div>
  </div>
<script type="text/javascript">
    //<![CDATA[
    $('level_id').addEvent('change', function(){
        window.location.href = en4.core.baseUrl + 'admin/ynsocialads/settings/level/level_id/'+this.get('value');
    });
</script>