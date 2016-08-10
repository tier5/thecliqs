<?php
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headScript()
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js')	
	->appendScript('jQuery.noConflict();')
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/vendor/jquery.ui.widget.js')	
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.iframe-transport.js')
	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.fileupload.js')	
	->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');	
?>
<?php
$playlist = $this -> playlist;
$songs  = $playlist -> getSongs();
?>
<input name="order" type="hidden" id="songs-order" value=""/>
<input name="deleted" type="hidden" id="songs-deleted" value=""/>
<div id="playlist-song-items">
<?php foreach($songs as $song) :?>
	<div id="<?php echo $song -> getIdentity();?>" class="song-item">
		<?php if (!$song->isViewable()) :?>
		<div class="disabled"></div>
		<?php endif;?>
		<span class="play-btn-<?php echo $song->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $song->getGuid()?>" class="fa fa-play"></i></a></span>
		<span><?php echo $song;?></span>
		<div class="song-btn">
			<span class="music-song-move playlist-song-move"><i class="fa fa-arrows"></i></span>
			<span class="music-song-remove playlist-song-remove"><i class="fa fa-times"></i></span>
		</div>
	</div>
<?php endforeach;?>
</div>


<script type="text/javascript">

    en4.core.runonce.add(function(){
        new Sortables('playlist-song-items', {
            contrain: false,
            clone: true,
            handle: 'span.playlist-song-move',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                var order = this.serialize().toString();
                $('songs-order').set('value', order);
            }
        });
    });
    
    window.addEvent('domready', function() {
    	$$('.playlist-song-remove').addEvent('click', function() {
    		var parent = this.getParent('.song-item');
    		var id = parent.get('id');
    		var ids = $('songs-deleted').get('value');
    		if (ids == '') ids = id;
    		else ids = ids+','+id;	
    		$('songs-deleted').set('value', ids);
    		parent.destroy();
    		
    		if ($$('#edit_songs-wrapper .song-item').length == 0) {
    			$('edit_songs_header-wrapper').hide();
    			$('edit_songs-wrapper').hide();
    		}
    	});
    });
</script>

