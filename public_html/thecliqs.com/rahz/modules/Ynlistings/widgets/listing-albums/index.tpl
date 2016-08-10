<?php
	$this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynlistings_listing_album').getParent();
            $('ynlistings_albums_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynlistings_albums_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynlistings_albums_previous').removeEvents('click').addEvent('click', function(){
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

            $('ynlistings_albums_next').removeEvents('click').addEvent('click', function(){
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
  <?php if( $this->canUpload ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'ynlistings_extended',
          'controller' => 'album',
          'action' => 'create',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('Create Album'), array(
          'class' => 'buttonlink icon_listings_add_photos'
      )) ?>
  <?php endif; ?>

  <?php if($this->viewer->getIdentity() > 0) :?>
  	  <?php echo $this->htmlLink(array(
          'route' => 'ynlistings_extended',
          'controller' => 'album',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('Browse Albums'), array(
          'class' => 'buttonlink icon_listings_browse_photos'
      )) ?>
  <?php endif; ?>   
</div>
  
<ul id='ynlistings_listing_album'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ):
          $listing = $this->listing?>
  <ul class="thumbs">
     
    <?php foreach( $this->paginator as $album ): ?>
     <li style="height:auto;margin-bottom: 10px;">
        <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>"  style="padding:1px;">
          <?php $photo = $album->getFirstCollectible();
                if($photo):?>
            <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal');?>)"></span>
          <?php else:?>
            <span style="background-image: url(./application/modules/Ynlistings/externals/images/nophoto_album_thumb_normal.png)"></span>
          <?php endif;?>
        </a>
        <p class="thumbs_info">
          <?php $title = Engine_Api::_()->ynlistings()->subPhrase($album->getTitle(),70);
                if($title == '') $title = "Untitle Album";
                echo $this->htmlLink($album->getHref(),"<b>".$title."</b>");?>
          <br/>
          <?php echo $this->translate('By');?>
          <?php if($album->user_id != 0 ){
              $name = Engine_Api::_()->ynlistings()->subPhrase($album->getMemberOwner()->getTitle(),18);
              echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'thumbs_author'));
            }
             else{
              $name = Engine_Api::_()->ynlistings()->subPhrase($listing->getOwner()->getTitle(),18);
              echo $this->htmlLink($listing->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
             }
          ?>
          <br />
          <?php echo $this->timestamp($album->creation_date) ?>
        </p>
      </li>
   <?php endforeach;?>
  </ul>
  <div class="clearfix">
    <div id="ynlistings_albums_previous" class="paginator_previous">
    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
    	  'onclick' => '',
    	  'class' => 'buttonlink icon_previous'
    	)); ?>
    </div>
    <div id="ynlistings_albums_next" class="paginator_next">
    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
    	  'onclick' => '',
    	  'class' => 'buttonlink_right icon_next'
    	)); ?>
    </div>
  </div>
  <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No albums have been uploaded to this listing yet.');?>
    </span>
  </div>
  <style type="text/css">
	.layout_advgroup_profile_albums ul.global_form_box {
		padding: 15px 0 0!important;
	}
  </style>
  <?php endif; ?>
</ul>