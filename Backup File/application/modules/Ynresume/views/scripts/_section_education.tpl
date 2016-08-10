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
$education = $resume->getAllEducation();
if (count($education) <= 0 && $manage) {
    $create = true;
}
?>
<?php if (count($education) > 0 || (!$hide && ($create || $edit))) : ?>
    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add education')?></a>
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
        <div id="ynresume-section-form-education" class="ynresume-section-form">
            <form rel="education" class="section-form" enctype="multipart/form-data">
                <?php $item = null;?>
                <p class="error"></p>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php $item = Engine_Api::_()->getItem('ynresume_education', $params['item_id']);?>
                <input type="hidden" name="item_id" class="item_id" id="education-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
                <?php endif; ?>
                <div id="education-title-wrapper" class="ynresume-form-wrapper">
                    <label for="education-title"><?php echo $this->translate('*School')?></label>
                    <div class="ynresume-form-input">                        
                        <input type="text" id="education-title" name="title" value="<?php if ($item) echo $item->title?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                <div id="education-year_attended-wrapper" class="ynresume-form-wrapper">                
                    <label><?php echo $this->translate('Year Attended')?></label>
                    <div class="ynresume-form-input ynresume-form-input-2item">
                        <?php $curYear = intval(date("Y"));?>
                        <?php $maxYear = intval(date("Y")) + 10;?>
                        <select name="attend_from" id="education-attend_from" value="<?php if ($item) echo $item->attend_from?>">
                            <option value="0000"><?php echo $this->translate('-')?></option>
                            <?php for ($i = $curYear; $i >= 1900; $i--) : ?>
                            <option value="<?php echo $i?>" <?php if ($item && $item->attend_from == $i) echo 'selected';?>><?php echo $this->translate($i)?></option>
                            <?php endfor; ?>
                        </select>
                         - 
                        <select name="attend_to" id="education-attend_to" value="<?php if ($item) echo $item->attend_to?>">
                            <option value="0000"><?php echo $this->translate('-')?></option>
                            <?php for ($i = $maxYear; $i >= 1900; $i--) : ?>
                            <option value="<?php echo $i?>" <?php if ($item && $item->attend_to == $i) echo 'selected';?>><?php echo $this->translate($i)?></option>
                            <?php endfor; ?>
                        </select>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="education-study_field-wrapper" class="ynresume-form-wrapper">                
                    <label for="education-study_field"><?php echo $this->translate('Field of Study')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="education-study_field" name="study_field" value="<?php if ($item) echo $item->study_field?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="education-degree_id-wrapper" class="ynresume-form-wrapper">                
                    <label><?php echo $this->translate('Degree')?></label>
                    <div class="ynresume-form-input">
                        <select name="degree_id" id="education-degree_id" value="<?php if ($item) echo $item->degree_id?>">
                            <option value="0"><?php echo $this->translate('-')?></option>
                            <?php $degrees = Engine_Api::_()->getDbTable('degrees', 'ynresume')->getAllDegress(); ?>
                            <?php foreach ($degrees as $degree) : ?>
                            <option value="<?php echo $degree->degree_id?>" <?php if ($item && $item->degree_id == $degree->degree_id) echo 'selected';?>><?php echo $this->translate($degree->name)?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="education-grade-wrapper" class="ynresume-form-wrapper">                
                    <label for="education-grade"><?php echo $this->translate('Grade')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="education-grade" name="grade" value="<?php if ($item) echo $item->grade?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="education-activity-wrapper" class="ynresume-form-wrapper">                
                    <label for="education-activity"><?php echo $this->translate('Activities and Societies')?></label>
                    <div class="ynresume-form-input">
                        <textarea id="education-activity" name="activity"/><?php if ($item) echo $item->activity?></textarea>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="education-description-wrapper" class="ynresume-form-wrapper">                
                    <label for="education-description"><?php echo $this->translate('Description')?></label>
                    <div class="ynresume-form-input">
                        <textarea id="education-description" name="description"/><?php if ($item) echo $item->description?></textarea>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="education-photos-wrapper" class="ynresume-form-wrapper upload-photos-wrapper">                
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
                                    <input class="section-fileupload" id="education-fileupload" type="file" accept="image/*" name="files[]" multiple>
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
                                        <a class="file-remove" onclick="removeFile(this, <?php echo $photo->getIdentity()?>)" href="javascript:;" title="<?php echo $this->translate('Click to remove this entry.')?>">Remove</a>
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
                        <a href="javascript:void(0);" class="ynresume-remove-btn"><?php echo $this->translate('Remove school')?></a>
                        <?php endif; ?>
                    </div>                
                </div>            
            </form>
        </div>
        <?php endif;?>
    <?php endif;?>
    <?php if (count($education) > 0) : ?>
    <div id="ynresume-section-list-education" class="ynresume-section-list">
        <ul id="education-list" class="section-list">
        <?php foreach ($education as $item) :?>
        <li class="section-item" id="education-<?php echo $item->getIdentity()?>">
            <div class="sub-section-item">
                <?php if ($item->attend_from >= 1900 || $item->attend_to >= 1900) : ?>
                <div class="education-time section-subline hidden visible_theme_4 span-background-theme-4">
                    <?php if ($item->attend_from >= 1900) : ?>
                    <span class="from"><?php echo $item->attend_from?></span>
                    <?php endif;?>

                    <?php if ($item->attend_from >= 1900 && $item->attend_to >= 1900) : ?>
                    <span>-</span>
                    <?php endif;?>

                    <?php if ($item->attend_to >= 1900) : ?>
                    <span class="to"><?php echo $item->attend_to?></span>
                    <?php endif;?>
                </div>
                <?php endif;?>

                <div>
                <div class="education-title section-title inline_theme_4"><?php echo strip_tags($item->title); ?></div>
    
                <?php if ($item->study_field) : ?>
                    <div class="education-study_field section-head-title inline_theme_4"><i class="fa fa-graduation-cap"></i> <?php echo $item->study_field?></div>
                <?php endif;?>
                </div>
    
                <?php if ($item->attend_from >= 1900 || $item->attend_to >= 1900) : ?>
                <div class="section-item-calendar">
                    <?php if ($item->attend_from >= 1900) : ?>
                    <div><span class="from year"><?php echo $item->attend_from?></span></div>
                    <?php endif;?>

                    <?php if ($item->attend_to >= 1900) : ?>
                    <div><span class="to year"><?php echo $item->attend_to?></span></div>
                    <?php endif;?>
                </div>
                <div class="education-time section-subline hidden_theme_4">
                    <?php if ($item->attend_from >= 1900) : ?>
                    <span class="from"><?php echo $item->attend_from?></span>
                    <?php endif;?>

                    <?php if ($item->attend_from >= 1900 && $item->attend_to >= 1900) : ?>
                    <span>-</span>
                    <?php endif;?>

                    <?php if ($item->attend_to >= 1900) : ?>
                    <span class="to"><?php echo $item->attend_to?></span>
                    <?php endif;?>
                </div>
                <?php endif;?>
    
                <?php if ($item->degree_id) : ?>
                <div class="education-degree">
                    <span class="label"><?php echo $this->translate('Degree')?></span>
                    <span class="value">
                    <?php
                        $degree = Engine_Api::_()->getDbTable('degrees', 'ynresume')->getDegreeById($item->degree_id);
                        echo ($degree) ? $this->translate($degree->name) : $this->translate('Unknown');
                    ?>
                    </span>
                </div>
                <?php endif;?>
    
                <?php if ($item->grade) : ?>
                <div class="education-grade">
                    <span class="label"><?php echo $this->translate('Grade')?></span>
                    <span class="value"><?php echo $item->grade?></span>
                </div>
                <?php endif;?>
    
                <?php if ($item->activity) : ?>
                <div class="education-activity">
                    <span class="label"><?php echo $this->translate('Activities & Societies')?></span>
                    <span class="value"><?php echo $item->activity?></span>
                </div>
                <?php endif;?>
            </div>
    
            <?php if ($item->description) : ?>
            <div class="education-description section-description"><?php echo $item->description?></div>
            <?php endif;?>
    
            <?php $recommendations = Engine_Api::_()->ynresume()->getShowRecommendationsOfOccupation('education', $item->getIdentity(), $resume->user_id)?>
            <?php if (count($recommendations)) :?>
            <div class="occupation-recommendations">
                <div class="recommendation-label">
                    <a href="javascript:void(0)" class="show-hide-recommendations-btn"><?php echo $this->translate(array('recommendation_count', '%s recommendations', count($recommendations)), count($recommendations))?></a>
                </div>
                <ul class="recomendation-list">
                    <?php foreach ($recommendations as $recommendation) : ?>
                    <li class="recommendation-item">
                        <?php $giver = $recommendation->getGiver();?>
                        <div class="giver-avatar"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'))?></div>
                        <div class="giver-title"><?php echo $this->htmlLink($giver->getHref(), $giver->getTitle())?></div>
                        
                        <div class="giver-headline">
                        <?php if (isset($giver->headline) && !empty($giver->headline)) : ?>
                            <?php if ($giver->title) :?><span><?php echo $giver->title?></span><?php endif; ?>
                            <?php if ($giver->company) :?><span><i class="fa fa-building"></i> <?php echo $giver->company?></span><?php endif; ?>
                            <?php endif;?>
                        </div>
                        
                        <div class="recommendation-content">
                            <?php echo $this->viewMore($recommendation->content, 255, 3*1027);?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif;?>
            
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