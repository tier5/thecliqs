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
<div class='global_form_popup'>
    <?php if (isset($this->success)): ?>
      <div class="global_form_popup_message">
      <?php if ($this->success): ?>

       <p><?php echo $this->message ?></p>
       <br />
       <button onclick="parent.window.location.href='<?php echo $this->url(array('album_id' => $this->album_id), 'mp3music_edit_album') ?>'">
         &laquo; <?php echo $this->translate('Return to page')  ?>&raquo;
       </button>
       <button onclick="parent.window.location.href='<?php echo $this->url(array('page' => '1'), 'mp3music_manage_album') ?>'">
         <?php echo $this->translate('Go to my album') ?> &raquo;
       </button>
      <?php elseif (!empty($this->error)): ?>
        <pre style="text-align:left"><?php echo $this->error ?></pre>
      <?php else: ?>
        <p><?php echo $this->translate('There was an error processing your request.  Please try again later.') ?></p>
      <?php endif; ?>
      </div>
    <?php return; endif; ?>

    <?php  echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
var singer_id = <?php echo Engine_Api::_()->getItem('mp3music_album_song', $this->song_id)->singer_id; ?>;   
function updateTextFieldSingers() {
  if ($('music_singer_id').selectedIndex > 0) { 
    $('other_singer-wrapper').hide();
  } else {
    $('other_singer-wrapper').show();
  }
}
// populate field if singer_id is specified
if (singer_id > 0) {
  $$('#music_singer_id option').each(function(el, index2) {
    if (el.value == singer_id)
      $('music_singer_id').selectedIndex = index2;
  });
  updateTextFieldSingers();
}
</script>
