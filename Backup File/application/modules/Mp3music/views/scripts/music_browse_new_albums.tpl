<?php 
if(!function_exists('renderMusicPagination'))
{
    function renderMusicPagination($html)
    {
        $posStartHref = strpos($html,"<a href='#'>");
        if ($posStartHref<=0)
            return $html;
        $posEndHref = strpos($html,"</a>",$posStartHref);
        $page = trim(substr($html,$posStartHref+12,$posEndHref-$posStartHref-4));
        $pagenumber = (int)$page;
        if ( ($pagenumber)<0)
            return $html;
        $html = str_replace("href='#'","href='mp3-music/browse/browse_new_albums/".$pagenumber."'",$html);
        return $html;
    }
}
?>
    <?php
	$this->headScript()
		 ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');  
	?>	
	<h3><?php echo $this->translate('All Albums'); ?> 
        </h3>   
<ul class="global_form_box" style="background: none; overflow: auto;">   
<div class="mp3_browse_album">  
        <?php $albums    =  $this->browse->albumPaginator;
        foreach ($albums as $album):?>
           <?php if(count($album->getSongs()) > 0):?>
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
                    <?php echo $this->translate('Created: %s',$this->timestamp($album->creation_date)) ?>
                    <div style="padding-top: 10px;"> 
                        <?php echo $album->description ?>
                    </div>
                </div>
            </div>
        </li>  
         <?php endif;  endforeach;  ?>    
</div>                       
    <span style="float:right ;"> <?php echo renderMusicPagination($this->paginationControl($this->browse->albumPaginator, null, null, array(
        'pageAsQuery' => false,
        'query' => array("search"=>$this->browse->params['search'], "title"=>$this->browse->params['title']))))?>  </span> 
    <?php if (0 == count($albums) ): ?>
                <div class="tip" style="padding-left: 20px;">
                <span>
                    <?php echo $this->translate('Nobody has uploaded an album yet.') ?> 
                </span> 
                </div>
                <?php endif;  ?> 
</ul>
