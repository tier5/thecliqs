var YnMediaImporter = {
    request : null,
    emptyAjaxQueue : function() {
        if (YnMediaImporter.request) {
            YnMediaImporter.request.cancel();
        }
    },
    getHash : function() {
        var match = document.location.href.match(/#!.+$/);
        if (match) {
            return match.pop().replace('#!', '').parseQueryString();
        }
        return false;
    },
    toHash : function(params) {
        document.location.href = document.location.href.replace(/#!.+$/, '') + '#!' + (new Hash(params)).toQueryString();
    },
    openSmoothBox : function(href) {
        // create an element then bind to object
        var a = new Element('a', {
            href : href,
            'style' : 'display:none'
        });
        var body = document.getElementsByTagName('body')[0];
        a.inject(body);
        Smoothbox.open(a);
    },
    selectAll : function() {
        $$('.ynmediaimporter_checkbox').each(function(a, b) {
            a.checked = 1;
        });
    },
    unselectAll : function() {
        $$('.ynmediaimporter_checkbox').each(function(a, b) {
            a.checked = 0;
        });
    },
    getSelected : function() {
        var rows = [];
        $$('.ynmediaimporter_checkbox').each(function(a, b) {
            if (a.checked) {
                rows.push({
                    id : a.value,
                    data : a.get('data-cache'),
                    media : a.get('media'),
                    provider : a.get('provider')
                });
            }
        });
        return rows;
    },
    importMedia : function(rows) {
        // get total selected them dumped all
        if ( typeof rows == 'undefined') {
            rows = this.getSelected();
        }
        if (!rows.length) {
            alert(en4.core.language.translate("There is no selected!"));
            return 0;
        }
        var url = en4.core.baseUrl + 'ynmediaimporter/import/check/';

        var hash = new Hash({
            'format' : 'smoothbox',
        });
        // open waiting box.
        this.openSmoothBox(url + '?' + hash.toQueryString());
    },
    updatePage : function(url) {
        url = en4.core.baseUrl + '?m=lite&module=ynmediaimporter&name=getdata&' + url;
        this.updateBrowse({}, url);
    },
    refresh : function(cache) {
        var data = this.lastData.json;
        if ( typeof cache != 'undefined' && cache) {
            data['remove-cache'] = 1;
        }
        this.updateBrowse(data, this.lastData.url);
    },
    viewMore : function(json, url) {
        json.noControl = 1;
        function loading() {
            var wrapper = $('feed_viewmore');
            if ( typeof wrapper != 'undefined') {
                var html = '<div class="ynmediaimporter_viewmore_loading">{loading}</div>';
                html = html.replace('{loading}', en4.core.language.translate('Loading ...'));
                wrapper.innerHTML = html;
            }
        }

        loading();
        if ( typeof url == 'undefined') {
            url = en4.core.baseUrl + '?m=lite&module=ynmediaimporter&name=getdata';
        }
        if ( typeof json.offset != 'undefined') {
            json.offset = parseInt(json.offset) + parseInt(json.limit);
        }
        var request = new Request({
            url : url,
            method : 'get',
            data : json,
            onSuccess : function(text) {
                try {
                    if (text != '') {
                        var json = JSON.decode(text);
                        if (json.message != '') 
                        {
                            // in most case of unix time we must to reload page for some thing
                            window.location.href = en4.core.baseUrl+'media-importer/connect/service/facebook';
                        } else {
                            var wrapper = $$('.ynmeidaimporter_result_holder').pop();
                            if ( typeof wrapper != 'undefined') {
                                wrapper.innerHTML = json.html;
                            }
                        }
                    }
                } catch(e) {
                    alert('There are an error occur, please refresh(F5) this page!');
                }
            }
        });
        this.emptyAjaxQueue();
        this.request = request;
        request.send();
    },
    lastData : {
        url : null,
        json : null
    },
    updateBrowse : function(json, url) {
        function loading() {
            var wrapper = $$('.layout_ynmediaimporter_media_browse')[0];
            if ( typeof wrapper != 'undefined') {
                var html = '<div class="ynmediaimporter_loading_image" style="display:block;">&nbsp;</div><div><center>{loading}</center></div>';
                html = html.replace('{loading}', en4.core.language.translate('Loading ...'));
                wrapper.innerHTML = html;
            }
        }

        loading();
        if ( typeof url == 'undefined') {
            url = en4.core.baseUrl + '?m=lite&module=ynmediaimporter&name=getdata';
        }

        this.lastData = {
            url : url,
            json : json
        };
        var request = new Request({
            url : url,
            method : 'get',
            data : json,
            onSuccess : function(text) {
                try {
                    if (text != '') {
                        var json = JSON.decode(text);
                        if (json.message != '') {
                            window.location.href = en4.core.baseUrl+'media-importer/connect/service/facebook';

                        } else {
                            var wrapper = $$('.layout_ynmediaimporter_media_browse')[0];
                            if ( typeof wrapper != 'undefined') {
                                wrapper.innerHTML = json.html;
                            }
                        }
                    }
                } catch(e) {
                    alert('There are an error occur, please refresh(F5) this page!');
                }
            }
        });
        this.emptyAjaxQueue();
        this.request = request;
        request.send();
    }
};