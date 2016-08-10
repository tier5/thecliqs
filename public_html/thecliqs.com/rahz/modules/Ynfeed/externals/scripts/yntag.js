/**
 * Observer - Observe formelements for changes
 *
 * - Additional code from clientside.cnet.com
 *
 * @version		1.1
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
var YnTagsObserver = new Class({

	Implements : [Options, Events],

	options : {
		periodical : false,
		delay : 1000
	},

	initialize : function(el, onFired, options) {
		this.element = document.id(el) || $$(el);
		this.addEvent('onFired', onFired);
		this.setOptions(options);
		this.bound = this.changed.bind(this);
		this.resume();
	},

	changed : function() {
		var value = this.element.get('value');
		if ($equals(this.value, value))
			return;
		this.clear();
		this.value = value;
		this.timeout = this.onFired.delay(this.options.delay, this);
	},

	setValue : function(value) {
		this.value = value;
		this.element.set('value', value);
		return this.clear();
	},

	onFired : function() {
		this.fireEvent('onFired', [this.value, this.element]);
	},

	clear : function() {
		$clear(this.timeout || null);
		return this;
	},

	pause : function() {
		if (this.timer)
			$clear(this.timer);
		else
			this.element.removeEvent('keyup', this.bound);
		return this.clear();
	},

	resume : function() {
		this.value = this.element.get('value');
		if (this.options.periodical)
			this.timer = this.changed.periodical(this.options.periodical, this);
		else
			this.element.addEvent('keyup', this.bound);
		return this;
	}
});

var $equals = function(obj1, obj2) {
	return (obj1 == obj2 || JSON.encode(obj1) == JSON.encode(obj2));
};

/**
 * Autocompleter
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version		1.1.2
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

var YnTagsAutocompleter = new Class({

	Implements : [Options, Events],

	options : {
		minLength : 1,
		markQuery : true,
		width : 'inherit',
		maxChoices : 10,
		injectChoice : null,
		customChoices : null,
		emptyChoices : null,
		visibleChoices : true,
		className : 'autocompleter-choices',
		zIndex : 100,
		delay : 1,
		observerOptions : {},
		fxOptions : {},

		autoSubmit : false,
		overflow : false,
		overflowMargin : 25,
		selectFirst : true,
		filter : null,
		filterCase : false,
		filterSubset : false,
		forceSelect : false,
		selectMode : true,
		choicesMatch : null,
		multiple : false,
		separator : ', ',
		separatorSplit : /\s*[,;]\s*/,
		autoTrim : false,
		allowDupes : false,

		cache : false,
		relative : true,
		tokenFormat : 'object',
		tokenIdKey : 'id',
		tokenValueKey : 'label',
		prefetchOnInit : false,
		alwaysOpen : false,
		ignoreKeys : false,
		ignoreOverlayFix : false
	},

	initialize : function(element, options) {
		this.element = document.id(element);
		this.setOptions(options);
		this.build();
		this.observer = new YnTagsObserver(this.element, this.prefetch.bind(this), $merge({
			'delay' : this.options.delay
		}, this.options.observerOptions));
		this.queryValue = null;
		this.caretPos = 0;
		if (this.options.filter)
			this.filter = this.options.filter.bind(this);
		var mode = this.options.selectMode;
		this.typeAhead = (mode == 'type-ahead');
		this.selectMode = (mode === true) ? 'selection' : mode;
		this.cached = [];
		if (this.options.prefetchOnInit)
			this.prefetch.delay(this.options.delay + 50, this);
	},

	/**
	 * build - Initialize DOM
	 *
	 * Builds the html structure for choices and appends the events to the element.
	 * Override this function to modify the html generation.
	 */
	build : function() {
		if (document.id(this.options.customChoices)) {
			this.choices = this.options.customChoices;
		} else {
			this.choices = new Element('ul', {
				'class' : this.options.className,
				'styles' : {
					'zIndex' : this.options.zIndex
				}
			}).inject(document.body);
			this.relative = false;
			if (this.options.relative) {
				this.choices.inject(this.element, 'after');
				this.relative = this.element.getOffsetParent();
			}
			if (!this.options.ignoreOverlayFix)
				this.fix = new OverlayFix(this.choices);
		}
		if (!this.options.separator.test(this.options.separatorSplit)) {
			this.options.separatorSplit = this.options.separator;
		}
		if (!this.options.alwaysOpen) {
			this.fx = (!this.options.fxOptions) ? null : new Fx.Tween(this.choices, $merge({
				'property' : 'opacity',
				'link' : 'cancel',
				'duration' : 200
			}, this.options.fxOptions)).addEvent('onStart', Chain.prototype.clearChain).set(0);
		}
		this.element.setProperty('autocomplete', 'off').addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', this.onCommand.bind(this)).addEvent('click', this.onCommand.bind(this, [false]));

		if (!this.options.alwaysOpen) {
			this.element.addEvent('focus', this.toggleFocus.create({
				bind : this,
				arguments : true,
				delay : 100
			})).addEvent('blur', this.toggleFocus.create({
				bind : this,
				arguments : false,
				delay : 100
			}));
		}
	},

	destroy : function() {
		if (this.fix)
			this.fix.destroy();
		this.choices = this.selected = this.choices.destroy();
	},

	toggleFocus : function(state) {
		this.focussed = state;
		if (!state)
			this.hideChoices(true);
		this.fireEvent((state) ? 'onFocus' : 'onBlur', [this.element]);
	},

	onCommand : function(e) {
		if (!e && this.focussed)
			return this.prefetch();
		if (e && e.key && !e.shift && !this.options.ignoreKeys) {
			switch (e.key) {
			case 'enter':
				//e.stop();
				if (!this.selected) {
					return true;
				}

				if (this.selected && this.visible) {
					this.choiceSelect(this.selected);
					return !!(this.options.autoSubmit);
				}
				break;
			case 'up':
			case 'down':
				var value = this.element.value;
				if (!this.prefetch() && this.queryValue !== null) {
					var up = (e.key == 'up');
					this.choiceOver((this.selected || this.choices)[
					(this.selected) ? ((up) ? 'getPrevious' : 'getNext') : ((up) ? 'getLast' : 'getFirst')
					](this.options.choicesMatch), true);
					this.element.value = value;
				}
				return false;
			case 'esc':
				this.hideChoices(true);
				break;
			case 'tab':
				if (this.selected && this.visible) {
					this.choiceSelect(this.selected);
					return !!(this.options.autoSubmit);
				} else {
					this.hideChoices(true);
					break;
				}

			}
		}
		this.fireEvent('onCommand', e);
		return true;
	},

	setSelection : function(finish) {
		var tokenInfo = this.selected.retrieve('autocompleteChoice');
		var input = (this.options.tokenFormat == 'object' ? tokenInfo[this.options.tokenValueKey] : tokenInfo );
		var value = input;
		var start = this.queryValue.length, end = input.length;
		if ((input.substr(0, start) || '').toLowerCase() != (this.queryValue || '').toLowerCase())
			start = 0;
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			value = this.element.value;
			start += this.queryIndex;
			end += this.queryIndex;
			var old = value.substr(this.queryIndex).split(split, 1)[0];
			value = value.substr(0, this.queryIndex) + input + value.substr(this.queryIndex + old.length);
			if (finish) {
				var tokens = value.split(this.options.separatorSplit).filter(function(entry) {
					return this.test(entry);
				}, /[^\s,]+/);
				if (!this.options.allowDupes)
					tokens = [].combine(tokens);
				var sep = this.options.separator;
				value = tokens.join(sep) + sep;
				end = value.length;
			}
		}
		// @todo figure what this is for
		if (this.options.autocompleteType == 'tag')
			this.observer.setValue(value);
		this.opted = value;
		if (finish || this.selectMode == 'pick')
			start = end;
		$try( function() {
			this.element.selectRange(start, end)
		}.bind(this));
		// This seems to be throwing an error sometimes
		this.fireEvent('onSelection', [this.element, this.selected, value, input]);
	},

	showChoices : function() {
		var match = this.options.choicesMatch, first = this.choices.getFirst(match);
		this.selected = this.selectedValue = null;
		if (this.fix) {
			var pos = this.element.getCoordinates(this.relative), width = this.options.width || 'auto';
			this.choices.setStyles({
				'width' : (width === true || width == 'inherit') ? pos.width : width
			});
		}
		if (!first)
			return;
		if (!this.visible) {
			this.visible = true;
			this.choices.setStyle('display', '');
			if (this.fx)
				this.fx.start(1);
			this.fireEvent('onShow', [this.element, this.choices]);
		}
		if (this.options.selectFirst || this.typeAhead || first.inputValue == this.queryValue)
			this.choiceOver(first, this.typeAhead);
		var items = this.choices.getChildren(match), max = this.options.maxChoices;
		var styles = {
			'overflowY' : 'hidden',
			'height' : ''
		};
		this.overflown = false;
		if (items.length > max) {
			var item = items[max - 1];
			styles.overflowY = 'scroll';
			styles.height = item.getCoordinates(this.choices).bottom;
			this.overflown = true;
		};
		this.choices.setStyles(styles);
		if (this.fix)
			this.fix.show();
		if (this.options.visibleChoices) {
			var scroll = document.getScroll(), size = document.getSize(), coords = this.choices.getCoordinates();
			if (coords.right > scroll.x + size.x)
				scroll.x = coords.right - size.x;
			if (coords.bottom > scroll.y + size.y)
				scroll.y = coords.bottom - size.y;
			window.scrollTo(Math.min(scroll.x, coords.left), Math.min(scroll.y, coords.top));
		}
	},

	hideChoices : function(clear) {
		if (clear) {
			var value = this.element.value;
			if (this.options.forceSelect)
				value = this.opted;
			if (this.options.autoTrim) {
				value = value.split(this.options.separatorSplit).filter($arguments(0)).join(this.options.separator);
			}
			this.observer.setValue(value);
		}
		if (!this.visible)
			return;
		this.visible = false;
		if (this.selected)
			this.selected.removeClass('autocompleter-selected');
		this.observer.clear();
		var hide = function() {
			this.choices.setStyle('display', 'none');
			if (this.fix)
				this.fix.hide();
		}.bind(this);
		if (this.fx)
			this.fx.start(0).chain(hide);
		else
			hide();
		this.fireEvent('onHide', [this.element, this.choices]);
	},

	prefetch : function() {
		var value = this.element.value, query = value;
		this.caretPos = this.getCarret();
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			var values = value.split(split);
			var index = this.element.getSelectedRange().start;
			var toIndex = value.substr(0, index).split(split);
			var last = toIndex.length - 1;
			index -= toIndex[last].length;
			query = values[last];
		}
		var body = this.element.value;
		query = body.substr(0, this.caretPos);
		
		pos = query.lastIndexOf('@');
		if(pos > 0)
		{
			var before = query.substr(pos - 1, 1);
			if(before !== ' ')
			{
				return false;
			}
		}
		
		if (pos == -1) {
			return false;
		}
		query = query.substr(pos, this.caretPos);
		if (query.length < 2) {
			return false;
		}
		query = query.substr(1);
		query = query.replace(/\#/, '');
		if (query.length < this.options.minLength) 
		{
			this.hideChoices();
		} else {
			if (query === this.queryValue || (this.visible && query == this.selectedValue)) {
				if (this.visible)
					return false;
				this.showChoices();
			} else {
				this.queryValue = query;
				this.queryIndex = index;
				if (!this.fetchCached())
					this.query();
			}
		}
		return true;
	},
	// can use mootools input.getCarretPosition();
	getCarret  : function() 
	{
		var carretPos = 0;
		// IE Support
		if(document.selection) {
			this.element.focus();
			var Sel = document.selection.createRange();
			Sel.moveStart('character', -this.element.value.length);
			carretPos = Sel.text.length;
		} else if(this.element.selectionStart || this.element.selectionStart == '0') {
			carretPos = this.element.selectionStart;
		}
		return carretPos;
	},

	// can user mootools input.setCarretPosition(pos);
	setCarret : function(pos)
	{
		if(this.element.createTextRange) {
			var range = this.element.createTextRange();
			range.move("character", pos);
			range.select();
		} else if(this.element.selectionStart) {
			this.element.focus();
			this.element.setSelectionRange(pos, pos);
		}
	},
	
	fetchCached : function() {
		switch( true ) {
		// Not enabled or no data
		case ( !this.options.cache ):
		// Query value became less specific
		case ( !this.cachedQueryValue || this.queryValue.length < this.cachedQueryValue.length ):
		// Query value became completely different
		case ( this.queryValue.indexOf(this.cachedQueryValue) == -1 ):
			// Choices left are less than max choices
			return false;
			break;
		}
		// If choices left are less than max choices, filter and return
		if (this.cached.length < this.options.maxChoices) {
			this.update(this.filter(this.cached));
			return true;
		}

		// If choices left are greater than or equal to maxChoices, but all match new query
		var newChoices = this.filter(this.cached, this.queryValue);
		if (newChoices.length >= this.cached.length) {
			this.update(this.filter(this.cached));
			return true;
		}

		// This means strange things?
		return false;
	},

	update : function(tokens) {
		document.id(this.choices).empty();
		this.cached = tokens;
		this.cachedQueryValue = this.queryValue;
		var type = tokens && $type(tokens);
		if (!type || (type == 'array' && !tokens.length) || (type == 'hash' && !tokens.getLength())) {
			(this.options.emptyChoices || this.hideChoices).call(this);
		} else {
			if (this.options.maxChoices < tokens.length && !this.options.overflow)
				tokens.length = this.options.maxChoices;
			tokens.each(this.options.injectChoice ||
			function(token) {
				tokenValue = (this.options.tokenFormat == 'object' ? token[this.options.tokenValueKey] : token );
				var choice = new Element('li', {
					'html' : this.markQueryValue(tokenValue)
				});
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
			}, this);
			this.showChoices();
		}
	},

	choiceOver : function(choice, selection) {
		if (!choice || choice == this.selected)
			return;
		if (this.selected)
			this.selected.removeClass('autocompleter-selected');
		this.selected = choice.addClass('autocompleter-selected');
		this.fireEvent('onSelect', [this.element, this.selected, selection]);
		if (!this.selectMode)
			this.opted = this.element.value;
		if (!selection)
			return;
		this.selectedValue = this.selected.retrieve('autocompleteChoice');
		if (this.overflown) {
			var coords = this.selected.getCoordinates(this.choices), margin = this.options.overflowMargin, top = this.choices.scrollTop, height = this.choices.offsetHeight, bottom = top + height;
			if (coords.top - margin < top && top)
				this.choices.scrollTop = Math.max(coords.top - margin, 0);
			else if (coords.bottom + margin > bottom)
				this.choices.scrollTop = Math.min(coords.bottom - height + margin, bottom);
		}
		if (this.selectMode)
			this.setSelection();
	},

	choiceSelect : function(choice) {
		if (choice)
			this.choiceOver(choice);
		this.setSelection(true);
		var body_value = this.element.value;
		var queryReplace = this.queryValue;
		this.queryValue = false;

		if (!this.options.alwaysOpen) {
			this.hideChoices();
		} else {
			this.observer.setValue('');
			this.prefetch.delay(this.options.delay, this);
		}
		this.fireEvent('onChoiceSelect', choice);
		if (this.options.autocompleteType == 'message') {
			this.element.value = '';
			var token = choice.retrieve('autocompleteChoice');

			//checking if the choice is a list of friends
			if (token.friends) {
			} else {
				this.replaceQueryToValue(choice.id, token, 'toValues', true, queryReplace, body_value);
			}
		}

	},
	replaceQueryToValue : function(name, token, newItem, list, query, bodyvalue) {
		var index = bodyvalue.lastIndexOf('@');
		name = '­' + name; // inser virtual character
		var newCaretPos = index + name.length;
		bodyvalue = bodyvalue.substr(0, index) + name + bodyvalue.substr(this.caretPos);
		replace_text = '@' + name;
		this.element.value = bodyvalue;
		this.setCarret(newCaretPos);

		this.pastes(bodyvalue, token, replace_text);
		if (token.type == 'group') {
			$('ynfeed_activity_body_tagged_groups').value += ',' + token.id;
		} else if (token.type = 'user') {
			$('ynfeed_activity_body_tagged_users').value += ',' + token.id;
		}
	},
	pastes : function(value, token, query) {
		var hi2 = $('ynfeed_input_hidden');
		var replace2 = '#tags@' + 'tag' + token.type + '@' + token.id + '@;' + '­' + token.label + '#'; // insert virtual character
		var hi2_value = hi2.value;
		var hi2_value_temp = hi2_value;
		hi2_value_temp = hi2_value_temp.replace(/(#hashtags)@([^\#]+)#/gim, '$1&$2&');
		hi2_value_temp = hi2_value_temp.replace(/(#tags)@(\w+)@(\d+)@;([^\#]+)#/gim,'$1&$2&$3&;$4#');
		index = hi2_value_temp.lastIndexOf('@');
		hi2_value = hi2_value.substr(0, index) + query + hi2_value.substr(index);
		hi2.value = hi2_value;
		hi2.value = hi2.value.replace(query, replace2).replace(/\r/g, " <br />");
		this.takeTagMap(this.element.value);
	},
	takeTagMap : function(value) {
		var hi = $('ynfeed_activity_body_hightlighter');
		var hi2 = $('ynfeed_input_hidden');
		var s1 = value;
		// input from keyboard
		var s2 = hi2.value;
		// hidden field value
		var reg = /#tags@\w+@\d+@;[^\#]+#/gim;
		var ks = s2.match(reg);
		// array contain match from s2
		var ts = s2.split(reg);
		// array of substring split from s2

		var vs = new Array();
		// new array will contain value from ks after replace
		if (ks == null) {
			this.takeHashMap();
			return;
		}
		if (this.takeHashMap()) {
			s1 = hi2.value;
		}

		var len = ks.length;
		for (var i = 0; i < len; ++i) {
			var s3 = ks[i];
			vs[i] = s3.replace(/^#tags@\w+@\d+@;/, '').replace(/\#$/, '');
		}
		var res = '';
		var hv = '';
		var ces = '';
		for (var i = 0; i < len; ++i) {
			var pv = s1.search(vs[i]);
			if (pv > -1) {
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
		if (i > 0) {
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
		hv = hv.replace(/(#hashtags)@([^\#]+)#/gim, '<a href="$1@$2@">#$2</a>');
		hv = hv.replace(/­/gim, ''); // remove virtual character
		hi.innerHTML = hv;
	},
	takeHashMap : function() {
		var hi = $('ynfeed_activity_body_hightlighter');
		var hi2 = $('ynfeed_input_hidden');
		var input = this.element;
		var s1 = input.value;
		// input from keyboard
		var reg = /(^|\s)#([^\s|\@]+)/gim;
		var ks = s1.match(reg);
		// array contain match from s1
		if (ks != null) {
			var ts = s1.split(reg);
			// array of substring split from s1
			var res = '';
			var hv = '';
			var ces = '';
			var len = ks.length;

			for (var i = 0; i < len; ++i) {
				ks[i] = ks[i].replace(/\s/, '');
				ts[i] = ts[i].replace(/\s/, '');
				var pv = s1.search(ks[i]);
				if (pv > -1) {
					ts[i] = s1.substr(0, pv);
					s1 = s1.substr(pv + ks[i].length);
				} else {
					ks[i] = '';
					vs[i] = '';
				}
				var kk = ts[i] || '';
				res += kk + '#hashtags@' + ks[i].replace(/\#/, '') + '#';
				hv += kk + '<a href="#hashtags@' + (ks[i].replace(/\#/, '')) + '@">' + ks[i] + '</a>';
			}

			if (i > 0) {
				ces = s1.replace(ks[i - 1], '');
				res += ces
				hv += ces;
			}
			hi2.value = res;
			// hidden ynfeed_input_hidden;
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
	},

	filter : function(tokens, queryValue) {
		queryValue = queryValue || this.queryValue;
		tokens = tokens || this.tokens;
		var regex = new RegExp(((this.options.filterSubset) ? '' : '^') + queryValue.escapeRegExp(), (this.options.filterCase) ? '' : 'i');
		if (this.options.tokenFormat == 'object') {
			var key = this.options.tokenValueKey;
			return tokens.filter(function(token) {
				return regex.test(token[key]);
			});
		} else {
			return tokens.filter(function(token) {
				return regex.test(token);
			});
		}

		return tokens;
	},

	/**
	 * markQueryValue
	 *
	 * Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
	 * Call this i.e. from your custom parseChoices, same for addChoiceEvents
	 *
	 * @param		{String} Text
	 * @return		{String} Text
	 */
	markQueryValue : function(str) {
		return (!this.options.markQuery || !this.queryValue) ? str : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<span class="autocompleter-queried">$1</span>');
	},

	/**
	 * addChoiceEvents
	 *
	 * Appends the needed event handlers for a choice-entry to the given element.
	 *
	 * @param		{Element} Choice entry
	 * @return		{Element} Choice entry
	 */
	addChoiceEvents : function(el) {
		return el.addEvents({
			'mouseover' : this.choiceOver.bind(this, el),
			'click' : this.choiceSelect.bind(this, el)
		});
	}
});

var OverlayFix = new Class({

	initialize : function(el) {
		if (Browser.Engine.trident) {
			this.element = document.id(el);
			this.relative = this.element.getOffsetParent();
			this.fix = new Element('iframe', {
				'frameborder' : '0',
				'scrolling' : 'no',
				'src' : 'javascript:false;',
				'styles' : {
					'position' : 'absolute',
					'border' : 'none',
					'display' : 'none',
					'filter' : 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			}).inject(this.element, 'after');
		}
	},

	show : function() {
		if (this.fix) {
			var coords = this.element.getCoordinates(this.relative);
			delete coords.right;
			delete coords.bottom;
			this.fix.setStyles($extend(coords, {
				'display' : '',
				'zIndex' : (this.element.getStyle('zIndex') || 1) - 1
			}));
		}
		return this;
	},

	hide : function() {
		if (this.fix)
			this.fix.setStyle('display', 'none');
		return this;
	},

	destroy : function() {
		if (this.fix)
			this.fix = this.fix.destroy();
	}
});

Element.implement({

	getSelectedRange : function() {
		if (!Browser.Engine.trident)
			return {
				start : this.selectionStart,
				end : this.selectionEnd
			};
		var pos = {
			start : 0,
			end : 0
		};
		var range = this.getDocument().selection.createRange();
		if (!range || range.parentElement() != this)
			return pos;
		var dup = range.duplicate();
		if (this.type == 'text') {
			pos.start = 0 - dup.moveStart('character', -100000);
			pos.end = pos.start + range.text.length;
		} else {
			var value = this.value;
			var offset = value.length - value.match(/[\n\r]*$/)[0].length;
			dup.moveToElementText(this);
			dup.setEndPoint('StartToEnd', range);
			pos.end = offset - dup.text.length;
			dup.setEndPoint('StartToStart', range);
			pos.start = offset - dup.text.length;
		}
		return pos;
	},

	selectRange : function(start, end) {
		if (Browser.Engine.trident) {
			var diff = this.value.substr(start, end - start).replace(/\r/g, '').length;
			start = this.value.substr(0, start).replace(/\r/g, '').length;
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', start + diff);
			range.moveStart('character', start);
			range.select();
		} else {
			this.focus();
			this.setSelectionRange(start, end);
		}
		return this;
	}
});

/* compatibility */

YnTagsAutocompleter.Base = YnTagsAutocompleter;

/**
 * Autocompleter.Local
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version		1.1.2
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

YnTagsAutocompleter.Local = new Class({

	Extends : YnTagsAutocompleter,

	options : {
		minLength : 0,
		delay : 200
	},

	initialize : function(element, tokens, options) {
		this.parent(element, options);
		this.tokens = tokens;
	},

	query : function() {
		this.update(this.filter());
	}
});

/**
 * Autocompleter.Request
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version		1.1.2
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

YnTagsAutocompleter.RequestTag = new Class({

	Extends : YnTagsAutocompleter,
	options : {
		postData : {},
		ajaxOptions : {},
		postVar : 'value',
		delay : 250

	},

	query : function() {
		var data = $unlink(this.options.postData) || {};
		data[this.options.postVar] = this.queryValue;
		data['users'] = $('ynfeed_activity_body_tagged_users').value;
		data['groups'] = $('ynfeed_activity_body_tagged_groups').value;
		var indicator = document.id(this.options.indicator);
		if (indicator)
			indicator.setStyle('display', '');
		var cls = this.options.indicatorClass;
		if (cls)
			this.element.addClass(cls);
		this.fireEvent('onRequest', [this.element, this.request, data, this.queryValue]);
		this.request.send({
			'data' : data
		});
	},

	/**
	 * queryResponse - abstract
	 *
	 * Inherated classes have to extend this function and use this.parent()
	 */
	queryResponse : function() {
		var indicator = document.id(this.options.indicator);
		if (indicator)
			indicator.setStyle('display', 'none');
		var cls = this.options.indicatorClass;
		if (cls)
			this.element.removeClass(cls);
		return this.fireEvent('onComplete', [this.element, this.request]);
	}
});

YnTagsAutocompleter.RequestTag.JSON = new Class({

	Extends : YnTagsAutocompleter.RequestTag,

	initialize : function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.JSON($merge({
			'url' : url,
			'link' : 'cancel'
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},
	queryResponse : function(response) {
		this.parent();
		this.update(response);

	}
});

YnTagsAutocompleter.RequestTag.HTML = new Class({

	Extends : YnTagsAutocompleter.RequestTag,

	initialize : function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.HTML($merge({
			'url' : url,
			'link' : 'cancel',
			'update' : this.choices
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},

	queryResponse : function(tree, elements) {
		this.parent();
		if (!elements || !elements.length) {
			this.hideChoices();
		} else {
			this.choices.getChildren(this.options.choicesMatch).each(this.options.injectChoice ||
			function(choice) {
				var value = choice.innerHTML;
				choice.inputValue = value;
				this.addChoiceEvents(choice.set('html', this.markQueryValue(value)));
			}, this);
			this.showChoices();
		}

	}
});

/* compatibility */

YnTagsAutocompleter.Ajax = {
	Base : YnTagsAutocompleter.RequestTag,
	Json : YnTagsAutocompleter.RequestTag.JSON,
	Xhtml : YnTagsAutocompleter.RequestTag.HTML
};
