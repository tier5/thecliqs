<script type="text/javascript">

  en4.core.runonce.add(function () {
    friendsPhotosBind();
  });

  window.friendsPhotosIds = [];

  function friendsPhotosBind() {
    $$('.photo_container').removeEvents().addEvents({
      mouseenter:function () {
        this.getElement('span').fade('out');
        var elInfo = this.getElement('div');
        elInfo.fade('in');
      },
      mouseleave:function () {
        this.getElement('span').fade('in');
        var elInfo = this.getElement('div');
        elInfo.fade('out');
      }
    });

    $$('.layout_headvancedalbum_friends_photos .m_photos_container ul li').get('id').each(function (id) {
      window.friendsPhotosIds[window.friendsPhotosIds.length] = id.substr(6);// photo_
    });

  }

  function friendsPhotosMore() {

    // Display loader
    $('friend-photos-seemore').hide();
    $('friend-photos-loading').show();

    (new Request(
      {
        url:en4.core.baseUrl + 'core/widget/index/content_id/<?php echo $this->identity;?>/format/html',
        data:{
          hide_ids:window.friendsPhotosIds
        },
        onComplete:function (html) {
          var $new_content = new Element('div', {html:html});
          var html = $new_content.getElement('.generic_layout_container').get('html');
          $$('.layout_headvancedalbum_friends_photos').set('html', html);
          en4.core.runonce.trigger();
          friendsPhotosBind();
        }
      }
    )).send();
  }
  ;


</script>


<div class="he_friends_photos">

  <?php foreach ($this->paginator as $photo): ?>
  <div class="photo_container" id="photo_<?php echo $photo->getIdentity();?>">
    <a href="<?php echo $photo->getHref(); ?>"><img id="photo_<?php echo $photo->photo_id; ?>" class=""
                                                    src="<?php echo $photo->getPhotoUrl('thumb.profile')?>"></a>
 <span id="owner_name_<?php echo $photo->photo_id; ?>"
       class="owner_name"><?php echo $this->translate('by');?> <?php echo $photo->getOwner()->getTitle(); ?></span>

    <div class="info">
      <div class="albums_title"><?php echo $photo->getAlbum()->getTitle() ?></div>
   <span class="author"><?php echo $this->translate('by');?> <a
     href="<?php echo $photo->getOwner()->getHref(); ?>"><?php echo $photo->getOwner()->getTitle(); ?></a></span>
      <a href="<?php echo $photo->getHref(); ?>" class="comments"><img
        src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/comment.png"
        alt=""><?php echo $photo->comments()->getCommentCount(); ?></a>
      <a href="<?php echo $photo->getHref(); ?>" class="likes"><img
        src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/like-icon.png"
        alt=""><?php echo $photo->likes()->getLikeCount(); ?></a>
    </div>
  </div>
  <?php endforeach; ?>
</div>


<?php if (!$this->the_end): ?>
  <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Core/externals/images/loading.gif" alt="" style="display: none;" id="friend-photos-loading" class="he_advanced_albums_loading"/>
<a href="javascript:void(0);" class="he_advanced_albums_see_more" id="friend-photos-seemore"
   onclick="friendsPhotosMore();"><?php echo $this->translate('HEADVANCEDALBUM_SEE_MORE');?></a>
<?php endif; ?>