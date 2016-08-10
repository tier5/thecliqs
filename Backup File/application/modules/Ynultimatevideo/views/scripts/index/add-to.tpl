<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<div class="ynultimatevideo_addTo_frame" id="ynultimatevideo_addTo_list">
    <?php if ($this->loggedIn) : ?>
        <div class="ynultimatevideo_addTo_watchlater">
            <div class="ynultimatevideo_add_to_menu_item" id="ynultimatevideo_add_to_watch_later">
                <?php echo '<i class="fa fa-clock-o"></i>'.$this->translate('Watch Later') ?>
            </div>
        </div>

        <div class="ynultimatevideo_addTo_playlists" id="ymbScrollPlaylist">
            <div>
            <div class="ynultimatevideo_addTo_text">
                <?php echo $this->translate('Add to') ?>
            </div>
            <div class="ynultimatevideo_add_to_menu_item ynultimatevideo_add_to_favorite_menu_item">
                <?php echo $this->translate('Favorite') ?>
            </div>

            <?php foreach ($this->playlists as $playlist) : ?>
                <div class="ynultimatevideo_add_to_menu_item ynultimatevideo_menu_item_playlist"
                     playlist="<?php echo $playlist->getIdentity() ?>">
                    <?php
			            $check = false;
                        $tablePlaylistAssoc = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
                        $row = $tablePlaylistAssoc -> getMapRow($playlist -> getIdentity(), $this -> video -> getIdentity());
                        if(isset($row) && !empty($row)) {
                            $check = true;
                        }
                    ?>
                    <input <?php echo ($check)? "checked" : "" ;?> type="checkbox">
                         <?php echo $playlist->title ?>
                </div>
           <?php endforeach; ?>

           </div>
        </div>

        <div class="ynultimatevideo_addTo_newplaylist">
            <div class="ynultimatevideo_add_to_menu_item" id="ynultimatevideo_menu_item_add_to_new_playlist">
                <?php echo '<i class="fa fa-plus"></i>'.$this->translate('Add to new playlist') ?>
            </div>
        </div>

        <div class="ynultimatevideo_addTo_cancel">
            <div class="ynultimatevideo_add_to_menu_item" id="ynultimatevideo_menu_item_cancel">
                <?php echo '<i class="fa fa-times"></i>'.$this->translate('Cancel') ?>
            </div>
        </div>
    <?php else : ?>
        <div class="ynultimatevideo_addTo_result_block">
            <?php
            echo $this->htmlLink(
                    array('route' => 'user_login'), 'Sign In') . ' ' . $this->translate('to add this video to a playlist')
            ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($this->loggedIn) : ?>
    <div class="ynultimatevideo_addTo_frame" id="ynultimatevideo_addTo_new_playlist" style="display: none;">
        <?php if ($this->form) : ?>
            <?php echo $this->form->render(); ?>
        <?php endif; ?>
    </div>

    <div class="ynultimatevideo_addTo_frame" id="ynultimatevideo_addTo_new_playlist_successfully" style="display: none;">
        <div class="ynultimatevideo_addTo_result_block">
            <div class="ynultimatevideo_addTo_result_text ynultimatevideo_addTo_successfully">
                <?php echo $this->translate('Added to') ?>
            </div>

            <p class="ynultimatevideo_addTo_playlist_text" id="ynultimatevideo_addTo_playlist_successfully"></p>
        </div>
        <div class="ynultimatevideo_addTo_cancel">
            <div class="ynultimatevideo_add_to_menu_item" id="ynultimatevideo_menu_item_cancel_successfully">
                <?php echo $this->translate('Close') ?>
            </div>
        </div>
    </div>

    <div class="ynultimatevideo_addTo_frame" id="ynultimatevideo_addTo_dupplicate" style="display:none;">
        <div class="ynultimatevideo_addTo_result_block">
            <div class="ynultimatevideo_addTo_result_text ynultimatevideo_addTo_dupplicate">
                <?php echo $this->translate('Duplicates are not allowed for this playlist.') ?>
            </div>

            <p class="ynultimatevideo_addTo_playlist_text" id="ynultimatevideo_addTo_playlist_dupplicate">
                <a href="javascript:void" onclick="ultimatevideoNextSlideAddToList()">
                    <?php echo $this->translate('Back to playlist') ?>
                </a>
            </p>
        </div>
        <div class="ynultimatevideo_addTo_cancel">
            <div class="ynultimatevideo_add_to_menu_item" id="ynultimatevideo_menu_item_cancel_dupplicate">
                <?php echo $this->translate('Close') ?>
            </div>
        </div>
    </div>

    <div class="ynultimatevideo_addTo_frame" id="ynultimatevideo_addTo_unsuccessfully" style="display:none;">
        <div class="ynultimatevideo_addTo_result_block">
            <div class="ynultimatevideo_addTo_result_text ynultimatevideo_addTo_unsuccessfully">
                <?php echo $this->translate('There is an error occured. Please try again !!!') ?>
            </div>
        </div>
        <div class="ynultimatevideo_addTo_cancel">
            <div class="ynultimatevideo_add_to_menu_item" id="ynultimatevideo_menu_item_cancel_unsuccessfully">
                <?php echo $this->translate('Close') ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
    var myScroll;
    jQuery(document).ready(function() {
        setTimeout(function () {
            myScroll = new iScroll('ymbScrollPlaylist');
        }, 200);
    });
</script>