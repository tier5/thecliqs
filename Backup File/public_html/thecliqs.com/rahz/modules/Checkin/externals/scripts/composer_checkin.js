


Wall.Composer.Plugin.Checkin = new Class({

  Extends : Wall.Composer.Plugin.Interface,
  name : 'checkin',

  checkin_enabled: true,
  edit_mode: false,
  navigator_shared: false,
  suggest: {},
  position: {},
  data: {},
  $checkin_cont: {},
  $share_btn: {},
  $share_info: {},
  $loader: {},

  default_location: false,

  blur_state: false,

  initialize : function(options, default_location) {
    this.params = new Hash(this.params);
    this.parent(options);

    if (default_location && default_location.length != 0) {
      this.default_location = default_location;
    }
  },

  attach : function() {
    var self = this;
    this.$checkin_cont = this.getComposer().container.getElement('.checkinWallShareLocation');
    this.getComposer().container.getElement('.submitMenu').grab(this.$checkin_cont);

    this.$share_btn = this.$checkin_cont.getElement('.checkinShareLoc');
    this.$share_info = this.$checkin_cont.getElement('.checkinLocationInfo');
    this.$share_edit_info = this.$checkin_cont.getElement('.checkinEditLocation');
    this.$loader = this.$checkin_cont.getElement('.checkinLoader');

    new Wall.Tips(this.$share_btn, {'title': en4.core.language.translate('CHECKIN_Share location')});

    // Submit
    this.getComposer().addEvent('editorSubmit', function (){
      self.linkAttached = false;
      if (self.checkin_enabled && self.isValidPosition()) {
        var checkin_hash = new Hash(self.position);
        self.getComposer().makeFormInputs({checkin: checkin_hash.toQueryString()});
      } else {
        self.getComposer().makeFormInputs({checkin: ''});
      }
    });

    this.$share_btn.addEvent('click', function() {
      this.blur();

      if (!self.navigator_shared) {
        self.toggleLoader(true);

        if (!Browser.Platform.ipod) {
          self.enableLocation();
        } else {
          self.enableLocationIpod();
        }

        return;
      }

      if (self.isValidPosition() && !self.checkin_enabled) {
        self.toggleCheckin(true);
      } else {
        self.toggleCheckin(false);
      }
    });

    this.$share_info.addEvent('click', function() {
      if (self.edit_mode) {
        return;
      }
      self.edit_mode = true;
      self.editLocation();
    });

    this.$share_edit_info.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(e) {
      if (!['esc', 'tab', 'up', 'down', 'enter'].contains(e.key)) {
        this.addClass('checkinLoader');
      }
    });

    this.suggest = this.getSuggest();

    if (this.default_location) {
      this.toggleLoader(false);
      this.navigator_shared = true;
      this.setPosition(this.default_location);
    }

    return this;
  },

  detach : function() {
    this.toggleCheckin(false);
    return this;
  },

  toggleCheckin: function(enable) {
    if (enable) {
      this.checkin_enabled = true;
      this.$share_btn.addClass('checkinShareLocAct');
      this.$share_info.removeClass('disabled');

    } else {
      this.checkin_enabled = false;
      this.$share_btn.removeClass('checkinShareLocAct');
      this.$share_info.addClass('disabled');
    }
  },

  enableLocation: function() {
    var self = this;
    var positionTimeLimit = 6000;
    this.navigator_shared = true;

    var positionTimeout = window.setTimeout(function() {
      try {
        navigator.geolocation.clearWatch(self.watchID);
      } catch (e) {}

      self.toggleLoader(false);

      var data = {
        'accuracy': 0,
        'latitude': 0,
        'longitude': 0,
        'name': '',
        'vicinity': ''
      };

      self.setPosition(data);
    }, positionTimeLimit);

    try {
      self.watchID = navigator.geolocation.watchPosition(function(position) {
        window.clearTimeout(positionTimeout);
        self.toggleLoader(false);
        self.checkin_enabled = true;
        var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
        var data = {
          'accuracy': position.coords.accuracy,
          'latitude': position.coords.latitude,
          'longitude': position.coords.longitude,
          'name': (position.address) ? (position.address.street + delimiter + position.address.city) : '',
          'vicinity': (position.address) ? (position.address.street + delimiter + position.address.city) : ''
        };

        self.setPosition(data);
      });
    } catch (e) {}
  },

  enableLocationIpod: function() {
    var self = this;
    var positionTimeLimit = 6000;
    this.navigator_shared = true;

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position){
          var data = {
            'accuracy': position.coords.accuracy,
            'latitude': position.coords.latitude,
            'longitude': position.coords.longitude,
            'name': (position.address) ? (position.address.street + delimiter + position.address.city) : '',
            'vicinity': (position.address) ? (position.address.street + delimiter + position.address.city) : ''
          };

          if (data.name.length == 0 && data.latitude && data.longitude) {
            var latLong = new google.maps.LatLng( data.latitude, data.longitude);
            var map = new google.maps.Map(new Element('div'), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: latLong, zoom: 15});

            var request = {location: latLong, radius: 500};

            service = new google.maps.places.PlacesService(map);
            service.search(request, function(results, status) {
              self.toggleLoader(false);
              self.checkin_enabled = true;

              if (status == 'OK') {
                data.name = results[0].name;
                data.vicinity = results[0].vicinity;
              }

              self.setPosition(data);
            });

          } else {
            self.toggleLoader(false);
            self.checkin_enabled = true;
            self.setPosition(data);
          }

        },
        function(msg) {
          self.toggleLoader(false);

          var data = {
            'accuracy': 0,
            'latitude': 0,
            'longitude': 0,
            'name': '',
            'vicinity': ''
          };

          self.setPosition(data);
        }
      );
    }
  },

  setPosition: function(position) {
    this.edit_mode = false;
    this.position = position;
    if (this.isValidPosition(position)) {
      var checkin_hash = new Hash(position);
      this.checkin_enabled = true;
    } else {
      this.checkin_enabled = false;
    }


    this.$share_info.set('text', this.getLocationText());
    this.$share_btn.addClass('checkinShareLocAct');
    this.checkStatus();
    this.suggest.setOptions({'postData': this.getLocation()});
  },

  editLocation: function() {
    var locationValue = this.getLocationText();
    locationValue = (locationValue == en4.core.language.translate('CHECKIN_Where are you?')) ? '' : locationValue;
    this.$share_edit_info.set('value', locationValue);
    this.$share_info.addClass('display_none');
    this.$share_edit_info.removeClass('display_none');

    this.$share_edit_info.focus();
  },

  toggleLoader: function(show) {
    show = (show != 'undefined' && show) ? show : false;

    if (show) {
      this.$share_info.addClass('display_none');
      this.$loader.removeClass('display_none');
    } else {
      this.$loader.addClass('display_none');
      this.$share_edit_info.addClass('display_none');
      this.$share_info.removeClass('display_none');
    }
  },

  getLocationText: function() {
    if (this.position.name) {
      var locationText = this.position.name;
    } else {
      var locationText = en4.core.language.translate('CHECKIN_Where are you?');
    }

    return locationText;
  },

  getLocation: function() {
    var location = {'latitude': 0, 'longitude': 0};

    if (this.isValidPosition(false, true)) {

      location.latitude = this.position.latitude;
      location.longitude = this.position.longitude;
    }

    return location;
  },

  checkStatus: function() {
    if (this.isValidPosition()) {
      this.$share_btn.addClass('checkinShareLocAct');
      this.$share_info.removeClass('disabled');
      return true;
    } else {
      this.$share_btn.removeClass('checkinShareLocAct');
      this.$share_info.addClass('disabled');
      return false;
    }
  },

  isValidPosition: function(position, check_coordinates) {
    var position = (position) ? position : this.position;
    var isValid = (check_coordinates)
      ? (position && position.latitude && this.position.longitude)
      : (position && position.name != undefined && position.name != '')

    return isValid;
  },

  showSelectedMarker: function(user_checkin, $choice) {
    var self = this;
    var $checkin_map = this.suggest.choices.getElement('.checkin-autosuggest-map');
    $checkin_map.removeClass('display_none');

    if (user_checkin.latitude == undefined) {
      var map = new google.maps.Map(new Element('div'), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: new google.maps.LatLng(0, 0), zoom: 15});
      var service = new google.maps.places.PlacesService(map);
      service.getDetails({'reference': user_checkin.reference}, function(place, status) {
        if (status == 'OK') {
          user_checkin.name = place.name;
          user_checkin.google_id = place.id;
          user_checkin.latitude = place.geometry.location.lat();
          user_checkin.longitude = place.geometry.location.lng();
          user_checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
          user_checkin.icon = place.icon;
          user_checkin.types = place.types.join(',');

          $choice.store('autocompleteChoice', user_checkin);

          self.showSelectedMarker(user_checkin, $choice);
        }
      });

      return;
    }

    var myLatlng = new google.maps.LatLng(user_checkin.latitude, user_checkin.longitude);
    var new_map = false;
    if (this.map == undefined || !$checkin_map.getFirst()) {
      new_map = true;
      this.map = new google.maps.Map($checkin_map, {mapTypeId: google.maps.MapTypeId.ROADMAP, center: myLatlng, zoom: 15});
    }

    if (new_map) {
      this.marker = new google.maps.Marker({position: myLatlng, map: this.map});
      this.map.setCenter(myLatlng);
    } else {
      this.marker = (this.marker == undefined) ? new google.maps.Marker({position: myLatlng, map: this.map}) : this.marker;
      this.marker.setPosition(myLatlng);
      this.map.panTo(myLatlng);
    }

    this.bindMapEvents();
  },

  bindMapEvents: function() {
    var self = this;
    google.maps.event.addListener(this.map, 'maptypeid_changed', function(){
      self.suggest.element.focus();
      self.suggest.element.fireEvent('focus');
    });

    google.maps.event.addListener(this.map, 'zoom_changed', function(){
      self.suggest.element.focus();
      self.suggest.element.fireEvent('focus');
    });

    google.maps.event.addListener(this.map.getStreetView(), 'closeclick', function() {
      self.suggest.element.focus();
      self.suggest.element.fireEvent('focus');
    });

    google.maps.event.addListener(this.map.getStreetView(), 'pov_changed', function() {
      self.suggest.element.focus();
      self.suggest.element.fireEvent('focus');
    });
  },

  getSuggest: function() {
    var self = this;
    var $choices_cont = this.getComposer().container.getElement('.checkin_choice_cont_tpl').clone();
    $choices_cont.removeClass('checkin_choice_cont_tpl').addClass('checkin-autosuggest-cont');
    $choices_cont.inject(this.getComposer().container.getElement('.submitMenu'), 'after');

    return new Wall.Autocompleter.Request.JSON(this.$share_edit_info, en4.core.baseUrl + 'checkin/index/suggest/format/json', {
      'minLength': 1,
      'delay': 250,
      'width': 510,
      'cache': false,
      'selectMode': 'pick',
      'postVar': 'keyword',
      'autocompleteType': 'tag',
      'multiple': false,
      'className': 'checkin-autosuggest-cont',
      'filterSubset' : true,
      'tokenFormat': 'object',
      'tokenValueKey': 'name',
      'customChoices': $choices_cont,
      'customContainer': 'checkin-autosuggest',
      'postData': this.getLocation(),
      'injectChoice': function(token) {
        var $choice = self.getComposer().container.getElement('.checkin_choice_tpl').clone();
        var static_base_url = (en4.core.staticBaseUrl === undefined) ? '' : en4.core.staticBaseUrl;
        var default_icon = static_base_url + 'application/modules/Checkin/externals/images/map_icon.png';

        $choice.setProperty('id', token.id);
        $choice.removeClass('checkin_choice_tpl').addClass('autocompleter-choices');
        $choice.getElement('.checkin_choice_icon').setProperty('src', (token.icon) ? token.icon : default_icon);
        $choice.getElement('.checkin_choice_label').set('html', this.markQueryValue(token.name));

        var choice_info = token.info;
        if (typeof(choice_info) == 'number') {
          choice_info = en4.core.language.translate('CHECKIN_%s were here', token.info);
        }

        $choice.getElement('.checkin_choice_info').set('html', choice_info);

        var $choice_list = this.choices.getElements('.checkin-autosuggest');
        if ($choice_list.length == 0) {
          this.choices.set('html', self.getComposer().container.getElement('.checkin_choice_cont_tpl').innerHTML);
        }

        this.choices.setStyle('width', self.getComposer().container.getCoordinates().width);
        this.choices.getElement('.checkin-autosuggest').setStyle('width', self.getComposer().container.getCoordinates().width);
        this.addChoiceEvents($choice).inject(this.choices.getElement('.checkin-autosuggest'));
        $choice.store('autocompleteChoice', token);

        var $box = this.choices.getElement('.checkin-scroller-box');
        var $knob = this.choices.getElement('.checkin-scroller');
        $choice_list = this.choices.getElement('.checkin-autosuggest');
        $box.setStyle('height', 250);

        var count = this.choices.getElements('.checkin-autosuggest li').length;

        if ((count - 6) <= 4) {
          var steps = (count - 6)*43;
        } else {
          var steps = 4*43;
        }

        self.scroller = new Slider($box, $knob, {
          'mode': 'vertical',
          'steps': steps,
          'onChange': function(step) {
            $knob.addClass('active_knob');
            var top_css = (step > 0) ? (-1) * step : step;
            $choice_list.setStyle('top', top_css);

            self.suggest.element.focus();
            self.suggest.element.fireEvent('focus');
          },
          'onComplete': function() {
            $knob.removeClass('active_knob');
          }
        });

        if (count < 6) {
          this.choices.getElement('.checkin-autosuggest-list').setStyle('height', count * 43);
          self.scroller.element.addClass('display_none');
        } else {
          this.choices.getElement('.checkin-autosuggest-list').setStyle('height', 6 * 43);
          self.scroller.element.removeClass('display_none');
        }

        this.element.removeClass('checkinLoader');
      },
      'onShow': function() {
        var count = this.choices.getElements('.checkin-autosuggest li').length;

        if (count < 6) {
          this.choices.getElement('.checkin-autosuggest-list').setStyle('height', count * 43);
          self.scroller.element.addClass('display_none');
          return;
        } else {
          this.choices.getElement('.checkin-autosuggest-list').setStyle('height', 6 * 43);
          self.scroller.element.removeClass('display_none');
        }

        if ((count - 6) <= 4) {
          var steps = (count - 6)*43;
        } else {
          var steps = 4*43;
        }

        self.scroller.setOptions({'steps': steps});
        this.element.removeClass('checkinLoader');
      },
      'onHide': function() {
        this.element.removeClass('checkinLoader');
        try {
          self.scroller.element.addClass('display_none');
        } catch (e){

        }
      },
      'onChoiceSelect': function($choice) {
        var user_checkin = $choice.retrieve('autocompleteChoice');
        if (user_checkin.latitude == undefined) {
          var map = new google.maps.Map(new Element('div'), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: new google.maps.LatLng(0, 0), zoom: 15});
          var service = new google.maps.places.PlacesService(map);
          service.getDetails({'reference': user_checkin.reference}, function(place, status) {
            if (status == 'OK') {
              user_checkin.name = place.name;
              user_checkin.google_id = place.id;
              user_checkin.latitude = place.geometry.location.lat();
              user_checkin.longitude = place.geometry.location.lng();
              user_checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
              user_checkin.icon = place.icon;
              user_checkin.types = place.types.join(',');

              $choice.store('autocompleteChoice', user_checkin);
              self.toggleLoader(false);
            }
          });
        }

        self.setPosition(user_checkin);
        self.toggleLoader(false);
      },

      'emptyChoices': function() {
        this.fireEvent('onHide', [this.element, this.choices]);
      },

      'onSelect': function(input, choice) {
        var count = this.choices.getElements('.checkin-autosuggest li').length;
        var user_checkin = choice.retrieve('autocompleteChoice');
        var number = parseInt(user_checkin.id.substr(8));
        var steps = (count - 6 <= 4) ? (count - 6)*43 : 4*43;
        var initialStep = (number >= 6) ? (number - 6)*43 : 0;

        var $box = this.choices.getElement('.checkin-scroller-box');
        var $knob = this.choices.getElement('.checkin-scroller');
        $choice_list = this.choices.getElement('.checkin-autosuggest');

        self.scroller = new Slider($box, $knob, {
          'mode': 'vertical',
          'steps':  steps,
          'initialStep': initialStep,
          'onChange': function(step) {
            $knob.addClass('active_knob');
            var top_css = (step > 0) ? (-1) * step : step;
            $choice_list.setStyle('top', top_css);
          },
          'onComplete': function() {
            $knob.removeClass('active_knob');
          }
        });

        self.showSelectedMarker(user_checkin, choice);
      },

      'elementBlur': true,

      'onElementBlur': function(input) {
        self.suggestTimeout = window.setTimeout(function() {
          self.suggest.toggleFocus(false);
          self.toggleLoader(false);
        }, 500);
      },

      'onBlur': function(input) {
        if (self.blur_state) {
          return;
        }

        self.blur_state = true;

        self.suggestTimeout = window.setTimeout(function() {
          if (input.value != '') {
            self.setPosition(self.position);
          } else {
            self.setPosition({'accuracy': 0, 'latitude': 0, 'longitude': 0, 'name': '', 'vicinity': ''});
          }

          input.removeClass('checkinLoader');
          self.suggest.toggleFocus(false);
          self.toggleLoader(false);
        }, 500);
      },

      'onFocus': function() {
        self.blur_state = false;
        window.clearTimeout(self.suggestTimeout);
      }
    });
  },

  activate: $empty,
  deactivate : $empty,
  poll : function() {}
});