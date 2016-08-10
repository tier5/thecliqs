<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplate
 * @copyright  Copyright 2010-2011 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     Vadim
 */
?>

<?php if ( $this->navigation->count()>=10 ) { ?>
<script type="text/javascript">
window.addEvent('domready', function() {
	$('netlogtemplate_navigation_more').addEvent('click', function(){
		$('netlogtemplate_navigation_dropdown').toggle();
	});
});
</script>
<?php } ?>

<?php if ( $this->navigation->count()<10 ) { ?>
	<?php echo $this->navigation()
		->menu()
		->setContainer($this->navigation)
		->setPartial(null)
		->setUlClass('navigation')
		->render();
	?>
<?php } else { ?>
<ul class="navigation">
<?php
	$counter = 1;
	foreach ( $this->navigation as $navig ) {
		if ( $counter==10 ) {
			print '<li><a href="javascript://" id="netlogtemplate_navigation_more">More &rarr;</a></li></ul><ul id="netlogtemplate_navigation_dropdown" class="navigation_dropdown">';
		}
		print '<li>' . $this->htmlLink($navig->getHref(), $navig->getLabel()) . '</li>';
		$counter++;
	}
?>
</ul>
<?php } ?>