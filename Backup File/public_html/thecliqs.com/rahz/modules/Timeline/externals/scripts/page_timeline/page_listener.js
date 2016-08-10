/**
 * Created by JetBrains PhpStorm.
 * User: mt.uulu
 * Date: 1/20/12
 * Time: 12:46 PM
 */

var TimelineListener = new Class({
  Implements:[Options],
  options:{
    class_name:'',
    block_id:null
  },
  block:null,
  items:[],

  initialize:function (options) {
    if (!('class_name' in options)) {
      return false;
    }

    this.setOptions(options);
  },

  init:function () {
    var self = this;

    if (self.options.block_id != null) {
      self.block = $(self.block_id);
    } else {
      self.block = document;
    }

    if (self.block == null) {
      return false;
    }

    self.items = self.block.getElements('.' + self.options.class_name);
    this.initEvents();
  },

  initEvents:function () {
    var self = this;

    self.block.addEvent('click', function (el) {

      var div = el.target.getParent('div#timeline');

      if(div != null) {
        el.target = div;
      }

      self.items.each(function (item) {
        self.deactivate(item, el.target);
      });

      if (el.target.getNext() != null && el.target.getNext().hasClass(self.options.class_name)) {
        if(!el.target.hasClass('active')){
          el.target.addClass('active')
        }
        el.target.getNext().setStyle('display', 'block');
      }
    });
  },


  deactivate:function (listener, target) {
    var self = this;

    if (self.hasParent(target, self.options.class_name)) {
      return;
    }
    if (target.getProperty('id') != null && listener.hasClass('bound-' + target.getProperty('id'))) {
      return;
    }

    var bound = self.get_full_class('bound-', listener);
    if (bound.length > 0) {
      var boundEl = $(bound.substr(6));

      if (boundEl != null && boundEl.hasClass('active')) {
        boundEl.removeClass('active');
      }
    }

    listener.setStyle('display', 'none');
  },

  hasParent:function (el, str) {
    do {
      if (el.getParent() == null) {
        return false;
      }
      if (el.getParent().hasClass(str)) {
        return true;
      }
    } while (el = el.getParent());

    return false;
  },

  get_full_class:function (str, el) {

    if ($type(str) != 'string') {
      return false;
    }

    var c = '';
    if ($type(el) == 'element') {
      c = el.className;
    } else
    if ($type(el) == 'string') {
      c = el;
    }

    if (c.length <= 0) {
      return false;
    }

    var haystack = c.split(' ');
    for (var i = 0; i < haystack.length; i++) {
      if (haystack[i].substr(0, str.length) === str) {
        return haystack[i];
      }
    }

    return false;
  }
});