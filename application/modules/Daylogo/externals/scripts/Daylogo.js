/* $Id: Daylogo.js 2012-08-16 16:27 nurmat $ */

var Daylogo =
{
  url: {
    'form': '',
    'remove': '',
    'enable': '',
    'disable': '',
    'remove_photo': '',
    'edit': ''
  },

  time_out: 500,
  logo_id: 0,

  $loader: null,
  $elm: null,
  $form: null,

  init: function ()
  {
    this.$loader = $('daylogo_loader');
    this.$elm = $('daylogo');
    this.$form = $('daylogo-form');
  },

  goForm: function ()
  {
    this.loadTab('form');
  },

  formLogo: function (logo_id)
  {
    var self = this;
    if (logo_id)
    {
        this.request(this.url.edit, {id: logo_id}, function (obj){
            if (obj.result){
                self.loadTab('form');
                self.formReset();

                var $title = self.$form.getElement('h3');
                if ($title){
                    $title.set('html', en4.core.language.translate('DAYLOGO_EDIT_TITLE'));
                }
                self.$form.getElements('.datepicker_container.starttime-container input')
                    .set('value', obj.logo_info.start_date);

                self.$form.getElements('.datepicker_container.endtime-container input')
                    .set('value', obj.logo_info.end_date);

                if (obj.photo_html){
                    self.$form.getElement('#logo_photo-demo-status').setStyle('display', 'none');
                    self.$form.getElement('#logo_photo-demo-list')
                        .setStyle('display', 'block')
                        .set('html', obj.photo_html);
                }
                self.$form.title.value = obj.logo_info.title;
                self.$form.id.value = logo_id;
            }
        });
    }
    else
    {
      self.loadTab('form');
      self.formReset();

      var $title = self.$form.getElement('h3');
      if ($title){
        $title.set('html', en4.core.language.translate('DAYLOGO_CREATE_TITLE'));
      }
    }

  },

  formSubmit: function (form)
  {
    var self = this;
    this.request(this.url.form, $(form).toQueryString(), function (obj){
      if (obj.result){
        self.showMessage(obj.html);
        setTimeout(function (){ self.view(obj.id); }, self.time_out);
      } else {
        self.showMessage(obj.html);
      }
    });
    return false;
  },

  formReset: function ()
  {
    window.logo_photo_up.fileList.each(function (file){
        file.remove();
    });
    this.$form.title.value = '';
    this.$form.starttime.value = '';
    this.$form.endtime.value = '';

    this.$form.getElements('.datepicker_container input').set('value', '');
    this.$form.id.value = 0;

    $('logo_photo-demo-list').setStyle('display', 'none').empty();
    $('logo_photo-demo-status').setStyle('display', 'block');

  },

  formCancel: function ()
  {
    location.href = 'admin/daylogo';
  },

  removePhoto: function (photo_id)
  {
    this.request(this.url.remove_photo, {'photo_id': photo_id});

    this.$form.getElement('#logo_photo-demo-status').setStyle('display', 'block');
    this.$form.getElement('#logo_photo-demo-list').empty().setStyle('display', 'none');

  },

  loadTab: function (tab)
  {
    var $tab = this.$elm.getElement('.tab_' + tab);
    this.$elm.getElements('.tab').addClass('hidden');
    $tab.removeClass('hidden');
    return $tab;
  },

    view: function (id)
    {
        location.href = 'admin/daylogo';
    },

  remove: function (id)
  {
    var self = this;
    he_show_confirm(
      en4.core.language.translate('DAYLOGO_DELETE_TITLE'),
      en4.core.language.translate('DAYLOGO_DELETE_DESCRIPTION'),
      function (){
        self.request(self.url.remove, {'logo_id': id}, function (obj){
          if (obj.result){
            self.showMessage(obj.html);
            setTimeout(function (){ self.view(); }, self.time_out);
          } else {
            self.showMessage(obj.html);
          }
        });
      }
    );
  },

    enable: function (id)
    {
        var self = this;
        he_show_confirm(
            en4.core.language.translate('DAYLOGO_ENABLE_TITLE'),
            en4.core.language.translate('DAYLOGO_ENABLE_DESCRIPTION'),
            function (){
                self.request(self.url.enable, {'logo_id': id}, function (obj){
                    if (obj.result){
                        self.showMessage(obj.html);
                        setTimeout(function (){ self.view(); }, self.time_out);
                    } else {
                        self.showMessage(obj.html);
                    }
                });
            }
        );
    },
    disable: function (id)
    {
        var self = this;
        he_show_confirm(
            en4.core.language.translate('DAYLOGO_DISABLE_TITLE'),
            en4.core.language.translate('DAYLOGO_DISABLE_DESCRIPTION'),
            function (){
                self.request(self.url.disable, {'logo_id': id}, function (obj){
                    if (obj.result){
                        self.showMessage(obj.html);
                        setTimeout(function (){ self.view(); }, self.time_out);
                    } else {
                        self.showMessage(obj.html);
                    }
                });
            }
        );
    },

  showMessage: function (html)
  {
    this.loadTab('message').set('html', html);
  },

  request: function (url, data, callback)
  {
    var self = this;

    if (typeof(data) == 'string')
    {
      data += '&format=json&no_cache=' + Math.random();
    }
    else if (typeof(data) == 'object')
    {
      data.format = 'json';
      data.nocache = Math.random();
    }

    if (self.$loader != null)
        self.$loader.removeClass('hidden');

    var request = new Request.JSON({
      secure: false,
      url: url,
      method: 'post',
      data: data,
      onSuccess: function(obj)
      {
        self.$loader.addClass('hidden');
        if (callback){ callback(obj); }
      }
    }).send();

  }

};