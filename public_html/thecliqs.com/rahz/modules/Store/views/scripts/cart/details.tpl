<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: details.tpl  4/25/12 6:35 PM mt.uulu $
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
          $el.getParent('.form-elements').getElementById('state-element').set('html', $response.html);
        }
      }
    }).send();
  }
</script>

<div class="global_form_popup">
  <?php echo $this->form->render($this); ?>
</div>
