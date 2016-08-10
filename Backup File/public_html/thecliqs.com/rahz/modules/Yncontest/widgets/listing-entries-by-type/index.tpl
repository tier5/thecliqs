<?php if( count($this->entries) > 0 ): ?>
	<script type ="text/javascript">
		var changeOrder_<?php echo $this->entryType?> = function(obj)
		{
			var browseby = obj.get('value');
			var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
		    en4.core.request.send(new Request.HTML({
		      'url' : url,
		      'data' : {
		        'format' : 'html',
		        'browseby' : browseby
		      },
			  'onRequest' : function(){
					$('image_loading_<?php echo $this->entryType?>').style.display = '';
					$('ynContest_entries_listing_<?php echo $this->entryType?>').style.display = 'none';
			  },
			  'onSuccess' : function(responseJSON, responseText)
			  {
			  	console.log(responseJSON);
			    console.log(responseText);
					$('image_loading_<?php echo $this->entryType?>').style.display = 'none';
					$('ynContest_entries_listing_<?php echo $this->entryType?>').style.display = '';
			  }
		    }), {
		      'element' : $('ynContest_entries_type_<?php echo $this->entryType?>').getParent()
		    });
		}
	</script>

	<div class="yncontest_entries_select">
		<select id="search_category" onchange="changeOrder_<?php echo $this->entryType?>(this)">
			<?php foreach($this->options as $key => $option):?>
				<?php if($this->browseby == $key):?>
					<option selected="selected" value="<?php echo $key;?>" label="<?php echo $this->translate($option);?>"><?php echo $this->translate($option);?></option>
				<?php else:?>
					<option value="<?php echo $key;?>" label="<?php echo $this->translate($option);?>"><?php echo $this->translate($option);?></option>
				<?php endif;?>

			<?php endforeach;?>
		</select>
	 </div>
	 <div id="image_loading_<?php echo $this->entryType?>" style="display: none"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/loading.gif'?>"/></div>

	 <br/>
	<div id= "ynContest_entries_type_<?php echo $this->entryType?>">
		<ul id="ynContest_entries_listing_<?php echo $this->entryType?>" class="ynContest_listCompare thumbs">
			<?php foreach ($this->entries as $entry): ?>
			<li style="width:<?php echo $this->width?>px; height: <?php echo $this->height?>px;">
					<?php echo $this->partial('_formItem.tpl','yncontest' ,
							array(
									'item' => $entry
								))
					?>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
<?php else: ?>
  <div class="tip">
	<span>
	<?php echo $this->translate('There are no entries.') ?>
	</span>
  </div>
<?php endif; ?>