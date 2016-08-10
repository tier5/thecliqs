<div class="ynvideochannel_form_add_channel">
    <i class="fa fa-desktop" aria-hidden="true"></i>&nbsp;<?php echo $this->translate('Add a channel') ?>
</div>

<?php if($this->exist):?>
<div>
<ul class="form-errors">
    <li>
        <ul class="errors"><li><?php echo $this->translate('You have already shared this channel.')?></li></ul>
    </li>
</ul>
</div>
<?php endif?>
<?php if($this->inValid):?>
<div>
    <ul class="form-errors">
        <li>
            <ul class="errors"><li><?php echo $this->translate('Please provide a valid URL for your channel.')?></li></ul>
        </li>
    </ul>
</div>
<?php endif?>

<div class="ynvideochannel_form_add_channel-block">
    <div class="ynvideochannel_form_add_channel-row">
        <label><?php echo $this -> translate("Channel URL")?></label>

        <div class="ynvideochannel_form_add_channel-input">
            <input type="text" id="channel_url" name="channel_url" value="<?php echo $this -> url?>" onkeydown="getKeyDown()">
            <span><?php echo $this->translate('Paste the web address of the Youtube channel here.'); ?></span>
        </div>

        <button type="button" class="buttonlink" onclick="getChannel()"><?php echo $this -> translate("Get Channel")?></button>
    </div>

    <div class="ynvideochannel_form_add_channel-row">
        <label><?php echo $this -> translate("Keywords")?></label>
        <input type="text" name="keyword" id = "keyword" value="<?php echo $this -> keyword?>" onkeydown="getKeyDown()">
        <button type="button" class="buttonlink" onclick="findChannel()"><?php echo $this -> translate("Find Channels")?></button>
    </div>
</div>

<script type="text/javascript">
    function getKeyDown(evt)
    {
        evt = evt || window.event;
        var charCode = evt.keyCode || evt.which;
        if (charCode == 13) {
            // prevent submitting add channel form
            evt.preventDefault();
            if (typeof evt.target.id != 'undefined' && evt.target.id == 'channel_url')
                getChannel();
            else if (typeof evt.target.id != 'undefined' && evt.target.id == 'keyword')
                findChannel();
        }
    }

    var getChannel = function()
    {
        var channel_url = $('channel_url').value;
        window.location = "<?php echo $this -> url(array('action' => 'get-channel'), 'ynvideochannel_general', true)?>" + "?channel_url=" + channel_url.replace(/.*?:\/\//, "").replace(/(<([^>]+)>)/ig,"");
    };
    var findChannel = function()
    {
        var keyword = $('keyword').value;
        window.location = "<?php echo $this -> url(array('action' => 'find-channel'), 'ynvideochannel_general', true)?>" + "?keyword=" + keyword.replace(/(<([^>]+)>)/ig,"");
    };
</script>