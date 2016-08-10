<ul>
<?php if (count($this->friend_requests)): ?>
<?php foreach($this -> friend_requests as $item):?>
	<?php 
	$viewer_id = $this -> viewer() ->getIdentity();
	$subject = Engine_Api::_()->user()->getUser($item->resource_id);
	$friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsName = $friendsTable->info('name'); 
    // Mututal friends/following mode            
    $sql = "SELECT `user_id` FROM `{$friendsName}` WHERE (`active`= 1 and `resource_id`={$item->resource_id})
        and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$viewer_id} and `active`= 1))";
    $friends = $friendsTable->getAdapter()->fetchcol($sql);
	$totalFriends = count($friends);
	?>
	<li id="yf_requests_<?php echo $subject->getIdentity()?>" class ="ynfeed_user_item">
		<a href="<?php echo $subject->getHref(); ?>">
		    <?php echo $this->itemPhoto($subject, 'thumb.profile');?>
		</a>
		<div class="yf_requests_info">
			<div class="yf_requests_link">
				<?php echo $subject;?>
			</div>
    		<div class="yf_requests_mutual"> 
    			<?php echo $this->translate(array("%s mutual friend","%s mutual friends",$totalFriends), $this->locale()->toNumber($totalFriends));?>
    		</div>
			<div class="yf_requests_options" id="yf_friend_action_<?php echo $subject->getIdentity()?>"> 
				<button class="yf_requests_optionAccept" onclick="return yfwelcome_doConfirm(<?php echo $subject->getIdentity()?>)"> <?php echo $this->translate("Accept") ?> </button>
				<a href="javascript:;" class="yf_requests_optionCancel" onclick="return yfwelcome_doCancel(<?php echo $subject->getIdentity()?>)"> <?php echo $this->translate("Not Now") ?> </a>
			</div>
		</div>
    </li>
<?php endforeach;?>
<?php else:?>
    <div class="tip">
		<span><?php echo $this->translate("You have no new friend requests.") ?></span>
	</div>
<?php endif;?>
</ul>
<script type="text/javascript">
var yfwelcome_doConfirm = function(rid)
{
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynfeed/externals/images/loading.gif';
      $('yf_requests_'+rid).innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      new Request.JSON({
           url    :    en4.core.baseUrl + 'user/friends/confirm/',
           data : {
                format : 'json',
                user_id : rid
            },
            onComplete : function(responseJSON)
            {
                loadFriendRequests();
            }
       }).send();
       return false;
  }

  var yfwelcome_doCancel = function(rid)
  {
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynfeed/externals/images/loading.gif';
      $('yf_requests_'+rid).innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      new Request.JSON({
           url    :    en4.core.baseUrl + 'user/friends/cancel/',
           data : {
                format : 'json',
                user_id : rid
            },
            onComplete : function(responseJSON)
            {
                loadFriendRequests();
            }
       }).send();
       return false;
  }
</script>