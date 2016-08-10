<?php
  $this->headScript()
       ->appendFile($this->baseUrl() . '/externals/smoothbox/smoothbox.js')
       ->appendFile($this->baseUrl() . '/externals/smoothbox/smoothbox4.js');  
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
			 <input type="text" style="width: 80%; height:20px;border:1px solid #d2d8db; margin:1px" readonly="readonly" onclick="url_select_text(this)" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array(),'mp3music_album');?>/<?php echo $this->song->album_id; ?>/song_id/<?php echo $this->song->song_id; ?>"/>
			 </div>
			 
			 <div style="padding-bottom: 10px;">  
			 <span style="padding-left: 20px; padding-right: 14px"><?php echo $this->translate("HTML Code"); ?>:</span>
			 <input id="result_url" type="text" style="width: 80%; height:20px;border:1px solid #d2d8db; margin:1px" readonly="readonly" onclick="url_select_text(this)" value="<?php echo htmlspecialchars($html_code_for_blog)?>"/>
			 </div>
			 
			<div style="padding-bottom: 10px;">  
			 <span style="padding-left: 20px; padding-right: 9px"><?php echo $this->translate("Forum Code"); ?>:</span>
			 <input type="text" style="width: 80%; height:20px;border:1px solid #d2d8db; margin:1px; " readonly="readonly" onclick="url_select_text(this)" value="[URL=<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array(),'mp3music_album');?>/<?php echo $this->song->album_id; ?>/song_id/<?php echo $this->song->song_id; ?>]<?php echo $this->song->title; ?>[/URL]"/>
			 </div>
 </ul>