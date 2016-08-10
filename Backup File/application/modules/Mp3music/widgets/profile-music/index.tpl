<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   ?>
<ul class="music_browse">
  <?php foreach ($this->paginator as $album): ?>
  <li>
    <div class='music_browse_info'>
      <div class="music_browse_info_title">
	  <h3><a href="javascript:;" class ='title_thongtin2' onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',800,565)"><?php echo $album->getTitle();?></a></h3> 
      </div>
      <div class='music_browse_info_date'>
        <?php echo $this->translate('Posted %s', $this->timestamp($album->creation_date)); ?>
      </div>
      <div class='music_browse_info_desc'>
        <?php echo $album->description ?>
      </div>
    </div>
    <?php $songs = Engine_Api::_() -> mp3music() -> getservicesongs($album);
    echo $this->partial('application/modules/Mp3music/views/scripts/_Player.tpl', array('album'=>$album, 'songs' => $songs)) ?>
  </li>
  <?php endforeach; ?>
</ul>
