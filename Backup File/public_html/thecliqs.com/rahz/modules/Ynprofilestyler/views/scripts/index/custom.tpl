<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<div id="uiNavbar">
	<ul >
		<?php foreach($this->groups as $group):	?>
		<li>
			<a href="javascript: ynps2.update('#content','index/custom-<?php echo $group->group_name ?>',{rulegroup_id : <?php echo $group->rulegroup_id?>})">
				<?php echo $this->translate($group->title)?>
			</a>
		</li>
		<?php endforeach;?>
	</ul>
</div>
<div class="clear"></div>
<div id="content">

</div>