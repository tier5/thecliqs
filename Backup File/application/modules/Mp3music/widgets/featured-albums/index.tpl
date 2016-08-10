<?php
$this->headScript()  
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js')
     ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/slideshow/Navigation.js')
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/slideshow/Loop.js')
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/slideshow/SlideShow.js');
?>
<section id="mp3music_navigation" class="demo">
	<div id="mp3music_navigation-slideshow" class="slideshow">
		<?php
         $i = 0;
         foreach ($this->albums as $album):
         if(count($album->getSongs()) > 0):
         if($i < $this->limit):
         $i ++;
         ?>
		    <span id="lp<?php echo $i?>">
		    	<div class="mp3music_album_photo">
	                <a title="<?php echo $album->title?>" href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
	                 <?php echo $this->itemPhoto($album, ''); ?>  
	                </a>
		            <div class="mp3music_albumfeatured_info">
		            	<div class="mp3music_album_title">
		            		<h3>
		            			<a title="<?php echo strip_tags($album->title);?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
				                <?php echo $this->string()->truncate($album->title, 100);?>
				                </a>
		            		</h3>
		            	</div>
		            	<p class="mp3music_album_info">
		            		<?php echo $this->translate("Posted by %1s on %2s", $album->getOwner(),$this->locale()->toDateTime(strtotime($album->creation_date), array('type' => 'date')));?> - 
		            		<?php echo $this->translate(array("%s play", "%s plays", $album->play_count),$album->play_count)?>
		            	</p>
		            	<p class="mp3music_album_info">
		            		<?php echo $this->string()->truncate($album->description, 160);?>
		            	</p>
		            </div>
		       </div>
		    </span> 
	
    	<?php endif; endif; endforeach; ?> 
		<ul class="mp3music_pagination" id="mp3music_pagination">
			<li><a class="current" href="#lp1"></a></li>
			<?php for ($j = 2; $j <= $i; $j ++):?>
			<li><a href="#lp<?php echo $j?>"></a></li>
			<?php endfor;?>
		</ul>
	</div>
</section>
