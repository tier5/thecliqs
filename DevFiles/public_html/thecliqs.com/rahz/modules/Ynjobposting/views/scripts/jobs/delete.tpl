<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else: ?>
<form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Job") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure you want to delete this Job? All of the information about this job will be erased.") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->job_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
<?php endif; ?>