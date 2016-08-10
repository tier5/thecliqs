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
<div class='global_form'>
  <?php   $user = Engine_Api::_()->user()->getViewer();
           $max_playlists =  Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_playlist', $user, 'max_playlists');
          if($max_playlists == "")
            {
             $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'mp3music_playlist'")
                ->where("level_id = ?",$user->level_id)
                ->where("name = 'max_playlists'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $max_playlists = $mallow_a['value'];
              else
                 $max_playlists = 10;
            }
         $cout_playlist = Mp3music_Model_Playlist::getCountPlaylists($user);
        if($cout_playlist < $max_playlists):
             echo $this->form->render($this);
         else: ?>
           <div style="color: red; padding-left: 300px;">
                <?php echo $this->translate("Sorry! Maximum numbers of allowed playlist : "); echo $max_playlists; echo " playlists" ; ?> 
           </div> 
        <?php endif; ?>
</div>
<script type="text/javascript">
var playlist_id = <?php echo $this->playlist_id ?>;
function updateTextFields() {
  if ($('playlist_id').selectedIndex > 0) {
    $('title-wrapper').hide();
    $('description-wrapper').hide();
    $('search-wrapper').hide();
  } else {
    $('title-wrapper').show();
    $('description-wrapper').show();
    $('search-wrapper').show();
  }
}
// populate field if playlist_id is specified
if (playlist_id > 0) {
  $$('#playlist_id option').each(function(el, index) {
    if (el.value == playlist_id)
      $('playlist_id').selectedIndex = index;
  });
  updateTextFields();
}
</script>