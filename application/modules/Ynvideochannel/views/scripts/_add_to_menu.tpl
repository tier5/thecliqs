<?php if($this -> viewer() -> getIdentity()):?>
    <div class="ynvideochannel-action-add-playlist show-hide-action action-container">
        <a class="ynvideochannel-action-link show-hide-btn" href="javascript:void(0)" title="<?php echo $this->translate('Add to playlist')?>">
            <i class="fa fa-plus"></i>
        </a>
        <div class="ynvideochannel-action-pop-up" style="display: none">
            <div class="add-to-playlist-notices"></div>
            <div class="video-action-add-playlist">
                <?php $url = $this->url(array('action'=>'render-favorite-link', 'video_id'=>$this->video->getIdentity()),'ynvideochannel_video', true)?>
                <div rel="<?php echo $url;?>" class="ynvideochannel-loading favorite-loading" style="display: none;text-align: center">
                    <span class="ajax-loading ynvideochannel_ajax_loading">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                    </span>
                </div>
                <div class="favorite_link"></div>
            </div>
            <div class="video-action-add-playlist dropdow-action-add-playlist">
                <span><?php echo $this-> translate('add to playlist') ?></span>
                <?php $url = $this->url(array('action'=>'render-playlist-list', 'video_id'=>$this->video->getIdentity()),'ynvideochannel_video', true)?>
                <div rel="<?php echo $url;?>" class="ynvideochannel-loading add-to-playlist-loading" style="display: none;text-align: center">
                    <span class="ajax-loading ynvideochannel_ajax_loading">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                    </span>
                </div>
                <div class="box-checkbox">
                </div>
            </div>

            <?php if(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynvideochannel_playlist', null, 'create')->checkRequire()):?>
                <div class="video-action-dropdown ynvideochannel-action-dropdown">
                    <a href="javascript:void(0);" onclick="ynvideochannelAddNewPlaylist(this, '<?php echo $this->video->getGuid()?>', '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynvideochannel_general', true);?>');" class="ynvideochannel-action-link add-to-playlist" data="<?php echo $this->video->getGuid()?>"><i class="fa fa-plus"></i><span><?php echo $this->translate('Add to new playlist')?></span></a>
                    <span class="play_list_span"></span>
                </div>
            <?php endif;?>
        </div>
    </div>
<?php endif;?>