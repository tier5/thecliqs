  <div class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Category?") ?></h3>
      <p class="description">
        <?php echo $this->translate("Are you sure that you want to delete this category? It will not be recoverable after being deleted.") ?>
      </p>
      <?php if($this->usedCount > 0): ?>
      	<?php 
      	echo $this->form->render($this);?>
      <br />
      <?php else: ?>
      
        <form method="post">
        <p>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo " ".$this->translate("or")." " ?> 
        <a onclick="parent.Smoothbox.close();" href='<?php echo $this->url(array('action' => 'index')) ?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
      </form>
      <?php endif;?>
    </div>
  </div>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
