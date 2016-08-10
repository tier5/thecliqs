<?php
  $id_prefix = '';
  if (!empty($this->id_prefix)){
    $id_prefix = $this->id_prefix;
  }
?>

<?php foreach ($this->paginator as $key => $photo):?>

  <?php

    if (isset($this->photos[$key])){
      $photo = $this->photos[$key];
    }
    $owner = $photo->getOwner();
  ?>

  <li id="<?php echo $id_prefix;?>photo_<?php echo $photo->getGuid();?>" style="visibility: hidden;"> <!--  visibility: hidden; waiting rebuild -->
    <div class="photo">
      <a href="<?php echo $photo->getHref();?>" class="aimg"><img src="<?php echo $photo->getPhotoUrl();?>" class="img" alt="" id="<?php echo $id_prefix;?>img_<?php echo $photo->getGuid();?>"/></a>
    </div>
    <div class="caption">
      <div class="content">
        <?php echo $this->translate('by');?>&nbsp;<a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a>
      </div>
    </div>
    <div class="hover-caption">
      <div class="content">
        <div class="title">

          <?php
            $title = $photo->getTitle();
            if (empty($title)){
              if (method_exists($photo, 'getAlbum')){
                $title = $photo->getAlbum()->getTitle();
              } else if (method_exists($photo, 'getCollection')){
                $title = $photo->getCollection()->getTitle();
              }
            }
          ?>

          <?php echo $title;?><br />
          <?php echo $this->translate('by');?>&nbsp;<a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a>
        </div>
        <div class="info">
          <span class="comment-count"><i class="icon-thumbs-up"></i> <?php echo $photo->likes()->getLikeCount();?></span>
          <span class="like-count"><i class="icon-comment"></i> <?php echo $photo->comments()->getCommentCount();?></span>
        </div>
      </div>
    </div>
  </li>
<?php endforeach;?>