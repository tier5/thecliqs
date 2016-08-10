function ynvideochannelAddNewPlaylist(ele, guid, url) {
    var nextEle = ele.getNext();
    if(nextEle.hasClass("ynvideochannel_active_add_playlist")) {
        //click to close
        nextEle.removeClass("ynvideochannel_active_add_playlist");
        nextEle.setStyle("display", "none");
    } else {
        //click to open
        nextEle.addClass("ynvideochannel_active_add_playlist");
        nextEle.setStyle("display", "block");
    }
    $$('.play_list_span').each(function(el){
        if(el === nextEle){
            //do not empty the current box
        } else {
            el.empty();
            el.setStyle("display", "none");
            el.removeClass("ynvideochannel_active_add_playlist");
        }
    });
    var data = guid;
    var request = new Request.HTML({
        url : url,
        data : {
            subject: data,
        },
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var spanEle = nextEle;
            spanEle.innerHTML = responseHTML;
            eval(responseJavaScript);

            var popup = spanEle.getParent('.ynvideochannel-action-pop-up');
            var layout_parent = popup.getParent('.layout_middle');
            if (!layout_parent) layout_parent = popup.getParent('#global_content');
            var y_position = popup.getPosition(layout_parent).y;
            var p_height = layout_parent.getHeight();
            var c_height = popup.getHeight();
            if(p_height - y_position < (c_height + 65)) {
                layout_parent.addClass('popup-padding-bottom');
                var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 65 + y_position - p_height)+'px');
            }
        }
    });
    request.send();
}

function ynvideochannelAddToPlaylist(ele, playlistId, guild, url) {
    var checked = ele.get('checked');
    var data = guild;
    var request = new Request.JSON({
        url : url,
        data : {
            subject: data,
            playlist_id: playlistId,
            checked: checked,
        },
        onSuccess: function(responseJSON) {
            if (!responseJSON.status) {
                ele.set('checked', !checked);
            }
            var div = ele.getParent('.ynvideochannel-action-pop-up');
            var notices = div.getElement('.add-to-playlist-notices');
            var notice = new Element('div', {
                'class' : 'add-to-playlist-notice',
                text : responseJSON.message
            });
            notices.adopt(notice);
            notice.fade('in');
            (function() {
                notice.fade('out').get('tween').chain(function() {
                    notice.destroy();
                });
            }).delay(2000, notice);
        }
    });
    request.send();
}

function ynvideochannelAddToFavorite(ele, video_id, url, favtext, unfavtext) {
    var popup = ele.getParent('.ynvideochannel-action-pop-up');
    var favorite = popup.getElement('.favorite-loading');
    if (favorite) {
        favorite.show();
    }
    var favorite_link = popup.getElement('.favorite_link');
    if (favorite_link) {
        favorite_link.hide();
    }

    var request = new Request.JSON({
        url : url,
        data : {
            id: video_id
        },
        onSuccess: function(responseJSON) {
            if (favorite) {
                favorite.hide();
            }
            if (favorite_link) {
                favorite_link.show();
            }
            if (responseJSON.result) {
                if (responseJSON.added == 1) {
                    var html = '<i class="fa fa-star"></i>' + ' ' + unfavtext;
                    ele.innerHTML = html;
                } else {
                    var html = '<i class="fa fa-star-o"></i>' + ' ' + favtext;
                    ele.innerHTML = html;
                }
            }
            var notices = popup.getElement('.add-to-playlist-notices');
            var notice = new Element('div', {
                'class' : 'add-to-playlist-notice',
                text : responseJSON.message
            });
            notices.adopt(notice);
            notice.fade('in');
            (function() {
                notice.fade('out').get('tween').chain(function() {
                    notice.destroy();
                });
            }).delay(2000, notice);
        }
    });
    request.send();
}

window.addEvent('domready', function(){
    ynvideochannelAddToOptions();
});

//Function outerclick
    (function($,$$){
        var events;
        var check = function(e){
        var target = $(e.target);
        var parents = target.getParents();
        events.each(function(item){
            var element = item.element;
            if (element != target && !parents.contains(element))
                item.fn.call(element, e);
            });
        };

        Element.Events.outerClick = {
            onAdd: function(fn){
              if(!events) {
                document.addEvent('click', check);
                events = [];
              }
              events.push({element: this, fn: fn});
            },
            onRemove: function(fn){
              events = events.filter(function(item){
                return item.element != this || item.fn != fn;
              }, this);
              if (!events.length) {
                document.removeEvent('click', check);
                events = null;
              }
            }
        };
    })(document.id,$$);

// Add to box
function ynvideochannelAddToOptions(){
    $$('a.ynvideochannel-action-link.show-hide-btn').removeEvents('click').addEvent('click', function() {
        var parent = this.getParent('.action-container');
        var popup = parent.getElement('.ynvideochannel-action-pop-up');
        var parent_active = $$('.ynvideochannel_video-channel-playlist_options').length;

        $$('.action-container').each(function(el) {
            el.removeClass("ynvideochannel-action-shown");
        });

        if(parent_active){
            this.getParents('.ynvideochannel_video-channel-playlist_options').toggleClass('ynvideochannel-addtoplaylist-active');
        }

        var pageParent = popup.getParent('#global_content');
        var otherPopup = pageParent.getElement('.ynvideochannel_button_more_explain');
        if (otherPopup != null) {
            otherPopup.hide();
        }

        $$('.ynvideochannel-action-pop-up').each(function(el) {
            if (el != popup) el.hide();
        });

        if (!popup.isDisplayed()) {
            parent.addClass("ynvideochannel-action-shown");
            var loading = popup.getElement('.add-to-playlist-loading');
            if (loading) {
                var url = loading.get('rel');
                loading.show();
                var checkbox = popup.getElement('.box-checkbox');
                checkbox.hide();
                var request = new Request.HTML({
                    url : url,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        elements = Elements.from(responseHTML);
                        if (elements.length > 0) {
                            checkbox.empty();
                            checkbox.adopt(elements);
                            eval(responseJavaScript);
                            loading.hide();
                            checkbox.show();
                            var layout_parent = popup.getParent('.layout_middle');
                            if (!layout_parent) layout_parent = popup.getParent('#global_content');
                            var y_position = popup.getPosition(layout_parent).y;
                            var p_height = layout_parent.getHeight();
                            var c_height = popup.getHeight();
                            if(p_height - y_position < (c_height + 65)) {
                                layout_parent.addClass('popup-padding-bottom');
                                var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                                layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 65 + y_position - p_height)+'px');
                            }
                        }
                    }
                });
                request.send();
            }
            var favorite = popup.getElement('.favorite-loading');
            if (favorite) {
                var url = favorite.get('rel');
                favorite.show();
                var favorite_link = popup.getElement('.favorite_link');
                favorite_link.hide();
                var request = new Request.HTML({
                    url : url,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        elements = Elements.from(responseHTML);
                        if (elements.length > 0) {
                            favorite_link.empty();
                            favorite_link.adopt(elements);
                            eval(responseJavaScript);
                            favorite.hide();
                            favorite_link.show();
                        }
                    }
                });
                request.send();
            }
        } else {
            parent.removeClass("ynvideochannel-action-shown");
        }
        popup.toggle();
        var layout_parent = popup.getParent('.layout_middle');
        if (!layout_parent) layout_parent = popup.getParent('#global_content');
        if (layout_parent.hasClass('popup-padding-bottom')) {
            layout_parent.setStyle('padding-bottom', '0');
        }
        var y_position = popup.getPosition(layout_parent).y;
        var p_height = layout_parent.getHeight();
        var c_height = popup.getHeight();
        if (popup.isDisplayed()) {
            if(p_height - y_position < (c_height + 1)) {
                layout_parent.addClass('popup-padding-bottom');
                layout_parent.setStyle('padding-bottom', (c_height + 1 + y_position - p_height)+'px');
            }
            else if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
        }
        else {
            if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
        }
    });

    $$('.ynvideochannel-action-add-playlist').addEvent('outerClick',function(){
        var popup = this.getElement('.ynvideochannel-action-pop-up');
        var display_popup = popup.getStyle('display');
        var parent_popup_active = this.getParents('.ynvideochannel_video-channel-playlist_options');
        if(display_popup = 'block'){
            popup.setStyle('display','none');
        };

        if(this.hasClass('ynvideochannel-action-shown')) {
            this.removeClass('ynvideochannel-action-shown');
        }

        if(parent_popup_active.hasClass('ynvideochannel-addtoplaylist-active')) {
            parent_popup_active.removeClass('ynvideochannel-addtoplaylist-active');
        }
    });

    $$('a.ynvideochannel-action-link.cancel').removeEvents('click').addEvent('click', function() {
        var parent = this.getParent('.ynvideochannel-action-pop-up');
        if (parent) {
            parent.hide();
            var layout_parent = popup.getParent('.layout_middle');
            if (!layout_parent) layout_parent = popup.getParent('#global_content');
            if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
        }
    });
}

//Video Options
function ynvideochannelVideoOptions(){
    var parent_active = $$('.ynvideochannel_video-channel-playlist_options').length;
    $$('.ynvideochannel_video_options-btn').removeEvents('click').addEvent('click', function() {
        this.getParent('.ynvideochannel_video_options').toggleClass('explained');
        if(parent_active){
            this.getParents('.ynvideochannel_video-channel-playlist_options').toggleClass('ynvideochannel-options-active');
        }
    });

    $$('.ynvideochannel_video_options-btn').addEvent('outerClick',function(){
        var popup = this.getParent('.ynvideochannel_video_options');
        var parent_popup = this.getParents('.ynvideochannel_video-channel-playlist_options'); //Add class for hover out scope not hidden.
        if (popup.hasClass('explained')){
            popup.removeClass('explained');
        }
        if (parent_popup.hasClass('ynvideochannel-options-active')){
            parent_popup.removeClass('ynvideochannel-options-active');
        }
    });
}


//Channel Options
function ynvideochannelChannelOptions(){
    $$('.ynvideochannel_channel_options-btn').removeEvents('click').addEvent('click', function() {
        var popup = this.getParent('.ynvideochannel_channel_options');
        popup.toggleClass('explained');

        var layout_parent = popup.getParent('.layout_middle');
        if (!layout_parent) layout_parent = popup.getParent('#global_content');
        var y_position = popup.getPosition(layout_parent).y;
        var p_height = layout_parent.getHeight();
        var c_height = popup.getElement('.ynvideochannel_channel_options-block').getHeight();
        if(p_height - y_position < (c_height + 60)) {
            layout_parent.addClass('popup-padding-bottom');
            var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
            layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 80 + y_position - p_height)+'px');
        }
    });

    $$('.ynvideochannel_channel_options').addEvent('outerClick',function(){
        var popup = this;
        if (popup.hasClass('explained')){
            popup.removeClass('explained');

            $$('.owl-next').setStyle('display','block');
        }
    })
}


//Playlist options
function ynvideochannelPlaylistOptions(){
    var parent_active = $$('.ynvideochannel_video-channel-playlist_options').length;
    $$('.ynvideochannel_video_options-btn').removeEvents('click').addEvent('click', function() {
        this.getParent('.ynvideochannel_video_options').toggleClass('explained');
        if(parent_active){
            this.getParents('.ynvideochannel_video-channel-playlist_options').toggleClass('ynvideochannel-options-active');
        }
    });

    $$('.ynvideochannel_video_options-btn').addEvent('outerClick',function(){
        var popup = this.getParent('.ynvideochannel_video_options');
        var parent_popup = this.getParents('.ynvideochannel_video-channel-playlist_options'); //Add class for hover out scope not hidden.
        if (popup.hasClass('explained')){
            popup.removeClass('explained');
        }
        if (parent_popup.hasClass('ynvideochannel-options-active')){
            parent_popup.removeClass('ynvideochannel-options-active');
        }
    })
}


