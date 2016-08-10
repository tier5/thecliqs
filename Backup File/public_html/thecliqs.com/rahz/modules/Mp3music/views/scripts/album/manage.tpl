  <?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');
       ?>

<h3><?php echo $this->translate("My Albums") ?></h3>
<div class='layout_middle'>
  <?php if (0 == count($this->albumPaginator) ): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no music uploaded yet.') ?>
        <?php if (TRUE): // @todo check if user is allowed to create a music ?>
        <?php echo $this->htmlLink(array('route'=>'mp3music_create_album'), $this->translate('Why don\'t you add some?')) ?>
        <?php endif; ?>
      </span>
    </div><!-- one more ending div for 'layout_middle' --></div>
  <?php return; endif; ?>
  <div style="padding-bottom:15px;">
  <?php 
  $user = Engine_Api::_()->user()->getViewer();
  $cout_album = Mp3music_Model_Album::getCountAlbums($user); 
  $cout_song = Mp3music_Model_Album::getCountSongs($user); 
  $str_song = $this->translate('songs');
  $str_album = $this->translate('albums');
  if($cout_song == 1)
    $str_song = $this->translate('song');
  if($cout_album == 1)
    $str_album = $this->translate('album');
  ?>
  <h5><?php echo $this->translate("You have ").$cout_song. " ".$str_song.$this->translate(" in ").$cout_album. " " .$str_album; ?> </h5>
  </div>
  <div style="border-top: 1px solid #EAEAEA; padding-bottom: 15px;"></div>
  <ul class="mp3music_browse music_browse">
    <?php foreach ($this->albumPaginator as $album): ?>
    <li id="mp3music_album_item_<?php echo $album->getIdentity() ?>">
      <div class="mp3music_browse_options music_browse_options">
        <?php if ($album->isDeletable() || $album->isEditable()): ?>
        <ul>
          <?php if ($album->isEditable()): ?>
          <li>
            <?php echo $this->htmlLink($album->getEditHref(),
              $this->translate('Edit'),
              array('class'=>'buttonlink icon_mp3music_edit'
              )) ?>
          </li>
          <?php endif; ?>
          <?php if ($album->isDeletable()): ?>
          <li>
            <?php echo $this->htmlLink($album->getDeleteHref(),
              $this->translate('Delete'),
              array('class'=>'buttonlink smoothbox icon_mp3music_delete'
            )) ?>
          </li>
          <?php endif; ?>
        </ul>
        <?php endif; ?>
      </div>
      <div class="mp3music_browse_info music_browse_info">
       <div style="float: left; padding-right: 10px;">
       <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)">
       <?php echo $this->itemPhoto($album, 'thumb.normal')?> </a> 
       </div>
        <div class="mp3music_browse_info_title">
        <?php if($album->getSongIDFirst($album->album_id)): ?>
          <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)"><?php echo $album->getTitle() ?></a>
         <?php else: ?>
         <?php echo $album->getTitle() ?>
         <?php endif; ?>
        </div>
        <div class="mp3music_browse_info_date">
          <?php echo $this->translate('Created %s by ', $this->timestamp($album->creation_date)) ?>
          <?php echo $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle()) ?>
          -
           <?php if($album->getSongIDFirst($album->album_id)): ?>  
          <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)"> <?php echo $this->translate(array('%s comment', '%s comments', $album->getCommentCount()),$this->locale()->toNumber($album->getCommentCount())); ?> </a> 
           <?php else: ?>   
         <?php echo $this->translate(array('%s comment', '%s comments', $album->getCommentCount()),$this->locale()->toNumber($album->getCommentCount())); ?>
         <?php endif; ?> 
        </div>
        <div class="mp3music_browse_info_desc">
          <?php echo $album->description ?>
          <?php if(!$album->getSongIDFirst($album->album_id)):?>
            <div class="tip" style="clear: none; padding-top: 10px;">
              <span>
                <?php echo $this->translate('There are no songs uploaded yet.') ?>
                <?php if ($album->isEditable()): ?>
                  <?php echo $this->htmlLink(array('route'=>'mp3music_edit_album','album_id'=>$album->album_id), $this->translate('Why don\'t you add some?')) ?>
                <?php endif; ?>
              </span>
            </div>
            <?php endif; ?>
        </div> 
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
  <div class='browse_nextlast'>
    <?php echo $this->paginationControl($this->albumPaginator); ?>
  </div>
</div>
