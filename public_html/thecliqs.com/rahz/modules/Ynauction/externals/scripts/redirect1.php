<?php
if(!session_id())
    session_start();
    $url = curPageURL();
    $index = strpos($url,"/application/");
    $core_path = substr($url,0,$index);
    header('Location:'.$core_path.'/auction/manageauction');
    exit;
    
     
?>
<?php
     function curPageURL() {
         $pageURL = 'http';
     if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
         $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
    }
?>
