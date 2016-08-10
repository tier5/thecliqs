/**
 * Created by JetBrains PhpStorm.
 * User: mt.uulu
 * Date: 1/20/12
 * Time: 12:08 PM
 */
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

var PageTimeLine = new Class({
    Implements:[Options, Chain],

    options:{
        content_id:'global_content',
        timeline_id:'timeline',

        dates_url:'',
        life_event_url:''
    },
    content:null,
    timeline:null,

    now:{},
    last_month:{},
    years:{},
    data:{},

    isOwner:false,

    init:function () {
        this.content = document.getElementById(this.options.content_id);
        this.timeline = document.getElementById(this.options.timeline_id);
        this.scroller.init();
        this.feed.init();
        this.separator.scan();
        this.composer.init();
        this.initEvents();
    },

    initEvents:function () {
        var self = this;
        var top = 0;

        if (this.isOwner) {

            self.timeline.removeEvents();
            var plus = self.timeline.getElement('.plus');
            self.timeline.addEvents({
                'click':function (e) {
                    self.composer.move(e);
                },
                'mouseenter':function () {
                    var obj = self.timeline;
                    if (obj != null && obj.offsetParent != null) {
                        do {
                            top += obj.offsetTop;
                        } while (obj = obj.offsetParent);
                    }
                    plus.setStyle('display', 'block');
                },
                'mouseleave':function () {
                    plus.setStyle('display', 'none');
                    top = 0;
                },
                'mousemove':function (e) {
                    plus.setStyle('top', (e.page.y - top - 13) + 'px');
                }
            });
        }

        document.addEvent('scroll', function () {
            self.scroller.checkPosition();
            self.pagination.autoLoad();
        });
    },

    addData:function (data) {
        var self = timeline;
        for (year in data) {
            if (year == null) continue;
            self.data[year] = data[year];
        }
    },


    items:{
        align:function (date) {
            var self = timeline;
            var items = [];
            var aligns = {left:0, right:0};

            if (date != null) {
                var date_arr = self.tools.dateToArray(date, true);
                var cl = 'li[class*=' + date_arr.year;
                if (date_arr.month != null) cl += '-' + date_arr.month;
                cl += ']';
                items = self.feed.getItems(cl);
            } else {
                items = self.feed.getItems();
            }

            items.each(function (el) {
                aligns = self.items.alignElement(el, aligns);
            });
        },

        alignElement:function (el, params) {
            if (el.hasClass('sep')) {
                return;
            }

            if (params == null) {
                params = {right:0, left:0}
            }

            if (el.hasClass('starred')) {
                params = {right:0, left:0}
                return;
            }

            if (params.left <= params.right) {
                params.left += el.clientHeight + 30;
                el.removeClass('r').addClass('l');
            } else {
                params.right += el.clientHeight + 30;
                el.removeClass('l').addClass('r');
            }

            return params;
        },

        resize:function (el) {
            var videos = el.getElements('.video_object');

            videos.each(function (el) {
                var object = el.getElement('object');
                var embed = el.getElement('embed');

                if (object != null && object.width != 380) {
                    object.width = 380;
                    object.height = 300;
                }

                if (embed != null && embed.width != 380) {
                    embed.width = 380;
                    embed.height = 300;
                }
            });
        }
    },


    feed:{
        id:['tl-page-feed', 'activity-feed'],
        tl:null,
        ob:null,
        activity:null,
        is_loading:false,

        object:{
            get:function () {
                if (this.ob != null) {
                    return this.ob;
                }

                var id = timeline.feed.tl.getElement('.wallFeed').getProperty('id');
                this.ob = Wall.feeds.get(id);

                return this.ob;
            },

            setLasts:function (date, id) {
                var self = timeline;
                var feed = this.get();

                var d1 = self.tools.convertToDate(date);
                var d2 = self.tools.convertToDate(feed.options.last_date);
                if (d1 > d2 || (d1 >= d2 && id > feed.options.last_id)) {
                    feed.setLastId(id);
                    feed.setLastDate(date);
                    return true;
                }

                return false;
            }
        },

        init:function () {
            this.tl = document.getElementById(this.id[0]);
            this.activity = $(this.tl).getElementById(this.id[1]);
            this.loader.init();
            (new Fx.Tween(this.tl)).start('opacity', '0', '1');
        },

        setDate:function (el) {
            if (el.hasClass('d')) return;
            var self = timeline;
            self.items.resize(el);
            var date = null;

            if (null == (date = self.tools.itemDate(el))) return;

            el.addEvent('click', function () {
                self.items.align(date);
                var obj = this.getElement('object');
                if (obj != null && obj.width != 380) {
                    obj.width = 380;
                    obj.height = 300;
                }
            });

            var date_arr = self.tools.dateToArray(date);


            el.addClass('d');
            el.store('date', date);
        },

        get:function () {
            if (this.activity != null)
                return this.activity;

            return this.tl.getElementById(this.id[1]);
        },

        getItems:function (cl) {
            return this.get().getChildren(cl);
        },

        getLast:function (date) {
            if (date == null) {
                var last = this.get().getLast('li');

                while (last != undefined && last != null && last.hasClass('le')) {
                    last = last.getPrevious();
                }

                return last;
            }

            var self = timeline;

            if (!self.separator.exists(date)) {
                var date_arr = self.tools.dateToArray(date, true);
                if (date_arr.year == null) return;


                var cl = 'li[class*=' + date_arr.year;
                if (date_arr.month != null) cl += '-' + date_arr.month;
                cl += ']';
                var lis = self.feed.getItems(cl), last = self.separator.get(date_arr.year + '-' + date_arr.month);

                if (date_arr.day == null) {
                    var index = lis.length;
                    do {
                        index--;
                        last = lis[index];
                    } while ($type(last) != 'element' || last.hasClass('le'));

                    return last;
                }

                var tmp_cl = cl = 'li[class*=' + date_arr.year + '-' + date_arr.month + '-' + date_arr.day + ']';
                var tmp_lis = self.feed.getItems(tmp_cl);
                if (tmp_lis.length > 0) {
                    lis = tmp_lis;
                }

                for (var i = 0; i < lis.length; i++) {
                    if (null == (date = lis[i].retrieve('date'))) continue

                    var li_d = self.tools.dateToArray(date, true);

                    if (li_d.day >= date_arr.day || (last == null && li_d.day == null)) {
                        last = lis[i];
                    } else
                    if (last != null) {
                        break;
                    }
                }

                while (last != null && last.hasClass('le')) {
                    last = last.getPrevious();
                }

                return last;
            }

            var li = self.separator.get(date);
            var cl = 'm';

            if (li.hasClass('y')) {
                cl = 'y';
            }

            var tmp_li = null;
            do {
                tmp_li = li;
                li = li.getNext('li');
            } while (!(li == null || li.hasClass(cl) || li.hasClass('y')));

            if (li == null) {
                li = tmp_li;
            }

            if (!li.hasClass('e')) {
                li = li.getPrevious('li');
            }


            while (li.hasClass('le')) {
                li = li.getPrevious();
            }

            return li;
        },

        loader:{
            'cl':'loader',
            'item':null,
            init:function () {
                var self = timeline;
                this.item = self.feed.tl.getElement('.' + this.cl);
            },
            get:function () {
                return this.item;
            },
            show:function (el) {
                var self = timeline;
                var li = new Element('li', {'class':'sep loader'});
                var clone = this.item.clone();
                clone.inject(li);

                li.inject(el, 'after');

                return li;
            }
        },

        load:function (el) {

            if (this.is_loading || el == null)  return;

            var self = timeline;

            if (el.hasClass('le')) {
                return this.loadLifeEvent(el);
            }

            var next = self.separator.getNext(el);
            if ($type(next) == 'element' && next.retrieve('date') != null) {
                return this.loadInterval(el, next);
            }

            this.is_loading = true;

            var feed = this.object.get();

            var date_arr = self.tools.dateToArray(el.retrieve('date'));
            var date_str = self.tools.arrayToDate(date_arr);
            var params = {
                'maxdate':date_str,
                'maxid':el.retrieve('maxid'),
                'limit':10
            }

            var loader = this.loader.show(el);

            feed.loadFeed(params, 'after', function () {
                self.feed.afterLoad(el);
            }, {'viewall':true}, el);

            return self;
        },

        loadInterval:function (previous, next) {
            if (this.is_loading)  return;

            var self = timeline;

            this.is_loading = true;

            var feed = this.object.get();

            var prev_arr = self.tools.dateToArray(previous.retrieve('date')), prev_str = self.tools.arrayToDate(prev_arr);
            var next_arr = self.tools.dateToArray(next.retrieve('date')), next_str = self.tools.arrayToDate(next_arr);
            var params = {
                'maxdate':prev_str,
                'maxid':previous.retrieve('maxid'),
                'mindate':next_str,
                'minid':next.retrieve('maxid')
            };

            var loader = this.loader.show(previous);

            feed.loadFeed(params, 'after', function () {
                self.feed.afterLoad(previous);
            }, {'viewall':true}, previous);

            return self;
        },

        loadLifeEvent:function (el) {
            if (this.is_loading || el == null)  return;

            if ($type(el) != 'element') return;

            this.is_loading = true;

            var self = timeline;

            var type = el.retrieve('date')
            var loader = self.feed.loader.show(el);

            new Request.HTML({
                'method':'get',
                'url':self.options.life_event_url,
                'data':{'format':'html', 'type':type},
                'evalScripts':false,
                'onComplete':function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    loader.destroy();

                    var div = new Element('div');
                    div.set('html', responseHTML);

                    Smoothbox.bind(div);

                    div.getChildren().each(function (element) {
                        element.inject(el, 'after');
                    });

                    eval(responseJavaScript);
                    en4.core.runonce.trigger();
                    self.feed.afterLoad(el);
                }
            }).send()

            return el;
        },

        afterLoad:function (el) {
            var self = timeline;
            var date = null;
            if (null == (date = el.retrieve('date', null))) {
                return;
            }

            var date_arr = self.tools.dateToArray(date);
            var y = 'y' + date_arr.year, m = 'm' + date_arr.month;

            el.removeClass('active');
            var a = null;
            if (null != (a = el.getElement('a'))) {
                a.removeEvents();
            }

            if (el.hasClass('e')) {
                el.destroy();
            } else
            if (el.hasClass('y')) {
                el.getElement('div').getElement('a').set('text', date_arr.year);
            } else
            if (el.hasClass('m') && y in self.years) {

                if (date == self.last_month['key']) {
                    el.getElement('div').getElement('a').set('text', self.last_month['name']);
                } else {
                    el.getElement('div').getElement('a').set('text', self.years[y][m]['name'] + ', ' + date_arr.year);
                }
            }

            if (self.composer.rev != null) {
                self.composer.highlight();
            }

            tl_listener.init();

            this.is_loading = false;
        }
    },


    separator:{
        prefix:'sep',

        scan:function (ranges) {
            var self = timeline;
            var items = [];
            var aligns = {left:0, right:0};

            if ($type(ranges) == 'string') {
                ranges = ranges.split(' ');
                ranges.each(function (date) {
                    if (date.trim().length > 4) {
                        var tmp_items = self.feed.getItems('li[class*=' + date.trim() + ']');
                        items = tmp_items.concat(items);
                    }
                });
            }

            if (items.length == 0) {
                items = self.feed.getItems();
            }

            items.each(function (el) {
                if ($type(el) == 'element') {
                    if (el.hasClass('utility-viewall')) {
                        self.pagination.toSeparator(el);
                    } else {
                        if (!el.hasClass('d')) {
                            self.feed.setDate(el);
                            self.separator.lookup(el);
                        }
                    }
                }
            });

            items.each(function (el) {
                aligns = self.items.alignElement(el, aligns);
            });


            return self;
        },

        lookup:function (el) {
            var previous = el.getPrevious('li');

            var self = timeline;
            var date = null;

            if (null == (date = self.tools.dateToArray(el.retrieve('date'), true))) {
                return null;
            }

            var key = date.year + '-' + date.month;

            if (this.exists(key)) {
                return null;
            }

            var y = 'y' + date.year;
            var m = 'm' + date.month;


            var text = '';
            if (!((self.years != null && y in self.years && m in self.years[y]) || (self.last_month != null && key == self.last_month['key']))) {
                return null;
            }

            var li = null;
            if (key == self.last_month['key']) {
                li = this.add({
                    'date':key,
                    'text':self.last_month['name'],
                    'class':'m ' + self.last_month.year + ' ' + self.last_month.month,
                    'max_id':self.last_month['max_id']
                }, previous);
            } else {

                if (!this.exists(y) && y in self.years) {
                    li = this.add({
                        'date':date.year,
                        'text':date.year,
                        'class':'y ' + date.year,
                        'max_id':self.scroller.getMaxId(y)
                    }, previous);

                    if (li != null) {
                        previous = li;
                    }
                }

                if (m in self.years[y]) {
                    li = this.add({
                        'date':key,
                        'text':self.years[y][m]['name'] + ', ' + date.year,
                        'class':'m ' + self.years[y][m]['month'] + ' ' + self.years[y][m]['year'],
                        'max_id':self.years[y][m]['max_id']
                    }, previous);
                }
            }

            return li;
        },

        add:function (params, el) {
            if (this.exists(params['date'])) {
                return null;
            }
            var self = timeline;

            var li = new Element('li', {'id':this.prefix + params['date'], 'class':'sep ' + params['class']});

            var div = new Element('div');
            var a = new Element('a', {'text':params['text'], 'href':'javascript:void(0);'});
            li.store('date', params['date']);
            li.store('maxid', params['max_id']);

            div.grab(a);
            li.grab(div);

            if (el != null) {
                li.inject(el, 'after');
            } else {
                li.inject(self.feed.get(), 'top');
            }


            if (li.hasClass('active')) {
                a.addEvent('click', function () {
                    self.feed.load(li);
                });
            }

            return li;
        },

        get:function (date) {
            return document.getElementById(this.prefix + date);
        },

        getNext:function (item, cl) {
            var previous = item, next;

            if ($type(item) == 'string') {
                previous = this.get(item);
            }

            if ($type(previous) != 'element') {
                return null
            }

            if ($type(cl) != 'string') {
                return previous.getNext('.sep');
            }

            return previous.getNext('.sep.' + cl);
        },

        load:function (item) {
            var self = timeline;

            var date = self.tools.dateToArray(item, true);
            var key = date.year + '-' + date.month;
            var last = null;
            var el = null;

            if (key == self.last_month['key']) {
                if ((el = this.exists(date.year)) && el.hasClass('active')) {
                    last = el;
                } else {
                    last = this.loadLastMonth();
                }

                self.feed.load(last);
                self.scroller.scroll(last);
            } else

            if (!this.exists(date.year)) {
                last = this.loadYears(date);
                self.feed.load(last);
                self.scroller.scroll(last);
            } else

            if (!(this.exists(date['year'] + '-' + date['month']))) {
                last = this.loadMonths(date);
                self.feed.load(last);
                self.scroller.scroll(last);
            }

            return self.feed.get();
        },

        loadLastMonth:function () {
            var self = timeline, last = null;

            if (this.exists(self.last_month.key)) {
                last = self.feed.getLast(self.last_month.key);

                if (last.hasClass('y')) {
                    last.getElement('a').set('text', en4.core.language.translate('Earlier in %1s', ' ' + self.last_month['name']));
                    last.removeClass('y');
                    last.addClass('m');
                }

                return last;
            }

            last = self.feed.getLast(self.now['key']);

            if (last.hasClass('sep')) {
                last.getElement('a').set('text', en4.core.language.translate('Earlier in %1s', ' ' + self.now['name']));
                last.removeClass('y');
                last.addClass('m');
            }

            last = this.add({
                'date':self.last_month.key,
                'text':en4.core.language.translate('Show %1s', ' ' + self.last_month['name']),
                'class':'active m ' + self.last_month.year + ' ' + self.last_month.month,
                'max_id':self.last_month['max_id']
            }, last);

            return last;
        },

        loadMonths:function (date) {
            var self = timeline;
            var year = 'y' + date.year, month = 'm' + date.month;
            var months = [], i = 0, previous = null, last = null;
            for (m in self.years[year]) {
                var key = date.year + '-' + self.years[year][m]['month'];

                if (self.years[year][m]['month'].toInt() > date.month.toInt() || (self.years[year][m]['month'].toInt() == date.month.toInt() && !this.exists(key))) {
                    if (this.exists(key)) {
                        previous = m;
                        months = [];
                        i = 0;
                    } else {
                        months[i] = m;
                        i++;
                    }
                }
            }

            if (months.length == 0) {
                return null;
            }

            if (previous == null) {
                last = this.get(date.year);
            } else {
                last = self.feed.getLast(date.year + '-' + self.years[year][previous]['month']);

                if (last.hasClass('y')) {
                    var a = last.getElement('a');
                    a.set('text', en4.core.language.translate('Earlier in %1s', ' ' + self.years[year][previous]['name'] + ', ' + date.year));
                    last.removeClass('y');
                    last.addClass('m');
                }
            }

            for (i = 0; i < months.length; i++) {
                last = this.add({
                    'date':date.year + '-' + self.years[year][months[i]]['month'],
                    'text':en4.core.language.translate('Show %1s', ' ' + self.years[year][months[i]]['name'] + ', ' + date.year),
                    'class':'active m ' + date.year + ' ' + self.years[year][months[i]]['month'],
                    'max_id':self.years[year][months[i]]['max_id']
                }, last);
            }

            return last;
        },

        loadYears:function (date) {
            var self = timeline;
            var year = 'y' + date.year;

//            if (!( year in self.years)) {
//                return null;
//            }

            var month = 'm' + date.month, years = [], i = 0, previous = null, last = null;

            if (date.year == self.last_month.year) {
                last = this.loadLastMonth();

                for (m in self.years[year]) {
                    month = m;
                    break;
                }
                if (self.years[year]) {
                    last = this.add({
                        'date':date.year + '-' + self.years[year][month]['month'],
                        'text':self.years[year][month]['name'] + ', ' + date.year,
                        'class':'active m ' + date.year + ' ' + self.years[year][month]['month'],
                        'max_id':self.years[year][month]['max_id']
                    }, last);
                }
//                else {
//                    last = this.add({
//                        'date':date.year,
//                        'text':self.years[year][month]['name'] + ', ' + date.year,
//                        'class':'active m ' + date.year,
//                        'max_id':self.years[year][month]['max_id']
//                    }, last);
//                }
                return last;
            }


            for (var y in self.years) {
                y = y.substr(1).toInt();
                if (y > date.year || (y == date.year && !this.exists(y))) {
                    if (this.exists(y)) {
                        previous = y;
                        years = [];
                        i = 0;
                    } else {
                        years[i] = y;
                        i++;
                    }
                }
            }

            if (years.length == 0) {
                return null;
            }
            last = self.feed.getLast(previous);

            var ldate = self.tools.dateToArray(last.retrieve('date'));

            var lkey = ldate.year + '-' + ldate.month;

            var temp_years = [], j = 0;
            for (i = 0; i < years.length; i++) {
                if (ldate.year == years[i]) {
                    temp_years = [];
                    j = 0;
                } else {
                    temp_years[j] = years[i];
                    j++;
                }
            }

            years = temp_years;

            if (years.length == 0) {
                return last;
            }

            for (i = 0; i < years.length; i++) {
                last = this.add({
                    'date':years[i],
                    'text':en4.core.language.translate('Show %1s', ' ' + years[i]),
                    'class':'active y ' + years[i],
                    'max_id':self.scroller.getMaxId('y' + years[i])
                }, last);
            }

            return last;
        },

        loadLifeEvent:function (date, params) {
            var self = timeline;

            var el = null;
            if (null != (el = this.exists(params.type))) {
                return el;
            }

            var date_arr = self.tools.dateToArray(date);

            if (self.years != null) {
                this.loadYears(date_arr)
            }

            var last = self.feed.getLast(date);

            if (last == null) {
                last = self.feed.getLast();
            }

            last = this.add({
                'date':params.type,
                'text':params.text,
                'class':'active le',
                'max_id':'0'
            }, last);

            return last;
        },

        exists:function (date) {
            var self = timeline, el = null;
            if (null != (el = self.feed.get().getElementById(this.prefix + date))) {
                return el;
            }

            false;
        }
    },


    pagination:{
        toSeparator:function (el) {
            var self = timeline;
            var li = null, params = null;

            if (null == (li = el.getPrevious('.tli'))) {
                return;
            }

            var rev = el.getElement('.pagination').getElement('a').get('rev').split('|');
            var max_id = rev[0].substr(6).toInt() - 1;
            var date_str = rev[1].substr(8);
            var date = self.tools.dateToArray(date_str), year = 'y' + date.year, month = 'm' + date.month, key = date.year + '-' + date.month;
            var li_d = self.tools.dateToArray(li.retrieve('date'));

            var text = null, classes = null;
//      if (date.year == li_d.year) {
            if (el.getNext('li') != null && el.getNext('li').hasClass('m')) {

                if (key == self.now.key) {
                    text = en4.core.language.translate('Earlier in %1s', ' ' + self.now.name);
                    max_id = self.now.max_id;
                } else
                if (key == self.last_month.key) {
                    text = en4.core.language.translate('Earlier in %1s', ' ' + self.last_month.name);
                    max_id = self.last_month.max_id;
                } else {
                    text = en4.core.language.translate('Earlier in %1s', ' ' + self.years[year][month]['name'] + ', ' + date.year);
                    max_id = self.years[year][month]['max_id'];
                }

                classes = 'active m e ' + key;
            } else {
                text = en4.core.language.translate('Earlier in %1s', ' ' + date.year);
                classes = 'active y e ' + key;
            }

            params = {
                'date':date_str,
                'text':text,
                'class':classes,
                'max_id':max_id
            }

            li = self.separator.add(params, el);

            el.destroy();
//      }

            return li;
        },

        autoLoad:function () {
            var self = timeline;

            if (self.feed.is_loading) return;

            var paginators = self.feed.get().getElements('.sep.active.e, .sep.active.m');
            if (paginators.length <= 0) {
                return;
            }

            var doc_top = document.getScroll().y;
            var doc_bottom = doc_top + parseInt((document.getHeight() / 2));

            var top = doc_top + 800;
            var bottom = doc_top + document.getHeight() + 2000;

            for (var i = 0; i < paginators.length; i++) {
                var y = paginators[i].getPosition().y;

                //@todo Auto Select scroller items;
//        if(!paginators[i].hasClass('active') && doc_bottom > y && y > doc_top){
//
//          var li = self.scroller.get().getElement('li[rev=' + paginators[i].retrieve('date') + ']');
//          if(li != null){
//            var span = li.getElement('span');
//            self.scroller.activate(span);
//            self.scroller.deactivateAll(span);
//          }
//        }

                if (paginators[i].hasClass('active') && bottom > y && y > top && !paginators[i].retrieve('loaded')) {
                    self.feed.load(paginators[i]);
                    break;
                }
            }
        }
    },


    composer:{
        id:'tl-composer',
        top:0,
        element:null,
        rev:null,

        init:function () {
            this.element = document.getElementById(this.id);
        },

        move:function (e) {
            var self = timeline;
            this.get().setStyles({'opacity':0, 'display':'block'});
            wall_object.compose.close();
            this.top = self.tools.topOffset(e);
            this.get().setStyles({'top':this.top + 'px', 'opacity':1});
        },

        get:function () {
            return this.element;
        },

        close:function () {
//            this.get().setStyle('display', 'none');
        },

        show:function () {
            this.get().setStyle('display', 'block');
        },

        inject:function (el) {
            if (el == null || el.getElement('.timestamp') == null) {
                return null;
            }

            var self = timeline;

            var date = self.tools.dateToArray(self.tools.itemDate(el));
            var y = 'y' + date.year, m = 'm' + date.month, key = date.year + '-' + date.month;
            var sep = null, li = null;

            self.feed.setDate(el);

            if (self.now != null && key == self.now.key) {
                li = self.feed.getLast(key + '-' + date.day);
                if (li.hasClass('sep')) {
                    el.inject(li, 'before');
                } else {
                    el.inject(li, 'after');
                }

                span = self.scroller.get().getElement('li[rev=now]').getElement('span.month');
                span.fireEvent('click');
                self.separator.scan(key);

                setTimeout(function () {
                    self.composer.highlight(el)
                }, '500');
                return;
            }

            if (null != (sep = self.separator.exists(key) )) {
                if (key == self.last_month.key) {
                    li = self.scroller.get().getElement('li[rev=' + key + ']');
                } else {
                    li = self.scroller.get().getElement('li[rev=' + date.year + ']');
                    span = li.getElement('span.year');
                    if (!li.hasClass('active')) {
                        span.fireEvent('click');
                    }

                    li = li.getElement('.months').getElement('li[rev=' + key + ']');
                }

                span = li.getElement('span.month');

                if (sep.hasClass('active')) {
                    this.rev = el.get('rev');
                    self.feed.load(sep);
                } else {
                    li = self.feed.getLast(key + '-' + date.day);

                    if (li.hasClass('sep')) {
                        el.inject(li, 'before');
                    } else {
                        el.inject(li, 'after');
                    }

                    el.inject(li, 'after');
                    span.fireEvent('click');
                    self.separator.scan(key);

                    setTimeout(function () {
                        self.composer.highlight(el)
                    }, '500');
                }

                return;
            }

            if (self.years != null && (!(y in self.years) || !(m in self.years[y]))) {
                this.rev = el.get('rev');

                self.scroller.reload(function () {
                    var span = self.scroller.get().getElement('span.year[rev=' + date.year + ']');
                    if (span == null) {
                        location.reload();
                        return;
                    }

                    span.fireEvent('click');

                    setTimeout(function () {
                        var span2 = span.getParent('li').getElement('li[rev=' + key + ']').getElement('span');
                        span2.fireEvent('click');
                    }, '700');
                });

                return;
            }

            if (null == self.separator.exists(date.year) || null == self.separator.exists(key)) {
                var span = self.scroller.get().getElement('span.year[rev=' + date.year + ']');

                if (span == null) {
                    location.reload();
                    return;
                }

                span.fireEvent('click');

                setTimeout(function () {
                    var span2 = span.getParent('li').getElement('li[rev=' + key + ']').getElement('span');
                    span2.fireEvent('click');
                }, '500');

                return;
            }
        },

        highlight:function (el) {
            if (el == null && this.rev == null) return;
            var self = timeline;

            if (el == null) {
                el = self.feed.get().getElement('li[rev=' + this.rev + ']');
            }

            if (el == null) return;

            var toBg = el.getStyle('background-color');
            el.addClass('just_loaded');
            var fromBg = el.getStyle('background-color');
            self.scroller.scroll(el);

            setTimeout(function () {
                var myFx = new Fx.Tween(el);
                myFx.start('background-color', fromBg, toBg).chain(function () {
                    el.removeClass('just_loaded');
                });
            }, '2000');

            this.rev = null;
        }
    },


    scroller:{
        id:'tl-dates',
        element:null,
        months:[],
        years:[],
        fx:null,

        life_events:[],

        get:function () {
            if (this.element == null) {
                this.element = document.getElementById(this.id);
            }

            return this.element;
        },

        init:function () {
            var self = timeline;

            this.months = $(this.get()).getElements('.month');
            this.years = $(this.get()).getElements('.year');
            this.life_events = $(this.get()).getElements('.life-event');

            this.fx = new Fx.Scroll(document.body);

            this.years.each(function (el) {
                self.scroller.deactivate(el);
            });

            this.initEvents(this.months);
            this.initEvents(this.years);
            this.initEvents(this.life_events);
        },

        initEvents:function (items) {
            var self = timeline;

            items.addEvent('click', function () {

                if (this.hasClass('life-event')) {
                    return self.scroller.lifeEvent(this);
                }

                var parent = this.getParent();
                var date = parent.get('rev');
                var el = null;

                if (null != (el = self.separator.exists(date))) {
                    self.scroller.scroll(el);

                    if (el.hasClass('active')) {
                        self.feed.load(el);
                    }

                } else {
                    if (date != 'now') {
                        el = self.separator.load(date)
                    } else {
                        el = self.feed.tl
                    }

                    self.scroller.scroll(el);
                }


                if (!this.getParent('li').hasClass('active')) {
                    self.scroller.deactivateAll(this);
                }
                self.scroller.activate(this);
            });
        },

        lifeEvent:function (el, dont_activate) {
            var self = timeline;

            if ($type(el) == 'string') {
                el = this.get().getElement('li[rev=' + el + ']').getElement('span');
            }

            var type = el.getParent('li').get('rev');

            if (type == null) return;

            var date = el.get('rev');
            var sep = self.separator.loadLifeEvent(date, {
                'type':type,
                'text':el.get('title')
            });

            if ($type(sep) != 'element') return;

            if (sep.hasClass('active')) {
                self.feed.loadLifeEvent(sep);
            }

            if (!dont_activate) {
                self.scroller.scroll(sep);
                self.scroller.deactivateAll(el);
                self.scroller.activate(el);
            }
        },

        reload:function (callback) {
            var self = timeline;

            new Request.JSON({
                'method':'get',
                'url':self.options.dates_url,
                'data':{'format':'json'},
                'onSuccess':function (response) {
                    if (response.status) {
                        self.scroller.get().set('html', response.html);
                        self.years = response.dates.years;
                        self.scroller.init();
                    }

                    // callback
                    if ($type(callback) == 'function') {
                        callback(response);
                    }
                }
            }).send();
        },

        checkPosition:function () {
            var self = timeline;
            var scroller = this.get();

            if (scroller == null) return;

            if (document.getScroll().y >= self.content.getTop()) {
                scroller.setStyles({'position':'fixed', 'top':'5px'});
            } else {
                scroller.setStyles({'position':'absolute', 'top':'0'});
            }
        },

        getMaxId:function (date_str) {
            var self = timeline;
            var date = date_str.split('|');

            if (!(date[0] in self.years)) {
                return null;
            }

            var max_id = null;
            if (date.length == 1) {
                for (i in self.years[date[0]]) {
                    max_id = self.years[date[0]][i]['max_id'];
                    break;
                }
            } else
            if (date.length == 2 && (date[1] in self.years[date[0]])) {
                max_id = self.years[date[0]][date[1]]['max_id'];
            }

            return max_id;
        },

        activate:function (el) {
            var self = timeline;
            var parent = el.getParent();

            parent.addClass('active');

            if (el.hasClass('year')) {
                parent.getElement('.months').getElements('.active').removeClass('active');
                parent.getElement('.months').getFirst().addClass('active');
            }


            var ul = parent.getElement('.months');
            if (ul != null) {
                ul.setStyle('display', 'block');
                ul.slide('in');
            }

            return false;
        },

        deactivate:function (el) {
            var parent = el.getParent();
            parent.removeClass('active');

            var ul = parent.getElement('.months');

            if (ul != null) {
                ul.setStyle('display', 'none');
                ul.slide('out');
            }
        },

        deactivateAll:function (el) {
            var self = timeline;

            if (el != null) {
                var li = el.getParent('.active');
                if (li != null) {
                    var year = li.get('rev');
                }
            }

            self.scroller.years.each(function (el) {
                if (year == undefined || year != el.getParent('li').get('rev')) {
                    self.scroller.deactivate(el);
                }
            });
            self.scroller.months.each(function (el) {
                self.scroller.deactivate(el);
            });
            self.scroller.life_events.each(function (el) {
                self.scroller.deactivate(el);
            });
        },

        scroll:function (el) {
            if ($type(el) != 'element') return;
            this.fx.start(0, (el.getOffsets().y - 10));
        }
    },


    tools:{
        topOffset:function (e) {
            var self = timeline;

            var top = 0;
            if (!e) var e = window.event;

            if (e.page.y) {
                top = e.page.y;
            }
            else if (e.client.y) {
                top = e.client.y + document.body.scrollTop + document.documentElement.scrollTop;
            }

            var feedTop = 0;
            var obj = self.composer.get().offsetParent;
            if (obj != null && obj.offsetParent != null) {
                do {
                    feedTop += obj.offsetTop;
                } while (obj = obj.offsetParent);
            }

            return parseInt(top - feedTop - 17);
        },

        offsetElements:function (top) {
            var self = timeline;

            if (top < 0) return;
            var lis = self.feed.getItems();
            var elOffset;

            for (var i = 0; i < lis.length; i++) {
                elOffset = lis[i].offsetTop;
                if (elOffset > top && i > 0) {

                    if (lis[i].hasClass('sep')) {
                        return lis[i - 1];
                    }

                    return lis[i];
                }
            }

            return lis[lis.length - 1];
        },

        positionDate:function (top) {
            var element = this.offsetElements(top);
            var date = this.dateToArray(element.retrieve('date'));
            return this.arrayToDate(date);
        },

        itemDate:function (item) {

            if (item == null || item.getElement('.timestamp') == null) return null;

            var date_str = item.getElement('.timestamp').getProperty('title');
            var date = new Date(date_str);
            var d = {
                'ye':date.getUTCFullYear(),
                'mo':this.getDecade(parseInt(date.getUTCMonth() + 1)),
                'da':this.getDecade(date.getUTCDate()),
                'ho':this.getDecade(date.getUTCHours()),
                'mi':this.getDecade(date.getUTCMinutes()),
                'se':this.getDecade(date.getUTCSeconds())
            }

            var new_date = d.ye + '-' + d.mo + '-' + d.da + ' ' + d.ho + ':' + d.mi + ':' + d.se;

            return new_date;
        },

        getDecade:function (d) {
            return (d < 10) ? '0' + d : d;
        },

        convertToDate:function (d) {
            if ($type(d) == 'string') {
                d = this.dateToArray(d);
            }

            if (d.length < 3 || !('year' in d) || !('month' in d) || !('day' in d)) {
                return false;
            }

            var date = new Date(d.year + '-' + d.month + '-' + d.day);
            date.setHours(d.hour);
            date.setMinutes(d.minute);
            date.setSeconds(d.second);

            return date;
        },

        dateToArray:function (date, allowNull) {
            if (date == null) return null;

            if ($type(date) == 'object' && 'year' in date) {
                return date
            }

            if ($type(date) != 'string') {
                date = date.toString()
            }

            var arr = date.split(' ');
            var d = arr[0].split('-');
            var result = {};

            if (allowNull) {
                result = {'year':d[0], month:null, day:null, hour:null, minute:null, second:null}
            } else {
                result = {'year':d[0], month:12, day:31, hour:23, minute:59, second:59}
            }


            if (1 in d) {
                result.month = d[1]
            }

            if (2 in d) {
                result.day = d[2]
            }

            if (1 in arr) {
                var h = arr[1].split(':');
                result.hour = h[0];

                if (1 in h) {
                    result.minute = h[1]
                }

                if (2 in h) {
                    result.second = h[2]
                }
            }

            return result
        },

        arrayToDate:function (arr) {
            var str = '';

            if (!($type(arr) == 'object' || $type(arr) == 'array') || !('year' in arr)) {
                return str;
            }
            str = arr.year;

            if (!'month' in arr) {
                return str;
            }
            str += '-' + arr.month;

            if (!'day' in arr) {
                return str;
            }
            str += '-' + arr.day;

            if (!'hour' in arr) {
                return str;
            }
            str += ' ' + arr.hour;

            if (!'minute' in arr) {
                return str;
            }
            str += ':' + arr.minute;

            if (!'second' in arr) {
                return str;
            }
            str += ':' + arr.second;

            return str;
        }
    }
});
