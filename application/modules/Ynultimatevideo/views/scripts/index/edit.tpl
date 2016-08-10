<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$('category_id').addEvent('change', function () {
			$(this).getParent('form').submit();
		});

		if ($('0_0_1-wrapper')) {
			$('0_0_1-wrapper').setStyle('display', 'none');
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

<?php
    echo $this->form->render();
?>
