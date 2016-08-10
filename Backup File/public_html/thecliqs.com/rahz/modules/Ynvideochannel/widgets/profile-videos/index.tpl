<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
        var anchor = $('ynvideochannel_list_item_browse_<?php echo $this->identity; ?>').getParent();
        $('ynvideochannel_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynvideochannel_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynvideochannel_videos_previous').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function(){
                    window.setTimeout(function(){
                        ynvideochannelAddToOptions();
                        ynvideochannelVideoOptions();
                    }, 500);
                }
            }), {
                'element' : anchor
            });
            en4.core.runonce.add(function() {
                anchor.getElement('h3').destroy();
            });
        });

        $('ynvideochannel_videos_next').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function(){
                    window.setTimeout(function(){
                        ynvideochannelAddToOptions();
                        ynvideochannelVideoOptions();
                    }, 500);
                }
            }), {
                'element' : anchor
            });
            en4.core.runonce.add(function() {
                    anchor.getElement('h3').destroy();
            });
        });
        <?php endif; ?>
        });
</script>
<div id="ynvideochannel_list_item_browse_<?php echo $this->identity; ?>">
    <?php echo $this->partial('_videos_grid.tpl', 'ynvideochannel', array('videos' => $this->paginator)); ?>
</div>

<div>
    <div id="ynvideochannel_videos_previous" class="paginator_previous">
        <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        'onclick' => '',
        'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="ynvideochannel_videos_next" class="paginator_next">
        <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => '',
        'class' => 'buttonlink_right icon_next'
        ));
        ?>
    </div>
</div>