/**
 * @package Ynfbpp
 * @category Extenstion
 * @author YouNet Company
  */

var ynfbpp = {
	pt : [],
	ele : 0,
	href : 0,
	data : {},
	timeoutId : 0,
	isShowing: 0,
	cached: {},
	dir: {cx:0,cy:0},
	isShowing: 0,
	ignoreClasses: ['uiContextualDialogContent','layout_page_header','layout_page_footer'],
	enableThumb: false,
	isMouseOver: 1,
	mouseOverTimeoutId: 0,
	box:0,
	timeoutOpen: 300,
	timeoutClose: 300,
	enabledAdmin: false,
	data: {match_type: '', match_id: 0},
	boxContent: 0,
	setTimeoutOpen: function(time){
		ynfbpp.timeoutOpen = time;
		return ynfbpp;
	},
	clearCached: function(){
		ynfbpp.cached = {};
	},
	openSmoothBox: function(href){
		// create an element then bind to object
		var a = new Element('a', {
			href : href,
			'style' : 'display:none'
		});
		var body = document.getElementsByTagName('body')[0];
		a.inject(body);
		Smoothbox.open(a);
	},
	setTimeoutClose: function(time){
		ynfbpp.timeoutClose = time;
		return ynfbpp;
	},
	setEnabledAdmin: function(flag){
		ynfbpp.enabledAdmin = flag;
		return ynfbpp;
	},
	setIgnoreClasses: function(s){
		var ar = s.replace('.',' ').replace(',',' ').split(/\s+/);
		for(var i=0; i<ar.length; ++ i){
			if(ar[i]!= null && ar[i]!= undefined && ar[i]){
				ynfbpp.ignoreClasses.push(ar[i]);
			}
		}
		return ynfbpp;
	},
	setEnableThumb: function(flag){
		ynfbpp.enableThumb =  flag;
		return ynfbpp;
	},
	removeIgnoreClass: function(k){
		for(var i=0; i<ynfbpp.ignoreClasses.length; ++i){
			if(ynfbpp.ignoreClasses[i] == k){
				ynfbpp.ignoreClasses[i] = '';
			}
		}
		return ynfbpp;
	},
	boot : function() {

		if(window.parent != window){return;}

		if(document.location.href.search(en4.core.baseUrl+'admin')>0){
			if(ynfbpp.enabledAdmin == false){
				return ;
			}else{
				Asset.css(en4.core.basePath + 'application/css.php?f=application/modules/Ynfbpp/externals/styles/main.css');
			}
		}

		$(document.body).addLiveEvent('mouseover', 'a', ynfbpp.check);

		if(ynfbpp.enableThumb){
			$(document.body).addLiveEvent('mouseover', 'img', ynfbpp.check);
			//$(document.body).addLiveEvent('mouseover', 'font', ynfbpp.check);
		}
	},
	checkIngoreClasses: function (a){
		var p = a;
		var len = ynfbpp.ignoreClasses.length;
		while(p.parentNode != null && p.parentNode != undefined && p.tagName != null && p.tagName != undefined){
			for(var i=0; i<len; ++i){
				if(ynfbpp.ignoreClasses[i] && $(p).hasClass(ynfbpp.ignoreClasses[i])){
					return false;
				}
			}
			p  = p.parentNode;
		}
		return true;
	},
	check : function(e) {
		if(e.target == null && e.target == undefined){
			return;
		}

		var a = e.target;
		var ele = e.target;

		if(a.getAttribute == null || a.getAttribute == undefined){
			return;
		}

		if(ele.tagName.toUpperCase() == 'IMG' && ($(ele).hasClass('thumb_icon') || $(ele).hasClass('thumb_normal'))){
			var found=false;
			while(a.parentNode != null && a.parentNode!= undefined && a.parentNode.tagName != null && a.parentNode.tagName != undefined &&  a.tagName.toUpperCase() != 'A'){
				a = a.parentNode;
				found=  true;
				break;
			};
			if(!found){return;}
		}

		if($(a).hasClass('buttonlink') || $(a).hasClass('menu_core_mini')){
			return ;
		}

		var href = a.getAttribute('href');
		if(href == null && href == undefined){
			return;
		}

		var p = a;

		if(ynfbpp.checkIngoreClasses(p) == false){
			return ;
		}

		for(var i =0; i<ynfbpp.pt.length; ++i) {
			var data = ynfbpp.pt[i](href);

			if(data != null && data != undefined && data != false) {
				ynfbpp.ele = $(ele);
				ynfbpp.href = href;
				ynfbpp.data = data;
				if(ynfbpp.timeoutId) {
					try {
						window.clearTimeout(ynfbpp.timeoutId);
					} catch(e) {

					}
				}
				$(a).addEvent('mouseleave',function(){ynfbpp.resetTimeout(0);});
				ynfbpp.timeoutId = 0;
				ynfbpp.isRunning = 0;
				ynfbpp.dir.cx = e.event.clientX;
				ynfbpp.dir.cy = e.event.clientY;
				ynfbpp.timeoutId = window.setTimeout('ynfbpp.requestPopup()', ynfbpp.timeoutOpen);
				return ;
			}
		}

	},
	updateBoxContent: function(html){
	  ynfbpp.boxContent.innerHTML = html;
	  return ynfbpp;
	},
	startSending: function(html){
	  ynfbpp.boxContent.innerHTML = '<div class="uiContextualDialogContent"> \
                                <div class="uiYnfbppHovercardStage"> \
                                    <div class="uiYnfbppHovercardContent"> \
                                    ' +html+ ' \
                                    </div> \
                                </div> \
                            </div> \
                            ';
		return ynfbpp;

	},
	requestPopup : function() {
		ynfbpp.timeoutId = 0;
		var box = ynfbpp.getBox();
		box.style.display = 'none';

		if(!ynfbpp.data.match_type || !ynfbpp.data.match_id){
			return ;
		}

		var key = ynfbpp.data.match_type + '-' + ynfbpp.data.match_id;
		if(ynfbpp.cached[key] != undefined){
			ynfbpp.showPopup(ynfbpp.cached[key]);
			return;
		}
		var jsonRequest = new Request.JSON({
			url : en4.core.baseUrl + '?m=lite&module=ynfbpp&name=popup',
			onSuccess : function(json, text) {
				ynfbpp.cached[key] = json;
				ynfbpp.showPopup(json);
			}
		}).get({match_type:ynfbpp.data.match_type,match_id: ynfbpp.data.match_id});
		ynfbpp.startSending(en4.core.language.translate('Loading...'));
		ynfbpp.resetPosition(1);
		return ynfbpp;

	},
	resetTimeout: function($flag){
		ynfbpp.isMouseOver = $flag;
		if(ynfbpp.mouseOverTimeoutId){
			try{
				window.clearTimeout(ynfbpp.mouseOverTimeoutId);
				ynfbpp.mouseOverTimeoutId = 0;
				if(ynfbpp.timeoutId){
				    try{
				        window.clearTimeout(ynfbpp.timeoutId);
				        ynfbpp.timeoutId = 0;
				    }catch(e){
				    }
				}
			}catch(e){
			}
		}
		if($flag ==0){
			ynfbpp.data.match_id = 0;
			ynfbpp.mouseOverTimeoutId = window.setTimeout('ynfbpp.closePopup()',ynfbpp.timeoutClose);
		}
		return ynfbpp;

	},
	closePopup: function(){
		box = ynfbpp.getBox();
		box.style.display = 'none';
		ynfbpp.isShowing = 0;
		return ynfbpp;
	},
	resetPosition: function(flag){
		ynfbpp.isShowing = 1;
		var box = ynfbpp.getBox();
		var ele =  ynfbpp.ele;

		if(!ele){
			return ;
		}
		var pos = ele.getPosition();
		var size = ele.getSize();

		if(pos == null || pos == undefined){
			return ;
		}

		if(ynfbpp.dir.cy >180){
			box.style.top =  pos.y  +'px';
			box.removeClass('uiYnfbppDialogDirDown').addClass('uiYnfbppDialogDirUp');
		}else{
			box.style.top =  pos.y + size.y +'px';
			box.removeClass('uiYnfbppDialogDirUp').addClass('uiYnfbppDialogDirDown');
		}


		if(en4.orientation=='ltr'){
			// check the position of the content

			if(window.getSize().x - ynfbpp.dir.cx > 350){
				box.removeClass('uiYnfbppDialogDirLeft').addClass('uiYnfbppDialogDirRight');
				var px = size.x > 200? ynfbpp.dir.cx:pos.x;
				box.style.left =  px + 'px';
			}else{
				box.removeClass('uiYnfbppDialogDirRight').addClass('uiYnfbppDialogDirLeft');
				var px = size.x > 200? ynfbpp.dir.cx:(pos.x+size.x);
				box.style.left =  px + 'px';
			}
		}else{
			// right to left
			if(ynfbpp.dir.cx< 310){
				box.removeClass('uiYnfbppDialogDirLeft').addClass('uiYnfbppDialogDirRight');
				var px = size.x > 200? ynfbpp.dir.cx:pos.x;
				box.style.left =  px + 'px';
			}else{
				var px = size.x > 200? ynfbpp.dir.cx:(pos.x+size.x);
				box.style.left =  px + 'px';
				box.removeClass('uiYnfbppDialogDirRight').addClass('uiYnfbppDialogDirLeft');
			}

		}
		if(flag){
			box.style.display = 'block';
		}


	},
	showPopup : function(json) {
		if(json == null || json == undefined){
			return ;
		}
		if(json.match_type != ynfbpp.data.match_type || json.match_id != ynfbpp.data.match_id){
			ynfbpp.closePopup();
			return ;
		}
		ynfbpp.resetPosition(1);
		var box = ynfbpp.getBox();
		ynfbpp.updateBoxContent(json.html);
		box.style.display='block';
		return ynfbpp;
	},
	getBox: function(){
		if(ynfbpp.box){
			return ynfbpp.box;
		}
		var ct = document.createElement('DIV');
		ct.setAttribute('id','uiYnfbppDialog');
		var html = '<div class="uiYnfbppDialogOverlay" id="ynfbppUiOverlay" onmouseover="ynfbpp.resetTimeout(1)" onmouseout="ynfbpp.resetTimeout(0)">\
						<div class="uiYnfbppOverlayContent" id="ynfbppUiOverlayContent">\
						</div> \
						<i class="uiYnfbppContextualDialogArrow"></i> \
					</div> \
		';
		ct.innerHTML = html;
		var body = document.getElementsByTagName('body')[0];
		body.appendChild(ct);
		$(ct).addClass('uiYnfbppDialog');
		ynfbpp.box = $('uiYnfbppDialog');
        ynfbpp.boxContent = $('ynfbppUiOverlayContent');
		return ynfbpp.box;
	}
};

ynfbpp.pt = [
function(href) {
	reg = new RegExp("profile/([^\/]+)$", "i");
	match = href.match(reg);
	if(match != null && match != undefined) {
		return {
			match_id : decodeURIComponent(match[1]),
			match_type : 'user'
		}
	}
	return false;
},function(href) {
	var match = href.match(/\/group\/(\d+)(\/)?/i);
	if(match != null && match != undefined) {
		return {
			match_id : decodeURIComponent(match[1]),
			match_type : 'group'
		}
	}
	return false;
},function(href) {
	var match = href.match(/\/event\/(\d+)(\/)?/i);
	if(match != null && match != undefined) {
		return {
			match_id : decodeURIComponent(match[1]),
			match_type : 'event'
		}
	}
	return false;
}];

/**
 * extends Mootools component
 */
Element.implement({
	addLiveEvent : function(event, selector, fn) {
		this.addEvent(event, function(e) {
			var t = $(e.target);
			if(!t.match(selector))
				return ;
			fn.apply(t, [e]);
		}.bindWithEvent(this, selector, fn));
	}
});

window.addEvent('domready', ynfbpp.boot);
