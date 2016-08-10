<?php					
$album = $this->album;
$check_mobile = false;
if(defined('YNRESPONSIVE'))
{
	if(Engine_Api::_() -> ynresponsive1() -> isMobile())
		$check_mobile = true;
}
?>
	<!-- HTML5 -->
	<div class="younet_mp3music_share mp3music_container init">
		<div class="mp3music_share_player">
				<div class="share_album_info">
					<div class="share_image_album">  
						<?php echo $this->itemPhoto($album, 'thumb.normal', $album->getTitle()) ?>
					</div>
					<div class = "feed_player_right">
					<span id="song-title-head"><?php echo $this->string()->truncate($this->songs[0]['title'], 80);?></span>
					<audio class="yn-audio-skin" class="mejs" width="100%" src="<?php echo $this->songs[0]['filepath'];?>" type="audio/mp3" controls="controls" autoplay="true" preload="none"></audio>
					</div>
				</div>				
		</div>
		
		<ul class="mejs-list scroll-pane song-list">
			<?php foreach ($this->songs as $index => $arSong):?>
			<?php 
				$title = $this->string()->truncate($arSong['title'], 40);
			?>
				<li class="<?php echo $index == 0 ? 'current': '';?>">
					<span class="song_id" style="display: none;"><?php echo $arSong['song_id'];?></span>
					<span class="song_vote" style="display: none;"><?php echo $arSong['vote'];?></span>
					<span class="link"><?php echo $arSong['filepath'];?></span>
					
					<?php if($arSong['isdownload'] == 'true'):?>
						<a href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . $this ->baseUrl() . '/application/modules/Mp3music/externals/scripts/download.php?idsong='.$arSong['song_id'].'&f='.$arSong['filepath'].'&fc=\''.$arSong['title'].'.mp3\'';?>" onclick="javascript:void(0)" target = "_blank" class = "yn-download">
							<?php echo $this->translate('download')?>
						</a>
					<?php else:?>
					<a class = "yn-download ynmp3music-disable" href = "javascript:void(0)"><?php echo $this->translate('download');?></a>
					<?php endif;?>
					
					<div class = "mp3music-song-title" <?php if($check_mobile) echo 'onclick="next_song(this)'; ?>">
						<span class="song-title"><?php echo ++$index.'. '.$this->string()->truncate($arSong['title'],47);?></span>
						<span class="yn-play"><?php echo "(".$this->translate(array('%s play','%s plays',$arSong['play_count']),$arSong['play_count']).")";?></span>
					</div>	
				</li>
			<?php endforeach;?>
		</ul>
	</div>
	<!-- END -->
