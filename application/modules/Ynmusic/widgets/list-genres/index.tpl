<div class="generic_list_widget">
    <ul class="ymb_menuRight_wapper ynmusic-genres">
        <?php foreach ($this->genres as $genre) : ?>
            <li value ='<?php echo $genre->getIdentity() ?>' class="ynmusic-genre">
                <i class="fa fa-play-circle"></i><?php echo $genre;?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>