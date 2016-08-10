<?php
//$src = "http://socialengine4-demo.s3.amazonaws.com/public/user/8a/04/0486_c7af.jpg";
//$photo1 =  Engine_Api::_()->yncontest()->getEntryThumnail($this->entry1->entry_type,$this->entry1->item_id);

if(is_object($photo1))		
$src1 =  $photo1->getPhotoUrl('thumb.profile');

//$photo2 =  Engine_Api::_()->yncontest()->getEntryThumnail($this->entry2->entry_type,$this->entry2->item_id);
if(is_object($photo2))		
$src2 =  $photo2->getPhotoUrl('thumb.profile');

?>
<ul id="compare_results" class="ynContest_itemCompareWrapper thumbs clearfix">
	<li class = "ynContest_itemCompare ynContest_itemCompareLeft">
		<a class="thumbs_photo" href="<?php echo $this->entry1->getHref(); ?>">
			<img src = "<?php echo $src1; ?>" />
		</a>
		<p class="ynContest_ItemCompareInfo thumbs_info">
			<span class="ynContest_Item_thumbsTitle thumbs_title">					
				<a href="<?php echo $this->entry1->getHref(); ?>" title="<?php echo $this->entry1->entry_name ?>">
					<?php echo Engine_Api::_() -> yncontest() -> subPhrase($this->entry1->entry_name,90); ?>			
				</a>
			</span>
			<span>
				<?php echo $this->translate('Vote')." : ".$this->entry1->vote_count ;?>
			</span><br/>
			<span>
				<?php echo $this->translate('View')." : ".$this->entry1->view_count ;?>
			</span><br/>			
			<span>
				<?php echo $this->translate('Like')." : ".$this->entry1->like_count ;?>
			</span><br/>
			<span>
				<?php echo $this->translate('Comment')." : ".$this->entry1->comment_count ;?>
			</span>
		</p>
		<?php echo $this->form1->render($this);?>
	</li>
	<li class="ynContest_itemCompare ynContest_itemCompareRight" id= "ynContest_itemCompareRight">
		<a class="thumbs_photo" href="<?php echo $this->entry2->getHref(); ?>">
			<img src = "<?php echo $src2; ?>" />
		</a>
		<p class="ynContest_ItemCompareInfo thumbs_info">
			<span class="ynContest_Item_thumbsTitle thumbs_title">					
				<a href="<?php echo $this->entry2->getHref(); ?>" title="<?php echo $this->entry2->entry_name ?>">
					<?php echo Engine_Api::_() -> yncontest() -> subPhrase($this->entry2->entry_name,90); ?>			
				</a>
			</span>
			<span>
				<?php echo $this->translate('Vote')." : ".$this->entry2->vote_count ;?>
			</span><br/>
			<span>
				<?php echo $this->translate('View')." : ".$this->entry2->view_count ;?>
			</span><br/>			
			<span>
				<?php echo $this->translate('Like')." : ".$this->entry2->like_count ;?>
			</span><br/>
			<span>
				<?php echo $this->translate('Comment')." : ".$this->entry2->comment_count ;?>
			</span>
		</p>
		<?php echo $this->form2->render($this);?>
	</li>
</ul>