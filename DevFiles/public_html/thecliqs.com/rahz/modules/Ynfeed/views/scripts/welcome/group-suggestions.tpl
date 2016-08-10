<ul>
<?php if (count($this->group_suggestions)): ?>
<?php
$isAdvanced = false;
$module = 'group';
if (Engine_Api::_() -> hasModuleBootstrap('advgroup')) {
	$isAdvanced = true;
	$module = 'advgroup';
}
foreach($this -> group_suggestions as $item): if(!$item){continue;}?>
	<li id="yf_group_<?php echo $item->getIdentity()?>" class="ynfeed_user_item">
		<div class="yf_group_photo">
			<a href="<?php echo $item->getHref(); ?>">
				<?php $backgroundURL = $item -> getPhotoUrl("thumb.profile");
				if(!$backgroundURL)
				{
					$backgroundURL = $this->baseUrl().'/application/modules/Group/externals/images/nophoto_group_thumb_profile.png';
				}?>
			    <span class="image-thumb" style="background-image:url('<?php echo  $backgroundURL?>')" ></span>
			</a>
		</div>
		<div class="yf_group_info ynfeed-clearfix">
			<div class="yf_group_title">
				<?php echo $this->translate($this->htmlLink($item->getHref(), $item-> getTitle())); ?>
			</div>
			<div class="yf_group_options">
				<?php
				$param = array();
				if($isAdvanced)
				{
					if ($item -> is_subgroup)
					{
						$parent_group = $item -> getParentGroup();
						if ($parent_group -> membership() -> isResourceApprovalRequired())
						{
							$param = array(
								'label' => 'Request Membership',
								'action' => 'request-group',
							);
						}
						elseif ($item -> membership() -> isResourceApprovalRequired())
						{
							$param = array(
								'label' => 'Request Membership',
								'action' => 'request-group',
							);
						}
						else
						{
							$param = array(
								'label' => 'Join Group',
								'action' => 'join-group',
							);
						}
					}
					else
					{
						if ($item -> membership() -> isResourceApprovalRequired())
						{
							$param = array(
								'label' => 'Request Membership',
								'action' => 'request-group',
								);
						}
						else
						{
							$param = array(
								'label' => 'Join Group',
								'action' => 'join-group',
								);
						}
					}
				}
				else {
					if ($item -> membership() -> isResourceApprovalRequired())
						{
							$param = array(
								'label' => 'Request Membership',
								'action' => 'request-group',
								);
						}
						else
						{
							$param = array(
								'label' => 'Join Group',
								'action' => 'join-group',
								);
						}
				}
				if($param):
				?>
					<button class="yf_group_option_<?php echo $param['action']?>" onclick="return yfwelcome_doActionGroup(<?php echo $item->getIdentity()?>, '<?php echo $param['action']?>', <?php echo $item->category_id?>)"> <?php echo $this->translate($param['label']) ?> </button>
				<?php endif; ?>
			</div>
			<div class="yf_group_stats">
				<div class="yf_group_members">
					<i class="fa fa-users" title="<?php echo $this -> translate("Guests") ?>"></i>									
					<?php echo $this->translate(array("%s member", "%s member", $item->member_count),$item->member_count); ?>
				</div>
				<?php if($isAdvanced && $item -> location):
					$location = json_decode($item->location);?>
					<?php if(isset($location->{'location'}) && $location->{'location'}):?>
						<div class="yf_group_location">
							<i class="fa fa-map-marker" title="<?php echo $this -> translate("Location")?>"></i>
							<?php echo $location->{'location'} ?>
						</div>
					<?php else:?>
					<div class="yf_group_time_active">
						<i class="fa fa-clock-o" title="<?php echo $this -> translate("Time create") ?>"></i>
						<?php echo Engine_Api::_() -> ynfeed() -> getTimeAgo($item); ?>
					</div>
					<?php endif;?>
				<?php else:?>
					<div class="yf_group_time_active">
						<i class="fa fa-clock-o" title="<?php echo $this -> translate("Time create")?>"></i>
						<?php echo Engine_Api::_() -> ynfeed() -> getTimeAgo($item); ?>
					</div>
				<?php endif;?>				
			</div>
		</div>
    </li>
<?php endforeach;?>
<?php else:?>
    <div class="tip">
		<span><?php echo $this->translate("You have no groups suggestions.") ?></span>
	</div>
<?php endif;?>
</ul>
<script type="text/javascript">
var yfwelcome_doActionGroup = function(gid, action, category)
{
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynfeed/externals/images/loading.gif';
      $('yf_group_'+ gid).innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      new Request.JSON({
           url    :    en4.core.baseUrl + 'ynfeed/welcome/'+ action +'/',
           data : {
                format: 'json',
                group_id : gid
            },
            onComplete : function(responseJSON)
            {
                loadGroupSuggestions(category);
            }
       }).send();
       return false;
  }
</script>