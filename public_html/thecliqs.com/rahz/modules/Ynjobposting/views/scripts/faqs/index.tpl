<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynjobposting-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynjobposting-collapse"; ?>">
			<div class="ynjobposting-faq-title">
				<span class="ynjobposting-faq-icon"></span>
				<div class="ynjobposting-faq-title-item ynjobposting_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynjobposting-faq-title-item ynjobposting_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynjobposting-faq-content rich_content_body">
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
	$$('.ynjobposting-faq-title').addEvent('click', function(){
		this.getParent('div.ynjobposting-faq-item').toggleClass('ynjobposting-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynjobposting').getParent().addClass('active');
</script>