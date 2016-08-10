<?php

// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// ini_set('error_reporting', E_ALL);

if(isset($_REQUEST['access_token']))
{
	exit('Connecting with Twitter');
}


$settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
$flag = true;

if( empty($settings['key']) || empty($settings['secret']) || empty($settings['enable']) || $settings['enable'] == 'none' ) 
{
	if ( !Engine_Api::_() -> hasModuleBootstrap('social-connect') || !Engine_Api::_() -> hasModuleBootstrap('socialbridge') )
	{
		$flag = false;
	}
	else
	{
		$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
		$select = $apiSetting->select()->where('api_name = ?', 'twitter');
		$provider = $apiSetting->fetchRow($select);
		if ($provider == null)
		{
			$flag = false;
		}
		else 
		{
			$api_params = unserialize($provider -> api_params);
			$key = $api_params['key'];
			$secret = $api_params['secret'];
			if (!$key || !$secret)
			{
				$flag =  false;
			}
		}
	}
}
else
{
	$key = $settings['key'];
	$secret = $settings['secret'];
}

if (!($flag))
{
	exit('Administrators does not allow Twitter Connect.');
}

$coreTwitter = Engine_Api::_()->getApi("twitter","ynmobile");
$twitter = $coreTwitter->getApi($key, $secret);
$twitterOauth = $coreTwitter->getOauth($key, $secret);

$twitter_token = false;

if(!isset($_GET['oauth_token']))
{
	unset($_SESSION['twitter_token']);
    unset($_SESSION['twitter_secret']);
	  
	// init page url
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

	
	//GET REQUEST TOKEN
	$additional['oauth_callback'] = $pageURL;
	$response = $twitterOauth->sendRequest('https://twitter.com/oauth/request_token', $additional, "POST");	 
    $data     = $response->getDataFromBody();


	if(isset($data['oauth_token']))
	{
		$_SESSION['twitter_token']  = $data['oauth_token'];
	    $_SESSION['twitter_secret'] = $data['oauth_token_secret'];
		$params = array('oauth_token' =>  $data['oauth_token']);
		$url = sprintf('%s?%s', 'https://twitter.com/oauth/authenticate', HTTP_OAuth::buildHTTPQuery($params));
	}
	else 
	{
		$url = $pageURL;
	}

	header('location: '. $url);

	exit();
}

else if($_GET['oauth_verifier'])
{
	$twitterOauth -> getAccessToken('https://twitter.com/oauth/access_token', $_GET['oauth_verifier']);
	$twitter_token = $twitterOauth->getToken();
	$twitter_secret = $twitterOauth->getTokenSecret();
}else{
	exit("failure");
}

if($twitter_token)
{
	$result = $twitterOauth->sendRequest('https://api.twitter.com/1.1/account/verify_credentials.json',array(), 'GET');
	$body=$result->response->getBody();
	
	$data  = json_decode($body,1);
	$profileImage = ($data['profile_image_url']) 
		? ($data['profile_image_url'])
		: ($data['profile_image_url_https']) ;
		
	$profileImage = str_replace("_normal", "", $profileImage);
	$json =  array(
		'id'=> $data['id'],
		'name'=>$data['name'],
		'screen_name'=>$data['screen_name'],
		'profile_image_url'=>base64_encode($profileImage)
	);

	
	session_destroy();

	$url = '?'. http_build_query(array(
		'access_token'=>$twitter_token, 
		'secret_token' => $twitter_secret, 
		'm'=>'lite',
		'module'=>'ynmobile',
		'name'=>'twitter',
		'json_data'=>json_encode($json)),null,'&');

	header('location: ' .$url);

	exit($url);

	echo("Connecting with Twitter ...");
	
	exit;

}



