<div class="ynContest_PromoteWrapper">
	<div class="ynContest_donateCode">		
		<h3><?php echo $this->translate("Promote Contest Code")?></h3>
		<textarea readonly="readonly" class="ynContest_boxCode" id="box_code" rows = "11"><iframe src="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array('action' => 'display-promote', 'contestId' => $this->contest->getIdentity()));?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:auto; height:490px;" allowTransparency="true">;</iframe>
		</textarea>	
		<?php 
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile):?>
		<button id="copy_clipboard"><?php echo $this->translate("Copy to Clipboard");?></button>
		<script>
			new MooClip('#copy_clipboard', {
				moviePath: en4.core.baseUrl+'application/modules/Yncontest/externals/scripts/ZeroClipboard.swf',
				dataSource: function(target){						
					return document.id('box_code').get('value');
				},
				
			});
		</script>	
		 <?php else:?>
	   <button id="copy_clipboard" onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close");?></button>
	   <?php endif;?>
	</div>
	<div class="ynContest_contestPromote ynContest_subProperty" id = "iframe_promote_block">		
		<?php echo $this->htmlLink($this->contest->getHref(), $this->string()->truncate($this->contest->getTitle(), 28), array('title' => $this->string()->stripTags($this->contest->getTitle()), 'class' => 'ynContest_promoteTitle','target'=> '_blank')) ?>
		<p class="ynContest_ownerStat">
			<?php echo $this->translate("Created by");?>
			<a target="_blank" href="<?php echo $this->contest->getOwner()->getHref()?>"><?php echo $this->contest->getOwner()->getTitle();?> </a>
		</p>
		<div class ='ynContest_promote_photoColRight'>
			<a target="_blank" href="<?php echo $this->contest->getHref()?>"><?php echo $this->itemPhoto($this->contest, 'thumb.profile') ?></a>
		</div>		
		<p class="ynContest_promoteDesc">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->contest->description), 115);?>
		</p>				
	</div>
</div>

<script type="text/javascript">
	window.addEvent('domready', function () {	 
		
		//get width, heigth
		var width = $('iframe_promote_block').getCoordinates().width;
		var height = $('iframe_promote_block').getCoordinates().height;
		html = "<iframe src='<?php echo (!empty($_ENV['HTTPS']) && 'on' ==strtolower($_ENV['HTTPS'])) ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'].$this->url(array('action' => 'display-promote'));?>' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:"+width+"px; height:"+height+"px;' allowTransparency='true' ></iframe>";			
			
		$('box_code').set('value',html);	
	});
</script>
