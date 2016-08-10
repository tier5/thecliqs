<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('submit').addEvent('click',function(e){
      $itemPerPage = $('donation_browse_count');
      $value = $itemPerPage.get('value').trim();
      $itemPerPage.set('value',$value);
      $topDonorsCount = $('donation_donors_count');
      $value = $topDonorsCount.get('value').trim();
      $topDonorsCount.set('value',$value);
      $supportersCount = $('donation_supporters_count');
      $value =$supportersCount.get('value').trim();
      $supportersCount.set('value',$value);
    });
  });
</script>

<h2><?php echo $this->translate("Donation Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='donation_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class="settings admin_home_middle" style="clear: none;">
  <div class="settings">
    <?php echo $this->form->render($this); ?>
  </div>
</div>