<div class='ynresume-tabs tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<script type="text/javascript">
	$$('.ynresume-tabs').getElement('.navigation').removeClass('navigation');
    $$('.ynresume_main_recommendations').getParent().addClass('active');
    $$('.core_main_ynresume').getParent().addClass('active');
</script>