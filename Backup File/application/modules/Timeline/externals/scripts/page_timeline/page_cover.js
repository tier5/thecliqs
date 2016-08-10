/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */

var TimelineCover = new Class({
  Implements:[Options],
  options:{
    element_id:null,
    edit_buttons:null,
    loader_id:null,
    cover_width:850,
    cover_url:'',
    position_url:'',
    is_allowed:false
  },

  block:null,
  buttons:null,
  element:null,
  changeButton:null,
  saveButton:null,

  initialize:function (options) {
    this.setOptions(options);
  },

  init:function (options) {
    var self = this;

    if ($(self.options.element_id) != null) {
      self.element = $(self.options.element_id);
    }
    self.buttons = $(self.options.edit_buttons);
    self.block = self.buttons.getParent('div.cover').getParent();
    self.changeButton = self.buttons.getElement('.cover-change');
    self.saveButton = self.buttons.getElement('.save-positions');

    self.initEvents();
  },

  initEvents:function () {
    var self = this;

    if (!self.isAllowed()) return;

    self.get('block').addEvents({
      mouseenter:function () {
        self.getButton().getParent('.cover-edit').setStyle('display', 'block');
      },
      mouseleave:function () {
        self.buttons.setStyle('display', 'none');
      }
    });

    self.getButton().addEvents({
      'click':function () {
        this.toggleClass('active');
        self.buttons.getElement('.cover-options').toggleClass('visiblity-hidden');
      }
    });

    self.getButton('save').addEvent('click', function () {
      this.toggleClass('active');
      self.reposition.stop();
    });
  },

  get:function (type) {
    if (type == 'block') {
      return this.block;
    }

    return $(this.options.element_id);
  },

  getButton:function (type) {
    if (type == 'save') {
      return this.saveButton;
    }

    return this.changeButton;
  },

  isAllowed:function () {
    return this.options.is_allowed;
  },

  load_photo:function (id) {
    var self = this;

    new Request.HTML({
      'method':'get',
      'url':self.options.cover_url,
      'data':{'format':'html'},
      'onComplete':function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var block = self.get('block');
        var cover_block = block.getElementById('tl-cover');

        if (cover_block != null && responseHTML.length > 0) {
          cover_block.set('html', responseHTML);
          cover_block.getElement('img').setStyle('margin', '0');
          block.getElement('.cover').removeClass('no-cover');
          self.position.top = 0;
          self.position.left = 0;
          setTimeout(function () {
            self.reposition.start()
          }, '1000');
        }
      }
    }).send();
  },

  position:{
    top:0,
    left:0
  },

  reposition:{
    drag:null,
    active:false,
    start:function () {
      if (this.active) {
        return;
      }

      var self = document.tl_cover;
      var cover = self.get();
      this.active = true;
      self.getButton().fireEvent('click');

      self.getButton('save').removeClass('hidden');
      self.getButton().addClass('hidden');
      cover.addClass('draggable');
      var cont = cover.getParent();

      var verticalLimit = cover.getStyle('height').toInt() - cont.getStyle('height').toInt();

      var horizontalLimit = cover.getStyle('width').toInt() - cont.getStyle('width').toInt();

      var limit = {x:[0, 0], y:[0, 0]};

      if (verticalLimit > 0) {
        limit.y = [-verticalLimit, 0]
      }

      if (horizontalLimit > 0) {
        limit.x = [-horizontalLimit , 0]
      }

      this.drag = new Drag(cover, {
        limit:limit,
        onComplete:function (el) {
          self.position.top = el.getStyle('top').toInt();
          self.position.left = el.getStyle('left').toInt();
        }
      }).detach();

      this.drag.attach();
    },

    stop:function () {
      if (!this.active) {
        return;
      }

      var self = document.tl_cover;
      var cover = self.get();

      new Request.JSON({
        method:'get',
        url:self.options.position_url,
        data:{'format':'json', 'position':self.position},
        onRequest:function () {

        },
        onSuccess:function (response) {
          self.reposition.drag.detach();
          self.getButton('save').removeClass('active');
          self.getButton('save').addClass('hidden');
          self.getButton().removeClass('hidden');
          cover.removeClass('draggable');

          self.reposition.drag = null;
          self.reposition.active = false;
        }
      }).send();
    }
  },

  slideShow: function(url, guid, element, $page_id){
    var self = this;

    if(self.reposition.active){
      return;
    }

    new Wall.Slideshow(url, guid, element, $page_id);
  }
});