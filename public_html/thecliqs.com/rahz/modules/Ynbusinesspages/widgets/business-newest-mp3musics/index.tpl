<!-- Content -->
<?php if( $this->paginator->getTotalItemCount() > 0 ): 
$business = $this->business;?>
<ul class="thumbs ynbusinesspages_music">  			
	<?php foreach ($this->paginator as $album): 
		$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($album);?>     	
	<li id="mp3music_album_item_<?php echo $album->getIdentity() ?>">
		<div class="mp3music_browse_info music_browse_info">
			<div class="photo">
				<a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)">
					<?php if($album -> getPhotoUrl("thumb.profile")): ?>
						<span class="image-thumb" style="background-image:url('<?php echo $album -> getPhotoUrl("thumb.profile"); ?>')"></span>
					<?php else: ?>
						<span class="image-thumb" style="background-image:url('<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynbusinesspages/externals/images/nophoto_music_playlist.png')"></span>
					<?php endif; ?>
				 </a> 
			</div>
			<div class="info">
				<div class="mp3music_browse_info_title title">					
				<?php if($album->getSongIDFirst($album->album_id)): ?>
					<a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)"><?php echo $album->getTitle() ?></a>
				<?php else: ?>
					<?php echo $album->getTitle() ?>
				<?php endif; ?>					
				</div>
				<div class="stats">
					<div class="author-name">
					<?php if(Engine_Api::_() -> ynbusinesspages() -> getSingers($album->album_id)): ?>
						<?php echo Engine_Api::_() -> ynbusinesspages() -> getSingers($album->album_id);?>
					<?php else: ?>
						<?php echo $this->htmlLink($owner, $owner->getTitle()) ?>
					<?php endif; ?>
					</div>							
				</div>
			</div>
		</div>
	</li>
	<?php endforeach; ?> 		 
</ul>  

<?php endif; ?>
