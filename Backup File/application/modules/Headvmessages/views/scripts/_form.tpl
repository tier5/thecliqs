<div class="headvmessages-compose-form">
  <form>
    <div class="input">
      <input type="text" id="send_to" name="to" placeholder="<?php echo $this->translate('HEADVMESSAGES_Send To'); ?>">
      <input type="hidden" id="toValues" name="toValues">
      <div id="toValues-wrapper">
        <div id="toValues-element">
        </div>
      </div>
    </div>
    <div class="input">
      <input type="text" id="headvmessages-subject" name="title" placeholder="<?php echo $this->translate('HEADVMESSAGES_Subject'); ?>">
    </div>
    <?php echo $this->render('_composer.tpl'); ?>
  </form>
</div>