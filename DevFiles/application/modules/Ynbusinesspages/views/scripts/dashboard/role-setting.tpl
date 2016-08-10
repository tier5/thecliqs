<h3><?php echo $this->translate("Manage Role Settings");?></h3>
<?php echo $this->form->render();?>
<script type="text/javascript">
function selectRole(elm)
{
	role_id = elm.value;
	window.location = '<?php echo $this->url(array('action' => 'role-setting', 'business_id' => $this->businessId), 'ynbusinesspages_dashboard', true); ?>' + '/role_id/' + role_id;
}
</script>