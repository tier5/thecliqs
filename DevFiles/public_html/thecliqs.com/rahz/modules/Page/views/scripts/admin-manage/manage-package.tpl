<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage-package.tpl 2012-06-1 12:53 idris $
 * @author     Idris
 */
?>

<div class="admin_manage_package">
  <h3><?php echo $this->page_title?></h3>
  <div class = "label"><?php echo $this->translate('Package Name')?>:</div>
  <div class = "text"><?php echo $this->all_packages[$this->package_id][0];?></div>

  <div class = "label"><?php echo $this->translate('Package Description')?>:</div>
  <div class = "text"><?php echo $this->all_packages[$this->package_id][1];?></div>

  <div class = "label"><?php echo $this->translate('Available Modules')?>:</div>
  <div class = "text"><?php echo $this->all_packages[$this->package_id][2];?></div>

  <?php echo $this->form->render($this); ?>
</div>


<script type="text/javascript">
  window.addEvent('domready', function(){

    packages = <?php echo $this->packages;?>;

    $('package_id').addEvent('change', function(){
      $$('.text')[0].innerHTML = packages[this.value][0];
      $$('.text')[1].innerHTML = packages[this.value][1];
      $$('.text')[2].innerHTML = packages[this.value][2];
    });


    myFx = new Fx.Slide($('expiration-wrapper')).hide();

    $('is_expired_day').addEvent('click', function(){
      myFx.toggle();
    });
  });
</script>