<?php if(count($this->announcements) > 0) :?>  
<ul class="announcements" id="business_announcement">	
	<?php $i_announcement = 1; ?>
	<?php foreach( $this->announcements as $item ): ?>
		<li>
			<div class="business_announcement_item">
				<div class="title">					
					<?php echo $item->title ?>
				</div>
				<div class="content">					
					<span id="ynbusinesspages_announcement_content_<?php echo $item->getIdentity()?>" class="rows4" expand="rows4">
					   <?php echo $item->body;  ?>
					</span>
					<?php 
					$text_hidden = $this->translate('Less');
                    $text_show = $this->translate('More');
                    if(strlen($item->body) > 150):?>
                        &raquo; <a href="javascript:;" onclick="ynbusiness_showhide(<?php echo $item->getIdentity()?>,'<?php echo $text_hidden?>','<?php echo $text_show?>', this)"><?php echo $this->translate('More');?></a>
				    <?php endif;?>
				</div>
				<div class="footer">
					<div class="business_announcement_counter">
						<b><?php echo $i_announcement; ?></b>/<?php echo count($this->announcements);?>
					</div>
					
					<div class='business_announcement_button'>
						<?php echo $this->htmlLink(array(
							'route' => 'ynbusinesspages_announcement',
							'module' => 'ynbusinesspages',
							'action' => 'mark',
							'business_id' => $this->business->getIdentity(),
							'announcement_id' => $item->getIdentity(),
						  ), $this->translate('Mark this read'), array(
							'class' => 'smoothbox',
							'title'=> $this -> translate("Mark this read")
						))?>
					</div>
				</div>
			</div>
			<?php  ?>			
	  	</li>
	<?php $i_announcement++; ?>
	<?php endforeach;?>
</ul>
<script type="text/javascript">
    function ynbusiness_showhide(id, hide, show, obj)
    {
        if ($('ynbusinesspages_announcement_content_' + id).hasClass('rows4'))
        {
            $('ynbusinesspages_announcement_content_' + id).removeClass('rows4');
            obj.innerHTML = hide;
        } 
        else 
        {
            $('ynbusinesspages_announcement_content_' + id).addClass('rows4');
             obj.innerHTML = show;
        }
    } 
</script>
<?php endif; ?>

<?php if(count($this->announcements) > 0) :?> 
<div class="announcements-footer">
	<span class="prev"><i class="fa fa-chevron-left"></i></span>
	<span class="next"><i class="fa fa-chevron-right"></i></span>
</div>
<script type="text/javascript">
	$$('.announcements-footer .prev').addEvent('click', function(){
		$$('#business_announcement li')[<?php echo count($this->announcements)-1; ?>].inject( $('business_announcement'), 'top');
	});

	$$('.announcements-footer .next').addEvent('click', function(){
		$$('#business_announcement li')[0].inject( $('business_announcement'), 'bottom');
	});
</script>
<?php endif; ?>
