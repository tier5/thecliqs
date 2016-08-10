<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 7244 2011-03-12 15:42:53 taalay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('page_links').getParent();
    $('page_links_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('page_links_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('page_links_previous').removeEvents('click').addEvent('click', function(){
      var url = '<?php echo $this->url(array('action' => 'index', 'content_id' => $this->identity), 'page_widget', true) ?>';
      en4.core.request.send(new Request.HTML({
        url : url,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('page_links_next').removeEvents('click').addEvent('click', function(){
      var url = '<?php echo $this->url(array('action' => 'index', 'content_id' => $this->identity), 'page_widget', true) ?>';
      en4.core.request.send(new Request.HTML({
        url : url,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<ul class="page_links" id="page_links">
  <?php foreach( $this->paginator as $link ): ?>
    <li>
      <div style="float: right;">
        <?php
          if ($link->isDeletable()) {
            echo $this->htmlLink(array('route' => 'default', 'module' => 'core', 'controller' => 'link', 'action' => 'delete', 'link_id' => $link->link_id, 'format' => 'smoothbox'), $this->translate('delete link'), array(
              'class' => 'smoothbox page_icon_link_delete'
            ));
          }
        ?>
      </div>
      <?php if($link->photo_id != 0):?>
        <div class="page_links_photo">
          <?php echo $this->htmlLink($link->getHref(), $this->itemPhoto($link)) ?>
        </div>
      <?php else: ?>
        <div class="page_links_photo">
          <?php echo $this->htmlLink( $link->getHref(), '<img src="application/modules/Page/externals/images/page/nophoto_page_link.png" class="thumb_profile item_photo_core_link item_nophoto " />'); ?>
        </div>
      <?php endif;?>
      <div class="page_links_info">
        <div class="page_links_title">
          <?php echo $this->htmlLink($link->getHref(), $link->getTitle(), array('target' => '_blank')) ?>
        </div>
        <div class="page_links_description">
          <?php echo $this->htmlLink($link->getHref(), $link->getDescription()) ?>
        </div>
        <?php if( !$link->getOwner()->isSelf($link->getParent()) ): ?>
        <div class="page_links_author">
          <?php echo $this->translate('Posted by %s', $link->getOwner()->__toString()) ?>
          <?php echo $this->timestamp($link->creation_date) ?>
        </div>
        <?php endif; ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<div>
  <div id="page_links_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="page_links_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
