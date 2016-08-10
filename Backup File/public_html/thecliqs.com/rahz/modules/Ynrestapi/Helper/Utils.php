<?php

include_once dirname(dirname(__FILE__)) . '/externals/libraries/SmartDOMDocument.php';

class Ynrestapi_Helper_Utils
{
    /**
     * @var string
     */
    protected static $rootUrl;

    /**
     * @var string
     */
    protected static $baseUrl;

    /**
     * @param  $html
     * @return string
     */
    public static function prepareHtmlHref($html)
    {
        $doc = new \archon810\SmartDOMDocument();
        $doc->loadHTML($html);

        $anchors = $doc->getElementsByTagName('a');
        foreach ($anchors as $a) {
            $href = $a->getAttribute('href');
            $scheme = parse_url($href, PHP_URL_SCHEME);
            if (!isset($scheme)) {
                $a->setAttribute('href', self::prepareUrl($href));
            }
        }

        return $doc->saveHTMLExact();
    }

    /**
     * @return string
     */
    public static function getSchema()
    {
        $schema = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $schema = 'https';
        }
        return $schema . '://';
    }

    /**
     * @return string
     */
    public static function getRootUrl()
    {
        if (!self::$rootUrl) {
            self::$rootUrl = self::getSchema() . $_SERVER['SERVER_NAME'] . '/';
        }
        return self::$rootUrl;
    }

    /**
     * @return string
     */
    public static function getBaseUrl()
    {
        if (null == self::$baseUrl) {
            self::$baseUrl = self::getSchema() . str_replace('/index.php', '', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
        }
        return self::$baseUrl;
    }

    /**
     * @param  $url
     * @return string
     */
    public static function prepareUrl($url)
    {
        if ($url && strpos($url, 'https://') === false && strpos($url, 'http://') === false) {
            return self::getRootUrl() . ltrim($url, '/');
        }
        return $url;
    }

    /**
     * Generate random string
     * 
     * @param  $len
     * @return mixed
     */
    public static function generateRandomString($len = 10)
    {
        $seek = '0123456789AWETYUIOPASDFGHJKLZXCVBNMqwertyuioppasdfghjklzxcvbnm';
        $max = strlen($seek) - 1;
        $str = '';

        for ($i = 0; $i < $len; ++$i) {
            $str .= substr($seek, mt_rand(0, $max), 1);
        }

        return $str;
    }
}
