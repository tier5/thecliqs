<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');  	
	  
?>
<?php if($this->params['search'] == 'browse_new_albums'): ?>
    <?php echo $this->partial('music_browse_new_albums.tpl','mp3music',array('browse'=>$this)) ?>     
<?php endif; ?> 
<?php if($this->params['search'] == 'album'): ?>
    <?php echo $this->partial('music_browse_albums.tpl','mp3music',array('browse'=>$this)) ?>     
<?php endif; ?>               