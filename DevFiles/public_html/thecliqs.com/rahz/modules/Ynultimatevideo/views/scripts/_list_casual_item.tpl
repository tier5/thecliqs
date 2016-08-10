<?php $videos = $this->videos; ?>
<?php $totalVideo = $videos->getCurrentItemCount(); ?>

<?php if($totalVideo > 0):?>
    <?php $itemPerRow = 5; ?>
    <!--calculate total number of rows-->
    <?php $totalRow = ceil($totalVideo / $itemPerRow); ?>
    <?php for($row = 1; $row <= $totalRow; $row++): ?>
        <!--calculate number items each row-->
        <?php
            if ($row < $totalRow) {
                $currentItemCount = $itemPerRow;
            } else {
                $currentItemCount = $totalVideo - (($row - 1) * $itemPerRow);
            }
            $style = $row % 3;
            if (!$style) $style = 3;
        ?>
        <!--calculate number items each row-->
        <div class="ynultimatevideo-row total-<?php echo $currentItemCount; ?> style-<?php echo $style; ?> clearfix">
            <?php for($index = 1; $index <= $currentItemCount; $index++): ?>
                <?php echo $this->partial('_video_item.tpl', 'ynultimatevideo', array(
                    'video' => $videos->getItem((($row - 1) * $itemPerRow) + $index)
                )); ?>
            <?php endfor; ?>
        </div>
    <?php endfor; ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("No videos found.") ?>
        </span>
    </div>
<?php endif;?>
