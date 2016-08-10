<?php
# $Date: 2006-06-10 11:45:08 +0100 (Sat, 10 Jun 2006) $
# iono Version Integration #1 - http://www.olate.co.uk

$product_id = 13; // Product ID

# You do not need to edit any of the following code

// Home call details
$home_url_site = 'www.game-script.net';
$home_url_port = 80;
$home_url_iono = '/order/remote.php';
$fsock_terminate = false;

// Build request
$request = 'remote=version&product_id='.urlencode($product_id);

$request = $home_url_iono.'?'.$request;

// Build HTTP header
$header  = "GET $request HTTP/1.0\r\nHost: $home_url_site\r\nConnection: Close\r\nUser-Agent: iono (www.olate.co.uk/iono)\r\n";
$header .= "\r\n\r\n";

// Contact license server
$fpointer = fsockopen($home_url_site, $home_url_port, $errno, $errstr, 5);
$return = '';
if ($fpointer) 
{
	fwrite($fpointer, $header);
	while(!feof($fpointer)) 
	{
		$return .= fread($fpointer, 1024);
	}
	fclose($fpointer);
}
else
{
	($fsock_terminate) ? exit : NULL;
}

// Get rid of HTTP headers
$content = explode("\r\n\r\n", $return);
$content = explode($content[0], $return);

// Assign version to var
$version = trim($content[1]);

// Clean up variables for security
unset($home_url_site, $home_url_iono, $request, $header, $return, $fpointer, $content);

echo $version;
?>
<br /><br /><center>
Your current version you are on is <?=$version?><br />
To see what version we are currently on you can visit <a href="http://www.game-script.net" target="_blank">Http://www.game-script.net</a><br />
Should you need support you can contact us at admin@game-script.net<br />
