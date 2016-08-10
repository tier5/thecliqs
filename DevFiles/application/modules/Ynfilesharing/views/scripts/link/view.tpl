<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>


<div class="global_form_popup">
	<h3>Link:</h3>
	<div><input type="text" onclick="select()" name="ynfilesharing_link" class="ynfs_share_link_textbox"
		value="<?php echo $this->shareLink ?>" />
	</div>
	<div class="ynfs_share_link_note"><i><?php echo $this->translate("Copy to clipboard: Ctrl+C")?></i></div>
</div>