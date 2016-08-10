 <?php
	$staticBaseUrl = $this->layout()->staticBaseUrl;
		
	$this->headScript()
	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/dragdealer.js')
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery-1.10.2.js');
  ?>


<style>
	.dragdealer {
	  width: 400px;
	  position: relative;
	  height: 300px;
	  background: #EEE;
	}
	.dragdealer .handle {
	  position: absolute;
	  top: 0;
	  left: 0;
	  cursor: pointer;
	}
	.dragdealer .red-bar {
	  width: 100px;
	  height: 30px;
	  background: green;
	  color: #FFF;
	  font-size: 14px;
	  line-height: 30px;
	  text-align: center;
	}
	.dragdealer .disabled {
	  background: #898989;
	}
	
	.demo .dragdealer {
	  background: #e1e1e1;
	  height: 40px;
	  border-radius: 3px;
	}
	  .demo .dragdealer .red-bar {
	    height: 40px;
	    font-size: 16px;
	    line-height: 40px;
	    border-radius: 3px;
	  }
	
	/* Just a slider */
	
	#just-a-slider {
	  height: 60px;
	}
	  #just-a-slider .handle {
	    height: 60px;
	    line-height: 60px;
	  }
	    #just-a-slider .value {
	      padding: 0 0 0 5px;
	      font-size: 30px;
	      font-weight: bold;
	    }
	
</style>  
<?php $settings = Engine_Api::_()->getApi('settings', 'core');?>
<div class="form-wrapper form-ynmusic-drag-drop">
	<div class="form-label">
		<?php echo $this->translate('Mini Player Position')?>
	</div>
	<div class="form-element">
		<input type="hidden" id ="x_value" name="x_value" value="<?php echo $settings->getSetting('ynmusic_x_value', 0);?>"/>
		<input type="hidden" id ="y_value" name="y_value" value="<?php echo $settings->getSetting('ynmusic_y_value', 0);?>"/>		
		<br/>
		<div id="demo-simple-slider" class="dragdealer">
			<div id="handle-red-bar" class="handle red-bar">drag me</div>
		</div>
  	</div>
</div>


<script type="text/javascript">
	window.addEvent('domready', function() {
		
		var X = <?php echo $settings->getSetting('ynmusic_x_value', 0);?>;
		var Y = <?php echo $settings->getSetting('ynmusic_y_value', 0);?>;
		var transform = "";
		
		var MAX_X = 300;
		var MAX_Y = 270;
		
		var transform = "translateX("+X*MAX_X+"px) translateY("+Y*MAX_Y+"px)";
		var counter = 0;
		var looper = setInterval(function(){ 
		    counter++;
		    jQuery("#handle-red-bar").css("transform", transform);
		    if (counter >= 3)
		    {
		        clearInterval(looper);
		    }
		}, 50);
		
		jQuery(function () 
		{
			jQuery(function() {
			  new Dragdealer('demo-simple-slider',{
				horizontal: true,
			    vertical: true,
			    callback: function(x, y){
			    	if(x > 0 && x < 1 && y < 1 && y > 0)
					{
						jQuery("#handle-red-bar").css("background", "red");
						jQuery('#color').val('red');
				    }
			    },
				dragStopCallback: function(x, y) {	
					if(x > 0 && x < 1 && y < 1 && y > 0)
					{
						jQuery("#handle-red-bar").css("background", "red");
						jQuery('#color').val('red');
					}
					else{
						jQuery("#handle-red-bar").css("background", "green");
						jQuery('#color').val('green');
						X = x;
						Y = y;
						jQuery('#x_value').val(X);
						jQuery('#y_value').val(Y);
					}
				}
			  }); 
			});
    	});
    	
   }); 
	
</script>
