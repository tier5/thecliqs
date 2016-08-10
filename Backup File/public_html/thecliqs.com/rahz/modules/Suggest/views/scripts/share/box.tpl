<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: box.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>
<?php
    $type = $this->subject->getType();
    $id = $this->subject->getIdentity();
?>
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.'.$this->subject->getType())): ?>

<?php

    $share_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->subject->getHref();
    $share_url = urlencode($share_url);
?>

<?php
  if ($this->app_id) {
    $FB = '
      <div class="fb_share_box_container">
        <iframe src="http://www.facebook.com/plugins/like.php?app_id=' . $this->app_id . '&amp;href=' . $share_url . '&amp;send=false&amp;layout=box_count&amp;width=60&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=64" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:60px; height:64px;" allowTransparency="true"></iframe>
      <div class="clr"></div>
      </div>
    ';
  } else {
    $FB = '';
  }
?>
<?php
  $twitter = '
    <div class="twitter_share_box_container">
      <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">'.$this->translate('Tweet').'</a>
    </div>
    <div class="clr"></div>
  ';

  $google = '<div class="google_share_box_container"><div class="g-plusone" data-size="tall" data-count="true" data-href="' . $this->subject->getHref() . '"></div></div><div class="clr"></div>';

  $linkedin = '<div class="linkedin_share_box_container"></div><div class="clr"></div>';

  if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
    $se = '
    <div class="se_share_box_container"> '
    . $this->htmlLink($this->url(array(
          'module' => 'activity',
          'controller' => 'index',
          'action' => 'share',
          'type' => $type,
          'id' => $id,
          'format' => 'smoothbox',
        ), 'default'), $this->translate('share'),
        array('
          class' => 'smoothbox buttonlink'
        ))
    . '</div>
    <div class="clr"></div>
    <div class="he_suggest_box_container">
      <a href="javascript:HESuggest.open()" class="buttonlink">'.$this->translate('suggest').'</a>
    </div>
    <div class="clr"></div>
    ';
  } else {
    $se = '';
  }
?>

window.addEvent('load', function() {
  HESuggest.share(<?php echo Zend_Json_Encoder::encode($twitter.$FB.$google.$linkedin.$se); ?>, <?php echo $this->jsonInline($this->app_id); ?>);
});

<?php endif; ?>