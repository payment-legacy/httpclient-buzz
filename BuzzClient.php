<?php

namespace Payment\HttpClient;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Message\Response as BuzzResponse;

class BuzzClient implements HttpClientInterface
{
    /**
     * @var Browser
     */
    protected $browser;

    public function setClient(Browser $browser)
    {
        $this->browser = $browser;
        return $this;
    }

    /**
     * @return Browser
     */
    public function getClient()
    {
        if(is_null($this->browser))
        {
            $this->browser = new Browser(new Curl());
        }

        return $this->browser;
    }

    /**
     * @param string $method
     * @param string $url
     * @param null $content
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     * @throws HttpException
     */
    public function request($method, $url, $content = null, array $headers = array(), array $options = array())
    {
        try
        {
            $originalResponse = $this->getClient()->call($url, $method, $headers, $content);
            /** @var BuzzResponse $originalResponse */

            $rawContentType = $originalResponse->getHeader('Content-Type');

            return new NullResponse(
                $originalResponse->getStatusCode(),
                substr($rawContentType, 0, strpos($rawContentType, ';')),
                $originalResponse->getContent(),
                $originalResponse->getHeaders()
            );
        }
        catch(\Exception $e)
        {
            throw new HttpException($e);
        }
    }
}