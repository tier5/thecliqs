<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $level_id = 5;
    if ($viewer->getIdentity()) $level_id = $viewer->level_id;
    $resume = (isset($params['view']) && $params['view']) ? Engine_Api::_()->core()->getSubject() : $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
    
    $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
    if ($business_enable) {
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table->getBusinessesSelect(array('status' => 'published'));
        $businesses = $table->fetchAll($select);
    }
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max_photo = $permissionsTable->getAllowed('ynresume_resume', $level_id, 'max_photo');
    if ($max_photo == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $level_id)
        ->where('type = ?', 'ynresume_resume')
        ->where('name = ?', 'max_photo'));
        if ($row) {
            $max_photo = $row->value;
        }
    }
?>
<?php
$certification = $resume->getAllCertification();
if (count($certification) <= 0 && $manage) {
    $create = true;
}
?>

<?php if (count($certification) > 0 || (!$hide && ($create || $edit))) : ?>
    <?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>

    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add New Certification')?></a>
    <?php endif; ?>
    
    <h3 class="section-label">
        <span class="section-label-icon"><i class="<?php echo Engine_Api::_()->ynresume()->getSectionIconClass($this->section);?>"></i></span>
        <span><?php echo $label;?></span>
    </h3>
    
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    
    <div class="ynresume-section-content">
    <?php if ($manage) : ?>
        <?php if (!$hide && ($create || $edit)) : ?>
        <?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"); ?>
        <div id="ynresume-section-form-certification" class="ynresume-section-form">
            <form rel="certification" class="section-form" enctype="multipart/form-data">
                <p class="error"></p>
                <?php $item = null;?>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php $item = Engine_Api::_()->getItem('ynresume_certification', $params['item_id']);?>
                <input type="hidden" name="item_id" class="item_id" id="certification-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
                <?php endif; ?>
                
                <div id="certification-name-wrapper" class="ynresume-form-wrapper">
                    <label for="certification-name"><?php echo $this->translate('*Certification Name')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="certification-name" name="name" value="<?php if ($item) echo htmlentities($item->name);?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="certification-authority-wrapper" class="ynresume-form-wrapper">
                    <label for="certification-authority"><?php echo $this->translate('Certification Authority')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="certification-authority" name="authority" value="<?php if ($item) echo htmlentities($item->authority);?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="certification-license_number-wrapper" class="ynresume-form-wrapper">
                    <label for="certification-license_number"><?php echo $this->translate('License Number')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="certification-license_number" name="license_number" value="<?php if ($item) echo htmlentities($item->license_number);?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="certification-url-wrapper" class="ynresume-form-wrapper">
                    <label for="certification-url"><?php echo $this->translate('Certification URL')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="certification-url" name="url" value="<?php if ($item) echo htmlentities($item->url);?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="certification-time_period-wrapper" class="ynresume-form-wrapper">                
                    <label><?php echo $this->translate('*Time Period')?></label>
                    <div class="ynresume-form-input ynresume-form-input-4item">
                        <div class="">
                            <select name="start_month" id="certification-start_month" value="<?php if ($item) echo $item->start_month?>">
                                <?php $month = array('Choose...', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')?>
                                <?php foreach ($month as $key => $value) : ?>
                                <option value="<?php echo $key?>" <?php if ($item && $item->start_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="start_year" id="certification-start_year" value="<?php if ($item) echo $item->start_year?>"/>
                             - 
                            <select name="end_month" id="certification-end_month">
                                <?php foreach ($month as $key => $value) : ?>
                                <option value="<?php echo $key?>" <?php if ($item && $item->end_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="end_year" id="certification-end_year" value="<?php if ($item) echo $item->end_year?>"/>
                            <label id="certification-present"><?php echo $this->translate('Present')?></label>
                        </div>
                        <div class="ynresume-form-input-checkbox">
                            <input type="checkbox" id="certification-current" name="current" value="1" <?php if ($item && !$item->end_year) echo 'checked'?>/>
                            <label for="certification-current"><?php echo $this->translate('This certificate does not expire')?></label>
                        </div>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="certification-photos-wrapper" class="ynresume-form-wrapper upload-photos-wrapper">
                    <label><?php echo $this->translate('Add photos')?></label>
                    <div class="ynresume-form-input">
                        <div id="file-wrapper">
                            <div class="form-element">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                <p class="element-description"><?php echo $this->translate(array('add_photo_description', 'You can add up to %s photos', $max_photo), $max_photo)?></p>
                                <span class="btn fileinput-button btn-success" type="button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span><?php echo $this->translate("Add Photos")?></span>
                                    <!-- The file input field used as target for the file upload widget -->
                                    <input class="section-fileupload" id="certification-fileupload" type="file" accept="image/*" name="files[]" multiple>
                                </span>
                                <button type="button" class="btn btn-danger delete" onclick="clearList(this)">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    <span><?php echo $this->translate("Clear List")?></span>
                                </button>
                                <br /> 
                                <br />  
                                
                                <!-- The global progress bar -->
                                <div id="progress" class="progress" style="display: none; width: 400px; float:left">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                                <span id="progress-percent"></span>
                                <!-- The container for the uploaded files -->
                                <?php $upload_photos = ($item) ? Engine_Api::_()->getItemTable('ynresume_photo')->getPhotosItem($item) : array();?>
                                <?php $photos = array()?>
                                <ul id="files" class="files" style="<?php if (count($upload_photos)) echo 'display:block;'?>">
                                <?php foreach ($upload_photos as $photo) : ?>
                                    <li class="file-success">
                                        <a class="file-remove" onclick="removeFile(this, <?php echo $photo->getIdentity()?>)" href="javascript:;" title="<?php echo $this->translate('Click to remove this photo.')?>">Remove</a>
                                        <span class="file-name"><?php echo $photo->title?></span>
                                    </li>
                                <?php $photos[] = $photo->getIdentity();?>
                                <?php endforeach;?>
                                </ul>
                                <input type="hidden" class="upload-photos" name="photo_ids" value="<?php echo implode(' ', $photos)?>"/>
                            </div>
                        </div>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div class="ynresume-form-buttons ynresume-form-wrapper">
                    <label></label>
                    <div class="ynresume-form-input">
                        <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                        <button type="button" class="ynresume-cancel-btn"><?php echo $this->translate('Cancel')?></button>
                        <?php if ($edit && isset($params['item_id'])) : ?>
                        <?php echo $this->translate(' or ')?>
                        <a href="javascript:void(0);" class="ynresume-remove-btn"><?php echo $this->translate('Remove this')?></a>
                        <?php endif; ?>
                    </div>
                </div>            
            </form>
        </div>
        <script type="text/javascript">
            //add event for form
            window.addEvent('domready', function() {
                var current = $('certification-current');
                if (current) {
                    if (current.checked) {
                        $('certification-end_month').hide();
                        $('certification-end_year').hide();
                    }
                    else {
                        $('certification-present').hide();
                    }
                    
                    current.addEvent('change', function() {
                        if (current.checked) {
                            $('certification-end_month').hide();
                            $('certification-end_year').hide();
                            $('certification-present').show();
                        }
                        else {
                            $('certification-present').hide();
                            $('certification-end_month').show();
                            $('certification-end_year').show();
                        }
                    });
                }
            });
        </script>
        
        <?php if ($business_enable && count($businesses)) : ?>
        <script type="text/javascript">
        //script for autocomplete business
            var business_id = [];
            var business_name = [];
            <?php foreach ($businesses as $business) : ?>
            business_id.push(<?php echo $business->getIdentity();?>);
            <?php $business_title = $business->getTitle(); ?>
            business_name.push('<?php echo htmlspecialchars("$business_title", ENT_QUOTES);?>');
            <?php endforeach; ?>
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
                                var index = business_name.indexOf(selected.textContent);
                                document.getElementById('certification-business_id').set('value', business_id[index]);
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
                
            
            window.addEvent('domready', function() {
                var input = document.getElementById('certification-company');
                if (input) {
                    input.addEvent('change', function(){
                        $('certification-business_id').set('value', 0);
                    });
                    
                    new Awesomplete(input, {
                        list: business_name,
                        sort: function() {
                            return;
                        }
                    });
                } 
            });
        </script>
        
        <?php endif;?>
	<?php endif;?>
<?php endif;?>
    <?php if (count($certification) > 0) : ?>
    <div id="ynresume-section-list-certification" class="ynresume-section-list">
        <ul id="certification-list" class="section-list">
        <?php foreach ($certification as $item) :?>
        <li class="section-item" id="certification-<?php echo $item->getIdentity()?>">
            <div class="sub-section-item">
                <?php 
                    $start_month = ($item->start_month) ? $item->start_month : 1;
                    $start_date = date_create($item->start_year.'-'.$start_month.'-'.'1');
                    if ($item->start_month) {
                        $start_time = date_format($start_date, 'M Y');
                    }
                    else {
                        $start_time = date_format($start_date, 'Y');
                    }
                    if ($item->end_year) {
                        $end_month = ($item->end_month) ? $item->end_month : 1;
                        $end_date = date_create($item->end_year.'-'.$end_month.'-'.'1');
                        if ($item->end_month) {
                            $end_time = date_format($end_date, 'M Y');
                        }
                        else {
                            $end_time = date_format($end_date, 'Y');
                        }
                    }
                    else {
                        $end_date = date_create();
                        $end_time = $this->translate('Present');
                    }
                    $diff = date_diff($start_date, $end_date);
                ?>

                <div class="certification-time section-subline hidden visible_theme_4 span-background-theme-4">                
                    <span class="start-time"><?php echo $start_time?></span>
                    <span>-</span>
                    <span class="end-time"><?php echo $end_time?></span>
                    <?php $period = $diff->format('%y')*12 + $diff->format('%m');?>
                    <span class="period">(<?php echo $this->translate(array('month_diff','%s months',$period),$period)?>)</span>
                </div>

                <div class="section-title"><?php echo strip_tags($item->name); ?></div>
            
                <?php if ($item->authority):?>
                <div class="certification-authority">
                	<span class="label"><?php echo $this->translate("Certification Authority");?></span>
                	<span><?php echo $item->authority; ?></span>
                </div>
                <?php endif;?>

                <?php if ($item->license_number):?>
                <div class="certification-license_number">
                    <span class="label"><?php echo $this->translate("License Number");?></span>
                    <span><?php echo $item->license_number; ?></span>
                </div>
                <?php endif;?>
                
                <?php if ($item->url):?>
                <div class="certification-url">
                	<span class="label"><?php echo $this->translate("Certification URL");?></span>
                    <span><a href="<?php echo Engine_Api::_() -> ynresume() -> addScheme($item -> url); ?>"><?php echo $item -> url ?></a></span>
                </div>
                <?php endif;?>
                
                
                <div class="section-item-calendar">
                    <div>
                        <?php if ($item->start_month) : ?>
                            <span class="month"><?php echo date_format($start_date, 'M');?></span>
                        <?php endif; ?>
                        <span class="year"><?php echo date_format($start_date, 'Y');?></span>
                    </div>

                    <div>
                        <?php if ($item->start_month) : ?>
                            <span class="month"><?php echo date_format($end_date, 'M');?></span>
                        <?php endif; ?>
                        <span class="year"><?php echo date_format($end_date, 'Y');?></span>
                    </div>
                </div>
                <div class="certification-time section-subline hidden_theme_4">                
                    <span class="start-time"><?php echo $start_time?></span>
                    <span>-</span>
                    <span class="end-time"><?php echo $end_time?></span>
                    <?php $period = $diff->format('%y')*12 + $diff->format('%m');?>
                    <span class="period">(<?php echo $this->translate(array('month_diff','%s months',$period),$period)?>)</span>
                </div>
        	</div>

    		<?php $photos = Engine_Api::_()->getItemTable('ynresume_photo')->getPhotosItem($item);?>
            <?php if (count($photos)) :?>
        	<?php $count = 0;?>
            <div class="section-photos">
                <ul class="photo-lists">
                    <?php foreach ($photos as $photo) : ?>
	                <li class="<?php if ($count >= 3) echo 'view-more'?>">
	                    <div class="photo-item">
                            <a href="<?php echo $photo->getPhotoUrl();?>" data-lightbox-gallery="gallery" class="photoSpan" style="background-image: url('<?php echo $photo->getPhotoUrl('thumb.main');?>');"></a>
                            <div class="photo-title"><?php echo $photo->getTitle();?></div>
                        </div>
	                </li>
		            <?php $count++;?>
		            <?php endforeach;?>
                </ul>
                <?php if (count($photos) > 3) : ?>
                <div class="ynresume-photos-showmore">
                    <a href="javascript:void(0)" class="view-more-photos"><i class="fa fa-arrow-down"></i> <?php echo $this->translate('View more')?></a>
                    <a href="javascript:void(0)" class="view-less-photos"><i class="fa fa-arrow-up"></i> <?php echo $this->translate('View less')?></a>
                </div>
            <?php endif; ?>
            </div>
            <?php endif;?>
    		
            <?php if ($manage) : ?>
            <a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a>
            <?php endif; ?>
        </li>
        <?php endforeach;?>    
        </ul>
    </div>    
    <?php endif; ?>
    </div>    
<?php endif; ?>