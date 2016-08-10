<div id="ynmusic-history-listing" class="music-listing">
<?php if ($this->paginator->getTotalItemCount() > 0) :?>	

<div class="ynmusic-block-count-mode-view not-mode-view">
	<div id="ynmusic-total-item-count">
		<?php echo $this->translate(array('ynmusic_music_count_num_ucf', '%s Results', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?>
	</div>
</div>

	<div class="ynmusic-listing-content">
		<ul class="history-items music-items">
		<?php foreach ($this->paginator as $history) : ?>
			<?php $item = Engine_Api::_()->getItem('ynmusic_'.$history['item_type'], $history['item_id']);?>
			<?php if ($item) :?>
			<?php 
			$class = '';
			if (in_array($item->getType(), array('ynmusic_album', 'ynmusic_playlist'))) {
				if (!$item->getCountSongs() || !$item->isViewable()) $class = 'clearfix';
			}
			?>
				
			<li class="history-item music-item <?php echo $history['item_type']?>-item <?php echo $class;?>" id="<?php echo $item->getGuid()?>">
				<?php echo $this->partial('_'.$history['item_type'].'_view.tpl', 'ynmusic', array('item' => $item, 'history' => $history['history_id']));?>
			</li>
			<?php endif;?>
		<?php endforeach;?>
		</ul>
		<div>
	    <?php echo $this->paginationControl($this->paginator, null, null, array(
	        'pageAsQuery' => true,
	        'query' => $this->formValues,
	    )); ?>
		</div>

		<div style="padding-bottom:5px;">
		    <button type='button' onclick="removeSelected()"><?php echo $this->translate('Remove Selected From History') ?></button>
			<?php echo $this->htmlLink(array('action'=>'removeall','route'=>'ynmusic_history'), '<button>'.$this->translate('Clear History').'</button>', array('class'=>'smoothbox'))?>
		</div>
	</div>
<?php else:?>
	<div class="tip">
		<span><?php echo $this->translate('There are no history.')?></span>
	</div>
<?php endif;?>
</div>

<script type="text/javascript">
	en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
	en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});
	
	function addNewPlaylist(ele, guid) {
		var nextEle = ele.getNext();
		if(nextEle.hasClass("ynmusic_active_add_playlist")) {
			//click to close
			nextEle.removeClass("ynmusic_active_add_playlist");
			nextEle.setStyle("display", "none");
		} else {
			//click to open
			nextEle.addClass("ynmusic_active_add_playlist");
			nextEle.setStyle("display", "block");
		}
		$$('.play_list_span').each(function(el){
			if(el === nextEle){
				//do not empty the current box
			} else {
				el.empty();
				el.setStyle("display", "none");
				el.removeClass("ynmusic_active_add_playlist");
			}
		});
		var data = guid;
		var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynmusic_playlist', true);?>';
		var request = new Request.HTML({
	        url : url,
	        data : {
	        	subject: data,
	        },
	        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
	            var spanEle = nextEle;
	            spanEle.innerHTML = responseHTML;
	            eval(responseJavaScript);
	            
	            var popup = spanEle.getParent('.action-pop-up');
	            var layout_parent = popup.getParent('.layout_middle');
		    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
		    	var y_position = popup.getPosition(layout_parent).y;
				var p_height = layout_parent.getHeight();
				var c_height = popup.getHeight();
	    		if(p_height - y_position < (c_height + 21)) {
	    			layout_parent.addClass('popup-padding-bottom');
	    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
	    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
				}
	        }
	    });
	    request.send();
	}
	
	function addToPlaylist(ele, playlistId, guild) {
		var checked = ele.get('checked');
		var data = guild;
		var url = '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynmusic_playlist', true);?>';
		var request = new Request.JSON({
	        url : url,
	        data : {
	        	subject: data,
	        	playlist_id: playlistId,
	        	checked: checked,
	        },
	        onSuccess: function(responseJSON) {
	        	if (!responseJSON.status) {
	        		ele.set('checked', !checked);
	        	}
	            var div = ele.getParent('.action-pop-up');
	            var notices = div.getElement('.add-to-playlist-notices');
	            var notice = new Element('div', {
	            	'class' : 'add-to-playlist-notice',
	            	text : responseJSON.message
	            });
	            notices.adopt(notice);
	            notice.fade('in');
	            (function() {
	            	notice.fade('out').get('tween').chain(function() {
	            		notice.destroy();
	            	});
	        	}).delay(3000, notice);
	        }
	    });
	    request.send();
	}
	
	function removeSelected(){
	    var checkboxes = $$('input.remove-history-checkbox[type=checkbox]:checked');
	    selecteditems = [];
	    checkboxes.each(function(item){
	        var value = item.value;
	        selecteditems.push(value);
	    });
	   	var url = '<?php echo $this->url(array('action'=>'multiremove'), 'ynmusic_history', true) ?>/ids/'+selecteditems.join(',');
	    Smoothbox.open(url);
	}
</script>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('.ynmusic_main_manage_albums').getParent().addClass('active');
	});
</script>