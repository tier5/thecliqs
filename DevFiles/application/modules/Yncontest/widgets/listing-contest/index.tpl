<h3><?php echo $this->translate("Listing contest"); ?></h3>
    <?php if( count($this->paginator) > 0 ): ?>
      <ul class='contest_browse contest_list_tab'>
        <?php foreach( $this->paginator as $contest ): ?>
          <li>
	        <div class="contest_photo">
	          <?php echo $this->htmlLink($contest->getHref(), $this->itemPhoto($contest, 'thumb.icon')) ?>
	        </div> 
	        <div class="contest_info">
	          <div class="contest_title">
	            <div class="contest_photo">
	            <?php $contest_title = Engine_Api::_()->yncontest()->subPhrase($contest->getTitle(),60);?>
	            <?php echo $this->htmlLink($contest->getHref(), $contest_title);?>
	            -
	            <?php echo $contest->getOwner();?>
	            </div>
	            <div class="contest_options">
	          		<?php echo $this->timestamp(strtotime($contest->modified_date)); ?>
	        	</div>
	          </div>              
	          <div class="contest_desc">
	            <?php echo Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->description),100); ?>
	          </div>
	        </div>
      </li>
        <?php endforeach; ?>
      </ul>
      <?php if( count($this->paginator) > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
      <?php endif; ?>

    <?php else: ?>
      <div class="tip">
        <span>
        <?php echo $this->translate('Nobody has written an contest with that criteria.') ?>
        </span>
      </div>
    <?php endif; ?>




