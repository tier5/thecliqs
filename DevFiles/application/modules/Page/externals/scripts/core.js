/* $Id: core.js 2010-08-31 16:02 idris $ */
// Timeline Page
function changePageProfileType(url) {
  new Request.JSON({
      url: url,
      method:'post',
      onSuccess:function (response) {
        //console.log(response);
        if (response) {
          if (!response.error) {
            window.location.reload();
          } else {
          }
        }
      }
    }
  ).send();
}

// Timeline Page
function string_to_slug(str)
{
  str = str.replace(/^\s+|\s+$/g, '')
    .replace(/[^a-zA-Z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .toLowerCase();

  return str;
};

function toggle_page_edit_tab(link_hide, link_show, tab_id, tab_desc_id, visible)
{
  if (visible){
    $(tab_id).setStyle('display', 'block');
    $(tab_id).removeClass('hidden');
    $(tab_desc_id).setStyle('display', 'none');
    $(tab_desc_id).addClass('hidden');
  } else {
    $(tab_id).setStyle('display', 'none');
    $(tab_id).addClass('hidden');
    $(tab_desc_id).setStyle('display', 'block');
    $(tab_desc_id).removeClass('hidden');
  }
  $(link_hide).setStyle('display', 'none');
  $(link_hide).addClass('hidden');
  $(link_show).setStyle('display', 'inline');
  $(link_show).removeClass('hidden');
};

var page = {
  page_id: 0,
  ajax_url: '',
  block: false,
  note: '',
  empty_note: '',

  init: function(){
    var self = this;
    $$('.team_title_input').addEvent('blur', function(){
      var admin_id = parseInt(this.id.substr(18));
      var title = this.value;
      if ($('admin_title_'+admin_id).innerHTML == title){
        self.hide_admin_edit(admin_id);
        self.show_admin_info(admin_id);
      }else{
        self.change_title(admin_id, title);
      }
    });
  },

  prepare_post: function(page_id){
    var self = this;
    this.page_id = page_id;
    if (!this.note) {
      this.note = '';
    }
    this.edit_mode(true);
  },

  edit_mode: function(flag){
    if (flag){
      $('profile_note_link').setStyle('display', 'none');
      $('profile_note_textarea').setStyle('display', 'block');
      $('profile_note_text').setStyle('display', 'none');
      $$('#profile_note_textarea textarea')[0].value = this.note;
      $$('#profile_note_textarea textarea')[0].focus();
    }else{
      $('profile_note_link').setStyle('display', 'block');
      $('profile_note_textarea').setStyle('display', 'none');
      if ($('profile_note_text').innerHTML){
        $('profile_note_text').setStyle('display', 'block');
      }
    }
  },

  getPages: function() {
    var params = {
      'm': 'page',
      'l': 'getPages',
      'c': 'page.addFavorites',
      't': en4.core.language.translate('Choose pages to add to favorites list'),
      'params': {
        'approved': 1,
        'favorite': en4.core.subject.id,
        'team_id': en4.user.viewer.id
      }
    };

    var contacts = new HEContacts(params);
    contacts.box();
  },

  see_all_checkins: function(user_id, page_id) {
    he_list.box('checkin', 'getCheckinsUsersByPage', 'Members checked-in this place', {'user_id': user_id, 'page_id': page_id, 'disable_list1': 1, 'disable_list2': 1});
  },

  close: function() {
    window.parent.Smoothbox.close();
  },

  addFavorites: function(contacts) {
    new Request.JSON({
      'url': en4.core.baseUrl + 'page-team/add-favorites/' + en4.core.subject.id,
      'method': 'post',
      'data': {
        'favorites': contacts,
        'format': 'json'
      },
      onSuccess: function(response) {
        he_show_message(response.message, response.type, 3500);
      }
    }).send();
  },

  see_all: function(object, object_id) {
    var params = {
      'm': 'page',
      'l': 'getFavorites',
      'c': 'page.deleteFavorites',
      't': 'Choose pages to delete from favorites list',
      'params': {
        'object': object,
        'page_id': object_id,
        'button_label': 'Delete'
      }
    };

    var contacts = new HEContacts(params);
    contacts.box();
  },

  deleteFavorites: function(contacts) {
    new Request.JSON({
      'url': en4.core.baseUrl + 'page-team/delete-favorites/' + en4.core.subject.id,
      'method': 'post',
      'data': {
        'favorites': contacts,
        'format': 'json'
      },
      onSuccess: function(response) {
        he_show_message(response.message, response.type, 3500);
        window.location.reload();
      }
    }).send();
  },

  change_title: function(admin_id, title){
    var self = this;

    if (this.block){
      return ;
    }
    this.block = true;

    $('admin_title_input_'+admin_id).disabled = true;
    new Request.JSON({
      'url' : self.ajax_url,
      'method' : 'post',
      'data' : {
        'admin_id' : admin_id,
        'title' : title,
        'format': 'json',
        'task' : 'change_title',
        'page_id' : self.page_id
      },
      onSuccess : function(response) {
        $('admin_title_'+admin_id).innerHTML = title;
        self.hide_admin_edit(admin_id);
        self.show_admin_info(admin_id);
        self.block = false;
        $('admin_title_input_'+admin_id).disabled = false;
      }
    }).send();
  },

  post_note: function(note){
    var self = this;
    if (note.trim() == this.note){
      self.edit_mode(false);
      return ;
    }
    $$('#profile_note_textarea textarea')[0].disabled = true;
    new Request.JSON({
      'url': self.ajax_url+'?page='+self.page_id,
      'method': 'post',
      'data': {
        'note': note,
        'task' : 'post_note',
        'format': 'json'
      },
      onSuccess: function(response){
        self.note = note;
        if (!response.result){
          $('profile_note_text').innerHTML = en4.core.language.translate("There was error.");
        }else{
          if (self.note.trim() == ''){
            $('profile_note_text').innerHTML = self.empty_note;
          }else{
            $('profile_note_text').innerHTML = response.note;
          }
        }
        $$('#profile_note_textarea textarea')[0].disabled = false;
        self.edit_mode(false);
      }
    }).send();
  },

  choose_admins: function(){
    he_contacts.box('page', 'getUsersForTeam', 'page.add_admins', en4.core.language.translate('Add admins'), {page_id:this.page_id,button_label:en4.core.language.translate('Page_Team_Add')});
  },

  add_admins: function(admins){
    var self = this;
    if (this.block){
      return ;
    }
    this.block = true;
    new Request.JSON({
      'url': self.ajax_url,
      'method': 'post',
      'data': {
        'user_ids': admins,
        'format': 'json',
        'task' : 'add_admins',
        'page_id': self.page_id
      },
      onSuccess: function(response){
        self.block = false;
        window.location.href = window.location.href;
      }
    }).send();
  },


  choose_employers: function(){
    he_contacts.box('page', 'getUsersForTeam', 'page.add_employers', en4.core.language.translate('Add Employers'), {page_id:this.page_id,button_label:en4.core.language.translate('Page_Team_Add')});
  },

  add_employers: function(employers){
    var self = this;
    if (this.block){
      return ;
    }
    this.block = true;
    new Request.JSON({
      'url': self.ajax_url,
      'method': 'post',
      'data': {
        'user_ids': employers,
        'format': 'json',
        'task' : 'add_employers',
        'page_id': self.page_id
      },
      onSuccess: function(response){
        self.block = false;
        window.location.href = window.location.href;
      }
    }).send();
  },

  hide_admin_info: function(admin_id){
    $('admin_title_'+admin_id).addClass('hidden');
    $('admin_title_edit_'+admin_id).addClass('hidden');

    $('admin_title_'+admin_id).removeClass('visible');
    $('admin_title_edit_'+admin_id).removeClass('visible');
  },

  show_admin_info: function(admin_id){
    $('admin_title_'+admin_id).addClass('visible');
    $('admin_title_edit_'+admin_id).addClass('visible');

    $('admin_title_'+admin_id).removeClass('hidden');
    $('admin_title_edit_'+admin_id).removeClass('hidden');
  },

  show_admin_edit: function(admin_id){
    $('admin_title_input_box_'+admin_id).addClass('visible');
    $('admin_title_input_box_'+admin_id).removeClass('hidden');
  },

  hide_admin_edit: function(admin_id){
    $('admin_title_input_box_'+admin_id).addClass('hidden');
    $('admin_title_input_box_'+admin_id).removeClass('visible');
  },

  focus_input: function(admin_id){
    $('admin_title_input_'+admin_id).focus();
  },

  edit_admin_title: function(admin_id){
    this.hide_admin_info(admin_id);
    this.show_admin_edit(admin_id);
    this.focus_input(admin_id);
  }
};

var pages_map =
{
  pages_array: {},
  markers_array: {},
  map_bounds: {},
  current_data: {},
  zoom: 4,
  constructed: false,
  canvas_id: 'map_canvas',

  construct: function(pages_array, markers_array, zoom, map_bounds, edit_mode){
    this.pages_array = pages_array;
    this.markers_array = markers_array;
    this.map_bounds = map_bounds;
    this.zoom = zoom;
    this.init();

    if (edit_mode == undefined && !edit_mode) {
      this.show_map();
    } else {
      this.show_edit_map();
    }

    this.constructed = true;
  },

  init: function(){
    window.map = new google.maps.Map(document.getElementById('map_canvas'),{
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: false
    });
  },

  show_map: function() {
    if( this.markers_array.length==0 ) {return false;}

    //this.clearOverlays();

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    for( var i=0; i<this.markers_array.length; i++ )
    {
      marker = this.markers_array[i];
      point = new google.maps.LatLng(marker.lat, marker.lng);
      marker_obj = new google.maps.Marker({
        position: point,
        map: window.map
      });

      marker_obj.html = '<table width="500px"><tr valign="top"><td width="110px"><img src="' + marker.pages_photo + '" width="100"></td><td width="400px"><h3 style="margin-top:0; margin-bottom:6px;"><a href="'+marker.url+'">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';
      if (!marker.desc) {marker.desc='';}

      google.maps.event.addListener(marker_obj, 'click', function() {
        infoWindow.setContent(this.html);
        infoWindow.open(window.map, this);
      });

    }

    window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
    window.map.setZoom(4);
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      window.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng));
      window.map.setZoom(4);
    } else {
      window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      window.map.setZoom(this.zoom);
    }
    if (bds) {
      window.map.fitBounds(bds);
    }
  },

  show_edit_map: function() {
    if( this.markers_array.length==0 ) {return false;}
    var self = this;

    //this.clearOverlays();

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    for( var i=0; i<this.markers_array.length; i++ )
    {
      marker = this.markers_array[i];
      point = new google.maps.LatLng(marker.lat, marker.lng);
      marker_obj = new google.maps.Marker({
        position: point,
        map: window.map,
        draggable: true
      });

      marker_obj.html = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.pages_photo + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;"><a href="'+marker.url+'">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';

      if (!marker.desc) {marker.desc='';}

      google.maps.event.addListener(marker_obj, 'click', function() {
        infoWindow.setContent(this.html);
        infoWindow.open(window.map, this);
      });
    }

    window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
    window.map.setZoom(4);
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      window.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng));
      window.map.setZoom(4);
    } else {
      window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      window.map.setZoom(this.zoom);
    }
    if (bds) {window.map.fitBounds(bds);}

    google.maps.event.addListener(marker_obj, "dragend", function() {
      self.trackChanges(marker_obj);
    });
  },

  showEditMap: function(pages_array, markers_array, zoom, map_bounds, edit_mode) {
    $$('.page_edit_form_map_show').addClass('display_none');
    $$('.page_edit_form_map_hide').removeClass('display_none');
    $('map_canvas').removeClass('display_none');

    if (!this.constructed) {
      this.construct(pages_array, markers_array, zoom, map_bounds, edit_mode);
    }
  },

  hideEditMap: function() {
    $$('.page_edit_form_map_show').removeClass('display_none');
    $$('.page_edit_form_map_hide').addClass('display_none');
    $('map_canvas').addClass('display_none');
  },

  trackChanges: function(marker) {
    var coords = marker.getPosition();
    $('coordinates').value = coords.lat() + ';' + coords.lng();

    geocoder = new google.maps.Geocoder();

    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          var address = csvToArray(results[0].formatted_address, ',');
          var index = address.length - 1;

          if(index >= 0){
            $('country').value = address[index].trim();
            index--;
          }

          if(index >= 0){
            var len = results[0].address_components.length, i;
            for( i = len - 1; i > 0; i-- ) {
              if( results[0].address_components[i].types[0] == 'country') {
                i--;
                $('state').value = results[0].address_components[i].long_name;
                break;
              }
            }
            index--;
          }


          if(index >= 0){
            $('city').value = address[index].trim();
            index--;
          }


          if(index >= 0){
            $('street').value = address[index].trim();
            index--;
          }
        }
      }
    });
  }
};

var he_word_completer = {
  prepend_word : '',
  title : null,
  completer : null,
  default_value : '',
  ajax_url : '',
  block_request : false,
  url : null,
  url_changed : 0,

  init : function(){
    var self = this;
    var ignored_keys = ['left', 'right', 'home', 'end', 'delete', 'backspace'];

    en4.core.runonce.add(function(){
      $(self.title).addEvent('keyup', function(e) {
        if (self.url_changed) {
          return;
        }
        self.complete(this.value);
      });

      $(self.url).addEvent('keyup', function(e) {
        if (ignored_keys.contains(e.key)) {
          return;
        }
        self.complete(this.value);
      });

      $(self.title).addEvent('blur', function() {
        if (self.url_changed) {
          return;
        }
        self.complete(this.value);
        if (!self.block_request){
          if ($(self.title).value == '') {
            $(self.url).value  = '';
          }
          self.block_request = true;
          self.validate(this.value);
          self.block_request = false;
        }
      });

      $(self.url).addEvent('blur', function() {
        self.complete(this.value);
        console.log(self.block_request);
        if (!self.block_request) {
          self.block_request = true;
          var title_text = string_to_slug($(self.title).value);
          if (title_text == this.value) {
            self.url_changed = 0;
          } else {
            self.url_changed = 1;
          }
          self.validate(this.value);
          self.block_request = false;
        }
      })
    });
  },

  complete : function(value){
    if (value != "") {
    value = string_to_slug(value);
      $(this.url).value = value;
      $(this.completer).innerHTML = this.prepend_word + value;
    }else{
      $(this.completer).innerHTML = this.prepend_word + this.default_value;
    }
  },

  validate : function(url){
    var self = this;
    new Request.JSON({
      'url' : self.ajax_url,
      'method' : 'post',
      'data' : {
        'url' : url,
        'format' : 'json'
      },
      onSuccess : function(response){
        self.highlight($('url'), response);
      }
    }).send();
  },

  highlight : function($element, response){
    $$('.success_image').each(function($item){
      $item.dispose();
    });
    $$('.error_image').each(function($item){
      $item.dispose();
    });
    $element = $($element);
    $element.removeClass('error');
    $element.removeClass('success');
    if (response.success){
      $img = new Element('div', {'class' : 'success_image'});
      $img.innerHTML = "<span class='success_message'>";
      $element.addClass('success');
    }else{
      $img = new Element('div', {'class' : 'error_image'});
      $img.innerHTML = "<span class='error_message'>";
      $element.addClass('error');
    }
    $img.innerHTML += response.message+"</span>";
    $img = $($img);
    $img.inject($element, 'after')
  }
};

var page_manager = {
  page_num : 1,
  field_ids: {},
  sort: 'recent',
  setId: 0,
  category: 0,
  city: '',
  tag_id: '',
  abc: '',
  tag_url: '',
  keyword: '',
  badge: '',
  view_mode: 'list',
  sort_type: '',
  sort_value: '',

  adv_search: 0,
  adv_location: '',
  adv_keyword: '',
  adv_within: 20,
  adv_country: '',
  adv_state: '',
  adv_city: '',
  adv_street: '',
  adv_category: '',
  adv_sponsored: '0',
  adv_approved: '0',
  adv_featured: '0',

  init : function() {
    var self = this;

    if (document.getElementById('filter_form') != undefined) {
      var default_value = en4.core.language.translate("Search");
      en4.core.runonce.add(function() {
        $('keyword').value = default_value;
        $('submit').addEvent('click', function(e) {
          e.stop();
          self.page_num = 1;
          self.getPages();
        });
        $('keyword').addEvents({
          'focus' : function() {
            if (this.value == default_value){
              this.value = "";
            }
          },
          'blur' : function(){
            if (this.value == ""){
              this.value = default_value;
            }
          }
        });
        $$('#filter_form div ul li').setStyle('display', 'none');
        $('keyword').getParent().setStyle('display', 'block');
        if($('set').getChildren('option').length == 2) {
          $('set').getParent().setStyle('display', 'none');
          $('profile_type').getParent().setStyle('display', 'block');
        }
        else
          $('set').getParent().setStyle('display', 'block');
        /*        if ($('profile_type')) {
         $('profile_type').getParent().setStyle('display', 'block');
         }*/
        $('submit').getParent().setStyle('display', 'block');

        var $elements = $$('#filter_form div ul li input, #filter_form div ul li select, #filter_form div ul li textarea');

        if ($('profile_type')){
          $('profile_type').addEvent('change', function() {
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
      });
    }
  },

  getData : function () {
    var self = this;


    var data =  {
      'format': 'html',
      'page': self.page_num,
      'view_mode': self.view,
      'sort': self.sort,
      'setId': self.setId,
      'category': self.category,
      'city': self.city,
      'tag_id': self.tag_id,
      'abc': self.abc,
      'keyword': self.keyword,
      'badge': self.badge,
      'sort_type': self.sort_type,
      'sort_value': self.sort_value,

      'adv_search': self.adv_search,
      'adv_keyword': self.adv_keyword,
      'adv_location': self.adv_location,
      'adv_within': self.adv_within,
      'adv_country': self.adv_country,
      'adv_state': self.adv_state,
      'adv_city': self.adv_city,
      'adv_street': self.adv_street,
      'adv_category': self.adv_category,
      'adv_sponsored': self.adv_sponsored,
      'adv_featured': self.adv_featured,
      'adv_approved': self.adv_approved
    };

    $$('#filter_form > div > ul > li input, #filter_form > div > ul > li select, #filter_form div > ul > li textarea').each(function(item){
      if(null == item.name.match(/^(\d+)_/))
        return;

      if (item.type == 'checkbox' && !item.checked) {
        return;
      }

      if (item.type == 'radio' && !item.checked) {
        return;
      }

      data[item.name] = item.get('value');
    });

    return data;

  },

  resetData : function() {
    var self = this;
    //self.view = '';
    self.sort = '';
    self.categoryId = 0;
    self.subCategoryId = 0;
    self.city = '';
    self.tag_id = '';
    self.abc = '';
    self.badge = '';
    self.keyword = '';

    self.adv_search = 0;
    self.adv_keyword = '';
    self.adv_location = '';
    self.adv_within = '0';
    self.adv_country = '';
    self.adv_state = '';
    self.adv_city = '';
    self.adv_street = '';
    self.adv_category = '';
    self.adv_sponsored = '';
    self.adv_approved = '';
    self.adv_featured = '';
  },

  getPages : function() {
    var self = this;

    if ($('page_loader_browse')) {
      $('page_loader_browse').removeClass('hidden');
    }

    if ($('filter_form') != undefined) {
      if ($('keyword').value == en4.core.language.translate('Search')) {
        $('keyword').value = '';
      }
      self.keyword = $('keyword').value;
      self.setId = $('set').value;
      self.category = $('profile_type').value;
      if ($('keyword').value == '')
        $('keyword').value = en4.core.language.translate('Search');
    }
    var data = self.getData();

    new Request.HTML({
      url:self.widget_url,
      data : data,
      evalScripts: true,
      onSuccess:function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        var el = $$('.layout_page_browse_pages');
        var tElement = new Element('div', {'html': responseHTML});
        el[0].innerHTML = tElement.getElement('.layout_page_browse_pages').innerHTML;

        if (self.tag_id) {
          if ($('tag_'+self.tag_id)) {
            $('page_tag_info').innerHTML = '<span class="bold">'+self.truncate($('tag_'+self.tag_id).innerHTML, 10)+'</span> '+en4.core.language.translate('tag')+'. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setTag(0);">x</a>]';
            $('page_tag_info').removeClass('hidden');
          }
        } else
        if(self.sort_type == 'tag') {
          $('page_tag_info').innerHTML = '<span class="bold">'+self.truncate(self.sort_value, 10)+'</span> '+en4.core.language.translate('tag')+'. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setTag(0);">x</a>]';
          $('page_tag_info').removeClass('hidden');
        } else{
          $('page_tag_info').innerHTML = "";
          $('page_tag_info').addClass('hidden');
        }

        if (self.city) {
          if ($('page_sort_location_'+self.city.replace(/ /g, ''))) {
            $('page_city_info').innerHTML = '<span class="bold">'+self.truncate($('page_sort_location_'+self.city.replace(/ /g, '')).innerHTML, 10)+'</span> '+en4.core.language.translate('city')+'. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setLocation(0);">x</a>]';
            $('page_city_info').removeClass('hidden');
          }
        } else
        if(self.sort_type == 'location'){
          $('page_city_info').innerHTML = '<span class="bold">'+self.truncate(self.sort_value, 10)+'</span> '+en4.core.language.translate('city')+'. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setLocation(0);">x</a>]';
          $('page_city_info').removeClass('hidden');
        } else {
          $('page_city_info').innerHTML = "";
          $('page_city_info').addClass('hidden');
        }

        if (self.category || self.setId) {
          if(typeof pcs !== 'undefined') {
            pcs.removeCategorySelections();
            pcs.removeSetSelections();
            if(self.setId && self.setId != 0) {
              pcs.openSingleSet(self.setId).addSetSelection(self.setId);
            }
            if(self.category && self.category != 0) {
              pcs.addCategorySelection(self.category);
            }
          }

          if(self.category || self.setId){
            if(self.category == 0 && self.setId == 0) {
              $('page_category_info').innerHTML = "";
              $('page_category_info').addClass('hidden');
            }else {

              var cat_str = $$('#profile_type option[value='+self.category+']').innerHTML
              if( !cat_str ) {
                if($('pcs-cat-' + self.category + '-a')) {
                  cat_str = $('pcs-cat-' + self.category + '-a').innerHTML;
                } else {
                  cat_str = '';
                }
              }

              var set_str = $$('#set option[value='+self.setId+']').innerHTML
              if( !set_str ) {
                if( $('pcs-set-' + self.setId + '-a') ) {
                  set_str = $('pcs-set-' + self.setId + '-a').innerHTML;
                } else {
                  set_str = '';
                }
              }

              var setPart = !isNaN(parseInt(self.setId)) ? set_str : '';
              var catPart = !isNaN(parseInt(self.category)) ? self.truncate(cat_str, 10) : '';
              var label = '<span class="bold">' + setPart + (setPart.length != 0 && catPart.length !=0 ? ' - ': '') + catPart + '</span> '+en4.core.language.translate('category');

              $('page_category_info').innerHTML = label +
                '. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setCategory(0, 0);">x</a>]';
              $('page_category_info').removeClass('hidden');
            }
          }
        } else
        if(self.sort_type == 'category'){
          $('page_category_info').innerHTML = '<span class="bold">'+self.truncate(self.sort_value, 10)+'</span> '+en4.core.language.translate('category')+'. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setLocation(0);">x</a>]';
          $('page_category_info').removeClass('hidden');
        } else {
          $('page_category_info').innerHTML = "";
          $('page_category_info').addClass('hidden');
        }

        if(self.adv_search == 1) {
          $('page_adv_info').innerHTML = '<span class="bold">'+'</span> '+en4.core.language.translate('Adv Search')+'. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setAdvSearch(0);">x</a>]';
          $('page_adv_info').removeClass('hidden');
        } else {
          $('page_adv_info').innerHTML = "";
          $('page_adv_info').addClass('hidden');
        }

        if ($('page_loader_browse')) {
          $('page_loader_browse').addClass('hidden');
        }

        en4.core.runonce.trigger();
      }
    }).post();
  },

  setSort : function(sort) {
    this.page_num = 1;
    this.sort = sort;
    this.getPages();
  },

  setView : function(view, el) {
    $$('.pages-view-types').removeClass('active');
    if ($type(el) == 'element'){
      el.addClass('active');
    }

    if ($('page_list_cont') != null) {
      if (view == 'map') {
        $('page_list_cont').setStyle('display', 'none');
        $('page_icons_cont').setStyle('display', 'none');
        $('map_canvas').setStyle('position', 'relative');
        $('map_canvas').setStyle('top', '0px');
      } else if (view == 'list') {
        $('page_list_cont').setStyle('display', 'block');
        $('page_icons_cont').setStyle('display', 'none');
        $('map_canvas').setStyle('position', 'absolute');
        $('map_canvas').setStyle('top', '10000px');
      } else {
        $('page_icons_cont').setStyle('display', 'block');
        $('page_list_cont').setStyle('display', 'none');
        $('map_canvas').setStyle('position', 'absolute');
        $('map_canvas').setStyle('top', '10000px');
      }
    }
    this.view = view;
  },

  setCategory : function(setId, category) {
    if(setId != 0) {
      $$('.page-categories').each(function(elem){
        if($(elem).hasClass('page-categories-simple'))
          return;
        elem.hide();
      });

      if($$('.layout_page_page_categories').length > 0)
        $$('.layout_page_page_categories').getChildren('h3')[0].hide()
    }
    else{
      $$('.page-categories').each(function(elem){
        if($(elem).hasClass('page-categories-simple'))
          return;
        elem.show();
      });

      if($$('.layout_page_page_categories').length > 0)
        $$('.layout_page_page_categories').getChildren('h3')[0].show()
    }

    this.page_num = 1;
    if ($('filter_form') != undefined) {
      $('filter_form').reset();
      $('keyword').value = this.keyword;
      $('profile_type').value = category;
      changeFields($('profile_type'));
    }
    this.setId = setId;
    if( $('set') ) {
      $('set').value = this.setId;
      $('set').fireEvent('change');
    }
    if($('profile_type')) {
      $('profile_type').value = category;
      $('profile_type').fireEvent('change');
      if(typeof $('profile_type').onClick != 'undefined')
        $('profile_type').onClick();
    }

    this.category = category;
    if(this.sort_type == 'category'){
      this.sort_type = '';
      this.sort_value = '';
    }
    this.getPages();
  },

  setLocation : function(city) {
    this.page_num = 1;
    this.city = city;

    if(this.sort_type == 'location'){
      this.sort_type = '';
      this.sort_value = '';
    }

    this.getPages();
  },

  setPage : function(page){
    this.page_num = page;
    this.getPages();
  },

  setTag : function(tag_id) {
    this.page_num = 1;
    this.tag_id = tag_id;

    if(this.sort_type == 'tag'){
      this.sort_type = '';
      this.sort_value = '';
    }

    this.getPages();
  },

  setAdvSearch : function(adv) {
    this.page_num = 1;
    this.adv_search = adv;
    if(this.adv_search == 1){
      this.adv_keyword = $('adv_search_keyword').value;
      this.adv_location = $('adv_search_where').value;
      this.adv_within = $('adv_search_within').value;
      this.adv_country = $('adv_search_country').value;
      this.adv_state = $('adv_search_state').value;
      this.adv_city = $('adv_search_city').value;
      this.adv_street = $('adv_search_street').value;
      this.adv_category = ($('adv_search_category')) ? $('adv_search_category').value: null;
      this.adv_sponsored = $('adv_search_sponsored').checked;
      this.adv_approved = $('adv_search_approved').checked;
      this.adv_featured = $('adv_search_featured').checked;
    } else {
      this.adv_keyword = $('adv_search_keyword').value = '';
      this.adv_location = $('adv_search_where').value = '';
      this.adv_within = $('adv_search_within').value = 20;
      this.adv_country = $('adv_search_country').value = '';
      this.adv_state = $('adv_search_state').value = '';
      this.adv_city = $('adv_search_city').value = '';
      this.adv_street = $('adv_search_street').value = '';
      this.adv_category = ($('adv_search_category')) ? $('adv_search_category').value: null;
      this.adv_sponsored = $('adv_search_sponsored').value = false;
      this.adv_approved = $('adv_search_approved').value = false;
      this.adv_featured = $('adv_search_featured').value = false;
    }
    this.getPages();
  },

  setAbc : function(abc) {
    this.abc = abc;
    var lnk = 'page-abc-'+abc;
    var tmp = $('page-abc-wrapper');
    $$(tmp.getElements('a')).each(function($element) {
      if($element.hasClass('active'))
        $element.removeClass('active');
    });
    $(lnk).addClass('active');
    this.getPages();
  },

  setBadge : function(badge) {
    this.page_num = 1;
    this.badge = badge;
    this.getPages();
  },

  truncate: function(str, num) {
    if( !str ) {
      return '';
    }

    str = str.trim();
    var len = str.length;
    if(num >= len)
      return str;

    return (str.substr(0, num) + '...');
  }
};

var page_search = {

  url: '',
  filter_url: '',

  page_id: 0,

  p: 1,

  keyword: '',

  placeholder: '  ',

  element: 'page-search-field',
  $element: null,

  container: 'page-search-results',
  $container: null,

  tab: 'page-search-tab',
  $tab: null,

  tab_container: 'page-search-tab-container',
  $tab_container: null,

  loader: 'page-search-loader',
  $loader: null,

  middle: 'div.layout_core_container_tabs',
  $middle: null,

  filter_form: 'page-search-filter-form',

  tag_container: 'page-tag-results',
  $tag_container: null,

  timeout: null,
  block: false,

  toggle_timer: null,

  init: function(){
    var self = this;

    self.$middle = $$(self.middle)[0];
    self.init_more_block();
    self.init_tag_cloud();

    self.$element = $(self.element);
    self.$container = $(self.container);
    self.$tab = $(self.tab);
    self.$tab_container = $(self.tab_container);
    self.$loader = $(self.loader);
    self.placeholder = self.$element.value;

    var left = page_search.$element.getParent().getParent().offsetLeft;
    var top = page_search.$element.getParent().getParent().offsetTop + 30;

    self.$container.setStyle('left', left);
    self.$container.setStyle('top', top);

    if (self.$element) {
      self.$element.addEvents({
        'keyup': function(){
          var value = this.value;
          if (!value){
            return ;
          }
          if (self.keyword != value){
            window.clearTimeout(self.timeout);
            self.timeout = window.setTimeout(function(){
              self.search(value);
            }, 1000);
          }
        },
        'focus': function(){
          var value = this.value;
          if (value == self.placeholder){
            this.value = '';
            $(this).removeClass('inactive');
          }else if (value == ''){
            this.value = self.placeholder;
            $(this).addClass('inactive');
          }
        },
        'blur': function(){
          var value = this.value;
          if (value == ''){
            this.value = self.placeholder;
            $(this).addClass('inactive');
          }
        }
      });
    }
    if (self.$container){
      self.$container.addEvents({
        'mouseout': function(){
          window.clearTimeout(self.toggle_timer);
          self.toggle_timer = window.setTimeout(function(){
            self.close();
          }, 1000);
        },
        'mouseover': function(){
          window.clearTimeout(self.toggle_timer);
        }
      });
    }
  },

  init_tag_cloud: function(){
    var self = this;
    this.$middle = $$(self.middle)[0];
    if (this.$middle) {
      if ($(self.tag_container)){
        return $(self.tag_container);
      }

      var $container = new Element('div', {'id': self.tag_container, 'class': self.tag_container + ' hidden'});
      this.$middle.appendChild($container);
      this.$tag_container = $container;

      return $container;
    }
  },

  search: function(keyword){
    var self = this;

    if (this.block){
      return ;
    }
    this.block = true;

    self.keyword = keyword;
    self.show_loader();

    new Request.JSON({
      'url': self.url,
      'method': 'post',
      'data': {
        'keyword': keyword,
        'page_id': self.page_id,
        'p': self.p,
        'format': 'json'
      },
      onSuccess: function(response){
        var html = response.html ? response.html : '';
        var tab_html = response.tab_html ? response.tab_html : '';
        self.block = false;

        self.$container.set('html', html);
        if (!html){
          self.hide_results();
        }else{
          var mored = !self.$tab_container.hasClass('hidden') && self.$tab_container.getStyle('display') != 'none';
          if (!mored){
            self.show_results();
          }
        }
        if (html == ''){
          self.more();
        }
        self.$tab.set('html', tab_html);
        self.hide_loader();
      }
    }).send();
  },

  filter: function($form){
    var self = this;
    $form = $($form);
    self.show_loader();

    new Request.JSON({
      'url': self.filter_url+'?'+$form.toQueryString(),
      'method': 'post',
      'data': {
        'page_id': self.page_id,
        'format': 'json'
      },
      onSuccess: function(response){
        var html = response.html ? response.html : '';

        self.$tab.set('html', html);
        self.hide_loader();
      }
    }).send();
  },

  close: function(){
    this.hide_results();
  },

  init_more_block: function(){
    var self = this;
    var $middle = this.$middle;
    var $container = new Element('div', {'id': self.tab_container, 'class': 'page-search-tab-container hidden'});
    var $items = new Element('div', {'id': self.tab, 'class': 'page-search-tab'});

    $container.set('html', $(this.filter_form).innerHTML);
    $container.appendChild($items);
    $middle.appendChild($container);
    $(this.filter_form).dispose();

    return $container;
  },

  more: function(){
    $$(this.$middle.getElements('.generic_layout_container')).each(function($element){
      $element.setStyle('display', 'none');
    });

    this.$tab_container.removeClass('hidden');
    this.$tab_container.setStyle('display', 'block');

    this.$tag_container.addClass('hidden');
    this.$tag_container.setStyle('display', 'none');

    this.close();

    $$($('main_tabs').getElements('li.active')).each(function($li){
      $li.removeClass('active');
    });

    $$($('search_filter_form').getElements('input[type=checkbox]')).each(function($checkbox){
      $checkbox.checked = true;
    });

    $('search-keyword').value = this.keyword;
  },

  tag_action: function(){
    $$(this.$middle.getElements('.generic_layout_container')).each(function($element){
      $element.setStyle('display', 'none');
    });

    if (this.$tab_container){
      this.$tab_container.addClass('hidden');
      this.$tab_container.setStyle('display', 'none');
    }

    this.$tag_container.removeClass('hidden');
    this.$tag_container.setStyle('display', 'block');
    this.$tag_container.set('html', '<div class="page-tag-cloud-loader"><img src="'+window.en4.core.baseUrl+'application/modules/Page/externals/images/loader_search.gif" /><span class="loading_span"> '+en4.core.language.translate('Loading...')+'</span></div>');

    this.close();

    $$($('main_tabs').getElements('li.active')).each(function($li){
      $li.removeClass('active');
    });
  },

  show_results: function(){
    var self = this;

    this.$container.setStyle('opacity', 0);
    this.$container.removeClass('hidden');

    var fx = new Fx.Morph(this.$container.id, {duration: 500, transition: Fx.Transitions.Sine.easeOut});
    fx.start({
      'opacity': 1
    });
  },

  hide_results: function(){
    var self = this;
    if (!this.$container){
      return ;
    }
    var fx = new Fx.Morph(this.$container.id, {duration: 500, transition: Fx.Transitions.Sine.easeOut});
    fx.start({
      'opacity': 0
    });

    window.setTimeout(function(){
      self.$container.addClass('hidden');
    }, 500);
  },

  show_loader: function(){
    this.$loader.setStyle('background-image', 'url(' + window.en4.core.baseUrl + 'application/modules/Core/externals/images/loading.gif)');
  },

  hide_loader: function(){
    this.$loader.setStyle('background-image', 'url(' + window.en4.core.baseUrl + 'application/modules/Page/externals/images/viewpage.png)');
  },

  view: function(type, id){
    switch (type) {
      case 'pagealbumphoto':{
        page_album.init_album();
        page_album.view_photo(id);
      }
        break;
      case 'store_product':{
        store_page.product_id = id;
        store_page.view(id);
        store_page.init_store();
      }
        break;
      case 'pagealbum': {
        page_album.album = id;
        page_album.init_album();
        page_album.view(id);
      }
        break;
      case 'pagedocument': {
        page_document.init_document();
        page_document.view(id);
      }
        break;
      case 'pagevideo': {
        page_video.init_video();
        page_video.view_comments(id);
      }
        break;
      case 'pageblog': {
        page_blog.blog_id = id;
        page_blog.init_blog();
      }
        break;
      case 'song': {
        page_music.init_music();
        page_music.view_song(id);
      }
        break;
      case 'playlist': {
        page_music.playlist_id = id;
        page_music.init_music();
        page_music.view(id);
      }
        break;
      case 'pagereview': {
        Review.elm.view = '.pagereview_container_view';
        Review.initView(id);
      }
        break;
      case 'pagediscussion_pagetopic': {
        Pagediscussion.goDiscussionTab(id);
      }
        break;
      case 'pagediscussion_pagepost': {
        Pagediscussion.goDiscussionTab(null, id);
      }
        break;
      case 'pageevent': {
        Pageevent.loadView(id);
      }
        break;
    }


    this.close();
    return false;
  },

  search_by_tag: function(tag_id){
    var self = this;
    self.tag_action();

    new Request.JSON({
      'url': self.tag_url,
      'method': 'post',
      'data': {
        'tag_id': tag_id,
        'page_id': self.page_id,
        'format': 'json'
      },
      onSuccess: function(response){
        self.$tag_container.set('html', response.html);
      }
    }).send();
  }

};

var my_location = {
  my_lat : '',
  my_lon: '',
  my_request: '',
  my_map: '',
  my_url: '',
  my_marker: '',
  my_location_page_num: 1,
  my_location_address: '',

  init: function(myurl) {
    var self = this;
    self.my_url = myurl;
    self.my_map = new google.maps.Map(document.getElementById("my_map_canvas"), {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: false,
      center: new google.maps.LatLng(37.0902400, -95.7128910),
      zoom: 3
    });

    self.my_marker = new google.maps.Marker({
      map: self.my_map,
      animation: google.maps.Animation.BOUNCE,
      html: 'My Location!',
      title: 'My Location!'
    });
  },

  show_pages: function( markers, bounds ) {
    var self = this;
    var marker;
    if( markers.length==0 ) {return false;}

    //this.clearOverlays();

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    for( var i=0; i<markers.length; i++ )
    {
      marker = markers[i];
      var point = new google.maps.LatLng(marker.lat, marker.lng);
      var marker_obj = new google.maps.Marker({
        position: point,
        map: self.my_map
      });

      marker_obj.html = '<table width="500px"><tr valign="top"><td width="110px"><img src="' + marker.pages_photo + '" width="100"></td><td width="400px"><h3 style="margin-top:0; margin-bottom:6px;"><a href="'+marker.url+'">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';
      if (!marker.desc) {marker.desc='';}

      google.maps.event.addListener(marker_obj, 'click', function() {
        infoWindow.setContent(this.html);
        infoWindow.open(self.my_map, this);
      });

    }

    self.my_map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
    self.my_map.setZoom(4);
    if (bounds && bounds.min_lat && bounds.max_lng && bounds.min_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds && bounds.map_center_lat && bounds.map_center_lng) {
      self.my_map.setCenter(new google.maps.LatLng(bounds.map_center_lat, bounds.map_center_lng));
      self.my_map.setZoom(4);
    } else {
      self.my_map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      self.my_map.setZoom(this.zoom);
    }
    if (bds) {self.my_map.fitBounds(bds);}
  },
  set_my_marker: function( latitude, longitude ) {
    var self = this;

    self.my_lat = latitude;
    self.my_lon = longitude;

    var point = new google.maps.LatLng(self.my_lat, self.my_lon);
    self.my_marker.setPosition(point);

    self.my_map.setCenter(point);

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    google.maps.event.addListener(self.my_marker, 'click', function() {
      infoWindow.setContent(this.html);
      infoWindow.open(self.my_map, this);
    });

  },

  get_geolocation: function() {
    var self = this;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position){
        self.set_my_marker(position.coords.latitude, position.coords.longitude);
        self.get_pages();
      });
    }
    else {
      alert('Your browser does not support geolocation api');
    }
  },
  set_my_location_page: function(page_num) {
    this.my_location_page_num = page_num;
    this.get_pages();
  },
  set_my_location_address: function(address)
  {
    this.my_location_address = address;
    this.my_location_page_num = 1;
    this.get_pages();
  },
  get_pages: function()
  {
    var self = this;
    $('my_location_loading').removeClass('hidden');
    my_request = new Request.HTML({
      url: self.my_url,
      data: {
        my_latitude: self.my_lat,
        my_longitude: self.my_lon,
        my_address: self.my_location_address,
        my_page_num: self.my_location_page_num
      },

      onComplete:function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $$('.layout_page_my_location');
        var tElement = new Element('div', {'html': responseHTML});
        $('my_location_pages').innerHTML = tElement.getElement('.layout_page_my_location').innerHTML;
        if( $('my_location_pagination_select') )
          $('my_location_pagination_select').value = self.my_location_page_num;
      }
    }).post();
  }
};

function csvToArray( strData, strDelimiter ){
  strDelimiter = (strDelimiter || ",");

  var objPattern = new RegExp(
    (
      "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +
        "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
        "([^\"\\" + strDelimiter + "\\r\\n]*))"
      ),
    "gi"
  );

  var arrData = [[]];

  var arrMatches = null;

  while (arrMatches = objPattern.exec( strData )){
    var strMatchedDelimiter = arrMatches[ 1 ];
    if ( strMatchedDelimiter.length && (strMatchedDelimiter != strDelimiter) ){
      arrData.push( [] );
    }
    if (arrMatches[ 2 ]){
      var strMatchedValue = arrMatches[ 2 ].replace(
        new RegExp( "\"\"", "g" ),
        "\""
      );

    } else {
      var strMatchedValue = arrMatches[ 3 ];
    }
    arrData[ arrData.length - 1 ].push( strMatchedValue );
  }

  return( arrData[0] );
}
PageCategoriesSimple = function(contId) {
  var contId = contId,
    o = this;

  this.toggleSet = function ( setId ){
    $$('.pcs-set-'+setId+'-cat-li').toggle();
    $('set-'+setId+'-plus').isVisible()? $('set-'+setId+'-plus').hide() : $('set-'+setId+'-plus').setStyle('display', 'inline');
    $('set-'+setId+'-minus').isVisible()? $('set-'+setId+'-minus').hide() : $('set-'+setId+'-minus').setStyle('display', 'inline');

    return o;
  }

  this.addSetSelection = function(setId){
    var $setElement = $('pcs-set-'+setId+'-li');
    if ($setElement) {
      ($setElement.getChildren('a')[0]).addClass('selected-set');
    }

    return o;
  }

  this.addCategorySelection = function(catId){
    if($('pcs-cat-'+catId+'-li') == null)
      return o;

    ($('pcs-cat-'+catId+'-li').getChildren('a')[0]).addClass('selected-cat');

    return o;
  }

  this.removeSetSelections = function(){
    $$('.selected-set').each(function(elem){
      elem.removeClass('selected-set');
    });

    return o;
  }

  this.openSingleSet = function(setId){
    $$('.set-plus').setStyle('display', 'inline');
    $$('.set-minus').hide();

    try {
      $('set-'+setId+'-plus').hide();
      $('set-'+setId+'-minus').setStyle('display', 'inline');
    } catch (e) {}

    $$('.pcs-cat-li').each(function(elem){
      if(elem.hasClass('pcs-set-'+setId+'-cat-li')) {
        elem.show();
        return;
      } else {
        elem.hide();
      }
    });

    return o;
  }

  this.removeCategorySelections = function(){
    $$('.selected-cat').each(function(elem){
      elem.removeClass('selected-cat');
    });

    return o;
  }
}