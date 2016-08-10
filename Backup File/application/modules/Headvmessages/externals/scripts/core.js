var composeInstance;

function removeFromToValue(id) {
  var toValues = headvmessagesCore.wrapper.getElement('input#toValues').value;
  var toValueArray = toValues.split(",");
  var toValueIndex = "";

  var checkMulti = id.search(/,/);

  if (checkMulti != -1) {
    var recipientsArray = id.split(",");
    for (var i = 0; i < recipientsArray.length; i++) {
      headvmessagesCore.removeToValue(recipientsArray[i], toValueArray);
    }
  }
  else {
    headvmessagesCore.removeToValue(id, toValueArray);
  }

  headvmessagesCore.wrapper.getElement('input#send_to').disabled = false;
}

var headvmessagesCore = {
  allowSmiles: false,
  allowEnter: false,
  maxRecipients: 10,
  composeNew: null,
  screen: null,
  loader: null,

  conversationsWrapper: null,
  messagesWrapper: null,

  wrapper: null,
  linkWrapper: null,
  link: null,

  friendsSuggestUrl: '',

  init: function () {
    var self = this;
  },

  prepare: function () {
    var self = this;
    var tmp = $('core_menu_mini_menu');
    if (tmp) {
      self.link = tmp.getElement('a.core_mini_messages');

      if (self.link) {
        self.link.set('href', 'javascript://');
        self.link.set('id', 'headvmessages-link');
        self.link.addEvent('click', function (e) {
          self.toggle();
        });

        self.linkWrapper = self.link.getParent();

        var tmpWrapper = new Element('span', {'class': 'headvmessages-span-wrapper'});

        self.wrapper = new Element('div', {'id': 'headvmessages-wrapper'});

        self.messagesWrapper = new Element('div', {'class': 'messages-list'});
        self.conversationsWrapper = new Element('div', {'class': 'conversations-list'});

        self.screen = new Element('div', {'class': 'headvmessages-screen'});
        self.loader = new Element('div', {'class': 'headvmessages-loader headvmessages-loader-circle'});

        self.wrapper.grab(self.conversationsWrapper);
        self.wrapper.grab(self.messagesWrapper);
        self.wrapper.grab(self.screen);
        self.wrapper.grab(self.loader);

        tmpWrapper.grab(self.wrapper);

        self.wrapper.set('style', 'display:none;');

        self.linkWrapper.grab(tmpWrapper);

        var close = new Element('a', {
          'class': 'hei hei-times headvmessages-close-popup'
        });
        close.addEvent('click', function () {
          self.toggle();
        });
        self.wrapper.grab(close);
      }
    }
  },

  onAir: false,

  toggle: function () {
    var self = this;
    if (!self.wrapper) {
      return;
    }

    if (self.onAir) {
      self.close();
      self.onAir = false;
    } else {
      self.wrapper.set('style', 'display: block;');
      self.open();
      self.onAir = true;
    }
  },

  openConversation: function (id) {
    var self = this;
    self.toggleLoader(true);
    new Request.HTML({
      method: 'get',
      data: {conversation_id: id},
      url: en4.core.baseUrl + 'headvmessages/index/conversation',
      evalScripts: false,
      onComplete: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('conversation-' + id) && !$('conversation-' + id).hasClass('headvmessage-active-conversation')) {
          $('conversation-' + id).addClass('headvmessage-active-conversation');
        }
        $('conversation-' + id).getElement('span.headvmessages-new-conversation').setStyle('display', 'none');
        self.messagesWrapper.set('html', responseHTML);
        eval(responseJavaScript);

        self.initCoreComposer();
        self.prepareComposerLinks();
        self.toggleLoader(false);
        self.initEnterSendEvent();
        self.initSendEvent(id);

        self.initSmiles();
      }
    }).send();
  },

  initConversationsEvents: function () {
    var self = this;

    self.conversationsWrapper.getElements('a.headvmessages-remove').removeEvents().addEvent('click', function (e) {
      var parent = $(this).getParent();
      parent.getElements('a.hei').setStyle('display', 'inline-block');
      $(this).setStyle('display', 'none');
    });

    self.conversationsWrapper.getElements('a.headvmessages-remove-cancel').removeEvents().addEvent('click', function (e) {
      var parent = $(this).getParent();
      parent.getElements('a.hei').setStyle('display', 'none');
      parent.getElements('a.headvmessages-remove').setStyle('display', 'inline-block');
    });

    self.conversationsWrapper.getElements('a.headvmessages-remove-confirm').removeEvents().addEvent('click', function (e) {
        var id = $(this).get('data-id');
        var parent = $(this).getParent('li');

        self.request('headvmessages/index/delete', {id: id}, function (response) {
          if (response.status) {
            if (parent.hasClass('headvmessage-active-conversation')) {
              self.messagesWrapper.set('html', '');
              console.log('wtf 1');
              if (!response.cCount) {
                console.log('wtf 2');
                self.showNoDialogsSpan();
              }
            }
            parent.remove();
          } else {
            parent.getElements('a.hei').setStyle('display', 'inline-block');
            $(this).setStyle('display', 'none');
          }
        }, 'post');
      }
    )
    ;

    var items = self.conversationsWrapper.getElements('li.list-group-item');
    items.each(function (el, i) {
      el.removeEvents().addEvent('click', function (e) {

        if (e.target.hasClass('hei')) {
          return;
        }

        var id = $(this).get('data-id');
        self.openConversation(id);
        items.removeClass('headvmessage-active-conversation');
        $(this).addClass('headvmessage-active-conversation');
      });
    });
    self.wrapper.getElement('a#headvmessages-compose-new').addEvent('click', function () {
      self.showComposeForm();
    });
  },

  prepareComposerLinks: function () {
    var self = this;
    try {
      self.wrapper.getElement('a#compose-photo-activator')
        .addClass('hei hei-camera')
        .set('text', '')
        .setStyle('background', 'none')
      ;

      self.wrapper.getElement('a#compose-link-activator')
        .addClass('hei hei-link')
        .set('text', '')
        .setStyle('background', 'none')
      ;

      self.wrapper.getElement('a#compose-music-activator')
        .addClass('hei hei-music')
        .set('text', '')
        .setStyle('background', 'none')
      ;

      self.wrapper.getElement('a#compose-video-activator')
        .addClass('hei hei-film')
        .set('text', '')
        .setStyle('background', 'none')
      ;
    } catch (e) {
    }
  },

  showComposeForm: function () {
    var self = this;
    self.toggleLoader(true);
    new Request.HTML({
      method: 'get',
      data: {},
      url: en4.core.baseUrl + 'headvmessages/index/compose',
      evalScripts: false,
      onComplete: function (responseTree, responseElements, responseHTML, responseJavaScript) {

        self.messagesWrapper.set('html', responseHTML);
        eval(responseJavaScript);

        self.initCoreComposer();
        self.initSmiles();
        self.prepareComposerLinks();
        self.initComposerEvents();
        self.toggleLoader(false);
      }
    }).send();
  },
  initSmiles: function () {
    var self = this;
    /*if (self.allowSmiles) {
      var t = self.wrapper.getElement('#messages-list-controls');
      var smile = new Element('a', {
        'id': 'compose-smile-activator',
        'class': 'compose-activator buttonlink hei hei-smile-o',
        'onclick': 'javascript:void(0);',
        'style': 'background: none;'
      });
      smile.addEvent('click', function () {
        self.showSmiles();
      });
      t.grab(smile);
    }*/
  },
  showSmiles: function () {
    console.log('show smiles');

    var self = this;

    new Request.HTML({
      url: en4.core.baseUrl + 'heemoticon/index/index?format=html',
      method:'get',
      evalScripts:false,
      onRequest: function () {
        self.toggleLoader(true);
      },
      onComplete: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        self.wrapper.getElement('#headvmessages-smiles').set('html', responseHTML);

        var cont = self.wrapper.getElements('.wall_data_comment');
        var opt = {
          autoHide: 1,
          fade: 1,
          className: 'scrollbar',
          proportional: true,
          proportionalMinHeight: 15,
          left: 295,
          top: 50
        };
        var myScrollable = new Scrollable(cont,opt);

        self.wrapper.getElements('.smiles_standart').addEvent('click', function () {
          console.log('smile click');
          /*composer.setContent(composer.getContent() + '&nbsp;' + $(this).get('rev') + '&nbsp;');
          composer.moveCaretToEnd();
           Heemoticons.hideSmile();*/
        });

        self.wrapper.getElements('.smiles_NEW').addEvent('click', function () {
          console.log('animated smile click');
          /*var contaner_img = body;
          tray.setStyle('display','block');

          self.makeLoading();
          var sticker_id = this.get('data-id');
          var req = new Request({
            method: 'get',
            url: en4.core.baseUrl +  'heemoticon/index/poststicker?format=html&sticker_id='+ sticker_id+'&comment_id='+0,
            onComplete: function (response) {
              var elem = new Element('a', {
                'class':'smiles_NEW'
              });
              var img = new Element('img', {
                'src':response.trim(),
                'sticker_id':sticker_id
              });
              img.inject(elem);
              contaner_img.set('html','');
              elem.inject(contaner_img);
              contaner_img.setStyle('display', 'block');
              var used_id = 0;
              var row = response.trim().split('?').pop();
              if(row){used_id=row.split('=').pop();}
              var delete_button = new Element('div', {
                'id': 'delete_' + 0,
                'class': 'wpClose hei hei-times delete_photo_in_comment_button',
                'style':'color:#fff'
              }).inject(contaner_img);

              this.emoticon_id =  sticker_id;
              this.type_emoticon = 'heemoticon_post';
              this.name = 'heemoticon_post';
              var data  = {
                'emoticon_id':sticker_id,
                'type':'heemoticon_post'
              }
              $H(data).each(function (value, key) {
                self.setFormInputValue(key, value);
              });
              self.ready();
              delete_button.addEvent('click', function(e){
                Heemoticons.deleteImageComposer(used_id,sticker_id,0);
              });
              Heemoticons.hideSmile();

            }
          }).send();*/
        });
        self.toggleLoader(false);
      },
      onFailure: function () {
        self.toggleLoader(false);
      },
      onCancel: function () {
        self.toggleLoader(false);
      },
      onException: function () {
        self.toggleLoader(false);
      }
    }).send();
  },

  initCoreComposer: function () {
    var mel = $('messages-list-controls');
    var tel = $('messages-list-controls-tray');

    if (!Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad()) {
      composeInstance = new Composer('hidden-body', {
        overText: false,
        menuElement: mel,
        trayElement: tel,
        baseHref: en4.core.baseUrl,
        hideSubmitOnBlur: false,
        allowEmptyWithAttachment: false,
        submitElement: 'messages-list-send',
        type: 'message'
      });
    }
    en4.core.runonce.trigger();
  },

  initEnterSendEvent: function () {
    var self = this;
    var text = self.wrapper.getElement('textarea#headvmessages-body');
    var send = self.wrapper.getElement('a#messages-list-send');

    var t = text.removeEvents();
    if (text && send) {
      text.removeEvents();
      text.addEvent('keypress', function (e) {
        if (e.key == 'enter') {
          if (self.allowEnter) {
            send.click();
            return false;
          }
        }
      });
    }
  },

  initSendEvent: function (id) {
    var self = this;

    self.wrapper.getElement('a#messages-list-send').addEvent('click', function () {
      var form = $(this).getParent('form');

      var data = self.collectForm(form);
      data.id = id;

      if (!$('headvmessages-body').value.trim().length) {
        self.markField($('headvmessages-body'));
        return;
      }

      self.request('headvmessages/index/reply', data, function (response) {
        var type = '';
        if (response.status) {
          self.openConversation(id);
        } else {
          type = 'error';
        }
        if (response.message) {
          he_show_message(response.message, type, 5000);
        }
      }, 'post');
    });
  },

  initComposerEvents: function () {
    var self = this;

    self.wrapper.getElement('a#messages-list-send').addEvent('click', function () {
      var form = $(this).getParent('form');

      var data = self.collectForm(form);

      if (!self.checkForm()) {
        return;
      }

      self.request('headvmessages/index/compose', data, function (response) {
        var type = '';
        if (response.status) {
          self.conversationsWrapper.set('html', response.conversationsHtml);
          self.initConversationsEvents();
          self.openConversation(response.id);
        } else {
          type = 'error';
        }
        if (response.message) {
          he_show_message(response.message, type, 5000);
        }
      }, 'post');
    });

    new Autocompleter.Request.JSON('send_to', self.friendsSuggestUrl, {
      'minLength': 1,
      'delay': 250,
      'selectMode': 'pick',
      'autocompleteType': 'message',
      'multiple': false,
      'className': 'message-autosuggest',
      'filterSubset': true,
      'tokenFormat': 'object',
      'tokenValueKey': 'label',
      'injectChoice': function (token) {
        if (token.type == 'user') {
          var choice = new Element('li', {
            'class': 'autocompleter-choices',
            'html': token.photo,
            'id': token.label
          });
          new Element('div', {
            'html': this.markQueryValue(token.label),
            'class': 'autocompleter-choice'
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
        else {
          var choice = new Element('li', {
            'class': 'autocompleter-choices friendlist',
            'id': token.label
          });
          new Element('div', {
            'html': this.markQueryValue(token.label),
            'class': 'autocompleter-choice'
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }

      },
      onPush: function () {
        if (self.wrapper.getElement('input#toValues').value.split(',').length >= self.maxRecipients) {
          self.wrapper.getElement('input#send_to').disabled = true;
        }
      }
    });
  },

  collectForm: function (form) {
    var formObjects = form.toQueryString().parseQueryString();
    return formObjects;
  },

  checkForm: function () {
    var self = this;

    var toValues = self.wrapper.getElement('input#toValues').value.trim();
    var subj = self.wrapper.getElement('input#headvmessages-subject').value;
    var body = self.wrapper.getElement('textarea#headvmessages-body').value;

    var result = true;

    if (!toValues.length) {
      self.markField(self.wrapper.getElement('input#send_to'));
      result = false;
    }
    if (!subj.length) {
      self.markField(self.wrapper.getElement('input#headvmessages-subject'));
      result = false;
    }
    if (!body.length) {
      self.markField(self.wrapper.getElement('textarea#headvmessages-body'));
      result = false;
    }

    return result;
  },

  markField: function (el) {
    var back = el.getStyle('background-color');
    el.setStyle('background-color', 'salmon');
    setTimeout(function () {
      el.setStyle('background-color', back);
    }, 2000);
  },

  removeFromToValue: function (id) {
    var self = this;
    var toValues = self.wrapper.getElement('input#toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    if (checkMulti != -1) {
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++) {
        self.removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else {
      self.removeToValue(id, toValueArray);
    }

    self.wrapper.getElement('input#send_to').disabled = false;
  },

  removeToValue: function (id, toValueArray) {
    var self = this;
    for (var i = 0; i < toValueArray.length; i++) {
      if (toValueArray[i] == id) toValueIndex = i;
    }

    toValueArray.splice(toValueIndex, 1);
    self.wrapper.getElement('input#toValues').value = toValueArray.join();
  },

  toggleLoader: function (mode) {
    var self = this;
    if (mode) {
      self.screen.setStyle('display', 'block');
      self.loader.setStyle('display', 'block');
    } else {
      self.screen.setStyle('display', 'none');
      self.loader.setStyle('display', 'none');
    }
  },

  showNoDialogsSpan: function () {
    var self = this;
    var span = new Element('span',
      {'class': 'headvmessages-no-active-dialogs',
        'text': en4.core.language.translate('HEADVMESSAGES_No active dialogs')
      }
    );
    span.addEvent('click', function (e) {
      self.showComposeForm();
    });
    self.messagesWrapper.grab(span);
  },

  /*openConversationFromNotification:function(id) {
   var self = this;

   self.request('headvmessages/index', {}, function (response) {
   if (response.status) {
   self.conversationsWrapper.set('html', response.html);
   self.initConversationsEvents();
   if (!response.cCount) {
   self.showNoDialogsSpan();
   }

   self.openConversation(id);
   }
   }, 'post');

   $$('html')[0].setStyle('overflow-y', 'hidden');
   if( $('store-cart-box') ) {
   $('store-cart-box').setStyle('z-index', '99');
   }
   },*/
  open: function () {
    var self = this;

    self.request('headvmessages/index', {}, function (response) {
      if (response.status) {
        self.conversationsWrapper.set('html', response.html);
        self.initConversationsEvents();
        if (!response.cCount) {
          self.showNoDialogsSpan();
        }
      }
    }, 'post');

    $$('html')[0].setStyle('overflow-y', 'hidden');
    if ($('store-cart-box')) {
      $('store-cart-box').setStyle('z-index', '99');
    }
  },

  close: function () {
    var self = this;
    if (!self.wrapper) {
      return;
    }
    $$('html')[0].setStyle('overflow-y', 'scroll');
    $('store-cart-box').setStyle('z-index', '100');
    self.wrapper.set('style', 'display: none;');
    self.conversationsWrapper.set('html', '');
    self.messagesWrapper.set('html', '');

    self.toggleLoader(false);

    self.onAir = false;
  },

  request: function (url, data, callback, method) {
    var self = this;
    data.format = 'json';
    new Request.JSON({
      url: en4.core.baseUrl + url,
      data: data,
      onRequest: function () {
        self.toggleLoader(true);
      },
      onSuccess: function (response) {
        callback(response);
        self.toggleLoader(false);
      },
      onFailure: function () {
        self.toggleLoader(false);
      },
      onCancel: function () {
        self.toggleLoader(false);
      },
      onException: function () {
        self.toggleLoader(false);
      },
      onComplete: function () {
        self.toggleLoader(false);
      }
    }).send();
  }
};
