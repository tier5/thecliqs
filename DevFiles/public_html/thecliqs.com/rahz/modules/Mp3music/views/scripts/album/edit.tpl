<?php
$songs = $this->album->getSongs();
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Mp3 Music');?>
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
          <?php echo strlen($song->getTitle())>30?substr($song->getTitle(),0,30).'...':$song->getTitle() ?>
        </span>
        (<a href="<?php echo 
                    $this->url(array('album_id'=>$song->album_id,'song_id'=>$song->song_id), 'mp3music_edit_song') ?>" ><?php echo $this->translate('Edit') ?> </a> )
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<script type="text/javascript">
//<![CDATA[
  en4.core.runonce.add(function(){

    // IMPORT SONGS INTO FORM
    if ($$('#music_songlist li.file').length) 
    {
      $$('#music_songlist li.file').inject($('files'));
      $$('#files li span.file-name').setStyle('cursor', 'move');
      $('files').show()
    }
    // SORTABLE album
    new Sortables('files', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'album','action'=>'album-sort'), 'default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'album_id': <?php echo $this->album->album_id ?>,
            'order': this.serialize().toString()
          }
        }).send();
      }
    });
 
    // REMOVE/DELETE SONG FROM album
    $$('a.song_action_remove').addEvent('click', function(){
    var flag = confirm('<?php echo $this->string()->escapeJavascript($this->translate('Are you sure you want to delete this song?')) ?>');
      if(flag == true)
      {
      var song_id  = $(this).getParent('li').id.split(/_/);
          song_id  = song_id[ song_id.length-1 ];

      
      $(this).getParent('li').destroy();
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'mp3music','controller'=>'album','action'=>'remove-song-album'), 'default') ?>',
        data: {
          'format': 'json',
          'song_id': song_id,
          'album_id': <?php echo $this->album->album_id ?>
        }
      }).send();
      }
      return false;
    });

});
//]]>
</script>
