<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */
 
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
    	var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
    	var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {
            'postVar' : 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'customChoices' : true,
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