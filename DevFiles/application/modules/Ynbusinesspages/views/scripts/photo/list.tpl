<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">
		<h2>  
		  <?php echo $this->business->__toString()." ".$this->translate("&#187; Photos") ?>
		</h2>
	</div>
</div>
</div>

<div class='generic_layout_container layout_middle ynbusinesspages_photo_list_container'>
	
	<div class="ynbusinesspages-profile-module-header">
        <!-- Menu Bar -->
        <div class="ynbusinesspages-profile-header-right">
			<?php 
		  	$session = new Zend_Session_Namespace('mobile');
			if($session -> mobile)
			  	echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity()), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
			    'class' => 'buttonlink'
			  ));
			else {
				echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
			    'class' => 'buttonlink'
			  ));
			} ?>

		  	<?php if( $this->canUpload ): ?>
			    <?php echo $this->htmlLink(array(
			        'route' => 'ynbusinesspages_extended',
			        'controller' => 'photo',
			        'action' => 'upload',
			        'subject' => $this->subject()->getGuid(),
			        'tab' => $this -> tab,
			      ), '<i class="fa fa-plus-square"></i>'.$this->translate('Upload Photos'), array(
			        'class' => 'buttonlink'
			    )) ?>
			 <?php endif; ?>
         </div>   
    </div> 

  	<ul class="thumbs thumbs_nocaptions">
    <?php 
    $thumb_photo = 'thumb.normal';
		if(defined('YNRESPONSIVE'))
		{
			$thumb_photo = 'thumb.profile';
		}
	    foreach( $this->paginator as $photo ): ?>
	      <li id='thumbs_nocaptions_<?php echo $photo->getIdentity()?>'>
	        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
	          <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
	        </a>
	        <p id="nocaptions_photo_<?php echo $photo->getIdentity()?>">
	        	<?php if($this->business->isAllowed('album_delete', null, $photo)) :?>
	        	<a id="nocaptions_photo_remofile_<?php echo $photo->getIdentity()?>" class="buttonlink" onclick="return removeFile(<?php echo $photo->getIdentity()?>)" href="javascript:void();" ><i class="fa fa-times"></i> <?php echo $this->translate("Remove")?></a>
	       		<?php endif;?>
	        </p>

	      </li>
	    <?php endforeach;?>
	</ul>

	<?php if( $this->paginator->count() > 0 ): ?>
    	<br />
    	<?php echo $this->paginationControl($this->paginator); ?>
  	<?php endif; ?>
</div>

<script type="text/javascript">
   	function removeFile(photo_id)
   	{
   		var action = confirm(en4.core.language.translate('Are you sure you want to delete this photo?'));
   		
   		if(action)
   		{
   			request = new Request.JSON({
				'format' : 'json',
	            'url' :  en4.core.baseUrl + 'pages/photo/delete-photo',
	            'data': {
	            	'photo_id' : photo_id,
	            	'business_id' : <?php echo $this->business->getIdentity();?>
	            },
	            'onSuccess' : function(responseJSON) {
	            	
	            }
			});
	        request.send();
	        
	        $('thumbs_nocaptions_'+ photo_id).dispose();
   		}
		
		return false;
   	}  
	
</script>