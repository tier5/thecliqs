<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynbusinesspages-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynbusinesspages-collapse"; ?>">
			<div class="ynbusinesspages-faq-title">
				<span class="ynbusinesspages-faq-icon"></span>
				<div class="ynbusinesspages-faq-title-item ynbusinesspages_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynbusinesspages-faq-title-item ynbusinesspages_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynbusinesspages-faq-content rich_content_body">
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
	$$('.ynbusinesspages-faq-title').addEvent('click', function(){
		this.getParent('div.ynbusinesspages-faq-item').toggleClass('ynbusinesspages-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynbusinesspages').getParent().addClass('active');
</script>