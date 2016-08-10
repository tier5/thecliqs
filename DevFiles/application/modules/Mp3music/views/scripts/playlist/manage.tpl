     <?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js')
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/core.js');   
       ?>

<h3><?php echo $this->translate("My Playlists") ?></h3>     
<div class='layout_middle'>

  <?php if (0 == count($this->paginator) ): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not created any playlist yet!') ?>
        <?php if (TRUE): // @todo check if user is allowed to create a music ?>
        <?php echo $this->htmlLink(array('route'=>'mp3music_create_playlist'), $this->translate('Click here to create one.')) ?>
        <?php endif; ?>
      </span>
    </div><!-- one more ending div for 'layout_middle' --></div>
  <?php return; endif; ?>
  <div style="padding-bottom:15px;"> 
  <?php 
  $user = Engine_Api::_()->user()->getViewer();
  $cout_playlist = Mp3music_Model_Playlist::getCountPlaylists($user); 
  $cout_song = Mp3music_Model_Playlist::getCountSongs($user); 
  $str_song = $this->translate('songs');
  $str_playlist = $this->translate('playlists');
  if($cout_song == 1)
    $str_song = $this->translate('song');
  if($cout_playlist == 1)
    $str_playlist = $this->translate('playlist');
  ?>
  <h5><?php echo $this->translate("You have ").$cout_song." ".$str_song.$this->translate(" in ").$cout_playlist." ".$str_playlist; ?> </h5>
  </div>
  <div style="border-top: 1px solid #EAEAEA; padding-bottom: 15px;"></div>     

  <ul class="mp3music_browse music_browse">
    <?php foreach ($this->paginator as $playlist): ?>

    <li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
      <div class="mp3music_browse_options music_browse_options">
        <?php if ($playlist->isDeletable() || $playlist->isEditable()): ?>
        <ul>
          <?php if ($playlist->isEditable()): ?>
          <li>
            <?php echo $this->htmlLink($playlist->getEditHref(),
              $this->translate('Edit'),
              array('class'=>'buttonlink icon_mp3music_edit'
              )) ?>
          </li>
          <?php endif; ?>
          <?php if ($playlist->isDeletable()): ?>
          <li>
            <?php echo $this->htmlLink($playlist->getDeleteHref(),
              $this->translate('Delete'),
              array('class'=>'buttonlink smoothbox icon_mp3music_delete'
            )) ?>
          </li>
          <?php endif; ?>
          <li>
            <?php echo $this->htmlLink($this->url(array('playlist_id'=>$playlist->playlist_id,'action'=>'set-profile-playlist','controller'=>'playlist','module'=>'mp3music'), 'default'),
              $playlist->profile ? $this->translate('Disable Profile Playlist') : $this->translate('Play on my Profile'),
              array('class'=>'buttonlink icon_music_playonprofile music_set_profile_playlist'
            )) ?>
          </li>
        </ul>
        <?php endif; ?>
      </div>
      <div class="mp3music_browse_info music_browse_info">
         <div style="float: left; padding-right: 10px;">
       <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('playlist_id'=>$playlist->playlist_id), 'mp3music_playlist');?>',500,565)">
       <?php echo $this->itemPhoto($playlist->getOwner(), 'thumb.profile')?> </a> 
       </a> 
       </div>
        <div class="mp3music_browse_info_title">
          <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('playlist_id'=>$playlist->playlist_id), 'mp3music_playlist');?>',500,565)"><?php echo  $playlist->getTitle() ?> </a>
        </div>
        <div class="mp3music_browse_info_date">
          <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
          <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
          -
         <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('playlist_id'=>$playlist->playlist_id), 'mp3music_playlist');?>',500,565)"> <?php echo $this->translate(array('%s comment', '%s comments', $playlist->getCommentCount()),$this->locale()->toNumber($playlist->getCommentCount())); ?> </a> 
        </div>
        <div class="mp3music_browse_info_desc">
          <?php echo $playlist->description ?>
           <?php if(!$playlist->getSongIDFirst()):?>
            <div class="tip" style="clear: none; padding-top: 10px;">
              <span>
                <?php echo $this->translate('There are no songs uploaded yet.') ?>
                <?php if ($playlist->isEditable()): ?>
                  <?php echo $this->htmlLink(array('route'=>'mp3music_browse'), $this->translate('Why don\'t you add some?')) ?>
                <?php endif; ?>
              </span>
            </div>
            <?php endif; ?>
        </div>
      </div>
    </li>

    <?php endforeach; ?>
  </ul>
  <div class='mp3browse_nextlast'>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

</div>
