<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: address.tpl  4/17/12 3:22 PM mt.uulu $
 * @author     Mirlan
 */
?>
<script type="text/javascript">
  var getLocations = function($el){
    var parent_id = $el.get('value');

    new Request.JSON({
      url: "<?php echo $this->url(array('action'=>'address'), 'store_panel', true); ?>",
      data: {'format':'json', 'just_locations':1, 'parent_id':parent_id},
      onSuccess:function($response){
        if( $response.status ) {
          $('state-element').set('html', $response.html);
        }
      }
    }).send();
  }
</script>

<?php echo $this->content()->renderWidget('store.navigation-tabs'); ?>

<div class="layout_left">
  <div id='panel_options'>
    <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_navIcons.tpl', 'core'))
      ->render()
    ?>
  </div>
</div>

<div class="layout_middle">
  <?php echo $this->form->render($this); ?>
</div>