<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php 
switch ($this->contest->contest_type) {
	case 'ynblog':
		$url = $this->url(array('action' => 'create', 'contest_id'=>$this->contest->contest_id), 'blog_general',true);
		break;
	case 'advalbum':
		$url = $this->url(array('action' => 'upload', 'contest_id'=>$this->contest->contest_id), 'album_general', true);
		break;
	case 'ynvideo':
		$url = $this->url(array('action' => 'create', 'contest_id'=>$this->contest->contest_id), 'video_general', true);
		break;
	case 'mp3music':
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name = ?', 'mp3music');		
		$mp3music = $table -> fetchRow($mselect);
		
		if($mp3music)
			$url = $this->url(array( 'contest_id'=>$this->contest->contest_id), 'mp3music_create_album', true);
		else 
			$url = $this->url(array('action' => 'create', 'contest_id'=>$this->contest->contest_id), 'music_general', true);
		
		break;
	case 'ynmusic':
		$url = $this->url(array('action' => 'upload', 'contest_id'=>$this->contest->contest_id), 'ynmusic_song', true);
		break;
	case 'ynultimatevideo':
		$url = $this->url(array('action' => 'create', 'contest_id'=>$this->contest->contest_id), 'ynultimatevideo_general', true);
		break;
	default:
		break;
}
?>

<script type="text/javascript">
	window.addEvent('domready', function()
		{
			window.tabContainerSwitch($$('.tab_layout_yncontest_submit_entry')[0]);
		}
	)

  // Populate data
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
  
  function removeFromToValue(id) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('height', '0');
    }

    $('item_name_submit_entry').disabled = false;
  }
  function loaddefault(toID,name)
      {
      	var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
		    en4.core.request.send(new Request.HTML({
		      'url' : url,
		      'data' : {
		        'format' : 'html', 
		        'contestId': <?php echo $this->contest->contest_id?>,  
		        'submit': true,        
		        'item_id' : toID,  
		        'page' :1,		   
		        'item_name': name     
		      },
			  'onRequest' : function(){	  	
					$('image_loading').style.display = '';
					$('contest_paginators').style.display = 'none';
			  },
			  'onSuccess' : function(responseJSON, responseText)
			  {	  	
					$('image_loading').style.display = 'none';
					$('contest_paginators').style.display = '';
			  }
		    }), {
		      'element' : $('wrap_contest').getParent()
		    });
      }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    if( !isPopulated ) { // NOT POPULATED
		var push =  new Autocompleter.Request.JSON('item_name_submit_entry', '<?php echo $this->url(array('action' => 'suggest', 'contest'=>$this->contest->contest_type), 'yncontest_myentries', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){
          if(token.type == 'user'){
            var choice = new Element('li', {
              'class': 'autocompleter-choices',
              'html': token.photo,
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          else {
            var choice = new Element('li', {
              'class': 'autocompleter-choices friendlist',
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
            
        },
        onPush : function(){
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('item_name').disabled = true;
          }
        }
      });
      
           

	  push. doPushSpan =  function(name, toID, newItem, hideLoc, list){	  		
          	loaddefault(toID,name);
        },
      
      new Composer.OverText($('item_name_submit_entry'), {
        'textOverride' : '<?php echo $this->translate('Choose item...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });

    } else { // POPULATED

      var myElement = new Element("span", {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title /* + ' <a href="javascript:void(0);" ' +
                  'onclick="this.parentNode.destroy();removeFromToValue("' + toID + '");">x</a>"' */
      });
      $('to-element').appendChild(myElement);
      $('to-wrapper').setStyle('height', 'auto');

      // Hide to input?
      $('item_name').setStyle('display', 'none');
      $('toValues-wrapper').setStyle('display', 'none');
    }
  });
  
</script>

<?php
	$this->headScript()
    	->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<ul class="generic_list_widget ynContest_ULWrapper" id = "wrap_contest">
	<?php if($this->contest->contest_type == 'ynblog'): ?>		  
		<div><?php echo $this->translate('If you do not have any items, please %1$screate new%2$s','<a  href="'.$url.'">', '</a>')?></div>
		
		<?php if($this->paginator->getTotalItemCount()):?>
			<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name"><?php echo $this->translate("Search a blog")?></label></div>
			<div class="form-element" id="item_name-element" >
				<div class="wrap_input_search">
					<input type="text" value="<?php echo $this->translate($this->item_name) ?>" id="item_name_submit_entry" name="item_name">
					<?php if(strlen($this->item_name)>0):?>
						<a onclick="loaddefault(0,'');"> <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/icons/cancel-16.png'?>" /></a>				
					<?php endif;?>
				</div>
			</div></div>
			<div class="form-wrapper" id="toValues-wrapper"><div class="form-label" id="toValues-label">&nbsp;</div>
			<div class="form-element" id="toValues-element">
			<input type="hidden" id="toValues" value="" name="toValues"></div></div>	
			<?php echo $this->partial('_list_entries_item.tpl','yncontest' , array(
				'paginator' => $this->paginator,
				'width' => $this->width,
				'height' => $this->height,
				'item' => $this->contest,
				'identity'	=> $this->identity,
				'items_per_page' => $this->items_per_page,				
				));  ?>
			<div id="form_item_import">
			<?php echo $this->form->render($this);?>
			</div>
		<?php else:?>
			<div class="tip">
		    <span>
		        <?php echo $this->translate('You do not have any items.'); ?>
		       
		    </span>
		</div>
		<?php endif;?>
	<?php endif;?>
	
	<?php if($this->contest->contest_type == 'advalbum'): ?>		  
		<div><?php echo $this->translate('If you do not have any items, please %1$screate new%2$s','<a href="'.$url.'">', '</a>')?></div>		
		<?php if(count($this->albums)>0):?>
			<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name"><?php echo $this->translate("Select album")?></label></div>
			<div class="yncontest_album_select">
				<select id="search_album" onchange="changeOrder_advalbum(this)">
					<?php foreach($this->albums as $album):?>
						<?php if($this->album_id == $album->getIdentity()):?>
							<option selected="selected" value="<?php echo $album->getIdentity();?>" label="<?php echo $album->getTitle();?>"><?php echo $album->getTitle();?></option>
						<?php else:?>
						<option value="<?php echo $album->getIdentity();?>" label="<?php echo $album->getTitle();?>"><?php echo $album->getTitle();?></option>
						<?php endif;?>						
					<?php endforeach;?> 
				</select>
			 </div>			
			<?php echo $this->partial('_list_entries_item.tpl','yncontest' , array(
					'paginator' => $this->paginator,
					'width' => $this->width,
					'height' => $this->height,
					'item' => $this->contest,
					'identity'	=> $this->identity,
					'items_per_page' => $this->items_per_page,
					'album_id' => $this->album_id
			));  ?>
			<div id="form_item_import">
				<?php echo $this->form->render($this);?>
			</div>
		<?php else:?>
			<div class="tip">
		    <span>
		        <?php echo $this->translate('You do not have any items.'); ?>		       
		    </span>
		</div>
	  	<?php endif; ?> 	   
	<?php endif;?>
  
  	<?php if ($this->contest->contest_type == 'ynvideo' ): ?>
  		<div><?php echo $this->translate('If you do not have any items, please %1$screate new%2$s','<a href="'.$url.'">', '</a>')?></div>		
		<?php if($this->paginator->getTotalItemCount()):?>
			
			<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name"><?php echo $this->translate("Search a video")?></label></div>
			<div class="form-element" id="item_name-element" >
				<div class="wrap_input_search">
					<input type="text" value="<?php echo $this->translate($this->item_name) ?>" id="item_name_submit_entry" name="item_name">
					<?php if(strlen($this->item_name)>0):?>
						<a onclick="loaddefault(0,'');"> <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/icons/cancel-16.png'?>" /></a>				
					<?php endif;?>
				</div>
			</div></div>
			
			<div class="form-wrapper" id="toValues-wrapper"><div class="form-label" id="toValues-label">&nbsp;</div>
			<div class="form-element" id="toValues-element">
			<input type="hidden" id="toValues" value="<?php echo $this->translate($this->item_name) ?>" name="toValues"></div></div>  	
	  		
			
			<?php echo $this->partial('_list_entries_item.tpl','yncontest' , array(
				'paginator' => $this->paginator,
				'width' => $this->width,
				'height' => $this->height,
				'item' => $this->contest,
				'identity'	=> $this->identity,
				'items_per_page' => $this->items_per_page,				
				));  ?>
			<div id="form_item_import">
			<?php echo $this->form->render($this);?>
			</div>
		<?php else:?>
			<div class="tip">
		    <span>
		        <?php echo $this->translate('You do not have any items.'); ?>		       
		    </span>
		</div>
	  	<?php endif; ?> 
	<?php endif; ?>    
	<?php if ($this->contest->contest_type == 'mp3music' ): ?>
  		<div><?php echo $this->translate('If you do not have any items, please %1$screate new%2$s','<a href="'.$url.'">', '</a>')?></div>		
		<?php if($this->paginator->getTotalItemCount()):?>
			
			<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name"><?php echo $this->translate("Search a music")?></label></div>
			<div class="form-element" id="item_name-element" >
				<div class="wrap_input_search">
					<input type="text" value="<?php echo $this->translate($this->item_name) ?>" id="item_name_submit_entry" name="item_name">
					<?php if(strlen($this->item_name)>0):?>
						<a onclick="loaddefault(0,'');"> <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/icons/cancel-16.png'?>" /></a>				
					<?php endif;?>
				</div>
			</div></div>
			
			<div class="form-wrapper" id="toValues-wrapper"><div class="form-label" id="toValues-label">&nbsp;</div>
			<div class="form-element" id="toValues-element">
			<input type="hidden" id="toValues" value="" name="toValues"></div></div>  	
	  		<?php echo $this->partial('_list_entries_item.tpl','yncontest' , array(
				'paginator' => $this->paginator,
				'width' => $this->width,
				'height' => $this->height,
				'item' => $this->contest,
				'identity'	=> $this->identity,
				'items_per_page' => $this->items_per_page,				
				));  ?>
	  		<div id="form_item_import">
				<?php echo $this->form->render($this);?>
			</div>
	  	<?php else:?>
			<div class="tip">
		    <span>
		        <?php echo $this->translate('You do not have any items.'); ?>		       
		    </span>
		</div>
	  	<?php endif; ?> 
	<?php endif; ?>    
<?php if ($this->contest->contest_type == 'ynmusic' ): ?>
	<div><?php echo $this->translate('If you do not have any items, please %1$screate new%2$s','<a href="'.$url.'">', '</a>')?></div>
	<?php if($this->paginator->getTotalItemCount()):?>
		<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name"><?php echo $this->translate("Search a music")?></label></div>
			<div class="form-element" id="item_name-element" >
				<div class="wrap_input_search">
					<input type="text" value="<?php echo $this->translate($this->item_name) ?>" id="item_name_submit_entry" name="item_name">
					<?php if(strlen($this->item_name)>0):?>
						<a onclick="loaddefault(0,'');"> <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/icons/cancel-16.png'?>" /></a>
					<?php endif;?>
				</div>
			</div>
		</div>
		<div class="form-wrapper" id="toValues-wrapper">
			<div class="form-label" id="toValues-label">&nbsp;</div>
			<div class="form-element" id="toValues-element">
				<input type="hidden" id="toValues" value="" name="toValues">
			</div>
		</div>
		<?php echo $this->partial('_list_entries_item.tpl','yncontest' , array(
			'paginator' => $this->paginator,
			'width' => $this->width,
			'height' => $this->height,
			'item' => $this->contest,
			'identity'	=> $this->identity,
			'items_per_page' => $this->items_per_page,
		)); ?>
		<div id="form_item_import">
			<?php echo $this->form->render($this);?>
		</div>
	<?php else:?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You do not have any items.'); ?>
			</span>
		</div>
	<?php endif; ?>
<?php endif; ?>
<?php if ($this->contest->contest_type == 'ynultimatevideo' ): ?>
	<?php $totalVideos = $this->paginator->getTotalItemCount(); ?>
	<?php if($totalVideos > 0): ?>
		<div><?php echo $this->translate('You have %1$s videos. You can also %2$supload new video%3$s', $totalVideos, '<a href="'.$url.'">', '</a>' )?></div>
		<br/>
		<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name"><?php echo $this->translate("Filter your videos")?></label></div>
			<div class="form-element" id="item_name-element" >
				<div class="wrap_input_search">
					<input type="text" value="<?php echo $this->translate($this->item_name) ?>" id="item_name_submit_entry" name="item_name">
					<?php if(strlen($this->item_name)>0):?>
					<a onclick="loaddefault(0,'');"> <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/images/icons/cancel-16.png'?>" /></a>
					<?php endif;?>
				</div>
			</div>
		</div>
		<div class="form-wrapper" id="toValues-wrapper">
			<div class="form-label" id="toValues-label">&nbsp;</div>
			<div class="form-element" id="toValues-element">
				<input type="hidden" id="toValues" value="" name="toValues">
			</div>
		</div>
		<?php echo $this->partial('_list_entries_item.tpl','yncontest' , array(
			'paginator' => $this->paginator,
			'width' => $this->width,
			'height' => $this->height,
			'item' => $this->contest,
			'identity'	=> $this->identity,
			'items_per_page' => $this->items_per_page,
		)); ?>
		<div id="form_item_import">
			<?php echo $this->form->render($this);?>
		</div>
	<?php else:?>
		<div><?php echo $this->translate('You do not have any videos, please %1$screate one%2$s.','<a href="'.$url.'">', '</a>')?></div>
	<?php endif; ?>
<?php endif; ?>
</ul>

