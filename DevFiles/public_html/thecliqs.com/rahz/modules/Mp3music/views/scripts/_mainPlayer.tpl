<?php	
$album = $this->album;
$album_id = 0;
if($this->type == 'album')
{
	$album_id = $album->album_id;
}
else 
{
	$album_id = $album->playlist_id;
}
$check_mobile = false;
if(defined('YNRESPONSIVE'))
{
	if(Engine_Api::_() -> ynresponsive1() -> isMobile())
		$check_mobile = true;
}
$iframe_src = "http://" . $_SERVER['SERVER_NAME'] . $this -> url(array(
	'album_id' => $this->song -> album_id,
	'song_id' => $this->song -> song_id
), 'mp3music_iframe');
$html_code_for_blog = '<table width="380" border="0" style="background-color:#FFFFFF; " cellpadding="5px" > ';
$html_code_for_blog .= '<tr> <td align="center" nowrap="nowrap">';
$html_code_for_blog .= '<iframe src="' . $iframe_src . '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100%; height:100%;" allowTransparency="true"></iframe>';
$html_code_for_blog .= '</td>';
$html_code_for_blog .= '</tr>';
$html_code_for_blog .= '</table>';
?>
<?php if($this->song->song_id == ""): ?>
 <div class="tip">
      <span>
        <?php echo $this->translate('There are no songs uploaded yet.') ?>
      </span>
    </div>
<?php  else:?>
<script type="text/javascript">
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
    en4.core.runonce.add(function() {
      Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');
      en4.core.loader = new Element('img', {src: 'application/modules/Core/externals/images/loading.gif'});
      en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
      <?php if( $this->subject() ): ?>
        en4.core.subject = {type:'<?php echo $this->subject()->getType(); ?>',id:<?php echo $this->subject()->getIdentity(); ?>,guid:'<?php echo $this->subject()->getGuid(); ?>'};
      <?php endif; ?>
    });
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
</script>

<script language="javascript">
//var mp3jQuery = jQuery.noConflict();
(function($){
		$(document).ready(function(){		
			$('#mp3music_rating a').click(function(){
				var songId = $(this).closest('.mp3music_container').find('li.current .song_id').text();
				var star = $(this).text();
				var isVote = $(this).closest('.mp3music_container').find('li.current .isvote').text();
				if( isVote == 1) {
					_rate(songId, <?php echo Engine_Api::_()->user()->getViewer()->getIdentity();?>, star);
					//Update current star
					$(this).closest('.mp3music_container').find('li.current .isvote').text('0');
					isVote = 0;
					$(this).closest('.mp3music_container').find('li.current .song_vote').text(star);
					
					for (var x = 1; x <= parseInt(star); x++) {
						$('#rate_' + x).attr('class', 'mp3music_rated');
					}
					
					for (var x = parseInt(star) + 1; x <= 5; x++) {
						$('#rate_' + x).attr('class', 'mp3music_unrate');
					}
					
					var remainder = Math.round(star) - star;
					if (remainder <= 0.5 && remainder != 0) {
						var last = parseInt(star) + 1;
						$('#rate_' + last).attr('class', 'rating_star_big_generic rating_star_big_half');
					}
					
					if(isVote != 1) {
						for(x = 1; x < 5; x++) {
							$('#rate_' + x).addClass('mp3music_rate_disable');
						}
					}
				}
			});
		});
})(jQuery)
</script>
<div id = "mp3music_reverse">
	<div id="cter_popup">            
		<div class="content-block lyric-content" id="_lyricContainer">
			<div id="_lyricContainerZW6A796B">
				<div id="_lyricZW6A796B_0" class="_lyricItem ">
					<h3><strong><?php echo $this->song -> getTitle();?></strong></h3>
					 <?php if($this->song->lyric != "" && $this->song->lyric != "<p>No Lyric!</p>"): ?>
						<span id="mp3music_lyric_content" class="_lyricContent rows4" expand="rows4">
							<?php echo $this->song->lyric;  ?>
						</span>
					<?php else:?>
						<p><?php echo $this->translate("No Lyric!"); ?></p>
					<?php endif;?>
				</div>
			</div>
			<div class="iLyric">
				 <?php if($this->song->lyric != "" && $this->song->lyric != "<p>No Lyric!</p>"): ?>
				 	<?php $text_hidden = $this->translate('&raquo; Less');
				 	$text_show = $this->translate('&raquo; More');?>
					<a href="javascript:;" onclick="showhide('<?php echo $text_hidden?>','<?php echo $text_show?>', this)" class="read-all _viewMore"><?php echo $this->translate('&raquo; More');?></a>
				<?php endif;?>
			</div>
		</div>            
		
		<ul class="global_form_box" style="margin-bottom: 10px;"> 
			<h4 style="border: none;"><?php echo $this->translate("Embed") ?></h4>  
			<div style="padding-bottom: 10px;">
				<span style="padding-left: 20px; padding-right: 30px"><?php echo $this->translate("Link URL"); ?>:</span>
				<input type="text" style="width: 80%; height:20px;border:1px solid #d2d8db; margin:1px" readonly="readonly" onclick="url_select_text(this)" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array(),'default');?>mp3-music/albums/<?php echo $album_id; ?>/song_id/<?php echo $this->song->song_id; ?>"/>
			</div>
			
			<div style="padding-bottom: 10px;">  
				<span style="padding-left: 20px; padding-right: 14px"><?php echo $this->translate("HTML Code"); ?>:</span>
				<input id="result_url" type="text" style="width: 80%; height:20px;border:1px solid #d2d8db; margin:1px" readonly="readonly" onclick="url_select_text(this)" value="<?php echo htmlspecialchars($html_code_for_blog)?>"/>
			</div>
			
			<div style="padding-bottom: 10px;">  
				<span style="padding-left: 20px; padding-right: 9px"><?php echo $this->translate("Forum Code"); ?>:</span>
				<input type="text" style="width: 80%; height:20px;border:1px solid #d2d8db; margin:1px; " readonly="readonly" onclick="url_select_text(this)" value="[URL=<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array(),'default')?>mp3-music/albums/<?php echo $album_id; ?>/song_id/<?php echo $this->song->song_id; ?>]<?php echo $this->song->title; ?>[/URL]"/>
			</div>
		</ul>
	</div>
	
<ul id = "ynmp3music-wrapper">
	<div id = "ynmp3music-inner">
    <h3 style="color: #464646;"><?php echo $album->title; ?></h3>
			<div  style="margin-bottom: 20px; height: 5px;">
				<div class="mp3_album_info_player" style="float: left; margin-right: 10px;">
				<?php echo $this->translate('Posted by %1s on %2s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle()),$this->timestamp($album->creation_date)) ?>
				</div>
				<!-- AddThis Smart Layers BEGIN -->
				<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e4e2c280039ea82"></script>
				<script type="text/javascript">
				  addthis.layers({
				    'theme' : 'transparent',
				    'share' : {
				      'position' : 'left',
				      'numPreferredServices' : 5
				    },
				  });
				</script>
			<!-- AddThis Smart Layers END -->
			</div>
			<div class="mainplayer_image_album">  
				<?php echo $this->itemPhoto($album, 'thumb.profile')?>
				<p style="width: 180px; padding: 10px; font-size: 9pt;" class="mp3_album_info" >
				 <?php echo $album->description; ?>
				</p>
			</div>
     <!-- HTML5 -->
			<div class="mp3music_container younet_html5_player init">
				<div class="yn-music">
					<div class="song-info">
						<MARQUEE SCROLLDELAY="300">
								<span id="song-title-head"><?php echo $this->songs[0]['title'];?></span>
						</MARQUEE>
						<div id="mp3music_rating">
							<a id = "rate_1" class = "mp3music_unrate">1</a>
							<a id = "rate_2" class = "mp3music_unrate">2</a>
							<a id = "rate_3" class = "mp3music_unrate">3</a>
							<a id = "rate_4" class = "mp3music_unrate">4</a>
							<a id = "rate_5" class = "mp3music_unrate">5</a>
						</div>
					</div>
					<audio class="yn-audio-skin" class="mejs" width="100%" src="<?php echo $this->songs[0]['filepath'];?>" type="audio/mp3" controls autoplay preload = "none"></audio>
				</div>
				<!-- Playlist -->
				<ul class="song-list mejs-list scroll-pane" id = "test_safari">
					<?php foreach ($this->songs as $index => $arSong):?>
						<li class="<?php echo $index == 0 ? 'current': '';?>">
							<span class="song_id" style="display: none;"><?php echo $arSong['song_id'];?></span>
							<span class="song_vote" style="display: none;"><?php echo $arSong['vote'];?></span>
							<span class = "isvote" style = "display: none;"><?php echo $arSong['isvote'];?></span>
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
								<span class="song-title"><?php echo ++$index.'. '.$this->string()->truncate($arSong['title'],47);?></span>
								<span class="yn-play"><?php echo "(".$this->translate(array('%s play','%s plays',$arSong['play_count']),$arSong['play_count']).")";?></span>
							</div>
						</li>
					<?php endforeach;?>
				</ul>
				<!-- End Playlist -->
			</div>
			<!-- END -->
	</div>
</ul>          
</div>
<?php 
	echo $this->action("list", "comment", "core", array("type"=>($this->type == 'album')?"mp3music_album":"mp3music_playlist", "id"=>$album_id));
?>
<?php endif; ?> 