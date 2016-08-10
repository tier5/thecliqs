/* Id: mini.js 5/21/12 3:32 PM mt.uulu $ */
var MiniCart = new Class({
  html:'',
  menuContainer:'core_menu_mini_menu',
  cart:'core_mini_cart',
  loader:null,
  menu:null,

  init:function () {
    var self = mini_cart;
    var $container = document.getElementById(this.menuContainer);

    if ($type($container) == 'element')
      self.cart = $container.getElement('.' + this.cart);

    if ($type(self.cart) == 'element')
      self.cart = self.cart.getParent('li');

    if ($type(self.cart) == 'element') {
      self.cart.set('html', this.html);
      self.loader = self.cart.getElement('.cartitems_loading');
      self.menu = self.cart.getElement('.cartitems_menu');
    }
  },
  toggle:function (event, element) {
    if (element.hasClass('updates_pulldown')) {
      element.removeClass('updates_pulldown');
      element.addClass('updates_pulldown_active');
      this.showItems();
    } else {
      element.removeClass('updates_pulldown_active');
      element.addClass('updates_pulldown');
    }
  },
  showItems:function (fromUpdate) {
    var self = mini_cart;

    if (fromUpdate != 1) {
      this.updateCart(1);
    }

    new Request.HTML({
      'url':en4.core.baseUrl + 'store/cart/pulldown',
      'data':{
        'format':'html',
        'page':1
      },
      'onComplete':function (responseTree, responseElements, responseHTML, responseJavaScript) {

        if (responseHTML) {
          // hide loading icon
          if (self.loader) self.loader.setStyle('display', 'none');

          self.menu.innerHTML = responseHTML;
          self.menu.addEvent('click', function (event) {
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;

            if(current_link.get('tag') != 'a'){
              current_link = current_link.getElement('a');
            }

            var forward_link;

            if (current_link != null && current_link.get('href')) {
              forward_link = current_link.get('href');
              window.location = forward_link;
            }
          });
        }
      }
    }).send();
  },
  updateCart:function (fromItems) {
    var self = mini_cart;
    if ($type(self.cart) != 'element')
      return;

    new Request.JSON({
      'url':en4.core.baseUrl + 'store/cart/update-mini',
      'data':{'format':'json'},
      'onSuccess':function (response) {
        if (response.status) {
          var $label = self.cart.getElement('a.label')
          var $viewCart = self.cart.getElement('.pulldown_options').getElement('a');
          if ($type($label) == 'element') {
            $label.set('text', response.text);

            if (response.itemCount <= 0) {
              $label.removeClass('new_updates');
              $viewCart.addClass('hidden');
            } else {
              $label.addClass('new_updates');
              $viewCart.removeClass('hidden');
            }

            if (fromItems != 1) {
              self.showItems(1);
            }
          }
        } else {
        }
      }
    }).send();
  }
});


var mini_cart = new MiniCart();

en4.core.runonce.add(function () {
  mini_cart.init();
});