<ul>
<?php if (count($this->member_suggestions)): ?>
<?php 
$direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
foreach($this -> member_suggestions as $item):?>
	<li id="yf_member_<?php echo $item->getIdentity()?>" class="ynfeed_user_item">
		<a href="<?php echo $item->getHref(); ?>">
		    <?php echo $this->itemPhoto($item, 'thumb.profile');?>
		</a>
		<div class="yf_member_info">
			<div class="yf_member_link">
				<?php echo $item;?>
			</div>
    		<div class="yf_member_mutual"> 
    			<?php echo $this->translate(array("%s mutual friend","%s mutual friends", $item -> count), $this->locale()->toNumber($item -> count));?>
    		</div>
			<div class="yf_member_options" id="yf_member_action_<?php echo $item->getIdentity()?>"> 
				<?php
				if( !$direction )
				{
			      $viewerRow = $this->viewer()->membership()->getRow($item);
			      $subjectRow = $item->membership()->getRow($viewer);
			      $params = array();
			      
			      // Viewer?
			      if( null === $subjectRow ) {
			        // Follow
			        $params[] = array(
			          'label' => 'Follow',
			          'action' => 'add',
			        );
			      } 
			      // Subject?
			      if( null === $viewerRow ) 
			      {
			        // Do nothing
			      } else if( $viewerRow->resource_approved == 0 ) 
			      {
			        // Approve follow request
			        $params[] = array(
			          'label' => 'Approve Follow Request',
			          'action' => 'confirm',
			        );
			      }
			    }
			
			    // Two-way mode
			    else {
			      $params = array();
			      $row = $this->viewer()->membership()->getRow($item);
			      if( null === $row ) {
			        // Add
			        $params[] = array(
			          'label' => 'Add Friends',
			          'action' => 'add',
			        );
			      } else if( $row->resource_approved == 0 ) {
			        // Approve request
			        $params[] = array(
			          'label' => 'Approve Friend Request',
			          'action' => 'confirm',
			        );
				  }
			    }
				foreach ($params as $param):?>
					<button class="yf_member_option_<?php echo $param['action']?>" onclick="return yfwelcome_doActionFriend(<?php echo $item->getIdentity()?>, '<?php echo $param['action']?>')"> <?php echo $this->translate($param['label']) ?> </button>
				<?php endforeach;?>
			</div>
		</div>
    </li>
<?php endforeach;?>
<?php else:?>
    <div class="tip">
		<span><?php echo $this->translate("You have no new member suggestions.") ?></span>
	</div>
<?php endif;?>
</ul>
<script type="text/javascript">
var yfwelcome_doActionFriend = function(rid, action)
{
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynfeed/externals/images/loading.gif';
      $('yf_member_'+rid).innerHTML = '<img src="'+ img_loading +'" border="0" />';
      new Request.JSON({
           url    :    en4.core.baseUrl + 'user/friends/'+ action +'/',
           data : {
                format: 'json',
                user_id : rid,
            },
            onComplete : function(responseJSON)
            {
                loadMemberSuggestions();
            }
       }).send();
       return false;
  }
</script>