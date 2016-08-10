<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _statFilterForm.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">
window.addEvent('domready', function(){
	$('period').addEvent('change', function() {
		if (this.value == 'MM') {
			$('chunk').getParent().addClass('hidden');
			$('chunk').value = 'dd';
		} else {
			$('chunk').getParent().removeClass('hidden');
		}
	});
});
</script>
<div class="stat_filter_form"><?php echo $this->filterForm->render($this); ?></div>