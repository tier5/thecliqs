    <?php
	$this->headScript()
		 ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js'); 
	?>	
<h3><?php echo $this->translate('Browse Albums'); ?>
         </h3> 
<ul class="global_form_box" style="background: none; overflow: auto;">   
<div class="mp3_browse_album">  
        <?php $albums    =  $this->browse->albumPaginator;
        $i = 0;
         foreach ($albums as $album):
         if(count($album->getSongs()) > 0):
          $i ++;
            ?>
        <li class="mp3music_browsealbums" style="float: none; overflow: hidden;">
           <div class="mp3music_bgalbums" style="float: left" title="<?php echo $album->title;  ?>">
               <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                <?php echo $this->itemPhoto($album, 'thumb.normal'); ?>  
                </a>
            </div>
             <div class="mp3_album_des">
                <div class="mp3_title_link">
                    <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                    <?php echo strlen($album->title)>20?substr($album->title,0,17).'...':$album->title;?>
                    </a>
                </div>
                <div class="mp3_album_info" style="width: 380px;">
                    <?php echo $this->translate('Author: ');?><?php echo $album->getOwner() ?> <br/>
                    <?php echo $this->translate('Listens: %s - ', $album->play_count); ?>
                    <?php echo $this->translate(array('%s Comment', '%s Comments', $album->getCommentCount()),$this->locale()->toNumber($album->getCommentCount())); ?>
                    <div style="padding-top: 10px;"> 
                        <?php echo $album->description ?>
                    </div>
                </div>
            </div>
        </li>  
    <?php endif;  endforeach; ?>  
</div>
    <span style="float:right ;"> <?php echo $this->paginationControl($this->browse->albumPaginator, null, null, array(
        'pageAsQuery' => false,
        'query' => array("search"=>$this->browse->params['search'], "title"=>$this->browse->params['title'])))?>  </span> 
   <?php if (0 == count($albums) ): ?>
                <div class="tip" style="padding-left: 20px;">
                <span>
                    <?php echo $this->translate('Nobody has uploaded an album with that criteria.') ?> 
                </span> 
                </div>
                <?php endif;  ?>  
</ul>
