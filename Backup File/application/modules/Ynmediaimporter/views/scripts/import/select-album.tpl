<style type="text/css">
    #search-wrapper, #auth_view-wrapper, #auth_comment-wrapper, #auth_tag-wrapper,#message_stage,#ynmediaimporter_json_data {
        display: none;
    }
    #message_stage{
        padding: 5px 0;
    }
</style>
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
</script>
<?php echo $this->form;?>