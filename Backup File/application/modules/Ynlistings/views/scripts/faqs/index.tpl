<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynlistings-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynlistings-collapse"; ?>">
			<div class="ynlistings-faq-title">
				<span class="ynlistings-faq-icon"></span>
				<div class="ynlistings-faq-title-item ynlistings_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynlistings-faq-title-item ynlistings_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynlistings-faq-content rich_content_body">
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
	$$('.ynlistings-faq-title').addEvent('click', function(){
		this.getParent('div.ynlistings-faq-item').toggleClass('ynlistings-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynlistings').getParent().addClass('active');
</script>