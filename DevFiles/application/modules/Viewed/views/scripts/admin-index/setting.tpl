<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>
<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    window.location.href = en4.core.baseUrl + 'admin/viewed/index/setting/level_id/' + level_id;
    //alert(level_id);
  }
</script>


<h2>
  <?php echo $this->translate("Who Viewed Me") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>

<script>

</script>

