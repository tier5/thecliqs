/* $Id: Quiz.js 2010-05-25 01:44 michael $ */


Wall.Composer.Plugin.Date = new Class({

  Extends:Wall.Composer.Plugin.Interface,

  name:'date',

  years:null,
  months:null,
  days:null,

  birthdate: null,

  options:{
    title:'Select a date',
    lang:{},
    // Options for the date preview request
    requestOptions:{},
    // Delay to detect dates in input
    monitorDelay:600,
    debug:false,
    autoDetect:true
  },


  initialize:function (options) {
    this.params = new Hash(this.params);
    this.parent(options);
  },


  attach:function () {
    this.parent();
    this.makeActivator();

    this.monitorLastContent = '';
    this.monitorLastMatch = '';
    this.monitorLastKeyPress = $time();
    this.getComposer().addEvent('editorKeyPress', function () {
      this.monitorLastKeyPress = $time();
    }.bind(this));


    return this;
  },

  detach:function () {
    this.parent();
    if (this.interval) $clear(this.interval);
    return this;
  },

  activate:function () {
    if (this.active) return;
    this.parent();

    this.makeMenu();
    this.makeBody();

    // Generate body contents
    var self = this;
    var options = $merge({
      'data':{
        'format':'json',
        'date':this.getDate()
      },
      'onComplete':function (responseJSON, responseText) {

        if (responseJSON.status) {
          self.birthdate = responseJSON.birthdate;


          var div = new Element('div');
          div.set('html', responseJSON.html);

          var selects = div.getElements('select');

          selects.each(function (select) {
            if (select.getProperty('id') == 'date-year') {
              self.years = select.clone();
            } else
            if (select.getProperty('id') == 'date-month') {
              self.months = select.clone();
            } else {
              self.days = select.clone();
            }
          });

          selects.addEvent('change', function () {
            if (this.getProperty('id') == 'date-month') {
              self.monthChanged(div);
            } else
            if (this.getProperty('id') == 'date-year') {
              self.yearChanged(div);
            }
          });

          self.elements.body.set('html', '');
          div.inject(self.elements.body);
          self.yearChanged(div);
        }
      }
    }, this.options.requestOptions);

    // Inject loading
    this.makeLoading('empty');

    // Send request
    this.request = new Request.JSON(options);
    this.request.send();
  },

  monthChanged:function (div) {
    var self = this;
    var yearSelect = div.getElementById('date-year');
    var monthSelect = div.getElementById('date-month');
    var daySelect = div.getElementById('date-day');

    var days = this.daysInMonth(monthSelect.value.toInt() - 1, yearSelect.value);

    var d = new Date();
    daySelect.set('html', self.days.get('html'));

    daySelect.getChildren().each(function (option) {
      if (
        (option.value > days) ||
          (monthSelect.value == (d.getMonth() + 1) && yearSelect.value == d.getFullYear() && option.value > d.getDate()) ||
          (monthSelect.value == self.birthdate[1].toInt() && yearSelect.value == self.birthdate[0].toInt() && option.value > self.birthdate[2].toInt())
        ) {
        option.destroy();
      }
    });
  },

  yearChanged:function (div) {
    var self = this;
    var yearSelect = div.getElementById('date-year');
    var monthSelect = div.getElementById('date-month');

    var value = monthSelect.get('value');
    monthSelect.set('html', self.months.get('html'));

    var d = new Date();
    var m = 0;

    if ( yearSelect.value.toInt() == d.getFullYear().toInt() ){
      m = d.getMonth().toInt() + 1;
    } else
    if(yearSelect.value.toInt() == self.birthdate[0].toInt()){
      m = self.birthdate[1].toInt();
    }

    if(m > 0) {
      monthSelect.getChildren().each(function (option) {
        if (option.value > m) {
          option.destroy();
        }
      });
    }

    monthSelect.set(value);

    this.monthChanged(div);
  },

  daysInMonth:function (iMonth, iYear) {
    return 32 - new Date(iYear, iMonth, 32).getDate();
  },

  deactivate:function () {
    if (!this.active) return;
    this.parent();

    this.request = false;
  },

  getDate:function () {
    var date = timeline.tools.positionDate(timeline.composer.top);
    return date;
  }
});