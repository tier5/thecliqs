<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">
      <h2>
        <?php echo $this->business->__toString()." ".$this->translate("&#187; Discussions") ?>
      </h2>    
    </div>  
  </div>
</div>

<div class="generic_layout_container layout_main">
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container">

    <div class="ynbusinesspages-profile-module-header">
        <!-- Menu Bar -->
        <div class="ynbusinesspages-profile-header-right">
          <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
            'class' => 'buttonlink'
          )) ?>
          <?php if ($this->can_post) { echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller' => 'topic', 'action' => 'create', 'business_id' => $this->business->getIdentity()), '<i class="fa fa-plus-square"></i>'.$this->translate('Post New Topic'), array(
            'class' => 'buttonlink'
          )); }?>
        </div>      
    </div>  

    <?php if( $this->paginator->count() > 1 ): ?>
      <div class="ynbusssiness-clearfix">
        <?php echo $this->paginationControl($this->paginator) ?>
      </div>
    <?php endif; ?>

    <div classs="ynbusinesspages_discussions_list">
    <ul class="ynbusinesspages_discussions">
      <?php foreach( $this->paginator as $topic ):
          $lastpost = $topic->getLastPost();
          $lastposter = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($lastpost);
          ?>
        <li>
          <div class="ynbusinesspages_discussions_replies">
            <span>
              <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
            </span>
            <?php echo $this->translate(array('ynbusiness_reply', 'replies', $topic->post_count - 1), $topic->post_count - 1) ?>
          </div>
          <div class="ynbusinesspages_discussions_lastreply">
            <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
            <div class="ynbusinesspages_discussions_lastreply_info">
              <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> by <?php echo $lastposter->__toString() ?>
              <br />
              <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'ynbusinesspages_discussions_lastreply_info_date')) ?>
            </div>
          </div>
          <div class="ynbusinesspages_discussions_info">
            <h3<?php if( $topic->sticky ): ?> class='ynbusinesspages_discussions_sticky'<?php endif; ?>>
              <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            </h3>
            <div class="ynbusinesspages_discussions_blurb">
              <?php echo $this->viewMore($topic->getDescription()) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    </div>

    <?php if( $this->paginator->count() > 1 ): ?>
      <div class="ynbusssiness-clearfix">
        <?php echo $this->paginationControl($this->paginator) ?>
      </div>
    <?php endif; ?>
    </div>
  </div>
</div>