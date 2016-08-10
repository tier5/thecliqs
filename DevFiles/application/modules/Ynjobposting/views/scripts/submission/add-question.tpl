<style>
.hidden {
	display: none;
}
</style>
<script type="text/javascript">
function showOptions(elm){
	if (elm == null)
	{
		elm = $("type");
	}
	type = elm.value;
	if (type == 'checkbox' || type == 'radio')
	{
		$("options-wrapper").removeClass("hidden");
	}
	else
	{
		$("options-wrapper").addClass("hidden");
	}
}

function deleteOption(elm)
{
	
}
</script>

<div class='global_form' style="min-height: 200px;">
  <?php echo $this->form->render($this) ?>
  <a href="javascript: void(0);" onclick="return addAnotherOption();" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>
  <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {
    	showOptions(null);
      var maxOptions = 99;
      var options = <?php echo Zend_Json::encode($this->options) ?>;
      var optionParent = $('options').getParent();
		
      var addAnotherOption = window.addAnotherOption = function (dontFocus, label) {
        if (maxOptions && $$('input.pollOptionInput').length >= maxOptions) {
          return !alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s options are permitted.")) ?>').replace(/%s/, maxOptions));
          return false;
        }

        var optionElement = new Element('input', {
          'type': 'text',
          'name': 'optionsArray[]',
          'class': 'pollOptionInput',
          'value': label,
          'events': {
            'keydown': function(event) {
              if (event.key == 'enter') {
                if (this.get('value').trim().length > 0) {
                  addAnotherOption();
                  return false;
                } else
                  return true;
              } else
                return true;
            } // end keypress event
          } // end events
        });
        
        if( dontFocus ) {
          optionElement.inject(optionParent);
        } else {
          optionElement.inject(optionParent).focus();
        }

        $('addOptionLink').inject(optionParent);

        if( maxOptions && $$('input.pollOptionInput').length >= maxOptions ) {
          $('addOptionLink').destroy();
        }
      }
      
      // Do stuff
      if( $type(options) == 'array' && options.length > 0 ) {
        options.each(function(label) {
          addAnotherOption(true, label);
        });
        if( options.length == 1 ) {
          addAnotherOption(true);
        }
      } else {
        // display two boxes to start with
        addAnotherOption(true);
        addAnotherOption(true);
      }
    });
    // -->
  </script>
</div>