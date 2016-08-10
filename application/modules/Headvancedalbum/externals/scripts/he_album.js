var he_albums = {
  photos_browse_url: '',
  photos_cats_url: '',
  photos_search_url: '',
  $main_cont: {},
  last_photo_number: 0,

  constructor: function(nodeId) {
    var self = this;
    this.$main_cont = $(nodeId);
    browse_photos = $$('.layout_headvancedalbum_browse_he_photos');

    var count_loaded = 0;
    var photos = this.$main_cont.getElements('.photo_container .he_photos');

    // if have cache of image then don't work onload event
    if (Browser.ie) {
      photos.each(function (photo){
        photo.set('src', photo.get('src') + '&nocache=' + new Date().getTime());
      });
    }

    photos.addEvent('load', function () {
      count_loaded++;
      if (count_loaded >= photos.length) {
        $$('.he_loading').setStyle('display', 'none');
        self.resizeBeforeScroll();
      }
    });

    this.$main_cont.getElements('.photo_container').addEvents({
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

    window.addEvent('scroll', function () {
      self.onScrollBrowse();
    });

    window.scrollTo(0, 0);

    this.init_search();
  },

  init_search: function() {
    var default_value = en4.core.language.translate('HEADVANCEDALBUM_Search');
    $('search-query').value = default_value;

    $('search-query').addEvents({
      'focus' : function() {
        if (this.value == default_value) {
          this.value = "";
        }
      },
      'blur' : function(){
        if (this.value == ""){
          this.value = default_value;
        }
      }
    });
  },

  resizeBeforeScroll: function() {
    var self = this;
    var main_cont_width = this.$main_cont.getStyle('width').toInt();
    if (isNaN(main_cont_width)) {
      main_cont_width = 935;
    }
    // for static photos
    var photos = this.$main_cont.getElements('.he_photos');
    var owner_names = this.$main_cont.getElements('.owner_name');
    var sumWidth = 0;
    var sumRowWidth = 0;
    var photo_id = 0;
    photo_id_array = [];
    owner_names_id_array = [];
    var addingHeight = 0;
    var max_height = 180;
    var k = -1;
    var width = 0;
    var height = 0;

    for (var i = 0; i < photos.length; i++) {
      photo_id = photos[i].getProperty('id');
      k++;
      sumRowWidth += parseInt(this.$main_cont.getElement('#'+photo_id).getStyle('width')) + 11;
      if (sumRowWidth < main_cont_width) {
        photo_id_array[k] = photo_id;
        owner_names_id_array[k] = owner_names[i].getProperty('id');
      }
      if (sumRowWidth > main_cont_width) {
        k = -1;
        i = i - 1;
        sumRowWidth = 0;
        addingHeight = 0;
        while (sumWidth < main_cont_width - 14) {
          addingHeight++;
          sumWidth = 0;
          for (var j = 0; j < photo_id_array.length; j++) {
            self.$main_cont.getElement('#'+photo_id_array[j]).setStyle('max-height', max_height + addingHeight);
            sumWidth += parseInt(self.$main_cont.getElement('#'+photo_id_array[j]).getStyle('width'));
          }
          sumWidth = sumWidth + photo_id_array.length * 11;
        }
        photo_id_array.length = 0;
        sumWidth = 0;
      }
    }

    for (var i = 0; i < photo_id_array.length; i++) {
      this.$main_cont.getElement('#'+photo_id_array[i]).getParent('.photo_container').addClass('not_resized_cont');
    }

    this.$main_cont.getElements('.photo_container').fade('in');
  },

  onScrollBrowse: function() {
    var self = this;
    if ((window.getScrollTop() + 400 >= window.getScrollSize().y - window.getSize().y) && scrollOn){ // 400px round
      scrollOn = false;
      var photos = [];
      var sumWidth = 0;
      var sumRowWidth = 0;
      var photo_id = 0;
      var addingHeight = 0;
      var max_height = 180;
      var k = -1;
      var owner_name_id = 0;
      var width = 0;
      var height = 0;
      var owner_names = [];

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

      self.$main_cont.getElement('.he_scroll_loader').removeClass('display_none');

      var count_photos = this.$main_cont.getElements('.photo_container .he_photos').length;
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
          self.creatingNewElements($resp);
        }
      }).send();
    }
  },

  creatingNewElements: function($resp) {
    var self = this;
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

      this.$main_cont.grab($photo_cont);

      $el_photo.addEvent('load', function () {
        window.scroll_count_loaded++;
        if (window.scroll_count_loaded >= photo_ids.length) {
          self.$main_cont.getElement('.he_scroll_loader').addClass('display_none');
          self.resizeAfterScroll();
          scrollOn = true;
        }
      });
    }
  },

  resizeAfterScroll: function() {
    var self = this;
    var photos = this.$main_cont.getElements('.not_resized_cont .he_photos');
    var main_cont_width = this.$main_cont.getStyle('width').toInt();
    var owner_names = this.$main_cont.getElements('.not_resized_cont .owner_name');
    var sumWidth = 0;
    var sumRowWidth = 0;
    var photo_id = 0;
    var photo_id_array = [];
    var owner_names_id_array = [];
    var addingHeight = 0;
    var max_height = 180;
    var k = -1;

    for (var i = 0; i < photos.length; i++) {
      photo_id = photos[i].getProperty('id');
      k++;
      sumRowWidth += parseInt(this.$main_cont.getElement('#'+photo_id).getStyle('width')) + 11;
      if (sumRowWidth < main_cont_width) {
        photo_id_array[k] = photo_id;
        owner_names_id_array[k] = owner_names[i].getProperty('id');
      }
      if (sumRowWidth >= main_cont_width) {
        k = -1;
        i = i - 1;
        sumRowWidth = 0;
        addingHeight = 0;
        while (sumWidth < main_cont_width - 14) {
          addingHeight++;
          sumWidth = 0;
          for (var j = 0; j < photo_id_array.length; j++) {
            this.$main_cont.getElement('#'+photo_id_array[j]).setStyle('max-height', max_height + addingHeight);
            sumWidth += parseInt(this.$main_cont.getElement('#'+photo_id_array[j]).getStyle('width'));
          }
          sumWidth = sumWidth + photo_id_array.length * 11;
        }
        photo_id_array.length = 0;
        sumWidth = 0;
      }
    }
    this.$main_cont.getElements('.not_resized_cont').fade('in');
    this.$main_cont.getElements('.new_photos').removeClass('new_photos');
    this.$main_cont.getElements('.new_owner_names').removeClass('new_owner_names');

    this.$main_cont.getElements('.not_resized_cont').addEvents({
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

    this.$main_cont.getElements('.not_resized_cont').removeClass('not_resized_cont');
    for (var i = 0; i < photo_id_array.length; i++) {
      this.$main_cont.getElement('#'+photo_id_array[i]).getParent('.photo_container').addClass('not_resized_cont');
    }
  },

  last: true
};

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
