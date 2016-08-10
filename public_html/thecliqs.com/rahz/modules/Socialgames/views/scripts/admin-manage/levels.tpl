<h2><?php echo $this->translate('SocialGames'); ?></h2>

<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    window.location.href = en4.core.baseUrl + 'admin/socialgames/manage/levels/id/' + level_id;
  }
</script>

<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>