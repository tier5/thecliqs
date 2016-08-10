
<script type="text/javascript">
  var <?php echo $this->widgettype?>Page = <?php echo sprintf('%d', $this->manageentries->getCurrentPageNumber()) ?>;
  
  <?php echo $this->widgettype?>paginateEntriesWinCompany = function(page) {  	
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html', 
        'page<?php echo $this->widgettype?>' : page,    
        'contestId': <?php echo $this->contest->contest_id?>     
      },
	  'onRequest' : function(){	  	
			$('<?php echo $this->widgettype?>_image_loading').style.display = '';
			$('<?php echo $this->widgettype?>_paginators').style.display = 'none';
	  },
	  'onSuccess' : function(responseJSON, responseText)
	  {	  	
			$('<?php echo $this->widgettype?>_image_loading').style.display = 'none';
			$('<?php echo $this->widgettype?>_paginators').style.display = '';
	  }
    }), {
      'element' : $('<?php echo $this->widgettype?>_anchor').getParent()
    });
  } 
  function <?php echo $this->widgettype?>jumpPage(page){
		if(page != '<?php echo $this->manageentries->getCurrentPageNumber()?>')
			<?php echo $this->widgettype?>paginateEntriesWinCompany(page);
	}
	
	function <?php echo $this->widgettype?>jumpPageEnter(event) {
        if (event.which == 13 || event.keyCode == 13) {
            //code to execute here
			var page = $('jump_page_input').value;
			if(page != '<?php echo $this->manageentries->getCurrentPageNumber()?>')
				<?php echo $this->widgettype?>paginateEntriesWinCompany(page);
        }
    }
    function entryChoose(entry_id,obj){
            var checkbox = document.getElementById('ynContest_win_entry_checkbox_'+entry_id);
            var status = 0;
            if(obj.checked==true) status = 1;
            else status = 0;          
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array( 'action' => 'ajax-win-entry-by-owner'), 'yncontest_myentries') ?>',
              'data' : {
                'format' : 'json',
                'entry_id' : entry_id,                
                'status' : status
              },
              'onRequest' : function(){
              },
              'onSuccess' : function(responseJSON, responseText)
              {
              
                checkbox = document.getElementById('ynContest_win_entry_checkbox_'+entry_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
	    		
              }
            }).send();
    }     
</script>
<div id="<?php echo $this->widgettype?>_anchor">
	<form id='multidelete_form' method="post"
		class="global_form yncontestformwin">
		<div>
			<h3>
				<?php echo $this->translate("Win by votes")?>
			</h3>
			<div class="form-elements">
				<div class="form-wrapper" id="award_number-wrapper">
					<div class="form-label" id="award_number-label">
						<label class="required" for="award_number"><?php echo $this->translate("Number of entries win by votes")?>
						</label>
					</div>
					<div class="form-element" id="award_number-element">
						<input type="text" id="award_number" name="award_number"
							data-validators="required"
							value="<?php echo $this->contest->award_number?>">
					</div>
				</div>
				<div class="form-wrapper" id="vote_desc-wrapper">
					<div class="form-label" id="vote_desc-label">
						<label class="required" for="vote_desc"><?php echo $this->translate("Title for entries win by votes")?>
						</label>
					</div>
					<div class="form-element" id="vote_desc-element">
						<input type="text" id="vote_desc" name="vote_desc"
							value="<?php echo $this->contest->vote_desc?>">
					</div>
				</div>
			</div>
			<h3>
				<?php echo $this->translate("Win by other reason")?>
			</h3>
			<div class="form-elements">
				<div class="form-wrapper" id="reason_desc-wrapper">
					<div class="form-label" id="reason_desc-label">
						<label class="required" for="reason_desc"><?php echo $this->translate("Reason")?>
						</label>
					</div>
					<div class="form-element" id="reason_desc-element">
						<input type="text" id="reason_desc" name="reason_desc"
							value="<?php echo $this->contest->reason_desc?>">
					</div>
				</div>
				<?php if(count($this->manageentries)>0):?>

				<div class="<?php echo $this->widgettype?>_paginator">
					<div id="<?php echo $this->widgettype?>_image_loading"
						style="display: none">
						<img
							src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/loading.gif'?>" />
					</div>
					<div id="<?php echo $this->widgettype?>_paginators">

						<?php if($this->manageentries->getTotalItemCount()>$this->items_per_page):?>
						<div class="<?php echo $this->widgettype?>jump_page">
							<span><?php echo $this->translate('Page :');?> </span> <input
								id="<?php echo $this->widgettype?>jump_page_input"
								name="jump_page_input"
								value="<?php echo $this->manageentries->getCurrentPageNumber()?>"
								onblur="<?php echo $this->widgettype?>jumpPage(this.value)"
								onkeypress="<?php echo $this->widgettype?>jumpPageEnter(event)" />
							<span> / <?php echo count($this->manageentries)?>
							</span>
						</div>
						<div class="<?php echo $this->widgettype?>_next_previous">
							<?php if ($this->manageentries->getCurrentPageNumber() > 1): ?>
							<span
								id="<?php echo $this->widgettype?>_user_group_members_previous">
								<?php
								echo $this->htmlLink('javascript:void(0);', "<img src='" . $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/previous-icon.png' . "'/>", array(
															  'onclick' => $this->widgettype.'paginateEntriesWinCompany('.$this->widgettype.'Page - 1)',
															  'title' => $this->translate('Previous')
													  )); ?>
							</span>
							<?php endif; ?>
							<?php if ($this->manageentries->getCurrentPageNumber() < $this->manageentries->count()): ?>
							<span id="user_group_members_next"> <?php  echo $this->htmlLink('javascript:void(0);', "<img src='" . $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/next-icon.png' . "'/>", array(
									'onclick' => 	$this->widgettype.'paginateEntriesWinCompany('.$this->widgettype.'Page + 1)',
									'title' => $this->translate('Next')
																));?>
							</span>
							<?php endif; ?>
						</div>
						<?php endif;?>
						<ul id="ynContest_entries_listing>"
							class="ynContest_listCompare thumbs">
							<?php foreach ($this->manageentries as $entry): ?>
							<li style="width:<?php echo $this->width?>px; height: <?php echo $this->height?>px;">
								<?php echo $this->partial('_formItem.tpl','yncontest' ,		
										array(
															'item' => $entry,
															'manage_entries' => true,
														))
														?>
							</li>
							<?php endforeach;?>
						</ul>
					</div>
				</div>

				<?php endif; ?>
			</div>
			<div class='buttons'>
				<div class="form-label" id="button-label">
					<label>
					</label>
				</div>
				<div class="form-element">
					<button id="save" name="save" type='submit'>
						<?php echo $this->translate("Save") ?>
					</button>
				</div>
			</div>
		</div>
	</form>
</div>
