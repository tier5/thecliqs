<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    // Enable links
    $$('.classifieds_browse_info_blurb').enableLinks();
  });
</script>

<?php if( $this->tag ): ?>
  <h3>
    <?php echo $this->translate('Showing classified listings using the tag');?> #<?php echo $this->tag_text;?> <a href="<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
  </h3>
<?php endif; ?>

<?php if( $this->start_date ): ?>
  <?php foreach ($this->archive_list as $archive): ?>
    <h3>
      <?php echo $this->translate('Showing classified listings created on');?> <?php if ($this->start_date==$archive['date_start']) echo $archive['label']?> <a href="<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
    </h3>
  <?php endforeach; ?>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="classifieds_browse">
    <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class='classifieds_browse_photo'>
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
        </div>
        <div class='classifieds_browse_info'>
          <div class='classifieds_browse_info_title'>
            <h3>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            <?php if( $item->closed ): ?>
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Classified/externals/images/close.png'/>
            <?php endif;?>
            </h3>
          </div>
          <div class='classifieds_browse_info_date'>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
          </div>
          <div class='classifieds_browse_info_blurb'>
            <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
            <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

<?php elseif( $this->category || $this->show == 2 || $this->search ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted a classified listing with that criteria.');?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'classified_general', true).'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted a classified listing yet.');?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'classified_general', true).'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  //'params' => $this->formValues,
)); ?>
