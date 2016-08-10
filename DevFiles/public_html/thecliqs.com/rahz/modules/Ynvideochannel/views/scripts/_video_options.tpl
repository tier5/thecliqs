<?php
$item = $this -> video;
$viewer = $this -> viewer();
?>

<?php if (($item->authorization()->isAllowed($viewer, 'edit')) || ($item->authorization()->isAllowed($viewer, 'delete')) || ($this -> unfavorite)):?>
<div class="ynvideochannel_video_options">
    <span class="ynvideochannel_video_options-btn"><i class="fa fa-cog" aria-hidden="true"></i></span>
    <ul class="ynvideochannel_video_options-block">
    <?php if ($item->authorization()->isAllowed($viewer, 'edit')):?>
        <li>    
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_video',
            'action' => 'edit',
            'video_id' => $item->getIdentity(),
            ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit video'), array('class' => 'icon_ynvideochannel_edit'));
            ?>
        </li>
        <?php endif ?>
        <?php if ($item->authorization()->isAllowed($viewer, 'delete')):?>
        <li>
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_video',
            'action' => 'delete',
            'video_id' => $item->getIdentity(),
            'format' => 'smoothbox'
            ), '<i class="fa fa-trash"></i>'.$this->translate('Delete video'), array('class' => 'smoothbox icon_ynvideochannel_delete'));
            ?>
        </li>
    <?php endif ?>
    </ul>
</div>

<!-- Layout Options for mangage page. -->
<div class="ynvideochannel_manage_video_options" style="display: none">
    <ul class="ynvideochannel_manage_video_options-block">
    <?php if($this -> unfavorite) :?>
        <li>
            <?php echo $this->htmlLink(array(
                'route' => 'ynvideochannel_video',
                'action' => 'unfavorite',
                'video_id' => $item->getIdentity(),
                'format' => 'smoothbox'
                ), '<i class="fa fa-times" aria-hidden="true"></i>', array('class' => 'smoothbox icon_ynvideochannel_unfavorite', 'title'=>$this->translate('Un-favorite')));
            ?>
        </li>
    <?php else: ?>
    <?php if ($item->authorization()->isAllowed($viewer, 'edit')):?>
        <li>    
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_video',
            'action' => 'edit',
            'video_id' => $item->getIdentity(),
            ), '<i class="fa fa-pencil" aria-hidden="true"></i>', array('class' => 'icon_ynvideochannel_edit', 'title'=>$this->translate('Edit video')));
            ?>
        </li>
        <?php endif ?>
        <?php if ($item->authorization()->isAllowed($viewer, 'delete')):?>
        <li>
            <?php
            echo $this->htmlLink(array(
            'route' => 'ynvideochannel_video',
            'action' => 'delete',
            'video_id' => $item->getIdentity(),
            'format' => 'smoothbox'
            ), '<i class="fa fa-trash"></i>', array('class' => 'smoothbox icon_ynvideochannel_delete', 'title'=>$this->translate('Delete video')));
            ?>
        </li>
    <?php endif ?>
    <?php endif ?>
    </ul>
</div>
<?php endif ?>
