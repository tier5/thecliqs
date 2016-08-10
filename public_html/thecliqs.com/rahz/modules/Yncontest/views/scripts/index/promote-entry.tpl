<script type="text/javascript">
	window.addEvent('domready', function () {	 
		
		//get width, heigth
		var width = $('iframe_promote_block').getCoordinates().width;
		var height = $('iframe_promote_block').getCoordinates().height;
		html = "<iframe src='<?php echo (!empty($_ENV['HTTPS']) && 'on' ==strtolower($_ENV['HTTPS'])) ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'].$this->url(array('action' => 'display-promote-entry'));?>' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:"+width+"px; height:"+height+"px;' allowTransparency='true' ></iframe>";			
			
		$('box_code').set('value',html);	
	});
</script>
<div class="ynContest_PromoteWrapper">
	<div class="ynContest_donateCode">		
		<h3 class = "contest_title"><?php echo $this->translate("Promote Entry Code")?></h3>
		<textarea readonly="readonly" class="ynContest_boxCode" id="box_code" rows = "11"><iframe src="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array('action' => 'display-promote-entry'));?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:280px;" allowTransparency="true">;</iframe>
		</textarea>	
		<button id="copy_clipboard"><?php echo $this->translate("Copy to Clipboard");?></button>
			<script>
				new MooClip('#copy_clipboard', {
					moviePath: en4.core.baseUrl+'application/modules/Yncontest/externals/scripts/ZeroClipboard.swf',
					dataSource: function(target){						
						return document.id('box_code').get('value');
					},
					
				});
			</script>		
	</div>

	<div class="ynContest_contestPromote ynContest_subProperty" id = "iframe_promote_block">	
		<?php echo $this->htmlLink($this->contest->getHref(), $this->string()->truncate($this->contest->getTitle(), 28), array('title' => $this->string()->stripTags($this->contest->getTitle()), 'class' => 'ynContest_promoteTitle','target'=> '_blank')) ?>	
		<p class="ynContest_ownerStat">
			<?php echo $this->translate("Created by:");?>
			<a target="_blank" href="<?php echo $this->contest->getOwner()->getHref()?>"><?php echo $this->contest->getOwner()->getTitle();?> </a>
		</p>		
		<div class ='ynContest_promote_photoColRight'>		
			<?php //$object =  Engine_Api::_()->yncontest()->getEntryThumnail($this->contest->entry_type,$this->contest->item_id);?>			
			<a target="_blank" href="<?php echo $this->contest->getHref()?>"><?php  echo $this->itemPhoto($this->contest, 'thumb.normal'); ?></a>	
			
		</div>		
		<p class="ynContest_promoteDesc">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->contest->summary), 115);?>
		</p>				
	</div>
</div>


