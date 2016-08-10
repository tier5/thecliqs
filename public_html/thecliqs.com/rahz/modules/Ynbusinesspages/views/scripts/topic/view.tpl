<script type="text/javascript">
function checkOpenPopup(url)
{
	if(window.innerWidth <= 480)
  {
  	Smoothbox.open(url, {autoResize : true, width: 300});
  }
  else
  {
  	Smoothbox.open(url);
  }
}
</script>
<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">
      <h2>
        <?php echo $this->business->__toString() ?>
        <?php echo $this->translate('&#187;'); ?>
        <?php echo $this->htmlLink(array(
              'route' => 'ynbusinesspages_extended',
              'controller' => 'topic',
              'action' => 'index',
              'business_id' => $this->business->getIdentity(),
              'tab' => $this -> tab
            ), $this->translate('Discussions')) ?>
      </h2>
    </div>
  </div>
</div>

<div class="generic_layout_container layout_main">
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container">
<h3>
  <?php echo $this->topic->getTitle() ?>
</h3>

<?php $this->placeholder('businesstopicnavi')->captureStart(); ?>
<div class="ynbusinesspages_discussions_thread_options">
  <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller' => 'topic', 'action' => 'index', 'business_id' => $this->business->getIdentity(), 'tab' => $this -> tab), $this->translate('Back to Topics'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php if( $this->canPost ): ?>
    <?php echo $this->htmlLink($this->url(array()) . '#reply', $this->translate('Post Reply'), array(
      'class' => 'buttonlink icon_ynbusinesspages_post_reply'
    )) ?>
    <?php if( $this->viewer->getIdentity() ): ?>
      <?php if( !$this->isWatching ): ?>
        <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
          'class' => 'buttonlink icon_ynbusinesspages_topic_watch'
        )) ?>
      <?php else: ?>
        <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
          'class' => 'buttonlink icon_ynbusinesspages_topic_unwatch'
        )) ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php if( $this->canEdit): ?>
    <?php if( !$this->topic->sticky ): ?>
      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
        'class' => 'buttonlink icon_ynbusinesspages_post_stick'
      )) ?>
    <?php else: ?>
      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
        'class' => 'buttonlink icon_ynbusinesspages_post_unstick'
      )) ?>
    <?php endif; ?>
    <?php if( !$this->topic->closed ): ?>
      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
        'class' => 'buttonlink icon_ynbusinesspages_post_close'
      )) ?>
    <?php else: ?>
      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
        'class' => 'buttonlink icon_ynbusinesspages_post_open'
      )) ?>
    <?php endif; ?>
    <?php echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
      'class' => 'buttonlink smoothbox icon_ynbusinesspages_post_rename'
    )) ?>
   
  <?php elseif( !$this->canEdit ): ?>
    <?php if( $this->topic->closed ): ?>
      <div class="ynynbusinesspages_discussions_thread_options_closed">
        <?php echo $this->translate('This topic has been closed.')?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <?php if( $this->canDelete): ?>
  	 <?php echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
      'class' => 'buttonlink smoothbox icon_ynbusinesspages_post_delete'
    )) ?>
  <?php endif; ?>
</div>
<?php $this->placeholder('businesstopicnavi')->captureEnd(); ?>



<?php echo $this->placeholder('businesstopicnavi') ?>
<?php echo $this->paginationControl(null, null, null, array(
  'params' => array(
    'post_id' => null // Remove post id
  )
)) ?>


<script type="text/javascript">
  var quotePost = function(user, href, body) {
    if( $type(body) == 'element' ) {
      body = $(body).getParent('li').getElement('.ynbusinesspages_discussions_thread_body_raw').get('html').trim();
    }
    $("body").value = '[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n';
    $("body").focus();
    $("body").scrollTo(0, $("body").getScrollSize().y);
  }
  en4.core.runonce.add(function() {
    $$('.ynbusinesspages_discussions_thread_body').enableLinks();
  });
</script>
<ul class='ynbusinesspages_discussions_thread'>
  <?php foreach( $this->paginator as $post ):
    $user = $this->item('user', $post->user_id);
	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($post);
    $isOwner = false;
    $isMember = false;
    $liClass = 'ynbusinesspages_discussions_thread_author_none';
    if( $this->business->isOwner($user) ) {
      $isOwner = true;
      $isMember = true;
      $liClass = 'ynbusinesspages_discussions_thread_author_isowner';
    } else if( $this->business->membership()->isMember($user) ) {
      $isMember = true;
      $liClass = 'ynbusinesspages_discussions_thread_author_ismember';
    }
    ?>
  <li class="<?php echo $liClass ?>">
    <div class="ynbusinesspages_discussions_thread_author">
      <div class="ynbusinesspages_discussions_thread_author_name">
        <?php 
        echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
      </div>
      <div class="ynbusinesspages_discussions_thread_photo">
        <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')) ?>
      </div>
      <div class="ynbusinesspages_discussions_thread_author_rank">
        <?php
          if( $isOwner ) {
            echo $this->translate('Host');
          } else if( $isMember ) {
            echo $this->translate('Member');
          }
        ?>
      </div>
    </div>
    <div class="ynbusinesspages_discussions_thread_info">
      <div class="ynbusinesspages_discussions_thread_details">
        <div class="ynbusinesspages_discussions_thread_details_options">
          <?php if( $this->form ): ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Quote'), array(
              'class' => 'buttonlink icon_ynbusinesspages_post_quote',
              'onclick' => 'quotePost("'.$this->escape($owner->getTitle()).'", "'.$this->escape($owner->getHref()).'", this);',
            )) ?>
          <?php endif; ?>
          <?php if( $post->user_id == $this->viewer()->getIdentity() ||
                    $this->business->getOwner()->getIdentity() == $this->viewer()->getIdentity() ||
                    $this->canAdminEdit ): 
                    $url = $this->url(array('controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'business_id' => $this->business->getIdentity()),'ynbusinesspages_extended', true);?>
            <a class="buttonlink icon_ynbusinesspages_post_edit" href="javascript:;" onclick="checkOpenPopup('<?php echo $url?>');"><?php echo $this->translate('Edit')?></a>
            <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller' => 'post', 'action' => 'delete','business_id' => $this->business->getIdentity(), 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
              'class' => 'buttonlink smoothbox icon_ynbusinesspages_post_delete'
            )) ?>
          <?php endif; ?>
        </div>
        <div class="ynbusinesspages_discussions_thread_details_anchor">
          <a href="<?php echo $post->getHref() ?>">
            &nbsp;
          </a>
        </div>
        <div class="ynbusinesspages_discussions_thread_details_date">
          <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
          <?php //echo $this->locale()->toDateTime(strtotime($post->creation_date)) ?>
        </div>
      </div>
      <div class="ynbusinesspages_discussions_thread_body">
        <?php echo nl2br($this->BBCode($post->body, array('link_no_preparse' => true))) ?>
      </div>
      <span class="ynbusinesspages_discussions_thread_body_raw" style="display: none;">
        <?php echo $post->body; ?>
      </span>
    </div>
  </li>
  <?php endforeach; ?>
</ul>


<?php if($this->paginator->getCurrentItemCount() > 4): ?>

  <?php echo $this->paginationControl(null, null, null, array(
    'params' => array(
      'post_id' => null // Remove post id
    )
  )) ?>
  <br />
  <?php echo $this->placeholder('businesstopicnavi') ?>

<?php endif; ?>

<br />

<?php if( $this->form ): ?>
  <a name="reply"></a>
  <?php echo $this->form->setAttrib('id', 'ynbusinesspages_topic_reply')->render($this) ?>
<?php endif; ?>
</div>
</div>
</div>