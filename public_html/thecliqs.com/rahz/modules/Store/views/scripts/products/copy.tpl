<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl  17.09.11 11:55 TeaJay $
 * @author     Taalay
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/page.js');
?>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>


<?php if (!$this->gatewaysEnabled) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no ' .
              'enabled payment gateways. You must %1$sadd one%2$s before this ' .
              'page is available.', '<a href="' .
              $this->escape($this->url(array('action' => 'gateway', 'page_id' => $this->subject()->getIdentity()), 'store_settings', true)) .
              '">', '</a>');
      ?>
    </span>
  </div>
<?php endif; ?>

<?php if (!$this->hasShippingLocations) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Store needs a default list of supported shipping locations for your tangible products, ' .
              'customers from not-supported locations are not able to make a purchase. You must %1$sadd one%2$s before this ' .
              'page is available.', '<a href="' .
              $this->escape($this->url(array('controller' => 'locations', 'action' => 'add', 'page_id' => $this->subject()->getIdentity()), 'store_settings', true)) .
              '" class="smoothbox">', '</a>');
      ?>
    </span>
  </div>
<?php endif; ?>

<?php
  if ($this->error) {
    return ;
  }
?>

<script type="text/javascript">
  var switchType = function()
 	{
     if ( $('price_type').value == 'simple' ) {
       $('list_price-wrapper').style.display='none';
       $('discount_expiry_date-wrapper').style.display='none';
     } else {
       $('list_price-wrapper').style.display='block';
       $('discount_expiry_date-wrapper').style.display='block';
     }
 	}

  var switchAmount = function()
  {
    if ($('type').value == 'digital') {
      $('additional_params-wrapper').style.display='none';
      $('quantity-wrapper').style.display='none';
    } else {
      $('additional_params-wrapper').style.display='block';
      $('quantity-wrapper').style.display='block';
    }
  }

  en4.core.runonce.add(function()
  {
    cal_discount_expiry_date.calendars[0].start = new Date();
    cal_discount_expiry_date.navigate(cal_discount_expiry_date.calendars[0], 'm', 1);
    cal_discount_expiry_date.navigate(cal_discount_expiry_date.calendars[0], 'm', -1);

    switchAmount();
    switchType();

    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
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

  var resetValue = function() {
    $('discount_expiry_date-date').value = '';
    $('discount_expiry_date-hour').value = '';
    $('discount_expiry_date-minute').value = '';
    $('discount_expiry_date-ampm').value = '';
    $('calendar_output_span_discount_expiry_date-date').innerHTML = '<?php echo $this->translate('STORE_Select a date')?>';
  }
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>
<?php echo $this->form->render($this); ?>