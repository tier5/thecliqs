<h3><?php echo $this->translate("Cover photos");?></h3>
<div class="ynbusinesspages-cover-layout">
<?php $quota = $this->quota; ?>
<p><?php echo $this->translate(array("You can add maximum %s cover photo", "You can add maximum %s cover photos", $quota), $this->locale()->toNumber($quota));?></p>

<?php if( $this->mine || $this->canEdit ): ?>
  <script type="text/javascript">
    var SortablesInstance;

    en4.core.runonce.add(function() {
      $$('.thumbs_nocaptions > li').addClass('sortable');
      SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
        clone: true,
        constrain: true,
        //handle: 'span',
        onComplete: function(e) {
          var ids = [];
          $$('.thumbs_nocaptions > li').each(function(el) {
            ids.push(el.get('id').match(/\d+/)[0]);
          });
          //console.log(ids);

          // Send request
          var url = '<?php echo $this->url(array('action' => 'order-cover')) ?>';
          var request = new Request.JSON({
            'url' : url,
            'data' : {
              format : 'json',
              business_id : '<?php echo $this->businessId?>', 
              order : ids
            }
          });
          request.send();
        }
      });
    });
  </script>
<?php endif ?>
<?php if ($this->canUpload):?>
	<?php if( $this->mine || $this->canEdit ): ?>
	  <div class="album_options">
	    <?php echo $this->form->render($this) ?>
		 <script type="text/javascript">
		    //<!--
		    en4.core.runonce.add(function() {
		      var addMoreFile = window.addMoreFile = function () 
		      {
		        var fileElement = new Element('input', {
		          'type': 'file',
		          'name': 'photos[]',
		          'multiple': "multiple"
		        });
		        fileElement.inject($('photos-element'));
		      }
		    });
		    // -->
		  </script>
	  </div>
	<?php endif;?>
<?php endif;?>

  <ul class="thumbs thumbs_nocaptions">
    <?php foreach( $this->covers as $photo ): ?>
      <li id="thumbs-photo-<?php echo $photo->photo_id ?>">

        <a class="thumbs_photo">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.main'); ?>);"></span>
        </a>

      	<?php 
      	echo $this->htmlLink(array('route' => 'ynbusinesspages_dashboard', 'action' => 'delete-cover', 'business_id' => $this->businessId, 'cover_id' => $photo->cover_id),
      		$this->translate("<i class='fa fa-times'></i>"), array('class' => 'smoothbox ynbusinesspages-cover-delete') );
      	?>
      </li>
    <?php endforeach;?>
  </ul>
</div>