<script type="text/javascript">
  var $optionText = '<?php echo $this->translate('STORE_Add Option'); ?>';
  var storeProduct = {
    'init': function(){
      var self = this;
      self.paramBlock = $('additional-params');
    },
    'addParams': function(){
      var self = this;
      var $div = self.paramBlock.getElement('div');
      var $clone = $div.clone();
      var $delete = new Element('div', {
        'class': 'param-delete',
        'onClick': 'storeProduct.deleteParam($(this));',
        'text': 'X'
      });

      $clone.grab($delete);
      self.paramBlock.grab($clone);
    },
    'deleteParam': function($el){
      $el.getParent('div').dispose();
    }
  };

  en4.core.runonce.add(function(){
    storeProduct.init();
  });
</script>

<?php
  $values = $this->element->getValue();
  $i = 0;

  if (count($values) == 0){
    $values[] = '';
  }
?>

<p>
  <div id='additional-params'>
  <?php foreach($values as $key=>$value): ?>
    <div class="param-block">
      <div class='title-block'>
        <span><?php echo $this->translate('Label'); ?></span><br/>
        <input type='text' name='additional_params[]' class="param-title" value="<?php echo (isset($value['label']))?$value['label']:''; ?>"/>
      </div>
      <div>
        <span><?php echo $this->translate('STORE_Options. Comma separated.'); ?></span><br/>
        <input type='text' name='additional_params[options][]' class="param-options" value="<?php echo (isset($value['options']))?$value['options']:''; ?>"/>
      </div>
      <?php if ($i > 0 ): ?>
        <div class="param-delete" onclick="storeProduct.deleteParam($(this))">X</div>
      <?php endif; ?>
    </div>
    <?php $i++; endforeach; ?>
  </div>
  <a href="javascript:storeProduct.addParams()" class="buttonlink icon_add_params"><?php echo $this->translate('add more'); ?></a>
</p>