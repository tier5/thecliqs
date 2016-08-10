<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');	
	  
?>
<?php echo $this->partial('music_browse_playlists.tpl','mp3music',array('browse'=>$this)) ?>     