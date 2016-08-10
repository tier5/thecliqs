<?php
if (!function_exists('curPageURL'))
{
	function curPageURL()
	{
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

}
@session_start();
@ob_start();

// prevent 304 redirect permanently
header('HTTP/1.1 200 OK');
try
{
    $is_from_socialpublisher = 0;
    if (!empty($_GET['is_from_socialpublisher'])) {
        $is_from_socialpublisher = 1;
    }
	
	$redirect_uri = curPageURL();
  	$obj = Engine_Api::_()->socialbridge()->getInstance('linkedin');
	
	if (isset($_GET['error'])) 
	{
	    echo "<h1>ERROR</h1> <p>{$_GET['error_description']}</p>";
	} 
	elseif (isset($_GET['code']) && !isset($_SESSION['socialbridge_session']['linkedin'])) 
	{
		$linkedin_redirect_uri = $redirect_uri;
		if(isset($_SESSION['socialbridge_session']['linkedin_redirect_uri']))
		{
			$linkedin_redirect_uri = $_SESSION['socialbridge_session']['linkedin_redirect_uri'];
		}
	    $access_token = $obj -> fetchAccessToken($_GET['code'], $linkedin_redirect_uri);
		unset($_SESSION['socialbridge_session']['linkedin_redirect_uri']);
		
	    $_SESSION['socialbridge_session']['linkedin'] = $access_token;
	    
	    // add secret_token to support all pludins are using secret_token to check authetication (In oauth2.0 not use secrect token).
	    $_SESSION['socialbridge_session']['linkedin']['secret_token'] = $_SESSION['socialbridge_session']['linkedin']['access_token'];
		$obj -> saveToken();
	} 
	elseif (!isset($_SESSION['socialbridge_session']['linkedin'])) 
	{
		$_SESSION['socialbridge_session']['linkedin_redirect_uri'] = $redirect_uri;
		$scope = 'r_basicprofile,r_emailaddress,w_share';
		if(!empty($_GET['scope']))
		{
			$scope = $_GET['scope'];
		}
	    $url = $obj -> getAuthorizationUrl($redirect_uri, 'NOSTATE', $scope);
	    header('location:' . $url);
	}
	if(isset($_SESSION['socialbridge_session']['linkedin']))
	{
		$callbackUrl = $_REQUEST['callbackUrl'];
		$datatopost['service'] = "linkedin";
		$datatopost['contact'] = "mycontact";
		$params = http_build_query($datatopost)."&oauth_tok3n=".$_SESSION['socialbridge_session']['linkedin']['access_token'];
		// authorization successfully
		?>
		<?php if (!$is_from_socialpublisher): ?>
		<form method="post" id="connect_form" action="<?php echo $callbackUrl.'?'.$params?>">
		<input type="hidden" name="task" value="get_contacts" />
		</form>
		<script> document.getElementById('connect_form').submit(); </script>
		<script>self.close();</script>
		<?php endif; ?>
		<?php
			echo "<script>opener.parent.frames['TB_iframeContent'].document.location.reload();</script>";
			echo "<script>self.close();</script>";
	}
  
}
catch(LinkedInException $e) {
	// exception raised by library call
	echo $e->getMessage();
}
?>
