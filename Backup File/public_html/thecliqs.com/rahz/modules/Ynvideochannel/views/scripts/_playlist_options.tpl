<?php
$item = $this -> playlist;
$viewer = $this -> viewer();
$canEdit = $item->isEditable();
$canDelete = $item->isDeletable();
?>

<?php if ($canEdit || $canDelete):?>
<div class="ynvideochannel_video_options">
    <span class="ynvideochannel_video_options-btn"><i class="fa fa-cog" aria-hidden="true"></i></span>
    <ul class="ynvideochannel_video_options-block">
    <?php if ($canEdit):?>
        <li>
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_playlist',
            'action' => 'edit',
            'playlist_id' => $item->getIdentity(),
            ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit playlist'), array('class' => 'icon_ynvideochannel_edit'));
            ?>
        </li>
        <?php endif ?>
        <?php if ($canDelete):?>
        <li>
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_playlist',
            'action' => 'delete',
            'playlist_id' => $item->getIdentity(),
            'format' => 'smoothbox'
            ), '<i class="fa fa-trash"></i>'.$this->translate('Delete playlist'), array('class' => 'smoothbox icon_ynvideochannel_delete'));
            ?>
        </li>
    <?php endif ?>
    </ul>
</div>
<?php endif ?>
