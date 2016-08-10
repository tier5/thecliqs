window.addEvent('domready', function(){
	addEventsForSocialMusicPopup();
});

function addEventsForSocialMusicPopup() {
	$$('a.action-link.show-hide-btn').removeEvents('click').addEvent('click', function() {
		
    	var parent = this.getParent('.show-hide-action');
    	var popup = parent.getElement('.action-pop-up');
    	$$('.action-pop-up').each(function(el) {
    		if (el != popup) el.hide();
    	});
    	if (!popup.isDisplayed()) {
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
				    		if(p_height - y_position < (c_height + 1)) {
				    			layout_parent.addClass('popup-padding-bottom');
				    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
				    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 1 + y_position - p_height)+'px');
							}
		                }
		            }
		        });
		        request.send();
	       	}
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
    
    $$('a.action-link.cancel').removeEvents('click').addEvent('click', function() {
    	var parent = this.getParent('.action-pop-up');
    	if (parent) {
    		parent.hide();
    		var layout_parent = parent.getParent('.layout_middle');
    		if (!layout_parent) layout_parent = popup.getParent('#global_content');
    		if (layout_parent.hasClass('popup-padding-bottom')) {
    			layout_parent.setStyle('padding-bottom', '0');
    		}
    	}
    });
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}

function ynmusicLike(obj, itemType, itemId) {
	obj.getElement('i.fa').removeClass('fa-thumbs-up').addClass('fa-spinner').addClass('fa-pulse');
	obj.set('onclick', '');
	
	new Request.JSON({
        url: en4.core.baseUrl + 'core/comment/like',
        method: 'post',
        data : {
        	format: 'json',
        	type : itemType,
            id : itemId,
            comment_id : 0
        },
        onSuccess: function(responseJSON, responseText) {
        	if (responseJSON.status == true) {
        		obj.getElement('i.fa').addClass('fa-thumbs-up').removeClass('fa-spinner').removeClass('fa-pulse');
            	obj.set('onclick', 'ynmusicUnlike(this,"'+itemType+'","'+itemId+'")');
            	obj.addClass('liked');
            	var label = obj.getElement('.label');
            	if (label) label.set('text', en4.core.language.translate('Unlike'));
            	else obj.set('title', en4.core.language.translate('Unlike'));
            	var li = obj.getParent('.music-item');
            	if (li) {
            		var spans = li.getElements('.like-count span');
            		spans.each(function(el) {
            			var count = parseInt(el.get('text'));
            			count = count + 1;
            			el.set('text', count);
            		});
            	}
            	
            	var div = $('music-detail-'+itemType+'_'+itemId);
            	if (div) {
            		var spans = div.getElements('.like_count span');
            		spans.each(function(el) {
            			var count = parseInt(el.get('text'));
            			count = count + 1;
            			el.set('text', count);
            		});
            	}
        	}            
        }
    }).send();
}

function ynmusicUnlike(obj, itemType, itemId) {
	obj.getElement('i.fa').removeClass('fa-thumbs-up').addClass('fa-spinner').addClass('fa-pulse');
	obj.set('onclick', '');
	
	new Request.JSON({
        url: en4.core.baseUrl + 'core/comment/unlike',
        method: 'post',
        data : {
        	format: 'json',
        	type : itemType,
            id : itemId,
            comment_id : 0
        },
        onSuccess: function(responseJSON, responseText) {
        	if (responseJSON.status == true) {
        		obj.getElement('i.fa').addClass('fa-thumbs-up').removeClass('fa-spinner').removeClass('fa-pulse');
        		obj.set('onclick', 'ynmusicLike(this,"'+itemType+'","'+itemId+'")');
            	obj.removeClass('liked');
            	var label = obj.getElement('.label');
            	if (label) label.set('text', en4.core.language.translate('Like'));
            	else obj.set('title', en4.core.language.translate('Like'));
            	var li = obj.getParent('.music-item');
            	if (li) {
            		var spans = li.getElements('.like-count span');
            		spans.each(function(el) {
            			var count = parseInt(el.get('text'));
            			count = count - 1;
            			el.set('text', count);
            		});
            	}
            	
            	var div = $('music-detail-'+itemType+'_'+itemId);
            	if (div) {
            		var spans = div.getElements('.like_count span');
            		spans.each(function(el) {
            			var count = parseInt(el.get('text'));
            			count = count - 1;
            			el.set('text', count);
            		});
            	}
        	}   
        }
    }).send();
}

