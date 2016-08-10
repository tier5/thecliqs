<ul>
<?php if (count($this->event_suggestions)): ?>
<?php
$isAdvanced = false;
$module = 'event';
if (Engine_Api::_() -> hasModuleBootstrap('ynevent')) {
	$isAdvanced = true;
	$module = 'ynevent';
}
foreach($this -> event_suggestions as $item):?>
	<li id="yf_event_<?php echo $item->getIdentity()?>">
		<div class="yf_event_photo">
            <?php $backgroundURL = $item -> getPhotoUrl("thumb.profile");
			if(!$backgroundURL)
			{
				$backgroundURL = $this->baseUrl().'/application/modules/Event/externals/images/nophoto_event_thumb_profile.png';
			}?>
            <?php echo $this->htmlLink($item->getHref(), '<span class="image-thumb" style="background-image: url('.$backgroundURL.');"></span>', array('class' => 'thumb')) ?>
        </div>
        <div class="yf_event_info ynfeed-clearfix">
            <div class="yf_event_date">
                <span class="yf_event_day"><?php 
                $start_time = strtotime($item -> starttime);
				$oldTz = date_default_timezone_get();
				if($this->viewer() && $this->viewer()->getIdentity())
				{
					date_default_timezone_set($this -> viewer() -> timezone);
				}
				else {
					date_default_timezone_set( $this->locale() -> getTimezone());
				}
                echo date("d", $start_time); ?></span>
                <span class="yf_event_month"><?php echo date("M", $start_time); 
                date_default_timezone_set($oldTz);?></span>
            </div>
            <div class="yf_event_title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              <br />
              <span class="yf_event_events_members" style="font-weight: normal">
              <?php 
            	if($item->host)
            	{
	            	if(strpos($item->host,'younetco_event_key_') !== FALSE)
					{
					  	$user_id = substr($item->host, 19, strlen($item->host));
						$user = Engine_Api::_() -> getItem('user', $user_id);
						
						echo $this->translate('host by %1$s',
	                  	$this->htmlLink($user->getHref(), $user->getTitle())) ;
					}
					else{
						echo $this->translate('host by %1$s', $item->host);
					}
				}
				else{
					echo $this->translate('by %1$s',
	                  	$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ;
				}
            	?>
             </span>
            </div>                
            <div class="yf_event_stats">
                <span class="yf_event_person" title="<?php echo $this -> translate("Guests")?>"><?php echo $item->member_count; ?> <i class="ynfeed_ynicon-person"></i></span>
                <?php if($isAdvanced):?>
                	<span class="yf_event_like" title="<?php echo $this -> translate("Likes")?>"><?php echo $item->likes()->getLikeCount(); ?> <i class="fa fa-heart <?php if ($item->likes()->getLikeCount()==0) echo "gray";?>"></i></span>
                <?php endif;?>
            </div>
		</div>
		<div class="yf_event_options">
			<?php
			$param = array();
			if ($item -> membership() -> isResourceApprovalRequired())
			{
				$param = array(
					'label' => 'Request Invite',
					'action' => 'request-event',
				);
			}  
			else
			{
				$param = array(
					'label' => 'Join Event',
					'action' => 'join-event',
				);
			}
			if($param):
			?>
				<a href="javascript:;" class="yf_event_option_<?php echo $param['action']?>" onclick="return yfwelcome_doActionEvent(<?php echo $item->getIdentity()?>, '<?php echo $param['action']?>', <?php echo $item->category_id?>)"> <?php echo $this->translate($param['label']) ?> </a>
			<?php endif;?>
		</div>
    </li>
<?php endforeach;?>
<?php else:?>
    <div class="tip">
		<span><?php echo $this->translate("You have no events suggestions.") ?></span>
	</div>
<?php endif;?>
</ul>
<script type="text/javascript">
var yfwelcome_doActionEvent = function(eid, action, category)
{
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynfeed/externals/images/loading.gif';
      $('yf_event_'+ eid).innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      new Request.JSON({
           url    :    en4.core.baseUrl + 'ynfeed/welcome/'+ action +'/',
           data : {
                format: 'json',
                event_id : eid
            },
            onComplete : function(responseJSON)
            {
                loadEventSuggestions(category);
            }
       }).send();
       return false;
  }
</script>