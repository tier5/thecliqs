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
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'album','extra'=>'my'))?>')" href="javascript:void(0);"  class="buttonlink ynmediaimporter_link_album"><?php echo $this -> translate("My Albums"); ?></a>
        </li>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'photo','extra'=>'upload'))?>')" href="javascript:void(0);"  class="buttonlink ynmediaimporter_link_likedphoto"><?php echo $this -> translate("Uploaded Photos"); ?></a>
        </li>
        <li>
            <a onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$serviceName,'media'=>'photo','extra'=>'tag'))?>')" href="javascript:void(0);"  class="buttonlink ynmediaimporter_link_taggedme"><?php echo $this -> translate("Tagged Photos"); ?></a>
        </li>
    </ul>
</div>
