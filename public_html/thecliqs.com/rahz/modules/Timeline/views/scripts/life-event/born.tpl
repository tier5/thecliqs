<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version ID: born.tpl 2/16/12 11:08 AM mt.uulu $
 * @author Mirlan
 */
?>
<script type="text/javascript">
  document.tl_born = new TimelineBorn();

  <?php if ($this->canEdit): ?>
  document.tl_born.setOptions({
    'element_id':'born-photo',
    'edit_buttons':'tl-born-edit',
    'loader_id':'tl-born-loader',
    'is_allowed':true,

    'born_url':'<?php echo $this->url(array(
      'action' => 'get',
      'id' => $this->subject()->getIdentity(),
      'type' => 'born',
    ), 'timeline_photo', true); ?>',

    'position_url':'<?php echo $this->url(array(
      'action' => 'position',
      'id' => $this->subject()->getIdentity(),
      'type' => 'born',
    ), 'timeline_photo', true); ?>'
  });

  document.tl_born.position.top = <?php echo $this->position['top']; ?>;
  document.tl_born.position.left = <?php echo $this->position['left']; ?>;

  en4.core.runonce.add(function () {
    document.tl_born.init();
    document.tl_born.options.born_width = document.tl_born.get().getParent().getWidth();
  });
    <?php endif; ?>
</script>


<li class="born tli starred le">
  <div class="dot">
    <div></div>
  </div>
  <div class="arrow"></div>

  <div class="options"></div>

  <div class="info">
    <div>
      <?php
      if ($this->subject()->getType() == 'user')
        echo $this->htmlImage('application/modules/Timeline/externals/images/born_icon.png', '');
      elseif ($this->subject()->getType() == 'page')
        echo $this->htmlImage('application/modules/Timeline/externals/images/page_timeline/created_icon.png', '');
      ?>
    </div>

    <div class="date">
      <?php
      if ($this->subject()->getType() == 'user')
                echo $this->translate('TIMELINE_Born on %1s', $this->locale()->toDate($this->birthdate, array(
                    'size' => 'long',
                    'timezone' => false,
                )));
      elseif ($this->subject()->getType() == 'page') {
        echo $this->translate('TIMELINE_Created on %1s', $this->locale()->toDate($this->birthdate, array(
          'size' => 'long',
          'timezone' => false,
        )));
      }
      ?>
    </div>
  </div>

  <?php if ($this->photoExists || ($this->viewer()->getIdentity() && $this->canEdit)): ?>
  <div class="photo <?php if (!$this->photoExists): ?>add<?php endif; ?>">

    <div id="tl-born">
      <a href="javascript:void(0);"
        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.slideshow', true) && $this->albumPhoto): ?>
         onclick="tl_born.slideShow('<?php echo $this->albumPhoto->getPhotoUrl(); ?>', '<?php echo $this->albumPhoto->getGuid(); ?>', this)"
        <?php endif; ?>
        >
        <?php echo $this->subject()->getTimelinePhoto('born')?>
      </a>
    </div>

    <?php if ($this->canEdit): ?>
    <div id='tl-born-edit' class="tl-options born-edit">
      <ul class="tl-in-block">
        <li class="save">
          <?php echo $this->htmlLink(
          'javascript://',
          $this->translate('TIMELINE_Save Positions'),
          array('class' => 'save-positions hidden')); ?>
        </li>
        <li class="more">
          <?php echo $this->htmlLink('javascript://', $this->translate(($this->photoExists) ? "TIMELINE_Edit Photo" : "TIMELINE_+Add Photo"), array(
          'class' => 'born-change buttonlink',
          'id' => 'bp-options'
        )); ?>

          <ul class="born-options tl-in-block click-listener bound-bp-options">

            <?php if ($this->isAlbumEnabled): ?>
            <li><?php echo $this->htmlLink(array(
                'route' => 'timeline_photo',
                'id' => $this->subject()->getIdentity(),
                'reset' => true,
                'type' => 'born',
              ),
              $this->translate('TIMELINE_Choose from Photos...'),
              array(
                'class' => 'born-albums smoothbox',
                'title' => $this->translate('TIMELINE_Choose from Photos...'),
              )); ?>
            </li>
            <?php endif; ?>

            <li><?php echo $this->htmlLink(array(
              'route' => 'timeline_photo',
              'action' => 'upload',
              'id' => $this->subject()->getIdentity(),
              'type' => 'born',
            ), $this->translate('TIMELINE_Upload Photo...'), array(
              'class' => 'born-upload smoothbox',
              'title' => $this->translate('TIMELINE_Upload Photo...')
            )); ?>

            </li>

            <li <?php if (!$this->photoExists): ?>class="hidden"<?php endif; ?>>
              <?php echo $this->htmlLink(
              'javascript:document.tl_born.reposition.start()',
              $this->translate('TIMELINE_Reposition...'),
              array('class' => 'born-reposition')); ?>
            </li>

            <li <?php if (!$this->photoExists): ?>class="hidden"<?php endif; ?>>
              <?php echo $this->htmlLink(array(
                'route' => 'timeline_photo',
                'action' => 'remove',
                'id' => $this->subject()->getIdentity(),
                'type' => 'born',
              ),
              $this->translate('TIMELINE_Remove...'), array(
                'class' => 'born-remove smoothbox',
                'title' => $this->translate('TIMELINE_Remove...'),
              )); ?>
            </li>
          </ul>

        </li>
      </ul>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</li>
