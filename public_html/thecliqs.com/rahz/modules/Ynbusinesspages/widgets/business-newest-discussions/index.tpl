<?php if($this -> topics): ?>
  <div class="ynbusinesspages_discussions_list" id="ynbusinesspages_newest_discussions">
    <ul class="ynbusinesspages_discussions">
      <?php foreach( $this->topics as $topic ):
        $lastpost = $topic->getLastPost();
		$lastposter = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($lastpost);
        ?>
        <li>
          <div class="ynbusinesspages_discussions_replies">
            <span>
              <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
            </span>
            <?php echo $this->translate(array('ynbusiness_reply', 'replies', $topic->post_count - 1)) ?>
          </div>
          <div class="ynbusinesspages_discussions_info">
            <h3<?php if( $topic->sticky ): ?> class='ynbusinesspages_discussions_sticky'<?php endif; ?>>
              <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            </h3>
            <div class="ynbusinesspages_discussions_blurb">
              <?php echo strip_tags($topic->getDescription()) ?>
            </div>
          </div>
          <div class="ynbusinesspages_discussions_lastreply">
            <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
            <div class="ynbusinesspages_discussions_lastreply_info">
              <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
              <br />
              <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'ynbusinesspages_discussions_lastreply_info_date')) ?>
            </div>
          </div>          
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>