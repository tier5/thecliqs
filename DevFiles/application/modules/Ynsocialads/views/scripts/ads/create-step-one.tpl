<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
window.addEvent('load', function() {
    new Picker.Date($$('.date_picker'), { 
        positionOffset: {x: 5, y: 0}, 
        pickerClass: 'datepicker_dashboard', 
        useFadeInOut: !Browser.ie,
        timePicker: true,
        format: 'db'
    });
});

function toStepOne(obj) {
    var value = obj.value;
    window.open(en4.core.baseUrl + 'socialads/ads/create-step-one/package_id/'+value, '_blank');
}

function updateItem() {
    if ($('internal_module')) {
        var value = $('internal_module').value;
        var url = en4.core.baseUrl+'socialads/ads/get-items/module_id/'+value;
        new Request.JSON({
            url: url,
            onSuccess: function(responseJSON) {
                var items = responseJSON.json;
                var internal_items = $('internal_item');
                internal_items.empty();
                Object.keys(items).forEach(function (key) {
                   var option = new Element('option', {
                       value: key,
                       text: items[key],
                    });
                    internal_items.grab(option);
                });
                updateTitle();
            }
        }).send();
    }
}

function selectInternal() {
    $('internal_div').show();
    $('external_url_div').hide();
    updateTitle();
}

function selectExternal() {
    if ($('internal_div')) {
        $('internal_div').hide();
    }
    $('external_url_div').show();
}

function updateTitle() {
    if ($('internal_type').checked) {
        var selected = $$('#internal_item option:selected');
        if (selected.length > 0) {
            var value = selected[0].get('text');
            $('title').set('value', value.substring(0, 25));
            updateTitleInPreview();
            updateTitleLeft($('title'));
        }
    }
}

function activeDiv(obj) {
    $$('.ad_type_div').removeClass('active');
    obj.getParent('div.ad_type_div').addClass('active');
    
    switch(obj.get('id')) {
        case 'banner_type':
            $('text_div').hide();
            $('message_preview').hide();
            $('choose_placement').show();
            break;
        
        case 'feed_type':
            $('text_div').show();
            $('message_preview').show();
            $('choose_placement').hide();

            break;
            
        default:
            $('text_div').show();
            $('choose_placement').show();
            $('message_preview').show();
    }
}

function updateTitleLeft(obj) {
    var current = obj.value.length;
    var max = obj.get('maxlength');
    $('title_left').set('text', max - current);
}

function updateTextLeft(obj) {
    var current = obj.value.length;
    var max = obj.get('maxlength');
    $('text_left').set('text', max - current);
}

function updateTitleInPreview() {
    var value = $('title').value;
    $('title_preview').set('text', value);
}

function updateMessageInPreview(obj) {
    var value = obj.value;
    $('message_preview').set('text', value);
}

function updateImage(event) {
    var files = event.target.files; // FileList object

    for (var i = 0, f; f = files[i]; i++) {
      if (!f.type.match('image.*')) {
        continue;
      }
      var reader = new FileReader();
      reader.onload = (function(theFile) {
        return function(e) {
          // Render thumbnail.
           $('preview_ad_img').set('src', e.target.result);
        };
      })(f);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f);
    }
}
   
function updatePages(obj) {
    var value = obj.value;
    var package_id = "<?php  echo $this->package->package_id ?>";
    
    var sub_div = obj.getParent('.sub_div');
    var preview = sub_div.getElements('.img_preview')[0];
    preview.set('src', 'application/modules/Ynsocialads/externals/images/widgets/'+value.split('_')[0]+'.png')
    
    var url = en4.core.baseUrl+'socialads/ads/get-pages/package_id/'+package_id+'/placement/'+value;
    new Request.JSON({
        url: url,
        onSuccess: function(responseJSON) {
            var items = responseJSON.json;
            if (!items.length) {
                sub_div.getElements('.pages_div')[0].hide();
            }
            else {
                sub_div.getElements('.pages_div')[0].show();
                var pages = sub_div.getElements('.pages')[0];
                pages.empty();
                for(var i = 0; i < items.length; i++) {
                    var option = new Element('option', {
                       value: items[i]['block_id'],
                       text: items[i]['displayname']
                    });
                    pages.grab(option);
                };
            }
        }
    }).send();
}

function addPlacement(event) {
    
    event.preventDefault();
    var original_placement = $('original_placement');
    var new_placement = original_placement.clone();
    $('placement_list').grab(new_placement);
    var removeDiv = new Element('div', {
       'class': 'remove_link',
    })
    var removeBtn = new Element('a', {
       href: '',
       text: '<?php echo $this->translate('remove');?>',
       onclick: 'removePlacement(this, event)',
       'class': 'buttonlink remove_placement_icon',
    });
    removeDiv.grab(removeBtn);
    new_placement.grab(removeDiv, 'top');
    
    var numOfPlacements = $$('.placement_div').length;
    var numOfAvailablePlacements = $$('#original_placement .placement option').length;
    if (numOfPlacements == numOfAvailablePlacements)
        $('add_placement_div').hide();
}

function removePlacement(obj, event) {
    event.preventDefault();
    var placement = obj.getParent('.placement_div');
    placement.destroy();
    $('add_placement_div').show();
}

function calculatePrice(obj){
    var value = Number.from(obj.value);
    if (value == null)
        return false;
    var price = Number.from('<?php echo (($this->package->price)/($this->package->benefit_amount))?>')*value;
    $('price').set('text', price.toFixed(2)+' <?php echo $this->package->currency?>');
}

function updateCampaign(obj){
    var value = obj.value;
    if (value != '0') {
        $('new_campaign').hide();
    }
    else {
        $('new_campaign').show();
    }
}

function selectSchedule(obj) {
    var value = obj.value;
    if (value == 'specify') {
        $('select_date').show();
    }
    else {
        $('select_date').hide();
    }
}

function updateCountAudiences() {
   var data = {};
   var ageFrom = $('age_from').value;
   if (ageFrom == '0') {
       ageFrom = null;
   }
   var ageTo = $('age_to').value;
   if (ageTo == '0') {
       ageTo = null;
   }
   data['birthdate'] = {'min':ageFrom, 'max':ageTo}
    var gender = $('gender').value;
    if (gender != '0') {
        data['gender'] = gender;
    }
    if ($('cities')) {
        var cities = $('cities').value;
        if (cities != '') {
            data['cities'] = cities;
        }
    }
    if ($('countries')) {
        var countries = [];
        var selected = $('countries').getSelected();
        for (var i=0; i < selected.length; i++) {
            countries[i] = selected[i].value;
        }
        if (countries.length) {
            data['countries'] = countries;
        }
    }
    if ($('interests')) {
        var interests = $('interests').value;
        if (interests != '') {
            data['interests'] = interests;
        }
    }
    var birthday = $('birthday').checked;
    data['birthday'] = birthday;
    
    var networks = [];
    var selected = $('networks').getSelected();
    for (var i=0; i < selected.length; i++) {
        networks[i] = selected[i].value;
    }
    if (networks.length) {
        data['networks'] = networks;
    }
    
    var profile_type = $$('input[name=profile_type]:checked')[0].get('value');
    if (profile_type != '0') {
        data['profile_type'] = profile_type;
    }
    var url = en4.core.baseUrl+'socialads/ads/count-audiences/';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            json: JSON.encode(data)
        },
        onSuccess: function(data) {
                
           $('audiences_count').set('text', data);

        }
    }).send();
}

function placeOrder() {
    $('draft').value = '0';
    checkValidate();
}

function saveAsDraft() {
    $('draft').value = '1';
    checkValidate();
}

function checkValidate() {
    var error = [];
    if ($('external_type').checked) {
        if ($('external_url').value == '') {
            error.push('<?php echo $this->translate('External url must not empty!');?>');
        }
    }
    else {
        if ($('internal_item').value == '') {
            error.push('<?php echo $this->translate('Please select an internal item.');?>');
        }
    }
    if ($('title').value == '') {
        error.push('<?php echo $this->translate('Ad title must not empty!');?>');
    }
    if ($('image').value == '') {
        error.push('<?php echo $this->translate('Ad image must not empty!');?>');
    }
    var adblocks = $$('.pages option:selected');
    
    var placements = $$('.placement option:selected');
    var footer = false;
    for (var i = 0; i < placements.length; i ++) {
        if (placements[i].value == 'footer' ) {
            footer = true;
            break;
        }
    }
    var feed_type = ($('feed_type')) ? $('feed_type').checked : false;
    if (adblocks.length == 0 && !footer && !feed_type) {
        error.push('<?php echo $this->translate('You must select at least 1 placement!');?>');
    }
    if ($('campaign_id').value == '0' && $('campaign_name').value == '') {
        error.push('<?php echo $this->translate('Campaign must be selected!');?>');
    }
    
    if($('benefit_amount')) {
        if ($('benefit_amount').value == '') {
            error.push('<?php echo $this->translate('Goal must not be empty!');?>');
        }
        else if (!isInteger(($('benefit_amount').value))) {
            error.push('<?php echo $this->translate('Goal must be a Integer!');?>');
        }
        else if ($('benefit_amount').value < parseInt('<?php echo $this->package->benefit_amount?>')) {
            error.push('<?php echo $this->translate('Goal must be more than package benefit amount!');?>');
        }
    }
    
    if ($('specify_schedule').checked) {
        var start = new Date($('start_date').value);
        var end = new Date($('end_date').value);
        if (start.getTime() >= end.getTime()) {
            error.push('<?php echo $this->translate('Start Date must lest than End Date!');?>');
        }
    }
    if (error.length > 0) {
        var error_list = $('error_list');
        error_list.empty();
        for (var i = 0; i < error.length; i++) {
            var li = new Element('li', {
                text: ''+error[i],
            });
            error_list.grab(li);
            document.getElementById('error_list').scrollIntoView();
        }
    }
    else {
        $('create-step-one').submit();
    }
}

function onload() {
    var ad_type = $$('.ad_type_div')[0];
    var radio = ad_type.getElements('input[type=radio]')[0];
    radio.set('checked', true);
    activeDiv(radio);
    var placements = $$('.placement');
    placements.each(function(placement) {
        updatePages(placement);
    });
    var numOfPlacements = $$('.placement_div').length;
    var numOfAvailablePlacements = $$('#original_placement .placement option').length;
    if (numOfPlacements == numOfAvailablePlacements)
        $('add_placement_div').hide();
}

function isInteger(obj) {
    return (obj.toString().search(/^-?[0-9]+$/) == 0 );
}
</script>
<div id="error_div">
<ul id="error_list" class="form-errors">    
</ul>
</div>
<div id="chose_package">
<?php if ($this->package) : ?>
<div class="package-item">
    <div class="package-price">
        <span class="price">
        <?php
        if ($this->package->price == 0){
            echo $this->translate('FREE');
        }
        else {
            echo $this->locale() -> toCurrency($this->package->price, $this->package->currency);
        }
        ?>
        </span>            
        <span class="title"><?php echo $this->translate($this->package->title)?></span>            
    </div>
    <div class="package-content">
        <p>
            <b><?php
                if ($this->package->price == 0){
                    echo $this->translate('YNSOCIALADS_FREE');
                }
                else {
                    echo $this->locale() -> toCurrency($this->package->price, $this->package->currency);
                }
                ?></b>
            <?php echo ' '.$this->translate('YNSOCIALADS_FOR').' '?>
            <b><?php echo ($this->package->benefit_amount.' '.strtoupper($this->package->benefit_type).'S')?></b>
        </p>
        <p>
            <b><?php echo $this->translate('YNSOCIALADS_YOU_CAN_ADVERTISE').': '?></b>
            <?php 
                echo implode(', ', $this->package->getAllModuleNames());
                if (count($this->package->getAllModuleNames())) echo  ' '.$this->translate('as').' ';
            
                $strTemp = ucwords(implode(', ', $this->package->allowed_ad_types));
                $arrTemp = explode(', ', $strTemp);
                foreach($arrTemp as &$item)
                {
                    $item = $this->translate($item);
                }
                echo implode(' '.$this->translate('YNSOCIALADS_OR').' ', $arrTemp)
            ?>
        </p>
        <p>
            <b><?php echo $this->translate('YNSOCIALADS_DESCRIPTION').': '?></b>
            <?php echo $this->package->description?>
        </p>
    </div>        
</div>     

<form id="create-step-one" method="post" enctype="multipart/form-data" onsubmit="checkValidate()">
    <div id="item_type" class="form_div">
        <p class="form_label"><?php echo $this->translate('What do you want to advertise?')?></p>
        <div class="form_content">
            <div class="left_content">
                <label class="form_label"><?php echo $this->translate('Item Type')?></label>
                <input type="radio" name="item_type" id="external_type" checked value="external" onclick="selectExternal()"/>
                <label for="external_type"><?php echo $this->translate('External URL')?></label>
                <div id="external_url_div">
                    <label class="form_label" for="external_url"><?php echo $this->translate('URL')?></label>
                    <input type="text" id="external_url" name="url" />
                </div>
                <br />
                <?php if (count($this->modules)) : ?>
                <input type="radio" name="item_type" id="internal_type" onclick="selectInternal()" value="internal" />
                <label for="internal_type"><?php echo $this->translate('Internal Content')?></label>
                <div id="internal_div">
                    <label for="internal_module"><?php echo $this->translate('Select Module:')?></label>
                    <select id="internal_module" name="module" onchange="updateItem()">
                        <?php foreach ($this->modules as $module) : ?>
                        <option value="<?php echo $module['module_id']?>"><?php echo $module['module_title']?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="internal_item"><?php echo $this->translate('Select Item:')?></label>
                    <select id="internal_item" name="item" onchange="updateTitle()">
                    </select>
                </div>
                <br />
                <?php endif; ?>
            </div>
            <div class="right_content">
                <h3><p class="form_label" id="getting_started"><?php echo $this->translate('Getting Started')?></p></h3>
                <p class="description"><?php echo $this->translate('YNSOCIALADS_ADS_CREATE_STEP_ONE_GETTING_STARTED_DESCRIPTION')?></p>
            </div>
        </div>
    </div>
    
    <div id="ad_type" class="form_div">
        <p class="form_label"><?php echo $this->translate('What type of ad you want to create?')?></p>
        
        <div class="form_content">
            <?php foreach ($this->package->allowed_ad_types as $type) : ?>
            <div class="ad_type_div">
                <img src="application/modules/Ynsocialads/externals/images/icons/<?php echo $type?>.png"/>
                <label class="form_label" for="text_type"><?php echo ucwords($this->translate($type))?></label>
                <label for="text_type"><?php echo $this->translate('YNSOCIALADS_ADS_CREATE_STEP_ONE_'.strtoupper($type).'_AD_DESCRIPTION')?></label>
                <input type="radio" value=<?php echo $type?> name="ad_type" id="<?php echo $type?>_type" checked onclick="activeDiv(this)"/>
            </div>
            <?php endforeach;?>
        </div>
    </div>
    
    <div id="preview_and_edit" class="form_div">
        <p class="form_label"><?php echo $this->translate('Preview and edit')?></p>
        <div class="form_content">
            <div class="sub_div">
                <div class="left_content">
                    <div id="title_div" class="form_element">
                        <label class="form_label" for="title"><?php echo $this->translate('Title')?></label>
                        <input type="text" name="name" id="title" maxlength="25" onkeyup="updateTitleLeft(this)" onchange="updateTitleInPreview()"/>
                        <p class="description"><span id="title_left">25</span><span><?php echo ' '.$this->translate('character(s) left')?></span></p>
                    </div>
                    <div id="text_div" class="form_element">
                        <label class="form_label" for="text"><?php echo $this->translate('Text')?></label>
                        <textarea rows="4" name="description" id="text" maxlength="100" onkeyup="updateTextLeft(this)" onchange="updateMessageInPreview(this)"></textarea>
                        <p class="description"><span id="text_left">100</span><span><?php echo $this->translate(' character(s) left')?></span></p>
                    </div>
                    <div id="image_div" class="form_element">
                        <label class="form_label" for="image"><?php echo $this->translate('Image')?></label>
                        <input type="file" name="photo" id="image" accept="image/*" onchange="updateImage(event)">
                    </div>
                </div>
                <div class="right_content">
                    <div class='form_label'><?php echo $this->translate('Preview Your Ad')?> : </div>
                    <div class="ynsocial_ads" >
                        <div class="ynsocial_ads_content">
                            <div class="ynsocial_ads_item">
                                <p id="title_preview" class="ynsocial_ads_cont_title">
                                    <?php echo $this->translate('Ad Title');?>
                                </p>
                                <p class="ynsocial_ads_cont_image">
                                    <img id="preview_ad_img" class="item_photo_ynsocialads_ad" src="application/modules/Ynsocialads/externals/images/ad_plank.png"/>
                                </p>
                                <div class="ynsocial_ads_cont" id="message_preview"><?php echo $this->translate('Ad Message')?></div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <div id="choose_placement">
            <div id="placement_list">
            <div id="original_placement" class="sub_div placement_div">
                <div class="left_content">
                    <div id="placement_div" class="form_element">
                        <label class="form_label" for="placement"><?php echo $this->translate('Display on Placement')?></label>
                        <select class="placement" name="placement[]" onchange="updatePages(this)">
                        <?php foreach ($this->placements as $key => $value) : ?>
                            <option value="<?php echo $key?>"><?php echo $value?></option>
                        <?php endforeach; ?>    
                        </select>
                    </div>
                    <div class="pages_div" class="form_element">
                        <label class="form_label" for="pages"><?php echo $this->translate('Display on Page(s)')?></label>
                        <select multiple class="pages" name="block_id[]" id="pages">
                        <?php foreach ($this->pages as $page) : ?>
                            <option value="<?php echo $page['block_id']?>"><?php echo $page['displayname']?></option>
                        <?php endforeach; ?>
                        </select>
                        <p class="small_description"><?php echo $this->translate('Press Ctrl and click to select multiple types.')?></p>
                    </div>
                </div>
                <div class="right_content">
                    <div class="form_element preview_layout">
                        <label class="form_label"><?php echo $this->translate('Preview Layout')?></label>
                        <img class="img_preview" src="<?php echo $this->preview_layout_src?>"/>
                    </div>
                </div>
            </div>
            </div>

            <div id="add_placement_div" class="add_link">
                <span class=""></span>
                <a href="" class="buttonlink add_placement" onclick="addPlacement(event)"><?php echo $this->translate('Add More Placement')?></a>
            </div>
            </div>
        </div>
    </div>
    
    <div class="form_div">
        <p class="form_label"><?php echo $this->translate('Pricing, Campaign and Schedule')?></p>
        <div class="form_content">
            <div class="price_div">
                <label class="form_label"><?php echo $this->translate('Pricing')?></label>
                <?php if ($this->package->price == 0): ?>
                <p class="description">
                    <span><?php echo $this->translate('This package ')?></span>
                    <span class="bold red"><?php echo $this->translate('YNSOCIALADS_FREE'); ?></span>
                    <span><?php echo $this->translate(' for ') ?></span>
                    <span class="bold"><?php echo ($this->package->benefit_amount.' '.ucwords($this->package->benefit_type).'s.')?></span>
                </p>
                <?php else:?>
                <p class="description">
                    <span><?php echo $this->translate('This package costs ')?></span>
                    <span class="bold red"><?php echo $this->locale() -> toCurrency($this->package->price, $this->package->currency); ?></span>
                    <span><?php echo $this->translate(' for ') ?></span>
                    <span class="bold"><?php echo ($this->package->benefit_amount.' '.$this->translate(ucwords($this->package->benefit_type)).$this->translate('s.'))?></span>
                </p>
                <p class="description"><?php echo $this->translate('Please select your goal.')?></p>
                <input onchange="calculatePrice(this)" type="text" name="benefit_total" id="benefit_amount" value="<?php echo $this->package->benefit_amount?>"/>
                <label class="bold inline" for="benefit_amount"><?php echo $this->translate(ucwords($this->package->benefit_type)).'s'?></label>
                <h3><p class="form_label" id="price_label"><?php echo $this->translate('Price')?></p></h3>
                <h2 id="price" class="bold red"><?php echo $this->locale() -> toCurrency($this->package->price, $this->package->currency); ?></h2>
                <?php endif;?>
            </div>
            <div class="campaign_div">
                <label class="form_label" for="campaign_id"><?php echo $this->translate('Select Campaign')?></label>
                <select id="campaign_id" name="campaign_id" onchange="updateCampaign(this)">
                    <option value="0"><?php echo $this->translate('Create a New Campaign')?></option>
                <?php foreach ($this->campaigns as $campaign) : ?>
                    <option value="<?php echo $campaign['campaign_id']?>"><?php echo $campaign['title']?></option>
                <?php endforeach; ?> 
                </select>
                <div id="new_campaign">
                    <label class="form_label" for="campaign_name"><?php echo $this->translate('Campaign Name')?></label>
                    <input type="text" id="campaign_name" name="campaign_name"/>
                </div>
            </div>
            <div class="schedule_div">
                <label class="form_label" for="campaign_id"><?php echo $this->translate('Schedule')?></label>
                <input type="radio" name="schedule" value="continuously" id="continuously_schedule" onclick="selectSchedule(this)"/>
                <label for="continuously_schedule"><?php echo $this->translate('Run ad continuously when approved')?></label>
                <br />
                <input type="radio" name="schedule" checked id="specify_schedule" onclick="selectSchedule(this)" value="specify" />
                <label for="specify_schedule"><?php echo $this->translate('Specify start and end date')?></label>
                <div id="select_date">
                    <label class="margin_top_10" for="start_date"><?php echo $this->translate('Start: ')?></label>
                    <?php 
                        $now = new Zend_Date();
                        $now->setTimezone($this->timezone);
                        $nextWeek = clone $now;
                        $nextWeek->add(7, 'dd');
                    ?>
                    <input type="text" id="start_date" name="start_date" class="date_picker" value="<?php echo $now->get('YYYY-MM-dd HH:mm:ss')?>"/>
                    <label class="margin_top_10" for="end_date"><?php echo $this->translate('End: ')?></label>
                    <input type="text" id="end_date" name="end_date" class="date_picker" value="<?php echo $nextWeek->get('YYYY-MM-dd HH:mm:ss')?>"/>
                </div>
            </div>
        </div>
    </div>
    <div id="audience_div" class="form_div">
        <p class="form_label"><?php echo $this->translate('Audience')?></p>
        <div class="form_content">
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label"><?php echo $this->translate('Age')?></label>
                </div>
                <div class="content_right">
                    <label for="age_from"><?php echo $this->translate('From')?></label>
                    <select class="small" id="age_from" name="age_from" onchange="updateCountAudiences()">
                        <option value="0"><?php echo $this->translate('Any')?></option>
                        <?php for ($i = 1; $i <= 100; $i++) { ?>
                        <option value="<?php echo $i?>"><?php echo $i?></option>
                        <?php } ?>    
                    </select>
                    <label for="age_to"><?php echo $this->translate('to')?></label>
                    <select class="small" id="age_to" name="age_to" onchange="updateCountAudiences()">
                        <option value="0"><?php echo $this->translate('Any')?></option>
                        <?php for ($i = 1; $i <= 100; $i++) { ?>
                        <option value="<?php echo $i?>"><?php echo $i?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label" for="gender"><?php echo $this->translate('Gender')?></label>
                </div>
                <div class="content_right">
                    <select id="gender" name="gender" onchange="updateCountAudiences()">
                        <option value="0"><?php echo $this->translate('All')?></option>
                        <option value="2"><?php echo $this->translate('Male')?></option>
                        <option value="3"><?php echo $this->translate('Female')?></option>
                    </select>
                </div>
            </div>
            <?php if (in_array('city', $this->targetAvailable)) : ?>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label" for="cities"><?php echo $this->translate('City')?></label>
                </div>
                <div class="content_right">
                    <input type="text" id="cities" name="cities" onchange="updateCountAudiences()"/>
                    <p class="small_description"><?php echo $this->translate('Separate multiple entries with commas.')?></p>
                </div>
            </div>
            <?php endif; ?>
            <?php if (in_array('country', $this->targetAvailable)) : ?>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label" for="countries"><?php echo $this->translate('Country')?></label>
                </div>
                <div class="content_right">
                    <select multiple id="countries" name="countries[]" onchange="updateCountAudiences()">
                    <?php foreach ($this->countries as $key => $value) : ?>
                        <option value="<?php echo $key?>"><?php echo $value?></option>
                    <?php endforeach; ?>     
                    </select>
                    <p class="small_description"><?php echo $this->translate('Press Ctrl and click to select multiple types.')?></p>
                </div>
            </div>
            <?php endif; ?>
            <?php if (in_array('interests', $this->targetAvailable)) : ?>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label" for="interests"><?php echo $this->translate('Interests')?></label>
                </div>
                <div class="content_right">
                    <textarea id="interests" name="interests" rows="7" onchange="updateCountAudiences()"></textarea>
                    <p class="small_description"><?php echo $this->translate('Separate multiple entries with commas.')?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label" for="birthday"><?php echo $this->translate('Birthday')?></label>
                </div>
                <div class="content_right">
                    <input type="checkbox" id="birthday" name="birthday" onchange="updateCountAudiences()" value="1"/>
                    <label for="birthday"><?php echo $this->translate('Target people having their birthday on current date.')?></label>
                </div>
            </div>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label" for="network"><?php echo $this->translate('Select Networks')?></label>
                </div>
                <div class="content_right">
                    <label for="networks"><?php echo $this->translate('Networks enables you to target your ad to users of specific networks. To reach all networks, simply leave the box empty.')?></label>
                    <select multiple id="networks" name="networks[]" onchange="updateCountAudiences()">
                    <?php foreach ($this->networks as $network) : ?>
                        <option value="<?php echo $network->getIdentity()?>"><?php echo $network->getTitle()?></option>
                    <?php endforeach; ?>   
                    </select>
                    <p class="small_description"><?php echo $this->translate('Press Ctrl and click to select multiple types.')?></p>
                </div>
            </div>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label"><?php echo $this->translate('Select Profile Type')?></label>
                </div>
                <div class="content_right">
                    <label class="description"><?php echo $this->translate('Profile types enables you to target your ad to users of specific profile type. Select "All" to reach all profile types.')?></label>
                    <input type="radio" name="profile_type" id="profile_type_0" checked value="0"/>
                    <label for="profile_type_0"><?php echo $this->translate('All')?></label>
                    <?php foreach ($this->profileTypes as $profileType):?>
                    <br />
                    <input type="radio" name="profile_type" id="profile_type_<?php echo $profileType['option_id']?>" value="<?php echo $profileType['option_id']?>"/>
                    <label for="profile_type_<?php echo $profileType['option_id']?>"><?php echo $profileType['label']?></label> 
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="sub_div">
                <div class="label_left">
                    <label class="form_label"><?php echo $this->translate('Public')?></label>
                </div>
                <div class="content_right">
                    <input type="checkbox" name="public" id="public" checked value="1"/>
                    <label><?php echo $this->translate('Allow guests to view this ad.')?></label>
                </div>
            </div>
            <div id="total_audiences" class="sub_div">
                <div class="label_left">
                    <label class="form_label"><?php echo $this->translate('Audiences')?></label>
                </div>
                <div class="content_right">
                    <span id="audiences_count">1000</span><span><?php echo $this->translate(' People')?></span>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="draft" name="draft" value="0"/>
    <div class="buttons">
      <button type="button" onclick="placeOrder()"><?php echo (($this->package->price == 0) ? $this->translate("Publish") : $this->translate("Place Order")) ?></button>
      <button type="button" onclick="saveAsDraft()"><?php echo $this->translate("Save As Draft") ?></button>
       <?php echo $this->translate(' or ') ?>
        <a href="javascript:;" onclick="history.go(-1); return false;"> <?php echo $this->translate('cancel') ?> </a>
      <button type="submit" id="submit_btn" style="display:none"></button>
    </div>
</form>
<?php endif; ?>
<script type="text/javascript">
$$('.core_main_ynsocialads').getParent().addClass('active');
$$('.ynsocialads_main_create_ad').getParent().addClass('active');
updateItem();
updateCountAudiences();
onload();
</script>
