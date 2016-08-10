<?php
    $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmusic/externals/scripts/owl.carousel.js');
    $this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Ynmusic/externals/styles/owl.carousel.css');
?>

<ul id="ynmusic-featured-albums" class="owl-carousel owl-theme music-items">
	<?php foreach ($this->albums as $album) :?>
	<?php 
		$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($album->cover_id)->current();
		$coverPhotoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/feature_album_cover_default.png';
		if($coverFile){
			$coverPhotoUrl = $coverFile->map();
		}
		$photoUrl = $album->getPhotoUrl();
		if (!$photoUrl)	 $photoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/nophoto_album_thumb_profile.png';
		$artists = $album->getArtists();
		$genres = $album->getGenres();
	?>
	<li class="album-item featured-album music-item item" id="<?php echo $album->getGuid()?>">
		<div class="album-cover music-cover" style="background-image: url('<?php echo $coverPhotoUrl?>')">
			<div class="bg-gradient"></div>
			<div class="album-photo music-photo" style="background-image: url('<?php echo $photoUrl?>')">
				<?php if ($this->viewer()->getIdentity()):?>
				<div class="grid-view-album-action-add-playlist show-hide-action">
					<a class="action-link show-hide-btn" href="javascript:void(0)"><i class="fa fa-plus"></i></a>
					<div class="action-pop-up" style="display: none">
						<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist()) :?>
						<div class='album-action-add-playlist dropdow-action-add-playlist'>
							<?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$album->getGuid()),'ynmusic_playlist', true)?>
							<div rel="<?php echo $url;?>" class="music-loading add-to-playlist-loading" style="display: none;text-align: center">
								<span class="ajax-loading">
							    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
								</span>
							</div>
							<span><?php echo $this-> translate('add to') ?></span>
							<div class="add-to-playlist-notices"></div>
							<div class="box-checkbox">
								<?php echo $this->partial('_add_exist_playlist.tpl', 'ynmusic', array('item' => $album)); ?>
							</div>
					    </div>
						<?php endif;?>
						<div class="album-action-dropdown music-action-dropdown">
							<?php if (Engine_Api::_()->ynmusic()->canCreatePlaylist()) :?>
							<a href="javascript:void(0);" onclick="addNewPlaylist(this, '<?php echo $album->getGuid()?>');" class="action-link add-to-playlist" data="<?php echo $album->getGuid()?>"><i class="fa fa-plus"></i><span class="label"><?php echo $this->translate('Add to new playlist')?></span></a>
							<span class="play_list_span"></span>
							<?php endif;?>
							
							<?php $url = $this->url(array('action' => 'download', 'id' => $album -> getIdentity()), 'ynmusic_album', true);?>	
							<a class="action-link download smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-download"></i><span class="label"><?php echo $this->translate('Download')?></span></a>
							
							<?php if ($album->isCommentable()) :?>
							<?php if( $album->likes()->isLike($this->viewer()) ): ?>
							<a class="action-link like liked" href="javascript:void(0);" onclick="ynmusicUnlike(this, '<?php echo $album->getType()?>', '<?php echo $album->getIdentity() ?>')" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-thumbs-up"></i><span class="label"><?php echo $this->translate('Unlike')?></span></a>
							<?php else: ?>
							<a class="action-link like" href="javascript:void(0);" onclick="ynmusicLike(this, '<?php echo $album->getType()?>', '<?php echo $album->getIdentity() ?>')" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-thumbs-up"></i><span class="label"><?php echo $this->translate('Like')?></span></a>
							<?php endif;?>
							<?php endif;?>
							
							<?php if ($this->viewer()->getIdentity()):?>
							<?php $url = $this -> url(array(
						        'module' => 'activity',
						        'controller' => 'index',
						        'action' => 'share',
						        'type' => 'ynmusic_album',
						        'id' => $album->getIdentity(),
						        'format' => 'smoothbox'),'default', true) ?>
							<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-share-alt"></i><span class="label"><?php echo $this->translate('Share')?></span></a>
							<?php endif;?>
							
							<?php if ($album->isEditable()) :?>
							<?php $url = $this->url(array('action' => 'edit', 'album_id' => $album -> getIdentity()), 'ynmusic_album', true);?>	
							<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-pencil-square-o"></i><span class="label"><?php echo $this->translate('Edit')?></span></a>
							<?php endif;?>
							
							<?php if ($album->isDeletable()) :?>
							<?php $url = $this->url(array('action' => 'delete', 'id' => $album -> getIdentity()), 'ynmusic_album', true);?>
							<a class="action-link delete smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-trash"></i><span class="label"><?php echo $this->translate('Delete')?></span></a>
							<?php endif;?>
							
							<?php if ($this->viewer()->getIdentity() && !$album->isOwner($this->viewer())):?>
							<?php $url = $this->url(array(
						        'module' => 'core',
						        'controller' => 'report',
						        'action' => 'create',
						        'subject' => $album->getGuid(),
						        'format' => 'smoothbox'),'default', true)?>
						    <a class="action-link report smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>"><i class="fa fa-ban"></i><span class="label"><?php echo $this->translate('Report')?></span></a>
							<?php endif;?>
							
							<a class="action-link cancel"><i class="fa fa-times"></i><span class="label"><?php echo $this->translate('Cancel')?></span></a>
						</div>
					</div>
				</div>
				<?php endif;?>
			</div>
			
			<div class="album-info music-info">
				<div class="album-title music-title"><?php echo $album?></div>
				<?php if (!empty($artists)) :?>
				<div class="album-artist music-artist">
					<span class="label"><?php echo $this->translate('Artist:')?></span>
					<span class="value"><?php echo implode(', ', $artists)?></span>
				</div>
				<?php endif;?>
				<div class="clearfix"></div>
				<div class="album-genre-song_count">
				<?php if (!empty($genres)) :?>
					<span class="label"><?php echo $this->translate('Genre:')?></span>
					<span class="genre value"><?php echo implode(', ', $genres)?></span>
					<span class="separator"> &middot; </span>
				<?php endif;?>
					<span class="album-song_count music-song_count">
						<?php echo $this->translate(array('ynmusic_song_count_num_ucf', '%s Songs', $album->getCountSongs()), $album->getCountSongs())?>
					</span>
				</div>
				
				
			</div>
			<div class="box-time-image-song" style="width: calc(100% - 60px)">
				<div class="playing-song-title"></div>
				<?php $firstSong = $album->getFirstSong();?>
				<?php if ($firstSong) : ?>
				<div class="time-song">
					<div class="duration-time"><?php echo date('i:s', $firstSong->duration)?></div>
				</div>
				<?php 
					$noPlayImg = $firstSong->getNoPlayImage();
					$playImg = $firstSong->getPlayImage();
				?>
				<?php if ($noPlayImg && $playImg) :?>
				<div class="image-song">
					<div class="no-play-div" style="background-image: url('<?php echo $noPlayImg?>');">
						<div class="progress-bar no-play"></div>
					</div>
					<div class="play-div" style="width: 0; overflow: hidden; background-image: url('<?php echo $playImg?>');">
						<div class="progress-bar play">
						</div>
					</div>
					<div class="drag-btn"></div>
					<div class="time-hover"></div>
				</div>
				<?php endif;?>
				<?php endif;?>
			</div>
		
			<div class="album-statistic-play_btn music-info-bottom" style="width: calc(100% - 60px)">
				<div class="album-like_count-commend_count music-statistic">
					<span class="like_count"><i class="fa fa-thumbs-up"></i><?php echo number_format($album -> like_count);?></span>
					<span class="comment_count"><i class="fa fa-comments-o"></i><?php echo number_format($album -> comment_count);?></span>
				</div>
				
				<div class="play-btn-<?php echo $album->getGuid()?> music-play-btn music-play-btn<?php if(!$album->getCountAvailableSongs()) echo '-disabled'?>">
					<a href="javascript:void(0)">
						<i rel="<?php echo $album->getGuid()?>" class="fa fa-play"></i>
					</a>
				</div>
				
				<div class="album-play_count-duration music-statistic">
					<span class="play_count"><i class="fa fa-headphones"></i><?php echo $album -> play_count;?></span>
					<span class="duration"><i class="fa fa-clock-o"></i><?php echo $album -> getDuration();?></span>
				</div>
			</div>
		</div>
	</li>
	<?php endforeach;?>
</ul>

<script type="text/javascript">
en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});

jQuery.noConflict();
jQuery(document).ready(function() {
	var owl_article = jQuery('#ynmusic-featured-albums');
	var item_amount = parseInt(owl_article.find('.item').length); 
	var true_false = 0;
	if (item_amount > 1) {
		true_false = true;
	}else{
		true_false = false;
	}

	var rtl = false;
	if(jQuery("html").attr("dir") == "rtl") {
		rtl = true;
	}

  	jQuery("#ynmusic-featured-albums").owlCarousel({
	rtl:rtl,
	nav:true_false,
	navText:["",""],
	loop: true_false,
	mouseDrag:true_false,	
	autoplay:true_false,
	dotsSpeed:1000,
	autoplayHoverPause:true,
	items:1
  });
  
  $$('.drag-btn').each(function(el) {
		var parent = el.getParent('.image-song');
		var width = parent.getSize().x;
		new Drag(el, {
			'limit': {
				x: [0, width]
			}, 
	    	'modifiers': {'x': 'left', 'y': null}, 
	    	onDrag: function(obj) {
	    		parent.addClass('on-drag');
				var play_div = parent.getElement('.play-div');
				var left = obj.getCoordinates(parent).left;
				left = left + 8;
				var width = parent.getSize().x;
				play_div.setStyle('width', left+'px');
	    	},
	    	onComplete: function(obj) {
	    		parent.removeClass('on-drag');
				var left = obj.getCoordinates(parent).left;
				left = left + 8;
				percent = left*100/width;
				updatePlayerTime(percent);
	    	}
		});
	});
	
	$$('.image-song').addEvent('mousemove', function(event) {
		var left = this.getPosition().x;
		var width = this.getSize().x;
		var pos = event.page.x - left;
		var percent = pos*100/width;
		var player = $('ynmusic-player');
		var hoverTime = this.getElement('.time-hover');
		if (player && hoverTime && player.duration) {
			var duration = player.duration;
			var time = percent*duration/100;
			var s = parseInt(time % 60);
			var m = parseInt((time / 60) % 60);
			if (s < 10) s = '0'+s;
			if (m < 10) m = '0'+m;
			hoverTime.innerHTML = m+':'+s;
			hoverTime.setStyle('margin-left', pos);
			hoverTime.show();
		}
		else {
			hoverTime.hide();
		}
	});
	
	$$('.image-song').addEvent('mouseleave', function(event) {
		var hoverTime = this.getElement('.time-hover');
		if (hoverTime) {
			hoverTime.hide();
		}
	});
	
	$$('.image-song').addEvent('click', function(event) {
		var left = this.getPosition().x;
		var width = this.getSize().x;
		var pos = event.page.x - left;
		var percent = pos*100/width;
		updatePlayerTime(percent);
	});
	
});
	
	function addNewPlaylist(ele, guid) {
		var nextEle = ele.getNext();
		if(nextEle.hasClass("ynmusic_active_add_playlist")) {
			//click to close
			nextEle.removeClass("ynmusic_active_add_playlist");
			nextEle.setStyle("display", "none");
		} else {
			//click to open
			nextEle.addClass("ynmusic_active_add_playlist");
			nextEle.setStyle("display", "block");
		}
		$$('.play_list_span').each(function(el){
			if(el === nextEle){
				//do not empty the current box
			} else {
				el.empty();
				el.setStyle("display", "none");
				el.removeClass("ynmusic_active_add_playlist");
			}
		});
		var data = guid;
		var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynmusic_playlist', true);?>';
		var request = new Request.HTML({
	        url : url,
	        data : {
	        	subject: data,
	        },
	        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
	            var spanEle = nextEle;
	            spanEle.innerHTML = responseHTML;
	            eval(responseJavaScript);
	            
	            var popup = spanEle.getParent('.action-pop-up');
	            var layout_parent = popup.getParent('.layout_middle');
		    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
		    	var y_position = popup.getPosition(layout_parent).y;
				var p_height = layout_parent.getHeight();
				var c_height = popup.getHeight();
	    		if(p_height - y_position < (c_height + 21)) {
	    			layout_parent.addClass('popup-padding-bottom');
	    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
	    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
				}
	        }
	    });
	    request.send();
	}
	
	function addToPlaylist(ele, playlistId, guild) {
		var checked = ele.get('checked');
		var data = guild;
		var url = '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynmusic_playlist', true);?>';
		var request = new Request.JSON({
	        url : url,
	        data : {
	        	subject: data,
	        	playlist_id: playlistId,
	        	checked: checked,
	        },
	        onSuccess: function(responseJSON) {
	        	if (!responseJSON.status) {
	        		ele.set('checked', !checked);
	        	}
	            var div = ele.getParent('.action-pop-up');
	            var notices = div.getElement('.add-to-playlist-notices');
	            var notice = new Element('div', {
	            	'class' : 'add-to-playlist-notice',
	            	text : responseJSON.message
	            });
	            notices.adopt(notice);
	            notice.fade('in');
	            (function() {
	            	notice.fade('out').get('tween').chain(function() {
	            		notice.destroy();
	            	});
	        	}).delay(3000, notice);
	        }
	    });
	    request.send();
	}
</script>