<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    topmenu
 * @copyright  Copyright 2011 SocialEnginePro
 * @license    http://www.socialengine.net/license/
 * @author     altrego
 */
?>

<div class="layout_topmenu">

<?php if ( $this->viewer->getIdentity() ) { ?>

	<?php if( $this->navigation->count()<=1 ) { ?>
<style type="text/css">
	.layout_topmenu .topmenu_shortcuts li { display:block; }
</style>
	<?php }?>

<script type="text/javascript">
window.addEvent('domready',function(){
	$('userStatusMenu').fade('show');
	$('userStatusMenu').fade('hide');

	$('btnChangeStatus').addEvent('click',function(){
		$dropdown = $('userStatusMenu');
		$dropdown.fade('toggle');
	});

	$$('#userStatusMenu li a').addEvent('click',function(){
		var translate = {
			'online':'<?php echo $this->translate('sep_userstatus_online')?>',
			'away':'<?php echo $this->translate('sep_userstatus_away')?>',
			'busy':'<?php echo $this->translate('sep_userstatus_busy')?>',
			'out to lunch':'<?php echo $this->translate('sep_userstatus_out to lunch')?>',
			'unavailable':'<?php echo $this->translate('sep_userstatus_unavailable')?>',
			'invisible':'<?php echo $this->translate('sep_userstatus_invisible')?>'
		};

		new Request.JSON({
			'url' : '<?php echo $this->url()?>/?action=setUserStatus&status=' + this.rel + '&nocache='+Math.random(),
			'method' : 'get',
			onSuccess: function(response){
			}
		}).send();

		$('userStatusMenu').fade('out');
		$("btnChangeStatus").set('html', translate[this.rel]);

		if( this.rel=='online' ) {
			$('btnChangeStatus').set('class', 'user_status user_online');
		} else if ( this.rel=='away' || this.rel=='out to lunch' ) {
			$('btnChangeStatus').set('class', 'user_status user_away');
		} else if ( this.rel=='busy' || this.rel=='unavailable' ) {
			$('btnChangeStatus').set('class', 'user_status user_busy');
		} else if ( this.rel=='invisible' ) {
			$('btnChangeStatus').set('class', 'user_status user_offline');
		}
	});

	<?php if( $this->navigation->count()>1 ) { ?>
	$('btnExpand').addEvent('click', function(){
		$$('.topmenu_shortcuts li').setStyle('display', 'block');
		$('btnExpand').setStyle('display','none');
	});
	<?php }?>

});
</script>
	<ul class="topmenu_shortcuts">
	<?php if( $this->navigation->count()>1 ) {?>
		<li id="btnExpand"><a href="javascript://" class="topmenu_icon topmenu_icon_expand"></a></li>
	<?php }?>
	<?php
		foreach( $this->navigation as $item ) {
			$item_route = $item->getRoute();
			// take menu item class for link by Route. default is a Route string without '_general' or Route string
			switch ($item_route) {
				default :
					$general_strpos = strpos($item_route, '_general');
					if ( $general_strpos!==false ) {
						$menu_item_class = substr($item_route, 0, $general_strpos);
					} else {
						$menu_item_class = $item_route;
					}
			}
			echo '<li>' . $this->htmlLink($item->getHref(), '', array('title'=>$this->translate($item->getLabel()), 'class'=>'topmenu_icon topmenu_icon_' . $menu_item_class)) . '</li>';
		}
	?>
	</ul>

	<div class="topmenu_right_side">
		<div class="topmenu_userinfo">
			<div style="float:left;"><?php echo $this->htmlLink($this->viewer->getHref(), $this->viewer->getTitle()) ?></div>
			<div class="userStatusContainer">
				<div class="userstatus_arrow"><a href="javascript://" class="user_status user_<?php echo $this->user_status['class']?>" id="btnChangeStatus" title="Change your status"><?php echo $this->translate('sep_userstatus_' . $this->user_status['status']); ?></a></div>
				<ul id="userStatusMenu" style="visibility:hidden; opacity:0;">
					<li><a rel="online" href="javascript://"><?php echo $this->translate('sep_userstatus_online')?></a></li>
					<li><a rel="away" href="javascript://"><?php echo $this->translate('sep_userstatus_away')?></a></li>
					<li><a rel="busy" href="javascript://"><?php echo $this->translate('sep_userstatus_busy')?></a></li>
					<li><a rel="out to lunch" href="javascript://"><?php echo $this->translate('sep_userstatus_out to lunch')?></a></li>
					<li><a rel="unavailable" href="javascript://"><?php echo $this->translate('sep_userstatus_unavailable')?></a></li>
					<li><a rel="invisible" href="javascript://"><?php echo $this->translate('sep_userstatus_invisible')?></a></li>
				</ul>
			</div>

			<div class="topmenu_user_links">
			<?php echo $this->htmlLink(array('route'=>'user_extended', 'controller'=>'edit', 'action'=>'profile'), $this->translate('edit my profile'), array('class' => 'topmenu_gray_link')) ?> &nbsp;&ndash;&nbsp;
			<?php echo $this->htmlLink(array('route'=>'user_logout'), $this->translate('sep_log out'), array('class' => 'topmenu_gray_link')) ?>
			</div>
		</div>
		<div class="topmenu_user_avatar"><?php echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon')) ?></div>
	</div>

<?php } else { ?>

	<div class="loginform_label"><?php echo $this->translate('nickname and password'); ?></div>
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
	
	<?php endif; ?>

<?php } ?>

</div>