/**
 * Created by JetBrains PhpStorm.
 * User: TeaJay
 * Date: 01.12.11
 * Time: 17:13
 * To change this template use File | Settings | File Templates.
 */

var checkin_map =
{
  checkin_array: {},
  markers_array: {},
  map_bounds: {},
  current_data: {},
  zoom: 4,
  constructed: false,
  get_event_loc_url: '',

  construct: function(checkin_array, markers_array, zoom, map_bounds, edit_mode){
    this.checkin_array = checkin_array;
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

  init: function() {
    this.map = new google.maps.Map(document.getElementById("map_canvas"), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: new google.maps.LatLng(0, 0), zoom: 15});
  },

  show_map: function() {
    var self = this;
    if( this.markers_array.length==0 ) {return false;}

    var infowindow = new google.maps.InfoWindow();

    for( var i=0; i<this.markers_array.length; i++ )
    {
      var marker = this.markers_array[i];
      var marker_obj = new google.maps.Marker({
        map: this.map,
        position: new google.maps.LatLng(marker.lat, marker.lng)
      });

      this.setMarkerInfo(marker, infowindow, marker_obj);
      this.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng), 4);
    }

    this.setMapCenterZoom();
  },

  setMarkerInfo: function(marker, infowindow, marker_obj) {
    var self = this;
    google.maps.event.addListener(marker_obj, 'click', function() {
      if (marker.url) {
        var marker_content = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.pages_photo + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;"><a href="'+marker.url+'">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';
      } else {
        var marker_content = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.checkin_icon + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;">' + marker.title + '</h3></td></tr></table>';
      }

      infowindow.setContent(marker_content);
      infowindow.open(self.map, this);
    });
  },

  setMapCenterZoom: function() {
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      this.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng), 4);
    } else {
      this.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng), this.zoom);
    }
    if (bds) {
      this.map.setCenter(bds.getCenter());
      this.map.fitBounds(bds);
    }
  },

  setView : function(view, el) {
    $$('.checkin-view-types').removeClass('active');
    if ($type(el) == 'element') {
      el.addClass('active');
    }

    if (view == 'map') {
      $('checkin_list_cont').setStyle('display', 'none');
      $('map_canvas').setStyle('position', 'relative');
      $('map_canvas').setStyle('top', '0px');
      google.maps.event.trigger(this.map, 'resize');
      this.setMapCenterZoom();
    } else if (view == 'list') {
      $('checkin_list_cont').setStyle('display', 'block');
      $('map_canvas').setStyle('position', 'absolute');
      $('map_canvas').setStyle('top', '10000px');
    }
  },


  // Event Map Widget
  initEventMap: function(widgetID, marker) {
    var self = this;
    this.$event_cont = $(widgetID);
    this.$event_info = this.$event_cont.getElement('.checkin_event_info');
    this.$event_get_location = this.$event_cont.getElement('.get_location');
    this.$event_edit_location = this.$event_cont.getElement('.edit_location');
    this.$event_location = this.$event_cont.getElement('.checkin_event_location');
    this.$event_locations = this.$event_cont.getElement('.event_locations');
    this.$event_map = this.$event_cont.getElement('.checkin_event_map');
    this.event_marker = false;

    try {
      if (marker) {
        this.event_marker = marker;
      }
    } catch(e) {}

    if (this.event_owner_mode) {
      this.initOwnerMode();
    } else if (this.event_marker && this.event_marker.place_id) {
      this.showEventMap();
    }
  },

  initOwnerMode: function() {
    var self = this;
    if (this.event_marker && this.event_marker.place_id) {
      this.showEventMap();
    } else {
      this.getEventLocation();
    }

    this.$event_edit_location.addEvent('keydown', function(event) {
      if (event.key == 'enter') {
        self.getEventLocation();
      }
    });

    this.$event_edit_location.addEvent('blur', function() {
      if (this.value == self.event_marker.name) {
        self.edit_event_interval = window.setTimeout(function() {
          self.$event_info.removeClass('display_none');
          self.$event_location.addClass('display_none');
        }, 3000);
      }
    });

    this.$event_info.addEvent('click', function() {
      self.editEventLocation();
    });

    this.$event_get_location.addEvent('click', function() {
      self.getEventLocation();
      if (self.edit_event_interval) {
        window.clearInterval(self.edit_event_interval);
      }
    });
  },

  getEventLocation: function() {
    var self = this;
    this.showEventLoader();

    new Request.JSON({
      'url' : self.get_event_loc_url+'?nocache='+Math.random(),
      'method' : 'post',
      'data' : {
        'format' : 'json',
        'keyword': self.$event_edit_location.value
      },
      onSuccess: function(response){
        self.hideEventLoader();
        if (response && response.places) {
          self.showEventLocations(response.places);
        }
      }
    }).send();
  },

  showEventLoader: function() {
    this.$event_get_location.addClass('loading');
    this.$event_edit_location.setProperty('disabled', true);
  },

  hideEventLoader: function() {
    this.$event_get_location.removeClass('loading');
    this.$event_edit_location.setProperty('disabled', false);
  },

  showEventLocations: function(locations) {
    var self = this;

    this.$event_locations.empty();
    this.$event_map.addClass('display_none');

    for (var i = 0; i < locations.length; i++) {
      var location = locations[i];
      var $suggest = new Element('div', {'class': 'choice_label'});
      $suggest.set('html', location.name);

      this.$event_locations.grab($suggest);
      $suggest.store('location', location);
      $suggest.addEvent('click', function() {
        var suggest = $(this).retrieve('location');
        self.selectEventLocation(suggest);
        self.hideEventSuggests();
      });
    }

    if (locations.length == 0) {
      var $suggest = new Element('div');
      $suggest.set('html', en4.core.language.translate('CHECKIN_There are no locations'));
      this.$event_locations.grab($suggest);
    }

    this.$event_locations.removeClass('display_none');
  },

  selectEventLocation: function(location) {
    var self = this;
    this.$event_info.getElement('.checkin_label').set('html', location.name);
    this.$event_info.removeClass('display_none');
    this.$event_location.addClass('display_none');

    this.$event_map.removeClass('display_none');
    this.$event_map.addClass('checkin_event_map_loading');

    new Request.JSON({
      'url' : self.set_event_loc_url+'?nocache='+Math.random(),
      'method' : 'post',
      'data' : {
        'format' : 'json',
        'reference' : location.reference
      },
      onSuccess: function(response){
        if (response && response.place) {
          self.$event_map.removeClass('checkin_event_map_loading');
          self.event_marker = response.place;
          var LatLng = new google.maps.LatLng(self.event_marker.latitude, self.event_marker.longitude);
          self.map = new google.maps.Map(self.$event_map, {mapTypeId: google.maps.MapTypeId.ROADMAP, center: LatLng, zoom: 15});
          var marker_obj = new google.maps.Marker({
            map: self.map,
            position: LatLng
          });
        }
      }
    }).send();
  },

  hideEventSuggests: function() {
    var self = this;
    this.$event_locations.addClass('display_none');
  },

  editEventLocation: function() {
    if (this.event_marker && this.event_marker.name)
    this.$event_edit_location.setProperty('value', this.event_marker.name);
    this.$event_info.addClass('display_none');
    this.$event_location.removeClass('display_none');
  },

  showEventMap: function() {
    this.$event_info.getElement('.checkin_label').set('html', this.event_marker.name);
    this.$event_info.removeClass('display_none');
    this.$event_location.addClass('display_none');
    this.$event_map.removeClass('display_none');
    var LatLng = new google.maps.LatLng(this.event_marker.latitude, this.event_marker.longitude);
    this.map = new google.maps.Map(this.$event_map, {mapTypeId: google.maps.MapTypeId.ROADMAP, center: LatLng, zoom: 15});
    var marker_obj = new google.maps.Marker({
      map: this.map,
      position: LatLng
    });
  },

  final: true
};