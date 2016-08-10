<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<?php if (!empty($this->url)) : ?>
	<script language="javascript" type="text/javascript">
		window.opener.ynps2.activeTab('#content-slideshow','index/slideshow', {}, -1, false, true)
		window.close();
	</script>
<?php endif; ?>
<?php echo $this->form->render($this) ?>