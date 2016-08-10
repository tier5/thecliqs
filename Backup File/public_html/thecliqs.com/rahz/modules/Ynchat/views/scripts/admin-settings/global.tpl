<h2><?php echo $this->translate("YouNet Chat Plugin") ?></h2>

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

<script>
    window.addEvent('domready', function() {
        var ip = $('ynchat_chatbox_userip-1');
        var url = $('ynchat_chatbox_userip-0');
        
        if (url.checked) {
            $('ynchat_chatbox_ipaddress-wrapper').hide();
        };
        
        ip.addEvent('click', function(){
            $('ynchat_chatbox_ipaddress-wrapper').show();
        });
        
        url.addEvent('click', function(){
            $('ynchat_chatbox_ipaddress-wrapper').hide();
        });
    });
</script>     