<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  11.01.12 17:58 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js')
  ;
?>

<h2>
  <?php echo $this->translate('Credits Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMINGIVECREDITS_INDEX_DESCRIPTION") ?>
</p>
<br />

<?php if ($this->ok) : ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      var values = <?php echo Zend_Json::encode($this->values); ?>;
      values.count = <?php echo $this->count;?>;
      values.total = <?php echo $this->total;?>;
      sendCredits(values);
    });

    var sendCredits = function($values) {
      $values.format = 'json';
      new Request.JSON({
        url : '<?php echo $this->url(array('module' => 'credit', 'controller' => 'give-credits', 'action' => 'send'), 'admin_default', true)?>',
        data : $values,
        onComplete:function(response)
        {
          $('sending_credits').innerHTML = response.html;
          if (!response.limit) {
            $values.count = response.count;
            $values.page = response.page;
            sendCredits($values);
          } else {
            setTimeout(
              function() {
                window.location = window.location.href;
              }, 3000
            );
          }
        }
      }).send();
    }
  </script>

  <ul id="sending_credits" class="form-notices">
    <li>
      <?php echo $this->translate('Please wait, the credits are still sending, do not close this page! There are still <span style="color: red">%s</span> users who have not received credits.', $this->count);?>
      <i class="icon_loading"></i>
    </li>
  </ul>

<?php else :?>

<div id="send_credits_form" class="settings">
  <?php echo $this->form->render($this)?>
</div>

<script type="text/javascript">
	var switchType = function(value)
	{
    if (value == '' || value == 'all_users') {
      disableAllTypes();
    } else {
      disableAllTypes();
      $(value + '-wrapper').style.display = 'block';
    }
	}

  var showOrNot = function(value)
  {
    if (value-1 > 0) {
      $('subject-wrapper').style.display = 'block';
      $('message-wrapper').style.display = 'block';
    } else {
      $('subject-wrapper').style.display = 'none';
      $('message-wrapper').style.display = 'none';
    }
  }

  var disableAllTypes = function() {
    $('levels-wrapper').style.display = 'none';
    $('networks-wrapper').style.display = 'none';
    $('spec_users-wrapper').style.display = 'none';
    $('levels').value = '';
    $('networks').value = '';
    $('spec_users').value = '';
  }

  en4.core.runonce.add(function() {
    $('users').value = '';
		switchType('');
		showOrNot(0);

    var url = '<?php echo $this->url(array('module' => 'credit', 'controller' => 'give-credits', 'action' => 'suggest'), 'admin_default', true) ?>';
    var creditAutocomplete = new Autocompleter.Request.JSON('spec_users', url, {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    creditAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      $('user_ids').value = $('user_ids').value + ', ' + selected.retrieve('autocompleteChoice').id;
    });
  });
</script>

<?php endif; ?>