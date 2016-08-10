<div class="ynbusinesspages-profile-module-header">
<?php if( $this->viewer()->getIdentity()): ?>
  <div class="ynbusinesspages-profile-header-right">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
        <?php echo $this->htmlLink(array(
          'route' => 'ynbusinesspages_extended',
          'controller' => 'topic',
          'action' => 'index',
          'business_id' => $this->subject()->getIdentity(),
          'tab' => $this->identity,
        ), '<i class="fa fa-list"></i>'.$this -> translate("View all Topics"), array(
          'class' => 'buttonlink'
      )) ?>
    <?php endif; ?>
    
    <?php if( $this -> subject() -> isAllowed('discussion_create')):?>
      <?php
      echo $this->htmlLink(array(
          'route' => 'ynbusinesspages_extended',
          'controller' => 'topic',
          'action' => 'create',
          'business_id' => $this->subject()->getIdentity(),
          'tab' => $this->identity,
        ), '<i class="fa fa-plus-square"></i>'.$this->translate('Post New Topic'), array(
          'class' => 'buttonlink'
      ));?>
    <?php endif;?>
  </div>
<?php endif;?>
<div class="ynbusinesspages-profile-header-content">
    <?php if( $this->paginator->getTotalItemCount() > 0 ):?> 
        <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
        <?php echo $this-> translate(array("ynbusiness_discussion", "Discussions", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
    <?php endif; ?>
</div>
</div>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<script type="text/javascript">
en4.core.runonce.add(function()
{
    var anchor = $('ynbusinesspages_profile_discussions').getParent();
    $('ynbusinesspages_profile_discussions_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynbusinesspages_profile_discussions_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynbusinesspages_profile_discussions_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('ynbusinesspages_profile_discussions_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>
  <div class="ynbusinesspages_discussions_list" id="ynbusinesspages_profile_discussions">
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
            <?php echo $this->translate(array("ynbusiness_reply", "replies", $topic->post_count - 1), $topic->post_count - 1) ?>
          </div>
          <div class="ynbusinesspages_discussions_lastreply">
            <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
            <div class="ynbusinesspages_discussions_lastreply_info">
              <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
              <br />
              <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'ynbusinesspages_discussions_lastreply_info_date')) ?>
            </div>
          </div>
          <div class="ynbusinesspages_discussions_info">
            <h3<?php if( $topic->sticky ): ?> class='ynbusinesspages_discussions_sticky'<?php endif; ?>>
              <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            </h3>
            <div class="ynbusinesspages_discussions_blurb">
               <?php echo strip_tags($topic->getDescription()) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<div class="ynbusinesspages-paginator">
  <div id="ynbusinesspages_profile_discussions_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="ynbusinesspages_profile_discussions_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate('No topics have been posted in this business yet.');?>
    </span>
  </div>
<?php endif; ?>