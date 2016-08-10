<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl:  07.07.11 14:27 taalay $
 * @author     Taalay
 */
?>
<?php
  $this->headTranslate(
    array('STORE_max', 'STORE_min')
  );
?>

<script type='text/javascript'>

en4.core.runonce.add(function()
{
  var $separators = $$('.layout_store_product_search').getElements('.browse-separator-wrapper');
	$separators.each(function($separator){
    $separator.setStyle('display', 'none');
  });
	var $price_input = $$('.price_input');
	var min = '<?php echo $this->translate('STORE_min')?>';
	var max = '<?php echo $this->translate('STORE_max')?>';
	$price_input.value = min;

	$price_input.addEvents({
			'focus':function(){
				if ($(this).value.trim() == max || $(this).value.trim() == min)
				{
					$(this).setProperty('value', '');
					$(this).setStyle('color', '#000000');
				}
			},
			'blur':function() {
				if ($(this).value.trim() == '')
				{
						$(this).setStyle('color', '#999999');
						if ($(this).getProperty('id') == 'min_price')
						{
							$(this).setProperty('value', min);
						}
						else
						if ( $(this).getProperty('id') == 'max_price')
						{
							$(this).setProperty('value', max);
						}
				}
			}
		});
  if ($('product_form_info')) {
    product_manager.init();
  }
});

</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => (int) @$this->topLevelId,
    'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

<?php echo $this->filterForm->render($this); ?>