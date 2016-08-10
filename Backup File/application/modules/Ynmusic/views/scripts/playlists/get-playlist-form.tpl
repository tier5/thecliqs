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
	$playlistViewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_playlist', $viewer, 'auth_view');
    $playlistCommentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_playlist', $viewer, 'auth_comment');
	  
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

<!-- If Type is album -->
<?php if($item -> getType() == "ynmusic_album") :?>
	<?php 
	$viewer = Engine_Api::_()->user()->getViewer();
	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max = $permissionsTable->getAllowed('ynmusic_playlist', $viewer->level_id, 'max_songs');
    if ($max == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynmusic_playlist')
            ->where('name = ?', 'max_songs'));
        if ($row) {
            $max = $row->value;
        }
    }
	?>
	<span><?php echo $this -> translate('Add Songs');?></span>
	<?php if ($max) :?>
	<div class="description"><?php echo $this -> translate('You can add %s song(s)', $max);?></div>	
	<?php endif;?>
	<ul class="list-add-songs-dropdown">
	<?php 
		$songIds = array();
		$songs = $item -> getAvailableSongs();
		foreach($songs as $song):
		$songIds[] = $song -> getIdentity();
	?>
		<li>
	  	<?php echo $song;?> <span class="remove-song" data="<?php echo $song -> getIdentity();?>" title="<?php echo $this -> translate('Remove');?>"><i class="fa fa-times"></i></span>
		</li>
	<?php endforeach;?>
	</ul>
	<input type="hidden" id="song_ids" value="<?php echo implode(",",$songIds);;?>" />
	<script type="text/javascript">
		$$('.remove-song').addEvent('click', function(){
			var id = this.get('data');
			
			var songIds = document.getElementById('song_ids').value;
		    var songIdsArray = songIds.split(",");
			
			var index = songIdsArray.indexOf(id);
			if(index > -1)
			{
				songIdsArray.splice(index, 1);
				$('song_ids').set('value', songIdsArray.toString());
			}
			this.getParent().destroy();
		});
	</script>
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
				parent.removeClass('ynmusic_active_add_playlist');
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
				<?php if($item -> getType() == "ynmusic_album") :?>
					var song_ids = $('song_ids').get('value');
					<?php if ($max) :?>
					var song_arr = song_ids.split(',');
					if (song_arr.length > <?php echo $max?>) {
						var errorDIV = new Element('div', {
							'class': 'create-playlist-error',
						    html: '<?php echo $this->translate('You can add only %s song(s). Please remove some for continue.', $max)?>',
						    styles: {
						        'color': 'red',
						        'font-size': 'bold'
						    },
						});
						$('playlist_title').grab(errorDIV, 'before');
						return;
					}
 					<?php endif;?>
				<?php else :?>
					var song_ids = '<?php echo $item -> getIdentity();?>';
				<?php endif;?>
				var url = '<?php echo $this->url(array('action'=>'create-playlist'), 'ynmusic_playlist')?>';
		        new Request.JSON({
		            'url': url,
		            'method': 'post',
		            'data' : {
		                'title' : title,
		                'auth_view' : auth_view,
		                'auth_comment' : auth_comment,
		                'song_ids' : song_ids,
		            },
		            'onSuccess': function(responseJSON) {
		                if(responseJSON.json == 'true') {
		                	var span = $$('.play_list_span.ynmusic_active_add_playlist')[0];
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
				            		span.removeClass('ynmusic_active_add_playlist');
				            		span.hide();
				            	});
				        	}).delay(3000, notice);
		                }
		            }
		        }).send();
			});
		}
	});
</script>
