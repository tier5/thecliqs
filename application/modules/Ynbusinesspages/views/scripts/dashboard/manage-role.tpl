<h3><?php echo $this->translate("Manage Member Roles");?></h3>
<p>
	<?php echo $this->translate("YNBUSINESSPAGES_MANAGE_MEMBER_ROLES_DESCRIPTION");?>
</p>
<br />
<?php if (count($this->roles)):?>
<table class='ynsocial_table frontend_table'>
<tr>
    <th><?php echo $this -> translate('Role Title');?></th>
    <th><?php echo $this -> translate('Options');?></th>
</tr>
<?php foreach ($this->roles as $role):?>
<tr>
    <td><?php echo $this -> translate($role->name);?></td>
    <td>
    <?php if ($role -> can_delete): ?>
    	<a class="smoothbox" href="<?php echo $this->url(array('action' => 'edit-role', 'business_id' => $this->businessId, 'role_id' => $role->getIdentity()), 'ynbusinesspages_dashboard');?>"><?php echo $this->translate("Edit");?></a>
     | 
    	<a class="smoothbox" href="<?php echo $this->url(array('action' => 'delete-role', 'business_id' => $this->businessId, 'role_id' => $role->getIdentity()), 'ynbusinesspages_dashboard');?>"><?php echo $this->translate("Delete");?></a>
    <?php endif; ?>
    </td>
</tr>
<?php endforeach;;?>
</table>
<?php endif;?>
<a class="smoothbox ynbusinesspages-button-type" href="<?php echo $this->url(array('action' => 'add-role', 'business_id' => $this->businessId), 'ynbusinesspages_dashboard');?>"><?php echo $this->translate("Add New Role");?></a>

