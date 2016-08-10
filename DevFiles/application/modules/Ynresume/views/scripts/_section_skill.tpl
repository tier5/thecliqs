<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
    $owner = $this -> resume -> getOwner();
    $skills = $resume -> getAllSkills();
    $canEndorse = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'endorse')->checkRequire();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $maxSkill = 0;
    $totalSkill = count($skills);
    if ($manage)
    {
        $level_id = $viewer -> level_id;
        $maxSkill = $permissionsTable->getAllowed('ynresume_resume', $level_id, 'max_skill');
        if ($maxSkill == null)
        {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $level_id)
            ->where('type = ?', 'ynresume_resume')
            ->where('name = ?', 'max_skill'));

            if ($row) {
                $maxSkill = $row->value;
            }
        }
    }

?>
<?php 
if (count($skills) <= 0 && $manage) {
    $create = true;
}
?>
<?php if (count($skills) > 0 || (!$hide && ($create || $edit))) : ?>
<script type="text/javascript">
    /*Render Skill section by JS*/
    function renderSkillSection(type, params) {
        var resume_id = <?php echo $this->resume->getIdentity();?>;
        var url = '<?php echo $this->url(array('action' => 'render-section', 'resume_id' => $this->resume->getIdentity()), 'ynresume_specific', true)?>';
        var data = {};
        data.type = type;
        data.reload = 1;
        data.params = params;
        var request = new Request.HTML({
            url : url,
            data : data,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                elements = Elements.from(responseHTML);
                if (elements.length > 0) {
                    if ($('sections-content-item_'+type)) {
                        var content = $('sections-content-item_'+type).getElements('.ynresume-section')[0];
                        content.empty();
                        content.adopt(elements);
                    } else {
                        var li = new Element('li', {
                            'class': 'sections-content-item',
                            id: 'sections-content-item_'+type,
                        });
                        var label = new Element('label', {
                            'class': 'order',
                            html: '<span class="ynresume-section-move"><i class="fa fa-arrows"></i></span><span class="ynresume-section-collapse"><i class="fa fa-chevron-down"></i></span>'
                        });
                        var div = new Element('div', {
                            'class': 'ynresume-section'
                        })
                        div.adopt(elements);
                        li.grab(label);
                        li.adopt(div);
                        $('sections-content-items').grab(li);
                    }
                    eval(responseJavaScript);
                    addEventToForm();
                    if (['experience', 'education'].indexOf(type) != -1 && (params.save || params.remove)) {
                        reloadCoverWidget();
                    }
                    $('sections-content-item_'+type).setFocus();
                }
            }
        });
        request.send();
    }

    /* BEGIN - Customizing awesome auto suggestion */
    (function () {

        function $(expr, con) {
            if (!expr) return null;
            return typeof expr === 'string'? (con || document).querySelector(expr) : expr;
        }

        function $$(expr, con) {
            return Array.prototype.slice.call((con || document).querySelectorAll(expr));
        }

        $.create = function(tag, o) {
            var element = document.createElement(tag);

            for (var i in o) {
                var val = o[i];

                if (i == "inside") {
                    $(val).appendChild(element);
                }
                else if (i == "around") {
                    var ref = $(val);
                    ref.parentNode.insertBefore(element, ref);
                    element.appendChild(ref);
                }
                else if (i in element) {
                    element[i] = val;
                }
                else {
                    element.setAttribute(i, val);
                }
            }

            return element;
        };

        $.bind = function(element, o) {
            if (element) {
                for (var event in o) {
                    var callback = o[event];

                    event.split(/\s+/).forEach(function (event) {
                        element.addEventListener(event, callback);
                    });
                }
            }
        };

        $.fire = function(target, type, properties) {
            var evt = document.createEvent("HTMLEvents");

            evt.initEvent(type, true, true );

            for (var j in properties) {
                evt[j] = properties[j];
            }

            target.dispatchEvent(evt);
        };

        var _ = self.Awesomplete = function (input, o) {
            var me = this;

            // Setup environment
            o = o || {};
            this.input = input;
            input.setAttribute("aria-autocomplete", "list");

            this.minChars = +input.getAttribute("data-minchars") || o.minChars || 1;
            this.maxItems = +input.getAttribute("data-maxitems") || o.maxItems || 100;

            if (input.hasAttribute("list")) {
                this.list = "#" + input.getAttribute("list");
                input.removeAttribute("list");
            }
            else {
                this.list = input.getAttribute("data-list") || o.list || [];
            }
            this.filter = o.filter || _.FILTER_CONTAINS;
            this.sort = o.sort || _.SORT_BYLENGTH;

            this.autoFirst = input.hasAttribute("data-autofirst") || o.autoFirst || false;

            this.item = o.item || function (text, input) {
                return $.create("li", {
                    innerHTML: text.replace(RegExp(regEscape(input.trim()), "gi"), "<mark>$&</mark>"),
                    "aria-selected": "false"
                });
            };

            this.index = -1;

            this.container = $.create("div", {
                className: "awesomplete",
                around: input
            });

            this.ul = $.create("ul", {
                hidden: "",
                inside: this.container
            });

            // Bind events

            $.bind(this.input, {
                "input": function () {
                    me.evaluate();
                },
                "blur": function () {
                    me.close();
                },
                "keydown": function(evt) {
                    var c = evt.keyCode;

                    if (c == 13 && me.index > -1) { // Enter
                        evt.preventDefault();
                        me.select();
                    }
                    else if (c == 27) { // Esc
                        me.close();
                    }
                    else if (c == 38 || c == 40) { // Down/Up arrow
                        evt.preventDefault();
                        me[c == 38? "previous" : "next"]();
                    }
                }
            });

            $.bind(this.input.form, {"submit": function(event) {
                me.close();
            }});

            $.bind(this.ul, {"mousedown": function(evt) {
                var li = evt.target;

                if (li != this) {

                    while (li && !/li/i.test(li.nodeName)) {
                        li = li.parentNode;
                    }

                    if (li) {
                        me.select(li);
                    }
                }
            }});
        };

        _.prototype = {
            set list(list) {
                if (Array.isArray(list)) {
                    this._list = list;
                }
                else {
                    if (typeof list == "string" && list.indexOf(",") > -1) {
                        this._list = list.split(/\s*,\s*/);
                    }
                    else {
                        list = $(list);
                        if (list && list.children) {
                            this._list = [].slice.apply(list.children).map(function (el) {
                                return el.innerHTML.trim();
                            });
                        }
                    }
                }
            },

            close: function () {
                this.ul.setAttribute("hidden", "");
                this.index = -1;

                $.fire(this.input, "awesomplete-close");
            },

            open: function () {
                this.ul.removeAttribute("hidden");

                if (this.autoFirst && this.index == -1) {
                    this.goto(0);
                }

                $.fire(this.input, "awesomplete-open");
            },

            next: function () {
                var count = this.ul.children.length;

                this.goto(this.index < count - 1? this.index + 1 : -1);
            },

            previous: function () {
                var count = this.ul.children.length;

                this.goto(this.index > -1? this.index - 1 : count - 1);
            },

            // Should not be used, highlights specific item without any checks!
            goto: function (i) {
                var lis = this.ul.children;

                if (this.index > -1) {
                    lis[this.index].setAttribute("aria-selected", "false");
                }

                this.index = i;

                if (i > -1 && lis.length > 0) {
                    lis[i].setAttribute("aria-selected", "true");
                }
            },

            select: function (selected) {
                selected = selected || this.ul.children[this.index];

                if (selected) {
                    var prevented;

                    $.fire(this.input, "awesomplete-select", {
                        text: selected.textContent,
                        preventDefault: function () {
                            prevented = true;
                        }
                    });

                    if (!prevented) {
                        this.input.value = selected.textContent;
                    //	var index = selected.getParent('ul').getChildren('li').indexOf(selected);
                        this.close();
                        $.fire(this.input, "awesomplete-selectcomplete");
                    }
                }
            },

            evaluate: function() {
                var me = this;
                var value = this.input.value;

                if (value.length >= this.minChars && this._list.length > 0) {
                    this.index = -1;
                    // Populate list with options that match
                    this.ul.innerHTML = "";

                    this._list.filter(function(item) {
                        return me.filter(item, value);
                    })
                    .sort(this.sort)
                    .every(function(text, i) {
                        me.ul.appendChild(me.item(text, value));

                        return i < me.maxItems - 1;
                    });

                    this.open();
                }
                else {
                    this.close();
                }
            }
        };

        _.FILTER_CONTAINS = function (text, input) {
            return RegExp(regEscape(input.trim()), "i").test(text);
        };

        _.FILTER_STARTSWITH = function (text, input) {
            return RegExp("^" + regEscape(input.trim()), "i").test(text);
        };

        _.SORT_BYLENGTH = function (a, b) {
            if (a.length != b.length) {
                return a.length - b.length;
            }

            return a < b? -1 : 1;
        };

        function regEscape(s) { return s.replace(/[-\\^$*+?.()|[\]{}]/g, "\\$&"); }

        function init() {
            $$("input.awesomplete").forEach(function (input) {
                new Awesomplete(input);
            });
        }

        // DOM already loaded?
        if (document.readyState !== "loading") {
            init();
        } else {
            // Wait for it
            document.addEventListener("DOMContentLoaded", init);
        }

        _.$ = $;
        _.$$ = $$;

    })();
    /* END - Customizing awesome auto suggestion */
</script>

<?php if ($manage) : ?>
	<a class="ynresume-add-btn" href="javascript:void(0);" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add Skill')?></a>
<?php endif;?>

	<h3 class="section-label">
		<span class="section-label-icon"><i class="<?php echo Engine_Api::_()->ynresume()->getSectionIconClass($this->section);?>"></i></span>
		<span><?php echo $label;?></span>
	</h3>
    
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    
    <div class="ynresume-section-content">
    <!-- status is EDITING -->
    <?php if ($create):?>
    	
    	<script type="text/javascript">
			var appendSkillSpan = function(){
                <?php if ($maxSkill): ?>
                    total_skill = <?php echo $maxSkill ?>;
                <?php else: ?>
                    total_skill = 0;
                <?php endif; ?>
                if (total_skill == 0 || $$("span.close-btn").length >= total_skill) {
                    //$('ynresume-skill-tip').show();
                    //$$('#ynresume-skill-tip span')[0].set('html', '<?php echo $this -> translate("The maximum skills is");?>'+' '+ total_skill);
                    alert('<?php echo $this -> translate("The maximum skills is");?>'+' '+ total_skill);
                    return false;
                }
				var skills = $$("span.close-btn").get('val');
				var skillText = $$("input[name='skill_text']")[0].get('value');
                skillText = skillText.replace(/(<([^>]+)>)/ig,"");
				if (skills.indexOf(skillText) >= 0){
					alert('<?php echo $this->translate('This is a duplicate skill.');?>');
					return false;
				}
                if (skillText.trim() == '')
                {
                    $$("input[name='skill_text']")[0].set('value', '');
                    return false;
                }
				var skillKey = skillText + '_' + 0;
				span1 = new Element("span", {text: skillText});
				span2 = new Element("span", {class:"close-btn", html: "<i class='fa fa-close'></i>", val:skillText, events: {
				        'click': function(e) {
				            e.preventDefault();
				            var id = this.get('id');
							var val = this.get('val');
							var elm = this.getParent();
							if (elm !== null)
							{
								value = $("ynresume_skills_orders").get("value");
								value = value.replace(val+'_'+id, '');
								value = value.replace(',,', ',');
								$("ynresume_skills_orders").set("value", value);
								elm.dispose();
							}
				        }
				    }
			    });
				div = new Element("div", {id: skillKey, class:"skill-item"});
				span1.inject(div);
				span2.inject(div);
				div.inject($("ynresume-endorse-skill-list"));
				
				new Sortables('ynresume-endorse-skill-list', {
		            contrain: false,
		            clone: true,
		            handle: 'div',
		            opacity: 0.5,
		            revert: true,
		            onComplete: function(){
		                $("ynresume_skills_orders").set("value", this.serialize().toString());
		            }
		        });
			};
    	
    		window.addEvent('domready', function(){
    			if ($("ynresume-add-skill-btn") != null){
    				$("ynresume-add-skill-btn").addEvent('click', function(){
    					appendSkillSpan();
                        $("ynresume_skill_text").set('value', '');
    				});
    			}
    			if ($("ynresume_skill_text") != null){
    				$("ynresume_skill_text").addEvent('keypress', function(e){
        		    	if(e.code === 13){
        		    		appendSkillSpan();
        		    		$("ynresume_skill_text").set('value', '');
        		    		return false;
        		        }
        		    });
    			}
    			$$("span.close-btn").addEvent('click', function(){
    				var id = this.get('id');
    				var val = this.get('val');
    				var elm = this.getParent();
    				if (elm !== null)
    				{
    					value = $("ynresume_skills_orders").get("value");
    					value = value.replace(val+'_'+id, '');
    					value = value.replace(',,', ',');
    					$("ynresume_skills_orders").set("value", value);
    					elm.dispose();
    				}
    			});
    			if ($("ynresume-save-skill-btn") != null){
    				$("ynresume-save-skill-btn").addEvent('click', function(){
    					this.set('disabled', true);
        				this.set('html', '<i class="fa fa-spinner fa-spin"></i>');
    					var skills = $$("span.close-btn").get('val');
    					var notify = 1;
    					if ($$("input[name='endorse_notify']")[0].get('checked') === false){
    						notify = 0;
    					}
    					var myRequest = new Request({
    					    url: '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'endorse'), 'ynresume_extended');?>',
    					    method: 'post',
    					    format: 'json',
    					    async : true,
    					    data: {
    						    'skills': skills,
    						    'resume_id': <?php echo $this -> resume -> getIdentity();?>,
    							'notify': notify
    						},
    					    onSuccess: function(responseText, responseXML){
    					    	renderSkillSection('skill', []);
    					    },
    					});
    					myRequest.send();
    				});
    			}
    			if ($("ynresume-save-endorsements-btn") != null){
    				$("ynresume-save-endorsements-btn").addEvent('click', function(){
    					var skillmap_ids = $$("input[name='skillmap_ids']:checked").get('value');
    					var notify = 1;
    					if ($$("input[name='endorse_notify']")[0].get('checked') === false){
    						notify = 0;
    					}
    					var myRequest = new Request({
    					    url: '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'save-endorsements'), 'ynresume_extended');?>',
    					    method: 'post',
    					    format: 'json',
    					    async : true,
    					    data: {
    						    'skillmap_ids': skillmap_ids,
    						    'resume_id': <?php echo $this -> resume -> getIdentity();?>,
    						    'notify': notify
    						},
    					    onSuccess: function(responseText, responseXML){
    					        //alert('<?php echo $this->translate('Saved successfully!');?>');
                                renderSkillSection('skill', []);
                                if ($("sections-content-item_skill"))
                                {
                                    $("sections-content-item_skill").removeAttribute("tabindex");
                                }
    					    },
    					});
    					myRequest.send();
    				});
    			}
    			if ($("ynresume-skill-add-remove") != null){
    				$("ynresume-skill-add-remove").addEvent('click', function(){
    					$$('.ynresume-skill-tab').removeClass('tab-active');
    					this.addClass('tab-active');

    					$("ynresume-tab-content-add-remove").set('style', '');
    					$("ynresume-tab-content-manage-endorsements").set('style', 'display:none');
    				});
    			}
    			if ($("ynresume-skill-manage-endorsements") != null){
    				$("ynresume-skill-manage-endorsements").addEvent('click', function(){
    					$$('.ynresume-skill-tab').removeClass('tab-active');
    					this.addClass('tab-active');

    					$("ynresume-tab-content-add-remove").set('style', 'display:none');
    					$("ynresume-tab-content-manage-endorsements").set('style', '');
    				});
    			}

    			$$('.ynresume-section-skill-endorses-lists .ynresume-section-skill-endorses').addEvent('click', function(){
    				$$('.ynresume-section-skill-endorses-item').removeClass('skill-active');
    				this.getParent().addClass('skill-active');
    			});

    			new Sortables('ynresume-endorse-skill-list', {
    	            contrain: false,
    	            clone: true,
    	            handle: 'div',
    	            opacity: 0.5,
    	            revert: true,
    	            onComplete: function(){
    	                $("ynresume_skills_orders").set("value", this.serialize().toString());
    	            }
    	        });
    		});
    		
    	</script>
    	
    	<form rel="skill" class="section-form ynresume-section-skill-form">
    		<div class="ynresume-section-skill-form-header">
	    		<div class="ynresume-section-skill-form-content">
	    			<span>
	    				<input type="checkbox" name="endorse_notify" value="1" <?php echo ($resume -> isEndorseNotify()) ? 'checked="checked"' : '';?> />
	    				<?php echo $this -> translate("Send me notifications when my friends endorse me.");?>
	    			</span>
	    		</div>
	    
	    		<div class="ynresume-section-skill-form-header-tab">
	    			<a class="ynresume-skill-tab tab-active" id="ynresume-skill-add-remove" href="javascript:void(0);" rel="<?php echo $this->section;?>"><i class="fa fa-cube"></i> <?php echo $this->translate('Add & Remove')?></a>
	    			<a class="ynresume-skill-tab" id="ynresume-skill-manage-endorsements" href="javascript:void(0);" rel="<?php echo $this->section;?>"><i class="fa fa-cubes"></i> <?php echo $this->translate('Manage Endorsements')?></a>
	    		</div>
	    	</div>
    		
    		<div id="ynresume-tab-content-add-remove">
                <div class="tip" id="ynresume-skill-tip" style="display: none;">
                    <span></span>
                </div>
    			<div class="ynresume-section-skill-form-add ynresume-clearfix">
    				<input type="text" name="skill_text" autocomplete="off" id="ynresume_skill_text" value="" />
    				<script type="text/javascript">
    					var data_list = [];
    					<?php $allSkills = Engine_Api::_()->getDbtable('skills', 'ynresume')->fetchAll();?>
    					<?php foreach($allSkills  as $sk) :?>
    						var item = '<?php echo strip_tags($sk -> text);?>';
    						data_list.push(item);
    					<?php endforeach;?>
    					window.addEvent('domready', function() {
    						var input = document.getElementById("ynresume_skill_text");
    						if(input)
    						{
    							new Awesomplete(input, {
    								list: data_list
    							});
    						}
    					});
    				</script>    				
    				<a class="button bold" id="ynresume-add-skill-btn" href="javascript:void(0);"><?php echo $this -> translate("Add");?></a>
    			</div>
    			<div class="ynresume-clearfix">
    				<div style="float: right;"><i class="fa fa-arrows-h"></i><i class="fa fa-tasks"></i><i class="fa fa-arrows-h"></i> <?php echo $this -> translate("Drag to Reorder");?></div>
                    <?php if($maxSkill > $totalSkill): ?>
    				    <div id="ynresume-remaining-skill"><?php echo $this -> translate("You can still add: %s", intval($maxSkill) - intval($totalSkill));?></div>
                    <?php else: ?>
                        <div id="ynresume-remaining-skill"></div>
                    <?php endif; ?>
    			</div>
    			<?php $totalSkills = Engine_Api::_()->getDbtable('skills', 'ynresume')->getSkillsByUser($resume, $owner);?>
    			<div class="ynresume-endorse-skill-list" id="ynresume-endorse-skill-list">
    				<?php foreach ($totalSkills as $skill):?>
    					<div class="skill-item" id="<?php echo $skill -> text;?>_<?php echo $skill -> skill_id;?>">
    						<span>
    							<?php echo strip_tags($skill -> text);?>
    						</span>
    						<span class="close-btn" id="<?php echo $skill -> skill_id;?>" val="<?php echo $skill -> text;?>">
    							<i class="fa fa-close"></i>
    						</span>
    					</div>
    				<?php endforeach;?>
    			</div>
    			<div class="ynresume-form-buttons">
    				<input type="hidden" name="skills" id="ynresume_skills_orders" />
                    <input type="hidden" name="has_skills" id="ynresume_skills_flag" value="<?php echo (count($totalSkills)) ? 1: 0; ?>" />
    				<button type="button" id="ynresume-save-skill-btn"><?php echo $this->translate('Save')?></button>
    				<?php echo $this->translate(' or ')?>
    				<a href="javascript:void(0);" class="ynresume-cancel-skill-btn"><?php echo $this->translate('Cancel')?></a>
    			</div> 
    		</div>
    		
    		<div id="ynresume-tab-content-manage-endorsements" style="display: none;">
    			<?php $skills = $resume -> getAllSkills(false);?>
    			<?php if (count($skills) > 0) : ?>
    				<div class="ynresume-section-skill-endorses-lists">
	    				<?php foreach($skills as $skill):?>
	    				<div class="ynresume-section-skill-endorses-item">
	    					<div class="ynresume-section-skill-endorses">
	    						<span class="ynresume-section-skill-endorses-count"><?php echo count($skill['endorses'])?></span>
	    						<span class="ynresume-section-skill-endorses-text"><?php echo strip_tags($skill['text'])?></span>
	    					</div>
	    
	    					<div class="ynresume-section-skill-endorses-content">
	    					<?php foreach ($skill['endorses'] as $endorse):?>
	    						<?php if ($endorse -> user_id == $this->resume->user_id) continue;?>
	    						<?php $user = Engine_Api::_()->user()->getUser($endorse -> user_id);?>
	    						<div class="ynresume-section-skill-endorses-user">
	    							<input type="checkbox" name="skillmap_ids" value="<?php echo $endorse -> skillmap_id; ?>" <?php echo ($endorse->deleted != '1') ? 'checked="checked"' : ''; ?> />
                                    <?php $userHref = Engine_Api::_()->ynresume()->getHref($user);?>
	    							<?php echo $this->htmlLink($userHref, $this->itemPhoto($user, 'thumb.icon'));?>
	    							<?php echo $this->htmlLink($userHref, $user->getTitle(), array('class'=>'ynresume-section-skill-endorses-username'));?>
	    						</div>
	    					<?php endforeach;?>
	    					</div>
	    				</div>
	    				<?php endforeach;?>
	    			</div>
    				
    				<div class="ynresume-form-buttons">
    					<button type="submit" id="ynresume-save-endorsements-btn"><?php echo $this->translate('Save')?></button>
    					<?php echo $this->translate(' or ')?>
    					<a href="javascript:void(0);" class="ynresume-cancel-skill-btn"><?php echo $this->translate('Cancel')?></a>
    				</div>
    			<?php endif;?>
    		</div>
    	</form>
    
    <?php else:?>
    <!-- status is VIEWING -->
    	
    	<style type="text/css">
	    	.add-endorse-btn {
	    		cursor: pointer;
	    	}
    	</style>
    	
    	<script type="text/javascript">
    		window.addEvent('domready', function(){
    			$$("span.add-endorse-btn").addEvent('click', function(){
    				var skill = this.get('val');
    				var currentElm = this;
    				var is_endorse = false;
    				
    				if ( !this.getElement('i') ) {
    					return false;
    				}

    				if (this.getElement('i').get('class') == 'fa fa-plus')
    				{
    					is_endorse = true;
    				}
    				this.set('html','<i class="fa fa-spinner fa-spin"></i>');
					if (is_endorse)
					{
						url = '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'endorse-one'), 'ynresume_extended');?>'; 
					}
					else
					{
						url = '<?php echo $this -> url(array('controller' => 'skill', 'action' => 'unendorse-one'), 'ynresume_extended');?>';
					}
    				var myRequest = new Request({
    				    url: url,
    				    method: 'post',
    				    format: 'json',
    				    async : true,
    				    data: {
    					    'skill': skill,
    					    'resume_id': <?php echo $this -> resume -> getIdentity();?>
    					},
    				    onSuccess: function(responseText, responseXML){
    				    	renderSkillSection('skill', []);
    				    	if ($("sections-content-item_skill"))
    				    	{
    				    		$("sections-content-item_skill").removeAttribute("tabindex");
    				    	}
    				    },
    				});
    				myRequest.send();
    			});
    		});
    	</script>
    	<?php
    		$skills = $resume -> getAllSkills();
    		if (count($skills) <= 0 && $manage) 
    		{
    		    $create = true;
    		}
    	?>
        <h4><?php echo $this->translate("Top Skills") ?></h4>
    	<?php if (count($skills) > 0) : ?>
    		<div class="ynresume-section-skill-items">
    		<?php $i = 0;?>
    		<?php foreach($skills as $skill):?>
    			<?php if ($i < 8):?>
		    		<div class="ynresume-section-skill-item">
		    			<?php if (!$manage) : ?>
                            <?php if ($canEndorse) : ?>
                                <span class="add-endorse-btn" val="<?php echo $skill['text']?>">
                                    <?php if ( $this->viewer()->getIdentity() != $this->resume->user_id ) : ?>
                                        <?php if (!in_array($this->viewer()->getIdentity(), $skill['endorsed_user_ids'])):?>
                                            <i class="fa fa-plus"></i>
                                        <?php else: ?>
                                            <i class="fa fa-minus"></i>
                                        <?php endif;?>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
			    		<?php endif; ?>
		
		    			<div class="ynresume-section-skill-endorses">
		    				<?php if (count($skill['endorses'])) : ?>
		    					<span class="ynresume-section-skill-endorses-count"><?php echo count($skill['endorses']); ?></span>
		    				<?php endif; ?>
		    				<span class="ynresume-section-skill-endorses-text"><?php echo strip_tags($skill['text']);?></span>
		    			</div>
		    
		    			<div class="ynresume-section-skill-user">
		    			<?php if (!$manage && ( $this->viewer()->getIdentity() != $this->resume->user_id)) : ?>
                            <?php if ($canEndorse) : ?>
                                <span class="add-endorse-btn" val="<?php echo $skill['text']?>">
                                <?php if ( $this->viewer()->getIdentity() != $this->resume->user_id ) : ?>
                                    <?php if (!in_array($this->viewer()->getIdentity(), $skill['endorsed_user_ids'])):?>
                                        <i class="fa fa-plus"></i>
                                    <?php else: ?>
                                        <i class="fa fa-minus"></i>
                                    <?php endif;?>
                                <?php endif; ?>
                            <?php endif; ?>
				    		</span>
			    		<?php endif; ?>
		    			<?php foreach ($skill['endorses'] as $endorse):?>
		    				<?php if ($endorse -> user_id == $this->resume->user_id) continue;?>
		    				<?php $user = Engine_Api::_()->user()->getUser($endorse -> user_id);?>
                            <?php $userHref = Engine_Api::_()->ynresume()->getHref($user);?>
		    				<?php echo $this->htmlLink($userHref, $this->itemPhoto($user, 'thumb.icon'));?>
		    			<?php endforeach;?>
		    			</div>
		
		    			<div class="ynresume-section-skill-arrow">
                            <a class="smoothbox" href="<?php echo $this -> url(array('controller' => 'skill', 'action' => 'endorsers', 'resume_id' => $resume -> getIdentity(), 'skill' => $skill['text']), 'ynresume_extended');?>">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </div>
		    		</div>
		    	<?php endif;?>
		    	<?php $i++;?>
    		<?php endforeach;?>
    		</div>

    		<?php if (count($skills) > 8) : ?>
    			<div class="ynresume-section-skill-title"><?php echo $this->translate("%s also knows about ...", $owner->getTitle()); ?></div>
	    		<div class="ynresume-section-skill-mini-items">
	    		<?php $i = 0;?>
	    		<?php foreach($skills as $skill):?>
	    			<?php if ($i >= 8):?>
			    		<div class="ynresume-section-skill-mini-item">
			    			<div class="ynresume-section-skill-endorses">
			    				<?php if (count($skill['endorses'])) : ?>
			    					<span class="ynresume-section-skill-endorses-count"><?php echo count($skill['endorses']); ?></span>
			    				<?php endif; ?>
			    				<span class="ynresume-section-skill-endorses-text"><?php echo strip_tags($skill['text']);?></span>
			    			</div>

							<?php if (!$manage) : ?>
                                <?php if ($canEndorse) : ?>
                                    <span class="add-endorse-btn" val="<?php echo $skill['text']?>">
                                    <?php if ( $this->viewer()->getIdentity() != $this->resume->user_id ) : ?>
                                        <?php if (!in_array($this->viewer()->getIdentity(), $skill['endorsed_user_ids'])):?>
                                            <i class="fa fa-plus"></i>
                                        <?php else: ?>
                                            <i class="fa fa-minus"></i>
                                        <?php endif;?>
                                    <?php endif; ?>
                                    </span>
                                <?php endif; ?>
				    		<?php endif; ?>
			    		</div>
			    	<?php endif;?>
			    	<?php $i++;?>
	    		<?php endforeach;?>
	    		</div>
    		<?php endif; ?>

    	<?php endif;?>
    	
    <?php endif;?>
    </div>
<?php endif;?>