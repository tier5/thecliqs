/* $Id: PhotoViewer.js 08.02.13 10:28 michael $ */

var PhotoViewer = {

  is_setup: false,
  c: null,
  is_viewer: false,
  last_scroll_top: 0,
  timeout: null,
  total_count: 0,
  photos: {},
  album_title: '',
  album_href: '',
  owner_title: '',
  owner_href: '',
  slideshow_is_stop: true,
  slideshow_is_pause: false,
  scale: 1,
  move: {
    top: 0,
    left: 0
  },
  comments: {},

  options: {
    album_id: 0,
    photo_id: 0,
    isPage: 0,
    slideshow_time: 3000,
    min_scale: 1,
    max_scale: 3,
    scale_step: 10
  },

  open: function (options)
  {
    var self = this;

    // Options
    this.options.album_id = options.album_id;
    this.options.photo_id = options.photo_id;
    this.options.isPage = options.isPage;

    this.setup();

    // save the last scroll top
    self.last_scroll_top = j(window).scrollTop();

    this.viewer();
    this.reset();

    self.viewer().addClass('active');

    setTimeout(function (){ // setTimeout is fix for moz animation
      j(window).scrollTop(0);
      j('html').addClass('hideScroll');
      self.resize();
      self.loadPhoto();
    }, 10);


    // save previous id of a comment box and replace their
    this.old_comments_block = j('.layout_core_comments');
    this.setIdPrefix(this.old_comments_block);


  },

  hide: function ()
  {
    var self = this;


    /**
     * If user click to close in the full mode
     *
     */
    if (self.viewer().hasClass('fullmode')){
      self.fullHide();
      return ;
    }


    self.viewer().removeClass('active');

    // restore the last scroll top
    j('html').removeClass('hideScroll');
    j(window).scrollTop(self.last_scroll_top);


    // restore previous id of a comment box
    this.removeIdPrefix(this.old_comments_block);

  },

  resize: function ()
  {

    var w = j(window).width();
    var h = j(window).height();


    if (this.viewer().hasClass('fullmode')) {

      /**
       * FULL MODE {
       */

      j('#wpPhotoViewer').find('.wpPhotoContent, .wpCommentsContent').css('height', h); // by window height
      j('#wpPhotoViewer').find('.wpPhoto').css('width', w); // by window width

      /**
       * FULL MODE }
       */

      // Set Previous to right of window
      j('#wpPhotoViewer').find('.wpNext, .wpPrev').css('right', 0);

    } else {

      /**
       * STANDARD MODE {
       */

      var comment_w = 400; // fixed width of comments
      var bar_h = 70; // height width of bottom bar

      w -= 50; // for margin (25px left and right)
      h -= 50; // for margin (25px for top and bottom)

      // Set sizes of photo and comments

      var top_content_h = h - bar_h;

      j('#wpPhotoViewer').find('.wpPhotoContent, .wpCommentsContent').css('height', top_content_h);
      j('#wpPhotoViewer').find('.wpPhoto').css('width', w - comment_w);
      j('#wpPhotoViewer').find('.wpComments').css('width', comment_w);

      /**
       * STANDARD MODE }
       */


      // Set Previous to Left of Comments
      j('#wpPhotoViewer').find('.wpNext, .wpPrev').css('right', comment_w-10);

    }


    // Center by vertical arrows
    j('#wpPhotoViewer').find('.wpNext, .wpPrev').css('line-height', h + 'px');


    // Detect one column
    var phw = this.viewer().find('.photos').width();

    if (phw-this.total_count*100>0){ // 100px is width of a item
      this.viewer().find('.wpPhotoList').addClass('one_col');
    } else {
      this.viewer().find('.wpPhotoList').removeClass('one_col');
    }

    this.hideOptions();


  },


  loadPhoto: function ()
  {
    var self = this;

    j.ajax({
      url: en4.core.baseUrl + 'photoviewer',
      data: {
        format: 'json',
        photo_id: this.options.photo_id,
        isPage: this.options.isPage
      },
      success: function (json)
      {
        if (!json || !json.photos){
          return ;
        }

        // Album info
        self.total_count = json.count;
        self.album_title = json.album_title;
        self.album_href = json.album_href;
        self.owner_title = json.owner_title;
        self.owner_href = json.owner_href;

        self.photos = {};

        var active_photo_id = 0;

        var html = '';
        j(json.photos).each(function (i, el){
          html+= '<a href="javascript:void(0);" class="wpPhotoItem" onclick="PhotoViewer.view('+el.photo_id+');" id="wp_'+el.photo_id+'"><img id="fullphoto_'+el.photo_id+'" src="'+el.thumb+'" alt=""/></a>';
          self.photos[el.photo_id] = el;

          // by default first
          if (i == 0){
            active_photo_id = el.photo_id;
          }
          if (el.active){
            active_photo_id = el.photo_id;
          }
        });


        // Set Content
        self.viewer().find('.wpPhotoList').find('.photos').html(html);

        // Face In on Load
        self.viewer().find('.wpPhotoList').find('.photos').find('img').load(function (){
          j(this).addClass('loaded');
        });

        // Count
        self.viewer().find('.wpPhotoList').find('.count').html(self.total_count);

        // show all photos button
        if (self.total_count > 1){
          self.viewer().find('.allPhotos').show();
        }

        // show slideshow button
        self.viewer().find('.goSlideshow').show();


        self.view(active_photo_id);
        self._checkPagination();
        self.resize();

      }
    });
  },


  view: function (photo_id)
  {
    var self = this;

    if (!this.photos[photo_id]){
      return ;
    }
    var el = this.photos[photo_id];

    this.viewer().find('.wpPhotoItem').removeClass('active');
    this.viewer().find('#wp_'+photo_id).addClass('active');

    var current = this.viewer().find('#wp_'+photo_id).prevAll().length+1;

    var $photo = this.viewer().find('.wpPhotoContent');

    // hide active photo
    $photo.find('.active').removeClass('active').removeClass('fade');

    // if a photo has been loaded

    var $new_photo =  $photo.find('#thephoto_'+photo_id);
    if ($new_photo.length){
      $new_photo.addClass('active');
      setTimeout(function (){
        $new_photo.addClass('fade');
      }, 10);
    } else {

      // create a new
      var html = '<div class="thephoto" id="thephoto_'+photo_id+'"><div class="moveElement">' +
          '<div id="imgPlace_'+photo_id+'"><img src="'+el.src+'" alt=""/></div>' +
          '<span class="photo_options">' +
            '<a href="javascript:void(0);" class="openfull icon-resize-full" onclick="PhotoViewer.fullOpen();"></a>' +
            '<a href="javascript:void(0);" class="hidefull icon-resize-small" onclick="PhotoViewer.fullHide();"></a>' +
          '</span>' +
        '</div></div>';
      $photo.append(j(html));

      var $new_photo = j('#thephoto_'+photo_id);
      clearTimeout(this.photo);
      this.photo = setTimeout(function (){
        $new_photo.addClass('active');
        setTimeout(function (){
          $new_photo.addClass('fade');
        }, 10);
      }, 10);

    }

    this.resize();

    // Set info about the album
    this.viewer().find('.wpPhotoOptions').find('.info').show();
    var $album_info = this.viewer().find('.wpPhotoOptions').find('.album_info');
    $album_info.find('.album_title').html('<a href="'+this.album_href+'">'+this.album_title+'</a>');
    $album_info.find('.album_owner').html(' - <a href="'+this.owner_href+'">'+this.owner_title+'</a>');

    this.viewer().find('.wpPhotoOptions').find('.count').find('.total').html(this.total_count);
    this.viewer().find('.wpPhotoOptions').find('.count').find('.current').html(current);

    // Ajax loading for current a photo
    self.loadComments(photo_id);

    this._checkPagination();

  },


  _checkPagination: function ()
  {

    var $next = this.viewer().find('.wpNext');
    var $prev = this.viewer().find('.wpPrev');

    if (this.total_count < 2){
      $next.hide();
      $prev.hide();
      return ;
    }

    var $active = this.viewer().find('.wpPhotoItem.active');
    if ($active.prev().length){
      $prev.show();
    } else {
      $prev.hide();
    }

    if ($active.next().length){
      $next.show();
    } else {
      $next.hide();
    }

  },

  next: function (callback_on_end)
  {
    var self = this;

    var $active = this.viewer().find('.wpPhotoItem.active');
    if ($active.next().length){
      $active.next().click();
    } else {
      if (callback_on_end){
        callback_on_end();
      } else {
        this.showList();
      }
    }

    this._checkPagination();

  },

  prev: function ()
  {
    var self = this;

    var $active = this.viewer().find('.wpPhotoItem.active');
    if ($active.prev().length){
      $active.prev().click();
    } else {
      this.showList();
    }

    this._checkPagination();

  },

  toggleList: function ()
  {
    if (this.viewer().find('.wpPhotoList').hasClass('active')){
      this.hideList();
    } else {
      this.showList();
    }
  },

  showList: function ()
  {
    if (this.total_count < 2){
      return ;
    }
    if (this.viewer().hasClass('fullmode')){
      return ;
    }

    this.viewer().find('.photos').jScrollPane();

    this.viewer().find('.wpPhotoList').addClass('active');
    this.showOver();

  },

  isListActive: function ()
  {
    return this.viewer().find('.wpPhotoList').hasClass('active');
  },

  hideList: function ()
  {
    this.viewer().find('.wpPhotoList').removeClass('active');
    this.hideOver();
  },

  loadComments: function (photo_id)
  {
    var self = this;

    // If comments has been loaded already
    if (j('#photo_comment_'+photo_id).length){
      self.setIdPrefix(self.viewer().find('.wpCommentsContent').find('.photo_comment').hide());
      self.removeIdPrefix(j('#photo_comment_'+photo_id).show());
      return ;
    }

    // Cancel previous request
    if (this.comment_request){
      this.comment_request.abort();
    }

    self.showLoading();

    this.comment_request = j.ajax({
      url: en4.core.baseUrl + 'photoviewer/index/comments',
      data: {
        format: 'html',
        photo_id: photo_id,
        isPage: this.options.isPage
      },
      success: function (html)
      {
        self.hideLoading();

        self.setIdPrefix(self.viewer().find('.wpCommentsContent').find('.photo_comment').hide());

        var $comment = j('<div />').attr({
          'id': 'photo_comment_'+photo_id,
          'class': 'photo_comment'
        });
        $comment.html(html);

        self.viewer().find('.wpCommentsContent').append($comment);

        en4.core.runonce.trigger();
        Smoothbox.bind($('photo_comment_'+photo_id));


      }
    });



  },

  setIdPrefix: function ($box)
  {
    $box.find('*[id]').not('.iswphprefix').each(function (i, el){
      j(el).attr('id', 'wphPrefix'+(Math.random()*1000).toInt() + 'wphEnd' + j(el).attr('id'));
      j(el).addClass('iswphprefix');
    });

  },

  removeIdPrefix: function ($box)
  {
    $box.find('.iswphprefix').each(function (i, el) {
      var str = j(el).attr('id');
      var s = str.indexOf('wphEnd');
      if (s === -1) {
        return;
      }
      var id = str.substr(s+6);
      j(el).attr('id', id);
      j(el).removeClass('iswphprefix');
    });
  },

  onError: function (error)
  {
    alert(error);
  },

  setup: function ()
  {
    var self = this;

    if (this.is_setup){
      return ;
    }
    this.is_setup = true;


    /**
     * Events
     */

    // Resize
    j(window).resize(function (){
      self.resize();
    });


    // Keys

    var key_event = 'keypress';
    // this is Mootools function to detect user's browser
    if ((Browser.Engine.trident || Browser.Engine.webkit)){
      key_event = 'keydown';
    }

    j(document).bind(key_event, function (e) {

      var tag = j(e.target).prop("tagName").toLowerCase();
      if (tag == 'textarea' || tag == 'input' || tag == 'select') {
        return;
      }
      if (self.isListActive()){
        return ;
      }

      if (e.keyCode == 37) { // left key
        self.prev();
      } else if (e.keyCode == 39) { // right key
        self.next();
      } else if (e.keyCode == 0) { // space
        //self.fullOpen();
      } else if (e.keyCode == 27) { // esc
        if (self.viewer().hasClass('fullmode')){
          self.fullHide();
        } else {
          self.hide();
        }
      } else if (e.keyCode == 109 || e.keyCode == 38){ // num+, key up
        self.zoom(2);
      } else if (e.keyCode == 107 || e.keyCode == 40){ // num-, key down
        self.zoom(-2);
      }

    });

    // Zooming
    this.viewer().find('.wpPhoto').mousewheel(function (e) {

      // it is mouse tagging
      if (self.viewer().hasClass('tagging_process')){
        return ;
      }

      var event = e.originalEvent;

      var delta = 0;
      if (event.wheelDelta) delta = event.wheelDelta / 120;
      if (event.detail) delta = -event.detail / 3;

      self.zoom(delta);

    });


    // Click to "actions"
    this.viewer().click(function (e){

      if (j(e.target).closest('.external-options').length || j(e.target).hasClass('actions') || j(e.target).parent().hasClass('actions')){
        return ;
      }
      self.hideOptions();
    });


    // Full Mode Events
    if (document.addEventListener){ // IE don't work
      document.addEventListener("fullscreenchange", function () {
        //self.fullToggle(document.fullscreen); // bugs in opera
      }, false);

      document.addEventListener("mozfullscreenchange", function () {
        self.fullToggle(document.mozFullScreen);
      }, false);

      document.addEventListener("webkitfullscreenchange", function () {
        self.fullToggle(document.webkitIsFullScreen);
      }, false);
    }


  },

  fullToggle: function (active)
  {
    if (active){
      this.fullOpen();
    } else {
      this.fullHide();
    }
  },

  zoom: function (delta)
  {
    if (this.isListActive()){
      return ;
    }

    var step = delta/this.options.scale_step;
    this.scale+=step; // step

    if (this.scale < this.options.min_scale){
      this.viewer().removeClass('zooming'); // original
      this.scale-=step; // back
      return ;
    }
    if (this.scale > this.options.max_scale){
      this.scale-=step; // back
      return ;
    }

    this.viewer().addClass('zooming');

    j('#wpPhotoViewer').find('.wpPhoto').find('.active')
      .css('-moz-transform', 'scale('+this.scale+')')
      .css('-webkit-transform', 'scale('+this.scale+')')
      .css('transform', 'scale('+this.scale+')')
    ;


    var ieVersion = this.getInternetExplorerVersion();

    if (ieVersion == 7 || ieVersion == 8){
      var $img = j('#wpPhotoViewer').find('.wpPhoto').find('.active').find('img');
      if (!$img.data('original-width')){
        $img.data('original-width', $img.width());
        $img.data('original-height', $img.height());
      }
      $img
        .css('width', $img.data('original-width')*this.scale)
        .css('height', $img.data('original-height')*this.scale);
    }

  },

  getInternetExplorerVersion:function () {
    var rv = -1;
    if (navigator.appName == 'Microsoft Internet Explorer') {
      var ua = navigator.userAgent;
      var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
      if (re.exec(ua) != null)
        rv = parseFloat(RegExp.$1);
    }
    return rv;
  },

  noZoom: function (enable)
  {
    if (enable){
      this.viewer().addClass('nozoom');
    } else {
      this.viewer().removeClass('nozoom');
    }
  },

  fullOpen: function ()
  {
    var el = this.viewer();
    el.addClass('fullmode');
    this.resize();

    var el_dom = el[0];

    if (el_dom.requestFullScreen){
      el_dom.requestFullScreen();
    }
    if (el_dom.webkitRequestFullScreen){
      el_dom.webkitRequestFullScreen();
    }
    if (el_dom.mozRequestFullScreen){
      el_dom.mozRequestFullScreen();
    }
    if (el_dom.requestFullscreen){
      el_dom.requestFullscreen();
    }

  },

  fullHide: function ()
  {
    var el = this.viewer();
    el.removeClass('fullmode');
    this.resize();

    // return to original
    this.stopSlideshow();
    this.viewer().removeClass('slideshow_process');
    this.viewer().find('.wpSlideshow').hide();

    
    if (document.cancelFullScreen){
      document.cancelFullScreen();
    }
    if (document.webkitCancelFullScreen){
      document.webkitCancelFullScreen();
    }
    if (document.mozCancelFullScreen){
      document.mozCancelFullScreen();
    }
    if (document.exitFullscreen){
      document.exitFullscreen();
    }

  },


  viewer: function ()
  {
    var self = this;

    if (this.is_viewer){
      return this.c;
    }
    this.is_viewer = true;

    var c = j('<div />');
    c.attr({
      'id': 'wpPhotoViewer',
      'class': 'wpPhotoViewer'
    });
    c.html(this.getHtmlContent());
    c.appendTo(j('body'));

    var ieVersion = this.getInternetExplorerVersion();
    if (ieVersion !== -1){
      c.addClass('wpIe');
      c.addClass('wpIe'+ieVersion);
    }

    clearTimeout(self.mouseTimer);

    // Mouse active
    c.mouseover(function (){
      clearTimeout(self.mouseTimer);
      self.mouseTimer = setTimeout(function (){
        self.viewer().addClass('mouseActive');
        clearTimeout(self.mouseTimerOut);
        self.mouseTimerOut = setTimeout(function (){
          self.viewer().removeClass('mouseActive');
        }, 2000);
      }, 10);
    });

    var $photoContent = c.find('.wpPhotoContent');

    $photoContent.unbind('mousemove').mousemove(function (e){

      // except tagging
      if (j(e.target).closest('#lassoMask').length){
        return ;
      }

      if (self.is_pressed){

        clearInterval(self.moving_timer);
        self.moving_timer = setTimeout(function (){

          var deltaX = self.move.startClientX - e.clientX;
          var deltaY = self.move.startClientY - e.clientY;
          self.move.startClientX = e.clientX;
          self.move.startClientY = e.clientY;

          self.photoMove(deltaX, deltaY);

        }, 1);

      }
      return false;
    });

    $photoContent.unbind('mousedown').mousedown(function (e){

      // except tagging
      if (j(e.target).closest('#lassoMask').length){
        return ;
      }

      self.is_pressed = true;
      self.move.startClientX = e.clientX;
      self.move.startClientY = e.clientY;
      $photoContent.addClass('moving');
      return false;
    });
    $photoContent.unbind('mouseup').mouseup(function (e){


      // except tagging
      if (j(e.target).closest('#lassoMask').length){
        return ;
      }

      self.is_pressed = false;
      $photoContent.removeClass('moving');
      return false;
    });

    $photoContent.unbind('mouseout').mouseout(function (e){


      // except tagging
      if (j(e.target).closest('#lassoMask').length){
        return ;
      }

      self.is_pressed = false;
      $photoContent.removeClass('moving');
      return false;
    });


    this.c = c;

    return c;
  },

  photoMove: function (deltaX, deltaY)
  {
    var $photo = j('#thephoto_'+this.getActiveId());

    this.move.top +=-deltaY*1.5;
    this.move.left +=-deltaX*1.5;

    $photo.css('margin-top', this.move.top);
    $photo.css('margin-left', this.move.left);
  },

  showOver: function ()
  {
    var self = this;
    this.viewer().find('.wpOver').show();
    clearTimeout(this.timeout);
    this.timeout = setTimeout(function (){
      self.viewer().find('.wpOver').addClass('active');
    }, 100);
  },


  hideOver: function ()
  {
    var self = this;
    self.viewer().find('.wpOver').removeClass('active');

    clearTimeout(this.timeout);
    this.timeout = setTimeout(function (){
      self.viewer().find('.wpOver').hide();
    }, 500);
  },


  slideshow: function ()
  {
    this.fullOpen();
    this.startSlideshow();
  },

  slideshowClose: function ()
  {
    this.stopSlideshow();
    this.fullHide();
    this.viewer().removeClass('slideshow_process');
    this.viewer().find('.wpSlideshow').hide();
  },

  startSlideshow: function ()
  {
    var self = this;

    this.stopSlideshow();
    this.slideshow_is_stop = false;
    this.slideshow_is_pause = false;
    this.viewer().addClass('slideshow_process');

    this.slideshow_timer = setInterval(function (){
      if (self.slideshow_is_pause){
        return ;
      }
      self.next(function (){
        self.stopSlideshow();
      });
    }, this.options.slideshow_time);

    this._checkStatusSlideShow();

  },

  stopSlideshow: function ()
  {
    clearInterval(this.slideshow_timer);
    this.slideshow_is_stop = true;
    this._checkStatusSlideShow();

  },

  repeatSlideshow: function ()
  {
    this.viewer().find('.photos').find('a:first').click();
    this.startSlideshow();
    this._checkStatusSlideShow();
  },

  _checkStatusSlideShow: function ()
  {
    if (this.slideshow_is_pause){
      this.viewer().find('.wpSlidStart').show();
      this.viewer().find('.wpSlidPause').hide();
      this.viewer().find('.wpSlidRepeat').hide();
    } else if (this.slideshow_is_stop){
      this.viewer().find('.wpSlidStart').hide();
      this.viewer().find('.wpSlidPause').hide();
      this.viewer().find('.wpSlidRepeat').show();
    } else {
      this.viewer().find('.wpSlidStart').hide();
      this.viewer().find('.wpSlidPause').show();
      this.viewer().find('.wpSlidRepeat').hide();
    }
  },

  pauseSlideshow: function ()
  {
    this.slideshow_is_pause = true;
    this._checkStatusSlideShow();
  },

  hideOptions: function ()
  {
    var $opt = j('#photo_comment_'+this.getActiveId()).find('.external-options');
    $opt.hide();
  },

  toggleOptions: function ()
  {
    var $opt = j('#photo_comment_'+this.getActiveId()).find('.external-options');
    $opt.toggle();

    var offset = this.viewer().find('.actions').offset();

    $opt
      .css('top', 32) // TODO
      .css('left', offset.left);

  },

  getActiveId: function ()
  {
    var $active = this.viewer().find('.photos').find('.active');
    if (!$active.length){
      return ;
    }
    return $active.attr('id').substr(3);
  },

  getActive: function ()
  {
    return this.photos[this.getActiveId()];
  },

  getHtmlContent: function ()
  {
    return '' +
      '<div class="wpContainer">' +
        '<a href="javascript:void(0);" class="wpPrev icon-angle-left" onclick="PhotoViewer.prev();" style="display: none;"></a>' +
        '<a href="javascript:void(0);" class="wpNext icon-angle-right" onclick="PhotoViewer.next();" style="display: none;"></a>' +
        '<a href="javascript:void(0);" class="wpClose icon-remove" onclick="PhotoViewer.hide();"></a>' +
        '<div class="wpPhoto">' +
          //'<div class="wpPhotoContent"></div>' +
          '<table width="100%" class="wpPhotoContentTable"><tr><td align="center" valign="center" class="wpPhotoContent"></td></tr></table>' +
        '</div>' +
        '<div class="wpComments">' +
          '<div class="wpCommentsContent">' +
            '<div class="photo_comment loadingComments">' +
              '<div class="layout_page_photoviewer_index_comments">' +
                '<div class="generic_layout_container layout_main"><span class="loading">'+en4.core.language.translate('PHOTOVIEWER_loading')+'</span></div>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>' +
        '<div class="wpBar">' +
          '<div class="wpPhotoList">' +
            '<div class="title">' +
              ''+en4.core.language.translate('PHOTOVIEWER_All photos')+'&nbsp;' +
              '(<span class="count">0</span>)' +
              '<a href="javascript:void(0);" onclick="PhotoViewer.hideList();" class="wpListClose"><i class="icon-remove"></i></a>' +
            '</div>' +
            '<div class="photos"></div>' +
          '</div>' +
          '<div class="wpPhotoOptions">' +
            '<div class="leftside">' +
              '<a href="javascript:void(0);" class="wpbtn wpbtn-inverse allPhotos" onclick="PhotoViewer.toggleList();" style="display: none;">'+en4.core.language.translate('PHOTOVIEWER_All photos')+'<i class="right icon-caret-up"></i></a>' +
              '<a href="javascript:void(0);" class="wpbtn wpbtn-inverse goSlideshow" onclick="PhotoViewer.slideshow();" style="display: none;">'+en4.core.language.translate('PHOTOVIEWER_Slideshow')+'</a>' +
              '<div class="info" style="display: none;">' +
                '<div class="album_info">' +
                  '<span class="album_title"></span>' +
                  '<span class="album_owner"></span></div>' +
                '<div class="count">' +
                    '<span class="current"></span>&nbsp;'+en4.core.language.translate('PHOTOVIEWER_from')+'&nbsp;<span class="total"></span>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="wpSlideshow" style="display: none;">' +
        '<a href="javascript:void(0);" onclick="PhotoViewer.startSlideshow();" style="display: none;" class="wpSlidStart wpbtn wpbtn-inverse">'+en4.core.language.translate('PHOTOVIEWER_Play')+'</a>' +
        '<a href="javascript:void(0);" onclick="PhotoViewer.pauseSlideshow();" style="display: none;" class="wpSlidPause wpbtn wpbtn-inverse">'+en4.core.language.translate('PHOTOVIEWER_Pause')+'</a>' +
        '<a href="javascript:void(0);" onclick="PhotoViewer.repeatSlideshow();" style="display: none;" class="wpSlidRepeat wpbtn wpbtn-inverse">'+en4.core.language.translate('PHOTOVIEWER_Repeat')+'</a>' +
        '<a href="javascript:void(0);" onclick="PhotoViewer.slideshowClose();" class="wpSlidClose wpbtn wpbtn-inverse"><i class="icon-remove"></i></a>' +
      '</div>' +
      '<div class="wpOver" onclick="PhotoViewer.hideList();" style="display: none;"></div>';
  },

  // Return the form to original
  reset: function ()
  {
    this.viewer().find('.photos').jScrollPane().data('jsp').destroy();

    this.viewer().find('.wpOver, .wpNext, .wpPrev, .info, .allPhotos, .goSlideshow, .wpSlideshow, .wpSlidStart, .wpSlidPause, .wpSlidRepeat').hide();
    this.viewer().find('.wpPhotoContent, .photos').empty();
    this.viewer().find('.wpCommentsContent').children().hide();
    this.viewer()
      .removeClass('fullmode')
      .removeClass('zooming')
      .removeClass('slideshow_process')
      .removeClass('mouseActive');

    this.viewer().find('.wpPhotoList').removeClass('.one_col');
    this.viewer().find('.wpCommentsContent').children('.loadingComments').show();

    return this.viewer();

  },

  showLoading: function ()
  {
    this.viewer().find('.wpCommentsContent').children().hide();
    this.viewer().find('.wpCommentsContent').children('.loadingComments').show();
  },

  hideLoading: function ()
  {
    //this.viewer().find('.wpCommentsContent').children().hide();
    this.viewer().find('.wpCommentsContent').children('.loadingComments').hide();
  },

  bindPhotoViewer: function ()
  {
    // jquery is not connected yet
    if (!j){
      return ;
    }

    // .feed_item_thumb TODO take all of links
    j('#global_content').find('a').not('.wp_init').addClass('wp_init').each(function (i, el){

      var href = j(el).attr('href');
      if (!href || href == 'javascript:void(0)'){
        return ;
      }

      var isPage = 0;
      var matches = href.match(/album_id\/([0-9]{0,})\/*\/photo_id\/([0-9]{0,})/i); // all of photo links
      if (!matches){
        matches = href.match(/content\/pagealbumphoto\/album_id\/([0-9]{0,})\/content_id\/([0-9]{0,})/i); // all of page's photo links
        isPage = 1;
      }
      if (!matches || !matches[0] || !matches[1] || !matches[2]){
        return ;
      }

      var album_id = matches[1];
      var photo_id = matches[2];

      $(el).removeEvents(); // TODO mootools remove previous events
      var img = $(el).getElement('img');
      if (img){
        img.removeEvents('click');
      }

      j(el)
        .attr('onclick', '') // fix
        .data('album_id', album_id)
        .data('photo_id', photo_id)
        .data('isPage', isPage)
        .click(function (e){

          e.stopPropagation();
          e.preventDefault();

          PhotoViewer.open({
            album_id: j(this).data('album_id'),
            photo_id: j(this).data('photo_id'),
            isPage: j(this).data('isPage')
          });
        });



    });

  }



};





