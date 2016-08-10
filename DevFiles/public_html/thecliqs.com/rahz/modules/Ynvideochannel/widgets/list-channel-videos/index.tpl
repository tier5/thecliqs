<?php $channel = $this->channel ?>

<div class="ynvideochannel_channel_detail-countvideos">
    <i class="fa fa-video-camera" aria-hidden="true"></i>
    <?php echo $this -> translate(array("%s video", "%s videos", $channel -> video_count), $channel -> video_count)?>
</div>

<ul class="ynvideochannel_videos_grid clearfix" id="video_list_container">
</ul>

<script>
    var url = '<?php echo $this->url(array('action'=>'ajax-get-videos', 'channel_id' => $this -> channel -> getIdentity(), 'itemCountPerPage' => $this->itemCountPerPage), 'ynvideochannel_channel')?>'
    function ajaxGetVideos(pageId)
    {
        var request = new Request.HTML({
            url : url,
            data : {
                page : pageId,
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                // responseHTML = responseHTML.replace(/<\/?span[^>]*>/g,"");
                document.getElementById("video_list_container").innerHTML = responseHTML;
                Smoothbox.bind('.smoothbox');
                ynvideochannelVideoOptions();
                ynvideochannelAddToOptions();
            }
        });
        request.send();
    }
    window.addEvent('domready', function(){
        ajaxGetVideos(1);
    });
    var openPage = function(pageId){
        ajaxGetVideos(pageId);
    }
</script>