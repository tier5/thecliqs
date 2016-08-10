<?php
$this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js')
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/m2bmusic_tabcontent.js'); 
	  
?>
<ul class="global_form_box" style="margin-bottom: 15px;">
<div class = 'avd_music'>  
        <?php
        $model = new Mp3music_Model_Album(array());
        $songs    =  $model->getReSongs($this->owner_id,$this->limit);
          foreach ($songs as $song):?>
          <li class="mp3_title_link" title="<?php echo $song->getTitle(); ?>">  
                <a  href="javascript:;" class ='title_thongtin2' 
                onClick="return openPage('<?php echo $this->url(array('album_id'=>$song->album_id,'song_id'=>$song->song_id), 'mp3music_album_song');?>',500,865)"><?php echo $this->string()->truncate($song->getTitle(),22);?></a> 
        </li> 
        <?php endforeach; ?> 
 </ul>