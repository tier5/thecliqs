<script type="text/javascript">
  var tabProfileContainerSwitch = function(element) {
    if (en4.core.request.isRequestActive())
      return;
    if (element.tagName.toLowerCase() == 'a') {
      element = element.getParent('li');
    }
    var myContainer = element.getParent('.ynfeed_tabs_feed').getParent();
    myContainer.getElements('ul > li').removeClass('ynfeed_tab_active');
    element.get('class').split(' ').each(function(className) {
      className = className.trim();
      if (className.match(/^tab_[0-9]+$/)) {
        element.addClass('ynfeed_tab_active');

      }
    });
  }
</script>
<div class="ynfeed_tabs_feed">
  <ul class="ynfeed_tabs_apps_feed">
  	 <li class="tab_1 ynfeed_tab_active" id="tab_advFeed_everyone">        
      <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
	    ynfeedFilter('all', '0');
	    $('feed-update').empty();" title="<?php echo $this->translate("All Activities") ?>"><?php echo $this->translate("All Activities") ?><span id="update_advfeed_blink" class="notification_star"></span></a>
    </li>
    <li>&#8226;</li>
    <li class="tab_2"> 	
      <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
	    ynfeedFilter('owner', '0');
	    $('feed-update').empty();
	    $('feed-update').style.display = 'none';" title="<?php echo $this -> translate("Owner of %s", $this->subject()->getTitle())?>">
	        <?php
	        if ($this->subject()->getType() === 'user'):
	          echo $this -> translate("%s's posts", $this->subject()->getTitle());
	        else:
	          echo ($this->subject()->getType() === 'event' || $this->subject()->getType() === 'ynevent_event') ? $this->translate("Event Owner") : $this->translate("Owner") ;
	        endif;
	        ?>  
	    </a>
    </li>
    <?php if ($this->subject()->getType() === 'event' || $this->subject()->getType() === 'ynevent'): ?>
	      <li>&#8226;</li>	
	      <li class="tab_3">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('membership', '0');
		      $('feed-update').empty();"  title="<?php echo  $this->translate("Guests")  ?>"><?php echo  $this->translate("Guests")  ?></a>
	      </li> 
	  <?php elseif ($this->subject()->getType() === 'group' || $this->subject()->getType() === 'advgroup'): ?>
	  	 <li>&#8226;</li>	
	      <li class="tab_3">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('officers', '0');
		      $('feed-update').empty();"  title="<?php echo  $this->translate("Officers")  ?>"><?php echo  $this->translate("Officers")  ?></a>
	      </li> 
	  	 <li>&#8226;</li>	
	      <li class="tab_4">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('membership', '0');
		      $('feed-update').empty();"  title="<?php echo  $this->translate("Members")  ?>"><?php echo  $this->translate("Members")  ?></a>
	      </li> 
	  <?php elseif ($this->subject()->getType() === 'ynbusinesspages_business'): ?>
	  	 <li>&#8226;</li>	
	      <li class="tab_3">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('business', '0');
		      $('feed-update').empty();"  title="<?php echo $this->subject()->getTitle() ?>"><?php  echo $this -> translate("%s's posts", $this->subject()->getTitle());?></a>
	      </li> 
	  	 <li>&#8226;</li>	
	      <li class="tab_3">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('admins', '0');
		      $('feed-update').empty();"  title="<?php echo  $this->translate("Admins")  ?>"><?php echo  $this->translate("Admins")  ?></a>
	      </li> 
	  	 <li>&#8226;</li>	
	      <li class="tab_4">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('membership', '0');
		      $('feed-update').empty();"  title="<?php echo  $this->translate("Members")  ?>"><?php echo  $this->translate("Members")  ?></a>
	      </li> 	
	  <?php elseif ($this->viewer()->getIdentity() && ($this->subject()->getType() != 'user')): ?>
	      <li>&#8226;</li>	
	      <li class="tab_3">
	        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));
		      ynfeedFilter('membership', '0');
		      $('feed-update').empty();" <?php echo  $this->translate("Friends") ?> ><?php echo  $this->translate("Friends") ?></a>
	      </li>   
	<?php endif; ?>
  </ul> 	
</div>