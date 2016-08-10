<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Delete Quiz?"); ?></h3>
    <p>
      <?php echo $this->translate("ADMIN_QUIZ_DELETE_CONFIRM"); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->quiz_id?>"/>
      <button type='submit'><?php echo $this->translate("Delete"); ?></button>
      <?php echo $this->translate(" or "); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Cancel"); ?></a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>