<?php
	$this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynlistings_videos').getParent();
            $('ynlistings_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynlistings_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynlistings_videos_previous').removeEvents('click').addEvent('click', function(){
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

            $('ynlistings_videos_next').removeEvents('click').addEvent('click', function(){
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

<div class="ynlisting_listing_action">
    <?php if ($this->canUpload) : ?>
    <?php echo $this->htmlLink(array(
    	'route' => 'video_general',
    	'action' => 'create',
    	'type_parent' =>'ynlistings_listing',
    	'id_subject' =>  $this->listing->getIdentity(),
      ), $this->translate('Add New Video'), array(
    	'class' => 'buttonlink icon_listings_add_videos'
    )) ?>
    <?php endif; ?>

    <?php if($this->viewer->getIdentity() > 0) :?>
    	  <?php echo $this->htmlLink(array(
            'route' => 'ynlistings_extended',
            'controller' => 'video',
            'action' => 'manage',
            'subject' => $this->subject()->getGuid(),
          ), $this->translate('Browse Videos'), array(
            'class' => 'buttonlink icon_listings_browse_videos'
        )) ?>
    <?php endif; ?>   
</div>

<?php if(count($this->paginator)>0):?>
    <ul class="generic_list_widget ynvideo_widget videos_browse ynvideo_frame ynvideo_list" id="ynlistings_videos" style="padding-bottom:0px;">
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <?php
                echo $this->partial('_video_listing.tpl', 'ynlistings', array(
                    'video' => $item,
                ));
                ?>
                 <div class="video_stats">
                    <?php echo $this->partial('_video_views_stat.tpl','ynlistings', array('video' => $item)) ?>
                    <div class="ynvideo_block">
                        <?php echo $this->partial('_video_rating_big.tpl','ynlistings', array('video' => $item)) ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="clearfix">
        <div id="ynlistings_videos_previous" class="paginator_previous">
        	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        	  'onclick' => '',
        	  'class' => 'buttonlink icon_previous'
        	)); ?>
        </div>
        <div id="ynlistings_videos_next" class="paginator_next">
        	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        	  'onclick' => '',
        	  'class' => 'buttonlink_right icon_next'
        	)); ?>
        </div>
    </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No videos have been added in this listing yet.');?>
    </span>
  </div>
<?php endif;?>