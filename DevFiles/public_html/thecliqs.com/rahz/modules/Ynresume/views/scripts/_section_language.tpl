<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = (isset($params['view']) && $params['view']) ? Engine_Api::_()->core()->getSubject() : $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
?>
<?php
$language = $resume->getAllLanguage();
if (count($language) <= 0 && $manage) {
    $create = true;
}
?>
<?php if (count($language) > 0 || (!$hide && ($create || $edit))) : ?>
	<?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>

    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add Language')?></a>
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
    	    <div id="ynresume-section-form-language" class="ynresume-section-form">
    	        <form rel="language" class="section-form" enctype="multipart/form-data">
    	            <p class="error"></p>
    	            <?php $item = null;?>
    	            <?php if ($edit && isset($params['item_id'])) : ?>
    		            <?php $item = Engine_Api::_()->getItem('ynresume_language', $params['item_id']);?>
    		            <input type="hidden" name="item_id" class="item_id" id="language-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
    	            <?php endif; ?>
    	            
    	            <div id="language-name-wrapper" class="ynresume-form-wrapper">	                
    	                <label for="language-name"><?php echo $this->translate('*Language')?></label>
    	                <div class="ynresume-form-input">    	                	
    	                	<input type="text" id="language-name" name="name" value="<?php if ($item) echo $item->name?>"/>
                            <p class="error"></p>
    	                </div>
    	            </div>
    	            
    	            <div id="language-proficiency-wrapper" class="ynresume-form-wrapper">	                
    	                <label><?php echo $this->translate('Proficiency')?></label>
    	                <div class="ynresume-form-input">
    						
    		                <select name="proficiency" id="language-proficiency" value="<?php if ($item) echo $item->proficiency?>">
    		                    <?php $proficiencyArr = array(
    				                    '' => 'Choose...', 
    				                    'elementary' => $this -> translate('Elementary'), 
    				                    'limited working' => $this -> translate('Limited Working'), 
    				                    'professional working' => $this -> translate('Professional Working'), 
    				                    'fill working' => $this -> translate('Fill Working'), 
    				                    'native or bilingual' => $this -> translate('Native or Bilingual')
    		                    )?>
    		                    <?php foreach ($proficiencyArr as $key => $value) : ?>
    		                    	<option value="<?php echo $key?>" <?php if ($item && $item->proficiency == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
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
    
    <?php if (count($language) > 0) : ?>
    <div id="ynresume-section-list-language" class="ynresume-section-list">
        <ul id="language-list" class="section-list">
        <?php foreach ($language as $item) :?>
        <li>
            <div class="section-item" id="language-<?php echo $item->getIdentity()?>">
                <?php if ($manage) : ?>
                    <a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a>
                <?php endif; ?>

                <div class="sub-section-item">
                    <div class="section-title"><?php echo $item->name?></div>
                    <?php 
                    		$proficiencyArr = array(
                                '' => 'Choose...', 
                                'elementary' => $this -> translate('Elementary'), 
                                'limited working' => $this -> translate('Limited Working'), 
                                'professional working' => $this -> translate('Professional Working'), 
                                'fill working' => $this -> translate('Fill Working'), 
                                'native or bilingual' => $this -> translate('Native or Bilingual')
            				)
            		?>
                    <?php if ($item->proficiency):?>
                    <div class="language-proficiency">
                    	<span class="label"><?php echo $this->translate("Proficiency");?></span>
                    	<span><?php echo $proficiencyArr[$item->proficiency]; ?></span>
                    </div>
                    <?php endif;?>
                </div>
            </div>                
        </li>
        <?php endforeach;?>    
        </ul>
    </div>    
    <?php endif; ?>
    </div>    
<?php endif; ?>