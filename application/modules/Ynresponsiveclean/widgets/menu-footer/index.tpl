<div class="photo-footer-menu">
	<div class="container clearfix">
	    <div class="cus-footer pull-left">
	        <span class="custom-copyright">
	        	&copy; <?php echo $this->translate('%s Clean Template', date('Y')) ?>
	        </span>
	    </div>

	    <div class="menu-mini-footer pull-right">
	      	<ul>
	      	<?php foreach( $this->navigation as $item ):
		      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
		        'reset_params', 'route', 'module', 'controller', 'action', 'type',
		        'visible', 'label', 'href'
		      )));
		      ?>
		      <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?></li>
		    <?php endforeach; ?>      
		
		 	<?php if( 1 !== count($this->languageNameList) ): ?>
		      <li>
		        <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
		          <?php $selectedLanguage = $this->translate()->getLocale() ?>
		          <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
		          <?php echo $this->formHidden('return', $this->url()) ?>
		        </form>
		      </li>  
	    	<?php endif; ?>
	    	
	    	<?php if( !empty($this->affiliateCode) ): ?>
		        <li>
		        <?php 
		          echo $this->translate('Powered by %1$s', 
		            $this->htmlLink('http://www.socialengine.com/?source=v4&aff=' . urlencode($this->affiliateCode), 
		            $this->translate('SocialEngine Community Software'),
		            array('target' => '_blank')))
		        ?>
		        </li>
		    <?php endif; ?>	
		 	</ul>
	   	</div>
    </div>

    <div class="wrap-scroll">
        <button class="scrollTop"><i class="fa fa-arrow-up"></i></button>
    </div>
</div>

<script>
	(function( $ ) {
	  $(function() {
		$(".scrollTop").on('click', function(){
			var body = $("html, body");
			body.animate({scrollTop:0}, '500', 'swing');
		});
	  });
	})(jQuery);
</script>


