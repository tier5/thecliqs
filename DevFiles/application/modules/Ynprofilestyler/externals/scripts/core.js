var ynps = {
	rules : {},

	outer : 0,
	
	slideshow : 0,
	
	opened : 0,
	
	existingStyle : null,
	
	stylesheetElement : null,
	
	isSaved: false,
	
	/**
	 * remove all the profile styles produced by this module
	 */
	removeStyle : function() {
		if (this.stylesheetElement != null) {
			$(this.stylesheetElement).destroy();
			this.stylesheetElement = null;
		}
	},
	
	applyTheme : function(layoutId) {
		this.getOriginalRules(layoutId);
	},

	/**
	 * get rules belonging to a layout
	 */
	getOriginalRules : function(layoutId) {
		var ruleRequest = new Request.JSON( {
			url : en4.core.baseUrl + 'ynprofilestyler/index/get-rules',
			method : 'GET',
			data : {
				'layout_id' : layoutId
			},
			async : false,
			onSuccess : function(responseJSON, responseText) {
				ynps.rules = responseJSON;
			}
		}).send();

		Array.each(this.rules, function(rule, index) {
			rule['changed'] = 1;
		});
	},
	
	/**
	 * get all possible rules that can be defined in this module
	 */
	getAllRules : function() {
		var rules;
		var ruleRequest = new Request.JSON( {
			url : en4.core.baseUrl + 'ynprofilestyler/index/get-all-rules',
			method : 'GET',			
			async : false,
			onSuccess : function(responseJSON, responseText) {
				rules = responseJSON;
				
			}
		}).send();

		Array.each(rules, function(rule, index) {
			if (ynps.rules[index]['value'] != rule['value']) {
				ynps.rules[index]['value'] = rule['value'];
				ynps.rules[index]['changed'] = 1;
			}
		});
		this.rules = rules;
	},

	/**
	 * open the iframe box to modifing the style
	 */
	open : function(data) {
		this.rules = {};

		var body = $$('body')[0];

		if (this.outer == 0) {
			this.existingStyle = data.style.clean();
			this.getOriginalRules();
			this.outer = new Element('div', {
				id : 'ynps-outer'
			});
			var iframe = new Element('iframe', {
				'src' : en4.core.baseUrl + 'ynprofilestyler/index',
				'name' : 'ynprofile-style',
				'id' : 'ynprofile-style',
				'width' : '100%'
			});
			this.outer.inject(body);
			iframe.inject(this.outer);
		} 

		// move the body down
		body.setStyle('margin-top', this.outer.getSize().y);
	},

	/**
	 * close the iframe box, stop editing the style 
	 */
	close : function(is_allowed) {		
		if (this.outer != 0) {
			/*
			var isChanged = false;			
			for ( var i = 0; i < this.rules.length; i++) {
				var rule = this.rules[i];
				if (rule['changed'] == '1') {					
					isChanged = true;
					break;
				}
			}
			*/
			if (ynps.isSaved === false) {
				// TODO
				if (confirm(ynpsPackageTrans['changeTheme'])) {
					this.save(is_allowed, null);
				} else {
					this.getOriginalRules();
					this.preview();
				}
			}			
			this.outer.destroy();
			this.outer = 0;
			this.iframe = 0;

			var body = $$('body')[0];
			body.setStyle('margin-top', 0);
		}
	},

	/**
	 * append new rule for the existing style
	 * @dompath : the css selector
	 * @name : rule name
	 * @value : rule value
	 * @type : the type of this rule, it can be image, color, text, or size
	 */
	appendRule : function(dompath, name, value, type) {
		var appliedValue = this.getAppliedValue(value, type);
		
		if (!navigator.userAgent.match(/msie/i)) {
			this.stylesheetElement.innerHTML += dompath + '{' + name + ':' +  appliedValue + ';}';
		} else {			 		
			this.stylesheetElement.styleSheet.cssText += dompath + '{' + name + ':' +  appliedValue + ';}';
		}
	},

	/**
	 * get the actually value depending on the type
	 */
	getAppliedValue : function(value, type) {
		var appliedValue = value;
		if (type) {
			var cmd = "this.callbacks." + type + "(value)";
			appliedValue = eval(cmd);
		}
		return appliedValue;
	},
	
	callbacks : {
		text : function(val) {
			return val;
		},
		
		color : function(val) {
			if (val != '') {
				return '#' + val;
			}
		},
		
		image : function(val) {
			if (val != '') {
				return 'url(' + val + ')';
			}
		},
		
		size : function(val) {
			if (!isNaN(val)) {
				return val + 'pt';
			}
			return val;
		}
	}, 

	/**
	 * preview the current style, erasing the existing style and create the new style based on the rules
	 */
	preview : function() {
		var doc = parent.document;
		var head = doc.getElementsByTagName('head')[0];
		
		if (this.opened == 0) {			
			var styles = $(head).getElements('style');
			for (var i = 0; i < styles.length; i++) {
				var strStyle = $$('style')[i].innerHTML.replace("<!--","").replace("-->","");
				strStyle = strStyle.clean();
				if (strStyle == this.existingStyle) {
					$(styles[i]).destroy();
					break;
				}
			}
			this.opened = 1;
		} 
		this.removeStyle();
		this.stylesheetElement = doc.createElement('style');
		this.stylesheetElement.ynps = 1;
		head.appendChild(this.stylesheetElement);

		Array.each(this.rules, function(rule, index) {
			if (rule['value'] || rule['changed'] == '1') {
				ynps.appendRule(rule['dompath'], rule['name'], rule['value'], rule['type']);
			}
		});
	},

	/**
	 * set value for a rule
	 */
	setRule : function(ruleId, value) {
		for (var i = 0; i < this.rules.length; i++) {
			var rule = this.rules[i];
			if (rule['rule_id'] == ruleId) {
				if (rule['value'] != value) {
					rule['value'] = value;
					rule['changed'] = 1;
					this.preview();
				}
				break;
			}
		}
	},
	
	getRuleValue : function(ruleId) {
		for (var i = 0; i < this.rules.length; i++) {
			var rule = this.rules[i];
			if (rule['rule_id'] == ruleId) {
				return rule['value'];				
			}
		}
	},
	
	/**
	 * save the current theme as a default theme (only the admins have the permission to use this feature)
	 */
	addToDefaultThemes : function() {
		var href = en4.core.baseUrl + 'ynprofilestyler/index/add';
		this.saveLayoutTemp();
		Smoothbox.open(href);
	},

	saveLayoutTemp : function() {
		var href = en4.core.baseUrl + 'ynprofilestyler/index/save-temp';
		var rules = [];
		Array.each(this.rules, function(r, index) {
			if (r['value']) {
				rules.push(r);
			}
		});
		var saveRequest = new Request.JSON( {
			url : href,
			method : 'POST',
			async : false,
			data : {
				'rules' : rules
			}
		}).send();
	},
	
	/**
	 * save the current layout for the user
	 */
	save : function(allowOtherUse, slideshowCfg) {
		var href = en4.core.baseUrl + 'ynprofilestyler/index/save';		
		
		var rules = [];
		Array.each(this.rules, function(r, index) {
			if (r['value']) {
				rules.push(r);
			}
		});
		var saveRequest = new Request.JSON( {
			url : href,
			method : 'POST',
			async : false,
			data : {
				'rules' : rules,
				'allowOtherUse' : allowOtherUse,
				'slideshowCfg' : slideshowCfg
			},
			onSuccess : function(responseJSON, responseText) {
				ynps.result = responseJSON;
			}
		}).send();
		
		if (this.result.message) {
			alert(this.result.message);
			// after saving, update the changed value for rules to 0			
			for (var i = 0; i < this.rules.length; i++) {
				ynps.rules[i]['changed'] = 0;
			}
			
			ynps.isSaved = true;
		}
	},
	
	/**
	 * use layout of another user
	 */
	useLayout : function(userId) {
		var ruleRequest = new Request.JSON( {
			url : en4.core.baseUrl + 'ynprofilestyler/index/use-user-layout',
			method : 'GET',
			data : {
				'user_id' : userId
			},
			async : false,
			onSuccess : function(responseJSON, responseText) {
				alert(responseJSON.message);
			}
		}).send();
	},
	
	/**
	 * add slideshow in the profile page
	 */
	addSlideshow : function(json) {
		if (this.slideshow == 0) {
			var top = json.top;
			var left = json.left;
			this.slideshow = new Element('div', {
				id : 'ynps-slideshow',
				html : json.html,
				styles : {
					'margin-top': parseInt(json.top, 10),
					'margin-left': parseInt(json.left, 10)
				}
			});
			this.slideshow.inject($('global_header'), 'after');
			
			var top = -(parseInt(json.height, 10) - parseInt(json.distance, 10));
			
			$('global_wrapper').set({
				'styles' : {
					'margin-top' : -(parseInt(json.height, 10) - parseInt(json.distance, 10)),
					'position' : 'relative',
					'z-index' : 3
				}
			});
			
			basic = new SlideShow('ynps-slideshow-container', {
				autoplay: true,
				delay: json.interval,
				selector: 'img'
			});
		} 		
	}
};