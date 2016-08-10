<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php 
	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
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

        var isMultiMode = <?php echo($this->isMultiMode? 'true': 'false');?>;
        if(!isMultiMode) {
            $('category-wrapper').hide();
            $('category').value = 1;
            return;
        }
        $('0_0_1-wrapper').hide();
        isVis = false;
        $('category').addEvent('change', function(){
            var set = <?php echo ($this->setInfoJSON) ? $this->setInfoJSON : 1; ?>;
            if($(this).get('value') == 0) {
                $('0_0_1-wrapper').hide();
                isVis = false;
                return;
            }
            if(!isVis) {
                $('0_0_1-wrapper').show();
                isVis = true;
            }
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
  });
</script>

<?php echo $this->render('_pageMainNavigation.tpl'); ?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array())
?>

<script type="text/javascript">
  he_word_completer.prepend_word = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('page_id' => ''), 'page_view').'/'; ?>";
  he_word_completer.title = "title";
  he_word_completer.url = "url";
  he_word_completer.completer = "page_url_placeholder";
  he_word_completer.default_value = "pagename";
  he_word_completer.ajax_url = "<?php echo $this->url(array('action' => 'validate'), 'page_ajax'); ?>";
  he_word_completer.init();
</script>

<script type="text/javascript">
  en4.core.runonce.add(function (){

    window.profile_type = '';
    window.page_terms =  <?php echo Zend_Json::encode($this->terms); ?>;

    window.pageTermLink = function (option_id)
    {
      var html = '' +
        '<form class="global_form_smoothbox"><div><div><div class="form-elements">'+
        '<div class="form_wrapper scroll_div">'+window.page_terms[option_id].terms+
        '<div class="form_href"><a href="javascript:void(0);" onclick="window.changeChecked(true);Smoothbox.close();" class="agreement"><?php echo $this->translate("Agree"); ?></a> | ' +
        '<a href="javascript:void(0);" onclick="window.changeChecked(false);Smoothbox.close();" class="close"><?php echo addslashes($this->translate("Don't Agree")); ?></a></div></div></div></div></div></form>';

      Smoothbox.open(new Element('div', {'html': html}), {mode: 'Inline', width: 700, height: 500});

    };
    window.changeChecked = function(check)
    {
      window.document.getElementById('agree_checkbox').checked = check;
    }

    $$('select[onchange=changeFields(this)]').addEvent('change', function ()
    {
      // reset
      window.profile_type=this.value;

      if($('page_agree_checkbox')){
        $('page_agree_checkbox').destroy();
      }
      if( window.page_terms[this.value])
      {
        var term = window.page_terms[this.value];

        var checkbox = new Element('div',{'id':'page_agree_checkbox','class': 'form-wrapper'});
        checkbox.innerHTML = '<div class="form-label"></div>' +
          '<div class="agreement-element_checkbox"><input type="checkbox" id="agree_checkbox" value="1">' +
          '<label><?php echo $this->translate("I have read and agree to the "); ?><a href="javascript:void(0);" onclick="window.pageTermLink('+this.value+')"><?php echo $this->translate("terms of use"); ?></label>';
        checkbox.inject($('buttons-wrapper'), 'before');
      }
    });

    $('execute').addEvent('click', function (e){
        if(!window.profile_type){
/*          e.stop();
          he_show_message('<?php echo $this->translate("You should choose profile type!") ?>','error')*/
        }
        else if ($('page_agree_checkbox')){
          if(!window.document.getElementById('agree_checkbox').checked){
            e.stop();
            he_show_message('<?php echo $this->translate("You must agree to the terms of use!") ?>', 'error');
          }
        }
    });

  });

</script>
<?php
if($this->err)
{
  echo '<h3>'.$this->translate('PAGE_MAXIMUM_ALLOWED_TITLE').'</h3>';
  echo '<br>';
  echo ''.$this->translate('PAGE_MAXIMUM_ALLOWED_DESCRIPTION').'';
}
else
  echo $this->form->render($this);
?>


