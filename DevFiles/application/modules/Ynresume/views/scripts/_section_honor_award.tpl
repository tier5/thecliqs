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
    $month = array('Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    
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
$awards = $resume->getAllAwards();
if (count($awards) <= 0 && $manage) {
    $create = true;
}
?>
<?php if (count($awards) > 0 || (!$hide && ($create || $edit))) : ?>
    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add Honors and Awards')?></a>
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
        <div id="ynresume-section-form-honor_award" class="ynresume-section-form">
            <form rel="honor_award" class="section-form" enctype="multipart/form-data">
                <?php $item = null;?>
                <p class="error"></p>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php $item = Engine_Api::_()->getItem('ynresume_award', $params['item_id']);?>
                <input type="hidden" name="item_id" class="item_id" id="honor_award-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
                <?php endif; ?>
                <div id="honor_award-title-wrapper" class="ynresume-form-wrapper">
                    <label for="honor_award-title"><?php echo $this->translate('*Title')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="honor_award-title" name="title" value="<?php if ($item) echo $item->title?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="honor_award-occupation-wrapper" class="ynresume-form-wrapper">                    
                    <label><?php echo $this->translate('Occupation')?></label>
                    <div class="ynresume-form-input">
                        <?php $occupation_item = ($item) ? $item->occupation_type.'-'.$item->occupation_id : null;?>
                        <select name="occupation" id="honor_award-occupation" value="<?php if ($occupation_item) echo $occupation_item?>">
                            <?php $occupations = Engine_Api::_()->ynresume()->getOccupations($resume->user_id);?>
                            <option value="0"><?php echo $this->translate('Choose...')?></option>
                            <?php foreach ($occupations as $occupation) : ?>
                            <option value="<?php echo $occupation['id']?>" <?php if ($occupation_item && $occupation_item == $occupation['id']) echo 'selected';?>><?php echo $occupation['title']?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="honor_award-title-wrapper" class="ynresume-form-wrapper">
                    <label for="honor_award-issuer"><?php echo $this->translate('Issuer')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="honor_award-issuer" name="issuer" value="<?php if ($item) echo $item->issuer?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="honor_award-date-wrapper" class="ynresume-form-wrapper">
                    <label><?php echo $this->translate('Date')?></label>
                    <div class="ynresume-form-input ynresume-form-input-2item">
                        <select name="date_month" id="honor_award-date_month" value="<?php if ($item) echo $item->date_month?>">
                            <?php foreach ($month as $key => $value) : ?>
                            <option value="<?php echo $key?>" <?php if ($item && $item->date_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php $curYear = intval(date("Y"));?>
                        <select name="date_year" id="honor_award-date_year" value="<?php if ($item) echo $item->date_year?>">
                            <option value="0000"><?php echo $this->translate('Year')?></option>
                            <?php for ($i = $curYear; $i >= 1900; $i--) : ?>
                            <option value="<?php echo $i?>" <?php if ($item && $item->date_year == $i) echo 'selected';?>><?php echo $this->translate($i)?></option>
                            <?php endfor; ?>
                        </select>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="honor_award-photos-wrapper" class="ynresume-form-wrapper upload-photos-wrapper">
                    <label><?php echo $this->translate('Add photos')?></label>
                    <div id="file-wrapper" class="ynresume-form-input">
                        <div class="form-element">
                            <!-- The fileinput-button span is used to style the file input field as button -->
                            <p class="element-description"><?php echo $this->translate(array('add_photo_description', 'You can add up to %s photos', $max_photo), $max_photo)?></p>
                            <span class="btn fileinput-button btn-success" type="button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span><?php echo $this->translate("Add Photos")?></span>
                                <!-- The file input field used as target for the file upload widget -->
                                <input class="section-fileupload" id="honor_award-fileupload" type="file" accept="image/*" name="files[]" multiple>
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
                        <a href="javascript:void(0);" class="ynresume-remove-btn"><?php echo $this->translate('Remove Honor or Award')?></a>
                        <?php endif; ?>
                    </div>                    
                </div>            
            </form>
        </div>
        <?php endif;?>
    <?php endif;?>
    <?php if (count($awards) > 0) : ?>
    <div id="ynresume-section-list-honor_award" class="ynresume-section-list">
        <ul id="honor_award-list" class="section-list">
        <?php foreach ($awards as $item) :?>
        <li class="section-item" id="honor_award-<?php echo $item->getIdentity()?>">
            <div class="sub-section-item">
                
                <?php if ($item->date_year) : ?>
                <div class="honor_award-date section-subline hidden visible_theme_4 span-background-theme-4">
                    <?php if ($item->date_month) : ?>
                    <span class="honor_award-date_month"><?php echo $this->translate($month[$item->date_month])?></span>
                    <?php endif;?>
                    <span class="honor_award-date_year"><?php echo $this->translate($item->date_year)?></span>
                </div>
                <?php endif;?>

                <div class="honor_award-title section-title"><?php echo strip_tags($item->title); ?></div>

                <?php if ($item->occupation_type && $item->occupation_id) : ?>
                <?php $occupation = Engine_Api::_()->ynresume()->getPosition2($item->occupation_type, $item->occupation_id);?>
                <div class="honor_award-occupation">
                    <span class="occupation-position label"><?php echo $occupation[0]?></span>
                    <span class="occupation-title"><?php echo $occupation[1]?></span>
                </div>
                <?php endif;?>

                <?php if ($item->issuer) : ?>
                <div class="honor_award-issuer section-head-title"><?php echo $item->issuer?></div>
                <?php endif;?>

                <?php if ($item->date_year) : ?>
                <div class="section-item-calendar">
                    <div>
                        <?php if ($item->date_month) : ?>
                            <span class="month"><?php echo substr($month[$item->date_month], 0, 3); ?></span>
                        <?php endif; ?>
                        <span class="year"><?php echo $this->translate($item->date_year)?></span>
                    </div>
                </div>
                <div class="honor_award-date section-subline hidden_theme_4">
                    <?php if ($item->date_month) : ?>
                    <span class="honor_award-date_month"><?php echo $this->translate($month[$item->date_month])?></span>
                    <?php endif;?>
                    <span class="honor_award-date_year"><?php echo $this->translate($item->date_year)?></span>
                </div>
                <?php endif;?>
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