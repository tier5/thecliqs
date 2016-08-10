/**
 * Created with JetBrains PhpStorm.
 * User: adik
 * Date: 10.08.12
 * Time: 11:39
 * To change this template use File | Settings | File Templates.
 */

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


var donations_map =
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
    if (bds) {window.map.fitBounds(bds);}
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

var Donate = {

  chooseAmount:'.donation_select > ul > li',

  checkAnonymous:'anon',

  fullName:'name',

  fieldsAnonymous:'.for_guests .fields_anonymous',

  selected_id:null,

  init:function (user_id) {
    this.initSelectors();
    if (!user_id) {
      var $name = $(this.fullName);
      $name.addEvent('focus', function () {
        if (this.value == en4.core.language.translate('DONATION_Anonym')) {
          this.value = '';
        }
      });
      $name.addEvent('blur', function () {
        if (this.value == '') {
          this.value = en4.core.language.translate('DONATION_Anonym');
        }
      });
    }
  },

  initSelectors:function () {
    var self = this;
    var $selectors = $$(this.chooseAmount);

    if ($selectors) {
      $selectors.addEvent('click', function () {
        $selectors.removeClass('active');
        this.addClass('active');
        self.selected_id = this.id;
      });
    }
  }
};
