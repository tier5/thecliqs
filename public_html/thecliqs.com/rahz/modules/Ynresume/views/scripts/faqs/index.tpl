<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynresume-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynresume-collapse"; ?>">
			<div class="ynresume-faq-title">
				<span class="ynresume-faq-icon"></span>
				<div class="ynresume-faq-title-item ynresume_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynresume-faq-title-item ynresume_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynresume-faq-content rich_content_body">
				<?php echo $item->answer?>
			</div>
		</div>
	<?php endforeach; ?>   
<?php else:?>
<div class="tip">
	<span>
		<?php echo $this->translate("No FAQs has been added.") ?>
	</span>
</div>
<?php endif; ?>

<!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array());?>
</div>
<script type="text/javascript">
	$$('.ynresume-faq-title').addEvent('click', function(){
		this.getParent('div.ynresume-faq-item').toggleClass('ynresume-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynresume').getParent().addClass('active');
</script>