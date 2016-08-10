<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: default.tpl 9089 2011-07-21 23:12:11Z john $
 * @author     John
 */
?>

<?php
$wall = '';
if (Engine_Api::_()->getDbTable('modules' ,'hecore')->isModuleEnabled('wall')){
  $wall = $this->partial('_header.tpl', 'wall');
}
?>

<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />


  <?php // ALLOW HOOKS INTO META ?>
  <?php echo $this->hooks('onRenderLayoutDefault', $this) ?>


  <?php // TITLE/META ?>
  <?php
  $counter = (int) $this->layout()->counter;

  $request = Zend_Controller_Front::getInstance()->getRequest();
  $this->headTitle()
    ->setSeparator(' - ');
  $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
    . '-' . $request->getControllerName();
  $pageTitle = $this->translate($pageTitleKey);
  if( $pageTitle && $pageTitle != $pageTitleKey ) {
    $this
      ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
  }
  $this
    ->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
  ;
  $this->headMeta()
    ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
    ->appendHttpEquiv('Content-Language', 'en-US');

  // Make description and keywords
  $description = '';
  $keywords = '';

  $description .= ' ' .$this->layout()->siteinfo['description'];
  $keywords = $this->layout()->siteinfo['keywords'];

  if($this->subject() && $this->subject()->getIdentity()){
    // $this->headTitle($this->subject()->title);

    // $description .= ' ' .$this->subject()->getDescription();
    if (!empty($keywords)) $keywords .= ',';
    $keywords .= $this->subject()->getKeywords(',');
  }

  $this->headMeta()->appendName('description', trim($description));
  $this->headMeta()->appendName('keywords', trim($keywords));
  // Get body identity
  if( isset($this->layout()->siteinfo['identity']) ) {
    $identity = $this->layout()->siteinfo['identity'];
  } else {
    $identity = $request->getModuleName() . '-' .
      $request->getControllerName() . '-' .
      $request->getActionName();
  }
  ?>
  <?php echo $this->hooks("onRenderLayoutDefaultSeo", $this) ?>
  <?php echo $this->headTitle()->toString()."\n" ?>
  <?php echo $this->headMeta()->toString()."\n" ?>
  <?php
  if(!is_null($this->page)) {
    // for SEO by Kirill
    // Open Graph

    if( strpos('http://', $this->subject()->getPhotoUrl('thumb.normal')) === false && strpos('https://', $this->subject()->getPhotoUrl('thumb.normal')) === false)
      $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    elseif( strpos('http://', $this->subject()->getPhotoUrl('thumb.normal')) === 0 || strpos('https://', $this->subject()->getPhotoUrl('thumb.normal')) === 0  )
      $host_url = '';

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $app_id = $settings->getSetting('core.facebook.appid', false);
    if($app_id) echo '<meta property="fb:app_id" content="' . $app_id  . '"/>' . "\n";
    echo '<meta property="og:title" content="'.$this->subject()->title.'" />'."\n";
    echo '<meta property="og:type" content="website" />'."\n";
    echo '<meta property="og:url" content="'.$this->subject()->getHref().'" />'."\n";
    echo '<meta property="og:image" content="'. $host_url . $this->subject()->getPhotoUrl('thumb.normal') . '" />'."\n";
    echo '<meta property="og:description" content="'.strip_tags($this->subject()->description).'" />'."\n";
    // Open Graph
    // Google
    echo '<meta itemprop="name" content="'.$this->subject()->title.'" />'."\n";
    echo '<meta itemprop="description" content="'.strip_tags($this->subject()->description).'" />'."\n";
    echo '<meta itemprop="image" content="'. $host_url . $this->subject()->getPhotoUrl('thumb.normal') . '" />'."\n";
    // Google

    // for SEO by Kirill
  }
  ?>
  <?php // LINK/STYLES ?>
  <?php
  $this->headLink(array(
      'rel' => 'favicon',
      'href' => ( isset($this->layout()->favicon)
        ? $this->layout()->staticBaseUrl . $this->layout()->favicon
        : '/favicon.ico' ),
      'type' => 'image/x-icon'),
    'PREPEND');
  $themes = array();
  if( !empty($this->layout()->themes) ) {
    $themes = $this->layout()->themes;
  } else {
    $themes = array('default');
  }
  foreach( $themes as $theme ) {
    $this->headLink()
      ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/themes/'.$theme.'/theme.css');
    if( $orientation == 'rtl' ) {
      // @todo add include for rtl
    }
  }
  // Process
  foreach( $this->headLink()->getContainer() as $dat ) {
    if( !empty($dat->href) ) {
      if( false === strpos($dat->href, '?') ) {
        $dat->href .= '?c=' . $counter;
      } else {
        $dat->href .= '&c=' . $counter;
      }
    }
  }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>

  <?php // TRANSLATE ?>
  <?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>

  <?php // SCRIPTS ?>
  <script type="text/javascript">
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>

    en4.orientation = '<?php echo $orientation ?>';

    en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');

    Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');

    en4.orientation = '<?php echo $orientation ?>';
    en4.core.environment = '<?php echo APPLICATION_ENV ?>';
    en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');
    en4.core.loader = new Element('img', {src: 'application/modules/Core/externals/images/loading.gif'});

    en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
    en4.core.loader = new Element('img', {src: 'application/modules/Core/externals/images/loading.gif'});

    <?php if( $this->subject() ): ?>
    en4.core.staticBaseUrl = '<?php echo $this->escape($this->layout()->staticBaseUrl) ?>';
    en4.core.subject = {
      type : '<?php echo $this->subject()->getType(); ?>',
      id : <?php echo (int)$this->subject()->getIdentity(); ?>,
      guid : '<?php echo $this->subject()->getGuid(); ?>'
    };
      <?php endif; ?>
    <?php if( $this->viewer()->getIdentity() ): ?>
    en4.user.viewer = {
      type : '<?php echo $this->viewer()->getType(); ?>',
      id : <?php echo $this->viewer()->getIdentity(); ?>,
      guid : '<?php echo $this->viewer()->getGuid(); ?>'
    };
      <?php endif; ?>
    if( <?php echo ( Zend_Controller_Front::getInstance()->getRequest()->getParam('ajax', false) ? 'true' : 'false' ) ?> ) {
      en4.core.dloader.attach();
    }
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
  </script>
  <?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl.'application/modules/Page/externals/scripts/activity.js')
    ->appendFile($this->layout()->staticBaseUrl.'application/modules/Page/externals/scripts/comment.js')
    ->prependFile($this->layout()->staticBaseUrl.'externals/smoothbox/smoothbox4.js')
    ->prependFile($this->layout()->staticBaseUrl.'application/modules/User/externals/scripts/core.js')
    ->prependFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/core.js')
    ->prependFile($this->layout()->staticBaseUrl.'externals/chootools/chootools.js');

  $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
  $coreItem = $modulesTbl->getModule('core')->toArray();

  //Activity
  if (version_compare($coreItem['version'], '4.2.9') < 0) {
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl.'application/modules/Page/externals/scripts/old_activity.js');
  } else {
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl.'application/modules/Page/externals/scripts/activity.js');
  }

  if (version_compare($coreItem['version'], '4.1.7') < 0) {
    $this->headScript()
      ->prependFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-1.2.4.4-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-1.2.4-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
  } elseif(version_compare($coreItem['version'], '4.2.2') < 0) {
    $this->headScript()
      ->prependFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-1.2.5.1-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-1.2.5-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
  } else {
    $this->headScript()
      ->prependFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-more-1.4.0.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-core-1.4.5-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
  }

  // Process
  foreach( $this->headScript()->getContainer() as $dat ) {
    if( !empty($dat->attributes['src']) ) {
      if( false === strpos($dat->attributes['src'], '?') ) {
        $dat->attributes['src'] .= '?c=' . $counter;
      } else {
        $dat->attributes['src'] .= '&c=' . $counter;
      }
    }
  }
  $headIncludes = $this->layout()->headIncludes;
  ?>
  <?php echo $this->headScript()->toString()."\n" ?>
  <?php echo $headIncludes ?>

</head>
<body id="global_page_<?php echo $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName() ?>">
  <div class="page-search-results hidden" id="page-search-results"></div>
  <div id="global_header">
    <?php
      echo $this->pageContent('header');
      echo $wall;
    ?>
  </div>
  <div id='global_wrapper'>
    <div id='global_content'>
      <?php echo $this->layout()->content ?>
    </div>
  </div>
  <div id="global_footer">
    <?php
      echo $this->pageContent('footer');
    ?>
  </div>
</body>
</html>