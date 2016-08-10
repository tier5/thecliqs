<div class="layout_page_ynauction_help">
<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container">
		 <div class="headline">
		  <h2>
		    <?php echo $this->translate('Auction');?>
		  </h2>
		  <div class="tabs">
		    <?php
		      // Render the menu
		      echo $this->navigation()
		        ->menu()
		        ->setContainer($this->navigation)
		        ->render();
		    ?>
		  </div>
		</div>
	</div>
  </div>
</div>

<div class="generic_layout_container layout_main">
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container">
		<!--  do not remove this line  -->
		<a id="faq-0" name="faq-0"></a>

		<h2><?php echo $this->translate("FAQs") ?></h2>
		<br />	
		<?php if (count($this->items) > 0): ?>

		<ul class="faqs-lists">
			<?php foreach($this->items as $item): ?>
			<li><a href="<?php echo $this->url(array('controller'=>'faqs','action'=>'index')) ?>#faq-<?php echo $item->getIdentity() ?>"><?php echo $item->question ?></a></li>
			<?php endforeach; ?>
		</ul>

		<div>
		<?php $i=0; foreach($this->items as $item): ?>
			<!--  do not remove this line  -->
			<a id="faq-<?php echo $item->getIdentity() ?>" name="faq-<?php echo $item->getIdentity() ?>"></a>
			<div>
				<div class="faq-question"><?php echo ++$i?>. <?php echo $item->question ?></div>
				<p class="faq-answer">
					<?php echo $item->answer ?>
				</p>
				<p><a class="go-top" href="<?php echo $this->url(array('controller'=>'faqs','action'=>'index')) ?>#faq-0"><?php echo $this->translate("Go top");?></a></p>
				
			</div>
		<?php endforeach; ?>
		</div>
		<?php else: ?>
			<div class="tip"><span><?php echo $this->translate("There is no faq.") ?></span></div>
		<?php endif; ?>

		<style type="text/css">
			ul.faqs-lists{
				
			}
			ul.faqs-lists li{
				list-style-type: decimal;
				list-style-position: inside;
			}
			div.faq-question{
				font-weight: bold;
				margin: 12px 0;
			}
		</style>
	</div>
  </div>
</div>