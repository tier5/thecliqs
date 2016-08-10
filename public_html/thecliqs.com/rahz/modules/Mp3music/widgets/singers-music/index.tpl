<?php $model = new Mp3music_Model_SingerType(array());
      $singerTypes = $model->getSingerTypes($this->limit);
    foreach ($singerTypes as $singerType): ?>
	<div class="ybo_headline">
		<h3><?php echo $this->translate($singerType->title); ?></h3>
	</div>
    <ul class="global_form_box" style="margin-bottom: 15px;">
    <div class="avd_music_singers">
        <?php $singers = $singerType->getSingers($this->limit);
            foreach($singers as $singer):?>
                <li class="mp3_title_link">
                    <div class="mp3_image" title="<?php echo $singer->title; ?>">
                        <?php echo $this->itemPhoto($singer, 'thumb.icon');?>   
                    </div>
                    <div class="mp3_title_right" title="<?php echo $this->translate($singer->title); ?>">
                    <?php echo $this->htmlLink($this->url(array('search'=>'singer','title'=>null,'id'=>$singer->singer_id), 'mp3music_search'),
                        $this->string()->truncate($singer->title,30),
                        array('class'=>'')); ?>
                    </div>  
                </li>
            <?php endforeach;?>
            <li class="mp3_title_link">
            <div class="mp3_image">
                <img src="application/modules/Mp3music/externals/images/others.png" alt="" title="<?php echo $this->translate('Others') ?>">   
            </div>
            <div class="mp3_title_right" title="<?php echo $this->translate('Others') ?>">
            <?php echo $this->htmlLink($this->url(array('search'=>'singer','title'=>null,'id'=>null), 'mp3music_search'),
                $this->translate('Others'),
                array('class'=>'')); ?>
            </div>
            </li>      
    </div>           
    </ul>             
<?php endforeach; ?>

