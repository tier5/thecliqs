<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     Jung
 */
?>
<h2><?php echo $this->translate("Forums Plugin") ?></h2>
<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/forum/level/index/id/'+level_id;
    //alert(level_id);
  }
</script>

<div class='clear'>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>