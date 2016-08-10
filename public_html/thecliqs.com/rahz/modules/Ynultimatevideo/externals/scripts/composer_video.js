
/* $Id: composer_video.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;
Composer.Plugin.Ynultimatevideo = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'ynultimatevideo',

  options : {
    title : 'Add Ultimate Video',
    lang : {},
    // Options for the link preview request
    requestOptions : {},
    // Various image filtering options
    imageMaxAspect : ( 10 / 3 ),
    imageMinAspect : ( 3 / 10 ),
    imageMinSize : 48,
    imageMaxSize : 5000,
    imageMinPixels : 2304,
    imageMaxPixels : 1000000,
    imageTimeout : 5000,
    // Delay to detect links in input
    monitorDelay : 250
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    console.log(this.params);
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();

    // Generate body contents
    // Generate form

    this.elements.formInput = new Element('select', {
      'id' : 'compose-video-form-type',
      'class' : 'compose-form-input',
      'option' : 'test',
      'events' : {
        'change' : this.updateVideoFields.bind(this)
      }
    }).inject(this.elements.body);
    $('compose-video-form-type').options[0] = new Option(this._lang('Choose Source'), '0');
//    $('compose-video-form-type').options[1] = new Option(this._lang('YouTube'), '1');
//    $('compose-video-form-type').options[2] = new Option(this._lang('Vimeo'), '2');
    for (var i = 0; i < ynultimatevideo.types.length; i++) {
        var type = ynultimatevideo.types[i];
        $('compose-video-form-type').options[i + 1] = new Option(this._lang(type.title), type.value);
    }

    //if (this.options.allowed == 1 && this.options.type != 'message'){
    //  $('compose-video-form-type').options[3] = new Option(this._lang('My Computer'), '3');
    //}
    //link to full composer for now
    //$('compose-video-form-type').options[3] = new Option('My Computer','3');
    this.elements.formInput = new Element('input', {
      'id' : 'compose-video-form-input',
      'class' : 'compose-form-input',
      'type' : 'text',
      'style': 'display:none;'
    }).inject(this.elements.body);


    this.elements.formInputUpload = new Element('input', {
      'id' : 'fileToUpload',
      'name' : 'fileToUpload',
      'class' : 'compose-form-upload',
      'type' : 'file',
      'style': 'display:none;'
    }).inject(this.elements.body);

    // this.elements.previewDescription = new Element('div', {
    //   'id' : 'fileToUpload',
    //   'class' : 'fileToUpload',
    //   'html' : this._lang('To upload a video from your computer, please use our <a href="/ultimate-videos/create/type/3">full uploader</a>.'),
    //   'style': 'display:none;'
    // }).inject(this.elements.body);


    this.elements.formSubmit = new Element('button', {
      'id' : 'compose-video-form-submit',
      'class' : 'compose-form-submit',
      'style': 'display:none;',
      'html' : this._lang('Attach'),
      'events' : {
        'click' : function(e) {
          e.stop();
          this.doAttach();
        }.bind(this)
      }
    }).inject(this.elements.body);

    if (this.elements.formInput.style.display != 'none') {
    	this.elements.formInput.focus();
    }
  },

  deactivate : function() {
    // clean video out if not attached
    if (this.params.video_id)
      new Request.JSON({
        url: en4.core.basePath + 'ultimate-videos/delete',
        data: {
          format: 'json',
          video_id: this.params.video_id
        }
      }).send();
    if( !this.active ) return;
    this.parent();
  },

  // Getting into the core stuff now

  doAttach : function(e) {
    var val = this.elements.formInput.value;
    var upload = this.elements.formInputUpload.value;
    if( !val && !upload)
    {
      return;
    }
    var video_element = document.getElementById("compose-video-form-type");
    var type = video_element.value;
    if( !val.match(/^[a-zA-Z]{1,5}:\/\//) && type != 6 && type != 3)
    {
      val = 'http://' + val;
      this.params.set('uri', val);
    } else if (type == 3) {
      this.params.set('uri', upload);
    } else {
      this.params.set('uri', val);
    }
    // Input is empty, ignore attachment
    if( val == '' && upload == '') {
      e.stop();
      return;
    }
    if (type == 3) {
      // Send request
      var formData = new FormData();
      formData.append("fileToUpload", this.elements.formInputUpload.files[0]);
      formData.append("allow", 1);
      formData.append("type", type);
      this.request = new Browser.Request();
      this.request.addEventListener("load", this.doProcessResponseUpload.bind(this), false);
      this.request.open("POST", this.options.requestOptions.url, true);

      this.makeLoading('empty');
      this.request.send(formData);

    } else {
      // Send request to get attachment
      var options = $merge({
        'format' : 'json',
        'data' : {
          'format' : 'json',
          'uri' : val,
          'type': type
        },
        'onComplete' : this.doProcessResponse.bind(this)
      }, this.options.requestOptions);

      // Inject loading
      this.makeLoading('empty');

      // Send request
      this.request = new Request.JSON(options);
      this.request.send();
    }
  },
  doProcessResponseUpload: function (responseJSON, responseText) {
    var uploadJSON = JSON.decode(responseJSON.target.responseText);
    responseJSON = uploadJSON;

    var title = responseJSON.title || this.params.get('uri').replace('http://', '');
    this.params.set('title', responseJSON.title);
    this.params.set('description', responseJSON.description);
    this.params.set('photo_id', responseJSON.photo_id);
    this.params.set('video_id', responseJSON.video_id);
    this.params.set('type', responseJSON.type);
    this.elements.preview = Asset.image(responseJSON.src, {
      'id' : 'compose-video-preview-image',
      'class' : 'compose-preview-image',
      'onload' : this.doImageLoaded.bind(this)
    });
  },
  doProcessResponse : function(responseJSON, responseText) {
    // Handle error
    if( !responseJSON.type && (($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.video_id)) != 'number' )) {
      if( this.elements.loading ) this.elements.loading.destroy();
      this.makeError(responseJSON.message);

      //compose-video-error
      this.elements.ignoreValidation = new Element('a', {
        'href' : this.params.uri,
        'html' : this.params.title,
        'events' : {
          'click' : function(e) {
            e.stop();
            self.doAttach(this);
          }
        }
      }).inject(this.elements.previewTitle);

      return;
    }
    var title = responseJSON.title || this.params.get('uri').replace('http://', '');
    this.params.set('title', responseJSON.title);
    this.params.set('description', responseJSON.description);
    this.params.set('photo_id', responseJSON.photo_id);
    this.params.set('video_id', responseJSON.video_id);
    this.params.set('type', responseJSON.type);
    this.elements.preview = Asset.image(responseJSON.src, {
      'id' : 'compose-video-preview-image',
      'class' : 'compose-preview-image',
      'onload' : this.doImageLoaded.bind(this)
    });
  },

  doImageLoaded : function() {
    var self = this;

    if( this.elements.loading ) this.elements.loading.destroy();
    this.elements.preview.erase('width');
    this.elements.preview.erase('height');
    this.elements.preview.inject(this.elements.body);

    this.elements.previewInfo = new Element('div', {
      'id' : 'compose-video-preview-info',
      'class' : 'compose-preview-info'
    }).inject(this.elements.body);

    this.elements.previewTitle = new Element('div', {
      'id' : 'compose-video-preview-title',
      'class' : 'compose-preview-title'
    }).inject(this.elements.previewInfo);

	var href = this.params.uri;
    if(this.params.type == 6)
    {
    	var regex = /(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/;
    	var matches = regex.exec(href);
    	if(matches.length > 3)
			href = matches[3];
    }
    this.elements.previewTitleLink = new Element('a', {
      'href' : href,
      'html' : this.params.title,
      'events' : {
        'click' : function(e) {
          e.stop();
          self.handleEditTitle(this);
        }
      }
    }).inject(this.elements.previewTitle);

    this.elements.previewDescription = new Element('div', {
      'id' : 'compose-video-preview-description',
      'class' : 'compose-preview-description',
      'html' : this.params.description,
      'events' : {
        'click' : function(e) {
          e.stop();
          self.handleEditDescription(this);
        }
      }
    }).inject(this.elements.previewInfo);
    this.makeFormInputs();
  },

  makeFormInputs : function() {
    this.ready();
    this.parent({
      'photo_id' : this.params.photo_id,
      'video_id' : this.params.video_id,
      'title' : this.params.title,
      'description' : this.params.description
    });
  },

  updateVideoFields : function(element) {
    var video_element = document.getElementById("compose-video-form-type");
    var url_element = document.getElementById("compose-video-form-input");
    var post_element = document.getElementById("compose-video-form-submit");
    var upload_element = document.getElementById("fileToUpload");
    // clear url if input field on change
    $('compose-video-form-input').value = "";

  // If video source is empty
    if (video_element.value == 0)
    {
      upload_element.style.display = "none";
      post_element.style.display = "none";
      url_element.style.display = "none";
    } else {

	    // If video source is youtube or vimeo
	    if (video_element.value != 3)
	    {
	      upload_element.style.display = "none";
	      post_element.style.display = "block";
	      url_element.style.display = "block";
	      url_element.focus();
	    }

	    // if video source is upload
	    else if (video_element.value == 3)
	    {
	      upload_element.style.display = "block";
	      post_element.style.display = "block";
	      url_element.style.display = "none";
	    }
    }
  },
  handleEditTitle : function(element) {
    element.setStyle('display', 'none');
    var input = new Element('input', {
      'type' : 'text',
      'value' : htmlspecialchars_decode(element.get('html').trim()),
      'events' : {
        'blur' : function() {
          if( input.value.trim() != '' ) {
            this.params.title = input.value;
            element.set('html', this.params.title);
            this.setFormInputValue('title', this.params.title);
          }
          element.setStyle('display', '');
          input.destroy();
        }.bind(this)
      }
    }).inject(element, 'after');
    input.focus();
  },
  handleEditDescription : function(element) {
    element.setStyle('display', 'none');
    var input = new Element('textarea', {
      'html' : htmlspecialchars_decode(element.get('html').trim()),
      'events' : {
        'blur' : function() {
          if( input.value.trim() != '' ) {
            this.params.description = input.value;
            element.set('html', this.params.description);
            this.setFormInputValue('description', this.params.description);
          }
          else{
            this.params.description = '';
            element.set('html', '');
            this.setFormInputValue('description', '');
          }
          element.setStyle('display', '');
          input.destroy();
        }.bind(this)
      }
    }).inject(element, 'after');
    input.focus();
  }
});



})(); // END NAMESPACE
