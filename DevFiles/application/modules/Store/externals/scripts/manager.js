/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminFieldsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

var store_manager = {
  page_num:1,
  field_ids:{},
  view:'',
  sort:'recent',
  category:'',
  city:'',
  tag_id:'',
  tag_url:'',
  keyword:'',

  init:function () {
    var self = this;
    if ($('filter_form') != undefined) {
      var default_value = en4.core.language.translate("Search");
      $('keyword').value = default_value;
      $('submit').addEvent('click', function (e) {
        e.stop();
        self.page_num = 1;
        self.getPages();
      });
      $('keyword').addEvents({
        'focus':function () {
          if (this.value == default_value) {
            this.value = "";
          }
        },
        'blur':function () {
          if (this.value == "") {
            this.value = default_value;
          }
        }
      });
      $$('#filter_form div ul li').setStyle('display', 'none');
      $('keyword').getParent().setStyle('display', 'block');
      if ($('profile_type')) {
        $('profile_type').getParent().setStyle('display', 'block');
      }
      $('submit').getParent().setStyle('display', 'block');

      var $elements = $$('#filter_form div ul li input, #filter_form div ul li select, #filter_form div ul li textarea');

      if ($('profile_type')) {
        $('profile_type').addEvent('change', function () {
          var data = self.field_ids;
          for (var i = 0; i < $elements.length; i++) {
            var id = parseInt($elements[i].id.substr(6, 2));
            if (!id) {
              continue;
            }
            if (data[id] == this.value) {
              $elements[i].getParent().setStyle('display', 'block');
            } else {
              $elements[i].getParent().setStyle('display', 'none');
            }
          }
        });
      }
    }
    if (self.is(self.tag_id)) {
      self.getPages();
    }
  },

  getPages:function () {
    var self = this;

    if ($('store_loader_browse')) {
      $('store_loader_browse').removeClass('hidden');
    }

    if ($('filter_form') != undefined) {
      if ($('keyword').value == en4.core.language.translate('Search')) {
        $('keyword').value = '';
      }
      self.keyword = $('keyword').value;
      self.category = $('profile_type').value;
      if ($('keyword').value == '')
        $('keyword').value = en4.core.language.translate('Search')
    }

    var data = {
        'format':'html',
        'page':self.page_num,
        'view_mode':self.view,
        'sort':self.sort,
        'category':self.category,
        'city':self.city,
        'tag_id':self.tag_id,
        'keyword':self.keyword
    };

      $$('#filter_form div ul li input, #filter_form div ul li select, #filter_form div ul li textarea').each(function(item){
          if(null == item.name.match(/^(\d+)_/))
              return;
          data[item.name] = item.get('value');
      });

    new Request.HTML({
      url:self.widget_url,
      data: data,
      evalScripts:true,
      onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $$('.layout_store_store_browse');
        var tElement = new Element('div', {'html':responseHTML});
        el[0].innerHTML = tElement.getElement('.layout_store_store_browse').innerHTML;

        Smoothbox.bind(el[0]);

        if (self.tag_id) {
          if ($('tag_' + self.tag_id)) {
            $('store_tag_info').innerHTML = '<span class="bold">' + self.truncate($('tag_' + self.tag_id).getProperty('title'), 10) + '</span> ' + en4.core.language.translate('tag') + '. <a href="javascript:void(0)" class="bold" onClick="store_manager.setTag(0);">x</a>';
            $('store_tag_info').removeClass('hidden');
          }
        } else {
          $('store_tag_info').innerHTML = "";
          $('store_tag_info').addClass('hidden');
        }

        if (self.city) {
          var city_element = $$('.city_' + string_to_slug(self.city.trim()));
          if (city_element) {
            $('store_city_info').innerHTML = '<span class="bold">' + self.truncate(city_element[0].innerHTML, 10) + '</span> ' + en4.core.language.translate('city') + '. <a href="javascript:void(0)" class="bold" onClick="store_manager.setLocation(0);">x</a>';
            $('store_city_info').removeClass('hidden');
          }
        } else {
          $('store_city_info').innerHTML = "";
          $('store_city_info').addClass('hidden');
        }

        if (self.category) {
          if ($$('.category_' + self.category)[0] != undefined) {
            $('store_category_info').innerHTML = '<span class="bold">' + self.truncate($$('.category_' + self.category)[0].innerHTML, 10) + '</span> ' + en4.core.language.translate('category') + '. <a href="javascript:void(0)" class="bold" onClick="store_manager.setCategory(0);">x</a>';
            $('store_category_info').removeClass('hidden');
          }
        } else {
          $('store_category_info').innerHTML = "";
          $('store_category_info').addClass('hidden');
        }

        if ($('store_loader_browse')) {
          $('store_loader_browse').addClass('hidden');
        }

        en4.core.runonce.trigger();
      }
    }).post();
  },

  setSort:function (sort) {
    this.sort = sort;
    this.getPages();
  },

  setView:function (view, el) {
    $$('.store-view-types').removeClass('active');
    if ($type(el) == 'element') {
      el.addClass('active');
    }

    if ($('stores-items') != null) {
        if (view == 'list') {
        $('stores-items').setStyle('display', 'block');
        $('map_canvas').setStyle('position', 'absolute');
        $('map_canvas').setStyle('top', '10000px');
      } else {
        $('stores-items').setStyle('display', 'none');
        $('map_canvas').setStyle('position', 'relative');
        $('map_canvas').setStyle('top', '0px');
      }
    }
    this.view = view;
  },

  setCategory:function (category) {
    if (category == 0) {
      category = '';
    }
    if ($('filter_form') != undefined) {
      $('profile_type').value = category;
    }
    this.category = category;
    this.getPages();
  },

  setLocation:function (city) {
    this.page_num = 1;
    this.city = city;
    this.getPages();
  },

  setPage:function (page) {
    this.page_num = page;
    this.getPages();
  },

  setTag:function (tag_id) {
    if (this.is(this.tag_url)) {
      window.location = this.tag_url + '/tag_id/' + tag_id;
    } else {
      this.page_num = 1;
      this.tag_id = tag_id;
      this.getPages();
    }
  },

  is:function (x) {
    return !(x == '' || x == 0 || x == undefined || x === false)
  },

  truncate: function(str, num) {
    str = str.trim();
    var len = str.length;
    if(num+3 >= len)
      return str;

    return (str.substr(0, num) + '...');
  }
};

var product_manager = {
  page_num:1,
  widget_url:'',
  content_id:'',
  view:'',
  widget_element:'',
  sort:'recent',
  tag_id:0,
  category:0,
  sub_category:0,
  tag_url:'',
  via_credits: 0,
  cart_loader: 0,

  init:function () {
    var self = this;
    if ($('filter_form') != undefined) {
      if ($('search').value || self.isNumber($('max_price').value) || self.isNumber($('min_price').value) || $('profile_type').value) {
        self.getProducts();
      }
      $('submit').addEvent('click', function (e) {
        e.stop();
        self.page_num = 1;
        self.getProducts();
      });
    }

    if (self.is(self.tag_id) || self.is(self.category)) {
      self.getProducts();
    }
  },

  getProducts:function () {
    var self = this;

    if ($('store_loader_browse')) {
      $('store_loader_browse').removeClass('hidden');
    }

    if ($('cart_loader_browse') && self.cart_loader) {
      $('cart_loader_browse').removeClass('hidden');
      if ($('store_cart_items')) {
        $('store_cart_items').setStyle('opacity', 0.2);
      }
      self.cart_loader = 0;
    }

    var query = '';
    if ($('filter_form') != undefined) {
      query += $('filter_form').toQueryString();
    }
    new Request.HTML({
      url:self.widget_url + '/?' + query,
      method:'post',
      data:{
        'page':self.page_num,
        'content_id':self.content_id,
        'sort':self.sort,
        'v':self.view,
        'tag_id':self.tag_id,
        'cat':self.category,
        'sub_cat':self.sub_category,
        'via_credits': self.via_credits,
        'format':'html'
      },
      eval:true,
      onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('store_loader_browse')) {
          $('store_loader_browse').addClass('hidden');
        }
        var el = $$('.layout_middle > ' + self.widget_element);
        var tElement = new Element('div', {'html':responseHTML});

        if (el[0] != undefined) {
          el[0].innerHTML = tElement.getElement(self.widget_element).innerHTML;
        }

        Smoothbox.bind(el[0]);

        var is_form = false;

        if (
          $type($('search')) == 'element' && $('search').value ||
            $type($('max_price')) == 'element' && self.isNumber($('max_price').value) ||
            $type($('min_price')) == 'element' && self.isNumber($('min_price').value) ||
            $type($('profile_type')) == 'element' && ($('profile_type').value && !self.is(self.category))) {
          is_form = true;
        }

        if ($type($('product_tag_info')) == 'element') {
          if (self.is(self.tag_id)) {
            if ($('tag_' + self.tag_id)) {
              $('product_tag_info').innerHTML = '<span class="bold">'+self.truncate($('tag_' + self.tag_id).getProperty('title'), 10)+'</span> '+en4.core.language.translate('tag')+'. <a href="javascript:void(0)" class="bold" onClick="product_manager.setTag(0);">x</a>';
              $('product_tag_info').removeClass('hidden');
            }
          } else {
            $('product_tag_info').innerHTML = "";
            $('product_tag_info').addClass('hidden');
          }
        }

        if ($type($('product_category_info')) == 'element') {
          if (self.is(self.category)) {
            if ($('category_' + self.category)) {
              var $sub_category = '';
              if (self.is(self.sub_category)) {
                $sub_category = ' -> '+self.truncate($('sub_category_' + self.sub_category).innerHTML, 10)+'<a href="javascript:void(0)" class="bold" onClick="product_manager.setCategory('+self.category+', 0)"> x</a>';
              }
              $('product_category_info').innerHTML = '<span class="bold">'+self.truncate($('category_' + self.category).innerHTML, 10)+'</span> '+en4.core.language.translate('category')+'. <a href="javascript:void(0)" class="bold" onClick="product_manager.setCategory(0, 0);">x</a>' + $sub_category;
              $('product_category_info').removeClass('hidden');
            }
          } else {
            $('product_category_info').innerHTML = "";
            $('product_category_info').addClass('hidden');
          }
        }

        if ($type($('product_form_info')) == 'element') {
          if (is_form) {
            $('product_form_info').innerHTML = '<a href="javascript:void(0)" onClick="product_manager.reset_form();">' + en4.core.language.translate('STORE_Reset product search') + '</a>';
            $('product_form_info').removeClass('hidden');
          } else {
            $('product_form_info').innerHTML = "";
            $('product_form_info').addClass('hidden');
          }

          if ($('store_loader_browse')) {
            $('store_loader_browse').addClass('hidden');
          }

          if ($('cart_loader_browse')) {
            $('cart_loader_browse').addClass('hidden');
            if ($('store_cart_items')) {
              $('store_cart_items').setStyle('opacity', 1);
            }
          }
        }

        en4.core.runonce.trigger();
      }
    }).send();
  },

  setSort:function (sort) {
    this.page_num = 1;
    this.sort = sort;
    if (this.is(this.category) && $('filter_form')) {
      $('profile_type').value = '';
    }
    this.getProducts();
  },

  setView:function (view, el) {
    $$('.store-view-types').removeClass('active');
    if ($type(el) == 'element') {
      el.addClass('active');
    }

    if (view == 'icons') {
      $('stores-icons').setStyle('display', 'block');
      $('stores-items').setStyle('display', 'none');
    } else if (view == 'list') {
      $('stores-items').setStyle('display', 'block');
      $('stores-icons').setStyle('display', 'none');
    }
    this.view = view;
  },

  setPage:function (page) {
    if (page != undefined) {
      this.cart_loader = 1;
    }
    if (this.is(page)) this.page_num = page;
    this.getProducts();
  },

  setTag:function (tag_id) {
    if (this.is(this.tag_url)) {
      window.location = this.tag_url + '/tag_id/' + tag_id;
    } else {
      this.page_num = 1;
      this.tag_id = tag_id;
      if (this.is(this.category) && $('filter_form')) {
        $('profile_type').value = '';
      }
      this.getProducts();
    }
  },

  setCategory:function (cat_id, subCat_id) {
    this.page_num = 1;
    this.category = cat_id;
    this.sub_category = subCat_id;
    if ($('filter_form')) {
      $('profile_type').value = '';
    }
    this.getProducts();
  },

  reset_form:function () {
    $('search').value = '';
    $('min_price').value = en4.core.language.translate('STORE_min');
    $('max_price').value = en4.core.language.translate('STORE_max');
    $('profile_type').value = '';
    this.page_num = 1;
    this.getProducts();
  },

  isNumber:function (o) {
    return !isNaN(o - 0);
  },

  is:function (x) {
    return !(x == '' || x == 0 || x == undefined || x === false)
  },

  truncate: function(str, num) {
    str = str.trim();
    var len = str.length;
    if(num+3 >= len)
      return str;

    return (str.substr(0, num) + '...');
  },

  switchCheckoutForm: function(via_credits) {
    if (this.via_credits == via_credits) {
      return ;
    }
    this.via_credits = via_credits;
    this.cart_loader = 1;
    this.getProducts();
    store_cart.via_credits = via_credits;
    store_cart.getPrices();
  },

  useOffer: function(offer_id) {
    store_cart.offer_id = offer_id;
    store_cart.getPrices();
  }
};

var wishlist = {
  page:1,
  widget_url:'',
  id:0,

  getWishlist: function() {
    var self = this;
    new Request.HTML({
      url: self.widget_url,
      data: {
        'format': 'html',
        'page': self.page,
        'id': self.id
      },
      onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $$('.layout_store_profile_wish_list');
        var tElement = new Element('div', {'html':responseHTML});
        el[0].innerHTML = tElement.getElement('.layout_store_profile_wish_list').innerHTML;
      }
    }).post();
  },

  setPage: function(page) {
    this.page = page;
    this.getWishlist();
  }
};