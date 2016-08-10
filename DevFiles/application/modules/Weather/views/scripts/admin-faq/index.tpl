<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-12-21 17:53 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headLink()->appendStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Hecore/externals/styles/main.css');
?>
<link href='<?php echo $this->baseUrl().'/application/css.php?request=application/modules/Weather/externals/styles/main.css'; ?>' rel='stylesheet' type="text/css" />

<h2>
  <?php echo $this->translate('Weather Plugin FAQ'); ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p class="faq_desc">
  <?php echo $this->translate("WEATHER_ADMIN_MANAGE_FAQ") ?>
</p>
<br />

<h4 class="he_admin_faq_question"><?php echo $this->translate("WEATHER_ADMIN_FAQ_WEATHER_WIDGET") ?></h4>

<ul class="he_admin_faq_answer">
  <li>
    <div><?php echo $this->translate("WEATHER_ADMIN_FAQ_A1") ?></div>
    <a onclick="he_show_image('application/modules/Weather/externals/images/faq1.png');" href="javascript://">
      <img width="500px" style="border: 3px solid #696969" src="application/modules/Weather/externals/images/faq1.png"/>
    </a>
  </li>
  <li>
    <div><?php echo $this->translate("WEATHER_ADMIN_FAQ_A2") ?></div>
  </li>
  <li>
    <div><?php echo $this->translate("WEATHER_ADMIN_FAQ_A3") ?></div>
  </li>
  <li>
    <div><?php echo $this->translate("WEATHER_ADMIN_FAQ_A4") ?></div>
  </li>
</ul>


<h4 class="he_admin_faq_question"><?php echo $this->translate("WEATHER_ADMIN_FAQ_WEATHER_ON_PAGE") ?></h4>
<ul class="he_admin_faq_answer">
  <li>
    <div><?php echo $this->translate("WEATHER_ADMIN_FAQ_A5") ?></div>
  </li>
</ul>
