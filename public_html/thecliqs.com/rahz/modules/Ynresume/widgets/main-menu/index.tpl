<div class="headline">
    <h2>
    <?php echo $this->translate('Resume') ?>
    </h2>
    <div class="tabs ynresume-menu-top">
	    <?php
	    // Render the menu
	    echo $this->navigation()
	      ->menu()
	      ->setContainer($this->navigation)
	      ->render();
	    ?>
    </div>
</div>



<script type="text/javascript">
  en4.core.runonce.add(function() 
  {
        $$('a.ynresume_main_more').each(function(e)
        {
            <?php $session = new Zend_Session_Namespace('mobile');
            if($session -> mobile):?>
                var parent = e.getParent();
                var sub = parent.getChildren('ul');
                var sub_html = "";
                if(sub.length > 0)
                    sub_html = sub[0].innerHTML;
                var parent_parent = parent.getParent();
                parent.destroy();
                parent_parent.set('html', parent_parent.get("html") + sub_html);
            <?php else:?>
            e.addEvent('click', function() 
            {
                if(this.getParent().hasClass('ynresume_more_show'))
                {
                    this.getParent().removeClass('ynresume_more_show');
                }
                else
                {
                    this.getParent().addClass('ynresume_more_show');
                }
            });
            <?php endif;?>
        });
  });
</script>
