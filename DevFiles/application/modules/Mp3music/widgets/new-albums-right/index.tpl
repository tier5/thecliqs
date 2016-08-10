<?php
$this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');
      
?>
<ul class="global_form_box" style="margin-bottom: 15px;">
<div class="mp3_album_widgets"> 
<?php 
        $index = 0;
        foreach ($this->paginatorNewMusic as $album): 
        if($album->getSongIDFirst()): $index++ ;?>
        <li class="mp3_title_link">
          <div class="mp3_image_album" title="<?php echo strip_tags($album->title) ?>"> 
                <a onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id,'song_id'=>$album->getSongIDFirst(),'popout'=>true), 'mp3music_album');?>',500,890)">   
               <?php echo $this->itemPhoto($album, 'thumb.icon'); ?>
               </a>
          </div>
          <div class="mp3_title_album_right" >    
                 <a title="<?php echo strip_tags($album->title); ?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,890)">
                 	<?php echo $this->string()->truncate($album->title,10);?></a><br/>
                  <div class="mp3_album_info_right">  
                  <?php echo $this->timestamp($album->creation_date) ?><br /> 
                  <span title="<?php echo $album->getOwner()->getTitle()?>">      
                  <?php echo $this->translate('Author: ');?>
                  <a  href="<?php echo $album->getOwner()->getHref()?>" class ='title_thongtin3'>
                  	<?php echo $this->string()->truncate($album->getOwner()->getTitle(), 10);?></a>
                   
                  </span>
                  </div>
          </div>
          </li>
         <?php endif; endforeach; ?>    
           
        <?php if($index >= $this->limit): ?>
        
        <li class="mp3_link_more" style="padding-top: 10px;"> 
        <?php echo $this->htmlLink($this->url(array('search'=>'browse_new_albums'), 'mp3music_browse_new_albums'),
                     $this->translate('&raquo; View more'),
                    array('class'=>'')); ?>
        </li>   
        <?php endif; ?>
</div>
 </ul>

