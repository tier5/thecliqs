<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="ynwiki_browse" style="padding-top: 10px;" id="ynbusinesspages_newest_wikis">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='ynwiki_browse_photo'>
      	<?php $owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($item);?>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
      </div>
      <div class='ynwiki_browse_info'>
        <p class='ynwiki_browse_info_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </p>
        <p class='ynwiki_browse_info_date'>
          <?php echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($owner->getHref(), $owner->getTitle(), array('target'=>'_top')));?>
          |
          <?php echo $this->timestamp($item->creation_date) ?>
          <?php $revision = $item->getLastUpdated();
          if($revision):  ?>
          |
          <?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id);
         echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?>
         <?php echo $this->timestamp($revision->creation_date) ?>
          (<?php echo $this->htmlLink(array(
                  'action' => 'compare-versions',
                  'pageId' => $item->page_id,
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate("view change"), array(
                )) ?>)
           <?php endif;?>
        </p>
      </div>
      <p class='ynwiki_browse_info_blurb'>
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
      </p>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif;?>