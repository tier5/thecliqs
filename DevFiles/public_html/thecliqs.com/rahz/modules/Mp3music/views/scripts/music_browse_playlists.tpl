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
        //echo $pagenumber;
        if ( ($pagenumber)<0)
            return $html;
        $html = str_replace("href='#'","href='mp3-music/browse/browse_playlists/".$pagenumber."'",$html);
        return $html;
    }
}
?>    
    <?php
	$this->headScript()
		 ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');
	?>	
<h3> 
<?php if($this->browse->params['search']): 
 	echo $this->translate('Browse Playlists'); 
 endif; 
if(!$this->browse->params['search']): 
	echo $this->translate('New playlists'); 
endif; ?>  
</h3>
<ul class="global_form_box" style="background: none; overflow: auto;">   
<div class="mp3_browse_album">      
        <?php $playlists    =  $this->browse->playlistPaginator;
        foreach ($playlists as $playlist): ?>
        <li class="mp3music_browsealbums" style="float: none; overflow: hidden;">
           <div class="mp3music_bgalbums" style="float: left" title="<?php echo $playlist->title;  ?>">
               <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('playlist_id'=>$playlist->playlist_id), 'mp3music_playlist');?>',123,565)">
                <?php echo $this->itemPhoto($playlist->getOwner(), 'thumb.profile'); ?>  
                </a>
            </div>
             <div class="mp3_album_des">
                <div class="mp3_title_link">
                    <a title="<?php echo $playlist->title;?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('playlist_id'=>$playlist->playlist_id), 'mp3music_playlist');?>',123,565)">
                    <?php echo $playlist->title;?>
                    </a>
                </div>
                <div class="mp3_album_info" style="width: 380px;">
                    <?php echo $this->translate('Author: ');?><?php echo $playlist->getOwner() ?> <br/>
                    <?php echo $this->translate('Created: %s',$this->timestamp($playlist->creation_date)) ?>  
                    <div style="padding-top: 10px;"> 
                        <?php echo $playlist->description ?>
                    </div>
                </div>
            </div>
        </li>  
         <?php  endforeach; ?>    
 </div> 
   <span style="float:right ;"> <?php echo renderMusicPagination($this->paginationControl($this->browse->playlistPaginator, null, null, array(
        'pageAsQuery' => false,
        'query' => array("search"=>$this->browse->params['search'], "title"=>$this->browse->params['title']))))?>  </span> 
  <?php if (0 == count($playlists) ): ?>
                <div class="tip" style="padding-left: 20px;">
                <span>
                <?php if($this->browse->params['title']): 
                    echo $this->translate('Nobody has uploaded a playlist with that criteria.');
                else:
                    echo $this->translate('Nobody has uploaded a playlist yet.');
                endif;?> 
                </span>     
                </div>
                <?php endif;  ?> 
</ul>