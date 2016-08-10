<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Content Type?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this content type from availability for custom lists? You can also choose to disable it from custom lists using the icon in the table.'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->resource_type ?>"/>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>