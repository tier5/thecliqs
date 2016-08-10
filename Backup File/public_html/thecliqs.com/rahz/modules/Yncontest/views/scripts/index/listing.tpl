<?php echo $this->content()->renderWidget('yncontest.main-menu') ?>
<h3>Contest listing pages</h3>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
        $(function(){
                $('.asda').hover(
                    function(){
                      $(this).parent().find('.box').stop().animate({bottom:-138},{queue:false});
                      $(this).parent().find('img').stop().animate({opacity:1},{queue:false})
                    },
                    function(){
                      $(this).parent().find('.box').stop().animate({bottom:-197},{queue:false});
                      $(this).parent().find('img').stop().animate({opacity:1},{queue:false})
                    }
                )
            })
</script>
<?php 		
	if( count($this->paginator) ): 
?>
<div style="width:700px;" class="yncontest_listing">
	
	<?php foreach ($this->paginator as $contest): ?>
	<div class="box-1">
		<div class="text-top"><?php echo Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->contest_name),15); ?></div>
        <div class="asda">
             <?php
            	echo $this->itemPhoto($contest,'normal.thumb');
            ?>
            <div class="box">
            <span>
                <p>
                	<?php 
	                	if(isset($contest->award_name))
	                		echo Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->award_name),15); 
						else {							
						 		$awards = Engine_Api::_()->yncontest()->getAwardByContest($contest->contest_id);
								foreach($awards AS $award):
							 		echo $award->award_name;							 		
							 	endforeach;
							}	
                	?>
                </p>                	
                	
                <p><?php echo $contest->dayleft.$this->translate(" days left");?></p>
            </span>
            </div>
        </div>
    </div>
   <?php endforeach; ?>
</div> 	
 
	 <?php if( count($this->paginator) > 1 ): ?>
	        <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	      <?php endif; ?>
 
    <?php else: ?>
      <div class="tip">
        <span>
        <?php echo $this->translate('Have not contest.') ?>        
        </span>
      </div>
    <?php endif; ?>

 