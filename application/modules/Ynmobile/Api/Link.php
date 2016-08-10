<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Link.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Link extends Core_Api_Abstract
{
	/**
	 * Input data:
	 * + sLink: string, required.
	 *
	 * Output data:
	 * + sLink: string.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + sDefaultImage: string.
	 * + sMedium: string
	 * + iImageCount: int
	 * + aImages: array()
	 * + sEmbedCode: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see link/preview
	 *
	 * @param array $aData
	 * @return array
	 */
	public function preview($aData)
	{
		$sLink = isset($aData['sLink']) ? $aData['sLink'] : '';


		if (empty($sLink))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!"),
				'error_code' => 1
			);
		}
		$uri = trim(strip_tags($sLink));

		
		if (filter_var($uri, FILTER_VALIDATE_URL) === FALSE) 
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Link is not valid!"),
				'error_code' => 1
			);
		}

		try
		{
			$client = new Zend_Http_Client($uri, array(
				'maxredirects' => 2,
				'timeout' => 10,
			));

			// Try to mimic the requesting user's UA
			$client -> setHeaders(array(
				'Accept-Language'=>'en-US,en;q=0.8,fr;q=0.6,vi;q=0.4,fr-FR;q=0.2',
				'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.77 Safari/537.36',
			));

			$response = $client -> request();


			// Get content-type
			list($contentType) = explode(';', $response -> getHeader('content-type'));
			$contentType = $contentType;

			// Handling based on content-type
			switch( strtolower($contentType) )
			{

				// Images
				case 'image/gif' :
				case 'image/jpeg' :
				case 'image/jpg' :
				case 'image/tif' :
				// Might not work
				case 'image/xbm' :
				case 'image/xpm' :
				case 'image/png' :
				case 'image/bmp' :
					// Might not work
					return $this -> _previewImage($uri, $response);
					break;

				// HTML
				case '' :
				case 'text/html' :
					return $this -> _previewHtml($uri, $response);
					break;

				// Plain text
				case 'text/plain' :
					return $this -> _previewText($uri, $response);
					break;

				// Unknown
				default :
					break;
			}
		}

		catch( Exception $e )
		{
			return array(
				'sLink' => $uri,
				'sTitle' => $uri,
				'sDescription' => '',
				'sDefaultImage' => '',
				'iImageCount' => 0,
				'aImages' => array(),
				'sMedium' => '',
				'sEmbedCode' => ''
			);
		}
	}

	protected function _previewImage($uri, Zend_Http_Response $response)
	{
		return array(
			'sLink' => $uri,
			'sTitle' => '',
			'sDescription' => '',
			'sDefaultImage' => $uri,
			'iImageCount' => 1,
			'aImages' => array($uri),
			'sMedium' => '',
			'sEmbedCode' => ''
		);
	}

	protected function _previewText($uri, Zend_Http_Response $response)
	{
		$body = $response -> getBody();
		if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches))
		{
			$charset = trim($matches[1]);
		}
		else
		{
			$charset = 'UTF-8';
		}
		$body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);
		return array(
			'sLink' => $uri,
			'sTitle' => substr($body, 0, 63),
			'sDescription' => substr($body, 0, 255),
			'sDefaultImage' => '',
			'iImageCount' => 0,
			'aImages' => array(),
			'sMedium' => '',
			'sEmbedCode' => ''
		);
	}

	protected function _previewHtml($uri, Zend_Http_Response $response)
	{
		set_time_limit(0);
		$body = $response -> getBody();
		$body = trim($body);
		if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches))
		{
			$charset = trim($matches[1]);
		}
		else
		{
			$charset = 'UTF-8';
		}

		// Get DOM
		if (class_exists('DOMDocument'))
		{
			$dom = new Zend_Dom_Query($body);
		}
		else
		{
			$dom = null;
			// Maybe add b/c later
		}

		$title = null;
		if ($dom)
		{
			$titleList = $dom -> query('title');
			if (count($titleList) > 0)
			{
				$title = trim($titleList -> current() -> textContent);
				$title = substr($title, 0, 255);
			}
		}

		$description = null;
		if ($dom)
		{
			$descriptionList = $dom -> queryXpath("//meta[@name='description']");
			// Why are they using caps? -_-
			if (count($descriptionList) == 0)
			{
				$descriptionList = $dom -> queryXpath("//meta[@name='Description']");
			}
			if (count($descriptionList) > 0)
			{
				$description = trim($descriptionList -> current() -> getAttribute('content'));
				$description = substr($description, 0, 255);
			}
		}

		$thumb = null;
		if ($dom)
		{
			$thumbList = $dom -> queryXpath("//link[@rel='image_src']");
			if (count($thumbList) > 0)
			{
				$thumb = $thumbList -> current() -> getAttribute('href');
			}
		}

		$medium = null;
		if ($dom)
		{
			$mediumList = $dom -> queryXpath("//meta[@name='medium']");
			if (count($mediumList) > 0)
			{
				$medium = $mediumList -> current() -> getAttribute('content');
			}
		}

		// Get baseUrl and baseHref to parse . paths
		$baseUrlInfo = parse_url($uri);
		$baseUrl = null;
		$baseHostUrl = null;
		if ($dom)
		{
			$baseUrlList = $dom -> query('base');
			if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList -> current() -> getAttribute('href'))
			{
				$baseUrl = $baseUrlList -> current() -> getAttribute('href');
				$baseUrlInfo = parse_url($baseUrl);
				$baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
			}
		}
		if (!$baseUrl)
		{
			$baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
			if (empty($baseUrlInfo['path']))
			{
				$baseUrl = $baseHostUrl;
			}
			else
			{
				$baseUrl = explode('/', $baseUrlInfo['path']);
				array_pop($baseUrl);
				$baseUrl = join('/', $baseUrl);
				$baseUrl = trim($baseUrl, '/');
				$baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
			}
		}

		$images = array();
		if ($thumb)
		{
			$images[] = $thumb;
		}
		if ($dom)
		{
			$imageQuery = $dom -> query('img');
			foreach ($imageQuery as $image)
			{
				$src = $image -> getAttribute('src');
				// Ignore images that don't have a src
				if (!$src || false === ($srcInfo = @parse_url($src)))
				{
					continue;
				}
				$ext = ltrim(strrchr($src, '.'), '.');
				// Detect absolute url
				if (strpos($src, '/') === 0)
				{
					// If relative to root, add host
					$src = $baseHostUrl . ltrim($src, '/');
				}
				else
				if (strpos($src, './') === 0)
				{
					// If relative to current path, add baseUrl
					$src = $baseUrl . substr($src, 2);
				}
				else
				if (!empty($srcInfo['scheme']) && !empty($srcInfo['host']))
				{
					// Contians host and scheme, do nothing
				}
				else
				if (empty($srcInfo['scheme']) && empty($srcInfo['host']))
				{
					// if not contains scheme or host, add base
					$src = $baseUrl . ltrim($src, '/');
				}
				else
				if (empty($srcInfo['scheme']) && !empty($srcInfo['host']))
				{
					// if contains host, but not scheme, add scheme?
					$src = $baseUrlInfo['scheme'] . ltrim($src, '/');
				}
				else
				{
					// Just add base
					$src = $baseUrl . ltrim($src, '/');
				}
				// Ignore images that don't come from the same domain
				//if( strpos($src, $srcInfo['host']) === false ) {
				// @todo should we do this? disabled for now
				//continue;
				//}
				// Ignore images that don't end in an image extension
				if (!in_array($ext, array(
					'jpg',
					'jpeg',
					'gif',
					'png'
				)))
				{
					// @todo should we do this? disabled for now
					//continue;
				}
				if (! count($images) && !in_array($src, $images))
				{
					$imageInfo =  @getimagesize($src);
					 if($imageInfo && $imageInfo[0] >= 50 && $imageInfo[1] >= 50){
					 	$images[] = $src;
					 }
				}
			}
		}

		// Unique
		$images = array_values(array_unique($images));

		// Truncate if greater than 20
		if (count($images) > 20)
		{
			array_splice($images, 20, count($images));
		}
		if (!$thumb && count($images) > 0)
		{
			$thumb = $images[0];
		}
		return array(
			'sLink' => $uri,
			'sTitle' => $title,
			'sDescription' => $description,
			'sDefaultImage' => $thumb,
			'iImageCount' => count($images),
			'aImages' => $images,
			'sMedium' => $medium,
			'sEmbedCode' => ''
		);
	}

}
