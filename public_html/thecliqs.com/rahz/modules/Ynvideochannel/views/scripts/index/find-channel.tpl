<?php echo $this->partial('_addChannnel.tpl', 'ynvideochannel', array('url' => '', 'keyword' => $this -> keyword))?>
<div class="ynvideochannel_channel_find">
    <div class="ynvideochannel_channel_find-keyword"><?php echo $this->translate('Search results for') ?> <span>"<?php echo $this -> keyword?>"</span><?php if(!count($this -> aChannels)):?><?php echo ": ".$this -> translate("No channels found!")?><?php endif;?> </div>
        <ul class="ynvideochannel_channel_find-items">
            <?php foreach($this -> aChannels as $channel):?>
            <li class="ynvideochannel_channel_find-item clearfix">
                <div class="ynvideochannel_channel_find-bg" style="background-image: url('<?php echo $channel['video_image']?>')">
                </div>

                <div class="ynvideochannel_channel_find-actions">
                    <button class="buttonlink" onclick="selectChannel('<?php echo $channel['link'] ?>')"><?php echo $this -> translate("Select")?></button>

                    <?php if($channel['isExist']) :?>
                        <?php $existChannel = Engine_Api::_()->getItem('ynvideochannel_channel', $channel['isExist']);?>
                        <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $existChannel,'showEditDel' => false)); ?>
                    <?php endif; ?>
                </div>

                <div class="ynvideochannel_channel_find-info">
                    <a class="ynvideochannel_channel_find-title" href="<?php echo $channel['link']?>"><?php echo $channel['title']?></a>

                    <?php if($channel['summary']) :?>
                        <div class="ynvideochannel_channel_find-description"><?php echo $this -> string() -> truncate( $channel['summary'], 100) ?></div>
                    <?php endif; ?>

                    <?php if($channel['isExist']) :?>
                        <?php $existChannel = Engine_Api::_()->getItem('ynvideochannel_channel', $channel['isExist']);?>
                            <div class="tip">
                                <span>
                                    <?php echo $this->translate('This channel is already added.');?>
                                </span>
                            </div>
                    <?php endif; ?>

                    <div class="ynvideochannel_channel_find-count">

                        <span> <?php echo $this->translate(array('%s SUBSCRIBER', '%s SUBSCRIBERS',  $channel['subscriber_count']), $this->locale()->toNumber( $channel['subscriber_count'])) ?></span>
                        &nbsp;.&nbsp;
                        <span> <?php echo $this->translate(array('%s video', '%s videos',  $channel['video_count']), $this->locale()->toNumber($channel['video_count'])) ?></span>
                    </div>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
</div>

<div class="ynvideochannel_channel_find-button">
    <?php if(!empty($this->sPageTokenPrev)):?>
        <button class="buttonlink" onclick="prevChannel('<?php echo $this->sPageTokenPrev ?>')"><i class="fa fa-chevron-left"></i></button>
    <?php endif ?>
    <?php if(!empty($this->sPageTokenNext)):?>
        <button class="buttonlink" onclick="nextChannel('<?php echo $this->sPageTokenNext ?>')"><i class="fa fa-chevron-right"></i></button>
    <?php endif ?>
</div>

<script type="text/javascript">
    var selectChannel = function(channel_url)
    {
        window.location = "<?php echo $this -> url(array('action' => 'get-channel'), 'ynvideochannel_general', true)?>" + "?channel_url=" + channel_url;
    };

    var prevChannel = function(sPageTokenPrev)
    {
        window.location = "<?php echo $this -> url(array('action' => 'find-channel'), 'ynvideochannel_general', true)?>" + "?keyword=" + "<?php echo $this->keyword ?>" + "&prev_channels=" + sPageTokenPrev;

    };
    var nextChannel = function(sPageTokenNext)
    {
        window.location = "<?php echo $this -> url(array('action' => 'find-channel'), 'ynvideochannel_general', true)?>" + "?keyword=" + "<?php echo $this->keyword ?>" + "&next_channels=" + sPageTokenNext;
    };

</script>
