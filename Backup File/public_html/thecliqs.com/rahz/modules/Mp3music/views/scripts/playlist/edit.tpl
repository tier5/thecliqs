<?php
$songs = $this->playlist->getSongs();
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Advanced Music');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php echo $this->form->render($this) ?>
<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->song_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="song_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
        <span class="file-name">
          <?php echo strlen($song->title)>30?substr($song->title,0,30).'...':$song->title ?>
        </span>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<script type="text/javascript">
//<![CDATA[
  en4.core.runonce.add(function(){
    //$('save-wrapper').inject($('art-wrapper'), 'after');
    // IMPORT SONGS INTO FORM
    if ($$('#music_songlist li.file').length) {
      $$('#music_songlist li.file').inject($('demo-list'));
      $$('#demo-list li span.file-name').setStyle('cursor', 'move');
      $('demo-list').show()
    }
    // SORTABLE album
    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'playlist','action'=>'playlist-sort'), 'default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'playlist_id': <?php echo $this->playlist->playlist_id ?>,
            'order': this.serialize().toString()
          }
        }).send();
      }
    });
    //$$('#music_songlist > li > span').setStyle('cursor','move');
 
    // REMOVE/DELETE SONG FROM playlist
    $$('a.song_action_remove').addEvent('click', function(){
    var flag = confirm('<?php echo $this->translate('Are you sure you want to delete this song?') ?>');
      if(flag == true)
      {
      var song_id  = $(this).getParent('li').id.split(/_/);
          song_id  = song_id[ song_id.length-1 ];

      
      $(this).getParent('li').destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'playlist','action'=>'remove-song-playlist'), 'default') ?>',
        data: {
          'format': 'json',
          'song_id': song_id,
          'playlist_id': <?php echo $this->playlist->playlist_id ?>
        }
      }).send();
      }
      return false;
    });

});
//]]>
</script>
