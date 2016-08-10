<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynmusic-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynmusic-collapse"; ?>">
			<div class="ynmusic-faq-title">
				<span class="ynmusic-faq-icon"></span>
				<div class="ynmusic-faq-title-item ynmusic_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynmusic-faq-title-item ynmusic_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynmusic-faq-content rich_content_body">
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
	$$('.ynmusic-faq-title').addEvent('click', function(){
		this.getParent('div.ynmusic-faq-item').toggleClass('ynmusic-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynmusic').getParent().addClass('active');
</script>