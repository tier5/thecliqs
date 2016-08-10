<?php foreach( $this->paginator as $key => $album ): ?>

  <?php

    if (isset($this->albums[$key])){
      $album = $this->albums[$key];
    }
    $owner = $album->getOwner();
  ?>

  <li>
    <div class="thumbs_photo">
      <a class="" href="<?php echo $album->getHref(); ?>">
        <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
      </a>
      <div class="caption">
        <div class="content">
        </div>
      </div>
      <div class="hover-caption">
        <div class="content">
          <div class="title">
            <?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
          </div>
          <div class="info">
            <div class="photo-count">
              <i class="icon-picture"></i>
              <?php echo $album->count();?>
            </div>
          </div>
        </div>
    </div>

    </div>

    <span class="thumbs_title">
      <?php echo $this->htmlLink($album, $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10)) ?>
    </span>
  </li>
<?php endforeach;?>