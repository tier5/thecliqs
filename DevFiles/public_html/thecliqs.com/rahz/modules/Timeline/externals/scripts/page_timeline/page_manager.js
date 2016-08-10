/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */

var PageTimelineManager = new Class({
  content:'global_content',
  cover:'tl-cover',
  options:'profile-options',
  about:'additional-row',

  init:function () {
    this.content = document.getElementById(this.content);
    this.cover = $(this.cover);
    this.options = $(this.options);
    this.about = $(this.about).getElement('div[class=about]');

    this.application.init();
    this.initEvents();
  },

  initEvents:function () {
    var self = this;

    self.about.addEvent('click', function () {
      $(this).getElement('div').toggleClass('active');
    });

    self.options.getElementById('mp-options').addEvent('click', function () {
      self.moreOptions(this);
    });

  },

  moreOptions:function (el) {
    if (!el.hasClass('active')) {
      el.addClass('active');
      $$('.bound-' + el.getProperty('id')).setStyle('display', 'block');
    }
  },

  application:{
    class_name:'application',
    items:[],
    available:'available-applications',
    hidden:[],
    addButton:'add-app',
    container:'layout_core_container_tabs',
    navigation:'tl-navigation',

    getContainer:function () {
      return this.container;
    },

    init:function () {
      var self = tl_manager;
      this.items = $$('.' + this.class_name);
      this.available = $(this.available);
      this.hidden = $$('.' + this.class_name + '.hidden');
      this.addButton = $$('.' + this.addButton);
      this.container = $(self.content).getElement('.' + this.container);
      this.navigation = $(this.navigation);
      this.initEvents();
    },

    initEvents:function () {
      var self = tl_manager;
      this.items.addEvent('click', function () {
        if (this.hasClass('more')) {
          self.application.more(this.getElement('div'));
        }
      });

      this.addButton.addEvent('click', function () {
        self.application.add($(this));
      });

      window.onhashchange = function (e) {
        if (document.location.hash == '') {
          self.content.getElement('.layout_middle').removeClass('apps');
        }
      }
    },

    more:function (el) {
      var self = tl_manager;
      var parent = el.getParent('.applications');

      if (parent == null) {
        return;
      }
      el.addClass('loading');
      el.toggleClass('hide');

      setTimeout(
        function () {
          parent.toggleClass('active');
          parent.getElements('.application.h').toggleClass('hidden');
          el.removeClass('loading');
        }, '200');
    },

    add:function ($el) {
      this.available.inject($el, 'after');
    }
  },

  fireTab:function (content_id) {
    var self = this;
    if (content_id == 'timeline') {
      document.location.hash = '';
      self.content.getElement('.layout_middle').removeClass('apps');
      return;
    }
    document.location.hash = content_id;

    var ul = self.content.getElement('.layout_core_container_tabs');
    var el = ul.getElement('.tab_' + content_id);

    if (el == null)
      return;
    if (self.content.getElementById("profile_albums") != null) {
      self.content.getElementById("profile_albums").getElements('a').removeEvents();
    }

    self.content.getElement('.layout_middle').addClass('apps');

    var options = self.application.navigation.getElement('.tl-options').getElements('a');
    var button = self.application.navigation.getElement('.tab_button_' + content_id);

    options.removeClass('active');

    if (button != null) {
      button.addClass('active');
    }
    if (!el.hasAttribute('onclick')) {
      el = el.getElement('a');
    }
    el.onclick.apply(el);
  }


});
