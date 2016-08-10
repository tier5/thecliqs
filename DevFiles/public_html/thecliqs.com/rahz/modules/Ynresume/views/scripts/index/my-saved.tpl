 <?php if( count($this->paginator) > 0 ): ?>
    <div class="ynresume-resume-count">
        <?php 
        echo '<span id="ynresume_result_count_value">'.$this->total.'</span>';
        echo '<span id="ynresume_result_count_label">'.$this->translate(array('ynresume_count', 'Resumes Found', $this->total),$this->total).'</span>';
        ?>
    </div>
    <div class="ynresume-user-item-list">
    <?php foreach($this -> paginator as $resume) :?>
        <li>
            <div class="ynresume-user-item">
                <div class="ynresume-user-item-thumb">
                    <?php echo Engine_Api::_()->ynresume()->getPhotoSpan($resume, 'thumb.main'); ?>
                </div>                  
                <div class="ynresume-user-item-main">
                    <div class="ynresume-user-item-title">
                        <?php echo $this -> htmlLink($resume -> getHref(), $resume -> getTitle());?>
                    </div>
                    <div class="ynresume-user-item-subline">
                        <span><i class="fa fa-briefcase"></i><?php echo $resume->getJobTitle();?></span>
                        <span><i class="fa fa-building"></i><?php echo $resume->getCompany();?></span>
                    </div>
                    <div class="ynresume-user-item-subline">
                        <span class="ynresume-user-item-location"><?php echo $resume->location?></span>
                        <span class="ynresume-user-item-position">
                            <?php $industry = $resume->getIndustry();?>
                            <?php echo ($industry) ? $industry->getTitle() : $this->translate('Unknown Industry');?>
                        </span>
                    </div>
                    <div class="ynresume-user-item-description ynresume-description">
                        <div class="ynresume-user-item-description-title"><?php echo $this->translate('Summary') ?></div>
                        <div class="ynresume-description"><?php $industry = $resume->getSummary();?></div>
                        <div class="ynresume-user-item-description-action">
                            <?php $owner = $resume->getOwner()?>
                            <?php echo $this->htmlLink($owner->getHref(), $this->translate('View Profile'), array())?>
                            <?php echo $this->htmlLink($resume->getHref(), $this->translate('View Resume'), array())?>
                        </div>
                    </div>
                </div>
                <div class="ynresume-user-item-footer">
                    
                    <div><a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose-message', 'to' => $resume -> getOwner() -> getIdentity()), 'ynresume_general', true);?>"><span><i class="fa fa-envelope"></i> <span><?php echo $this -> translate('Message');?></span></span></a></div>
                    
                    <div class="ynresume-user-item-action">
                        <span><i class="fa fa-ellipsis-h"></i> <span><?php $this -> translate('More');?></span></span>
                        <ul class="ynresume-show-options">
                            <li><a href="<?php echo $this -> url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><i class="fa fa-file-pdf-o"></i><?php echo $this -> translate('Save to PDF');?></a></li>
                            <?php if($resume -> hasSkill()):?>
                                <li><a href="<?php echo $resume -> getHref();?>/endorse/1"><i class="fa fa-check-square-o"></i><?php echo $this -> translate('Endorse');?></a></li>
                            <?php endif;?>
                            <li><a href="<?php echo $this -> url(array('action' => 'give', 'receiver_id' => $resume -> getOwner() -> getIdentity()), 'ynresume_recommend', true);?>"><i class="fa fa-comments-o"></i><?php echo $this -> translate('Recommend');?></a></li>
                            <li><a id="ynresume_save_<?php echo $resume -> getIdentity();?>" onclick="saveResume(this, '<?php echo $resume -> getIdentity() ;?>');" href="javascript:;"><i class="fa fa-floppy-o"></i><?php echo $this -> translate('Unsave Resume');?></a></li>
                            <li><a href="javascript:void(0)" class="ynresume_favourite_<?php echo $resume -> getIdentity();?>" onclick="favouriteResume('<?php echo $resume -> getIdentity() ;?>');"><i class="fa fa-star-o"></i><?php echo ($resume -> hasFavourited()) ? $this -> translate('Unfavourite') : $this -> translate('Favourite');?></a></li>
                            <li><a href="<?php echo $resume->getOwner()->getHref();?>"><i class="fa fa-eye"></i><?php echo $this -> translate('View Profile');?></a></li>
                        </ul>       
                    </div>                      
                </div>
            </div>
        </li>
    <?php endforeach;?> 
    </div>
    <script text="text/javascript">
        (function($,$$){
          var events;
          var check = function(e){
            var target = $(e.target);
            var parents = target.getParents();
            events.each(function(item){
              var element = item.element;
              if (element != target && !parents.contains(element))
                item.fn.call(element, e);
            });
          };
          Element.Events.outerClick = {
            onAdd: function(fn){
              if(!events) {
                document.addEvent('click', check);
                events = [];
              }
              events.push({element: this, fn: fn});
            },
            onRemove: function(fn){
              events = events.filter(function(item){
                return item.element != this || item.fn != fn;
              }, this);
              if (!events.length) {
                document.removeEvent('click', check);
                events = null;
              }
            }
          };
        })(document.id,$$);

        $$('.ynresume-user-item-list .ynresume-user-item-action').addEvent('outerClick', function(){
            if ( this.hasClass('open-submenu') ) {
                this.removeClass('open-submenu');   
            }
        });

        $$('.ynresume-user-item-list .ynresume-user-item-action').addEvent('click', function(){
            if ( this.hasClass('open-submenu') ) {
                this.removeClass('open-submenu');   
            } else {
                $$('.open-submenu').removeClass('open-submenu');
                this.addClass('open-submenu');
            }
            var global_content = this.getParent('#global_content');
            var y_position = this.getPosition(global_content).y;
            var p_height = global_content.getHeight();
            var c_height = this.getChildren('.ynresume-show-options').getHeight();
            if(p_height - y_position < c_height)
            {
                this.addClass('ynresume-show-option-reverse');
            }
        });
    </script>
<?php else: ?>
    <br />
    <div class="tip">
        <span>
            <?php echo $this->translate('There are no resumes found yet.') ?>
        </span>
    </div>
<?php endif; ?>

<div id='paginator'>
    <?php if( $this->paginator->count() > 1 ): ?>
         <?php echo $this->paginationControl($this->paginator, null, null, array(
                'pageAsQuery' => true,
                'query' => $this->formValues,
              )); ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
	function favouriteResume(id)
	{
		new Request.JSON({
	        url: '<?php echo $this->url(array('action' => 'favourite'), 'ynresume_general', true); ?>',
	        method: 'post',
	        data : {
	        	format: 'json',
	            'id' : id
	        },
	        onComplete: function(responseJSON, responseText) {
	            if (responseJSON.save == '1')
	            {
	            	$$(".ynresume_favourite_"+id).each(function(el) {
	            		var icon = (el.hasClass('button')) ? '' : '<i class="fa fa-star-o"></i>';
	            		el.set("html", icon+'<?php echo $this -> translate("Unfavourite");?>');
	            	});
	            }
	            else
	            {
	            	$$(".ynresume_favourite_"+id).each(function(el) {
	            		var icon = (el.hasClass('button')) ? '' : '<i class="fa fa-star-o"></i>';
	            		el.set("html", icon+'<?php echo $this -> translate("Favourite");?>');
	            	});
	            }
	            
	        }
	    }).send();
	}
	
    function saveResume(obj, id)
    {
        new Request.JSON({
            url: '<?php echo $this->url(array('action' => 'save'), 'ynresume_general', true); ?>',
            method: 'post',
            data : {
                format: 'json',
                'id' : id
            },
            onComplete: function(responseJSON, responseText) {
                if (responseJSON.save == '0') {
                    var content = obj.getParent('.layout_core_content');
                    obj.getParent('.ynresume-user-item').destroy();
                    var count = parseInt($('ynresume_result_count_value').get('text'));
                    $('ynresume_result_count_value').set('text', count-1);
                    if ((count - 1) == 1) {
                        $('ynresume_result_count_label').set('text', '<?php echo $this->translate('Resume Found')?>');
                    }
                    if ((count - 1) == 0) {
                        console.log(obj);
                        var div = '<div class="tip"><span><?php echo $this->translate('There are no resumes found yet.') ?></span></div>'
                        content.innerHTML = div;
                    }
                }
                
            }
        }).send();
    }
    
</script>