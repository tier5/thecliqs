<h2>
  <?php echo $this->translate('Advanced Feed Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    /*---- Render the menu ----*/
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div>
<a href="<?php echo $this->url(array('action' =>'index', 'module' => 'ynfeed', 'controller' => 'welcome'), 'admin_default', true) ?>" class="buttonlink ynfeed_icon_back" title="<?php echo $this->translate('Back to Manage Contents');?>"><?php echo $this->translate('Back to Manage Contents');?></a>
</div>
<br />
<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>