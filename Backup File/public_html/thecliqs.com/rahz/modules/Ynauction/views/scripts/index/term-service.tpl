<h2><?php echo $this->translate('Terms of Service') ?></h2>
<p>
  <?php 
//  $str = $this->translate('_AFFILIATE_TERMS_OF_SERVICE');
//  if ($str == strip_tags($str)) {
//    // there is no HTML tags in the text
//    echo nl2br($str);
//  } else {
//    echo $str;
//  }
  $content = $this->terms;
  echo $content;
  ?>
</p>