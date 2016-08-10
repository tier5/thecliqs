<?php $item = $this -> item;?>
<div class="play_list_span_input">
	<input type="text" id="playlist_title">
</div>
<?php
  	$availableLabels = array(
		'everyone' => 'Everyone', 
		'registered' => 'All Registered Members', 
		'owner_network' => 'Friends and Networks', 
		'owner_member_member' => 'Friends of Friends', 
		'owner_member' => 'Friends Only', 
		'owner' => 'Just Me'
	);
	$viewer = $this -> viewer();
	$playlistViewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynultimatevideo_playlist', $viewer, 'auth_view');
    $playlistCommentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynultimatevideo_playlist', $viewer, 'auth_comment');
	  
    $playlistViewOptions = array_intersect_key($availableLabels, array_flip($playlistViewOptions));
    $playlistCommentOptions = array_intersect_key($availableLabels, array_flip($playlistCommentOptions));
?>	
<!-- View Playlist -->
<?php if( !empty($playlistViewOptions) && count($playlistViewOptions) >= 1 ) :?>
	  <!--  Make a hidden field -->
      	<input type="hidden" id="auth_view" name="auth_view" value="<?php echo key($playlistViewOptions);?>">
<?php endif;?>

<!-- Comment Playlist -->
<?php if( !empty($playlistCommentOptions) && count($playlistCommentOptions) >= 1 ) :?>
	  <!--  Make a hidden field -->
      	<input type="hidden" id="auth_comment" name="auth_comment" value="<?php echo key($playlistCommentOptions);?>">
<?php endif;?>

<button type="button" id="play_list_create"><?php echo $this -> translate("Create Playlist");?></button>
<a id="play_list_cancel" type="button" href="javascript:void(0);"><?php echo $this -> translate('Cancel');?></a>

<script type="text/javascript">
	window.addEvent('domready', function(){
		
		if($('play_list_cancel')) {
			$('play_list_cancel').addEvent('click', function (){
				var parent = this.getParent('.play_list_span');
				parent.innerHTML = "";
				parent.hide();
				parent.removeClass('ynultimatevideo_active_add_playlist');
				return false;
			});
		}
		
		if($('play_list_create'))
		{
			$('play_list_create').addEvent('click', function(){
				
				var title = $('playlist_title').get('value');
				title = title.trim();
				$$('.create-playlist-error').destroy();
				if(title == ""){
					var errorDIV = new Element('div', {
						'class': 'create-playlist-error',
					    html: '<?php echo $this->translate('Please input the playist title!')?>',
					    styles: {
					        'color': 'red',
					        'font-size': 'bold'
					    },
					});
					$('playlist_title').grab(errorDIV, 'before');
					return;
				}
				if($('auth_view'))
					var auth_view = $('auth_view').get('value');
				else
					var auth_view = $$("input[type=radio][name=auth_view]:checked").get('value')[0];
				if($('auth_comment'))
					var auth_comment = $('auth_comment').get('value');
				else
					var auth_comment = $$("input[type=radio][name=auth_comment]:checked").get('value')[0];	
				var video_id = '<?php echo $item -> getIdentity();?>';
				var url = '<?php echo $this->url(array('action'=>'create-playlist'), 'ynultimatevideo_playlist')?>';
		        new Request.JSON({
		            'url': url,
		            'method': 'post',
		            'data' : {
		                'title' : title,
		                'auth_view' : auth_view,
		                'auth_comment' : auth_comment,
		                'video_id' : video_id,
		            },
		            'onSuccess': function(responseJSON) {
		                if(responseJSON.json == 'true') {
		                	var span = $$('.play_list_span.ynultimatevideo_active_add_playlist')[0];
		                	var notice = new Element('div', {
				            	'class' : 'add-to-playlist-notice',
				            	text : '<?php echo $this->translate('Create new playlist successfully.')?>'
				            });
				            span.empty();
				            span.grab(notice, 'top');
				            notice.fade('in');
				            (function() {
				            	notice.fade('out').get('tween').chain(function() {
				            		notice.destroy();
				            		span.removeClass('ynultimatevideo_active_add_playlist');
				            		span.hide();
				            	});
				        	}).delay(2500, notice);
		                }
		            }
		        }).send();
			});
		}
	});
</script>
