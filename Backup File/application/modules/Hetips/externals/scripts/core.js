var Hetips = {};
Hetips.profileUrlEnabled = false;

Hetips.attach = function()
{
    Hetips.elements = new Hetips.Storage();
    var prepare_links = [];

    $$('a:not(.Hetips_liketips):not(.hetips_tips_active)').each(function (item){
        var href = item.get('href');

        if(Hetips.profileUrlEnabled){
            var parent = /invite|albums|blogs|classfields|chat|forum|polls|groups|events|pages|videos|usernotes|music|register|logout|members|classified|faq/i;
            var user_href = href.replace(en4.core.baseUrl, "");
            if(user_href.indexOf('/')  === -1 && user_href.indexOf('javascript:')  === -1){
               if(!parent.test(user_href) && !item.getParent('.hetips-liketips') && item.getParent('.layout_user_home_links') === null
                   && item.getParent('.layout_user_home_photo') === null
                   && (item.get('class')||'').indexOf('menu_core_mini')){
                   item.addClass('hetips_tips_active');
                   item.setStyle('display', 'inline-block');
                   prepare_links[prepare_links.length] = item;
               }

            }
        }

        if(href && href.indexOf("content_") === -1 && item.getParent('.paginationControl') === null && item.getParent().get('.feed_item_option_share') === null && (item.get('class')||'').indexOf('like_suggest') === -1 && item.getParent('.like_list') === null && (item.get('class')||'').indexOf('wall_liketips') === -1  && item.getParent('.wall-liketips') === null  && item.getParent('.likes') === null
            && item.getParent('.he-tool-tip') === null && (item.getParent().get('class')||'').indexOf('he_like') && (item.getParent().get('class')||'').indexOf('_admin_list')){
            if(href.indexOf(en4.core.baseUrl + 'profile/') !== -1 || href.indexOf(en4.core.baseUrl + 'page/')!== -1 || href.indexOf(en4.core.baseUrl + 'group/') !== -1){
                if(!item.get('id') || (item.get('id') && item.get('id').indexOf('like_action_item') === -1 && item.get('id') && item.get('id').indexOf('owner_user_') === -1)){
                    if(!item.getParent('.hetips-liketips')){
                        if((item.get('class')||'').indexOf('buttonlink') === -1 && (item.get('class')||'').indexOf('menu_core_mini')){
                            if((item.getParent().get('class')||'').indexOf('index_browse') === -1 && item.getParent('.tl-block') === null  && item.getParent('.layout_user_home_links') === null && item.getParent('.layout_user_home_photo') === null || (item.getParent().get('class')||'').indexOf('he_') === 0 ){
                                item.addClass('hetips_tips_active');
                                item.setStyle('display', 'inline-block');
                                prepare_links[prepare_links.length] = item;
                            }
                        }
                    }
                }
            }
        }
    });

    for(var i = 0; prepare_links.length > i; i++){
        prepare_links[i]
      Hetips.elementClass(Hetips.LikeTips, prepare_links[i]);
    }
};

Hetips.Storage = new Class({

  items: {},

  initialize: function ()
  {
    this.items = new Hash();
  },

  add: function (key, object)
  {
    if (this.items[key]){
      return ;
    }
    this.items[key] = object;
    return this;
  },

  get: function (key)
  {
    var options = Array.prototype.slice.call(arguments || []);
    if (options.length > 1){
      key = options.join("_");
    }
    return this.items[key];
  },

  getAll: function ()
  {
    return this.items
  },

  remove: function (key)
  {
    this.items.erase(key);
    return this;
  }

});

Hetips.elementClass = function ()
{
  var options = Array.prototype.slice.call(arguments || []);
  var newClass = options[0];

  if (!newClass || ($type(newClass) != 'class')){
    return ;
  }
  var name = newClass.prototype.name;
  if (!name){
    return ;
  }

  if ($type($(options[1])) == 'element'){  

    var element = $(options[1]);
    var key = name + '_' + (window.$uid || Slick.uidOf)(element);
    var instance = Hetips.elements.get(key);

    if (instance){
      return instance;
    }

    newClass._prototyping = true;
    newClass.$prototyping = true;
    instance = new newClass();
    delete newClass._prototyping;
    delete newClass.$prototyping;

    newClass.prototype.initialize.apply(instance, options.slice(1));

    Hetips.elements.add(key, instance);

    return instance;

  } 
};

Hetips.TipFx = new Class({

  Implements: [Events, Options],
  options: {
    class_var: '',
    is_arrow: true,
    relative_element: null,
    delay: 1
  },
  timeout: null,
  mouseActive: false,

  initialize: function (element, options)
  {
    this.setOptions(options);
    this.element = $(element);
    this.createDom();
  },

  createDom: function ()
  {
    var self = this;


    if ($type($(this.element)) != 'element'){
      return ;
    }

    this.$container = new Element('div', {'class': 'hetips-tips ' + (this.options.class_var || ''), style: 'display:none'});
    this.$inner = new Element('div', {'class': 'container', 'html': (this.options.html || '')});

    if (!Hetips.isLightTheme()){
      this.$container.addClass('night_theme');
    }

    if (this.options.is_arrow){
      this.$arrow_container = new Element('div', {'class': 'arrow_container'});
      this.$arrow = new Element('div', {'class': 'arrow'});
    }

    this.$inner.inject(this.$container);

    if (this.options.is_arrow){
      this.$arrow.inject(this.$arrow_container);
      this.$arrow_container.inject(this.$container);
    }

    this.$container.inject(Hetips.externalDiv());

    window.addEvent('resize', function (){
      this.build();
    }.bind(this));

    this.build();



    var mouseover = function (){



      this.mouseActive = true;

      if (this.options.delay){
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(function (){
          this.build();
          this.$container.setStyle('display', '');
          this.fireEvent('mouseover');
        }.bind(this), this.options.delay);

      } else {
        this.build();
        this.$container.setStyle('display', '');
        this.fireEvent('mouseover');
      }

    }.bind(this);

    this.element.addEvent('mouseover', mouseover);
    this.$container.addEvent('mouseover', mouseover);



    var mouseout = function (e){

      this.mouseActive = false;

      if (this.options.delay){
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(function (){
          if (e && $(e.relatedTarget)){
          }
          this.$container.setStyle('display', 'none');
          this.fireEvent('mouseout');
        }.bind(this), this.options.delay);

      } else {
        this.$container.setStyle('display', 'none');
        this.fireEvent('mouseout');
      }


    }.bind(this);

    this.element.addEvent('mouseout', mouseout);
    this.$container.addEvent('mouseout', mouseout);


    this.fireEvent('complete');


  },

  build: function ()
  {
    if (!this.element.isVisible()){
      return ;
    }

    var dir = 'ltr';
    if ($$('html')[0]){
      dir = $$('html')[0].get('dir');
    }

    this.$container.setStyle('display', '');

    var e_pos = this.element.getCoordinates();
    var c_pos = this.$container.getCoordinates();
    var w_pos = this.options.relative_element || $$('body')[0].getSize();

    this.$container
      .setStyle('display', 'none')
      .setStyle('padding-bottom', 2);

    var rebuild = function (left, top){
      if (left){
        this.$container.setStyle('left', e_pos.left);
        if (this.options.is_arrow){
          var left = (e_pos.width/2-2.5).toInt();
          if (left>c_pos.width/2-2.5){
            left = 10;
          }
          this.$arrow.setStyle('left', left);
        }
      } else {
        this.$container.setStyle('left', e_pos.left-(c_pos.width-e_pos.width));
        if (this.options.is_arrow){
          var right = (e_pos.width/2-2.5).toInt();
          if (right>c_pos.width/2-2.5){
            right = 10;
          }
          this.$arrow.setStyle('right', right);
        }
      }
      if (top){
        this.$container.setStyle('top', e_pos.top-c_pos.height-1);
        if (this.options.is_arrow){
          this.$arrow_container.inject(this.$inner, 'after');
          this.$arrow.addClass('bottom');
        }

      } else {
        this.$container.setStyle('top', e_pos.top+e_pos.height+1);
        if (this.options.is_arrow){
          this.$arrow_container.inject(this.$inner, 'before');
          this.$arrow.addClass('top');
        }

      }

      this.fireEvent('build');

    }.bind(this);

    //rebuild((e_pos.left+c_pos.width < w_pos.x-10), (e_pos.top+c_pos.height < w_pos.y-10));
    if (this.options.top != undefined && this.options.left != undefined){
      rebuild(this.options.left, this.options.top);
    } else {
      rebuild(1,1);
    }


  }

});

Hetips.liketips_enabled = true;
Hetips.liketips = new Hetips.Storage();
Hetips.loaders = [];


Hetips.LikeTips = new Class({

  Extends: Hetips.TipFx,
  name: 'Hetips.LikeTips',
  is_loading: false,

  options: {
    class_var: 'hetips-liketips',
    html: '<div class="data"><span class="hetips-loading">&nbsp;</span>' + en4.core.language.translate('HETIPS_LOADING') + '</div>',
    delay: 250
  },
  
  
  initialize: function (element, options)
  {
    this.parent(element, options);

    var query = '';
    var result = element.href.replace('http://' + document.domain + '' + en4.core.baseUrl, '').replace('https://' + document.domain + '' + en4.core.baseUrl, '');

    if (result){
        result = result.split('/');
        result[0] = result[0] == 'profile' ? 'user' : result[0];
        query += result.length == 1 ? 'type/user/id/' + result[0] : 'type/'+ result[0] +'/id/' + result[1];
      }

    var self = this;
    var guid = query;
    var loadContent = function (){
      var element = Hetips.liketips.get(guid);
      if (element){ 
	  
        if (element.get('html') == ''){
          self.$container.setStyle('display', 'none');
          return ;
        }

        element.inject(Hetips.externalDiv());
        self.$inner.empty();
        element.inject(self.$inner);
		
        self.build();
        if (self.mouseActive){
          self.$container.setStyle('display', 'block');
        }
        return ;
      }

      if (Hetips.loaders.contains(guid)){
        return ;
      }
      Hetips.loaders[Hetips.loaders.length] = guid;

      Hetips.requestHTML(en4.core.baseUrl + 'hetips/index/index/' + query, function (html){

		var element = new Element('div', {'class': ''});
		element.set('html', html);
		
        Hetips.liketips.add(guid, element);
        loadContent();
        
      });
    };


    this.addEvent('mouseover', loadContent);

  }

});

Hetips.lightHexColor = function (hex)
{
  if (hex[0]=="#") hex=hex.substr(1);
  if (hex.length==3) {
    var temp=hex; hex='';
    temp = /^([a-f0-9])([a-f0-9])([a-f0-9])$/i.exec(temp).slice(1);
    for (var i=0;i<3;i++) hex+=temp[i]+temp[i];
  }
  var result = /^([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i.exec(hex);
  if (!result){
	  return 150;
  }
  var triplets = result.slice(1);

  return 0.213 * parseInt(triplets[0],16) + 0.715 * parseInt(triplets[1],16) + 0.072 * parseInt(triplets[2],16);
};


Hetips.lightDiv = null;

Hetips.isLightTheme = function ()
{
  if (!Hetips.lightDiv){
    Hetips.lightDiv = new Element('div', {style: 'display:none', 'class': 'hetips-theme-foreground'});
    Hetips.lightDiv.inject(Hetips.externalDiv());
  }
  var hex = Hetips.lightDiv.getStyle('background-color');
  if (hex == 'transparent'){
    hex = Hetips.lightDiv
      .removeClass('hetips-theme-foreground')
      .addClass('hetips-theme-background')
      .getStyle('background-color');
  }

  return Hetips.lightHexColor(hex)>100;

};

Hetips.$external_div = null;

Hetips.externalDiv = function (){
  if (!this.$external_div || $type(this.$external_div) != 'element'){
    this.$external_div = new Element('div', {'class': 'hetips-element-external'});
    this.$external_div.inject($$('body')[0]);
  }
  return this.$external_div;
};

Hetips.requestHTML = function (url, callback, $container, data)
{
  data = $merge({'format': 'html'}, data);

  Hetips.is_request = true;

  var request = new Request.HTML({
    'url': url,
    'method': 'get',
    'data': data,
    'evalScripts' : false,
    'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript){

      Hetips.is_request = false;

      if ($container && $type($container) == 'element'){
        $container.set('html', responseHTML);
      }

      if ($type(callback) == 'function'){
        callback(responseHTML);
      }

      eval(responseJavaScript);
     Smoothbox.bind($$('body')[0]);
      en4.core.runonce.trigger();


    }
  });
  request.send();
};