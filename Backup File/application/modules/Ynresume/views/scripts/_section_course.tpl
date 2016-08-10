<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = (isset($params['view']) && $params['view']) ? Engine_Api::_()->core()->getSubject() : $this->resume;
    $courseTbl = Engine_Api::_()->getItemTable('ynresume_course');
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
?>
<?php
$course = $resume->getAllCourse();
if (count($course) <= 0 && $manage) {
    $create = true;
}
$orphanCourses = array();
foreach($course as $c)
{
    if ($c->associated_id == '0')
    {
        $orphanCourses[] = $c;
    }
}
?>

<?php if (count($course) > 0 || (!$hide && ($create || $edit))) : ?>
	<?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>
    
    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add Course')?></a>
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
    	    <div id="ynresume-section-form-course" class="ynresume-section-form">
    	        <form rel="course" class="section-form" enctype="multipart/form-data">
    	            <p class="error"></p>
    	            <?php $item = null;?>
    	            <?php if ($edit && isset($params['item_id'])) : ?>
    		            <?php $item = Engine_Api::_()->getItem('ynresume_course', $params['item_id']);?>
    		            <input type="hidden" name="item_id" class="item_id" id="course-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
    	            <?php endif; ?>
    	            
    	            <div id="course-name-wrapper" class="ynresume-form-wrapper">
    	                <label for="course-name"><?php echo $this->translate('*Course Name')?></label>
                        <div class="ynresume-form-input">
    	                   <input type="text" id="course-name" name="name" value="<?php if ($item) echo $item->name?>"/>
                           <p class="error"></p>
                        </div>
    	            </div>
    	            
    	            <div id="course-number-wrapper" class="ynresume-form-wrapper">
    	                <label for="course-number"><?php echo $this->translate('Number')?></label>
                        <div class="ynresume-form-input">
                            <input type="text" id="course-number" name="number" value="<?php if ($item) echo $item->number?>"/>
                            <p class="error"></p>
                        </div>
    	            </div>
    	            
    	            <div id="course-associated-wrapper" class="ynresume-form-wrapper">
    	                <label><?php echo $this->translate('Associated with')?></label>
                        <div class="ynresume-form-input">
        	                <select name="associated" id="course-associated" value="<?php if ($item) echo $item->associated_type?>">
        	                    <?php $associatedArr = $resume -> getCourseAssociatedAssoc();?>
        	                    <?php foreach ($associatedArr as $key => $value) : ?>
        	                    	<?php
        	                    	    if ($item->associated_id)
        	                    	        $associated = $item->associated_type . "::" . $item->associated_id;
                                        else
                                            $associated = 'ynresume_education::0';
                                    ?>
        	                    	<option value="<?php echo $key?>" <?php if ($associated == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
        	                    <?php endforeach; ?>
        	                </select>
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
        <?php endif;?>
    <?php endif;?>
    
    <?php if (count($course) > 0) : ?>
    <div id="ynresume-section-list-course" class="ynresume-section-list">
        <ul id="course-list" class="section-list">
        <?php 
        	$education = $resume -> getAllEducation();
        	$experience = $resume -> getAllExperience();
        ?>
        
        <?php foreach ($education as $edu) :?>
            <?php $courses = $courseTbl -> getCoursesByEducation($edu);?>
            <?php if (count($courses)) : ?>
        	<li class="section-item">
                <div class="sub-section-item">
        	    	<div class="section-title"><?php echo $edu -> title;?></div>
        	    	<?php if (count($courses)):?>
        	    		<?php foreach ($courses as $item) :?>
        				    <div class="section-course-item section-item" id="course-<?php echo $item->getIdentity()?>">
        				    	<span><?php echo $item -> name;?></span> | 
        				    	<span><?php echo $item -> number;?></span>
        				        <?php if ($manage) : ?>
        				            <span><a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a></span>
        				        <?php endif; ?>
        				    </div>
        			    <?php endforeach;?>   
        	    	<?php endif;?>
                </div>
        	</li>
            <?php endif; ?>
        <?php endforeach;?>
        
        <?php foreach ($experience as $exp) :?>
            <?php $courses = $courseTbl -> getCoursesByExperience($exp);?>
            <?php if (count($courses)) : ?>
        	<li class="section-item">
                <div class="sub-section-item">
        	    	<div class="section-title"><?php echo $exp -> title;?></div>
        	    	<?php if (count($courses)):?>
        	    		<?php foreach ($courses as $item) :?>
        				    <div class="section-course-item section-item" id="course-<?php echo $item->getIdentity()?>">
        				        <span><?php echo $item -> name;?></span> | 
        				    	<span><?php echo $item -> number;?></span>
        				        <?php if ($manage) : ?>
        				        <span><a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a></span>
        				        <?php endif; ?>
        				    </div>
        			    <?php endforeach;?>   
        	    	<?php endif;?>
                </div>
        	</li>
            <?php endif; ?>
        <?php endforeach;?>

        <?php if (count($orphanCourses)): ?>
            <li class="section-item">
                <div class="sub-section-item">
                    <div class="section-title"><?php echo $this -> translate("Others");?></div>
                    <?php foreach ($orphanCourses as $item) :?>
                    <div class="section-course-item section-item" id="course-<?php echo $item->getIdentity()?>">
                        <span><?php echo $item -> name;?></span> |
                        <span><?php echo $item -> number;?></span>
                        <?php if ($manage) : ?>
                        <span><a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach;?>
                </div>
            </li>
        <?php endif; ?>
        </ul>
    </div>    
    <?php endif; ?>
    </div>    
<?php endif; ?>