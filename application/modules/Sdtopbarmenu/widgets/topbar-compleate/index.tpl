<style type="text/css" >
#global_header {
  background-image: url(application/modules/Sdtopbarmenu/externals/images/widget_bg.png);
  background-repeat: repeat-x;
  background-position: 0px 0px;
  min-height: 40px;
}
#global_header .layout_page_header {
  background: none repeat scroll 0 0 transparent;
  padding-top: 0px;	
}
.layout_page_header .layout_main {
  background: none;
  width: 1100px;
}
.layout_sdtopbarmenu_topbar_compleate .form-wrapper {
	margin-top: 0px;
}
.layout_sdtopbarmenu_topbar_compleate {
  height: 38px;
  z-index:99999;
  background-color:#333;
}
.layout_sdtopbarmenu_topbar_compleate #topBarContainer #sdLogo {
	margin-right: 20px;
	max-width: 200px;
	max-height: 30px;
	overflow: hidden;
}
.layout_sdtopbarmenu_topbar_compleate #sdMainMenu {
  margin-top: 5px;	
}
.layout_sdtopbarmenu_topbar_compleate #topBarContainer #core_menu_mini_menu {
  margin-top: 7px;	
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu .popularmembers_thumb {
  display: block;
  /*float: left;*/
  margin-right: 10px;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu .dropDownMenuMini {
  display: none;
  height:auto;
  position:absolute;
  z-index:200;
  background-color: #333;
  margin-left: -10px;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu {
    float: right;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu ul#sdMenuMini {
  overflow: auto;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu ul#sdMenuMini > li {
  float: left;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu ul#sdMenuMini > li {
  margin-left: 10px;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu ul#sdMenuMini > li.account_more {
  margin-top: 6px;
  width: 114px;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu ul#sdMenuMini > li.account_more:hover .dropDownMenuMini{
  display: block;
}
.layout_sdtopbarmenu_topbar_compleate #signin_sd > form  div#buttons-wrapper #submit-wrapper button#submit {
	padding:2px 5px;
	background-image:none;
	border:0px;
}
.layout_sdtopbarmenu_topbar_compleate #signin_sd {
	float: right;
}
.layout_sdtopbarmenu_topbar_compleate #core_menu_mini_menu #global_search_field {
  font-size: 1em;	
}

/* Styles for accept cancel request buttons and top div */
.layout_sdtopbarmenu_topbar_compleate .sd_pulldown_contents ul > li {
	cursor: default;
}
.layout_sdtopbarmenu_topbar_compleate .sd_pulldown_contents ul > li > span{
	cursor: pointer;
}
.layout_sdtopbarmenu_topbar_compleate ul#sd_notifications_menu_request > li .sd_buttons {
  float: right;
  margin-top: -10px;
  margin-bottom: 3px;
  cursor: default;	
}
.layout_sdtopbarmenu_topbar_compleate ul#sd_notifications_menu_request > li .sd_buttons button {
    border-radius: 3px 3px 3px 3px;
    color: #FFFFFF;
    font-size: 10px;
    font-weight: normal;
    padding: 2px 4px;
    text-shadow: none;	
}
.layout_sdtopbarmenu_topbar_compleate ul#sd_notifications_menu_request > li .sd_buttons button:hover {
  background-color: #619DBE;	
}
.layout_sdtopbarmenu_topbar_compleate .frequest_heading,
.layout_sdtopbarmenu_topbar_compleate .message_heading {
  border-bottom: 1px solid #cecece;
  text-align:right;
  padding: 3px;
  padding-right: 5px;
  margin-bottom:4px;
}
.layout_sdtopbarmenu_topbar_compleate .frequest_heading > a,
.layout_sdtopbarmenu_topbar_compleate .message_heading > a {
	font-size: 12px;
}
.layout_sdtopbarmenu_topbar_compleate #layout_core_menu_main_user {
	margin-left: 30px;	
}

#twitter-element a{
  display:block;
  width:82px;
  height:22px;
  background-image:url(application/modules/Sdtopbarmenu/externals/images/twitter-sign-in.png);
  background-repeat:no-repeat;
}
#twitter-element a img{
  display:none;
}

</style>




<div id="topBarContainer">
	<div id="sdLogo">
<?php
$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
$logo  = $this->logo;
$route = $this->viewer()->getIdentity()
             ? array('route'=>'user_general', 'action'=>'home')
             : array('route'=>'default');

echo ($logo)
     ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title)))
     : $this->htmlLink($route, $title);
?>
</div> <!-- EOF sdLogo -->

<div id="advminimenu_notifications">
<?php if( $this->viewer->getIdentity()) :?>
  <ul id="sd_custom_updates"> 

  
<!--Message-------------------------> 
  
    <li id='sd_custom_messages'>
      <span onclick="toggleUpdatesPulldownMesg(event, this, '4');" style="display: inline-block;" class="sd_updates_pulldown" id="sd_updates_pulldown_mesg">
        <div class="sd_pulldown_contents_wrapper">
          <div class="sd_pulldown_contents">
            <ul class="sd_notifications_menu" id="sd_notifications_menu_mesg">
              <div class="notifications_loading" id="notifications_loading_mesg">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'messages', 'controller' => 'inbox'),
               $this->translate('View All Messages'),
               array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'onclick'=>"markMessageSd()",
            )) ?>
          </div>
        </div>
         <a class="btn_messages <?php if( $this->messageCount ){echo "sd_new_updates";}?>" href="javascript:void(0);" id="updates_toggle_msg"><?php if($this->locale()->toNumber($this->messageCount) != 0){echo $this->locale()->toNumber($this->messageCount);}?></a>
      </span>
    </li>
<?php endif; ?>

<?php if( $this->viewer->getIdentity()) :?>


<!--Friends Request-------------------------> 

    <li id='sd_custom_friendrequest'>
      <span onclick="toggleUpdatesPulldownRequest(event, this, '4');" style="display: inline-block;" class="sd_updates_pulldown" id="sd_updates_pulldown_request">
        <div class="sd_pulldown_contents_wrapper">
          <div class="sd_pulldown_contents">
            <ul class="sd_notifications_menu" id="sd_notifications_menu_request">
              <div class="notifications_loading" id="notifications_loading_request">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sdtopbarmenu', 'controller' => 'index'),
               $this->translate('View All Requests'),
               array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'onclick'=>"markRequestSd()",
            )) ?>
          </div>
        </div>
        <a class="btn_friendrequest <?php if( $this->requestsCount ){echo "sd_new_updates";}?>" href="javascript:void(0);" id="updates_toggle_request"><?php if($this->locale()->toNumber($this->requestsCount) != 0){echo $this->locale()->toNumber($this->requestsCount);}?></a>
      </span>
    </li>
<?php endif; ?>

<?php if( $this->viewer->getIdentity()) :?>


<!--Notification-------------------------> 

    <li id='sd_custom_notification'>
      <span onclick="toggleUpdatesPulldownNotifecation(event, this, '4');" style="display: inline-block;" class="sd_updates_pulldown" id="sd_updates_pulldown_notification">
        <div class="sd_pulldown_contents_wrapper">
          <div class="sd_pulldown_contents">
            <ul class="sd_notifications_menu" id="sd_notifications_menu_notification">
              <div class="notifications_loading" id="notifications_loading_notification">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sdtopbarmenu', 'controller' => 'index'),
               $this->translate('View All Updates'),
               array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'onclick'=>"markNotificationSd()",
            )) ?>
          </div>
        </div>
        <a class="btn_notifications <?php if( $this->notificationCount ){echo "sd_new_updates";}?>" href="javascript:void(0);" id="updates_toggle_notification"><?php if($this->locale()->toNumber($this->notificationCount) != 0){echo $this->locale()->toNumber($this->notificationCount);}?></a>
      </span>
    </li>
   </ul>
<?php endif; ?>

</div><!-- EOF advminimenu_notifications -->


<?php if(!$this->viewer->getIdentity()) : ?>
	<?php echo '<div class="signup_wrapper"><a class="sd_signup" href="/signup">
    <button id="sd-signup">';?><?php echo $this->translate("Sign Up"); ?><?php echo '</button></a></div>'; ?>
<?php endif; ?>



<?php
	$viewer = $this->viewer();
	if($viewer->getIdentity() ) {
?>

<?php 
///////Main menue

function cutStringuser($str,$limit){
	if(strlen($str)<=$limit){
		return $str;
	}else{
		$arrStr = explode(" ",$str);
		if(count($arrStr)==1){
			return strip_tags(substr($str, 0, 10));
		}
		
		if(count($arrStr)>=3){
		$newLimit = $arrStr[0] . " " . $arrStr[1] . " " . $arrStr[2];
		$n=2;
		}else if(count($arrStr)==2){
		$newLimit = $arrStr[0] . " " . $arrStr[1];
		$n = 1;
		}
		if(strlen($newLimit)<=$limit-2){
			$newStr = $newLimit . "..";
			return $newStr;
		}else{
			$newLimit = $arrStr[0];
			for($i=1; $i<$n; $i++){
			$newLimit = $newLimit . " " . $arrStr[$i];
			}
			if(strlen($newLimit)<=$limit-2){
				$newStr = $newLimit . "..";
				return $newStr;
			}else{
				$newStr = $arrStr[0] . "..";
				if(strlen($newStr)<= $limit-2){
				return $newStr;
				}else{
					return strip_tags(substr($newStr, 0, 10));
				}
				
			}
		}
	}
}
?>

<?php
$modu = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
$acti = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
$i = 0;
$arrMore = array();
$total = 0;
$active_more_button = 0;
?>
<?php
	$viewer = $this->viewer();
	if(!$viewer || !$viewer->getIdentity() ) {
?>
<div id='layout_ysmenu_main'>
<?php
	}else{
?>
<div id='layout_core_menu_main_user'>
<?php
}
?>

 <ul id="sdMainMenu">
    <?php foreach( $this->navigation as $item ): ?>
    	<?php
        $label = $item->getLabel();
		if($i < 2){
	    	if((strstr(strtolower($label),$modu) != "") || ($modu=="core" && $label=="Home") || ($modu=="user" && $label=="Members" && $acti != "home") || ($modu=="user" && strtolower($label)==$acti)){
	    	?>
	    	<li class="active"><?php //echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel())) ?>
	    	<a href="<?php echo $item->getHref();?>"><b><?php echo $this->translate($item->getLabel());?></b></a>
	    	</li>
	        <?php
	        $total +=1;
	        $i += 1;
	    	}else{
	        ?>
	        <li>
	        <a href="<?php echo $item->getHref();?>"><b><?php echo $this->translate($item->getLabel());?></b></a>
	        </li>
	        <?php
	        $i += 1;
	        }
	    }else{
		  	if(strstr(strtolower($label),$modu) != ""){
              $arrMore[$i] = "<li class ='active'><a href='" . $item->getHref() . "' class='".$item->getclass()."'>" . $this->translate($item->getLabel()) . "</a></li>";
                $i += 1;
                $active_more_button = 1;
	    	}else{
                $arrMore[$i] = "<li><a class='".$item->getclass()."' href='" . $item->getHref() . "'>" . $this->translate($item->getLabel()) . "</a></li>";
                $i += 1;
	    	}
	    } 
       ?>
    <?php endforeach;
    if($i>2){
    	if($active_more_button == 1){
   
    ?>
    		<li id = "more" class="view_more_menu"><a href="javascript: void(0)"><b><?php echo $this->translate("Browse"); ?></b></a>
        <?php
        }
        else
        {
        ?>
    		<li id = "more" class="view_more_menu"><a href="javascript: void(0)"><b><?php echo $this->translate("Browse"); ?></b></a>
    	<?php 
        } 
        ?>
            <ul id="dropDownMenuMain" class="dropDownMenuMain">
            <?php
                for($j = 2; $j < $i; $j++){
                    echo $arrMore[$j];
                }
            ?>
            </ul>
    <?php
    }
    ?>    
    </li>
    </ul>

 </div>
<!-------------------End main menue and start Mini menue------------------------------->



<div id='core_menu_mini_menu'>
  <?php
    // Reverse the navigation order (theyre floating right)
    $count = count($this->navigation2);
    foreach( $this->navigation2->getPages() as $item ) $item->setOrder(--$count);
  ?>
 <?php //echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon'), array('class' => 'popularmembers_thumb')) ?> 
  <ul id="sdMenuMini">
  
  
  
   <?php if($this->search_check):?>
      <li id="global_search_form_container">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
        </form>
      </li>
    <?php endif;?>
  <li class="user_img"><?php echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon'), array('class' => 'user_img_topbar')) ?></li>
  
  <li class="account_more"><a class="btn_account" href="javascript: void(0)"><b><?php echo $this->translate("Account"); ?></b></a>
 	 <ul class="dropDownMenuMini">
   	 	<?php foreach( $this->navigation2 as $item ): ?>
     	 <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
       	 'class' => ( !empty($item->class) ? $item->class : null ),
        	'alt' => ( !empty($item->alt) ? $item->alt : null ),
        	'target' => ( !empty($item->target) ? $item->target : null ),
      	))) ?></li>
    	<?php endforeach; ?>
    	</ul></li>
  </ul>
</div><!-- EOF core_menu_mini_menu -->
<?php } else {?>

<!-- Section for guests -->


<div id="signin_sd">


<?php if( !$this->noForm ): ?>

 <!-- <h3>
    <?php //echo $this->translate('Sign In or %1$sJoin%2$s', '<a href="'.$this->url(array(), "user_signup").'">', '</a>'); ?>
  </h3>-->

  <?php echo $this->form->setAttrib('class', 'global_form_box')->render($this) ?>

  <?php if( !empty($this->fbUrl) ): ?>

    <script type="text/javascript">
      var openFbLogin = function() {
        Smoothbox.open('<?php echo $this->fbUrl ?>');
      }
      var redirectPostFbLogin = function() {
        window.location.href = window.location;
        Smoothbox.close();
      }
    </script>

    <?php // <button class="user_facebook_connect" onclick="openFbLogin();"></button> ?>

  <?php endif; ?>

<?php else: ?>
    
  <h3 style="margin-bottom: 0px;">
    <?php echo $this->htmlLink(array('route' => 'user_login'), $this->translate('Sign In')) ?>
    <?php echo $this->translate('or') ?>
    <?php echo $this->htmlLink(array('route' => 'user_signup'), $this->translate('Join')) ?>
  </h3>

  <?php echo $this->form->setAttrib('class', 'global_form_box no_form')->render($this) ?>
    
<?php endif; ?>

</div><!-- EOF signin_sd -->






<?php } ?>


</div><!-- EOF topBarContainer -->

<script type='text/javascript'>

///           Message code start

var notificationUpdaterMesg;
  en4.core.runonce.add(function(){
    if($('notifications_markread_link_mesg')){
      $('notifications_markread_link_mesg').addEvent('click', function() {
        en4.messageSd.hideNotificationsMesg('<?php echo $this->string()->escapeJavascript($this->translate(""));?>');
      });
    }
    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdaterMesg = new NotificationUpdateHandlerMesg({
			  'delay' : <?php echo $this->updateSettings;?>-10000
            });
    notificationUpdaterMesg.start();
    window._notificationUpdaterMesg = notificationUpdaterMesg;
    <?php endif;?>
	
  });
  
   var markMessageSd = function() {
	new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/hidemessag',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      en4.messageSd.updateNotificationsMesg();  
      }
    }).send();
  }

  var toggleUpdatesPulldownMesg = function(event, element, user_id) {
    if( element.className=='sd_updates_pulldown' ) {
      element.className= 'sd_updates_pulldown_active';
      showNotificationsMesg();
    } else {
      element.className='sd_updates_pulldown';
    }
	if( $('sd_updates_pulldown_request').className=='sd_updates_pulldown_active' ) {
      $('sd_updates_pulldown_request').className= 'sd_updates_pulldown';
    }
	if( $('sd_updates_pulldown_notification').className=='sd_updates_pulldown_active' ) {
      $('sd_updates_pulldown_notification').className= 'sd_updates_pulldown';
    }
  }

  var showNotificationsMesg = function() {
    en4.messageSd.updateNotificationsMesg();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/messagepulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading_mesg')) $('notifications_loading_mesg').setStyle('display', 'none');

          $('sd_notifications_menu_mesg').innerHTML = responseHTML;
          $('sd_notifications_menu_mesg').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link_mesg = event.target;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			if(current_link_mesg.nodeName == 'DIV' || current_link_mesg.nodeName == 'IMG'){
				return false;
			}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            var notification_li_mesg = $(current_link_mesg).getParent('li');
            if( notification_li_mesg.id == 'core_menu_mini_menu_update' ) {
              notification_li_mesg = current_link_mesg;
            }
            var forward_link_mesg;
            if( current_link_mesg.get('href') ) {
              forward_link_mesg = current_link_mesg.get('href');
            } else{
              forward_link_mesg = $(current_link_mesg).getElements('a:last-child').get('href');
            }
            if( notification_li_mesg.get('class') == 'notifications_unread' ){
              notification_li_mesg.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'sdtopbarmenu/index/hidemessag',
                data : {
                  format     : 'json',
                  'actionid' : notification_li_mesg.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link_mesg;
                }
              }));
            } else {
              window.location = forward_link_mesg;
            }
          });
        } else {
          $('sd_notifications_menu_mesg').innerHTML = '<div class="message_heading"><a class="compose" href="/messages/compose"><?php echo $this->translate("Send New Message") ?></a></div><?php echo $this->translate("You have no new Message.");?>';
	
		  
		}
      }
    }).send();
  };
  
  (function() {
var $ = 'msg' in document ? document.msg : window.$;
en4.messageSd = {
    hideNotificationsMesg : function(reset_text) {
    en4.core.request.send(new Request.JSON({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/hidemessag',
      'data' : {
        'format' : 'html',
        'page' : 1
      }
    }));
    $('updates_toggle_msg').set('html', reset_text).removeClass('sd_new_updates');

    if($('updates_toggle_msg')){
      var notification_children = $('updates_toggle_msg').getChildren('li');
      notification_children.each(function(el){
          el.setAttribute('class', '');
      });
    }
  },
  	
  updateNotificationsMesg : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sdtopbarmenu/index/messageupdate',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotificationsMesg.bind(this)
    }));
  },

  showNotificationsMesg : function(responseJSON){
    if (responseJSON.messageCount>0){
      $('updates_toggle_msg').set('html', responseJSON.text).addClass('sd_new_updates');
    }
	else
	{
		$('updates_toggle_msg').set('html', responseJSON.text).removeClass('sd_new_updates');
	}
  },

};

NotificationUpdateHandlerMesg = new Class({

  Implements : [Events, Options],
  options : {
      debug : false,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      subject_guid : null
    },
  state : true,
  activestate : 1,
  fresh : true,
  lastEventTime : false,
  title: document.title,
  initialize : function(options) {
    this.setOptions(options);
  },
  start : function() {
    this.state = true;
    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this.activestate = 1;
        this.state= true;
      }.bind(this),
      'onStateIdle' : function() {
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
  },
  stop : function() {
    this.state = false;
  },

  updateNotificationsMesg : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sdtopbarmenu/index/messageupdate',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotificationsMesg.bind(this)
    }));
  },

  showNotificationsMesg : function(responseJSON){
    if (responseJSON.messageCount>0){
      $('updates_toggle_msg').set('html', responseJSON.text).addClass('sd_new_updates');
    }
	else
	{
		$('updates_toggle_msg').set('html', responseJSON.text).removeClass('sd_new_updates');
	}
  },
  
  loopMesg : function() {
    if( !this.state) {
      this.loop.delay(this.options.delay, this);
      return;
    }
    try {
      this.updateNotificationsMesg().addEvent('complete', function() {
        this.loopMesg.delay(this.options.delay, this);
      }.bind(this));
    } catch( e ) {
      this.loopMesg.delay(this.options.delay, this);
      this._log(e);
    }
  },
  // Utility
  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }
    // Firefox is dumb and causes problems sometimes with console
    try {
      if( typeof(console) && $type(console) ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});

})(); // END NAMESPACE





///           Friend Request code start

 var notificationUpdaterRequest;
  en4.core.runonce.add(function(){
    if($('notifications_markread_link_request')){
      $('notifications_markread_link_request').addEvent('click', function() {
        en4.requestSd.hideNotificationsRequest('<?php echo $this->string()->escapeJavascript($this->translate(""));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdaterRequest = new NotificationUpdateHandlerRequest({
			  'delay' : <?php echo $this->updateSettings;?>+10000
            });
    notificationUpdaterRequest.start();
    window._notificationUpdaterRequest = notificationUpdaterRequest;
    <?php endif;?>
  });
  
   var markRequestSd = function() {
	new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/hiderequest',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      en4.requestSd.updateNotificationsRequest();  
      }
    }).send();
  }

////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
   var friendConfirm = function(friendId) {
	new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/friend-confirm',
      'data' : {
        'format' : 'html',
        'resource_id' : friendId
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      showNotificationsRequest(); 
      }
    }).send();
  }
  
   var friendCancel = function(friendId) {
	new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/friend-cancel',
      'data' : {
        'format' : 'html',
        'resource_id' : friendId
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      showNotificationsRequest(); 
      }
    }).send();
  }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  var toggleUpdatesPulldownRequest = function(event, element, user_id) {
    if( element.className=='sd_updates_pulldown' ) {
      element.className= 'sd_updates_pulldown_active';
      showNotificationsRequest();
    } else {
      element.className='sd_updates_pulldown';
    }
	if( $('sd_updates_pulldown_mesg').className=='sd_updates_pulldown_active' ) {
      $('sd_updates_pulldown_mesg').className= 'sd_updates_pulldown';
    }
	if( $('sd_updates_pulldown_notification').className=='sd_updates_pulldown_active' ) {
      $('sd_updates_pulldown_notification').className= 'sd_updates_pulldown';
    }
  }

  var showNotificationsRequest = function() {
    en4.requestSd.updateNotificationsRequest();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/requestpulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading_request')) $('notifications_loading_request').setStyle('display', 'none');

          $('sd_notifications_menu_request').innerHTML = responseHTML;
          $('sd_notifications_menu_request').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link_request = event.target;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			if(current_link_request.nodeName == 'DIV' || current_link_request.nodeName == 'IMG' || current_link_request.nodeName == 'BUTTON'){
				return false;
			}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            var notification_li_request = $(current_link_request).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li_request.id == 'core_menu_mini_menu_update1' ) {
              notification_li_request = current_link_request;
            }

            var forward_link_request;
            if( current_link_request.get('href') ) {
              forward_link_request = current_link_request.get('href');
            } else{
              forward_link_request = $(current_link_request).getElements('a:last-child').get('href');
            }

            if( notification_li_request.get('class') == 'notifications_unread' ){
              notification_li_request.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'sdtopbarmenu/index/hiderequest',
                data : {
                  format     : 'json',
                  'actionid' : notification_li_request.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link_request;
                }
              }));
            } else {
              window.location = forward_link_request;
            }
          });
        } else {
          $('sd_notifications_menu_request').innerHTML = '<div class="frequest_heading"><a class="compose" href="/members"><?php echo $this->translate("Find New Friends") ?></a></div><?php echo $this->translate("You have no new Request.");?>';
        }
      }
    }).send();
  };
  

  
  
  
  
  
  (function() { // START NAMESPACE
var $ = 'ic' in document ? document.ic : window.$;
en4.requestSd = {
	
  hideNotificationsRequest : function(reset_text) {
    en4.core.request.send(new Request.JSON({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/hiderequest'
    }));
    $('updates_toggle_request').set('html', reset_text).removeClass('sd_new_updates');

    if($('updates_toggle_request')){
      var notification_children = $('updates_toggle_request').getChildren('li');
      notification_children.each(function(el){
          el.setAttribute('class', '');
      });
    }
  },	
	
  updateNotificationsRequest : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sdtopbarmenu/index/requestupdate',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotificationsRequest.bind(this)
    }));
  },

  showNotificationsRequest : function(responseJSON){
    if (responseJSON.requestsCount>0){
      $('updates_toggle_request').set('html', responseJSON.text).addClass('sd_new_updates');
    }
	else
	{
		$('updates_toggle_request').set('html', responseJSON.text).removeClass('sd_new_updates');
	}
  },

};

NotificationUpdateHandlerRequest = new Class({

  Implements : [Events, Options],
  options : {
      debug : false,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      subject_guid : null
    },

  state : true,

  activestate : 1,

  fresh : true,

  lastEventTime : false,

  title: document.title,

  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this.activestate = 1;
        this.state= true;
      }.bind(this),
      'onStateIdle' : function() {
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });

    this.loopRequest();
  },

  stop : function() {
    this.state = false;
  },

  updateNotificationsRequest : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sdtopbarmenu/index/requestupdate',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotificationsRequest.bind(this)
    }));
  },

  showNotificationsRequest : function(responseJSON){
    if (responseJSON.requestsCount>0){
      $('updates_toggle_request').set('html', responseJSON.text).addClass('sd_new_updates');
    }
	else
	{
		$('updates_toggle_request').set('html', responseJSON.text).removeClass('sd_new_updates');
	}
  },
  
  loopRequest : function() {
    if( !this.state) {
      this.loopRequest.delay(this.options.delay, this);
      return;
    }

    try {
      this.updateNotificationsRequest().addEvent('complete', function() {
        this.loopRequest.delay(this.options.delay, this);
      }.bind(this));
    } catch( e ) {
      this.loopRequest.delay(this.options.delay, this);
      this._log(e);
    }
  },

  // Utility

  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }

    // Firefox is dumb and causes problems sometimes with console
    try {
      if( typeof(console) && $type(console) ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});

})(); // END NAMESPACE




///           Notification code start

  var notificationUpdaterSd;
  en4.core.runonce.add(function(){
    if($('notifications_markread_link_sd')){
      $('notifications_markread_link_sd').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.notificationSd.hideNotificationsSd('<?php echo $this->string()->escapeJavascript($this->translate("0"));?>');
      });
    }


    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdaterSd = new NotificationUpdateHandlerSd({
			  'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdaterSd.start();
    window._notificationUpdaterSd = notificationUpdaterSd;
    <?php endif;?>
  });
  
   var markNotificationSd = function() {   
	new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/hide',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       en4.notificationSd.updateNotificationsSd();
      }
    }).send();
  }


  var toggleUpdatesPulldownNotifecation = function(event, element, user_id) {
    if( element.className=='sd_updates_pulldown' ) {
      element.className= 'sd_updates_pulldown_active';
      showNotificationsSd();
    } else {
      element.className='sd_updates_pulldown';
    }
	if( $('sd_updates_pulldown_mesg').className=='sd_updates_pulldown_active' ) {
      $('sd_updates_pulldown_mesg').className= 'sd_updates_pulldown';
    }
	if( $('sd_updates_pulldown_request').className=='sd_updates_pulldown_active' ) {
      $('sd_updates_pulldown_request').className= 'sd_updates_pulldown';
    }
  }

  var showNotificationsSd = function() {
    en4.notificationSd.updateNotificationsSd();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading_notification')) $('notifications_loading_notification').setStyle('display', 'none');

          $('sd_notifications_menu_notification').innerHTML = responseHTML;
          $('sd_notifications_menu_notification').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_linkSd = event.target;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			if(current_linkSd.nodeName == 'DIV' || current_linkSd.nodeName == 'IMG'){
				return false;
			}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            var notification_liSd = $(current_linkSd).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_liSd.id == 'core_menu_mini_menu_update2' ) {
              notification_liSd = current_linkSd;
            }

            var forward_linkSd;
            if( current_linkSd.get('href') ) {
              forward_linkSd = current_linkSd.get('href');
            } else{
              forward_linkSd = $(current_linkSd).getElements('a:last-child').get('href');
            }

            if( notification_liSd.get('class') == 'notifications_unread' ){
              notification_liSd.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'sdtopbarmenu/index/hide',
                data : {
                  format     : 'json',
                  'actionid' : notification_liSd.get('value')
                },
                onSuccess : function() {
                  window.location = forward_linkSd;
                }
              }));
            } else {
              window.location = forward_linkSd;
            }
          });
        } else {
          $('sd_notifications_menu_notification').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new Notification."));?>';
        }
      }
    }).send();
  };
  
 
  (function() { // START NAMESPACE
var $ = 'iddd' in document ? document.iddd : window.$;
en4.notificationSd = {
	
  hideNotificationsSd : function(reset_text) {
    en4.core.request.send(new Request.JSON({
      'url' : en4.core.baseUrl + 'sdtopbarmenu/index/hide'
    }));
    $('updates_toggle_notification').set('html', reset_text).removeClass('sd_new_updates');

    if($('updates_toggle_notification')){
      var notification_children = $('updates_toggle_notification').getChildren('li');
      notification_children.each(function(el){
          el.setAttribute('class', '');
      });
    }
  },	
	
  updateNotificationsSd : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sdtopbarmenu/index/update',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotificationsSd.bind(this)
    }));
  },

  showNotificationsSd : function(responseJSON){
    if (responseJSON.notificationCount>0){
      $('updates_toggle_notification').set('html', responseJSON.text).addClass('sd_new_updates');
    }
	else
	{
		$('updates_toggle_notification').set('html', responseJSON.text).removeClass('sd_new_updates');
	}
  },

};
NotificationUpdateHandlerSd = new Class({

  Implements : [Events, Options],
  options : {
      debug : false,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      subject_guid : null
    },

  state : true,

  activestate : 1,

  fresh : true,

  lastEventTime : false,

  title: document.title,

  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this.activestate = 1;
        this.state= true;
      }.bind(this),
      'onStateIdle' : function() {
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });

    this.loopNotification();
  },

  stop : function() {
    this.state = false;
  },

  updateNotificationsSd : function() {
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sdtopbarmenu/index/update',
      data : {
        format : 'json'
      },
      onSuccess : this.showNotificationsSd.bind(this)
    }));
  },

  showNotificationsSd : function(responseJSON){
    if (responseJSON.notificationCount>0){
      $('updates_toggle_notification').set('html', responseJSON.text).addClass('sd_new_updates');
    }
	else
	{
		$('updates_toggle_notification').set('html', responseJSON.text).removeClass('sd_new_updates');
	}
  },
  
  loopNotification : function() {
    if( !this.state) {
      this.loopNotification.delay(this.options.delay, this);
      return;
    }

    try {
      this.updateNotificationsSd().addEvent('complete', function() {
        this.loopNotification.delay(this.options.delay, this);
      }.bind(this));
    } catch( e ) {
      this.loopNotification.delay(this.options.delay, this);
      this._log(e);
    }
  },

  // Utility

  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }

    // Firefox is dumb and causes problems sometimes with console
    try {
      if( typeof(console) && $type(console) ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});

})(); // END NAMESPACE

</script>