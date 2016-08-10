<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: upload.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>

<script type="text/javascript">
  var updateTextFields = function()
  {
    var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper',
                            '#auth_view-wrapper',  '#auth_comment-wrapper', '#auth_tag-wrapper', '#auth_add_photo-wrapper'];
        fieldToggleGroup = $$(fieldToggleGroup.join(','))
    if ($('album').get('value') == 0) {
      fieldToggleGroup.show();
    } else {
      fieldToggleGroup.hide();
    }
  }
  en4.core.runonce.add(updateTextFields);
  var disable = function()
  {
      document.getElementById("submit-wrapper").style.display = "none";
  }
</script>
<?php echo $this->form->render($this) ?>
  <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {
      var addMoreFile = window.addMoreFile = function () 
      {
        var fileElement = new Element('input', {
          'type': 'file',
          'name': 'photos[]',
          'multiple': "multiple"
        });
        fileElement.inject($('photos-element'));
      }
    });
    // -->
  </script>