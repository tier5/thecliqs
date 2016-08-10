<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php
	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
        var isMultiMode = <?php echo($this->isMultiMode? 'true': 'false');?>;
        if(!isMultiMode) {
            $('category-wrapper').hide();
            $('category').value = 1;
            return;
        }
        if ($('category').get('value') == 0)
            $('0_0_1-wrapper').hide();

        $('category').addEvent('change', function(){
            var set = <?php echo ($this->setInfoJSON) ? $this->setInfoJSON : $this->setInfoJSON; ?>;
            if ($('category').get('value') == 0)
                $('0_0_1-wrapper').hide();
            else
                $('0_0_1-wrapper').show();
            var defaultOption = new Element('option');
            defaultOption.value = 1;
            defaultOption.innerHTML = 'Default';
            $('fields-0_0_1').empty().grab(defaultOption);
            var o = this;
            (new Hash(set[this.value]['items'])).each(function(item, i){
                var option = new Element('option', {'label':item['caption'], 'value':i});
                option.appendText(item['caption']);
                $('fields-0_0_1').grab(option);
            });
        });

    new Autocompleter.Request.JSON('extra-tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
?>

<?php echo $this->render('_page_options_menu.tpl'); ?>
<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <div class="page_basic_info_edit">
    <?php echo $this->form->render($this); ?>
  </div>
</div>
