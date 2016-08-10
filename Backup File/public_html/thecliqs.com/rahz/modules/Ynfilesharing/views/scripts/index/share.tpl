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
	<?php echo $this -> translate("Link:"); ?>
	<input type="text" onclick="select()" name="ynfilesharing_link" class="ynfs_share_link_textbox"
		value="<?php echo $this->base_url . 
					$this->url(array(
						'object_type' => $this->object_type,
						'object_id' => $this->object_id,
						'code' => $this->code
					), 
					'ynfilesharing_share_view', 
					true) ?>" />
	<div class="ynfs_share_link_note"><i><?php echo $this->translate("Copy to clipboard: Ctrl+C")?></i></div>
</div>

