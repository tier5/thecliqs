/**
 * Created with JetBrains PhpStorm.
 * User: adik
 * Date: 30.07.12
 * Time: 15:21
 * To change this template use File | Settings | File Templates.
 */

var donation_manager = {
    page_num:1,
    widget_url:'',
    content_id:'',
    view:'',
    widget_element:'',
    sort:'recent',

    init:function () {
        var self = this;
        if ($('filter_form') != undefined) {
            if ($('search').value || self.isNumber($('max_price').value) || self.isNumber($('min_price').value) || $('profile_type').value) {
                self.getDonations();
            }
            $('submit').addEvent('click', function (e) {
                e.stop();
                self.page_num = 1;
                self.getDonations();
            });
        }
    },

    getDonations:function () {
        var self = this;

        if ($('donation_loader_browse')) {
            $('donation_loader_browse').removeClass('hidden');
        }

        var query = '';
        if ($('filter_form') != undefined) {
            query += $('filter_form').toQueryString();
        }
        new Request.HTML({
            url:self.widget_url + '/?' + query,
            method:'post',
            data:{
                'page':self.page_num,
                'content_id':self.content_id,
                'sort':self.sort,
                'v':self.view,
                'format':'html'
            },
            eval:true,
            onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if ($('donation_loader_browse')) {
                    $('donation_loader_browse').addClass('hidden');
                }
                var el = $$('.layout_middle > ' + self.widget_element);
                var tElement = new Element('div', {'html':responseHTML});

                if (el[0] != undefined) {
                    el[0].innerHTML = tElement.getElement(self.widget_element).innerHTML;
                }

                Smoothbox.bind(el[0]);

                var is_form = false;

                if (
                    $type($('search')) == 'element' && $('search').value ||
                        $type($('max_price')) == 'element' && self.isNumber($('max_price').value) ||
                        $type($('min_price')) == 'element' && self.isNumber($('min_price').value) ||
                        $type($('profile_type')) == 'element' && ($('profile_type').value)) {
                    is_form = true;
                }


                if ($type($('donation_form_info')) == 'element') {
                    if (is_form) {
                        $('donation_form_info').innerHTML = '<a href="javascript:void(0)" onClick="donation_manager.reset_form();">' + en4.core.language.translate('donation_Reset donation search') + '</a>';
                        $('donation_form_info').removeClass('hidden');
                    } else {
                        $('donation_form_info').innerHTML = "";
                        $('donation_form_info').addClass('hidden');
                    }

                    if ($('donation_loader_browse')) {
                        $('donation_loader_browse').addClass('hidden');
                    }
                }

                en4.core.runonce.trigger();
            }
        }).send();
    },

    setSort:function (sort) {
        this.page_num = 1;
        this.sort = sort;
        this.getDonations();
    },

    setView:function (view, el) {
        $$('.donation-view-types').removeClass('active');
        if ($type(el) == 'element') {
            el.addClass('active');
        }

        if (view == 'icons') {
            $('donations-icons').setStyle('display', 'block');
            $('donations-items').setStyle('display', 'none');
        } else if (view == 'list') {
            $('donations-items').setStyle('display', 'block');
            $('donations-icons').setStyle('display', 'none');
        }
        this.view = view;
    },

    setPage:function (page) {
        if (this.is(page)) this.page_num = page;
        this.getDonations();
    },

    reset_form:function () {
        $('search').value = '';
        $('min_amount').value = en4.core.language.translate('min');
        $('max_amount').value = en4.core.language.translate('max');
        $('profile_type').value = '';
        this.page_num = 1;
        this.getDonations();
    },

    isNumber:function (o) {
        return !isNaN(o - 0);
    },

    is:function (x) {
        return !(x == '' || x == 0 || x == undefined || x === false)
    },

    truncate: function(str, num) {
        str = str.trim();
        var len = str.length;
        if(num+3 >= len)
            return str;

        return (str.substr(0, num) + '...');
    }
};