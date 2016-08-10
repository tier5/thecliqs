<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 29.08.12
 * Time: 12:40
 * To change this template use File | Settings | File Templates.
 */
?>

<?php if (!$this->error): ?>

function he_donate_box(box, html){
  var $container = null;
  if (document.getElementById(box)){
    var $container = document.getElementById(box);
  }else{
    return false;
  }
  $container.innerHTML = html;
}
he_donate_box('he_donate_box', <?php echo Zend_Json_Encoder::encode($this->html);  ?>);

<?php endif; ?>