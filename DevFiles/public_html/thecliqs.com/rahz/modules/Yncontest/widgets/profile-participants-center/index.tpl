<script type="text/javascript">
  var <?php echo $this->widgettype?>Page = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  
  <?php echo $this->widgettype?>paginateEntriesWinCompany = function(page) {  	
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html', 
        'page<?php echo $this->widgettype?>' : page,    
        'contestId': <?php echo $this->contest->contest_id?>,
        'view_participants': 1     
      },
	  'onRequest' : function(){	  	
			$('<?php echo $this->widgettype?>_image_loading').style.display = '';
			$('<?php echo $this->widgettype?>_paginators').style.display = 'none';
	  },
	  'onSuccess' : function(responseJSON, responseText)
	  {	  	
			$('<?php echo $this->widgettype?>_image_loading').style.display = 'none';
			$('<?php echo $this->widgettype?>_paginators').style.display = '';
			$('tab_1').style.display = 'block';
	  }
    }), {
      'element' : $('<?php echo $this->widgettype?>_anchor').getParent()
     
    });
  } 
  function <?php echo $this->widgettype?>jumpPage(page){
		if(page != '<?php echo $this->paginator->getCurrentPageNumber()?>')
			<?php echo $this->widgettype?>paginateEntriesWinCompany(page);
	}
	
	function <?php echo $this->widgettype?>jumpPageEnter(event) {
        if (event.which == 13 || event.keyCode == 13) {
            //code to execute here
			var page = $('jump_page_input').value;
			if(page != '<?php echo $this->paginator->getCurrentPageNumber()?>')
				<?php echo $this->widgettype?>paginateEntriesWinCompany(page);
        }
    }        
</script>
<div id="<?php echo $this->widgettype?>_anchor">
		<div class="<?php echo $this->widgettype?>_paginator">
			<div id="<?php echo $this->widgettype?>_image_loading"
				style="display: none">
				<img
					src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/loading.gif'?>" />
			</div>
			<div id="<?php echo $this->widgettype?>_paginators">

				<?php if($this->paginator->getTotalItemCount()>$this->items_per_page):?>
				<div class="<?php echo $this->widgettype?>jump_page">
					<span><?php echo $this->translate('Page :');?> </span> <input
						id="<?php echo $this->widgettype?>jump_page_input"
						name="jump_page_input"
						value="<?php echo $this->paginator->getCurrentPageNumber()?>"
						onblur="<?php echo $this->widgettype?>jumpPage(this.value)"
						onkeypress="<?php echo $this->widgettype?>jumpPageEnter(event)" />
					<span> / <?php echo count($this->paginator)?>
					</span>
				</div>
				<div class="<?php echo $this->widgettype?>_next_previous">
					<?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
					<span
						id="<?php echo $this->widgettype?>_user_group_members_previous">
						<?php
						echo $this->htmlLink('javascript:void(0);', "<img class='yncontest_nextprevious' src='" . $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/previous-icon.png' . "'/>", array(
													  'onclick' => $this->widgettype.'paginateEntriesWinCompany('.$this->widgettype.'Page - 1)',
													  'title' => $this->translate('Previous')
											  )); ?>
					</span>
					<?php endif; ?>
					<?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
					<span id="user_group_members_next"> <?php  echo $this->htmlLink('javascript:void(0);', "<img class='yncontest_nextprevious' src='" . $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/next-icon.png' . "'/>", array(
							'onclick' => 	$this->widgettype.'paginateEntriesWinCompany('.$this->widgettype.'Page + 1)',
							'title' => $this->translate('Next')
														));?>
					</span>
					<?php endif; ?>
				</div>
				<?php endif;?>
				<div id="yncontest_participants_list" class="ynContest_participantsWrapper">
					<ul id="list_member" class='ynContest_participantsList'>
					    <?php foreach( $this->paginator as $item ):?>				
					      <li style="width:<?php echo $this->width?>px; height: <?php echo $this->height?>px;">
							<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array("class" => "icon_class")) ?>
							<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
					      </li>			   
					    <?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>

	</div>


