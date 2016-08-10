<?php if( count($this->paginator) ): ?> 
    <?php foreach($this->paginator as $item) :?>
        <div class="ynsocialads-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynsocialads-collapse"; ?>">
            <div class="ynsocialads-faq-title">
                <span class="ynsocialads-faq-icon"></span>
                <div class="ynsocialads-faq-title-item ynsocialads_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
                <div class="ynsocialads-faq-title-item ynsocialads_question_full"><?php echo $item->title?></div>
            </div>
            <div class="ynsocialads-faq-content">
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
<br/>
 <!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array());?>
</div>
<script type="text/javascript">
    $$('.ynsocialads-faq-title').addEvent('click', function(){
        this.getParent('div.ynsocialads-faq-item').toggleClass('ynsocialads-collapse'); 
    });
</script>
<script type="text/javascript">
$$('.core_main_ynsocialads').getParent().addClass('active');
</script>