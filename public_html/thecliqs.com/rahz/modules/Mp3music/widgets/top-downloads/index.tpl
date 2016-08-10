<?php
$this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js')
         ; 
      
?>
 <ul class="global_form_box" style="margin-bottom: 15px; ">
<div class="mp3_song_widgets">
        <?php 
        $index = 0;
           $songs    =  $this->songs;
          foreach ($songs as $song): 
           if($index < $this->limit):
          $index++ ;?>
         <li class="mp3_title_link">
             <div class="mp3_image_album">    
                <span class="fl icon" style="background-image: url('./application/modules/Mp3music/externals/images/music/icon_top.png'); ">
             		<span style="color: #FFF; font-weight: bold ;width: 22px; height:22px;margin-top: 2px; text-align: center; display: block"><?php echo $index;?></span>
             	</span>
              </div>
              <div class="mp3_title_album_right" >
                    <a title="<?php echo $song->getTitle() ?>" rel="balloon_song1_<?php echo $song->song_id; ?>" href="javascript:;" class ='title_thongtin2' onClick="return openPage('<?php echo $this->url(array('album_id'=>$song->album_id,'song_id'=>$song->song_id), 'mp3music_album_song');?>',500,890)">
                    	<?php echo $this->string()->truncate($song->getTitle(), 12);?></a> <br />
                    <?php $album = Engine_Api::_()->getItem('mp3music_album', $song->album_id);  ?>
                    <div class="mp3_album_info_right">
                    <span title="<?php echo $album->getOwner()->getTitle()?>">
                        <?php echo $this->translate('Author: ');?><a rel="balloon_song_author1_<?php echo $album->album_id.$song->song_id; ?>" href="<?php echo $album->getOwner()->getHref()?>" class ='title_thongtin3'>
                        	<?php echo $this->string()->truncate($album->getOwner()->getTitle(), 10);?></a> <br /> 
                    </span>
                        <?php echo $this->translate('Downloads: ');?><?php echo $song->download_count ?>
                    </div> 
                     
                </div> 
              </li>
                <?php endif; endforeach; ?>
              <?php if(count($songs) >= $this->limit): ?>         
        <li class="mp3_link_more">
        <?php echo $this->htmlLink($this->url(array('search'=>'browse_topdownloads'), 'mp3music_browse_topdownloads'),
                     $this->translate('&raquo; View more'),
                    array('class'=>'')); ?>
        </li>        
         <?php endif; ?>   
</div>
</ul>