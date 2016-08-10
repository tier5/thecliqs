<?php
?>
<div class='global_form_popup'>
    <?php if (isset($this->success)): ?>
      <div class="global_form_popup_message">
      <?php if ($this->success): ?>

       <p><?php echo $this->message ?></p>
       <br />
       
       <button onclick="parent.window.location.href='<?php echo $this->url(array(''), 'mp3music_admin_music_setting') ?>'">
         &laquo; <?php echo $this->translate('Return to page')  ?>&raquo;
       </button>
      <?php elseif (!empty($this->error)): ?>
        <pre style="text-align:left"><?php echo $this->error ?></pre>
      <?php else: ?>
        <p><?php echo $this->translate('There was an error processing your request.  Please try again later.') ?></p>
      <?php endif; ?>
      </div>
    <?php return; endif; ?>

    <?php echo $this->form->render($this) ?>
</div>
