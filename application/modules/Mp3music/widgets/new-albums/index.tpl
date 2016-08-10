<ul class="global_form_box" style="margin-bottom: 15px; overflow: hidden; background:none;">
<div class="mp3_top_album">
<?php
    $model = new Mp3music_Model_Album(array());
    $albums    = $model->getAlbums();
    $i = 0;
    foreach ($albums as $album):
    if(count($album->getSongs()) > 0):
    if($i < 8):
    $i ++;
    ?>
    <li class="mp3music_newsalbums">
         <div style="height: 180px;"> 
            <div class="mp3music_bgalbums" title="<?php echo strip_tags($album->title)  ?>">
                <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                 <?php echo $this->itemPhoto($album, 'thumb.normal'); ?>  
                </a>
            </div>
            <div class="mp3_album_title_link">
                <a title="<?php echo strip_tags($album->title)?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                <?php echo $this->string()->truncate($album->title,17);?>
                </a>
            </div>
            <div class="mp3_album_info">
                <?php echo $this->timestamp($album->creation_date) ?><br />   
                <?php echo $this->translate('Author: ');?><?php echo $album->getOwner(); ?>
            </div>
        </div>
    </li>  
    <?php endif; endif;  endforeach; ?> 
</div>  
 <?php if($i >= 8): ?>
      <div style="clear: both; float: right; padding-right: 7px; padding-top: 7px;">
        <?php echo $this->htmlLink($this->url(array('search'=>'browse_new_albums'), 'mp3music_browse_new_albums'),
        '&raquo; View more',
        array('class'=>'')); ?>   
    </div>
     <?php endif; ?>                                                        
</ul>