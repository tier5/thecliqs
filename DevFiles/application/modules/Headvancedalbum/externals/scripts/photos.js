var he_albums = {
  photos_browse_url: '',
  photos_cats_url: '',
  photos_search_url: '',
  last: true
};

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

function onScrollBrowse() {

  if ((window.getScrollTop() + 400 >= window.getScrollSize().y - window.getSize().y) && scrollOn){ // 400px round
    scrollOn = false;
    var he_photos_container_width = parseInt(browse_photos.getElements('.he_photos_container')[0].getStyle('width'));
    var photos = new Array();
    var sumWidth = 0;
    var sumRowWidth = 0;
    var photo_id = 0;
    var addingHeight = 0;
    var max_height = 180;
    var k = -1;
    var owner_name_id = 0;
    var width = 0;
    var height = 0;
    var owner_names = new Array();

    var categories = $$('.category_item');
    var category = '';
    var pos = -1;

    for (var i = 0; i < categories.length; i++) {
      if (categories[i].hasClass('active')) {
        category = categories[i].getProperty('id');
        pos = category.indexOf('_');
        category = category.substring(pos + 1);
      }
    }

    var count_photos = browse_photos.getElements('.he_photos')[0].length;
    var page_number = count_photos / 5 + 1;
    var data = {'format':'json', 'page_number':page_number, 'count_photos':count_photos, 'nocache': Math.random(), 'category': category, 'type': 'photos'};

    new Request.JSON({
      url: he_albums.photos_browse_url,
      data:data,
      'method':'post',
      onSuccess:function($resp)
      {
        if ($resp.photosCount == 0) {
          return;
        }
        creatingNewElements($resp);
      }
    }).send();
  }
}

function creatingNewElements($resp) {
  var photo_ids = $resp.photo_ids;
  var photo_paths = $resp.photoPaths;
  var photo_urls = $resp.photoUrls;
  var owners_urls = $resp.owners_urls;
  var owners_titles = $resp.owners_titles;
  var albums_titles = $resp.albums_titles;
  var commentsCount = $resp.commentsCount;
  var likesCount = $resp.likesCount;

  window.scroll_count_loaded = 0;
  var $photo_cont_tpl = $('photo_container_tpl');
  for (var i = 0; i < photo_ids.length; i++) {
    var $photo_cont = $photo_cont_tpl.clone();

    $photo_cont.addClass('photo_container');
    $photo_cont.getElements('a')[0].setProperty('href', photo_urls[i]);

    var $el_photo = $photo_cont.getElement('.he_photos');
    $el_photo.setProperty('id', 'photo_' + photo_ids[i])
      .setProperty('src', photo_paths[i]);

    $photo_cont.getElement('.owner_name')
      .setProperty('id', 'owner_name_' + photo_ids[i])
      .set('html', en4.core.language.translate('by') + ' ' + owners_titles[i]);

    $photo_cont.getElement('.albums_title')
      .set('html', albums_titles[i]);

    $photo_cont.getElements('a.comments, a.likes')
      .setProperty('href', photo_urls[i]);

    $photo_cont.getElement('.author a')
      .setProperty('href', owners_urls[i])
      .set('html', owners_titles[i]);

    $$('.he_photos_container').grab($photo_cont);

    $el_photo.addEvent('load', function () {
      window.scroll_count_loaded++;
      if (window.scroll_count_loaded >= photo_ids.length) {
        resizeAfterScroll();
        scrollOn = true;
      }
    });
  }
}

function resizeBeforeScroll()
{
  var browse_photos_container = browse_photos.getElement('.he_photos_container');
  var he_photos_container_width = parseInt(browse_photos_container.getStyle('width'));
  if (isNaN(he_photos_container_width)) {
    he_photos_container_width = 935;
  }
  // for static photos
  var photos = browse_photos_container.getElements('.he_photos')[0];
  var owner_names = browse_photos_container.getElements('.owner_name')[0];
  var sumWidth = 0;
  var sumRowWidth = 0;
  var photo_id = 0;
  var owner_name_id = 0;
  photo_id_array = new Array();
  owner_names_id_array = new Array();
  var addingHeight = 0;
  var max_height = 180;
  var k = -1;
  var width = 0;
  var height = 0;

  for (var i = 0; i < photos.length; i++) {
    photo_id = photos[i].getProperty('id');
    k++;
    sumRowWidth += parseInt(browse_photos_container.getElement('#'+photo_id).getStyle('width')) + 11;
    if (sumRowWidth < he_photos_container_width) {
      photo_id_array[k] = photo_id;
      owner_names_id_array[k] = owner_names[i].getProperty('id');
    }
    if (sumRowWidth > he_photos_container_width) {
      k = -1;
      i = i - 1;
      sumRowWidth = 0;
      addingHeight = 0;
      while (sumWidth < he_photos_container_width - 14) {
        addingHeight++;
        sumWidth = 0;
        for (var j = 0; j < photo_id_array.length; j++) {
          browse_photos_container.getElement('#'+photo_id_array[j]).setStyle('max-height', max_height + addingHeight);
          sumWidth += parseInt(browse_photos_container.getElement('#'+photo_id_array[j]).getStyle('width'));
        }
        sumWidth = sumWidth + photo_id_array.length * 11;
      }
      photo_id_array.length = 0;
      sumWidth = 0;
    }
  }
  browse_photos_container.getElements('.photo_container')[0].fade('in');
}

function resizeAfterScroll()
{
  var browse_photos_container = browse_photos.getElement('.he_photos_container');
  var photos = browse_photos.getElements('.photo_container .he_photos')[0];
  var he_photos_container_width = parseInt(browse_photos_container.getStyle('width'));
  var owner_names = browse_photos_container.getElements('.owner_name')[0];
  var sumWidth = 0;
  var sumRowWidth = 0;
  var photo_id = 0;
  var photo_id_array = new Array();
  var owner_names_id_array = new Array();
  var addingHeight = 0;
  var max_height = 180;
  var k = -1;
  //var photos = new Array();

//  for (var i = 0; i < photo_id_array.length; i++) {
//    photos[i] = $(photo_id_array[i]);
//  }
//
//  if (photos[0] == undefined || photos[0] == null) {
//    photos = browse_photos.getElements('.he_photos')[0];
//  }
//
//  var new_photos = $$('.new_photos');
//  for (var m = 0; m < new_photos.length; m++) {
//    photos[i] = new_photos[m];
//    i++;
//  }

  for (var i = 0; i < photos.length; i++) {
    photo_id = photos[i].getProperty('id');
    k++;
    sumRowWidth += parseInt(browse_photos_container.getElement('#'+photo_id).getStyle('width')) + 11;
    if (sumRowWidth < he_photos_container_width) {
      photo_id_array[k] = photo_id;
      owner_names_id_array[k] = owner_names[i].getProperty('id');
    }
    if (sumRowWidth >= he_photos_container_width) {
      k = -1;
      i = i - 1;
      sumRowWidth = 0;
      addingHeight = 0;
      while (sumWidth < he_photos_container_width - 14) {
        addingHeight++;
        sumWidth = 0;
        for (var j = 0; j < photo_id_array.length; j++) {
          browse_photos_container.getElement('#'+photo_id_array[j]).setStyle('max-height', max_height + addingHeight);
          sumWidth += parseInt(browse_photos_container.getElement('#'+photo_id_array[j]).getStyle('width'));
        }
        sumWidth = sumWidth + photo_id_array.length * 11;
      }
      photo_id_array.length = 0;
      sumWidth = 0;
    }
  }
  browse_photos_container.getElements('.photo_container')[0].fade('in');
  browse_photos_container.getElements('.new_photos')[0].removeClass('new_photos');
  browse_photos_container.getElements('.new_owner_names')[0].removeClass('new_owner_names');

  browse_photos_container.getElements('.photo_container')[0].addEvents({
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
}

function showByCategories(element, category)
{
  if (element.hasClass('active')) {
    return false;
  }

  new Request.JSON({
    url:he_albums.photos_cats_url,
    data : {'format': 'json', 'category': category, 'type': 'photos'},
    'method':'post',
    'onRequest':function() {
      $$('.photo_container').destroy();
      $$('.he_loading_2').setStyle('display', 'block');
    },
    onSuccess:function($resp)
    {
      $$('.category_item').removeClass('active');
      element.addClass('active');
      if ($resp.photosCount) {
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

function searchPhotos()
{
  $$('.form-search').removeEvents().addEvent('submit', function(event) {
    event.stop();

    var searchValue = $('search-query').value;
    new Request.JSON({
      url:he_albums.photos_search_url,
      data : {'format': 'json', 'search_photos': searchValue, 'type': 'photos'},
      'method':'post',
      'onRequest':function() {
        $$('.photo_container').destroy();
        $$('.he_loading_2').setStyle('display', 'block');
      },
      onSuccess:function($resp)
      {
        if ($resp.photosCount) {
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
