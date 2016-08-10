<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<?php
$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/cover.js');
?>

<script type="text/javascript">
  document.tl_cover = new TimelineCover();
</script>

<?php if($this->canEdit): ?>
<script type="text/javascript">
  document.tl_cover.setOptions({
    'element_id':'cover-photo',
    'edit_buttons':'tl-cover-edit',
    'loader_id':'tl-cover-loader',
    'is_allowed':true,
    'cover_url':'<?php echo $this->url(array('action' => 'get', 'id' => $this->subject()->getIdentity()), 'timeline_photo', true); ?>',
    'position_url':'<?php echo $this->url(array('action' => 'position', 'id' => $this->subject()->getIdentity()), 'timeline_photo', true); ?>'
  });

  document.tl_cover.position.top = <?php echo $this->position['top']; ?>;
  document.tl_cover.position.left = <?php echo $this->position['left']; ?>;

  en4.core.runonce.add(function () {
    document.tl_cover.init();
    document.tl_cover.options.cover_width = document.tl_cover.get().getParent().getWidth();
  });
</script>
<?php endif; ?>

<div style="min-height: <?php echo $this->coverHeight;?>px">
  <div class="tl-block cover <?php if (!$this->coverExists): ?> no-cover <?php endif; ?>">

    <div id='tl-cover'>
      <a href="javascript:void(0);"
        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.slideshow', true) && $this->albumPhoto): ?>
         onclick="tl_cover.slideShow('<?php echo $this->albumPhoto->getPhotoUrl(); ?>', '<?php echo $this->albumPhoto->getGuid(); ?>', this)"
        <?php endif; ?>
        >

        <?php echo $this->subject()->getTimelinePhoto()?>
      </a>
    </div>

    <?php if ($this->canEdit): ?>

    <div id='tl-cover-edit' class="tl-options cover-edit">
      <ul class="tl-in-block">
        <li class="save">
          <?php echo $this->htmlLink(
          'javascript://',
          $this->translate('TIMELINE_Save Positions'),
          array('class' => 'save-positions hidden')); ?>
        </li>
        <li class="more">
          <?php echo $this->htmlLink('javascript://', $this->translate($this->label), array(
          'class' => 'cover-change buttonlink'
        )); ?>

          <ul class="cover-options tl-in-block visiblity-hidden">

            <?php if ($this->isAlbumEnabled): ?>
            <li><?php echo $this->htmlLink(array(
                'route' => 'timeline_photo',
                'id' => $this->subject()->getIdentity(),
                'reset' => true
              ),
              $this->translate('TIMELINE_Choose from Photos...'),
              array(
                'class' => 'cover-albums smoothbox',
              )); ?>
            </li>
            <?php endif; ?>

            <li><?php echo $this->htmlLink(array(
              'route' => 'timeline_photo',
              'action' => 'upload',
              'id' => $this->subject()->getIdentity(),
            ), $this->translate('TIMELINE_Upload Photo...'), array(
              'class' => 'cover-upload smoothbox')); ?>
            </li>

            <li><?php echo $this->htmlLink(
              'javascript:document.tl_cover.reposition.start()',
              $this->translate('TIMELINE_Reposition...'),
              array('class' => 'cover-reposition')); ?>
            </li>

            <li><?php echo $this->htmlLink(array(
                'route' => 'timeline_photo',
                'action' => 'remove',
                'id' => $this->subject()->getIdentity(),
              ),
              $this->translate('TIMELINE_Remove...'), array(
                'class' => 'cover-remove smoothbox')); ?>
            </li>
          </ul>

        </li>
      </ul>
    </div>

    <?php endif; ?>

  </div>
</div>