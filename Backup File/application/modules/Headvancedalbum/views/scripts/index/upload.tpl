<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: add.tpl 2013-01-21 15:31 ratbek $
 * @author     Ratbek
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate("HEADVANCEDALBUM_Add New Photos"); ?>
  </h2>
  <div class="tabs">
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    <!--<div class="offer_navigation_loader hidden" id="offer_navigation_loader">
      <?php /*echo $this->htmlImage($this->baseUrl().'/application/modules/Offers/externals/images/loader.gif'); */?>
    </div>-->
  </div>
</div>


<script type="text/javascript">
  var updateTextFields = function()
  {
    var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper',
                            '#auth_view-wrapper',  '#auth_comment-wrapper', '#auth_tag-wrapper'];
        fieldToggleGroup = $$(fieldToggleGroup.join(','))
    if ($('album').get('value') == 0) {
      fieldToggleGroup.show();
    } else {
      fieldToggleGroup.hide();
    }
  }
  en4.core.runonce.add(updateTextFields);

  en4.core.runonce.add(function (){

    var $elms = $$('.he_advanced_form.quick #album-wrapper,' +
      '.he_advanced_form.quick #search-wrapper,' +
      '.he_advanced_form.quick #auth_view-wrapper, ' +
      '.he_advanced_form.quick #auth_comment-wrapper,' +
      '.he_advanced_form.quick #auth_tag-wrapper');

    $elms.fade('out');

    $('headv_quick').addEvent('click', function (){
      $elms.fade('out');
      setTimeout(function (){
        $$('.he_advanced_form').addClass('quick');
      }, 500);

    });
    $('headv_advanced').addEvent('click', function (){
      $$('.he_advanced_form').removeClass('quick');
      $elms.fade('in');
    });
  });

</script>

<div class="he_advanced_form quick">
  <?php echo $this->form->render($this) ?>
</div>