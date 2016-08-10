<div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages-profile-header-right">
    <?php
    	if(count($this->paginator) > 0)
    	{
    		echo $this->htmlLink(array(
    	          'route' => 'ynbusinesspages_extended',
    	          'controller' => 'video',
    	          'action' => 'list',
    	          'subject' => $this->subject()->getGuid(),
    	          'tab' => $this->identity,
    	        ), '<i class="fa fa-list"></i>'.$this->translate('View All Videos'), array(
    	          'class' => 'buttonlink'
    	      ));
    	}
    	if( $this->canCreate ): 
    			echo $this->htmlLink(array(
    				'route' => 'video_general',
    				'action' => 'create',
    				'parent_type' =>'ynbusinesspages_business',
    				'subject_id' =>  $this->business->business_id,
    			), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Video'), array(
    			'class' => 'buttonlink'
    			)) ;
    	?>
    <?php endif; ?>
    </div>
    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_video", "Videos", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<?php if($this->paginator->getTotalItemCount() > 0 ):?>
<script type="text/javascript">
en4.core.runonce.add(function()
{
    var anchor = $('ynbusinesspages_profile_videos').getParent();
    $('ynbusinesspages_profile_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynbusinesspages_profile_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynbusinesspages_profile_videos_previous').removeEvents('click').addEvent('click', function(){
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

    $('ynbusinesspages_profile_videos_next').removeEvents('click').addEvent('click', function(){
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
  });
</script>
<ul class="generic_list_widget ynvideo_widget videos_browse ynvideo_frame ynvideo_list" id="ynbusinesspages_profile_videos" style="padding-bottom:0px;">
    <?php foreach ($this->paginator as $item): ?>
        <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
            <?php
            echo $this->partial('_video_listing.tpl', 'ynbusinesspages', array(
                'video' => $item,
                'recentCol' => $this->recentCol
            ));
            ?>
        </li>
        
    <?php endforeach; ?>
</ul>
<div class="ynbusinesspages-paginator">
  <div id="ynbusinesspages_profile_videos_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="ynbusinesspages_profile_videos_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No videos have been added in this business yet.');?>
    </span>
  </div>
<?php endif;?>