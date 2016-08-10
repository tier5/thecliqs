/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */

var TimelineBorn = new Class({
  Implements:[Options],
  options:{
    element_id:null,
    edit_buttons:null,
    loader_id:null,
    born_width:850,
    born_url:'',
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
    self.block = self.buttons.getParent('.born');

    self.changeButton = self.buttons.getElement('.born-change');
    self.saveButton = self.buttons.getElement('.save-positions');

    self.initEvents();
  },

  initEvents:function () {
    var self = this;

    if (!self.isAllowed()) return;

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
      'url':self.options.born_url,
      'data':{'format':'html'},
      'onComplete':function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var born_block = self.get('block').getElementById('tl-born');

        if (born_block != null && responseHTML.length > 0) {
          born_block.set('html', responseHTML);
          born_block.getElement('img').setStyle('margin', '0');
          born_block.setStyle('display', 'block');

          self.buttons.getElements('li').removeClass('hidden');
          var a = self.buttons.getElement('li.more a');
          if(a != null){
            a.set('text', en4.core.language.translate('TIMELINE_Edit Photo'));
          }


          self.get('block').getElement('.photo').removeClass('add');
          self.position.top = 0;
          self.position.left = 0;
          setTimeout(function () {
            self.reposition.start()
          }, '200');
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

      var self = document.tl_born;
      var born = self.get();
      this.active = true;
      self.getButton().getParent().addClass('hidden');

      self.getButton('save').removeClass('hidden');
      self.getButton().addClass('hidden');
      born.addClass('draggable');
      var cont = born.getParent();

      var verticalLimit = born.getStyle('height').toInt() - cont.getStyle('height').toInt();

      var horizontalLimit = born.getStyle('width').toInt() - cont.getStyle('width').toInt();

      var limit = {x:[0, 0], y:[0, 0]};

      if (verticalLimit > 0) {
        limit.y = [-verticalLimit, 0]
      }

      if (horizontalLimit > 0) {
        limit.x = [-horizontalLimit , 0]
      }

      this.drag = new Drag(born, {
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

      var self = document.tl_born;
      var born = self.get();

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
          born.removeClass('draggable');

          self.reposition.drag = null;
          self.reposition.active = false;

          self.getButton().getParent().removeClass('hidden');
        }
      }).send();
    }
  },

  slideShow: function(url, guid, element){
    var self = this;

    if(self.reposition.active){
      return;
    }

    new Wall.Slideshow(url, guid, element);
  }
});
