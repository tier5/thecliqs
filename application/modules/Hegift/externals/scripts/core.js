/* Id: core.js 03.02.12 12:26 TeaJay $ */

var gift_manager = {
  action_url: '',
  send_url: '',
  widget_url: '',
  page: 1,
  sort: 'recent',
  message: '',
  privacy: 1,
  gift_id: 0,
  contacts: false,
  action_name: 'received',
  options_url: '',
  user_id: 0,
  category_id: 0,

  init: function()
  {

  },

  open_form: function(object_id) {
    var self = this;
    self.gift_id = object_id;
    if (en4.user.viewer.id) {
      var params = {
        'm': 'hegift',
        'l': 'getFriends',
        'c': 'gift_manager.sendGift',
        't': 'Choose a friend to send a gift to:',
        'params': {
          'scriptpath':'application/modules/Hegift/views/scripts/',
          'gift_id': object_id,
          'button_label': 'Send',
          'ipp': 6,
          'user_id': self.user_id
        }
      };

      self.contacts = new HEContacts(params);
      self.contacts.box();
    } else {
      var url = self.send_url + '/gift_id/'+object_id;
      var $element = new Element('a', {'href': url});
      Smoothbox.open($element, {'mode': 'Request'});
    }
	},

  sendGift: function(contacts) {
    var self = this;
    new Request.JSON({
      'url': self.send_url,
      'method': 'post',
      'data': {
        'gift_id': self.gift_id,
        'recipients': contacts,
        'message': self.message,
        'privacy': self.privacy,
        'format': 'json'
      },
      onComplete: function(data) {
        if (data.result) {
          he_show_message(data.message, '', 5000);
          self.getGifts();
        } else {
          he_show_message(data.message, 'error', 5000);
        }
      }
    }).send();
  },

  getGifts: function()
  {
    var self = this;
    if (self.widget_url == '') {
      window.location.href = action_url;
    }
    self.addLoader();

    new Request.HTML({
      url : self.widget_url,
      data : {
        format : 'html',
        page : self.page,
        sort: self.sort,
        category_id: self.category_id,
        user_id: self.user_id
      },
      onSuccess:function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        var el = $$('.layout_hegift_browse_gifts');
        var tElement = new Element('div', {'html': responseHTML});
        el[0].innerHTML = tElement.getElement('.layout_hegift_browse_gifts').innerHTML;
        self.removeLoader();
      }
    }).post();
  },

  getRecievedAndSentGifts: function() {
    var self = this;
    self.addLoader();
    new Request.HTML({
      url : self.action_url,
      data : {
        format : 'html',
        action_name: self.action_name,
        page: self.page
      },
      onSuccess:function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        var el = $$('.sent_received_layout');
        var tElement = new Element('div', {'html': responseHTML});
        el[0].innerHTML = tElement.getElement('.sent_received_layout').innerHTML;
        self.removeLoader();
        self.initTips();
      }
    }).post();
  },

  getGiftsByOptions: function() {

    if(!confirm(en4.core.language.translate('HEGIFT_Do_This_Action_Confirm'))) {
      return 0;
    }
    var self = this;
    self.addLoader();
    new Request.JSON({
      url : self.options_url,
      data : {
        format : 'json',
        action_name: self.action_name,
        page: self.page
      },
      onComplete:function(data)
      {
        self.getRecievedAndSentGifts()
      }
    }).send();
  },

  setActionName: function(action_name) {

    if (action_name == 'received') {
      if(!$('received_gift_button').hasClass('active_gift_button')) {
        $('sent_gift_button').removeClass('active_gift_button');
        $('sent_gift_button').addClass('not_active_sent_gift_button');
        $('received_gift_button').removeClass('not_active_received_gift_button');
        $('received_gift_button').addClass('active_gift_button');
      }
    } else {
      if(!$('sent_gift_button').hasClass('active_gift_button')) {
        $('received_gift_button').removeClass('active_gift_button');
        $('received_gift_button').addClass('not_active_received_gift_button');
        $('sent_gift_button').removeClass('not_active_sent_gift_button');
        $('sent_gift_button').addClass('active_gift_button');
      }
    }

    var self = this;
    self.action_name = action_name;
    self.getRecievedAndSentGifts();
  },

  open_gift: function(url)
  {
    var self = this;
    var $element = new Element('a', {'href': url});
    Smoothbox.open($element);
  },

  setPage: function(page)
  {
    var self = this;
    self.page = page;
    if ($$('.layout_hegift_browse_gifts')[0]) {
      self.getGifts();
    } else {
      self.getRecievedAndSentGifts();
    }
  },

  setSort: function(sort)
  {
    var self = this;
    self.sort = sort;
    self.getGifts();
  },

  setCategory: function(category_id)
  {
    var self = this;
    var el = $('category-'+category_id);
    var old_el = $('category-'+self.category_id);
    self.category_id = category_id;
    self.getGifts();
    if (el && old_el) {
      old_el.removeClass('active');
      el.addClass('active');
    }
  },

  addLoader: function()
  {
    if (!$('gift_loader_browse').hasClass('hidden')) {
      return ;
    }

    if ($('browse_gifts')) {
      $('browse_gifts').setStyles({
        'opacity': 0.2,
        'filter': 'alpha(opacity=20)'
      });
    }
    $('gift_loader_browse').removeClass('hidden');
  },

  removeLoader: function()
  {
    if ($('browse_gifts')) {
      $('browse_gifts').setStyles({
        'opacity': 1,
        'filter': 'alpha(opacity=100)'
      });
    }
    $('gift_loader_browse').addClass('hidden');
  },

  initTips: function()
  {
    $$('.manage_gift_list_item').each(function (item) {
      Hegift.elementClass(Hegift.Tips, item, {'title': item.getElement('.item_info').get('html'), 'top': false, 'left': true});
    });
  },

  initProfileTips: function()
  {
    $$('.received_gift_info').each(function (item) {
      Hegift.elementClass(Hegift.Tips, item, {'title': item.getElement('.item_info').get('html'), 'top': false, 'left': true});
    });
  }
};


Hegift = {};

Hegift.TipFx = new Class({

  Implements:[Events, Options],
  options:{
    class_var:'',
    is_arrow:true,
    relative_element:null,
    delay:1
  },
  timeout:null,
  mouseActive:false,

  initialize:function (element, options) {
    this.setOptions(options);
    this.element = $(element);
    this.createDom();
  },

  createDom:function () {
    var self = this;


    if ($type($(this.element)) != 'element') {
      return;
    }

    this.$container = new Element('div', {'class':'hegifts-tips ' + (this.options.class_var || ''), style:'display:none'});
    this.$inner = new Element('div', {'class':'container', 'html':(this.options.html || '')});


    if (this.options.is_arrow) {
      this.$arrow_container = new Element('div', {'class':'arrow_container'});
      this.$arrow = new Element('div', {'class':'arrow'});
    }

    this.$inner.inject(this.$container);

    if (this.options.is_arrow) {
      this.$arrow.inject(this.$arrow_container);
      this.$arrow_container.inject(this.$container);
    }

    this.$container.inject(Hegift.externalDiv());

    window.addEvent('resize', function () {
      this.build();
    }.bind(this));

    this.build();


    var mouseover = function () {

      this.mouseActive = true;

      if (this.options.delay) {
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(function () {
          this.build();
          this.$container.setStyle('display', '');
          if (this.element.hasClass('manage_gift_list_item')) {
            this.element.addClass('active_gift_temp');
          }
          this.fireEvent('mouseover');
        }.bind(this), this.options.delay);

      } else {
        this.build();
        this.$container.setStyle('display', '');
        if (this.element.hasClass('manage_gift_list_item')) {
          this.element.addClass('active_gift_temp');
        }
        this.fireEvent('mouseover');
      }

    }.bind(this);

    this.element.addEvent('mouseover', mouseover);
    this.$container.addEvent('mouseover', mouseover);


    var mouseout = function (e) {

      this.mouseActive = false;

      if (this.options.delay) {
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(function () {
          if (e && $(e.relatedTarget)) {
          }
          this.$container.setStyle('display', 'none');
          this.fireEvent('mouseout');
          if (this.element.hasClass('manage_gift_list_item')) {
            this.element.removeClass('active_gift_temp');
          }
        }.bind(this), this.options.delay);

      } else {
        this.$container.setStyle('display', 'none');
        if (this.element.hasClass('manage_gift_list_item')) {
          this.element.removeClass('active_gift_temp');
        }
        this.fireEvent('mouseout');
      }
    }.bind(this);

    this.element.addEvent('mouseout', mouseout);
    this.$container.addEvent('mouseout', mouseout);


    this.fireEvent('complete');
  },

  build:function () {
    if (!this.element.isVisible()) {
      return;
    }

    var dir = 'ltr';
    if ($$('html')[0]) {
      dir = $$('html')[0].get('dir');
    }

    this.$container.setStyle('display', '');

    var e_pos;

    if ($type(this.options.relative_element) == 'element') {
      e_pos = this.options.relative_element.getCoordinates();
    } else {
      e_pos = this.element.getCoordinates();
    }


    var c_pos = this.$container.getCoordinates();

    this.$container
      .setStyle('display', 'none')
      .setStyle('padding-bottom', 2);

    var rebuild = function (left, top) {

      if (left) {
        this.$container.setStyle('left', e_pos.left);
        if (this.options.is_arrow) {
          var left = (e_pos.width / 2 - 2.5).toInt();
          if (left > c_pos.width / 2 - 2.5) {
            left = 10;
          }
          this.$arrow.setStyle('left', left);
        }
      } else {
        this.$container.setStyle('right', e_pos.left - (c_pos.width - e_pos.width));
        if (this.options.is_arrow) {
          var right = (e_pos.width / 2 - 2.5).toInt();
          if (right > c_pos.width / 2 - 2.5) {
            right = 10;
          }
          this.$arrow.setStyle('right', right);
        }
      }
      if (top) {
        this.$container.setStyle('top', e_pos.top - c_pos.height - 1);
        if (this.options.is_arrow) {
          this.$arrow_container.inject(this.$inner, 'after');
          this.$arrow.addClass('bottom');
        }

      } else {
        this.$container.setStyle('top', e_pos.top + e_pos.height + 1);
        if (this.options.is_arrow) {
          this.$arrow_container.inject(this.$inner, 'before');
          this.$arrow.addClass('top');
        }
      }

      this.fireEvent('build');

    }.bind(this);

//rebuild((e_pos.left+c_pos.width < w_pos.x-10), (e_pos.top+c_pos.height < w_pos.y-10));
    if (this.options.top != undefined && this.options.left != undefined) {
      rebuild(this.options.left, this.options.top);
    } else {

      if (dir == 'rtl') {
        rebuild(0, 1);
      } else {
        rebuild(1, 1);
      }
    }
  }
});

Hegift.Tips = new Class({

  Extends:Hegift.TipFx,

  name:'Hegift.Tips',

  initialize:function (element, options) {
    this.addEvent('onComplete', function () {
      var title = this.options.title || this.element.get('title');
      this.$inner.set('html', '<div class="data"><div class="title">' + title + '</div></div>');
      this.element.removeProperty('title');
    }.bind(this));

    this.parent(element, options);
  },

  setTitle:function (title) {
    this.$inner.set('html', '<div class="data"><div class="title">' + title + '</div></div>');
    this.build();
    if (this.mouseActive) {
      this.$container.setStyle('display', 'block');
    }
  }
});

Hegift.elementClass = function () {
  var options = Array.prototype.slice.call(arguments || []);
  var newClass = options[0];

  if (!newClass || ($type(newClass) != 'class')) {
    return;
  }
  var name = newClass.prototype.name;
  if (!name) {
    return;
  }

  if ($type($(options[1])) == 'element') {
    var element = $(options[1]);
    var key = name + '_' + (window.$uid || Slick.uidOf)(element);
    var instance = Hegift.elements.get(key);

    if (instance) {
      return instance;
    }

    newClass._prototyping = true;
    newClass.$prototyping = true;
    instance = new newClass();
    delete newClass._prototyping;
    delete newClass.$prototyping;

    newClass.prototype.initialize.apply(instance, options.slice(1));

    Hegift.elements.add(key, instance);

    return instance;
  }
};

Hegift.Storage = new Class({

  items:{},

  initialize:function () {
    this.items = new Hash();
  },

  add:function (key, object) {
    if (this.items[key]) {
      return;
    }
    this.items[key] = object;
    return this;
  },

  get:function (key) {
    var options = Array.prototype.slice.call(arguments || []);
    if (options.length > 1) {
      key = options.join("_");
    }
    return this.items[key];
  },

  getAll:function () {
    return this.items
  },

  remove:function (key) {
    this.items.erase(key);
    return this;
  }
});


Hegift.elements = new Hegift.Storage();

Hegift.$external_div = null;

Hegift.externalDiv = function (){
  if (!this.$external_div || $type(this.$external_div) != 'element'){
    this.$external_div = new Element('div', {'class': 'hegifts-element-external'});
    this.$external_div.inject($$('body')[0]);
  }
  return this.$external_div;
};