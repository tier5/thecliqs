<?php

/**
 *
 * @author An Nguyen <annt@younetco.com>
 */
class Ynrestapi_Service_Response extends \OAuth2\Response
{
    /**
     * @var mixed
     */
    protected $dataDebug;

    /**
     * @return mixed
     */
    public function getDataDebug()
    {
        return $this->dataDebug;
    }

    /**
     * @param $value
     */
    public function setDataDebug($value)
    {
        $this->dataDebug = $value;
    }

    /**
     * @param $name
     */
    public function unsetHttpHeader($name)
    {
        unset($this->httpHeaders[$name]);
    }

    /**
     * @param  $format
     * @return null
     */
    public function send($format = 'json')
    {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return;
        }

        switch ($format) {
            case 'json':
                $this->setHttpHeader('Content-Type', 'application/json');
                break;
            case 'xml':
                $this->setHttpHeader('Content-Type', 'text/xml');
                break;
        }
        // status
        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));

        // Fix header status code from OAuth2\Controller\ResourceController->verifyResourceRequest
        if ($this->statusCode == 403) {
            $this->unsetHttpHeader('WWW-Authenticate');
        }

        foreach ($this->getHttpHeaders() as $name => $header) {
            header(sprintf('%s: %s', $name, $header));
        }
        echo $this->getResponseBody($format);
        exit();
    }

    /**
     * @param $statusCode
     * @param $error
     * @param $errorDescription
     * @param null                $errorUri
     * @param null                $dataDebug
     */
    public function setError($statusCode, $error, $errorDescription = null, $errorUri = null, $dataDebug = null)
    {
        $parameters = array(
            'error' => $error,
            'error_description' => $errorDescription,
        );

        if (!is_null($errorUri)) {
            if (strlen($errorUri) > 0 && $errorUri[0] == '#') {
                // we are referencing an oauth bookmark (for brevity)
                $errorUri = 'http://tools.ietf.org/html/rfc6749' . $errorUri;
            }
            $parameters['error_uri'] = $errorUri;
        }

        $httpHeaders = array(
            'Cache-Control' => 'no-store',
        );

        $this->setStatusCode($statusCode);
        $this->addParameters($parameters);
        $this->addHttpHeaders($httpHeaders);

        if (null !== $dataDebug) {
            $this->setDataDebug($dataDebug);
        }

        if (!$this->isClientError() && !$this->isServerError()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not an error ("%s" given).', $statusCode));
        }
    }

    /**
     * @param $statusCode
     * @param $data
     * @param $dataDebug
     */
    public function setSuccess($statusCode, $data = null, $dataDebug = null)
    {
        $this->setStatusCode($statusCode);

        $this->parameters = $data;

        if (null !== $dataDebug) {
            $this->setDataDebug($dataDebug);
        }

        if (!$this->isSuccessful()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a successful code ("%s" given).', $statusCode));
        }
    }

    /**
     * @param  $format
     * @return mixed
     */
    public function getResponseBody($format = 'json')
    {
        switch ($format) {
            case 'json':
                $body = $this->isSuccessful() ? $this->_getSuccessResponseBody() : $this->_getErrorResponseBody();
                return Zend_Json::encode($body);
        }

        throw new \InvalidArgumentException(sprintf('The format %s is not supported', $format));
    }

    /**
     * @return mixed
     */
    private function _getSuccessResponseBody()
    {
        $body = array(
            'success' => true,
        );

        if (null !== $this->parameters) {
            $body['data'] = $this->parameters;
        }

        if (defined('YNRESTAPI_DEBUG') && YNRESTAPI_DEBUG === true && null !== ($dataDebug = $this->getDataDebug())) {
            $body['data_debug'] = $dataDebug;
        }

        return $body;
    }

    /**
     * @return mixed
     */
    private function _getErrorResponseBody()
    {
        // Convert Oauth lib format to Yn format
        $parameters = $this->getParameters();

        if (isset($parameters['error_description'])) {
            $parameters['message'] = $parameters['error_description'];
            unset($parameters['error_description']);
        }

        if (isset($parameters['error'])) {
            $parameters['error_text'] = $parameters['error'];
            unset($parameters['error']);
        }

        $body = array(
            'success' => false,
            'data' => array_merge(array(
                'message' => '',
                'error_text' => '',
                'error_code' => $this->getStatusCode(),
            ), $parameters),
        );

        if (defined('YNRESTAPI_DEBUG') && YNRESTAPI_DEBUG === true && null !== ($dataDebug = $this->getDataDebug())) {
            $body['data_debug'] = $dataDebug;
        }

        return $body;
    }
}
