<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  09.01.12 14:17 TeaJay $
 * @author     Taalay
 */
?>
<?php
	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl.'application/modules/Credit/externals/scripts/core.js')
  ;
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var creditAutocomplete = new Autocompleter.Request.JSON('username', '<?php echo $this->url(array('action' => 'suggest'), 'credit_general', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    creditAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      $('user_id').value = selected.retrieve('autocompleteChoice').id;
    });
    credit_manager.action_url = '<?php echo $this->url(array('action' => 'send'), 'credit_general', true) ?>';
    if (!credit_manager.started) {
      credit_manager.init();
    }
  });
</script>
<div id="credit_loader" class="hidden"></div>
<?php echo $this->form->render($this) ?>