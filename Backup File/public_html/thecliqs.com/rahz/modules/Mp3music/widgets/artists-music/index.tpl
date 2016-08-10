<ul class="global_form_box" style="margin-bottom: 15px;">
<div class="avd_music_singers"> 
<?php $artists = $this->artists;
    $allow_artist = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.artist', 1);
    if($allow_artist):
        foreach($artists as $artist): ?>
        <li class="mp3_title_link"> 
        <div class="mp3_image" title="<?php echo $artist["displayname"]; ?>">
            <?php $owner = Engine_Api::_()->getItem('user',$artist["user_id"]);
            echo $this->itemPhoto($owner, 'thumb.icon');?>  
        </div>
        <div class="mp3_title_right" title="<?php echo $artist["displayname"]; ?>">
        <?php echo $this->htmlLink($this->url(array('search'=>'owner','id'=>$artist["user_id"],'title'=>null), 'mp3music_search'),
            strlen($artist["displayname"])>30?substr($artist["displayname"],0,30).'...':$artist["displayname"],
            array('class'=>'')); ?>
        </div>
        </li>
        <?php endforeach;?>
    <?php else:
       foreach($artists as $artist):?>
        <li class="mp3_title_link">   
        <div class="mp3_image" title="<?php echo $artist->title; ?>">
            <?php echo $this->itemPhoto($artist, 'thumb.icon');?> 
        </div>
        <div class="mp3_title_right" title="<?php echo $artist->title; ?>"> 
            <?php echo $this->htmlLink($this->url(array('search'=>'artist','id'=>$artist->artist_id,'title'=>null), 'mp3music_search'),
                strlen($artist->title)>30?substr($artist->title,0,30).'...':$artist->title,
                array('class'=>'')); ?>
        </div>
        </li>
        <?php endforeach;?> 
        <li class="mp3_title_link"> 
        <div class="mp3_image">
            <img src="application/modules/Mp3music/externals/images/others.png" alt="" title="<?php echo $this->translate('Others') ?>">   
        </div> 
        <div class="mp3_title_right" title="<?php echo $this->translate('Others') ?>">   
        <?php echo $this->htmlLink($this->url(array('search'=>'artist','title'=>null,'id'=>null), 'mp3music_search'),
            $this->translate('Others'),
            array('class'=>'')); ?>
        </div>
        </li>     
    <?php endif; ?> 
</div>         
</ul>            
