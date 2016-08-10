<div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages-profile-header-right">
  <?php
  	if($this->paginator->getTotalItemCount() > 0 )
  	{
  		echo $this->htmlLink(array(
  	          'route' => 'ynbusinesspages_extended',
  	          'controller' => 'wiki',
  	          'action' => 'list',
  	          'subject' => $this->subject()->getGuid(),
  	          'tab' => $this->identity,
  	        ), '<i class="fa fa-list"></i>'.$this->translate('View All Spaces'), array(
  	          'class' => 'buttonlink'
  	      ));
  	}
    if( $this->canAdd ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'ynwiki_general',
          'action' => 'create',
          'parent_type'=> 'ynbusinesspages_business',
          'subject_id' => $this->subject()->getIdentity(),
          'business_id' => $this->subject()->getIdentity(),
        ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Space'), array(
          'class' => 'buttonlink'
      )) ?>
    <?php endif; ?>
  </div>
  <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_space", "Wikis", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynbusinesspages_profile_wikis').getParent();
            $('ynbusinesspages_profile_wikis_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynbusinesspages_profile_wikis_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynbusinesspages_profile_wikis_previous').removeEvents('click').addEvent('click', function(){
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

            $('ynbusinesspages_profile_wikis_next').removeEvents('click').addEvent('click', function(){
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
        <?php endif; ?>
    });
</script>

<ul class="ynwiki_browse" style="padding-top: 10px;" id="ynbusinesspages_profile_wikis">
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
      <p class='ynwiki_browse_info_blurb' style="margin-left: 58px">
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
      </p>
    </li>
  <?php endforeach; ?>
</ul>

<div class="ynbusinesspages-paginator">
    <div id="ynbusinesspages_profile_wikis_previous" class="paginator_previous">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="ynbusinesspages_profile_wikis_next" class="paginator_next">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => '',
            'class' => 'buttonlink_right icon_next'
        ));
        ?>
    </div>
    <div class="clear"></div>
</div>
<?php else:?>
<div class="tip">
    <span>
      <?php echo $this->translate('No wikis have been added to this business yet.');?>
    </span>
</div>
<?php endif;?>