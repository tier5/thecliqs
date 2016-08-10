<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: terms.tpl  19.12.11 18:52 TeaJay $
 * @author     Taalay
 */
?>

<h2><?php echo $this->translate('Terms of Service') ?></h2>
<div style="margin: 10px">
  <?php
  $str = $this->translate('_PAGE_CLAIM_TERMS_OF_SERVICE');
  if ($str == strip_tags($str)) {
    // there is no HTML tags in the text
    echo nl2br($str);
  } else {
    echo $str;
  }
  ?>
</div>

<style type="text/css">
  *{
    font-size:12px;
    font-family:Arial, Helvetica, sans-serif;
  }
  ol{
    float:left;
    width:100%;
    clear:both;
    margin-bottom:10px;
  }
  ol li
  {
    margin-left:30px;
    clear:both;
    margin-top: 5px;
  }
</style>