<style>
  #headvmessages-smiles .main_contaner_smiles .smile_left_scroll,
  #headvmessages-smiles .main_contaner_smiles .smile_right_scroll,
  #headvmessages-smiles .main_contaner_smiles .heemoticon_smiles {
    width: 45px;
    height: 42px;
    vertical-align: middle;
    display: inline-block;
    text-align: center;
  }
  #headvmessages-smiles .smiles_NEW {
    width: 70px;
    height: 70px;
  }
  #headvmessages-smiles {
    background: #fff;
    border: 1px solid;
    position: absolute;
    z-index: 999;
  }
</style>
<div class="messages-list-controls-wrapper">
  <div>
    <textarea placeholder="<?php echo $this->translate('HEADVMESSAGES_Message body'); ?>"
              name="body" id="headvmessages-body" cols="1" rows="2"></textarea>

    <textarea name="hidden-body" id="hidden-body" style="display: none;"></textarea>

    <div id="messages-list-controls" class="messages-list-controls">
      <?php foreach ($this->composePartials as $partial): ?>
        <?php echo $this->partial($partial[0], $partial[1]) ?>
      <?php endforeach; ?>
      <div id="headvmessages-smiles" style="display: none;">

      </div>
      <?php /*if($this->allowSmiles):*/ ?><!--
        <a id="compose-smile-activator" class="compose-activator buttonlink hei hei-smile-o"
           onclick="javascript:void(0);"
          style="background: none;"></a>
      --><?php /*endif;*/ ?>
    </div>
    <div id="messages-list-controls-tray">
    </div>
  </div>
  <div class="messages-list-send">
    <a href="javascript://" id="messages-list-send">
      <i class="hei hei-envelope"></i>
    </a>
  </div>
</div>
