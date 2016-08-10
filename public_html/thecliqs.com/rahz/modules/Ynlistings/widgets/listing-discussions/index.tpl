<?php $empty = true;
  if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div class="ynlistings_discussions_list">
    <ul class="ynlistings_discussions">
       <li>
    <?php if( $this->canPost ): ?>
        <div style="float:left;">
      <?php echo $this->htmlLink(array(
        'route' => 'ynlistings_extended',
        'controller' => 'topic',
        'action' => 'create',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Post New Topic'), array(
        'class' => 'buttonlink icon_ynlistings_post_new'
      )) ?>
        </div>
    <?php endif;?>
        <?php if( $this->viewMore ): ?>
        <div style="float:right;margin-right: 10px;">
          <?php echo $this->htmlLink(array(
              'route' => 'ynlistings_extended',
              'controller'=>'topic',
              'action'=>'index',
              'subject' => $this->listing->getGuid(),
          ),$this->translate("View All Discussions"),array(
              'class'=>'buttonlink item_icon_ynlistings_post')); ?>
        </div>
          <?php endif;?>
      </li>
      <?php foreach( $this->paginator as $topic ):
        if( empty($topic->lastposter_id) ) {
          continue;
        }
        $owner = $topic->getOwner();
        
        $lastpost = $topic->getLastPost();
        if( !$lastpost ) {
          continue;
        }
        $lastposter = $topic->getLastPoster();
        $empty = false;
        ?>
        <li>
          <?php if( $lastpost && $lastposter ): ?>
          <div class="ynlistings_discussions_lastreply">
              <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')) ?>
            <div class="ynlistings_discussions_lastreply_info">
              <b><?php echo $owner->__toString() ?></b>
            </div>
          </div>
          <?php endif; ?>

          <div class="ynlistings_discussions_replies">
            <span>
              <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
            </span>
            <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
          </div>
          
          <div class="ynlistings_discussions_info">
            <h3<?php if( $topic->sticky ): ?> class='ynlistings_discussions_sticky'<?php endif; ?>>
              <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            </h3>
            <div class="ynlistings_discussions_blurb" style="text-align: justify;">
              <?php echo $this->viewMore(strip_tags($topic->getDescription()),50,100) ?>
            </div>
            <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Replied')) ?>
            <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
            -
            <?php echo $this->timestamp(strtotime($topic->modified_date)) ?>
          </div>

        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif;?>
<?php if( $empty ): ?>

<?php if( $this->viewer()->getIdentity() ) ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No topics have been posted in this listing yet.');?>
      <?php if( $this->canPost ): ?>
      <?php echo $this->translate('Create a new one %1$shere%2$s',
      '<a href="'.$this->url(array('controller'=>'topic','action' => 'create','subject' =>$this->listing->getGuid()), 'ynlistings_extended').'">', '</a>');?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>