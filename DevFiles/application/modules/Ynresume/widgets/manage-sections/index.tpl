<?php 
    $this -> headScript() -> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynresume/externals/scripts/validator.js');
 	$this -> headScript() -> appendFile($this -> layout() -> staticBaseUrl . 'externals/tinymce/tinymce.min.js');
    $this -> headMeta()-> appendName('viewport', 'width=device-width, initial-scale=1.0');
    $staticBaseUrl = $this->layout()->staticBaseUrl;

    $this -> headLink() -> prependStylesheet($staticBaseUrl . 'application/modules/Ynresume/externals/styles/bootstrap.css')
                        -> prependStylesheet($staticBaseUrl . 'application/modules/Ynresume/externals/styles/upload_photo/jquery.fileupload.css')
                        -> appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/styles/magnific-popup.css');
        
    $this -> headScript() -> appendFile($staticBaseUrl . 'application/modules/Ynresume/externals/scripts/jquery.min.js')    
                          -> appendScript('jQuery.noConflict();')
                          -> appendFile($staticBaseUrl . 'application/modules/Ynresume/externals/scripts/js/vendor/jquery.ui.widget.js')  
                          -> appendFile($staticBaseUrl . 'application/modules/Ynresume/externals/scripts/js/jquery.iframe-transport.js')
                          -> appendFile($staticBaseUrl . 'application/modules/Ynresume/externals/scripts/js/jquery.fileupload.js')    
                          -> appendFile($staticBaseUrl . 'application/modules/Ynresume/externals/scripts/bootstrap.min.js')
                          -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/jquery.magnific-popup.js');

    $viewer = Engine_Api::_()->user()->getViewer();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max_photo = $permissionsTable->getAllowed('ynresume_resume', $viewer->level_id, 'max_photo');
    if ($max_photo == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $viewer->level_id)
        ->where('type = ?', 'ynresume_resume')
        ->where('name = ?', 'max_photo'));
        if ($row) {
            $max_photo = $row->value;
        }
    }
?>
<script type="text/javascript">
jQuery.noConflict();
var active = <?php echo $this->resume->active?>;

    en4.core.runonce.add(function(){
        new Sortables('sections-content-items', {
            contrain: false,
            clone: true,
            handle: 'span.ynresume-section-move',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                new Request.JSON({
                    url: '<?php echo $this->url(array('action'=>'sort', 'resume_id'=>$this->resume->getIdentity()), 'ynresume_specific', true) ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                    }
                }).send();
            }
        });
    });
    
    en4.core.language.addData({'email_valid': ' <?php echo $this->translate('email_valid')?>'});
    en4.core.language.addData({'require_valid': ' <?php echo $this->translate('require_valid')?>'});
    en4.core.language.addData({'require-select_valid': ' <?php echo $this->translate('require-select_valid')?>'});
    en4.core.language.addData({'year_valid': ' <?php echo $this->translate('year_valid')?>'});
    en4.core.language.addData({'year-before_valid': ' <?php echo $this->translate('year-before_valid')?>'});
    en4.core.language.addData({'month-year-before_valid': ' <?php echo $this->translate('year-before_valid')?>'});
    en4.core.language.addData({'month-year-before-current_valid': ' <?php echo $this->translate('month-year-before-current_valid')?>'});

    var confirm = false;
    var type = '';
    var item_id = 0;
    //view more sections function
    function showMoreSections() {
        $$('#sections-list li.section-item').removeClass('less');
        $$('#sections-list li.section-item').addClass('more');
        $('show-less-btn').show();
        $('show-more-btn').hide();
    }
    
    //view less sections function
    function showLessSections() {
        $$('#sections-list li.section-item').removeClass('more');
        $$('#sections-list li.section-item').addClass('less');
        $('show-less-btn').hide();
        $('show-more-btn').show();
    }
    
    function goToSectionDetail(section) {
        if (active == 1) {
            if ($('sections-content-item_'+section)) {
                $('sections-content-item_'+section).setFocus();
            }
            else {
            	var params = {};
            	params.create = true;
            	renderSection(section, params);
            }
        }
        else {
            alert('<?php echo $this->translate('Please enter and save general information first!')?>');
        }
    }
    
    function validForm(section) {
        var args = [];
        switch (section) {
            case 'experience':
                args.push(['experience-title', 'require', '<?php echo $this->translate('Position')?>']);
                args.push(['experience-company', 'require', '<?php echo $this->translate('Company')?>']);
                args.push(['experience-start_year', 'require', '<?php echo $this->translate('Start Date')?>']);
                args.push(['experience-start_year', 'year', '<?php echo $this->translate('Start Date')?>']);
                if (!$('experience-current').checked) {
                    args.push(['experience-end_year', 'require', '<?php echo $this->translate('End Date')?>']);
                    args.push(['experience-end_year', 'year', '<?php echo $this->translate('End Date')?>']);
                    args.push(['experience-start_year', 'month-year-before', 'experience-start_month', 'experience-end_year', 'experience-end_month', '<?php echo $this->translate('Start Date')?>', '<?php echo $this->translate('End Date')?>']);
                }
                break;
                
            case 'education':
                args.push(['education-title', 'require', '<?php echo $this->translate('School')?>']);
                args.push(['education-attend_from', 'year-before', 'education-attend_to', '<?php echo $this->translate('Start Year')?>', '<?php echo $this->translate('End Year')?>']);
                break;
                
            case 'contact':
                args.push(['contact-email', 'email', '<?php echo $this->translate('Email')?>']);
                args.push(['contact-phone', 'require', '<?php echo $this->translate('Phone')?>']);
                break;

            case 'language':
                args.push(['language-name', 'require', '<?php echo $this->translate('Language')?>']);
                //args.push(['language-proficiency', 'require', '<?php echo $this->translate('Proficiency')?>']);
                break;

            case 'publication':
                args.push(['publication-title', 'require', '<?php echo $this->translate('Publication Title')?>']);
                break;

            case 'education':
                args.push(['education-title', 'require', '<?php echo $this->translate('School')?>']);
                break;

            case 'certification':
                args.push(['certification-name', 'require', '<?php echo $this->translate('Certification Name')?>']);
                args.push(['certification-start_year', 'require', '<?php echo $this->translate('Start Date')?>']);
                args.push(['certification-start_year', 'year', '<?php echo $this->translate('Start Date')?>']);
                if (!$('certification-current').checked) {
                    args.push(['certification-end_year', 'require', '<?php echo $this->translate('End Date')?>']);
                    args.push(['certification-end_year', 'year', '<?php echo $this->translate('End Date')?>']);
                    args.push(['certification-start_year', 'month-year-before', 'certification-start_month', 'certification-end_year', 'certification-end_month', '<?php echo $this->translate('Start Date')?>', '<?php echo $this->translate('End Date')?>']);
                }
                break;

            case 'project':
                args.push(['project-name', 'require', '<?php echo $this->translate('Project Name')?>']);
                args.push(['project-start_year', 'require', '<?php echo $this->translate('Start Date')?>']);
                args.push(['project-start_year', 'year', '<?php echo $this->translate('Start Date')?>']);
                if (!$('project-current').checked) {
                    args.push(['project-end_year', 'require', '<?php echo $this->translate('End Date')?>']);
                    args.push(['project-end_year', 'year', '<?php echo $this->translate('End Date')?>']);
                    args.push(['project-start_year', 'month-year-before', 'project-start_month', 'project-end_year', 'project-end_month', '<?php echo $this->translate('Start Date')?>', '<?php echo $this->translate('End Date')?>']);
                }
                break;

            case 'course':
                args.push(['course-name', 'require', '<?php echo $this->translate('Course Name')?>']);
                break;

            case 'honor_award':
                args.push(['honor_award-title', 'require', '<?php echo $this->translate('Title')?>']);
                if ($('honor_award-date_month').get('value') != '0') {
                    args.push(['honor_award-date_year', 'require-select', '<?php echo $this->translate('Year')?>']);
                    if ($('honor_award-date_year').get('value') != '0000') {
                        args.push(['honor_award-date_year', 'month-year-before-current', 'honor_award-date_month']);
                    }
                }
                break;    
        }
        if ($('ynresume-section-form-'+section)) {
            $('ynresume-section-form-'+section).getElements('.error').each(function(el) {
                el.empty();    
            });
            validator.init(args);
            return validator.execute();
        }
    }
    
    
    function renderSection(type, params) {
        if ($('sections-content-item_'+type)) {
            var content = $('sections-content-item_'+type).getElement('.ynresume-section-content');
            var loading = $('sections-content-item_'+type).getElement('.ynresume_loading');
            if (loading) {
                loading.show();
            }
            if (content) {
                content.hide();
            }
        }
        var resume_id = <?php echo $this->resume->getIdentity();?>;
        var url = '<?php echo $this->url(array('action' => 'render-section', 'resume_id' => $this->resume->getIdentity()), 'ynresume_specific', true)?>';
        var data = {};
        data.type = type;
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
                    if (['experience', 'education'].indexOf(type) != -1) {
                        reloadCoverWidget();
                    }
                    $('sections-content-item_'+type).setFocus();
                    
                    var content = $('sections-content-item_'+type).getElement('.ynresume-section');
                    var form = content.getElement('.ynresume-section-form');
                    var button = content.getElement('.ynresume-add-btn');
                    if(form && button) {
                    	button.removeEvents('click');
                        button.addClass('disable');
                    }
                    else if (button && button.hasClass('disable')) {
                    	button.removeClass('disable');
	                    button.removeEvents('click');
	                    button.addEvent('click', function(e){
	                        var type = this.get('rel');
	                        var params = {};
	                        params.create = true;
	                        renderSection(type, params);
	                    });
                    }
                }
            }
        });
        request.send();
    }
    
    function removeItemConfirm() {
        if (confirm == true && type != '' && item_id > 0) {
            var params = {};
                params.remove = true;
                params.item_id = item_id;
                renderSection(type, params);
                confirm = false;
                type = '';
                item_id = 0;
        }
    }
    
    function reloadCoverWidget() {
        var options = $('ynresume_cover_wrapper');
        if (options) {
            var params = {};
            params['format'] = 'html';
            params['isEdit'] =  '0';
            var request = new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/name/ynresume.my-resume-cover',
                data : params,
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    var parent = options.getParent();
                    Elements.from(responseHTML).replaces(parent);
                    eval(responseJavaScript);
                }
            });
            request.send();
        }
    }
    
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }
    
    function deletePhoto(photo_id)  {
        new Request.JSON({
            url: '<?php echo $this->url(array('action'=>'delete-photo', 'resume_id'=>$this->resume->getIdentity()), 'ynresume_specific', true) ?>',
            data: {
                'format': 'json',
                'photo_id': photo_id
            }
        }).send();
    }
    function removeFile(obj, photo_id) {
        var parent_div = obj.getParent('.ynresume-form-wrapper');
        obj.getParent('li').destroy();
        var upload_photos = parent_div.getElements('.upload-photos')[0];
        if(photo_id) {
             upload_photos.value = upload_photos.value.replace(photo_id, '');
        }
        if(parent_div.getElements('.files')[0].getChildren().length <= 0) {
            parent_div.getElements('.files')[0].style.display = 'none';
            parent_div.getElements('#progress')[0].style.display = 'none';
            parent_div.getElements('#progress-percent')[0].innerHTML = '';
            upload_photos.value = '';
        }
        return false;
    }
    
    function clearList(obj) {
        var parent_div = obj.getParent('.ynresume-form-wrapper');
        parent_div.getElements('.files')[0].style.display = 'none';
        parent_div.getElements('.files')[0].set('text', '');
        parent_div.getElements('.upload-photos')[0].value = '';
        parent_div.getElements('#progress')[0].style.display = 'none';
        parent_div.getElements('#progress-percent')[0].innerHTML = '';
        return false;
    }
    function addEventToForm() {
    	
    	$$('.ynresume-group-cancel-btn').each(function(el) {
    	    el.removeEvents('click');
            el.addEvent('click', function(e){
                this.getParent('.section-form').innerHTML = "";
            });
        });
    	
        $$('.section-form').each(function(el) {
            el.removeEvents('submit');
            el.addEvent('submit', function(e){
                e.preventDefault();
                var type = this.get('rel');
                var params = this.toQueryString().parseQueryString();
                params.save = true;
                var valid = validForm(type);
                 if (valid)
                     renderSection(type, params);
            });
        });
        
        $$('.edit-section-btn').each(function(el) {
        	if(el.hasClass('group-custom-field-edit-btn'))
        	{
        		return;
        	}
            el.removeEvents('click');
            el.addEvent('click', function(e){
                var item = this.getParent('.section-item');
                var id = item.get('id');
                var arr = id.split('-');
                var type = arr[0];
                var item_id = arr[1];
                var params = {};
                params.edit = true;
                params.item_id = item_id;
                renderSection(type, params);
            });
        });
        
        $$('.ynresume-add-btn').each(function(el) {
            el.removeEvents('click');
            el.addEvent('click', function(e){
                var type = this.get('rel');
                var params = {};
                params.create = true;
                renderSection(type, params);
            });
        });

        $$('.ynresume-cancel-skill-btn').each(function(el) {
            el.removeEvents('click');
            el.addEvent('click', function(e){
                if ($('ynresume_skills_flag'))
                {
                    if ($('ynresume_skills_flag').value == '1')
                    {
                        renderSection('skill', {});
                    }
                    else
                    {
                        $('sections-content-item_skill').dispose();
                    }
                }
            });
        });
        
        $$('.ynresume-cancel-btn').each(function(el) {
            el.removeEvents('click');
            el.addEvent('click', function(e){
                var form = this.getParent('.section-form');
                form.hide();
                if (form.getElements('.upload-photos-wrapper').length > 0) {
                    
                    var value = form.getElements('.upload-photos')[0].get('value');
                    var ids = value.split(' ');
                    for (var i = 0; i < ids.length; i++) {
                        if (!isNaN(ids[i])) {
                            deletePhoto(ids[i]);
                        }
                    }
                }
                var div = this.getParent('.ynresume-section');
                
                var button = div.getElement('.ynresume-add-btn');
                if(button)
                {
	                button.removeClass('disable');
	                button.removeEvents('click');
	                button.addEvent('click', function(e){
	                    var type = this.get('rel');
	                    var params = {};
	                    params.create = true;
	                    renderSection(type, params);
	                });
                }
                if (div.getElements('.ynresume-section-list').length <= 0) {
                    this.getParent('.sections-content-item').destroy();
                }
            });
        });
        
        $$('.ynresume-remove-btn').each(function(el) {
            el.removeEvents('click');
            el.addEvent('click', function(e){
                var item = this.getParent('.ynresume-section-form').getElements('.item_id')[0];
                var id = item.get('id');
                var arr = id.split('-');
                type = arr[0];
                item_id = arr[1];
                
                var div = new Element('div', {
                   'class': 'ynresume-confirm-popup' 
                });
                var text = '';
                if (type == 'experience' || type == 'education') {
                    text = '<?php echo $this->translate('Do you want to remove this? All its recommendations will also be removed.')?>';
                }
                else {
                    text = '<?php echo $this->translate('Do you want to remove this?')?>';
                }
                var p = new Element('p', {
                    'class': 'ynresume-confirm-message',
                    text: text,
                });
                var button = new Element('button', {
                    'class': 'ynfeedback-confirm-button',
                    text: '<?php echo $this->translate('Remove')?>',
                    onclick: 'parent.Smoothbox.close();confirm=true;removeItemConfirm();'
                    
                });
                var span = new Element('span', {
                   text: '<?php echo $this->translate(' or ')?>' 
                });
                
                var cancel = new Element('a', {
                    text: '<?php echo $this->translate('Cancel')?>',
                    onclick: 'parent.Smoothbox.close();',
                    href: 'javascript:void(0)'
                });
                
                div.grab(p);
                div.grab(button);
                div.grab(span);
                div.grab(cancel);
                Smoothbox.open(div);
            });
        });
        
        new Sortables('sections-content-items', {
            contrain: false,
            clone: true,
            handle: 'span.ynresume-section-move',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                new Request.JSON({
                    url: '<?php echo $this->url(array('action'=>'sort', 'resume_id'=>$this->resume->getIdentity()), 'ynresume_specific', true) ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                    }
                }).send();
            }
        });
        
        $$('.show-hide-recommendations-btn').removeEvents('click');
        $$('.show-hide-recommendations-btn').addEvent('click', function() {
            var list = this.getParent('.occupation-recommendations').getElement('.recomendation-list');
            if (list) list.toggle();
        });
        
        $$('.show-hide-photos-btn').removeEvents('click');
        $$('.show-hide-photos-btn').addEvent('click', function() {
            var list = this.getParent('.section-photos').getElement('.photo-lists');
            if (list) list.toggle();
        });
        
        $$('.ynresume-section-collapse').removeEvents('click');
        $$('.ynresume-section-collapse').addEvent('click', function() {
            var li = this.getParent('.sections-content-item');
            if (li) {
                var div = li.getElements('.ynresume-section-content')[0];

                this.toggleClass('section-collapsed');
                div.toggle();
            }
        });
        //for upload photos
        var url = '<?php echo $this->url(array('action'=>'upload-photos', 'resume_id'=>$this->resume->getIdentity()), 'ynresume_specific', true)?>';
        $$('.section-fileupload').each(function(el) {
            var div_parent = el.getParent('.ynresume-form-wrapper');
            var id = el.get('id');
            jQuery('#'+id).fileupload({
                url: url,
                dataType: 'json',
                done: function (e, data) {
                    var files_div = div_parent.getElements('.files')[0];
                    files_div.style.display = 'block';
                    jQuery.each(data.result.files, function (index, file) {
                        var text = "";
                        var ele = jQuery('<li/>');
                        if(file.status) {
                            var count = div_parent.getElements('li.file-success').length;
                            if (count >= <?php echo $max_photo?>) {
                                text = '<a class="file-remove" onclick = "removeFile(this, 0)" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
                                if(file.name)
                                    text += '<span class="file-name">' + file.name + '</span>';
                                text += '<span class="file-info"><span><?php echo $this->translate('Number of photos reached limit.')?></span></span>';
                                ele.html(text).appendTo(files_div);
                                deletePhoto(file.photo_id);
                            }
                            else {
                                text = '<a class="file-remove" onclick = "removeFile(this,' + file.photo_id + ')" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
                                text += '<span class="file-name">' + file.name + '</span>';
                                ele.addClass('file-success');
                                ele.html(text).appendTo(files_div);
                                var upload_photos = div_parent.getElements('.upload-photos')[0];
                                upload_photos.value = upload_photos.value + ' ' + file.photo_id;
                            }
                        }
                        else {
                            text = '<a class="file-remove" onclick = "removeFile(this, 0)" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
                            if(file.name)
                                text += '<span class="file-name">' + file.name + '</span>';
                            text += '<span class="file-info"><span>' + file.error +'</span></span>';
                            ele.html(text).appendTo(files_div);
                        }
                    });
                },
                progressall: function (e, data) {
                    var progress_div = div_parent.getElements('#progress')[0];
                    progress_div.style.display = 'block';
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    progress_div.getElements('.progress-bar')[0].setStyle('width', progress + '%');
                    var progress_percent = div_parent.getElements('#progress-percent')[0];
                    progress_percent.set('text', progress + '%');
                }
            }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');        
        });
        
        //for show more/show less photos
        $$('.view-more-photos').removeEvents('click');
        $$('.view-more-photos').addEvent('click', function() {
            var div = this.getParent('.section-photos');
            if (div) {
            	div.getElements('.photo-lists li.view-more').setStyle('display','inline-block');
            	div.getElements('.view-more-photos').hide();
        		div.getElements('.view-less-photos').show();
            }
        });
        
        $$('.view-less-photos').removeEvents('click');
        $$('.view-less-photos').addEvent('click', function() {
            var div = this.getParent('.section-photos');
            if (div) {
            	div.getElements('.photo-lists li.view-more').hide();
            	div.getElements('.view-more-photos').show();
        		div.getElements('.view-less-photos').hide();
            }
        });

        jQuery.noConflict();
        jQuery('a[data-lightbox-gallery]').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            closeBtnInside: false,
            fixedContentPos: true,
            mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
            image: {
                verticalFit: true
            },
            zoom: {
                enabled: false,
                duration: 300 // don't foget to change the duration also in CSS
            }
        });
    }
    
    window.addEvent('domready', function() {
        addEventToForm();
    });
    
    Element.implement({
        setFocus: function(index) {
            this.setAttribute('tabIndex',index || 0);
            this.focus();
        }
    });
</script>
<div id="ynresume-manage-sections-box">
    <ul id="sections-list">
    <?php 
        $sections = Engine_Api::_()->ynresume()->getAllSections();
        if (isset($sections['general_info'])) unset($sections['general_info']);
        if (isset($sections['recommendation'])) unset($sections['recommendation']);
        $headings = Engine_Api::_()->getApi('fields', 'ynresume')->getHeading();
        $sectionsAddBtn = Engine_Api::_()->ynresume()->getSectionsAddBtn();
        $sectionsIconClass = Engine_Api::_()->ynresume()->getSectionsIconClass();
    ?>
    <?php foreach ($sections as $key => $section) : ?>
        <li class="section-item more">
            <div class="section-item-content">
                <div class="section-item-icon <?php echo 'section-'.$key?>">
                    <i class="<?php echo $sectionsIconClass[$key]?>"></i>
                </div>
                <div class="section-item-title">
                    <?php echo $this->translate($section);?>
                </div>
                <div class="section-item-description">
                    <?php echo $this->translate('YNRESUME_SECTION_'.strtoupper($key).'_DESCRIPTION');?>
                </div>                
            </div>
            <div class="section-item-add_button">
            	<?php $btnLabel = (string) $sectionsAddBtn[$key];?>
            	<?php if($key != 'photo'):?>
                	<a href="javascript:void(0)" onclick="goToSectionDetail('<?php echo $key?>')"><?php echo $this->translate($btnLabel)?></a>
            	<?php else:?>
            		<a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $this -> url(array('action' => 'photo', 'resume_id' => $this -> resume -> getIdentity()), 'ynresume_specific', true);?>')"><?php echo $this->translate($btnLabel)?></a>
            	<?php endif;?>
            </div>
        </li>
    <?php endforeach; ?>

    <?php foreach ($headings as $heading) : ?>
    	<?php
			$resume = new Ynresume_Model_Resume(array());        
        	$type = Engine_Api::_()->getApi('fields','ynresume')->getFieldTypeStr($resume);
        	if(Engine_Api::_()->getApi('fields','ynresume')->checkHasQuestion($type, $heading -> field_id, 1, 1)) :
    	?>
        <li class="section-item more">
            <div class="section-item-content">
                <div class="section-item-icon" style="background-color: <?php echo $heading->color?>">
                    <img src="<?php echo Engine_Api::_() -> ynresume() -> getPhoto($heading -> photo_id, 'thumb.icon');?>"/>
                </div>
                <div class="section-item-title">
                    <?php echo $this->translate($heading->label);?>
                </div>
                <div class="section-item-description">
                    <?php echo $this->translate($heading->description);?>
                </div>                
            </div>
            <div class="section-item-add_button">
                <?php $btnLabel = $this->translate('Add %s', $this->translate($heading->label));?>
                <a  href="javascript:void(0)" onclick="goToSectionDetail('<?php echo 'field_'.$heading->field_id?>')"><?php echo $this->translate($btnLabel)?></a>
            </div>
        </li>
        <?php endif;?>
    <?php endforeach; ?>
    
    </ul>
    <div id="show-btn">
        <a href="javascript:void(0)" id="show-more-btn" onclick="showMoreSections()"><i class="fa fa-arrow-down"></i> <?php echo $this->translate('View More')?></a>
        <a href="javascript:void(0)" id="show-less-btn" onclick="showLessSections()"><i class="fa fa-arrow-up"></i> <?php echo $this->translate('View Less')?></a>
    </div>
</div>

<div id="ynresume-manage-sections-content">
    <ul id="sections-content-items">
    <?php 
    $allSections = Engine_Api::_()->ynresume()->getAllSectionsAndGroups();
    if (isset($allSections['general_info'])) unset($allSections['general_info']);
    $order = $this->resume->getOrder();
    if ($order) {
        $allSections = array_merge(array_flip($order->order), $allSections);
    }
    ?>
    <?php foreach ($allSections as $key => $section): ?>
    	<?php if($key != 'photo') :?>
    		<?php 
        		$content = Engine_Api::_()->ynresume()->renderSection($key, $this->resume, array('hide' => true));
	        ?>
	        
	        <?php if (trim($content)) : ?>
		        <li class="sections-content-item" id="sections-content-item_<?php echo $key?>">
			        <label class="order">
			            <span class="ynresume-section-move"><i class="fa fa-arrows"></i></span>
			            <span class="ynresume-section-collapse"><i class="fa fa-chevron-down"></i></span>
			        </label>
			        <div class="ynresume-section">
			        	<?php echo $content; ?>
			        </div>
		        </li>
	        <?php endif;?>
	    <?php endif;?>   
    <?php endforeach;?>
    </ul>
</div>
