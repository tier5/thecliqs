<?php if (count($this->songs)) :?>
<ul class="song-items music-items">
<?php $count = 1;?>
<?php foreach($this->songs as $song) :?>
	<li class="song-item music-item clearfix" id="<?php echo $song->getGuid()?>">
		<?php if ($this->feed):?>
		<div class="song-count"><?php echo $count?></div>
		<?php endif; ?>

		<?php if ($this->detail || $this->feed):?>
		<div class="song-photo"><?php echo $this->htmlLink($song->getHref(), $this->itemPhoto($song, 'thumb.icon'))?></div>
		<?php endif;?>
		
		<?php if (!$this->feed):?>
		<div class="song-count"><?php echo $count?></div>
		<?php endif; ?>

		<div class="title-artist">
			<div class="title"><?php echo $song?></div>
			<?php $artists = $song->getArtists();?>
			<?php if (!empty($artists)):?>
			<div class="seperate"> - </div>
			<div class="artist"><?php echo implode(', ', $artists)?></div>
			<?php endif;?>
			<div class="play-count"><i class="fa fa-headphones"></i><?php echo $song -> play_count;?></div>
		</div>
		<div class="action">
			<?php if ($this->detail):?>
			<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist() || Engine_Api::_()->ynmusic()->canCreatePlaylist()) :?>
			<div class="list-view-song-action-add-playlist show-hide-action">
				<a class="action-link show-hide-btn" href="javascript:void(0)" title="<?php echo $this->translate('Add to playlist')?>"><i class="fa fa-plus"></i></a>
				<div class="action-pop-up" style="display: none">
					<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist()): ?>
					<div class='song-action-add-playlist dropdow-action-add-playlist'>
						<span><?php echo $this-> translate('add to') ?></span>
						<?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$song->getGuid()),'ynmusic_playlist', true)?>
						<div rel="<?php echo $url;?>" class="music-loading add-to-playlist-loading" style="display: none;text-align: center">
							<span class="ajax-loading">
						    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
							</span>
						</div>
						<div class="add-to-playlist-notices"></div>
						<div class="box-checkbox">
							<?php echo $this->partial('_add_exist_playlist.tpl', 'ynmusic', array('item' => $song)); ?>
						</div>
				    </div>
				    <?php endif;?>
				    <?php if (Engine_Api::_()->ynmusic()->canCreatePlaylist()): ?>
					<div class="song-action-dropdown music-action-dropdown">
						<a href="javascript:void(0);" onclick="addNewPlaylist(this, '<?php echo $song->getGuid()?>');" class="action-link add-to-playlist" data="<?php echo $song->getGuid()?>"><i class="fa fa-plus"></i><span class="label"><?php echo $this->translate('Add to new playlist')?></span></a>
						<span class="play_list_span"></span>
						
						<a class="action-link cancel"><i class="fa fa-times"></i><span class="label"><?php echo $this->translate('Cancel')?></span></a>
					</div>
					<?php endif;?>
				</div>
			</div>
			<?php endif;?>
			
			<?php if ($song->isDownloadable()) :?>
			<span><a class="action-link download" download="<?php echo $song->getTitle().'.mp3'?>" href="<?php echo $song->getFilePath()?>" rel="<?php echo $song->getIdentity()?>" title="<?php echo $this->translate('Download')?>"><i class="fa fa-download"></i></a></span>
			<?php endif;?>
			
			<?php if ($song->isCommentable()) :?>
			<?php if( $song->likes()->isLike($this->viewer()) ): ?>
			<span><a class="action-link like liked" href="javascript:void(0);" onclick="ynmusicUnlike(this, '<?php echo $song->getType()?>', '<?php echo $song->getIdentity() ?>')" rel="<?php echo $song->getIdentity()?>" title="<?php echo $this->translate('Unlike')?>"><i class="fa fa-thumbs-up"></i></a></span>
			<?php else: ?>
			<span><a class="action-link like" href="javascript:void(0);" onclick="ynmusicLike(this, '<?php echo $song->getType()?>', '<?php echo $song->getIdentity() ?>')" rel="<?php echo $song->getIdentity()?>" title="<?php echo $this->translate('Like')?>"><i class="fa fa-thumbs-up"></i></a></span>
			<?php endif;?>
			<?php endif;?>
			
			<?php if ($this->viewer()->getIdentity()):?>
			<?php $url = $this -> url(array(
		        'module' => 'activity',
		        'controller' => 'index',
		        'action' => 'share',
		        'type' => 'ynmusic_song',
		        'id' => $song->getIdentity(),
		        'format' => 'smoothbox'),'default', true) ?>
			<span><a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $song->getIdentity()?>" title="<?php echo $this->translate('Share')?>"><i class="fa fa-share-alt"></i></a></span>
			<?php endif;?>
			<?php endif; ?>
			<span class="play-btn-<?php echo $song->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i parent="<?php if($this->parent) echo $this->parent->getGuid()?>" rel="<?php echo $song->getGuid()?>" class="fa fa-play"></i></a></span>
		</div>
	</li>
<?php $count++;?>
<?php endforeach; ?>
</ul>

<?php if (count($this->songs) > 5 && empty($this->detail)) :?>
<a class="view-all-songs" href="javascript:void(0)"><?php echo $this->translate(array('View all %s song.', 'View all %s songs.', count($this->songs)), count($this->songs));?></a>
<a class="view-fewer-songs" href="javascript:void(0)"><?php echo $this->translate('View fewer songs.');?></a>
<?php endif;?>
<?php endif;?>

<script type="text/javascript">
	en4.core.runonce.add(function(){
		$$('.music-songs .view-all-songs').addEvent('click', function() {
			var parent = this.getParent('.music-songs');
			var ul = parent.getElement('ul.music-items');
			if (!ul) return;
			ul.addClass('ynmusic-view-all-songs');
			this.hide();
			var fewer = parent.getElement('.view-fewer-songs');
			fewer.show();
		});
		
		$$('.music-songs .view-fewer-songs').addEvent('click', function() {
			var parent = this.getParent('.music-songs');
			var ul = parent.getElement('ul.music-items');
			if (!ul) return;
			ul.removeClass('ynmusic-view-all-songs');
			this.hide();
			var all = parent.getElement('.view-all-songs');
			all.show();
			
			//update scroll playlist
			var playing = parent.getElement('.song-item.playing');
			if (playing) scrollInPlaylist(playing);
			else {
				var myFx = new Fx.Scroll(ul).toElement(parent.getElement('.song-item'));
			}
		});
		
		if (typeof addEventForPlayBtn == 'function') { 
		  	addEventForPlayBtn(); 
		}
	});
</script>