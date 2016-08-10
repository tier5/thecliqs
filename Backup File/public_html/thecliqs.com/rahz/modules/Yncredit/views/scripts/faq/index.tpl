<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="yncredit-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "yncredit-collapse"; ?>">
		    <div class="yncredit-faq-title">
		        <span class="yncredit-faq-icon"></span>
		        <div class="yncredit-faq-title-item yncredit_question_preview"><?php echo $this -> string() -> truncate($item -> question, 200);?></div>
		        <div class="yncredit-faq-title-item yncredit_question_full"><?php echo $item -> question?></div>
		    </div>
		    <div class="yncredit-faq-content">
		        <?php echo $item -> answer?>
		    </div>
		</div>
	<?php endforeach; ?>   
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No faq has been added.") ?>
    </span>
  </div>
<?php endif; ?>
<br/>
 <!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array());?>
</div>
<script type="text/javascript">
    $$('.yncredit-faq-title').addEvent('click', function(){
        this.getParent('div.yncredit-faq-item').toggleClass('yncredit-collapse'); 
    });
</script>