<?php
    echo $this->htmlLink(
        $this -> url(array('action' => 'create-playlist'),'ynvideochannel_general', true),
        $this -> translate("Create New Playlist"),
        array('title' => $this -> translate("Create New Playlist")));
?>