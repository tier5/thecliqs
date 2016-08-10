<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
        var anchor = $('ynvideochannel_list_channels_browse_<?php echo $this->identity; ?>').getParent();
        $('ynvideochannel_channels_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynvideochannel_channels_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynvideochannel_fav_videos_previous').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                }
            }), {
                'element' : anchor
            })
        });

        $('ynvideochannel_channels_next').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                }
            }), {
                'element' : anchor
            })
        });
        <?php endif; ?>
        });
</script>
<div id="ynvideochannel_list_channels_browse_<?php echo $this->identity; ?>">
    <?php echo $this->partial('_channels_grid.tpl', 'ynvideochannel', array('channels' => $this->paginator)); ?>
</div>

<div>
    <div id="ynvideochannel_channels_previous" class="paginator_previous">
        <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        'onclick' => '',
        'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="ynvideochannel_channels_next" class="paginator_next">
        <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => '',
        'class' => 'buttonlink_right icon_next'
        ));
        ?>
    </div>
</div>