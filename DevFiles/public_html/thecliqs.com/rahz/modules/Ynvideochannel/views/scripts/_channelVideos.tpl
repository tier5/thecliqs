<?php if(count($this->videos)): ?>
    <div class="ynvideochannel_form_select_videos clearfix">
        <span class="ynvideochannel_form_select_videos-label">
            <?php echo $this -> translate("Select videos");?>
        </span>

        <span class="ynvideochannel_form_select_videos-selectall clearfix">
            <input type='checkbox' class='checkbox' id = 'selectall'/>
            <label for="selectall" class="optional"><?php echo $this -> translate("Select all");?></label>
        </span>
    </div>
<?php else: ?>
    <div class="ynvideochannel-nomore-videos">
        <span>
            <?php echo $this->translate("No more videos can be added to this channel.") ?>
        </span>
    </div>
<?php endif; ?>


<?php $itemPerPage = $this -> itemPerPage;
    $videos = $this -> videos;
    $numberPage = ceil(count($videos)/$itemPerPage);
    $vCount = 0;
    for($index = 1; $index <= $numberPage; $index ++): ?>
        <ul id="page_<?php echo $index ?>" class="ynvideochannel_videos_page clearfix" <?php if($index !== 1) echo 'style="display: none"'?>>
            <?php for ($vCount; $vCount < count($videos); $vCount++): ?>
                <li class="ynvideochannel_videos_page-item">
                    <div class="ynvideochannel_videos_page-bg" style="background-image: url('<?php echo $videos[$vCount]['image_path']?>')">
                        <label for="<?php echo $videos[$vCount]['video_id']?>"></label>
                        <div class="ynvideochannel_videos_page-bgopacity"></div>
                        <input type="checkbox" class="checkbox page_<?php echo $index ?>" name="videos[]" id="<?php echo $videos[$vCount]['video_id']?>" value="<?php echo $videos[$vCount]['video_id']?>"/>
                    </div>
                    
                    <div class="ynvideochannel_videos_page-title"><?php echo $videos[$vCount]['title']?></div>
                    <div class="ynvideochannel_videos_page-date"><?php echo  date('j F Y', $videos[$vCount]['time_stamp'])?></div>
                </li>
                <?php if((($vCount + 1) % $itemPerPage) == 0)
                {
                    $vCount++;
                    break;
                }?>
            <?php endfor;?>
        </ul>
    <?php endfor;?>
<input type="hidden" id ="current_page" value="1"/>
<?php if($numberPage > 1) :?>
<div class="pages">
    <ul class="paginationControl">
        <?php
        for($index = 1; $index <= $numberPage; $index ++): ?>
        <li class="ynvideochannel_page_control <?php if($index == 1) echo 'selected';?>" id = "page_control_<?php echo $index?>" >
           <a href="javascript:;" onclick="openPage(<?php echo $index?>)"><?php echo $index?></a>
        </li>
        <?php endfor;?>
    </ul>
</div>
<?php endif ?>
<script type="text/javascript">

    en4.core.runonce.add(function(){
        $('selectall').addEvent('click', function(){
            var checked  = $(this).checked;
            var current_page = $('current_page').value;
            var checkboxes = $$('input.checkbox.page_' + current_page + '[type="checkbox"]');
            checkboxes.each(function(item){
                item.checked = checked;
            });
        })
    });
        var openPage = function(pageId){
        $('current_page').value = pageId ;
        $$('.ynvideochannel_videos_page').hide();
        $$('.ynvideochannel_page_control').each(function(ele, index){
            ele.removeClass('selected');
        });
        $('page_' + pageId).show();
        $$('#page_control_' + pageId).addClass('selected');
    }
</script>