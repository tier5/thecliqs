var ynps = window.parent.ynps;

var ynps2 = {
	baseUrl : '',
	mode : 'live',
	time : null,
	currentRule : null,
	tabs : [],
	translatedSentences : [],
	
	addTranslatedSentence : function(originalSentence, translatedSentence) {
		var sentence = {
			'original' : originalSentence,
			'translated' : translatedSentence
		};
		var i;
		for (i = 0; i < this.translatedSentences.length; i++) {
			var sen = this.translatedSentences[i];
			if (sen['original'] == originalSentence) {
				break;
			}
		}
		if (i == this.translatedSentences.length) {
			this.translatedSentences.push(sentence);
		}
	},
	
	getTranslatedSentence : function(sentence) {
		var i;
		for (i = 0; i < this.translatedSentences.length; i++) {
			var sen = this.translatedSentences[i];
			if (sen['original'] == sentence) {
				return sen['translated'];
			}
		}
		
		return sentence;
	},
	
	addTab : function(ele, href, params) {
		var tab = {
			'ele' : ele,
			'href' : href,
			'params' : params
		};
		var existed = false;
		for (var i = 0; i < this.tabs.length; i++) {
			var t = this.tabs[i];
			if (t['ele'] == ele) {
				t['href'] = href;
				t['params'] = params;
				existed = true;
				break;
			}
		}
		if (!existed) {
			this.tabs.push(tab);
		}
	},

	executeAsync : function (func, time) {
	    setTimeout(func, time);
	},
	
	/**
	 * switch the current theme to the last saved theme of user
	 */
	switchBackToDefaultSettings : function(ele) {		
		$(ele).toggleClass('loading');
		this.executeAsync(function() {
			ynps.removeStyle();
			ynps.getOriginalRules();
			ynps2.removeAndUpdateTabs();
			$(ele).toggleClass('loading');
		}, 100);
	},
	
	/**
	 * switch the current theme to the default theme of the website
	 */
	switchBackToDefaultLayout : function(ele) {
		$(ele).toggleClass('loading');
		
		this.executeAsync(function() {
			ynps.removeStyle();
			ynps.getAllRules();
			ynps2.removeAndUpdateTabs();
			$(ele).toggleClass('loading');
		}, 100);
	},
	
	/**
	 * destroy all tabs elements and rebuild the tab content
	 */
	removeAndUpdateTabs : function() {
		for (var i = 0; i < this.tabs.length; i++) {
			var t = this.tabs[i];
			$(t['ele']).html('');
			this.update(t['ele'], t['href'], t['params'], true);
			//alert(i);
		}
	},
	
	update : function(ele, href, param, buildComponents, force) {
		var jsScripts = null;
		
		if ($(ele).html() == '' || force) {
			href = this.baseUrl + 'ynprofilestyler/' + href;
			$.post(href, param, function(html) {
				var regex = /<script[^>]*?>[\s\S]*?<\/script>/gi;
				jsScripts = html.match(regex);
				html = html.replace(regex, '');
				$(ele).html(html);
			});
			if (buildComponents) {
				this.buildComponents(ele);
			}
		}
		if (jsScripts != null) {
			for (var i = 0; i < jsScripts.length; i++) {
				var js = jsScripts[i].replace(/<\/?[^>]+(>|$)/g, '');
				window.setTimeout(function(){eval(js)},100);
			}
		}
		this.bindRuleValueToForm(ele);
	},

	buildComponents : function(element) {
		// build color components
		this.buildColorComponents(element);
		// build image dropdownlist components
		this.buildImageDropdownlistComponents(element);
	},

	buildImageDropdownlistComponents : function(element) {
		$(element + ' select.image').each(function(index, ele) {
			$(ele).find('option').each(function(index, obj) {
				if ($(obj).val() != '') {
					$(obj).attr("title", $(obj).val());
				}
			});
            $(ele).val(ynps2.getRuleValue($(ele).attr('rule_id')));
            if ($(element + ' input[name=use_color_radio]')) {                            
                if ($(ele).val()) {                                
                    $(element + ' input[name=use_color_radio]').filter('[value=image]').prop("checked",true);
                }
            }
			$(ele).css( {
				width : 180
			});
			$(ele).msDropDown( {
				'rowHeight' : 50,
				'height' : 100,
				'visibleRows' : 2
			});
		});
	},

	buildColorComponents : function(element) {		
		if (element != '#content-background') {
			$(element + ' div.color').each(function(index, ele) {
				var hiddenEle = $(ele).parent().next('input[type=hidden]');
				
				$(ele).ColorPicker( {
					onChange : function(hsb, hex, rgb) {
						$(hiddenEle).val(hex);
						$(ele).css('backgroundColor', '#' + hex);
					},
					color : '#' + ynps2.getRuleValue($(hiddenEle).attr('rule_id'))					
				});
			});
		} else {
			$('#content-background div.color').ColorPicker({
				flat : true,
				onChange : function(hsb, hex, rgb) {
					$('#content-background input[name=background_color]').val(hex).trigger('change');
				},
				color : '#' + ynps2.getRuleValue($('#content-background .color').attr('rule_id'))
			});

			$('#content-background .color').attr('complex', 1);
		}
	},

	openWnd : function(url, ele) {
		var win = window.open(url, "wnd",
				"menubar=1,resizable=1,width=350,height=250");
		if (ele) {
			this.currentRule = {
				'ruleId' : $(ele).attr('rule_id'),
				'updateEle' : ele
			};
		}
	},

	setValueForCurrentRule : function(value) {
		if (this.currentRule != null) {
			this.currentRule.value = value;
			$(this.currentRule.updateEle).val(value);			
		}
	},

	preview : function() {
		$('form').find('.rule-element').each(function(index, element) {
			if (typeof($(element).attr('preview')) === 'undefined' || $(element).attr('preview') != '0') {
				var ruleId = $(element).attr('rule_id');
				for ( var i = 0; i < ynps.rules.length; i++) {
					var rule = ynps.rules[i];
					if (rule['rule_id'] == ruleId) {
						if (ynps.rules[i]['value'] != $(element).val()/* && $(element).attr('name')*/) {
							ynps.rules[i]['value'] = $(element).val();
							ynps.rules[i]['changed'] = 1;
						}
						break;
					}
				}
			}
		});
		
		if (ynps) {
			ynps.preview();
		}
	},

	live : function() {
		if (this.mode == 'live') {
			this.preview();
			this.time = setTimeout("ynps2.live()", 2000);
		}
	},

	bindRuleValueToForm : function(divId) {
		$(divId + ' form').find('.rule-element').each(function(index, element) {
			var ruleId = $(element).attr('rule_id');
			for ( var i = 0; i < ynps.rules.length; i++) {
				var rule = ynps.rules[i];
				if ((typeof($(element).attr('preview')) === 'undefined' || $(element).attr('preview') != '0')
						&& rule['rule_id'] == ruleId){	
					var value = ynps.rules[i]['value'];
					var tagName = $(element).get(0).tagName;
					
					// if the element is the image dropdownlist, it usually have a textbox bellow this element
					// for user to input their own image
					if ($(element).hasClass('image') && tagName == 'SELECT') {
						var exists = false;
						$(element).find('option').each(function(){
						    if (this.value == value) {
						        exists = true;
						        return false;
						    }
						});
						if (!exists) {
							break;
						} else {
							// if the image is existed in the dropdownlist image, then the image path will not show in the input textfield
							$(divId + ' form input[name= '  + $(element).attr('name') + ']').attr('preview', 0);
						}
					}
					$(element).val(value);
					break;
				}
			}
		});
	},

	close : function() {
		if (this.time != null) {
			clearTimeout(this.time);
			this.time = null;
		}
		var is_allowed = 0;
		if ($('input[name=allow_members_to_user]').is(':checked')) {
			is_allowed = 1;
		}
		ynps.close(is_allowed);
	},
	
	save : function() {
		var is_allowed = 0;
		if ($('input[name=allow_members_to_user]').is(':checked')) {
			is_allowed = 1;
		}
		var contentSlideshowForm = $('#content-slideshow form');
		var slideshowCfg = null;
		if (contentSlideshowForm) {
			slideshowCfg =  $(contentSlideshowForm).serializeArray()
		}
		
		//
		
		
		ynps.save(is_allowed, slideshowCfg);
	},

	addToDefaultThemes : function() {
		ynps.addToDefaultThemes();
	},

	applyTheme : function(id) {
		ynps.removeStyle();
		ynps.getOriginalRules(id);
		ynps2.removeAndUpdateTabs();
	},

	activeTab : function(ele, href, param, index, buildComponents, force) {
		if (typeof (index) == 'undefined') {
			index = 0;
		}
		
		if (index == -1) {
			index = $('#vtab > ul > li').length - 1;
		}
		
		var element = $('#vtab > ul > li').get(index);
		if (element) {
			$('#vtab > ul > li').each(function(index, e) {
				$(e).removeClass('selected');
			});
			$('#vtab > div').each(function(index, e) {
				$(e).hide();
			});
			this.update(ele, href, param, buildComponents, force);
			$(element).addClass('selected');
			$(ele).show();

		}
	},

	setRule : function(ruleId, value) {
		ynps.setRule(ruleId, value);
	},

	getRuleValue : function(ruleId) {
		return ynps.getRuleValue(ruleId);
	},
	
	notPreviewElements : function(elements) {
		for (var i = 0; i < elements.length; i++) {
			var ele = elements[i];
			$(ele).attr('preview', 0);			
			ynps2.setRule($(ele).attr('rule_id'), '');
		}
	},
	
	isNumericKey : function(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    },
    
    /**
     * get all selected slides' ids 
     */
    getSlideIds : function() {
    	var frm = $('form[name=ynps-slides]');
    	var data = frm.serializeArray();
    	var slideIds = [];
    	for(var i = 0; i < data.length; i++) {
    		var v = data[i];
    		slideIds.push(v.value);
    	}
    	
    	return slideIds;
    },
    
    /**
     * delete some slides (using for the slideshow feature)
     */
    deleteSlides : function() {
    	var slideIds = this.getSlideIds();
    	if (slideIds && slideIds.length > 0) {
    		href = this.baseUrl + 'ynprofilestyler/index/delete-slides';
			$.post(href, {'ids' : slideIds, 'format' : 'json'}, function(json) {
				if (json.result) {
					ynps2.activeTab('#content-slideshow','index/slideshow', {}, -1, false, true);
				}
			});			
    	} else {
    		alert(this.getTranslatedSentence('You have not selected any slides'));
    	}
    },
    
    /**
     * publish some slides (using for the slideshow feature)
     */
    publishSlides : function(published) {
    	var slideIds = this.getSlideIds();
    	if (slideIds && slideIds.length > 0) {
    		href = this.baseUrl + 'ynprofilestyler/index/publish-slides';
			$.post(href, {'ids' : slideIds, 'published' : published, 'format' : 'json'}, function(json) {
				if (json.result) {
					ynps2.activeTab('#content-slideshow','index/slideshow', {}, -1, false, true);
				}
			});			
    	} else {
    		alert(this.getTranslatedSentence('You have not selected any slides'));
    	}
    }
};

if (ynps) {
	ynps.iframe = ynps2;
}