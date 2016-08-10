<?php

// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
$appId = ""; $secret = ""; 
$flag = true;

if( empty($settings['secret']) || empty($settings['appid']) || empty($settings['enable']) || $settings['enable'] == 'none' )
{
	if ( !Engine_Api::_() -> hasModuleBootstrap('social-connect') || !Engine_Api::_() -> hasModuleBootstrap('socialbridge') )
	{
		$flag = false;
	}
	else
	{
		$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
		$select = $apiSetting->select()->where('api_name = ?', 'facebook');
		$provider = $apiSetting->fetchRow($select);
		if ($provider == null)
		{
			$flag = false;
		}
		else 
		{
			$api_params = unserialize($provider -> api_params);
			$appId = $api_params['key'];
			$secret = $api_params['secret'];
			if (!$appId || !$secret)
			{
				$flag =  false;
			}
		}
		
	}
}
else
{
	$appId = $settings['appid'];
	$secret = $settings['secret'];
}

if (!($flag))
{
	exit('Administrators does not allow Facebook Connect.');
}

$facebook = new Facebook_Api(array(
  'appId'  => $appId,
  'secret' => $secret,
  'cookie' => false, // @todo make sure this works
  'allowSignedRequest'=>false,
  'fileUpload'=>false,
  //'baseDomain' => $_SERVER['HTTP_HOST'],
));

if(isset($_GET['access_token'])){
	$username = $_GET['username'];
	echo "<div style='text-align: center; line-height: 40px; padding-top: 10px'>Connecting with <strong>$username</strong> ... </div>";
	//$_SESSION['fb_connected'] = true;
	exit;
}

if(isset($_GET['confirm_token']))
{
	$confirm_token = $_GET['confirm_token'];
	
	$me = $facebook->api('/me');
	$email = ($me['email']) ? ($me['email']) : "";
	$username = $me['name'];
	$uid = $me['id'];
	
	$newurl =  '?'. http_build_query(array('access_token'=>$confirm_token,'m'=>'lite','module'=>'ynmobile','name'=>'facebook','email'=>$email,'uid'=>$uid,'username'=>$username));
	$callback =  '?'. http_build_query(array('m'=>'lite','module'=>'ynmobile','name'=>'facebook','pass_confirm'=>1));
	$callback = 'http' 
				. ( (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "s://" : "://")
				. $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"] .  $callback;
	
	$logoutUrl = $facebook->getLogoutUrl(array('next'=> $callback));
	$avatar = sprintf("https://graph.facebook.com/%s/picture/?type=square", $uid);
	$formHTML = <<<EOF
	<div style="width: 100%; text-align: center; margin-top: 30px;">
		<div>
		<a href="{$newurl}"><img src="{$avatar}" width="50px" height="50px" /></a></div>
		<div>
		<a href="{$newurl}" style="color: #333; text-decoration: none; line-height: 40px;">Logged in as <strong>{$username}</strong></a>
		</div>
		<div style=""><button onclick="window.location.assign('{$logoutUrl}')">Login with another account</button></div>
	</div>
EOF;
	echo $formHTML; 
	exit;

}

if(!isset($_GET['code']))
{
	$url =  $facebook->getLoginUrl(array('scope'=>'email'));
	header('location: '. $url);
	exit();
}

$access_token  = $facebook->getAccessToken();

if($access_token)
{

	$me = $facebook->api('/me');
	$email = ($me['email']) ? ($me['email']) : "";
	$username = ($me['name']) ? ($me['name']) : "";

	if(@$_SESSION['fb_connected'] != true || isset($_GET['pass_confirm']))
	{
		$url =  '?'. http_build_query(array('access_token'=>$access_token,'m'=>'lite','module'=>'ynmobile','name'=>'facebook','email'=>$email, 'username'=> $username));
	}
	else
	{
		$url =  '?'. http_build_query(array('confirm_token'=>$access_token,'m'=>'lite','module'=>'ynmobile','name'=>'facebook','email'=>$email, 'username'=> $username));
	}

	$_SESSION['fb_connected'] = true;
	
	header('location: ' . $url);
}
else{
  exit('Could not connect Facebook please try again.');
}
