<script type="text/javascript">
    en4.core.runonce.add(function(){
        new Sortables('compare-businesses', {
            contrain: false,
            clone: true,
            handle: 'li',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                new Request.JSON({
                    url: '<?php echo $this->url(array('controller'=>'compare','action'=>'sort'), 'ynbusinesspages_compare') ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                        'category_id': '<?php echo $this->category->getIdentity()?>'
                    },
                }).send();
            }
        });
    });
</script>

<div id="ynbusinesspages-compare-page">
    <div id="compare-header" class="ynbusinesspages-clearfix">
        <?php if ($this->prevCategory !== false) :?>
        <?php $url = $this->url(array('category_id' => $this->prevCategory), 'ynbusinesspages_compare', true);?>
        <div id="prev-button-div">
            <button id="prev-button" onclick="window.location = '<?php echo $url;?>'"><?php echo $this->translate('Prev')?></button>
        </div>
        <?php endif; ?>
        <?php if ($this->nextCategory !== false) :?>
        <?php $url = $this->url(array('category_id' => $this->nextCategory), 'ynbusinesspages_compare', true);?>
        <div id="next-button-div">
            <button id="next-button" onclick="window.location = '<?php echo $url;?>'"><?php echo $this->translate('Next')?></button>
        </div>
        <?php endif; ?>
        <div id="select-category-div">
            <?php $categories = Engine_Api::_()->ynbusinesspages()->getAvailableCategories();?>
            <select id="select-category" onchange="changeCategory(this)">
                <?php foreach ($categories as $category) : ?>
                <option id="option-category_<?php echo $category->getIdentity()?>" <?php if ($category->getIdentity() == $this->category->getIdentity()) echo 'selected';?> value="<?php echo $category->getIdentity()?>"><?php echo $category->getTitle().' ('.Engine_Api::_()->ynbusinesspages()->countComparebusinessesOfCategory($category->getIdentity()).')'?></option>
                <?php endforeach;?>
            </select>
            <?php 
            if (!Engine_Api::_()->ynbusinesspages()->countComparebusinessesOfCategory($this->category->getIdentity()))
                Engine_Api::_()->ynbusinesspages()->removeCompareCategory($this->category->getIdentity()); 
            ?>
        </div>
    </div>
    
    <div id="compare-main">
        <?php if (count($this->businesses)) : ?>
        <div id="compare-fields-title-div">
            <ul id="compare-fields-title">
                <li class="business-photo_title"></li>
                <?php foreach ($this->availableCompareFields as $field) : ?>
                    <?php if ($field->type == 'customField') :?>
                    <?php $customFields = $this->category->getCustomFieldsList();
                    foreach ($customFields as $customField) :
                    ?>
                    <li class="business-customField_<?php echo $customField->field_id?>"><?php echo $customField->label?></li>
                    <?php endforeach;?>
                    <?php else : ?>
                    <li class="business-<?php echo $field->type?>"><?php echo $field->title?></li>
                    <?php endif; ?>
                <?php endforeach;?>
            </ul>
        </div>

        <div id="compare-fields-content-div">
            <ul id="compare-businesses">
            <?php foreach ($this->businesses as $business) :?>
                <li id="compare-business_<?php echo $business->getIdentity()?>">
                    <div class="delete">
                        <a href="javascript:void(0)" onclick="deleteComparebusiness2(this, <?php echo $business->getIdentity();?>, <?php echo $this->category->getIdentity();?>)"><i class="fa fa-times"></i></a>
                    </div>
                    <ul class="compare-values">
                        <li class="business-photo_title">
                            <div class="business-photo">
                                <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
                            </div>
                            
                            <div class="business-title">
                                <?php echo $this->htmlLink($business->getHref(), $business->getTitle())?>
                            </div>
                        </li>
                        
                        <?php foreach ($this->availableCompareFields as $fields) : ?>
                        <?php
                            if (in_array($fields->type, array('rating', 'memberCount', 'followerCount', 'review', 'contact', 'address', 'operatingHour', 'customField', 'shortDescription'))) {
                                $method = 'renderBusiness'.ucfirst($fields->type);
                                echo Engine_Api::_()->ynbusinesspages()->$method($business->getIdentity());
                            }
                        ?>
                        <?php if ($fields->type == 'header') :?>
                        <li class="business-header"></li>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </ul>
                </li>
            <?php endforeach; ?>
            </ul>            
        </div>
        <?php else : ?>
            <?php echo $this->translate('No businesses for comparison')?>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    function changeCategory(obj) {
        var url = '<?php echo $this->url(array('action' => 'index'), 'ynbusinesspages_compare', true)?>';
        window.location = url+'/index/category_id/'+obj.get('value');
    }
    
    function deleteComparebusiness2(obj, id, category_id) {
        var url = '<?php echo $this->url(array('action' => 'remove-business'),'ynbusinesspages_compare', true)?>';
        new Request.JSON({
            url: url,
            method: 'post',
            data: {
                'id': id,
                'category_id' : category_id,
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.status) {
                    obj.getParent('li').destroy();
                    var text = $('option-category_'+category_id).get('text');
                    var newText = text.replace(/\((\d)\)$/, "("+responseJSON.count+")");
                    $('option-category_'+category_id).set('text', newText);

                    autoHeightContent();
                }
                if(responseJSON.count == '0')
                {
                	window.location.replace("<?php echo $this -> url(array(), 'ynbusinesspages_general' , true );?>");
                }
            }
        }).send();        
    };

    function autoHeightContent() {
        $$('#compare-fields-title > li').each(function(item, index) {
            item.erase('style');
        });

        $$('#compare-businesses > li').each(function(item, index) {

            item.getElement('.compare-values').getElements('li').each(function(item, index){
                item.erase('style');               
            });    
        });

        $$('#compare-fields-title > li').each(function(item, index) {
            var index_div = index;
            var max_height = item.getSize().y;
            
            $$('#compare-businesses > li').each(function(item, index) {
                item.getElement('.compare-values').getElements('li').each(function(item, index){
                    if ( index == index_div) {
                        if (max_height < item.getSize().y ) {
                            max_height = item.getSize().y;
                        }
                    }               
                });    
            });

            item.setStyle('height', max_height);
            $$('#compare-businesses > li').each(function(item, index) {

                item.getElement('.compare-values').getElements('li').each(function(item, index){
                    if (index == index_div) {
                        item.setStyle('height', max_height);
                    }               
                });    
            });
        });
    }


    window.onload = function(e){ 
        $('compare-businesses').setStyle('width', 220*$$('#compare-businesses > li').length );   

        autoHeightContent();
    }
</script>