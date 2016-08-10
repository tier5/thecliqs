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
            <a href="<?php echo $provider->getDisconnectUrl()?>" class="buttonlink ynmediaimporter_link_disconnect"><?php echo $this -> translate("Disconnect"); ?></a>
        </li>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'photo','extra'=>'my'))?>')" href="javascript:void(0);"  class="buttonlink ynmediaimporter_link_photo"><?php echo $this -> translate("PhotoStream"); ?></a>
        </li>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'photoset','extra'=>'my'))?>')" href="javascript:void(0);"  class="buttonlink ynmediaimporter_link_album"><?php echo $this -> translate("Sets"); ?></a>
        </li>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'favourite','extra'=>'my'))?>')" href="javascript:void(0);"  class="buttonlink ynmediaimporter_link_photo"><?php echo $this -> translate("Favourites"); ?></a>
        </li>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'gallery','extra'=>'my'))?>')" href="javascript:void(0);"  class="buttonlink  ynmediaimporter_link_gallery"><?php echo $this -> translate("Galleries"); ?></a>
        </li>
    </ul>
</div>
