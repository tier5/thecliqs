Composer.prototype.cleanup = function(html) 
{
	// parse from data to some exclude function from there.
	// recacaluate html , skip current style to another.
	html = $('ynfeed_activity_body_hightlighter').innerHTML;
	var res = html.match(/#tags@\w+@\d+@/gim);
	function re(uid) 
	{
		var len = ynfriends.rows.length;
		for(var i = 0; i < len; ++i) {
			var o = ynfriends.rows[i];
			if(o && o.id && o.id == uid) {
				return o;
			}
		}
		return false;
	}

	var tagged_id = new Array();
	if(res && res.length)
	{
		for(var i = 0; i < res.length; ++i) 
		{
			var e = res[i];
			var ta = e.match(/#tags@(\w+)@(\d+)@/);
			if(ta && ta[2]) 
			{
				if (ta[1] == 'taggroup') 
				{

				}
				else if (ta[1] == 'taguser') 
				{
					/*
					 * replace #tags@taguser@2@ with URL
					 * html = html.replace(e, href = o.l);
					 */
				}
				else if (ta[1] == 'user') 
				{
					var o = re(ta[2]);
					if(o && o.l) 
					{
						tagged_id.push(o.st + ';' + o.id);
						html = html.replace(e, href = o.l);
					}
				}
			}
		}
	}

	html = html.replace(/ynfeedid_tags_\w+_\d+/gim, '@');
	$('ynfeed_activity_body_body_html').value = html;
	$('ynfeed_activity_body_tagged_users').value = tagged_id.join(',');
	return html;

}
Composer.prototype.setContent = function(newContent) {
	if(!newContent.trim() && !Browser.Engine.trident)
		newContent = '<br />';

	return this;
}
Composer.prototype.getContent = function() 
{
	function replaceURLWithHTMLLinks(text) 
	{
		//var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i;
		return text;//.replace(exp,"<a href='$1'>$1</a>");
	}
	// tranform url clickable link to a href.
	return replaceURLWithHTMLLinks(this.cleanup($('ynfeed_activity_body_hightlighter').innerHTML));
};
Composer.prototype.saveContent = function() {
	$('ynfeed_activity_body_body_html').value = this.getContent();
}
var _ynfeedsubmiting = false;

function checkStatusBody(ele, pluginReady) 
{
	if(_ynfeedsubmiting) 
	{
		return false;
	}
	ele = $(ele);
	var v = ele.value.trim();
	if(pluginReady) 
	{
		if(v == ele.getAttribute('placeholder').trim()) 
		{
			$('ynfeed_activity_body').value = '';
		}
		return true;
	}

	if(v == '') 
	{
		return false;
	}
	
	if(v == ele.getAttribute('placeholder').trim()) {
		return false;
	}

	$('ynfeed-compose-submit').addClass('button_disabled');
	_ynfeedsubmiting = true;

	var els = $('ynfeed-activity-form').elements;
	if(els[els.length - 1].value) {
		return true;
	}
	return true;
}

var ynac = {
	isHide : true,
	createUl : function(d) {
		var ul = document.createElement('ul');
		d.parentNode.appendChild(ul);
		ul.id = 'ynfeedac';
		ul.style.display = 'none';
		ul.className = 'ynacul';
		return ul;
	},
	init : function(e, f) {
		this.format = f;
		this.ul = this.createUl(e);
		this.isHide = true;
		return ynac;
	},
	clearActive : function() {
		var e = this.cur();
		if(e) {
			e.removeClass('active')
		}
	},
	create : function(o, cb) {
		var e = document.createElement('li');
		e.innerHTML = ['<span class="ynfeed_thumbnail"><img src="', o.photo, '" style="background-image: url(', o.photo, ')" /></span><span>', o.name, '</span>'].join('');
		var t = this;
		$(e).addEvent('mousedown', function(evt) {
			if(evt) {
				evt.stop()
			}
			cb(o);
		}).addEvent('mouseover', function() {
			t.clearActive();
			$(this).addClass('active')
		}).addEvent('mouseout', function() {
			$(this).removeClass('active')
		});
		return e;
	},
	setRange : function(os, cb) {
		this.ul.innerHTML = "";
		if(os.length) {
			this.show()
		} else {
			this.hide()
		}
		for(var i = 0; i < os.length; ++i) {
			var o = os[i];
			e = this.create(o, cb);
			if(i == 0) {
				e.className = 'active'
			}
			this.ul.appendChild(e);
		}
	},
	hide : function() {
		if($('ynfeed_privacy')) {
			$('ynfeed_privacy').show();
		}
		this.isHide = true;
		this.ul.style.display = 'none';
	},
	show : function() {
		if($('ynfeed_privacy')) 
		{
			$('ynfeed_privacy').hide();
		}
		this.isHide = false;
		this.ul.style.display = 'block';
	},
	cur : function() {
		var e = this.active();
		return e ? e : this.first();
	},
	active : function() {
		var es = this.lis();
		for(var i = 0; i < es.length; ++i) {
			if($(es[i]).hasClass('active')) {
				return es[i];
			}
		}
		return false;
	},
	select : function() {
		var e = this.active();
		return e ? $(e).fireEvent('mousedown') : 0;
	},
	lis : function() {
		return this.ul.children;
	},
	up : function() {
		var e = this.cur();
		if(e) {
			$(e).removeClass('active');
			if(e.previousSibling) {
				$(e.previousSibling).addClass('active');
			} else {
				this.last();
			}
		} else {
			this.last();
		}
	},
	down : function() {
		var e = this.cur();
		if(e) {
			$(e).removeClass('active');
			if(e.nextSibling) {
				$(e.nextSibling).addClass('active');
			} else {
				this.first();
			}
		} else {
			this.first();
		}
	},
	first : function() {
		var e = this.ul.firstChild;
		if(e) {
			$(e).addClass('active');
			return e;
		}
		return null;
	},
	last : function() {
		var e = this.ul.lastChild;
		if(e) {
			$(e).addClass('active');
			return e;
		}
		return null;
	}
};

function ynfeed(input, active_tags, is_edit, input_hidden) 
{
	var input = $(input);
	// validate younet friends
	(function() 
	{
		if( typeof ynfriends == 'undefined') 
		{
			ynfriends = {
				rows : []
			};
		}
		if( typeof ynfriends.rows == 'undefined') 
		{
			ynfriends.rows = new Array();
		}
		if( typeof ynfriends.rows.length == 'undefined') {
			ynfriends.rows = new Array(ynfriends.rows);
		}
	})();
	var hiw, hi, hi2, hi3;
	var tgs = new Array(), text = '', shortText = '', ac = null, reg = '', maxShow = 5, friends = new Array(), timeoutID = 0, timeout = 0, carretPos = 0;
	var _mapping = false;
	function initHighligher() 
	{
		if($$('.compose-content')) {
			$$('.compose-content').hide();
			if($$('.overTxtLabel')) {
				$$('.overTxtLabel').hide();
			}
		}
		input.style.display = 'block';
		var size = input.getSize();
		var p = $(input.parentNode);
		hiw = document.createElement('DIV');
		// hiw.style.overflow = 'hidden';
		hiw.style.position = 'absolute';

		hiw.className = 'fhighlighter';
		hiw.id = 'ynfeed_fhighlighter';
		$(hiw).inject(p, 'before');
		hi = document.createElement('DIV');

		hi.id = input.id + '_hightlighter';
		hi.className = 'ynfeedef2';

		hi.style.height = size.y + 'px';
		hi.style.position = 'relative';
		$(hi).inject(hiw, 'bottom');
		hi = $(hi);
		hi2 = document.createElement('INPUT');
		hi2.setAttribute('type', 'hidden');
		hi2.setAttribute('id','ynfeed_input_hidden');
		if(is_edit)
		{
			hi2.setAttribute('value', input_hidden);
		}
		hi2 = $(hi2);
		hi2.inject(hiw, 'bottom');
		hi3 = document.createElement('INPUT');
		hi3.setAttribute('type', 'hidden');
		hi3.setAttribute('name', 'tagged_users');
		hi3.id = input.id + '_tagged_users';
		hi3 = $(hi3);
		hi3.inject(hiw, 'bottom');
		hi3 = document.createElement('INPUT');
		hi3.setAttribute('type', 'hidden');
		hi3.setAttribute('name', 'tagged_groups');
		hi3.id = input.id + '_tagged_groups';
		hi3 = $(hi3);
		hi3.inject(hiw, 'bottom');
		hi3 = document.createElement('INPUT');
		hi3.setAttribute('type', 'hidden');
		hi3.setAttribute('name', 'body_html');
		hi3.id = input.id + '_body_html';
		hi3 = $(hi3);
		hi3.inject(hiw, 'bottom');

		p.style.position = 'relative';
		input.style.background = 'transparent';
		p.style.background = 'transparent';
	}

	initHighligher();

	function processText() {
		var s = findMatch();
		return s.length > 0 ? getFriends(s) : ac.hide();
	}

	function keyup(evt) {
		if(evt && evt.key) 
		{
			if(evt.key == 'esc' || !ac.isHide && ['up', 'esc', 'down', 'enter', 'tab'].indexOf(evt.key) > -1) {
				return false;
			}
		}
		if(active_tags)
		{
			processText();
			TimeOut();
			timeout = window.setTimeout(takeMap, 1);
		}
	}

	function TimeOut() {
		if(timeout) {
			window.clearTimeout(timeout);
		}
		timeout = 0;
	}

	function keydown(evt) {
		if(evt && evt.key) {
			if(ac.isHide) {
				return;
			}
			var k = evt.key;
			if(k == 'up') {
				ac.up();
				evt.stop();
			} else if(k == 'down') {
				ac.down();
				evt.stop();
			} else if(k == 'esc') {
				evt.stop();
				ac.hide();
			} else if(evt.key == 'enter' || evt.key == 'tab') {
				evt.stop();
				ac.select(fill);
			}
		}
	}

	function findMatch() {
		try {
			pos = getCarret();
			var pat = input.value.substring(0, pos);
			var p2 = pat.lastIndexOf('@');
			if(pos == -1) {
				return ''
			};
			value = pat.substr(p2, pos).replace(/\n/gi, '@').trim();
			value = value.replace(/\#/, '');
			
			// get char before @
			if(p2 > 0)
			{
				var before = pat.substr(p2 - 1, 1);
				if(before !== ' ')
				{
					return '';
				}
			}
			
			if(value.length > 20 || value.length < 1) {
				return '';
			}
			return value;
		} catch (e) {
			console.log(e);
		}
	}

	function curText() {
		var range;
		if(window.getSelection) {
			var sel = window.getSelection();
			if(sel.rangeCount) {
				range = sel.getRangeAt(0);
				text = range.startContainer.nodeValue || '';
				text = text.substring(0, range.startOffset);
			}

		} else if(document.selection) {
			range = document.selection.createRange();
			range.moveStart('character', -100);
			text = range.text;
		}

		return (text || '');

	}

	function getFriends(s) 
	{
		if((s.lastIndexOf('@') == -1) || (s.length < 2)) {
			return ac.hide();
		}
		checkTgs();
		delete (friends);
		friends = new Array();
		var e = new RegExp('(^|\\s+)' + s.escapeRegExp().replace('@', ''), 'i'), mx = 0;
		var l = ynfriends.rows.length;
		for(var i = 0; i < l; ++i) {
			var o = ynfriends.rows[i];
			if( typeof o == 'undefined' || typeof o.name == 'undefined') {
				continue;
			}
			if(o.name.match(e) && checkTgs2(o)) {
				friends.push(o);
				if(++mx >= maxShow) {
					break;
				}
			}
		}
		if(friends.length) {
			ac.show(ac.setRange(friends, fill));
		} else {
			ac.hide()
		}
	}

	function checkTgs2(o) {
		var e = null;
		for(var i in tgs) {
			e = tgs[i];
			if(e != undefined && e.id && e.st && e.id == o.id && e.st == o.st) {
				return false;
			}
		}
		return true;
	}

	function replaceEndByPos(text, search, replace, pos) {
		var pos2 = text.substr(0, pos).lastIndexOf(search);
		pos = pos2 < 0 ? pos : pos2;
		var t1 = text.substr(0, pos);
		var t2 = text.substr(pos + search.length);
		return t1 + replace + t2;
	}

	function paste(o) {
		if(timeout) {
			window.clearTimeout(timeout);
			timeout = 0;
		}
		var text = input.value;
		var replace2 = '#tags@' + o.st + '@' + o.id + '@;' + o.name + '#';
		input.value = replaceEndByPos(input.value, value, o.name, carretPos);
		hi2.value = hi2.value.replace(value, replace2).replace(/\r/g, " <br />");
		var pos = carretPos - value.length + o.name.length;
		setCarret(pos);
		takeMap();
		value = '';
		ac.hide();
	}

	function checkTgs() {
		tgs = new Array();
		var e, l, i;
		var r = hi2.value.match(/#tags@\w+@\d+@/gim);
		if(r != undefined && r.length) {
			l = r.length;
			for( i = 0; i < r.length; ++i) {
				e = r[i];
				if(e != undefined) {
					e = e.split('@')
					tgs.push({
						'st' : e[1],
						'id' : e[2]
					});
				};
			}
		}
		return tgs;
	}

	function fill(o) {
		ac.hide(paste(o));
		input.focus();
	}

	// can use mootools input.getCarretPosition();
	function getCarret() {
		carretPos = 0;
		// IE Support
		if(document.selection) {
			input.focus();
			var Sel = document.selection.createRange();
			Sel.moveStart('character', -input.value.length);
			carretPos = Sel.text.length;
		} else if(input.selectionStart || input.selectionStart == '0') {
			carretPos = input.selectionStart;
		}
		return carretPos;
	}

	// can user mootools input.setCarretPosition(pos);
	function setCarret(pos) {
		if(input.createTextRange) {
			var range = input.createTextRange();
			range.move("character", pos);
			range.select();
		} else if(input.selectionStart) {
			input.focus();
			input.setSelectionRange(pos, pos);
		}
	}


	input.addEvent('keydown', keydown);
	input.addEvent('keyup', keyup);
	window.input = input;
	var ac = ynac.init(input, function() 
	{
	});

	function takeMap() 
	{
		if(_mapping)
		{
			return;
		}
		$('ynfeed_activity_body_tagged_users').value = $('ynfeed_activity_body_tagged_groups').value = '';
		var s1 = input.value; // input from keyboard
		if(s1 == '')
		{
			hi2.value = '';
			hi.innerHTML = '';
			return;
		}
		var s2 = hi2.value;  // hidden ynfeed_input_hidden;
		
		// Tags
		var reg = /#tags@\w+@\d+@;[^\#]+#/gim;
		
		var ks = s2.match(reg); // array contain match from s2
		var ts = s2.split(reg); // array of substring split from s2
		
		var vs = new Array(); // new array will contain value from ks after replace
		if(ks == null) 
		{
			hi2.value = s1;
			s1 = s1.replace(/<iframe/gim, '&lt;iframe');
			s1 = s1.replace(/<\/iframe>/gim, '&lt;/iframe&gt;');
			s1 = s1.replace(/<style/gim, '&lt;style');
			s1 = s1.replace(/<\/style>/gim, '&lt;/style&gt;');
			s1 = s1.replace(/<img/gim, '&lt;img');
			s1 = s1.replace(/\n/g, '<br />');
			hi.innerHTML = s1;
			takeHashMap();
			return;
		}
		if(takeHashMap())
		{
			s1 = hi2.value;
		}
		var len = ks.length;
		for(var i = 0; i < len; ++i) 
		{
			var s3 = ks[i];
			vs[i] = s3.replace(/^#tags@\w+@\d+@;/, '').replace(/\#$/, '');
		}
		var res = '';
		var hv = '';
		var ces = '';
		for(var i = 0; i < len; ++i) 
		{
			// check user tagged to remove when delete tag
			var item_tagged = ks[i].match(/#tags@tag(\w+)@(\d+)@/);
			if(item_tagged && item_tagged[2]) 
			{
				if(item_tagged[1] == 'user')
				{
					$('ynfeed_activity_body_tagged_users').value += ',' + item_tagged[2];
				}
				else if(item_tagged[1] == 'group')
				{
					$('ynfeed_activity_body_tagged_groups').value += ',' + item_tagged[2];
				}
			}
			
			var pv = s1.search(vs[i]);
			if(pv > -1) {
				ts[i] = s1.substr(0, pv);
				s1 = s1.substr(pv + vs[i].length);
			} else {
				ks[i] = '';
				vs[i] = '';
			}
			var kk = ts[i] || '';
			res += kk + ks[i];
			if(vs[i])
				hv += kk + '<a href="' + (ks[i].replace(/;.+$/, '') ) + '">' + vs[i] + '</a>';
		}

		if(i > 0) {
			ces = s1.replace(vs[i - 1], '');
			res += ces
			hv += ces;
		}
		hi2.value = res;
		hv = hv.replace(/<iframe/gim, '&lt;iframe');
		hv = hv.replace(/<\/iframe>/gim, '&lt;/iframe&gt;');
		hv = hv.replace(/<style/gim, '&lt;style');
		hv = hv.replace(/<\/style>/gim, '&lt;/style&gt;');
		hv = hv.replace(/<img/gim, '&lt;img');
		hv = hv.replace(/\n/g, '<br />') + '';
		hv = hv.replace(/(#hashtags)@([^\#]+)#/gim,'<a href="$1@$2@">#$2</a>');
		hv = hv.replace(/­/gim, ''); // remove virtual character
		hi.innerHTML = hv;
		_mapping = false;
	}

	function takeHashMap() 
	{
		var s1 = input.value; // input from keyboard
		var reg = /(^|\s)#([^\s|\@]+)/gim;
		var ks = s1.match(reg); // array contain match from s1
		
		if(ks != null) 
		{
			var ts = s1.split(reg); // array of substring split from s1
			var res = '';
			var hv = '';
			var ces = '';
			var len = ks.length;
			
			for(var i = 0; i < len; ++i) 
			{
				ks[i] = ks[i].replace(/\s/, '');
				ts[i] = ts[i].replace(/\s/, '');
				var pv = s1.search(ks[i]);
				if(pv > -1) 
				{
					ts[i] = s1.substr(0, pv);
					s1 = s1.substr(pv + ks[i].length);
				} 
				else 
				{
					ks[i] = '';
				}
				var kk = ts[i] || '';
				res += kk + '#hashtags@' + ks[i].replace(/\#/, '') + '#';
				hv += kk + '<a href="#hashtags@' + (ks[i].replace(/\#/, '')) + '@">' + ks[i] + '</a>';
			}
	
			if(i > 0) 
			{
				ces = s1.replace(ks[i - 1], '');
				res += ces
				hv += ces;
			}
			hi2.value = res; // hidden ynfeed_input_hidden;
			hv = hv.replace(/<iframe/gim, '&lt;iframe');
			hv = hv.replace(/<\/iframe>/gim, '&lt;/iframe&gt;');
			hv = hv.replace(/<style/gim, '&lt;style');
			hv = hv.replace(/<\/style>/gim, '&lt;/style&gt;');
			hv = hv.replace(/<img/gim, '&lt;img');
			hv = hv.replace(/­/gim, ''); // remove virtual character
			hi.innerHTML = hv.replace(/\n/g, '<br />') + '';
			return true;
		}
		return false;
	}
	
	input.addEvent('focus', function() {
		if(input.value == input.getAttribute('placeholder')) {
			input.value = '';
			input.removeClass('input_placeholder');
		}
		if($('ynfeed_composer_tab'))
			$('ynfeed_composer_tab').style.display = '';
		if($('ynfeed_withfriends'))
			$('ynfeed_withfriends').style.display = 'none';
		if($('ynfeed_checkin'))
			$('ynfeed_checkin').style.display = 'none';
		if($('ynfeed-activity-form'))
		{
			$('ynfeed-activity-form').removeClass('ynfeed_form_border');
		}
		if($('ynfeed_fhighlighter'))
		{
			$('ynfeed_fhighlighter').addClass('fhighlighter_click');
		}
	});
	input.addEvent('blur', function() {
		if(input.value.trim() == '') {
			input.value = input.getAttribute('placeholder');
			input.addClass('input_placeholder');
		}
		ac.hide();
	});
	function autogrow(e, k) {
		var _resizing = false, useNullHeightShrink = true, internval = false;
		function handle() {
			if(_resizing)
				return;
			_resizing = true;
			_resize();
			if(Browser.Engine.gecko || Browser.Engine.webkit) {
				_shrink();
			}
			_resizing = false;
		}

		function _resize() {
			var scrollHeight = e.getScrollSize().y;
			if(scrollHeight) {
				var newHeight = _getHeight();
				var oldHeight = e.getSize().y;
				if(newHeight != oldHeight) {
					var h = newHeight + 'px';
					e.style.maxHeight = k.style.height = k.style.maxHeight = e.style.height = h;
				}
			} else {
				_estimate();
			}
		}

		function _getHeight() {
			var height = e.getScrollSize().y;
			if(Browser.Engine.gecko) {
				height += e.offsetHeight - e.clientHeight;
			} else if(Browser.Engine.trident) {
				height += e.offsetHeight - e.clientHeight;
			} else if(Browser.Engine.webkit) {
				height += e.getStyle('border-top-width').toInt() + e.getStyle('border-bottom-width').toInt();
			} else if(Browser.Engine.presto) {// Maybe need for safari < 4
				height += e.getStyle('padding-bottom').toInt();
			}
			return height;
		}

		function _shrink() {
			if(useNullHeightShrink) {
				e.style.height = '0px';
				_resize();
			} else {
				var scrollHeight = e.getScrollSize().y;
				var paddingBottom = e.getStyle('padding-bottom').toInt();

				// tweak padding to see if height can be reduced
				e.style.paddingBottom = paddingBottom + 1 + "px";
				// see if the height changed by the 1px added
				var newHeight = _getHeight() - 1;
				// if can be reduced, so now try a big chunk
				if(e.getStyle('max-height').toInt() != newHeight) {
					e.style.paddingBottom = paddingBottom + scrollHeight + "px";
					e.scrollTop = 0;
					var h = _getHeight() - scrollHeight + "px";
					e.style.maxHeight = h;
				}
				e.style.paddingBottom = paddingBottom + 'px';
			}
		}

		function _estimate() {
			e.style.maxHeight = "";
			e.style.height = "auto";
			e.rows = (e.value.match(/(\r\n?|\n)/g) || []).length + 1;
		}

		function init() {
			e.setStyles({
				'overflow-x' : 'auto',
				'overflow-y' : 'hidden',
				'-mox-box-sizing' : 'border-box',
				'-ms-box-sizing' : 'border-box',
				'resize' : 'none',
			});
			e.addEvent('focus', handle);
			e.addEvent('keyup', handle);
			e.addEvent('paste', handle);
			e.addEvent('cut', handle);
			if(Browser.Engine.webkit || Browser.Engine.trident)
				e.addEvent('scroll', handle)
			handle();
		}

		init();
	}

	if( typeof tabContainerSwitch != "undefined") {
		var els = $$('.tab_layout_ynfeed_feed a');

		if(els.length) {
			tabContainerSwitch(els[0], 'generic_layout_container layout_ynfeed_feed');
		}
	}
	
	autogrow(input, hi);
	if(is_edit)
	{
		input.fireEvent('keyup');
		input.fireEvent('focus');
	}
}