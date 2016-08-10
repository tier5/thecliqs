<?php
    $totalChannels = $this->paginator->getTotalItemCount();
?>
<div class="ynvideochannel_count_videos">
    <i class="fa fa-desktop"></i>
    <?php echo $this -> translate(array("%s channel", "%s channels", $totalChannels), $totalChannels)?>
</div>
<?php if ($totalChannels > 0) : ?>
    <ul class="ynvideochannel_manage_channel_items ynvideochannel_subscriptions_manage">
        <?php foreach ($this->paginator as $channel) : ?>
        <?php echo $this->partial('_channel_manage_item.tpl', array('item' => $channel, 'showOption' => false));?>

        <?php endforeach; ?>
    </ul>
    <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues
        ));
    ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No channels found.'); ?>
        </span>
    </div>
<?php endif; ?>
