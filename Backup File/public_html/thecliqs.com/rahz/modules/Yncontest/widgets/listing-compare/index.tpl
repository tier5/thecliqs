<script>
	function choose(item){
new Request.JSON({
'format': 'json',
'url' : '<?php echo $this->url(array('action' => 'get-entries-compare'), 'yncontest_myentries',true) ?>',
'data' : {
'entry_id' : item,
},
'onRequest' : function(){
},
'onSuccess' : function(responseJSON, responseText)
{
var   div = responseJSON.itemHTML;
div += "<p class = 'ynContest_ItemCompareInfo thumbs_info'>";
div += "<span class = 'ynContest_Item_thumbsTitle thumbs_title'>"+responseJSON.entry_name+"</span>";
div += "<span>Start : "+responseJSON.start_date+"</span><br/>";
div += "<span>Vote : "+responseJSON.vote+"</span><br/>";
div += "<span>Like : "+responseJSON.like+"</span><br/>";
div += "<span>Owner : "+responseJSON.owner+"</span>";
div += "<input type='hidden' name='cid' id='cid' value='"+responseJSON.entry_id+"' /></ul>";
$('ynContest_itemCompareRight').innerHTML = div;
$('ynContest_itemCompareRight').style.display = 'block';
}
}).send();
}

function compare(){
if($('cid')=='' || $('cid')== null ){
alert("<?php echo "Please choose items to compare !";?>");
}else{
var id = <?php $request = Zend_Controller_Front::getInstance() -> getRequest();
	echo $request -> getParam('entry_id');
 ?>;
var id2 = $('cid').value;
var uri = new URI("<?php echo $this->url(array('action' => 'entries-compare'), 'yncontest_myentries', true)?>
	");
	uri.setData('entry_id', [id, id2]);
	Smoothbox.open(uri.toString());
	}
	}
</script>
<ul class="ynContest_itemCompareWrapper thumbs clearfix">
	<li class = "ynContest_itemCompare ynContest_itemCompareLeft">
		<?php if(is_object($this->entries)): ?>
		<a class="thumbs_photo" href="<?php echo $this->entries->getHref()?>">
			<?php
				$photo = Engine_Api::_() -> yncontest() -> getEntryThumnail($this -> entries -> entry_type, $this -> entries -> item_id);
				$src = "";
				if (is_object($photo))
					$src = $photo -> getPhotoUrl('thumb.profile');
				//echo $this->itemPhoto($item,'thumb.normal');
				else
				{
					$src = "";
				}
			?>			
			<!-- <span style="background-image: url(<?php //echo $src;?>);"></span> -->
			<img src="<?php echo $src;?>" border="0"/>
		</a>
		<p class="ynContest_ItemCompareInfo thumbs_info">
			<span class="ynContest_Item_thumbsTitle thumbs_title">					
				<?php echo $this -> htmlLink($this -> entries -> getHref(), wordwrap(Engine_Api::_() -> yncontest() -> subPhrase(strip_tags($this -> entries -> entry_name), 80), 98, "\n", true), array('title' => $this->string()->stripTags($this->entries->entry_name)));?>				
			</span>
			<span>
				<?php echo $this -> translate("Start date") . " : " . $this->locale()->toDate( $this -> entries -> start_date, array('size' => 'short'));?>
			</span><br/>
			<span>
				<?php echo $this -> translate("Vote") . " : " . $this -> entries -> vote_count;?>
			</span><br/>
			<span>
				<?php echo $this -> translate("Like") . " : " . $this -> entries -> like_count;?>
			</span><br/>
			<span>
				<?php echo $this -> translate("Owner") . " : " . $this -> entries -> getOwner();?>
			</span>
		</p>
		<?php endif;?>
	</li>
	<li style="display: none;" class="ynContest_itemCompare ynContest_itemCompareRight" id= "ynContest_itemCompareRight"></li>
	<form method="post" id ="">
		<button type="button" name="submit" onclick="compare()" id="compare_submit">
			<?php echo $this -> translate('Compare');?>
		</button>
	</form>
</ul>	
<?php if( count($this->paginator) > 0 ): ?>
	<h4><span><?php echo $this -> translate('Choose one to compare'); ?></span></h4>
	<ul class="ynContest_listCompare thumbs">
		<?php foreach ($this->paginator as $entry): ?>
		<?php if($entry -> checkVote()):?>
		<li>
			<div>			
				<a class="thumbs_photo" href="<?php echo $entry->getHref()?>">
					<?php
					$item = Engine_Api::_() -> yncontest() -> getEntryThumnail($entry -> entry_type, $entry -> item_id);
					$src = "";
					if (is_object($item))
						$src = $item -> getPhotoUrl('thumb.profile');
					//echo $this->itemPhoto($item,'thumb.profile');
					else
					{
						$src = "";
					}
					?>			
						<span style="background-image: url(<?php echo $src;?>);"></span>
				</a>
				<p class="ynContest_listCompareInfo thumbs_info">
					<span class="thumbs_title">					
						<?php echo $this->htmlLink($entry->getHref(), Engine_Api::_()->yncontest()->subPhrase($entry->entry_name, 50), array('title' => $this->string()->stripTags($entry->entry_name)));?>					
					</span>
					<?php echo $this->translate("Created by %s",$this->htmlLink($entry->getOwner(), $this->string()->truncate($entry->getOwner()->getTitle(),12), array('title'=>$entry->getOwner()->getTitle())));?><br/>			
					<?php echo $this->translate("Start date:")?> <?php echo  $this->locale()->toDate( $entry->start_date, array('size' => 'short'));?><br/>
					<?php echo $this -> translate("Vote : ") . $entry -> vote_count . " - " . $this -> translate("Like : ") . $entry -> like_count;?><br/>
				</p>			
			</div>
			<div class="ynContest_compareEntries">
				<a href="javascript:choose(<?php echo $entry -> entry_id;?>)" class="choose"><?php echo $this -> translate("Choose");?></a>
				<?php /*echo $this->htmlLink(array(
					 'route' => 'yncontest_general',
					 'action' => 'compare-entries',
					 'entry_id' => $entry->getIdentity(),
					 ),
					 $this->translate('Choose'),
					 array('class' => 'buttonlink'));
					 */
				?>
			</div>			
		</li>
		<?php endif; ?>
		<?php endforeach;?>			
	</ul>
<?php if( count($this->paginator) > 1 ):?>
	<?php echo $this -> paginationControl($this -> paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this -> formValues,
		));
	?>
<?php endif;?>
<?php else:?>
<div class="tip">
	<span> <?php echo $this->translate('There are no entries.')	?></span>
</div>
<?php endif;?>