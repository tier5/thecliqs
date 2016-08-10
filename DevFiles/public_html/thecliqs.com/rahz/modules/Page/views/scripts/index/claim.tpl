<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: claim.tpl  19.12.11 18:36 TeaJay $
 * @author     Taalay
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
    var pageAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('action' => 'suggest'), 'page_claim', true) ?>', {
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

    pageAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      $('page_id').value = selected.retrieve('autocompleteChoice').id;
    });
  });
</script>

<?php echo $this->render('_pageMainNavigation.tpl'); ?>

<?php echo $this->form->render($this); ?>