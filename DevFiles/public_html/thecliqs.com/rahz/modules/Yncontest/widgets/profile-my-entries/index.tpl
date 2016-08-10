<?php if($this->items_per_pageadvalbum>0):?>
	<h3><?php echo $this->arrPlugins['advalbum']."(".$this->items_per_pageadvalbum.")"?></h3>
	<div id= "ynContest_entries_type">
		<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
			<?php foreach ($this->paginatoradvalbum as $entry):?>
			<li style="width:<?php echo $this->arrTemp[$entry->entry_type]['width']?>px; height: <?php echo $this->arrTemp[$entry->entry_type]['height']?>px;">
					<?php echo $this->partial('_formItem.tpl','yncontest' ,		
							array(									
									'item' => $entry
								)) 
					?> 
			</li>
			<?php endforeach;?>	
		</ul>
	</div>
<?php endif;?>
<?php if($this->items_per_pageynblog>0):?>
	<h3><?php echo $this->arrPlugins['ynblog']."(".$this->items_per_pageynblog.")"?></h3>
	<div id= "ynContest_entries_type">
		<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
			<?php foreach ($this->paginatorynblog as $entry): ?>
			<li style="width:<?php echo $this->arrTemp[$entry->entry_type]['width']?>px; height: <?php echo $this->arrTemp[$entry->entry_type]['height']?>px;">
					<?php echo $this->partial('_formItem.tpl','yncontest' ,		
							array(									
									'item' => $entry
								)) 
					?> 
			</li>
			<?php endforeach;?>	
		</ul>
	</div>
<?php endif;?>
<?php if($this->items_per_pageynvideo>0):?>	
	<h3><?php echo $this->arrPlugins['ynvideo']."(".$this->items_per_pageynvideo.")"?></h3>
	<div id= "ynContest_entries_type">
		<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
			<?php foreach ($this->paginatorynvideo as $entry): ?>
			<li style="width:<?php echo $this->arrTemp[$entry->entry_type]['width']?>px; height: <?php echo $this->arrTemp[$entry->entry_type]['height']?>px;">
					<?php echo $this->partial('_formItem.tpl','yncontest' ,		
							array(									
									'item' => $entry
								)) 
					?> 
			</li>
			<?php endforeach;?>	
		</ul>
	</div>
<?php endif;?>
<?php if($this->items_per_pagemp3music>0):?>	
	<h3><?php echo $this->arrPlugins['mp3music']."(".$this->items_per_pagemp3music.")"?></h3>
	<div id= "ynContest_entries_type">
		<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
			<?php foreach ($this->paginatormp3music as $entry): ?>
			<li style="width:<?php echo $this->arrTemp[$entry->entry_type]['width']?>px; height: <?php echo $this->arrTemp[$entry->entry_type]['height']?>px;">
					<?php echo $this->partial('_formItem.tpl','yncontest' ,		
							array(									
									'item' => $entry
								)) 
					?> 
			</li>
			<?php endforeach;?>	
		</ul>
	</div>
<?php endif;?>