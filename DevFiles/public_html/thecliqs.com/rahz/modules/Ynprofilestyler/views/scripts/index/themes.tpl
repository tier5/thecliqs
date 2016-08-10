<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<?php if (!$this->themes->count() > 0) : ?>
    <?php echo $this->translate('Sorry, there are no themes available.') ?>
<?php else : ?>
	<div class="ynprofilestyler_themes_container">
    	<div class="prev controls"></div>
    	<div class="next controls"></div>
    	<div class="ynprofilestyler_themes">
            <ul>
                <?php foreach ($this->themes as $item) : ?>
                    <li>
                    	<div>
                        	<img src="<?php echo $this->baseUrl() . $item->getThumbnail()?>" />
    						<p class="title" title="<?php echo $this->string()->htmlspecialchars($item->title)?>">
    						    <?php echo $this->string()->htmlspecialchars($this->string()->truncate($item->title, 15));?>
						    </p>
    						<button onclick="ynps2.applyTheme(<?php echo $item->getIdentity()?>)" class="btn-use-theme">
    						    <?php echo $this->translate('Use Theme')?>
    					    </button>                    	    
            			</div>            	
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <script language="javascript" type="text/javascript">
    	var visible = <?php echo $this->themes->count();?>;
        <?php if ($this->themes->count() > 5) : ?>
    		visible = 5;
    	<?php endif;?>
    	$(document).ready(function(){
    		$(".ynprofilestyler_themes").jCarouselLite({
    	        btnNext: ".prev",
    	        btnPrev: ".next",
    	        visible : visible,
    	        circular : true    	        
    	    });
    	});
    </script>
<?php endif; ?>