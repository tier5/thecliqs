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

var store_page = {
  url: '',
	page_id: 0,
	page_num: 1,
	container_id: '',
	$container: {},
	product_id: 0,
	count_span: '.tab_layout_store_page_profile_products a span',
	store_tab: '.tab_layout_store_page_profile_products a',
	tabs_container_id: 'main_tabs',

	init: function() {
		var self = this;
		this.$container = $(this.container_id);
	},

	init_store: function(){
		tabContainerSwitch($$(this.store_tab)[0], 'generic_layout_container layout_store_page_profile_products');
	},

  view: function(product_id) {
		var self = this;
		var data = {'page_id':self.page_id, 'product_id': product_id, 'format':'json'};
		var request = self.request(data, this.url);

		self.product_id = product_id;

		if ($('store_page_loader')) {
			$('store_page_loader').removeClass('hidden');
		}
		request.send();
	},

  request: function(data, ajax_url){
		var self = this;
		data.no_cache = Math.random();
		return new Request.JSON({
			'url': ajax_url,
			'data': data,
			onSuccess : function(responseJSON)
      {
        var el = $$('.layout_store_page_profile_products .he-items');
        var tElement = new Element('div', {'html': responseJSON.html});
        el[0].innerHTML = tElement.getElement('.he-items').innerHTML;
        en4.core.runonce.trigger();

				if ($('store_page_loader')) {
		    	$('store_page_loader').addClass('hidden');
		    }

				self.init();
			}
		});
	},

  switch_type: function() {
		if ( $('price_type').value == 'simple' )
			$('list_price-wrapper').style.display='none';
		else
			$('list_price-wrapper').style.display='block';
	}
};

var paging = {
  page_num : 1,
	widget_url: '',
  page_id: 0,

	getProducts : function() {
		var self = this;

    if ($('store_page_loader')) {
			$('store_page_loader').removeClass('hidden');
		}

    new Request.JSON({
      url:self.widget_url+'/?p='+self.page_num+'&page_id='+self.page_id,
      method: 'post',
      data:
      {
        'format': 'json'
      },
      eval: true,
      onSuccess : function(responseJSON)
      {
        if ($('store_page_loader')) {
		    	$('store_page_loader').addClass('hidden');
		    }

        var el = $$('.layout_store_page_profile_products .he-items');
        var tElement = new Element('div', {'html': responseJSON.html});
        el[0].innerHTML = tElement.getElement('.he-items').innerHTML;
        en4.core.runonce.trigger();
      }
    }).send();
	},

	setPage : function(page) {
    if (page != undefined) this.page_num = page;
		this.getProducts();
	}
};