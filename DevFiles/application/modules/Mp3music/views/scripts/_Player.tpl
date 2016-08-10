<?php
$album = $this->album;
$check_mobile = false;
if(defined('YNRESPONSIVE'))
{
	if(Engine_Api::_() -> ynresponsive1() -> isMobile())
		$check_mobile = true;
}
?>
<?php if(count($this->songs) > 0):?>
<div class = "mp3music_wrapper">
	<div class = "mp3music_album_thumb">
		<?php echo $this->itemPhoto($album, 'thumb.profile') ?>
		<span class = "mp3music_expand_thumb"></span>
	</div>
	<div class="mp3music_container younet_mp3music_feed init">
		<div class="mp3music_feed_player">
				<div class="album_info">
					<div class="mainplayer_image_album">  
						<?php echo $this->itemPhoto($album, 'thumb.normal') ?>
					</div>
					<?php
						$title = $this->songs[0]['title'];
						//$title = $this->string()->truncate($title, 48);
					?>
					<div class = "feed_player_right">
					<span id="song-title-head"><?php echo $title;?></span>
					<audio class="yn-audio-skin" class="mejs"  src="<?php echo $this->songs[0]['filepath'];?>" type="audio/mp3" controls="controls" preload="none" width = "100%"></audio>
					</div>
				</div>				
		</div>
		
		<ul class="song-list mejs-list scroll-pane">
			<?php foreach ($this->songs as $index => $arSong):?>
			<?php 
				$title = $this->string()->truncate($arSong['title'], 31);
			?>
			<li class="<?php echo $index == 0 ? 'current': '';?>">
				<span class="song_id" style="display: none;"><?php echo $arSong['song_id'];?></span>
				<span class="song_vote" style="display: none;"><?php echo $arSong['vote'];?></span>
				<span class="link"><?php echo $arSong['filepath'];?></span>
				<a href="javascript:void(0)" onclick="_addPlaylist(<?php echo $arSong['song_id']; ?>)" class="yn-add-playlist">
					<?php echo $this->translate('Add to playlist');?>
				</a>
				<?php if($arSong['isdownload'] == 'true'):?>
					<a href="<?php echo "http://" . $_SERVER['SERVER_NAME'] . $this ->baseUrl() . '/application/modules/Mp3music/externals/scripts/download.php?idsong='.$arSong['song_id'].'&f='.$arSong['filepath'].'&fc=\''.$arSong['title'].'.mp3\'';?>" onclick="javascript:void(0)" target = "_blank" class = "yn-download">
						<?php echo $this->translate('download');?>
					</a>
				<?php else:?>
				<a class = "yn-download ynmp3music-disable" href = "javascript:void(0)"><?php echo $this->translate('download');?></a>
				<?php endif;?>
				<div class = "mp3music-song-title" <?php if($check_mobile) echo 'onclick="next_song(this)'; ?>">
					<span class="song-title"><?php echo ++$index .'. '. $title;?></span>
					<span class="yn-play"><?php echo "(".$this->translate(array('%s play','%s plays',$arSong['play_count']),$arSong['play_count']).")";?></span>
				</div>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<?php else:?>
<div class = "tip">
	<span><?php echo $this->translate('There are no songs uploaded yet');?></span>
</div>
<?php endif;?>

<?php 
	//echo $this->action("list", "comment", "core", array("type"=>($this->type == 'album')?"mp3music_album":"mp3music_playlist", "id"=>$album_id));
?>
