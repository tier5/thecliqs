<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2013-02-18 16:48:00 ratbek $
 * @author     Ratbek
 */
?>

<script type="text/javascript">

function getInternetExplorerVersion() {
  var rv = -1;
  if (navigator.appName == 'Microsoft Internet Explorer') {
    var ua = navigator.userAgent;
    var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat(RegExp.$1);
  }
  return rv;
}

var album_id_array = new Array();
var owner_names_id_array = new Array();
window.scroll_count_loaded = 0;
var scrollOn = true;
var browse_albums;

en4.core.runonce.add(function()
{
  browse_albums = $$('.layout_headvancedalbum_browse_albums');

  var count_loaded = 0;
  var imgs = browse_albums.getElements('.album_container img')[0];
  imgs.addEvent('load', function () {
    count_loaded++;
    if (count_loaded >= imgs.length){
      $$('.he_loading').setStyle('display', 'none');
      resizeBeforeScroll();
    }
  });

  browse_albums.getElements('.album_container')[0].addEvents({
    mouseenter: function() {
      var elInfo = this.getElement('div');
      elInfo.fade('in');
    },
    mouseleave: function() {
      var elInfo = this.getElement('div');
      elInfo.fade('out');
    }
  });

  window.addEvent('scroll', function () {
      onScrollBrowse();
  });

  window.scrollTo(0,0);

  var default_value = '<?php echo $this->translate("HEADVANCEDALBUM_Search"); ?>';
  $('search-query').value = default_value;

  $('search-query').addEvents({
    'focus' : function() {
      if (this.value == default_value) {
        this.value = "";
      }
    },
    'blur' : function(){
      if (this.value == "") {
        this.value = default_value;
      }
    }
  });
});

function onScrollBrowse() 
{

  var offset_height = document.body.offsetHeight;
  var inner_height = window.innerHeight;

  if ((window.getScrollTop() + 400 >= window.getScrollSize().y - window.getSize().y) && scrollOn){ // 400px round
    scrollOn = false;
    var he_albums_container_width = parseInt($$('.he_albums_container').getStyle('width'));
    var albums = new Array();
    var sumWidth = 0;
    var sumRowWidth = 0;
    var album_id = 0;
    var addingHeight = 0;
    var max_height = 108;
    var k = -1;
    var owner_name_id = 0;
    var width = 0;
    var height = 0;

    var categories = $$('.category_item');
    var category = '';
    var pos = -1;

    for(var i=0; i < categories.length; i++) {
      if (categories[i].hasClass('active')) {
        category = categories[i].getProperty('id');
        pos = category.indexOf('_');
        category = category.substring(pos+1);
      }
    }

    var count_albums = $$('.he_albums').length;
    var page_number = count_albums / 5 + 1;
    var data = {'format':'json', 'page_number':page_number, 'count_albums':count_albums, 'nocache': Math.random(), 'category': category, 'type': 'albums'};

    new Request.JSON({
      url:"<?php echo $this->url(array('module'=>'headvancedalbum', 'controllers'=>'index', 'action' => 'scroll'), 'default', false); ?>",
      data : data,
      'method':'post',
      onSuccess:function($resp)
      {
        if ($resp.albumsCount == 0) {
          return;
        }
        creatingNewElements($resp);
      }
    }).send();
  }
}

function creatingNewElements($resp)
{
  var album_ids = $resp.album_ids;
  var album_paths = $resp.albumPaths;
  var album_urls = $resp.albumUrls;
  var owners_urls = $resp.owners_urls;
  var owners_titles = $resp.owners_titles;
  var albums_titles = $resp.albums_titles;
  var count_photos = $resp.count_photos;

  window.scroll_count_loaded = 0;

  for (var i = 0; i < album_ids.length; i++) {
    var el_album_link = new Element('a');
    el_album_link.setProperty('href', album_urls[i]);

    var el_album = new Element('img#album_'+album_ids[i]);
    el_album.setProperty('src', album_paths[i]);
    el_album.setProperty('class', 'he_albums new_albums');

    var el_album_container = new Element('div.album_container');
    el_album_container.setProperty('style','margin: 5px; opacity: 0');

    var el_info = new Element('div.info');

    var el_albums_title = new Element('a.albums_title');
    el_albums_title.set('href', el_album_link);
    el_albums_title.set('html', albums_titles[i]);

    var el_author = new Element('span.author');
    el_author.set('html', '<?php echo $this->translate('by');?> ');

    var el_author_link = new Element('a');
    el_author_link.setProperty('href', owners_urls[i]);
    el_author_link.set('html', owners_titles[i]);

    var el_count_photos = new Element('span.count_photos');
    el_count_photos.set('html', count_photos[i]);

    var el_picture_icon = new Element('img');
    el_picture_icon.set('src', '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/picture-icon.png');

    // adopting all elements
    el_album_link.adopt(el_album);
    el_album_link.adopt(el_albums_title);
    el_author.adopt(el_author_link);
    el_info.adopt(el_author, el_count_photos, el_picture_icon);
    el_album_container.adopt(el_album_link, el_info, el_albums_title);
    $$('.he_albums_container').grab(el_album_container);

    el_album.addEvent('load', function() {
      window.scroll_count_loaded++;
      if (window.scroll_count_loaded >= album_ids.length) {
        resizeAfterScroll();
        scrollOn = true;
      }
    });
  }
}

function resizeBeforeScroll()
{
  var browse_albums_container = browse_albums.getElement('.he_albums_container');

  var he_albums_container_width = parseInt(browse_albums_container.getStyle('width'));
  if (isNaN(he_albums_container_width)) {
    he_albums_container_width = 935;
  }
  // for static photos
  var albums = browse_albums_container.getElements('.he_albums')[0];
  var owner_names = browse_albums_container.getElements('.owner_name')[0];
  var sumWidth = 0;
  var sumRowWidth = 0;
  var album_id = 0;
  var owner_name_id = 0;
  var addingHeight = 0;
  var max_height = 108;
  var k = -1;
  var width = 0;
  var height = 0;
  var album_id_array = new Array();

  for (var i=0; i < albums.length; i++) {
    album_id = albums[i].getProperty('id');
    k++;
    sumRowWidth += parseInt($(album_id).getStyle('width'))+4;
    if (sumRowWidth < he_albums_container_width) {
      album_id_array[k] = album_id;
    }
    if (sumRowWidth > he_albums_container_width) {
      k = -1;
      i = i-1;
      sumRowWidth = 0;
      addingHeight = 0;
      while (sumWidth < he_albums_container_width) {
        addingHeight++;
        sumWidth = 0;
        for (var j=0; j < album_id_array.length; j++) {
          $(album_id_array[j]).setStyle('max-height', max_height + addingHeight);
          sumWidth += parseInt($(album_id_array[j]).getStyle('width'));
        }
        sumWidth = sumWidth + album_id_array.length * 26;
      }
      album_id_array.length = 0;
      sumWidth = 0;
    }
  }
  $$('.album_container').fade('in');
}

function resizeAfterScroll()
{
  var browse_albums_container = browse_albums.getElement('.he_albums_container');
  var albums = browse_albums.getElements('.he_albums')[0];
  var he_albums_container_width = parseInt(browse_albums_container.getStyle('width'));
  var owner_names = browse_albums_container.getElements('.owner_name')[0];
  var sumWidth = 0;
  var sumRowWidth = 0;
  var album_id = 0;
  var album_id_array = new Array();
  var owner_names_id_array = new Array();
  var addingHeight = 0;
  var max_height = 108;
  var k = -1;
  //var albums = new Array();

  /*for (var i = 0; i < album_id_array.length; i++) {
    albums[i] = $(album_id_array[i]);
  }

  if (albums[0] == undefined || albums[0] == null) {
    albums = browse_albums.getElements('.he_albums')[0];
  }

  var new_albums = $$('.new_albums');
  for (var m = 0; m < new_albums.length; m++) {
    albums[i] = new_albums[m];
    i++;
  }*/

  for (var i=0; i < albums.length; i++) {
    album_id = albums[i].getProperty('id');
    k++;
    sumRowWidth += parseInt(browse_albums_container.getElement('#'+album_id).getStyle('width')) + 4;
    if (sumRowWidth < he_albums_container_width) {
      album_id_array[k] = album_id;
    }
    if (sumRowWidth >= he_albums_container_width) {
      k = -1;
      i = i-1;
      sumRowWidth = 0;
      addingHeight = 0;
      while (sumWidth < he_albums_container_width) {
        addingHeight++;
        sumWidth = 0;
        for (var j=0; j < album_id_array.length; j++) {
          browse_albums_container.getElement('#'+album_id_array[j]).setStyle('max-height', max_height + addingHeight);
          sumWidth += parseInt(browse_albums_container.getElement('#'+album_id_array[j]).getStyle('width'));
        }
        sumWidth = sumWidth + album_id_array.length * 26;
      }
      album_id_array.length = 0;
      sumWidth = 0;
    }
  }

  browse_albums_container.getElements('.album_container')[0].fade('in');
  browse_albums_container.getElements('.new_albums')[0].removeClass('new_albums');

  browse_albums_container.getElements('.album_container')[0].addEvents({
    mouseenter: function() {
      var elInfo = this.getElement('div');
      elInfo.fade('in');
    },
    mouseleave: function() {
      var elInfo = this.getElement('div');
      elInfo.fade('out');
    }
  });
}

function showByCategories(element, category)
{
  if (element.hasClass('active')) {
    return false;
  }

  new Request.JSON({
    url:"<?php echo $this->url(array('module'=>'headvancedalbum', 'controllers'=>'index', 'action' => 'show-by-categories'), 'default', false); ?>",
    data : {'format': 'json', 'category': category, 'type': 'albums'},
    'method':'post',
    'onRequest':function() {
      $$('.album_container').destroy();
      $$('.he_loading_2').setStyle('display', 'block');
    },
    onSuccess:function($resp)
    {
      $$('.category_item').removeClass('active');
      element.addClass('active');
      if ($resp.albumsCount) {
        $('tip').setStyle('display', 'none');
      } else {
        $('tip').setStyle('display', 'block');
        return;
      }
      creatingNewElements($resp);
    },
    'onComplete':function() {
      $$('.he_loading_2').setStyle('display', 'none');
    }
  }).send();
}

function searchAlbums()
{
  $$('.form-search').removeEvents().addEvent('submit', function(event) {
    event.stop();
    var searchValue = $('search-query').value;
    if (searchValue == '<?php echo $this->translate("HEADVANCEDALBUM_Search"); ?>') {
      return;
    }

    new Request.JSON({
      url:"<?php echo $this->url(array('module'=>'headvancedalbum', 'controllers'=>'index', 'action' => 'search'), 'default', false); ?>",
      data : {'format': 'json', 'search_albums': searchValue, 'type': 'albums'},
      'method':'post',
      'onRequest':function() {
        $$('.album_container').destroy();
        $$('.he_loading_2').setStyle('display', 'block');
      },
      onSuccess:function($resp)
      {
        if ($resp.albumsCount) {
          $('tip').setStyle('display', 'none');
        } else {
          $('tip').setStyle('display', 'block');
          return;
        }
        creatingNewElements($resp);
        $$('.category_item').removeClass('active');
      },
      'onComplete':function() {
        $$('.he_loading_2').setStyle('display', 'none');
      }
    }).send();
  });
}
</script>

<div class="he_albums_container">
  <div class="headline"><h2><?php echo $this->translate('HEADVANCEDALBUM_Albums');?></h2></div>
  <div class="he_albums_categories">
    <span id="category_recent" class="category_item active recent"><a href="javascript://" onclick="showByCategories(this.getParent(), 'recent')"><?php echo $this->translate('HEADVANCEDALBUM_Recent');?></a></span>
    <span id="category_popular" class="category_item"><a href="javascript://" onclick="showByCategories(this.getParent(), 'popular')"><?php echo $this->translate('HEADVANCEDALBUM_Popular');?></a></span>
    <?php foreach($this->categories as $key => $category): ?>
      <span id="category_<?php echo $key; ?>" class="category_item">
        <a  href="javascript://" onclick="showByCategories(this.getParent(), '<?php echo $key; ?>')"><?php echo $category; ?></a>
      </span>
    <?php endforeach; ?>
  </div>
  <form class="form-search">
    <div class="input-append">
      <input id="search-query" class="input-medium search-query" type="text">
      <button class="search_btn" type="submit" onclick="searchAlbums()">
        <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/icon-search.png" alt="">
      </button>
    </div>
  </form>
  <div class="he_loading">
    <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/he_loading.gif">
  </div>
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <?php foreach($this->paginator as $album): ?>
      <div class="album_container">
        <a href="<?php echo $album->getHref(); ?>"><img id="album_<?php echo $album->album_id; ?>" class="he_albums" src="<?php echo $album->getPhotoUrl('thumb.profile')?>"></a>
        <div class="info">
          <span class="author"><?php echo $this->translate('by');?> <a href="<?php echo $album->getOwner()->getHref(); ?>"><?php echo $album->getOwner()->getTitle(); ?></a></span>
          <span class="count_photos"><?php echo $album->count_photos; ?></span>
          <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/picture-icon.png" alt="">
        </div>
        <a class="albums_title" href="<?php echo $album->getHref(); ?>"><?php echo $album->getTitle() ?></a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <script type="text/javascript">
      en4.core.runonce.add(function() {
        $$('.he_loading').setStyle('display', 'none');
      });
    </script>
    <div class="tip"><span><?php echo $this->translate("HEADVANCEDALBUM_There are no albums"); ?></span></div>
  <?php endif; ?>
  <div id="tip" class="tip" style="display: none"><span><?php echo $this->translate("HEADVANCEDALBUM_There are no albums"); ?></span></div>
  <div class="he_loading_2">
    <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/he_loading.gif">
  </div>
</div>