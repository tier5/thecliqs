<script type="text/javascript">

  en4.core.runonce.add(function () {
    friendsAlbumsBind();
  });

  window.friendsAlbumsIds = [];

  function friendsAlbumsBind() {
    $$('.m_album_container').removeEvents().addEvents({
      mouseenter:function () {
        this.getElement('span').fade('in');
        var elInfo = this.getElement('div');
        elInfo.fade('in');
      },
      mouseleave:function () {
        this.getElement('span').fade('out');
        var elInfo = this.getElement('div');
        elInfo.fade('out');
      }
    });

    $$('.layout_headvancedalbum_friends_albums .m_albums_container ul li').get('id').each(function (id) {
      window.friendsAlbumsIds[window.friendsAlbumsIds.length] = id.substr(6);// album_
    });

  }

  function friendsAlbumsMore() {

    // Display loader
    $('friend-albums-seemore').hide();
    $('friend-albums-loading').show();

    (new Request(
      {
        url:en4.core.baseUrl + 'core/widget/index/content_id/<?php echo $this->identity;?>/format/html',
        data:{
          hide_ids:window.friendsAlbumsIds
        },
        onComplete:function (html) {
          var $new_content = new Element('div', {html:html});
          var html = $new_content.getElement('.generic_layout_container').get('html');
          $$('.layout_headvancedalbum_friends_albums').set('html', html);
          en4.core.runonce.trigger();
          friendsAlbumsBind();
        }
      }
    )).send();
  }


</script>


<div class="m_albums_container">
  <ul>
    <?php foreach ($this->paginator as $album): ?>
    <li style="display: inline-block; cursor: pointer; text-align :center; margin-bottom: 16px;"
        id="album_<?php echo $album->getIdentity();?>">
      <div class="m_album_container">
        <img id="album_<?php echo $album->photo_id; ?>" class="albums"
             src="<?php echo $album->getPhotoUrl('thumb.profile')?>">

        <div class="info">
          <span class="author"><?php echo $this->translate('by');?> <a
            href="<?php echo $album->getOwner()->getHref(); ?>"><?php echo $album->getOwner()->getTitle(); ?></a></span>
          <span class="count"><a href="javascript://"><?php echo $album->count(); ?></a></span>
        </div>
      </div>
      <a title="<?php echo $album->getTitle(); ?>" class="title" href="<?php echo $album->getHref(); ?>">
        <?php echo $this->string()->truncate($album->getTitle(), 18, '...'); ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</div>

<?php if (!$this->the_end): ?>
<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Core/externals/images/loading.gif" alt="" style="display: none;" id="friend-albums-loading" class="he_advanced_albums_loading"/>
<a href="javascript:void(0);" class="he_advanced_albums_see_more" id="friend-albums-seemore"
   onclick="friendsAlbumsMore();"><?php echo $this->translate('HEADVANCEDALBUM_SEE_MORE');?></a>
<?php endif; ?>