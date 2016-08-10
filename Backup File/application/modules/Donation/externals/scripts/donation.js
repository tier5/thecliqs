/**
 * Created with JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 20.08.12
 * Time: 15:08
 * To change this template use File | Settings | File Templates.
 */

var donation = {

  page_id: 0,

  page_num: 1,

  itemCountPerPage: 10,

  type: 'charity',

  url: {},

  container_id: '',

  $container: '',

  donation_id: 0,

  count_span: '.tab_layout_donation_page_profile_donations a span',

  donation_tab: '.tab_layout_donation_page_profile_donations a',

  tabs_container_id: 'main_tabs',

  loader_id: 'donation_loader',

  $loader: '',

  donation_options: 'page_donation_options',

  $page_menus:'',

  $charity_menu: '',

  $project_menu: '',

  init: function(){
    var self = this;
    self.$loader = $(this.loader_id);
    self.$container = $(this.container_id);

    if($(self.donation_options)){
      self.$page_menus = $(self.donation_options).getElement('ul').getElements('li');

      if(self.$page_menus && self.$page_menus.length > 0){
        self.$charity_menu = self.$page_menus[0];

        if(self.$page_menus.length > 1){
          self.$project_menu = self.$page_menus[1];
        }
        else{
         self.$project_menu = self.$page_menus[0];
        }
      }
    }
  },

  init_donation: function(){
    tabContainerSwitch($$(this.donation_tab)[0], 'generic_layout_container layout_donation_page_profile_donations');
  },

  charity_list: function(){

    if(this.type == 'project' || this.type == 'all'){
      this.page_num = 1;
    }
    this.type = 'charity';
    var self = this;

    self.set_active_menu(this.type);

    var data = {
      'page_id': self.page_id,
      'format': 'json',
      'page': self.page_num,
      'itemCountPerPage': self.itemCountPerPage
    };
    var request = this.request(this.url.browse_charity, data);

    self.show_loader();

    request.send();
  },

  project_list: function(){

    if(this.type == 'charity' || this.type == 'all'){
      this.page_num = 1;
    }
    this.type = 'project';
    var self = this;

    self.set_active_menu(this.type);

    var data = {
      'page_id': self.page_id,
      'format': 'json',
      'page': self.page_num,
      'itemCountPerPage': self.itemCountPerPage
    };
    var request = this.request(this.url.browse_project, data);

    self.show_loader();

    request.send();
  },

  manage_list: function(){
    if(this.type == 'charity' || this.type == 'project'){
      this.page_num = 1;
    }
    this.type = 'all';
    var self = this;
    var data = {
      'page_id': self.page_id,
      'format': 'json',
      'page': self.page_num,
      'itemCountPerPage': self.itemCountPerPage
    };
    var request = this.request(this.url.manage_donations, data);

    self.show_loader();

    request.send();
  },

  show_loader: function(){
    this.$loader.removeClass('hidden');
  },

  hide_loader: function(){
    this.$loader.addClass('hidden');
  },

  request: function(ajax_url, data){
    var self = this;
    data.no_cache = Math.random();
    return new Request.JSON({
      'url': ajax_url,
      'method': 'post',
      'data': data,
      onSuccess: function(response){
        self.$container.innerHTML = response.html;
        if (response.eval){
          eval(response.eval);
        }
        self.hide_loader();
        self.init();
      }
    });
  },

  set_page: function(page){
    this.page_num = page;
    if(this.type == 'project'){
      this.project_list();
    }
    else if(this.type == 'charity'){
      this.charity_list();
    }
    else{
      this.manage_list();
    }
  },

  set_active_menu: function(type){
    var self = this;
    if(type == 'charity'){
      if(self.$project_menu && self.$project_menu.hasClass('donation-page-active-menu')){
        self.$project_menu.removeClass('donation-page-active-menu');
      }
      if(self.$charity_menu && !self.$charity_menu.hasClass('donation-page-active-menu')){
        self.$charity_menu.addClass('donation-page-active-menu');
      }
    }
    else{
      if(self.$charity_menu && self.$charity_menu.hasClass('donation-page-active-menu')){
        self.$charity_menu.removeClass('donation-page-active-menu');
      }
      if(self.$project_menu && !self.$project_menu.hasClass('donation-page-active-menu')){
        self.$project_menu.addClass('donation-page-active-menu');
      }
    }
  }
};
