<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Moderation
 * @license    http://www.socialengine.net/license/
 * @author     Younetco
 */
?>

  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Item?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete this content? It will not be recoverable after being deleted.") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="hf_id" value="<?php echo $this->id?>"/>
        <input type="hidden" name="hf_type" value="<?php echo $this->type?>"/>
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
