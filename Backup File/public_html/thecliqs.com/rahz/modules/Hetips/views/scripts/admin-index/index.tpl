<script type="text/javascript">
  en4.core.runonce.add(function() {
    $$('.tabs .navigation li').removeClass('active');
    $$('.hetips_admin_main_settings_' + '<?php echo $this->type; ?>')[0].getParent().addClass('active');
  });
</script>
<div class="headline">
  <div class="tabs">
    <?php //echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
    <div class="headline">
        <?php echo $this->render('_listMemberTips.tpl'); ?>
    </div>
</div>

<?php echo $this->render('_jsTips.tpl'); ?>


<div class="tips_select">
  <?php echo @$this->formSelect('subjectOption', (($this->option_id > 1)? $this->option_id  : $this->topLevelOptions->option_id), array(), $this->topLevelOptions); ?>
  <?php echo $this->formSelect('subjectTips', array_keys($this->tipsMeta), array(), $this->tipsMeta); ?>
  <?php echo $this->formButton('addTips', 'Add'); ?>
<ul class="tips_list">
  <?php if(count($this->tipsMaps) > 0): ?>
    <?php foreach( $this->tipsMaps as $tip ): ?>
      <?php echo $this->adminTipsMeta($tip); ?>
    <?php endforeach; ?>
  <?php else: ?>
  <div class="tip">
    <span>
        <?php echo $this->translate("In this category are no tips!"); ?>
      </span>
  </div>
  <?php endif; ?>
</ul>
</div>
<div class="settings tips-settings">
  <?php echo $this->form; ?>
</div>