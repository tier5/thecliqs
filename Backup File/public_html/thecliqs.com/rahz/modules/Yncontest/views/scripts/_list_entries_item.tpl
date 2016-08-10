<script type="text/javascript">
<?php if($this->item->contest_type == 'advalbum'):?>
	var changeOrder_advalbum = function(obj)
	{
		var album_id = obj.get('value');			
		var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html', 
	        'contestId': <?php echo $this->item->contest_id?>,  
	        'submit': true,        
	        'album_id' : album_id,  
	        'page' :1      
	      },
		  'onRequest' : function(){	  	
				$('image_loading').style.display = '';
				$('contest_paginators').style.display = 'none';
		  },
		  'onSuccess' : function(responseJSON, responseText)
		  {	  	
				$('image_loading').style.display = 'none';
				$('contest_paginators').style.display = '';
				setTimeout(function()
				{
					tinymce.init({ mode: "exact", elements: "summary", plugins: "table,fullscreen,media,preview,paste,code,image,textcolor,link", theme: "modern", menubar: false, statusbar: false, toolbar1: "undo,redo,removeformat,pastetext,|,code,media,image,link,fullscreen,preview", toolbar2: "", toolbar3: "", element_format: "html", height: "225px", convert_urls: false, language: "en", directionality: "ltr" });
				}, 10);
		  }
	    }), {
	      'element' : $('wrap_contest').getParent()
	    });
	};

  var ContestPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var paginateContestCompany = function(page) {  	
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html', 
        'contestId': <?php echo $this->item->contest_id ?>,  
        'submit': true,        
        'page' : page, 
        'album_id': <?php echo $this->album_id ?>       
      },
	  'onRequest' : function(){	  	
			$('image_loading').style.display = '';
			$('contest_paginators').style.display = 'none';
	  },
	  'onSuccess' : function(responseJSON, responseText)
	  {	  	
			$('image_loading').style.display = 'none';
			$('contest_paginators').style.display = '';
	  }
    }), {
      'element' : $('wrap_contest').getParent()
    });
  } 
<?php else:?>
	var ContestPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var paginateContestCompany = function(page) {  	
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html', 
        'contestId': <?php echo $this->item->contest_id ?>,  
        'submit': true,        
        'page' : page,         
      },
	  'onRequest' : function(){	  	
			$('image_loading').style.display = '';
			$('contest_paginators').style.display = 'none';
	  },
	  'onSuccess' : function(responseJSON, responseText)
	  {	  	
			$('image_loading').style.display = 'none';
			$('contest_paginators').style.display = '';
	  }
    }), {
      'element' : $('wrap_contest').getParent()
    });
  } 
<?php endif;?>
	function jumpPage(page){
		if(page != '<?php echo $this->paginator->getCurrentPageNumber()?>')
			paginateContestCompany(page);
	}
	
	function jumpPageEnter(event) {
        if (event.which == 13 || event.keyCode == 13) {
            //code to execute here
			var page = $('jump_page_input').value;
			if(page != '<?php echo $this->paginator->getCurrentPageNumber()?>')
				paginateContestCompany(page);
        }
    }
</script>


<br/>

<div id="contest_anchor">	
	<div class="contest_paginator">
			<div id="image_loading" style="display: none"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/loading.gif'?>"/></div>
			<div id="contest_paginators">
				
				<?php if($this->paginator->getTotalItemCount()>$this->items_per_page):?>					
					<div class="jump_page">
						<span><?php echo $this->translate('Page :');?></span>
						<input id="jump_page_input" name="jump_page_input" value="<?php echo $this->paginator->getCurrentPageNumber()?>" onblur="jumpPage(this.value)" onkeypress="jumpPageEnter(event)"/>
						<span> / <?php echo count($this->paginator)?></span>
					</div>
					<div class="paginator_next_previous">
						<?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
							<span id="user_group_members_previous">
								<?php
								  echo $this->htmlLink('javascript:void(0);', "<img src='" . $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/previous-icon.png' . "'/>", array(
										  'onclick' => 'paginateContestCompany(ContestPage - 1)',
										  'title' => $this->translate('Previous')
								  )); ?>
							</span>
						<?php endif; ?>
						<?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
						   <span id="user_group_members_next">
						  <?php  echo $this->htmlLink('javascript:void(0);', "<img src='" . $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/next-icon.png' . "'/>", array(
													'onclick' => 'paginateContestCompany(ContestPage + 1)',
													'title' => $this->translate('Next')
											));?>
									</span>
						<?php endif; ?>
					</div>
				<?php endif;?>	
			</div>   
	</div>
</div>
	
<ul class="generic_list_widget ynContestSubmit_widget ynContests_browse ynContestSubmit_list" id="ynContestSubmit_recent_videos">
    <?php foreach ($this->paginator as $item) : ?>
        <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
        <?php 
		$album = '';
		$music_type = '';
		if(isset($item['resource_type']))
			$music_type = $item['resource_type'];
        if(is_array($item))
		{
			$temp = Engine_Api::_()->getItemTable($item['resource_type'])->find($item['song_id'])->current();
			if ($item['resource_type'] == 'ynultimatevideo_video') {
				$temp = Engine_Api::_()->getItemTable($item['resource_type'])->find($item['video_id'])->current();
			} else if ($item['resource_type'] == 'music_playlist_song') {
    			$album = Engine_Api::_()->getItemTable('music_playlist')->find($temp->playlist_id)->current();	            			
			} else if ($item['resource_type'] == 'mp3music_album') {
    			$album = Engine_Api::_()->getItemTable('mp3music_album')->find($temp->album_id)->current();
    		}
			$item = $temp;
		}        	
			
		echo $this->partial('_item_listing.tpl', 'yncontest', array(
			'item'     => $item,
			'contest_type' => $this->item->contest_type,
			'album' => $album,
			'music_type' => $music_type
		));
        ?>
        </li>
    <?php endforeach; ?>        
</ul>

