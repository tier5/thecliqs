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


var StoreCart = new Class({
  Implements:Chain,

  counterBoxes:'.store-counters',
  priceBoxes:'.store-prices',
  totalPriceCart:'store-cart-total-price',

  box:'store-cart-box',
  button:'store-cart-button',
  productBox:'store-cart-items',
  cartCounter:null,
  tempBox:'store-cart-temp',
  checkoutBox:'store-cart-checkout',
  emptyBox:'store-cart-empty',

  btSlider:'store-cart-slider',
  prSlider:'store-cart-items-slider',

  headline:'.headline.store',

  addCartUrl:'',
  removeCartUrl:'',
  checkoutUrl:'',
  wishUrl:'',
  prices_url:'',
  profile_widget_url:'',

  html:'',
  count:10000,
  show_cart:1,
  via_credits: 0,
  offer_id: 0,

  init:function () {
    var self = this;
    var $div = new Element('div', {'id':self.box});

    $div.set('html', self.html);
    $div.setStyle('opacity', 0);
    document.body.appendChild($div);

    self.counterBoxes = $$(self.counterBoxes);
    self.priceBoxes = $$(self.priceBoxes);
    self.totalPriceCart = $(self.totalPriceCart);

    if (!self.show_cart) return;

    self.box = $(self.box);
    self.button = $(self.button);
    self.productBox = $(self.productBox);
    self.cartCounter = self.button.getElement("div[class='store-counters']");
    self.tempBox = $(self.tempBox);
    self.checkoutBox = $(self.checkoutBox);
    self.emptyBox = $(self.emptyBox);

    self.headline = $$(self.headline);
    if (self.headline[0]) {
      window.addEvent('load', function() {
        self.headline[0].setStyle('display', '');
      });
    }

    self.initSlider();

    self.button.addEvent('click', function () {
      self.slide();
    });


    setTimeout(function () {
      self.checkCart();
    }, 1000);
  },

  initSlider:function () {
    var self = this;

    if (!self.show_cart) {
      return false;
    }

    $(self.prSlider).setStyle('height', self.productBox.getHeight() + 50);
    self.prSlider = new Fx.Slide(self.prSlider, {
      duration:'100',
      offset:{
        'y':'200',
        'x':'200'
      }
    }).slideOut();

    self.btSlider = new Fx.Slide(self.btSlider, {
      mode:'horizontal',
      duration:'100'
    }).slideOut().chain(function () {
        self.prSlider.element.setStyle('margin-right', '-300px');
      });
  },

  slide:function ($do) {
    var self = this;
    if (!self.show_cart) {
      return false;
    }

    if ($do == 'out' || ( $do != 'in' && self.btSlider.open)) {
      self.prSlider.slideOut().chain(function () {
        self.prSlider.element.setStyle('margin-right', '-300px');
        self.btSlider.slideOut().chain(function () {
          self.executeChains();
        });
      });
    } else if ($do == 'in' || ( $do != 'out' && !self.btSlider.open)) {
      self.btSlider.slideIn().chain(function () {
        self.prSlider.element.setStyle('margin-right', '0');
        self.prSlider.slideIn().chain(function () {
          self.executeChains();
        });
      });
    }

    return self;
  },

  executeChains:function () {
    var self = this;
    self["$chain"].each(function ($f) {
      if ($type($f) == 'function') $f();
    });
    self.clearChain();
    self.checkScroll();
    self.checkCart();
  },

  product:{
    add:function ($product_id, $values, $quantity) {
      var self = store_cart;
      var href = '';
      var $params = {
        'format':'json',
        'product_id':$product_id,
        'params':$values,
        'quantity':$quantity
      };

      new Request.JSON({
        'url':self.addCartUrl,
        'method':'post',
        'data':$params,
        'onRequest':function () {
          var buttons = $$('.product_button');
          buttons.addClass('adding');
          buttons.set({
            'href':'javascript://',
            'text':en4.core.language.translate('Loading ...')
          });
        },

        'onSuccess':function ($response) {
          if ($response.status) {
            self.updateMini();
            self.updatePrices($response.totalPrice);
            self.updateCounts($response.totalCount);

            if (self.show_cart) {

              self.chain(
                function () {
                  if (self.checkoutBox.hasClass('hidden')) {
                    self.emptyBox.addClass('hidden');
                    self.checkoutBox.removeClass('hidden');
                  }
                },
                function () {
                  self.tempBox.set('html', $response.html);
                  self.product.cleanCart();
                },
                function () {
                  self.tempBox.getChildren('li').each(function (li) {
                    li.inject(self.emptyBox, 'before');
                  });
                },
                function () {
                  self.slide('in');
                },
                function () {
                  self.prSlider.element.scrollTop = self.prSlider.element.scrollHeight;
                },
                function () {
                  setTimeout(function () {
                    new Fx.Tween('cartitem-id-' + $response.item_id).start('background-color', document.body.getStyle('background-color'));
                  }, '1000');
                }
              ).slide('in');
            }
          } else {
            if ($type($response.message) == 'string') {
              he_show_message($response.message, 'error');
            }
          }

          if ($('store-product-profile-details-' + $product_id)) {
            self.refreshWidget($product_id);
          }
        }
      }).post();
    },

    remove:function ($product_id, $item_id) {
      var self = store_cart;

      var $params = {
        'format':'json',
        'product_id':$product_id,
        'item_id':$item_id
      };

      new Request.JSON({
        'url':self.removeCartUrl,
        'method':'post',
        'data':$params,
        'onRequest':function () {
          if ($('store-product-profile-details-' + $product_id)) {
            var buttons = $$('.product_button');
            buttons.addClass('adding');
            buttons.set({
              'href':'javascript://',
              'text':en4.core.language.translate('Loading ...')
            });
          }

          if ($('remove_loader_' + $product_id + '_' + $item_id)) {
            $('remove_cart_' + $product_id + '_' + $item_id).setStyle('display', 'none');
            $('remove_loader_' + $product_id + '_' + $item_id).setStyle('display', 'block');
          }

          var $cartitem = $('cartitem-id-' + $item_id);
          if ($type($cartitem) == 'element') {
            $cartitem.addClass('remove_loader');
            $cartitem.getElement('.store-cart-item-remove').setStyle('display', 'none');
          }

        },

        'onSuccess':function ($response) {
          if ($response.status) {

            self.updateMini();
            self.updatePrices($response.totalPrice);
            self.updateCounts($response.totalCount);

            var $li = $('store-cart-product-' + $item_id);
            if (self.show_cart) {
              new Fx.Tween('cartitem-id-' + $item_id).start('opacity', 1, 0).chain(
                function () {
                  if ($response.totalCount == 0) {
                    self.product.cleanCart();

                    if (self.emptyBox.hasClass('hidden')) {
                      self.checkoutBox.addClass('hidden');
                      self.emptyBox.removeClass('hidden');
                    }
                  }

                  this.element.destroy();
                  self.slide('in');
                });
            }

            if ($li != undefined) {
              if ($li.hasClass('supported')) {
                self.getPrices();
              }
              new Fx.Tween($li).start('opacity', 1, 0).chain(function () {
                this.element.destroy();
              });
              if (product_manager.widget_url) {
                product_manager.setPage()
              }
            }

            if ($('store-product-profile-details-' + $product_id)) {
              self.refreshWidget($product_id);
            }
          }
        }
      }).post();
    },

    cleanCart: function(){
      var self = store_cart;
      self.productBox.getChildren().each(function(li){
        if( !li.hasClass('store-cart-empty')) li.destroy();
      });
    },

    addToWishList:function ($product_id) {
      var self = store_cart;
      var $params = {
        'format':'json',
        'product_id':$product_id,
        'do':'add'
      };

      new Request.JSON({
        'url':self.wishUrl,
        'method':'post',
        'data':$params,
        'onRequest':function () {
          var buttons = $$('.wishlist_button');

          buttons.addClass('adding');
          buttons.set({
            'href':'javascript://',
            'onClick': '',
            'text':en4.core.language.translate('Loading ...')
          });
        },

        'onSuccess':function ($response) {
          if ($response.status) {
            he_show_message($response.message);
            if ($('store-product-profile-details-' + $product_id)) {
              self.refreshWidget($product_id);
            }
          } else {
            if ($type($response.message) == 'string') {
              he_show_message($response.message, 'error');
            }
            var buttons = $$('.wishlist_button');

            buttons.removeClass('adding');
            buttons.set({
              'href':'javascript://',
              'text':en4.core.language.translate('STORE_Add to Wishlist')
            });
          }
        }
      }).post();
    },

    removeFromWishList:function ($product_id) {
      var self = store_cart;
      var $params = {
        'format':'json',
        'product_id':$product_id,
        'do':'remove'
      };

      new Request.JSON({
        'url':self.wishUrl,
        'method':'post',
        'data':$params,
        'onRequest':function () {
          var buttons = $$('.wishlist_button');

          buttons.addClass('adding');
          buttons.set({
            'href':'javascript://',
            'onClick': '',
            'text':en4.core.language.translate('Loading ...')
          });
        },

        'onSuccess':function ($response) {
          if ($response.status) {
            he_show_message($response.message);
            if ($('store-product-profile-details-' + $product_id)) {
              self.refreshWidget($product_id);
            }
          } else {
            if ($type($response.message) == 'string') {
              he_show_message($response.message, 'error');
            }
            var buttons = $$('.wishlist_button');

            buttons.removeClass('adding');
            buttons.set({
              'href':'javascript://',
              'text':en4.core.language.translate('STORE_Remove from Wishlist')
            });
          }
        }
      }).post();
    }
  },

  getPrices:function () {
    var self = this;

    new Request.JSON({
      'url':self.prices_url,
      'method':'post',
      'data':{
        'format':'json',
        'via_credits': self.via_credits,
        'offer_id': self.offer_id
      },
      'onSuccess':function ($response) {
        var div = new Element('div', {'html':$response.html});
        var elements = div.getElement('div').getElements('.content');
        $$('.content')[self.via_credits].innerHTML = elements[self.via_credits].innerHTML;
        $$('.offers_list_container').set('html', div.getElements('.offers_list_container')[0].innerHTML);
        Smoothbox.bind($('store-cart-checkout-container'));
      }
    }).post();
  },

  updatePrices:function ($price) {
    var self = this;

    self.priceBoxes.each(function ($box) {
      $box.set('text', $price);
    });
  },

  updateCounts:function ($count) {
    var self = this;

    self.counterBoxes.each(function ($box) {
      $box.set('text', $count);
    });
  },

  updateMini:function () {
    if (typeof mini_cart != 'undefined') {
      mini_cart.updateCart();
    }
  },

  refreshWidget:function ($product_id) {
    var self = this;

    new Request.HTML({
      'url':self.profile_widget_url,
      'data':{'format':'html'},
      'method':'post',
      onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $('store-product-profile-details-' + $product_id);
        if ($type(el) != 'element') return;
        var div = new Element('div', {'html':responseHTML});
        el.getParent('div').set('html', div.getElement('div').get('html'));
        toCart.init();
      }
    }).post();
  },

  checkout:function () {
    location.href = this.checkoutUrl;
  },

  checkScroll:function () {
    var self = this;

    if (!self.show_cart) return;

    self.prSlider.element.setStyle('height', self.productBox.getHeight() + 50);
  },

  checkCart:function () {
    var self = store_cart;

    if (!self.show_cart) return;

    if (parseInt(self.cartCounter.get('text')) == 0 && !self.btSlider.open) {
      self.box.setStyle('opacity', 0.2);
    } else {
      self.box.setStyle('opacity', 1);
    }
  }
});

var store_cart = new StoreCart();

en4.core.runonce.add(function () {
  store_cart.init();
});