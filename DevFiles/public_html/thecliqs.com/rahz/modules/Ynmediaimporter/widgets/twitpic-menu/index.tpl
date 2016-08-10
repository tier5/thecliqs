<?php
$serviceName = $this -> serviceName;
$provider = Ynmediaimporter::getProvider($serviceName);
?>

<div>
    <div>
        <img src="<?php echo $provider -> getUserSquareAvatarUrl(); ?>" />    
    </div>
   <div>
       <?php echo $this -> translate("Connected as"); ?> <a href="<?php echo $provider -> getUserProfileUrl(); ?>" target="_default"> <?php echo $provider -> getUserDisplayname(); ?></a>
   </div>
</div>
<div id="profile_options">
    <ul>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'photo','extra'=>'my'))?>')" href="javascript:void(0);"  class="buttonlink menu_user_profile user_profile_edit"><?php echo $this -> translate("My Photos"); ?></a>
        </li>
    </ul>
</div>
