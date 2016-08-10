window.addEvent('domready', function(){
	addEventsForYnultimatevideoPopup();
});

function addEventsForYnultimatevideoPopup() {
	$$('a.ynultimatevideo-action-link.show-hide-btn').removeEvents('click').addEvent('click', function() {

    	var parent = this.getParent('.action-container');
    	var popup = parent.getElement('.ynultimatevideo-action-pop-up');

		$$('.action-container').each(function(el) {
			el.removeClass("ynultimatevideo-action-shown");
		});

		var pageParent = this.getParent('.layout_middle');
		var otherPopup = pageParent.getElement('.ynultimatevideo_button_more_explain');
		if (otherPopup != null) {
			otherPopup.hide();
		}

    	$$('.ynultimatevideo-action-pop-up').each(function(el) {
    		if (el != popup) el.hide();
    	});

    	if (!popup.isDisplayed()) {
			parent.addClass("ynultimatevideo-action-shown");
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
    	} else {
			parent.removeClass("ynultimatevideo-action-shown");
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

    $$('a.ynultimatevideo-action-link.cancel').removeEvents('click').addEvent('click', function() {
    	var parent = this.getParent('.ynultimatevideo-action-pop-up');
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

jQuery.noConflict();

function ynultimatevideoRenderViewMode(id, default_mode) {
	var view_mode = getCookie('browse_view_mode-' + id);
	if (view_mode == '') view_mode = default_mode;
	ynultimatevideoUpdateViewMode(id, view_mode);
}

function ynultimatevideoUpdateViewMode(id, view_mode)
{
	document.getElementById('ynultimatevideo_list_item_browse_' + id).set('class','ynultimatevideo_'+ view_mode +'-view');
	setCookie('browse_view_mode-' + id, view_mode);
}

function setCookie(cname,cvalue,exdays)
{
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++)
	{
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}
