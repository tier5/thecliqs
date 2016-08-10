  <h2>
    <?php echo $this->listing->__toString();
          echo ' &#187; ';
          if($this->album->getTitle()!='') echo $this->album->getTitle();
          else echo 'Untitle Album';
    ?>
</h2>

<div class="ynlisting_listing_action">
  <?php echo $this->htmlLink(array('route' => 'ynlistings_extended', 'controller' => 'album', 'action' => 'list','subject' => $this->listing->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Back to Album List'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php if($this->canEdit) echo $this->htmlLink(array('route' => 'ynlistings_extended','controller' => 'album', 'action' => 'edit', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
    'class' => 'buttonlink icon_listings_edit smoothbox'
  )) ?>
  <?php if($this->canEdit  && $this->album->getTitle() !== 'Listing Profile') echo $this->htmlLink(array('route' => 'ynlistings_extended','controller' => 'album', 'action' => 'delete', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete Album'), array(
    'class' => 'buttonlink smoothbox icon_listings_delete smoothbox'
  )) ?>
  <?php if($this->canEdit)
      echo $this->htmlLink(array('route' => 'ynlistings_extended','controller' => 'photo', 'action' => 'upload', 'listing_id' => $this->listing->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
    'class' => 'buttonlink icon_listings_add_photos'
  )) ?>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <p><?php echo $this->album->description?></p>
  <br/>
  <ul class="thumbs">
    <?php foreach( $this->paginator as $photo ): ?>
     <li id='thumbs_nocaptions_<?php echo $photo->getIdentity()?>'>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
        </a>
         <?php if($this->listing->isOwner($this->viewer)):?>
        <?php endif;?>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
)); ?>
  <?php endif; ?>
  <br/>
  <?php echo $this->action("list", "comment", "core", array("type"=>"ynlistings_album", "id"=>$this->album->getIdentity())); ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded in this album yet.');?>
    </span>
  </div>
<?php endif; ?>
