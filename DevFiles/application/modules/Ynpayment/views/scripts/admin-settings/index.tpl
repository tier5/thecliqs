
<script type="text/javascript">
  var supportedCurrencyIndex;
  var gateways;
  var displayCurrencyGateways = function() {
    var currency = $('currency').get('value');
    var has = [], hasNot = [];
    console.log(gateways);
    gateways.each(function(title, id) {
      console.log(id, title);
      if( !supportedCurrencyIndex.has(title) ) {
        hasNot.push(title);
      } else if( !supportedCurrencyIndex.get(title).contains(currency) ) {
        hasNot.push(title);
      } else {
        has.push(title);
      }
      var supportString = '';
      if( has.length > 0 ) {
        supportString += '<span class="currency-gateway-supported">'
            + 'Supported Gateways: ' + has + '</span>';
      }
      if( has.length > 0 && hasNot.length > 0 ) {
        supportString += '<br />';
      }
      if( hasNot.length > 0 ) {
        supportString += '<span class="currency-gateway-unsupported">'
            + 'Unsupported Gateways: ' + hasNot + '</span>';
      }
      $$('#currency-element .description')[0].set('html', supportString);
    });

  }
  window.addEvent('load', function() {
    supportedCurrencyIndex = new Hash(<?php echo Zend_Json::encode($this->supportedCurrencyIndex) ?>);
    gateways = new Hash(<?php echo Zend_Json::encode($this->gateways) ?>);
    $('currency').addEvent('change', displayCurrencyGateways);
    displayCurrencyGateways();
  });
</script>
<h2>
  <?php echo $this->translate("Advanced Payment Gateway") ?>
</h2>	
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>
